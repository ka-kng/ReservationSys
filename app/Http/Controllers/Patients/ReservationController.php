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
            ->map(fn($slot) => ['title' => '○', 'start' => $slot->date]);

        return response()->json($events);
    }

    public function create(Request $request)
    {
        $date = $request->query('date');

        $times = Schedule::where('date', $date)
            ->where('is_available', true)
            ->where('capacity', '>', 0)
            ->orderBy('start_time')
            ->get(['id', 'start_time', 'end_time']);

        return view('patients.reservations.slots', compact('date', 'times'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string',
            'name_kana' => 'required|string',
            'birth_date' => 'required|date',
            'gender' => 'required',
            'phone' => 'required|regex:/^\d+$/',
            'email' => 'nullable|email',
            'reservation_slot_id' => 'required',
            'symptoms_start' => 'nullable|string',
            'symptoms_type' => 'nullable|array',
            'symptoms_other' => 'nullable|string',
            'past_disease_flag' => 'nullable|string',
            'past_disease_detail' => 'nullable|string',
            'allergy_flag' => 'nullable|string',
            'allergy_detail' => 'nullable|string',
            'notes' => 'nullable|string',
        ]);

        DB::transaction(function () use ($validated, $request) {

            $slot = ReservationSlot::findOrFail($validated['reservation_slot_id']);

            if ($slot->capacity <= 0) {
                throw new \Exception('この時間は予約上限に達しています');
            }

            $slot->decrement('capacity');

            $patient = Patient::create([
                'name' => $validated['name'],
                'name_kana' => $validated['name_kana'],
                'birth_date' => $validated['birth_date'],
                'gender' => $validated['gender'],
                'phone' => $validated['phone'],
                'email' => $validated['email'],
                'symptoms_start' => $validated['symptoms_start'] ?? null,
                'symptoms_type' => isset($validated['symptoms_type']) ? implode(',', $validated['symptoms_type']) : null,
                'symptoms_other' => $validated['symptoms_other'] ?? null,
                'past_disease_flag' => $validated['past_disease_flag'] ?? null,
                'past_disease_detail' => $validated['past_disease_detail'] ?? null,
                'allergy_flag' => $validated['allergy_flag'] ?? null,
                'allergy_detail' => $validated['allergy_detail'] ?? null,
                'notes' => $validated['notes'] ?? null,
            ]);

            $reservation = Reservation::create([
                'reservation_number' => strtoupper(Str::random(5)),
                'patient_id' => $patient->id,
                'reservation_slot_id' => $validated['reservation_slot_id'],
                'status' => 'reserved',
            ]);

            $request->session()->put('reservation', [
                'patient' => $patient->toArray(),
                'reservation' => $reservation->toArray(),
                'slot' => $slot->toArray(),
            ]);

        });


        return redirect()->route('reservations.complate')->with('success', '予約が完了しました');
    }

    public function complate()
    {
        if (!session('reservation_complated')) {
            return redirect()->route('reservations.create')
                ->withErrors(['error' => '不正なアクセスです。']);
        }

        $info = session('reservation_info');

        // セッション削除
        session()->forget(['reservation_complated', 'reservation_info']);

        return view('patients.reservations.complate', compact('info'));
    }
}
