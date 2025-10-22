<?php

namespace Tests\Feature;

use App\Mail\ReservationConfirmation;
use App\Models\ReservationSlot;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class ReservationControllerTest extends TestCase
{
    use RefreshDatabase;

    // 日付選択ページが正しく表示されるか確認
    public function test_select_date_page__can_be_rendered()
    {
        // 日付選択ページにアクセス
        $response = $this->get(route('reservations.selectDate'));

        // ページが開いたか確認（200 OK）
        $response->assertStatus(200);

        // 正しいテンプレートが使われているか確認
        $response->assertViewIs('patients.reservations.calendar');
    }

    // 利用可能な予約日をJSONで返すか確認
    public function test_available_dates_return_json()
    {
        // 予約枠を1件作成
        $slot = ReservationSlot::factory()->create([
            'is_available' => true,
            'date' => '2025-10-13',
        ]);

        // 利用可能日を取得するAPIにアクセス
        $response = $this->get(route('patients.reservations.available-dates'));

        // 正常にJSONが返ってきたか確認
        $response->assertStatus(200);

        // 作った日付がJSONの中に含まれているか確認
        $response->assertJsonFragment(['start' => '2025-10-13']);
    }

    // 予約作成が成功するか確認
    public function test_can_create_reservation()
    {
        // メール送信を本番ではなく偽装
        Mail::fake();

        // 予約枠を1件作成
        $slot = ReservationSlot::factory()->create([
            'capacity' => 1,
            'start_time' => '10:00',
            'end_time' => '11:00'
        ]);

        // 予約作成フォームを送信
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

         // 予約完了ページにリダイレクトされるか確認
        $response->assertRedirect(route('reservations.complete'));

        // 患者情報がDBに登録されているか確認
        $this->assertDatabaseHas('patients', ['name' => 'Test Patient']);

        // 予約情報がDBに登録されているか確認
        $this->assertDatabaseHas('reservations', ['reservation_slot_id' => $slot->id]);

        // 予約確認メールが送信されたか確認
        Mail::assertSent(ReservationConfirmation::class);
    }
}
