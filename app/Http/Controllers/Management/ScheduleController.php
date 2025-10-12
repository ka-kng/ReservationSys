<?php

namespace App\Http\Controllers\Management;

use App\Http\Controllers\Controller;
use App\Models\Schedule;
use Carbon\Carbon;
use Illuminate\Http\Request;

class ScheduleController extends Controller
{
    private const MORNING = ['09:00', '12:00'];
    private const AFTERNOON = ['16:00', '18:00'];

    public function index(Request $request)
    {
        $year = $request->input('year', now()->year);
        $month = $request->input('month', now()->month);

        $firstDay = Carbon::create($year, $month, 1)->startOfMonth();
        $lastDay = $firstDay->copy()->endOfMonth();

        $slots = Schedule::whereBetween('date', [$firstDay, $lastDay])
            ->orderBy('date')
            ->orderBy('start_time')
            ->get();

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

    public function store(Request $request)
    {
        $request->validate([
            'dates' => 'required|array',
            'capacity' => 'required|integer|min:0',
        ]);

        $capacity = (int) $request->input('capacity');

        foreach ($request->input('dates', []) as $date => $type) {
            $this->processDate($date, $type, $capacity);
        }

        return redirect()->back()->with('success', '営業日を登録しました');
    }

    private function processDate(string $date, string $type, int $capacity): void
    {
        // 既存スロットで予約がないものは削除
        $slots = Schedule::with('reservations')
            ->where('date', $date)
            ->get();

        foreach ($slots as $slot) {
            if ($slot->reservations->isEmpty()) {
                $slot->delete();
            }
        }

        foreach ($this->getTimeRanges($type) as [$start, $end]) {
            $this->createSlots($date, $start, $end, $type, $capacity);
        }
    }

    private function getTimeRanges(string $type): array
    {
        return match ($type) {
            'morning' => [self::MORNING],
            'afternoon' => [self::AFTERNOON],
            'all' => [self::MORNING, self::AFTERNOON],
            default => [],
        };
    }

    private function createSlots(string $date, string $start, string $end, string $type, int $capacity): void
    {
        $current = Carbon::parse($start);
        $endTime = Carbon::parse($end);

        while ($current < $endTime) {
            $slotEnd = $current->copy()->addMinutes(30);

            $slot = Schedule::firstOrNew([
                'date' => $date,
                'start_time' => $current,
                'end_time' => $slotEnd,
            ]);

            // 既存予約がある場合は capacity 更新しない
            if ($slot->exists && $slot->reservations->isNotEmpty()) {
                
                $slot->is_available = false;

            } else {
                $slot->fill([
                    'slot_type' => $type,
                    'capacity' => $capacity,
                    'is_available' => true,
                ]);
            }

            $slot->save();
            $current = $slotEnd;
        }
    }
}
