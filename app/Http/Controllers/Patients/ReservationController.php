<?php

namespace App\Http\Controllers\Patients;

use App\Http\Controllers\Controller;
use App\Models\Schedule;
use Illuminate\Http\Request;

class ReservationController extends Controller
{
    public function selectDate()
    {
        return view('patients.reservations.calendar');
    }

    public function availableDates()
    {
        $events = Schedule::where('is_available', true)
            ->groupBy('date')
            ->selectRaw('date, COUNT(*) as slots')
            ->get()
            ->map(function ($slot) {
                return [
                    'title' => '○',
                    'start' => $slot->date,
                ];
            });

        return response()->json($events);
    }

    public function create(Request $request)
    {
        $date = $request->query('date');
        return view('patients.reservations.slots', compact('date'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
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
