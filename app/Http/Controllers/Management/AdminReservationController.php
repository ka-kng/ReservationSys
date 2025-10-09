<?php

namespace App\Http\Controllers\Management;

use App\Http\Controllers\Controller;
use App\Models\Reservation;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class AdminReservationController extends Controller
{
    public function index(Request $request)
    {
        $query = Reservation::with(['patient', 'slot']);

        if ($request->filled('reservation_number')) {
            $query->where('reservation_number', 'like', '%' . $request->reservation_number . '%');
        }

        if ($request->filled('date')) {
            $query->whereHas('slot', function ($q) use ($request) {
                $q->whereDate('date', $request->date);
            });
        }

        if ($request->filled('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        }

        $reservations = $query->orderBy('created_at', 'desc')->get();

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

        $status = $request->input('status');

        if (!in_array($status, ['reserved', 'visited', 'canceled'])) {
            return back()->with('error', '無効なステータスです');
        }

        $reservation->status = $status;
        $reservation->save();

        return redirect()->route('reservations.index');
    }

    public function downloadPdf($id)
    {
        $reservation = Reservation::with(['patient', 'slot'])->findOrFail($id);

        $pdf = Pdf::loadView('management.reservationPdf', compact('reservation'))->setPaper('a4', 'portrait');

        return $pdf->stream('reservation_' . $reservation->reservation_number . '.pdf');
    }
}
