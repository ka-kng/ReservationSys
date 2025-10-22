<?php

namespace App\Http\Controllers\Patients;

use App\Http\Controllers\Controller;
use App\Http\Requests\ReservationRequest;
use App\Models\ReservationSlot;
use App\Services\ReservationService;
use Illuminate\Http\Request;

class ReservationController extends Controller
{
    // 予約処理を担当するサービスクラスを保持
    private ReservationService $reservationService;

    // コンストラクタでReservationServiceを受け取り、プロパティにセット
    public function __construct(ReservationService $reservationService)
    {
        $this->reservationService = $reservationService;
    }

    //　患者側予約カレンダーページの表示
    public function selectDate()
    {
        return view('patients.reservations.calendar');
    }

    // 予約可能な日付をJSON形式で返す（カレンダーで使う用）
    public function availableDates()
    {
        // is_available=true のスロットを日付ごとにグループ化し、
        // 各日付の予約枠数を数える
        $events = ReservationSlot::where('is_available', true)
            ->groupBy('date')
            ->selectRaw('date, COUNT(*) as slots')
            ->get()
            ->map(fn($slot) => ['title' => '○', 'start' => $slot->date]);

        return response()->json($events);
    }

    // 日付ごとの予約枠選択画面を表示する
    public function create(Request $request)
    {
        // クエリパラメータから日付を取得
        $date = $request->query('date');

        if (!$date) {
            return redirect()->route('reservations.selectDate');
        }

        // 指定された日付の中で、空きがあるスロットを取得
        $times = ReservationSlot::where('date', $date)
            ->where('is_available', true)
            ->where('capacity', '>', 0)
            ->orderBy('start_time')
            ->get(['id', 'start_time', 'end_time']);

        return view('patients.reservations.slots', compact('date', 'times'));
    }

    // 予約情報を保存する処理
    public function store(ReservationRequest $request)
    {
        // バリデーション済みデータをReservationServiceに渡して保存処理を行う
        $this->reservationService->store($request->validated(), $request);

        return redirect()->route('reservations.complete')->with('success', '予約が完了しました');
    }

    // 予約完了画面の表示
    public function complete()
    {
        // 予約完了フラグがセッションにない場合は不正アクセス扱い
        if (!session('reservation_completed')) {
            return redirect()->route('reservations.create')
                ->withErrors(['error' => '不正なアクセスです。']);
        }

        // 予約情報をセッションから取得
        $info = session('reservation');

        // 使用後はセッション情報を削除
        session()->forget(['reservation_completed', 'reservation']);

        return view('patients.reservations.complete', compact('info'));
    }
}
