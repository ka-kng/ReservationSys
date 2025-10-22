<?php

namespace Tests\Unit\Services;

use App\Models\Reservation;
use App\Models\ReservationSlot;
use App\Services\ReservationSlotService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReservationSlotServiceTest extends TestCase
{
    use RefreshDatabase;

    // morningタイプでスロットが正しく作成されるか
    public function test_processDates_creates_morning_slots()
    {
        // 予約枠作成用のサービスを準備
        $service = new ReservationSlotService();

        $dates = [
            '2025-10-25' => 'morning',
        ];

        // morningスロット作成
        $service->processDates($dates, 5);

        $slots = ReservationSlot::where('date', '2025-10-25')->get();

        // 09:00-12:00で30分刻み → 6スロット作成される
        $this->assertCount(6, $slots);

        foreach ($slots as $slot) {
            $this->assertEquals(5, $slot->capacity); // 定員が正しい
            $this->assertTrue($slot->is_available);  // 作成時は予約可能
            $this->assertEquals('morning', $slot->slot_type); // morningタイプ
        }
    }

    // afternoonタイプでスロットが正しく作成されるか
    public function test_processDates_creates_afternoon_slots()
    {
        $service = new ReservationSlotService();

        $service->processDates(['2025-10-26' => 'afternoon'], 3);

        $slots = ReservationSlot::where('date', '2025-10-26')->get();

        // 16:00-18:00で30分刻み → 4スロット
        $this->assertCount(4, $slots);

        foreach ($slots as $slot) {
            $this->assertEquals(3, $slot->capacity); // 定員が正しい
            $this->assertTrue($slot->is_available); // 作成時は予約可能
            $this->assertEquals('afternoon', $slot->slot_type); // afternoonタイプ
        }
    }

    // allタイプでmorning + afternoonが作成される
    public function test_processDates_creates_all_slots()
    {
        $service = new ReservationSlotService();

        $service->processDates(['2025-10-27' => 'all'], 2);

        $slots = ReservationSlot::where('date', '2025-10-27')->get();

        // morning 6 + afternoon 4 = 10スロット
        $this->assertCount(10, $slots);

        // slot_type が 'all' になっているか確認
        foreach ($slots as $slot) {
            $this->assertEquals(2, $slot->capacity); // 定員が正しい
            $this->assertTrue($slot->is_available); // 作成時は予約可能
            $this->assertEquals('all', $slot->slot_type); // allタイプ
        }
    }

    // 既存の予約なしスロットは削除される
    public function test_existing_empty_slots_are_deleted()
    {
        // まず既存のスロットを作る（予約なし）
        $slot = ReservationSlot::factory()->create(['date' => '2025-10-28']);

        $service = new ReservationSlotService();
        $service->processDates(['2025-10-28' => 'morning'], 5);

        // 空の既存スロットは削除される
        $this->assertDatabaseMissing('reservation_slots', ['id' => $slot->id]);
    }

    // 既存スロットに予約がある場合は削除されない
    public function test_existing_slots_with_reservations_are_not_deleted()
    {
        $slot = ReservationSlot::factory()->create(['date' => '2025-10-29']);

        // このスロットに予約を作成
        Reservation::factory()->create(['reservation_slot_id' => $slot->id]);

        $service = new ReservationSlotService();
        $service->processDates(['2025-10-29' => 'morning'], 5);

        // 予約ありスロットは残る
        $this->assertDatabaseHas('reservation_slots', ['id' => $slot->id]);
    }
}
