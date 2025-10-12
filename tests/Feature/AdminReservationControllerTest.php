<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Reservation;
use App\Models\Schedule;
use App\Models\Patient;
use App\Models\User; // ログイン用
use Illuminate\Foundation\Testing\RefreshDatabase;

class AdminReservationControllerTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    protected function setUp(): void
    {
        parent::setUp();

        // 管理者ユーザーを作成してログイン
        $this->user = User::factory()->create();
        $this->actingAs($this->user);
    }

    /** @test */
    public function reserved_to_canceled_increments_slot_capacity()
    {
        $slot = Schedule::factory()->create(['capacity' => 1]);

        $reservation = Reservation::factory()
            ->for(Patient::factory())
            ->for($slot, 'slot')
            ->create(['status' => 'reserved']);

        $this->patch(route('reservations.updateStatus', $reservation->id), [
            'status' => 'canceled'
        ]);

        // ステータスが変更されているか確認
        $this->assertDatabaseHas('reservations', [
            'id' => $reservation->id,
            'status' => 'canceled',
        ]);

        // capacity が増えているか確認
        $this->assertDatabaseHas('reservation_slots', [
            'id' => $slot->id,
            'capacity' => 2,
        ]);
    }

    /** @test */
    public function canceled_to_reserved_decrements_slot_capacity()
    {
        $slot = Schedule::factory()->create(['capacity' => 5]);

        $reservation = Reservation::factory()
            ->for(Patient::factory())
            ->for($slot, 'slot')
            ->create(['status' => 'canceled']);

        $this->patch(route('reservations.updateStatus', $reservation->id), [
            'status' => 'reserved'
        ]);

        // ステータスが変更されているか確認
        $this->assertDatabaseHas('reservations', [
            'id' => $reservation->id,
            'status' => 'reserved',
        ]);

        // capacity が減っているか確認
        $this->assertDatabaseHas('reservation_slots', [
            'id' => $slot->id,
            'capacity' => 4,
        ]);
    }
}
