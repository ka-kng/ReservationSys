<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\ReservationSlot;
use App\Models\User; // 認証ユーザー用

class ReservationSlotControllerTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    protected function setUp(): void
    {
        parent::setUp();

        // テスト用ユーザー作成
        $this->user = User::factory()->create();
    }

    public function test_store_creates_slots_for_valid_dates()
    {
        $response = $this->actingAs($this->user)->post(route('calendar.store'), [
            'dates' => [
                '2025-10-18' => 'morning',
                '2025-10-19' => 'afternoon',
            ],
            'capacity' => 5,
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('reservation_slots', [
            'date' => '2025-10-18',
            'slot_type' => 'morning',
            'capacity' => 5,
            'is_available' => true,
        ]);

        $this->assertDatabaseHas('reservation_slots', [
            'date' => '2025-10-19',
            'slot_type' => 'afternoon',
            'capacity' => 5,
            'is_available' => true,
        ]);
    }

    public function test_store_returns_error_for_negative_capacity()
    {
        $response = $this->actingAs($this->user)->post(route('calendar.store'), [
            'dates' => [
                '2025-10-18' => 'morning',
            ],
            'capacity' => -1,
        ]);

        $response->assertSessionHasErrors(['capacity']);
    }

    public function test_holiday_removes_slots_without_reservations()
    {
        // まず普通のスロットを作る
        $slot = ReservationSlot::create([
            'date' => '2025-10-18',
            'slot_type' => 'morning',
            'start_time' => '09:00:00',
            'end_time' => '09:30:00',
            'capacity' => 5,
            'is_available' => true,
        ]);

        // 予約がない状態で holiday を設定
        $response = $this->actingAs($this->user)->post(route('calendar.store'), [
            'dates' => ['2025-10-18' => 'holiday'],
            'capacity' => 5,
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        // 予約がないスロットは削除されている
        $this->assertDatabaseMissing('reservation_slots', [
            'id' => $slot->id,
        ]);
    }
}
