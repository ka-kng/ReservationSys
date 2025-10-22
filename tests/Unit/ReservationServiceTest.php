<?php

namespace Tests\Unit\Services;

use App\Mail\ReservationConfirmation;
use App\Models\Patient;
use App\Models\Reservation;
use App\Models\ReservationSlot;
use App\Services\ReservationService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Session\Store;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class ReservationServiceTest extends TestCase
{
    use RefreshDatabase;

    protected ReservationService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new ReservationService();
    }

    /**
     * 正常系: 予約が作成され、患者情報が保存される
     */
    public function test_reservation_is_created_and_patient_is_saved()
    {
        // メール送信をモック
        Mail::fake();

        // 予約スロット作成
        $slot = ReservationSlot::factory()->create([
            'capacity' => 1,
            'date' => '2025-10-25',
            'start_time' => '10:00:00',
            'end_time' => '10:30:00',
        ]);

        $validated = [
            'reservation_slot_id' => $slot->id,
            'name' => 'テスト太郎',
            'name_kana' => 'テストタロウ',
            'birth_date' => '1990-01-01',
            'gender' => 0, // DB の integer 型に合わせる
            'phone' => '09012345678',
            'email' => 'test@example.com',
        ];

        // セッション付き Request を作成
        $store = new Store('test_session', session()->getHandler());
        $request = new \Illuminate\Http\Request();
        $request->setLaravelSession($store);

        $reservation = $this->service->store($validated, $request);

        // 予約がDBに作成されているか
        $this->assertDatabaseHas('reservations', [
            'id' => $reservation->id,
            'reservation_slot_id' => $slot->id,
            'status' => 'reserved',
        ]);

        // 患者情報がDBに作成されているか
        $this->assertDatabaseHas('patients', [
            'id' => $reservation->patient_id,
            'name' => 'テスト太郎',
        ]);

        // 予約スロットの容量が減っているか
        $slot->refresh();
        $this->assertEquals(0, $slot->capacity);

        // メール送信が行われたか
        Mail::assertSent(ReservationConfirmation::class, function ($mail) use ($validated) {
            return $mail->hasTo($validated['email']);
        });

        // セッションに予約情報が入っているか
        $this->assertTrue(session()->get('reservation_completed'));
        $this->assertEquals($reservation->id, session()->get('reservation_id'));
    }

    /**
     * 異常系: 予約スロットの容量が0の場合、例外が発生する
     */
    public function test_reservation_throws_exception_when_slot_capacity_is_zero()
    {
        $slot = ReservationSlot::factory()->create(['capacity' => 0]);

        $validated = [
            'reservation_slot_id' => $slot->id,
            'name' => 'テスト',
            'name_kana' => 'テスト',
            'birth_date' => '1990-01-01',
            'gender' => 0,
            'phone' => '09000000000',
            'email' => 'test@example.com',
        ];

        // セッション付き Request を作成
        $store = new Store('test_session', session()->getHandler());
        $request = new \Illuminate\Http\Request();
        $request->setLaravelSession($store);

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('この時間は予約上限に達しています');

        $this->service->store($validated, $request);
    }
}
