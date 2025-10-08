<?php

namespace App\Http\Controllers\Management;

use App\Http\Controllers\Controller;
use App\Models\Schedule;
use Carbon\Carbon;
use Illuminate\Http\Request;

class ScheduleController extends Controller
{
    public function index(Request $request)
    {
        $year = $request->input('year', now()->year);
        $month = $request->input('month', now()->month);

        $firstDay = \Carbon\Carbon::create($year, $month, 1)->startOfMonth();
        $lastDay = $firstDay->copy()->endOfMonth();

        $slots = Schedule::whereBetween('date', [$firstDay, $lastDay])
            ->whereMonth('date', $month)
            ->orderBy('date')
            ->orderBy('start_time')
            ->get();

        $slotsByDate = $slots->groupBy(function ($slot) {
            return Carbon::parse($slot->date)->format('Y-m-d');
        });

        return view('management.calendarForm', compact('slots', 'year', 'month', 'firstDay', 'lastDay', 'slotsByDate'));
    }

    public function create()
    {
        //
    }

    public function store(Request $request)
    {
        $capacity = $request->input('capacity', 1);

        foreach ($request->input('dates', []) as $date => $type) {

            $existingSlots = Schedule::where('date', $date)->get();

            // 既存予約がないスロットは削除
            foreach ($existingSlots as $slot) {
                if ($slot->reservations()->count() === 0) {
                    $slot->delete();
                }
            }

            if ($type === 'holiday') {
                // 予約がないスロットだけ非表示
                Schedule::where('date', $date)
                    ->whereDoesntHave('reservations')
                    ->update(['is_available' => false]);
                continue;
            }

            $timeRanges = match ($type) {
                'morning' => [['09:00', '12:00']],
                'afternoon' => [['16:00', '18:00']],
                'all' => [['09:00', '12:00'], ['16:00', '18:00']],
                'holiday' => [],
                default => [],
            };

            if ($type === 'holiday') {
                Schedule::where('date', $date)->update(['is_available' => false]);
                continue;
            } else {
                foreach ($timeRanges as [$start, $end]) {
                    $startTime = Carbon::parse($start);
                    $endTime = Carbon::parse($end);

                    while ($startTime < $endTime) {
                        $slotEnd = $startTime->copy()->addMinutes(30);

                        $slot = Schedule::firstOrNew(
                            [
                                'date' => $date,
                                'start_time' => $startTime,
                                'end_time' => $slotEnd,
                            ],
                            [
                                'slot_type' => $type,
                                'capacity' => $capacity,
                                'is_available' => true,
                            ]
                        );

                        if ($slot->exists && $slot->reservations()->count() > 0) {
                            $slot->is_available = true;
                        } else {
                            $slot->slot_type = $type;
                            $slot->capacity = $capacity;
                            $slot->is_available = true;
                        }

                        $slot->save();
                        $startTime = $slotEnd;
                    }
                }
            }
        }

        return redirect()->back()->with('success', '営業日を登録しました');
    }
}
