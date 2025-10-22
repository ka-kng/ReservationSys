<?php

namespace App\Services;

use App\Models\Reservation;
use Barryvdh\DomPDF\Facade\Pdf;

class AdminReservationPdfService
{
  // 予約情報をPDFに変換
  public function generate(Reservation $reservation)
  {
    // 'management.reservationPdf' を PDF に変換
    $pdf = Pdf::loadView('management.reservationPdf', compact('reservation'));

    // 縦向きの A4 サイズ
    $pdf->setPaper('a4', 'portrait');

    return $pdf;
  }
}
