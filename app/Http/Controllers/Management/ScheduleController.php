<?php

namespace App\Http\Controllers\Management;

use App\Http\Controllers\Controller;
use App\Models\ReservationSlot;
use App\Services\ReservationSlotService;
use Carbon\Carbon;
use Illuminate\Http\Request;

class ScheduleController extends Controller
{
    // ReservationSlotService を依存注入
    private ReservationSlotService $scheduleService;

    public function __construct(ReservationSlotService $scheduleService)
    {
        $this->scheduleService = $scheduleService;
    }

    // カレンダー画面の表示
    public function index(Request $request)
    {
        // URLクエリから年と月を取得、なければ現在の年・月を使用
        $year = $request->input('year', now()->year);
        $month = $request->input('month', now()->month);

        // 対象月の初日と末日を取得
        $firstDay = Carbon::create($year, $month, 1)->startOfMonth();
        $lastDay = $firstDay->copy()->endOfMonth();

        // 対象月の全スロットを取得
        $slots = ReservationSlot::whereBetween('date', [$firstDay, $lastDay])
            ->orderBy('date')
            ->orderBy('start_time')
            ->get();

        // 日付ごとにスロットをグループ化（連想配列に変換）
        $slotsByDate = $slots->groupBy(fn($slot) => Carbon::parse($slot->date)->format('Y-m-d'));

        return view('management.calendarForm', compact(
            'slots',
            'year',
            'month',
            'firstDay',
            'lastDay',
            'slotsByDate'
        ));
    }

    // 営業日（予約枠）の登録・更新
    public function store(Request $request)
    {
        $request->validate([
            'dates' => 'required|array',
            'capacity' => 'required|integer|min:0',
        ]);

        // capacityを整数に
        $capacity = (int) $request->input('capacity');

        // ReservationSlotService でスロットを作成・更新
        $this->scheduleService->processDates($request->input('dates', []), $capacity);

        return redirect()->back()->with('success', '営業日を登録しました');
    }
}
