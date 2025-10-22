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

    /**
     * 予約一覧ページが表示される
     */
    public function test_index_displays_reservations()
    {
        $slot = ReservationSlot::factory()->create();
        $reservation = Reservation::factory()->create(['reservation_slot_id' => $slot->id]);

        $response = $this->actingAs($this->user)
            ->get(route('reservations.index'));

        $response->assertStatus(200);
        $response->assertViewIs('management.reservationList');
        $response->assertViewHas('reservations', function ($reservations) use ($reservation) {
            return $reservations->contains($reservation);
        });
    }

    /**
     * 予約詳細ページが表示される
     */
    public function test_show_displays_reservation()
    {
        $slot = ReservationSlot::factory()->create();
        $reservation = Reservation::factory()->create(['reservation_slot_id' => $slot->id]);

        $response = $this->actingAs($this->user)
            ->get(route('reservations.show', $reservation->id));

        $response->assertStatus(200);
        $response->assertViewIs('management.reservationShow');
        $response->assertViewHas('reservation', $reservation);
    }

    /**
     * 予約ステータス更新が成功しリダイレクトされる
     */
    public function test_update_status_redirects_after_success()
    {
        $slot = ReservationSlot::factory()->create();
        $reservation = Reservation::factory()->create(['reservation_slot_id' => $slot->id, 'status' => 'reserved']);

        $response = $this->actingAs($this->user)
            ->patch(route('reservations.updateStatus', $reservation->id), [
                'status' => 'visited',
            ]);

        $response->assertRedirect(route('reservations.index'));

        $this->assertDatabaseHas('reservations', [
            'id' => $reservation->id,
            'status' => 'visited',
        ]);
    }

    /**
     * PDFダウンロードが成功する
     */
    public function test_download_pdf_returns_pdf_stream()
    {
        $slot = ReservationSlot::factory()->create();
        $reservation = Reservation::factory()->create(['reservation_slot_id' => $slot->id]);

        $response = $this->actingAs($this->user)
            ->get(route('reservations.downloadPdf', $reservation->id));

        $response->assertStatus(200);
        $this->assertStringContainsString('application/pdf', $response->headers->get('content-type'));
        $this->assertStringContainsString('reservation_' . $reservation->reservation_number . '.pdf', $response->headers->get('content-disposition'));
    }
}
