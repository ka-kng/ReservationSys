<?php

namespace App\Http\Controllers\Patients;

use App\Http\Controllers\Controller;
use App\Models\Patient;
use App\Models\Reservation;
use App\Models\ReservationSlot;
use App\Models\Schedule;
use App\Models\Symptom;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

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

        $times = Schedule::where('date', $date)
            ->where('is_available', true)
            ->orderBy('start_time')
            ->get(['id', 'start_time', 'end_time']);

        return view('patients.reservations.slots', compact('date', 'times'));
    }

    public function confirm(Request $request)
    {
        $validated = $request->validate([
            'reservation_slot_id' => 'required',
            'name' => 'required|string',
            'name_kana' => 'required|string',
            'birth_date' => 'required',
            'gender' => 'required',
            'phone' => 'required',
            'email' => 'nullable|email',
            'symptoms_start' => 'nullable|string',
            'symptoms_type' => 'nullable|array',
            'symptoms_other' => 'nullable|string',
            'past_disease_flag' => 'nullable|string',
            'past_disease_detail' => 'nullable|string',
            'allergy_flag' => 'nullable|string',
            'allergy_detail' => 'nullable|string',
            'notes' => 'nullable|string',
        ]);

        $slot = ReservationSlot::find($validated['reservation_slot_id']);

        return view('patients.reservations.confirm', compact('validated', 'slot'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'reservation_slot_id' => 'required',
            'name' => 'required|string',
            'name_kana' => 'required|string',
            'birth_date' => 'required|date',
            'gender' => 'required',
            'phone' => 'required',
            'email' => 'nullable|email',
            'symptoms_start' => 'nullable|string',
            'symptoms_type' => 'nullable|array',
            'symptoms_other' => 'nullable|string',
            'past_disease_flag' => 'nullable|string',
            'past_disease_detail' => 'nullable|string',
            'allergy_flag' => 'nullable|string',
            'allergy_detail' => 'nullable|string',
            'notes' => 'nullable|string',
        ]);

        DB::beginTransaction();

        try {

            $patient = Patient::create([
                'name' => $validated['name'],
                'name_kana' => $validated['name_kana'],
                'birth_date' => $validated['birth_date'],
                'gender' => $validated['gender'],
                'phone' => $validated['phone'],
                'email' => $validated['email'] ?? null,
            ]);

            $reservation = Reservation::create([
                'reservation_number' => strtoupper(Str::random(5)),
                'patient_id' => $patient->id,
                'reservation_slot_id' => $validated['reservation_slot_id'],
                'status' => 'confirmed',
            ]);

            Symptom::create([
                'reservation_id' => $reservation->id,
                'symptoms_start' => $validated['symptoms_start'] ?? null,
                'symptoms_type' => isset($validated['symptoms_type'])
                    ? implode(',', $validated['symptoms_type'])
                    : null,
                'symptoms_other' => $validated['symptoms_other'] ?? null,
                'past_disease_flag' => $validated['past_disease_flag'] ?? null,
                'past_disease_detail' => $validated['past_disease_detail'] ?? null,
                'allergy_flag' => $validated['allergy_flag'] ?? null,
                'allergy_detail' => $validated['allergy_detail'] ?? null,
                'notes' => $validated['notes'] ?? null,
            ]);

            DB::commit();

            return redirect()->route('patients.reservations.complete')->with('success', '予約が完了しました。');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => '登録中にエラーが発生しました: ' . $e->getMessage()]);
        }
    }

    public function complete()
    {
        return view('patients.reservations.complete');
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
