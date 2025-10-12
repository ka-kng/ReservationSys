<?php

namespace Tests\Unit;

use App\Models\Patient;
use App\Models\Schedule;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PatientTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function patient_has_expected_fillable_fields()
    {
        $patient = new Patient();

        $this->assertEquals(
            [
                'name',
                'name_kana',
                'birth_date',
                'gender',
                'phone',
                'email',
                'reservation_id',
                'symptoms_start',
                'symptoms_type',
                'symptoms_other',
                'past_disease_flag',
                'past_disease_detail',
                'allergy_flag',
                'allergy_detail',
                'notes'
            ],
            $patient->getFillable()
        );
    }

    /** @test */
    public function patient_reservation_relationship()
    {
        $patient = Patient::factory()->create();

        // ReservationSlot を作成
        $reservationSlot = Schedule::factory()->create();

        $reservation = $patient->reservations()->create([
            'reservation_number' => 'ABCDE',
            'reservation_slot_id' => $reservationSlot->id,
            'status' => 'reserved',
        ]);

        $this->assertTrue($patient->reservations->contains($reservation));
    }
}
