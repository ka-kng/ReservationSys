<?php

namespace App\Http\Controllers\Management;

use App\Http\Controllers\Controller;
use App\Models\Reservation;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class AdminReservationController extends Controller
{
    private const VALID_STATUSES = ['reserved', 'visited', 'canceled'];

    public function index(Request $request)
    {
        $reservations = Reservation::with(['patient', 'slot'])
            ->when($request->filled('reservation_number'), fn($q) =>
                $q->where('reservation_number', 'like', '%' . $request->reservation_number . '%')
            )
            ->when($request->filled('date'), fn($q) =>
                $q->whereHas('slot', fn($sq) => $sq->whereDate('date', $request->date))
            )
            ->when($request->filled('status') && $request->status !== 'all', fn($q) =>
                $q->where('status', $request->status)
            )
            ->orderByDesc('created_at')
            ->get();

        return view('management.reservationList', compact('reservations'));
    }

    public function show(string $id)
    {
        $reservation = Reservation::with(['patient', 'slot'])->findOrFail($id);
        return view('management.reservationShow', compact('reservation'));
    }

    public function updateStatus(Request $request, string $id)
    {
        $reservation = Reservation::findOrFail($id);
        $newStatus = $request->input('status');

        if (!in_array($newStatus, self::VALID_STATUSES)) {
            return back()->with('error', '無効なステータスです');
        }

        $this->adjustSlotCapacity($reservation, $newStatus);

        $reservation->update(['status' => $newStatus]);

        return redirect()->route('reservations.index');
    }

    private function adjustSlotCapacity(Reservation $reservation, string $newStatus): void
    {
        $slot = $reservation->slot;
        if (!$slot) return;

        $oldStatus = $reservation->status;

        if ($oldStatus === 'reserved' && $newStatus === 'canceled') {
            $slot->increment('capacity');
        } elseif ($oldStatus === 'canceled' && $newStatus === 'reserved') {
            if ($slot->capacity <= 0) {
                abort(400, '空き枠がありません');
            }
            $slot->decrement('capacity');
        }
    }

    public function downloadPdf($id)
    {
        $reservation = Reservation::with(['patient', 'slot'])->findOrFail($id);

        return Pdf::loadView('management.reservationPdf', compact('reservation'))
            ->setPaper('a4', 'portrait')
            ->stream('reservation_' . $reservation->reservation_number . '.pdf');
    }
}
