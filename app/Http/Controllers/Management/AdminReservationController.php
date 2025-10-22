<?php

namespace App\Http\Controllers\Management;

use App\Http\Controllers\Controller;
use App\Models\Reservation;
use App\Services\AdminReservationService;
use App\Services\AdminReservationPdfService;
use Illuminate\Http\Request;

class AdminReservationController extends Controller
{
    // サービスクラスを依存注入
    private AdminReservationService $reservationService;
    private AdminReservationPdfService $pdfService;

    public function __construct(AdminReservationService $reservationService, AdminReservationPdfService $pdfService)
    {
        $this->reservationService = $reservationService;
        $this->pdfService = $pdfService;
    }

    // 予約一覧
    public function index(Request $request)
    {
        $reservations = Reservation::with(['patient', 'slot'])
            // 予約番号で検索
            ->when(
                $request->filled('reservation_number'),
                fn($q) =>
                $q->where('reservation_number', 'like', '%' . $request->reservation_number . '%')
            )
            // スロットの日付で絞り込み
            ->when(
                $request->filled('date'),
                fn($q) =>
                $q->whereHas('slot', fn($sq) => $sq->whereDate('date', $request->date))
            )
            // ステータスで絞り込み
            ->when(
                $request->filled('status') && $request->status !== 'all',
                fn($q) =>
                $q->where('status', $request->status)
            )
            ->orderByDesc('created_at')
            ->get();

        return view('management.reservationList', compact('reservations'));
    }

    // 予約詳細
    public function show(string $id)
    {
        $reservation = Reservation::with(['patient', 'slot'])->findOrFail($id);
        return view('management.reservationShow', compact('reservation'));
    }

    // ステータス変更
    public function updateStatus(Request $request, string $id)
    {
        $reservation = Reservation::findOrFail($id);
        $newStatus = $request->input('status');

        try {
            // サービスでステータス更新＆スロット容量調整
            $this->reservationService->updateStatus($reservation, $newStatus);
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }

        return redirect()->route('reservations.index');
    }

    /**
     * PDFダウンロード
     */
    public function downloadPdf(string $id)
    {
        $reservation = Reservation::with(['patient', 'slot'])->findOrFail($id);

        // PDF生成してブラウザで表示
        return $this->pdfService->generate($reservation)
            ->stream('reservation_' . $reservation->reservation_number . '.pdf');
    }
}
