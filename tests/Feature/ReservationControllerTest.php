<?php

namespace Tests\Feature;

use App\Mail\ReservationConfirmation;
use App\Models\Schedule;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class ReservationControllerTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function select_date_page__can_be_rendered()
    {
        $response = $this->get(route('reservations.selectDate'));
        $response->assertStatus(200);
        $response->assertViewIs('patients.reservations.calendar');
    }

    /** @test */
    public function available_dates_return_json()
    {
        $slot = Schedule::factory()->create([
            'is_available' => true,
            'date' => '2025-10-13',
        ]);

        $response = $this->get(route('patients.reservations.available-dates'));

        $response->assertStatus(200);
        $response->assertJsonFragment(['start' => '2025-10-13']);
    }

    /** @test */
    public function can_create_reservation()
    {
        Mail::fake(); // メール送信を偽装

        $slot = Schedule::factory()->create([
            'capacity' => 1, // 定員1
            'start_time' => '10:00',
            'end_time' => '11:00'
        ]);

        $response = $this->post(route('reservations.store'), [
            'name' => 'Test Patient',
            'name_kana' => 'テスト',
            'birth_date' => '2000-01-01',
            'gender' => 0,
            'phone' => '09012345678',
            'email' => 'test@example.com',
            'email_confirmation' => 'test@example.com',
            'reservation_slot_id' => $slot->id,
        ]);

        $response->assertRedirect(route('reservations.complete'));
        $this->assertDatabaseHas('patients', ['name' => 'Test Patient']);
        $this->assertDatabaseHas('reservations', ['reservation_slot_id' => $slot->id]);

        Mail::assertSent(ReservationConfirmation::class);
    }
}
