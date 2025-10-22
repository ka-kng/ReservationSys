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

    // 正しい日付と容量で予約枠が作成されるか確認
    public function test_store_creates_slots_for_valid_dates()
    {
        // ログイン状態で予約枠作成リクエスト
        $response = $this->actingAs($this->user)->post(route('calendar.store'), [
            'dates' => [
                '2025-10-18' => 'morning',
                '2025-10-19' => 'afternoon',
            ],
            'capacity' => 5,
        ]);

        // 作成後、リダイレクトされることを確認
        $response->assertRedirect();

        // 成功メッセージがセッションに入っているか確認
        $response->assertSessionHas('success');

        // データベースに正しく予約枠が作られているか確認（18日の午前）
        $this->assertDatabaseHas('reservation_slots', [
            'date' => '2025-10-18',
            'slot_type' => 'morning',
            'capacity' => 5,
            'is_available' => true,
        ]);

        // データベースに正しく予約枠が作られているか確認（19日の午後）
        $this->assertDatabaseHas('reservation_slots', [
            'date' => '2025-10-19',
            'slot_type' => 'afternoon',
            'capacity' => 5,
            'is_available' => true,
        ]);
    }

    // 容量が負の値だとエラーになるか確認
    public function test_store_returns_error_for_negative_capacity()
    {
        // 負の容量で予約枠作成リクエスト
        $response = $this->actingAs($this->user)->post(route('calendar.store'), [
            'dates' => [
                '2025-10-18' => 'morning',
            ],
            'capacity' => -1,
        ]);

        // 容量に関するエラーが出ることを確認
        $response->assertSessionHasErrors(['capacity']);
    }

    // 祝日設定で、予約がないスロットが削除されるか確認
    public function test_holiday_removes_slots_without_reservations()
    {
        // 普通の予約枠を作る
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

        // 成功メッセージがセッションに入っているか確認
        $response->assertRedirect();
        $response->assertSessionHas('success');

        // 予約がないスロットは削除されているか確認
        $this->assertDatabaseMissing('reservation_slots', [
            'id' => $slot->id,
        ]);
    }
}
