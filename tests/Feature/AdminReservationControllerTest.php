<?php

namespace Tests\Feature;

use App\Models\Patient;
use App\Models\Reservation;
use App\Models\ReservationSlot;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class AdminReservationControllerTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();

        // ログイン用ユーザー作成（role カラムなし）
        $this->user = User::factory()->create();
    }

    // 予約一覧ページが表示される

    public function test_index_displays_reservations()
    {
        // 予約枠と予約を作成
        $slot = ReservationSlot::factory()->create();
        $reservation = Reservation::factory()->create(['reservation_slot_id' => $slot->id]);

        // 作成したユーザーでログインして一覧ページへアクセス
        $response = $this->actingAs($this->user)
            ->get(route('reservations.index'));

        // レスポンスが正常（200）であることを確認
        $response->assertStatus(200);

        // 使用されるビューが正しいか確認
        $response->assertViewIs('management.reservationList');

        // ビューに渡されたreservationsに、作成した予約が含まれているか確認
        $response->assertViewHas('reservations', function ($reservations) use ($reservation) {
            return $reservations->contains($reservation);
        });
    }

    // 予約詳細ページが表示される
    public function test_show_displays_reservation()
    {
        // 予約枠と予約を作成
        $slot = ReservationSlot::factory()->create();
        $reservation = Reservation::factory()->create(['reservation_slot_id' => $slot->id]);

        // 作成したユーザーでログインして詳細ページへアクセス
        $response = $this->actingAs($this->user)
            ->get(route('reservations.show', $reservation->id));

        // レスポンスが正常（200）であることを確認
        $response->assertStatus(200);

        // 使用されるビューが正しいか確認
        $response->assertViewIs('management.reservationShow');

        // ビューに渡されたreservationが正しいか確認
        $response->assertViewHas('reservation', $reservation);
    }

    // 予約ステータス更新が成功しリダイレクトされる
    public function test_update_status_redirects_after_success()
    {
        // 予約枠と予約を作成（初期ステータスは 'reserved'）
        $slot = ReservationSlot::factory()->create();
        $reservation = Reservation::factory()->create(['reservation_slot_id' => $slot->id, 'status' => 'reserved']);

        // 作成したユーザーでログインしてステータス更新リクエストを送信
        $response = $this->actingAs($this->user)
            ->patch(route('reservations.updateStatus', $reservation->id), [
                'status' => 'visited',
            ]);

        // 更新後、予約一覧ページへリダイレクトされることを確認
        $response->assertRedirect(route('reservations.index'));

        // DBに更新されたステータスが正しく保存されているか確認
        $this->assertDatabaseHas('reservations', [
            'id' => $reservation->id,
            'status' => 'visited',
        ]);
    }

    // PDFダウンロードが成功し、PDFファイルが返却されるかを確認
    public function test_download_pdf_returns_pdf_stream()
    {
        // 予約枠と予約を作成
        $slot = ReservationSlot::factory()->create();
        $reservation = Reservation::factory()->create(['reservation_slot_id' => $slot->id]);

        // 作成したユーザーでログインしてPDFダウンロード
        $response = $this->actingAs($this->user)
            ->get(route('reservations.downloadPdf', $reservation->id));

        // レスポンスが正常（200）であることを確認
        $response->assertStatus(200);

        // Content-Type が PDF であることを確認
        $this->assertStringContainsString('application/pdf', $response->headers->get('content-type'));

        // ファイル名が正しいことを確認
        $this->assertStringContainsString('reservation_' . $reservation->reservation_number . '.pdf', $response->headers->get('content-disposition'));
    }
}
