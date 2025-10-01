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

        // $slots = Schedule::whereBetween('date', [$firstDay, $lastDay])
        //     ->whereMonth('date', $month)
        //     ->orderBy('date')
        //     ->orderBy('start_time')
        //     ->get();

        return view('management.calendarForm', compact('year', 'month', 'firstDay', 'lastDay'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $capacity = $request->input('capacity', 1);

        foreach ($request->except('_token', 'capacity') as $date => $type) {
            $timeRanges = match ($type) {
                'morning' => [['09:00', '12:00']],
                'afternoon' => [['16:00', '18:00']],
                'all' => [['09:00', '12:00'], ['16:00', '18:00']],
                'holiday' => [],
                default => [],
            };

            if ($type === 'holiday') {
                Schedule::where('date', $date)->update(['is_available' => false]);
            } else {
                foreach ($timeRanges as [$start, $end]) {
                    $startTime = Carbon::parse($start);
                    $endTime = Carbon::parse($end);

                    while ($startTime < $endTime) {
                        $slotEnd = $startTime->copy()->addMinutes(30);

                        Schedule::updateOrCreate(
                            [
                                'date' => $date,
                                'start_time' => $startTime,
                                'end_time' => $slotEnd,
                            ],
                            [
                                'capacity' => $capacity,
                                'is_available' => true
                            ]
                        );

                        $startTime = $slotEnd;
                    }
                }
            }
        }

        return redirect()->back()->with('success', '営業日を登録しました');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
