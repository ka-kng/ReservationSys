<?php

namespace Tests\Unit\Services;

use App\Models\Reservation;
use App\Models\ReservationSlot;
use App\Models\User;
use App\Services\AdminReservationService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminReservationServiceTest extends TestCase
{
    use RefreshDatabase;

    protected AdminReservationService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new AdminReservationService();
    }

    // reserved → visited にステータス変更
    public function test_update_status_reserved_to_visited()
    {
        $slot = ReservationSlot::factory()->create(['capacity' => 5]);

        $reservation = Reservation::factory()->create([
            'reservation_slot_id' => $slot->id,
            'status' => 'reserved', // 現在の予約ステータス
        ]);

        // ステータスを visited に更新
        $this->service->updateStatus($reservation, 'visited');

        // DBに正しく更新されているか確認
        $this->assertDatabaseHas('reservations', [
            'id' => $reservation->id,
            'status' => 'visited',
        ]);

        // DBから最新の値を取得
        $slot->refresh();

        // スロットの枠数は変わらない
        $this->assertEquals(5, $slot->capacity);
    }

    // reserved → canceled にステータス変更（枠が1増える）
    public function test_update_status_reserved_to_canceled_increases_capacity()
    {
        $slot = ReservationSlot::factory()->create(['capacity' => 3]);
        $reservation = Reservation::factory()->create([
            'reservation_slot_id' => $slot->id,
            'status' => 'reserved',  // 現在の予約ステータス
        ]);

        // 予約をキャンセルに変更
        $this->service->updateStatus($reservation, 'canceled');

        // DBに正しく更新されたか確認
        $this->assertDatabaseHas('reservations', [
            'id' => $reservation->id,
            'status' => 'canceled',
        ]);

        // スロットの最新情報を取得
        $slot->refresh();

        // 枠が1増えているか確認
        $this->assertEquals(4, $slot->capacity); // 枠が1増える
    }

    // canceled → reserved に戻す（枠が1減る）
    public function test_update_status_canceled_to_reserved_decreases_capacity()
    {
        $slot = ReservationSlot::factory()->create(['capacity' => 5]);
        $reservation = Reservation::factory()->create([
            'reservation_slot_id' => $slot->id,
            'status' => 'canceled', // 現在キャンセル状態
        ]);

         // キャンセルから予約に戻す
        $this->service->updateStatus($reservation, 'reserved');

        // DBが正しく更新されているか
        $this->assertDatabaseHas('reservations', [
            'id' => $reservation->id,
            'status' => 'reserved',
        ]);

        // 最新情報を取得
        $slot->refresh();

        // 枠が1減っているか確認
        $this->assertEquals(4, $slot->capacity);
    }

    // 無効なステータスは例外
    public function test_update_status_with_invalid_status_throws_exception()
    {
        $reservation = Reservation::factory()->create(['status' => 'reserved']);

        // 無効なステータスを指定した場合、例外が投げられる
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('無効なステータスです');

        // 実際に無効なステータスを更新
        $this->service->updateStatus($reservation, 'invalid_status');
    }

    //  canceled → reserved で枠が0の場合は例外
    public function test_update_status_canceled_to_reserved_with_no_capacity_throws_exception()
    {
        $slot = ReservationSlot::factory()->create(['capacity' => 0]); // 空きなし
        $reservation = Reservation::factory()->create([
            'reservation_slot_id' => $slot->id,
            'status' => 'canceled', // 現在キャンセル状態
        ]);

        // 枠がないので例外が投げられることを確認
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('空き枠がありません');

         // 実際に予約に戻そうとする
        $this->service->updateStatus($reservation, 'reserved');
    }
}
