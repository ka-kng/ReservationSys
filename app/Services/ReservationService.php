<?php

namespace App\Services;

use App\Mail\ReservationConfirmation;
use App\Models\Patient;
use App\Models\Reservation;
use App\Models\ReservationSlot;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class ReservationService
{
    // 予約処理をまとめたメソッド
    public function store(array $validated, $request)
    {
        // トランザクション開始
        return DB::transaction(function () use ($validated, $request) {

            // 選択された予約枠を取得
            $slot = ReservationSlot::findOrFail($validated['reservation_slot_id']);

            // 定員オーバーなら例外を投げる
            if ($slot->capacity <= 0) {
                throw new \Exception('この時間は予約上限に達しています');
            }

            // 枠の残り定員を1減らす
            $slot->decrement('capacity');

            // 患者情報を新規登録
            $patient = Patient::create([
                'name' => $validated['name'],
                'name_kana' => $validated['name_kana'],
                'birth_date' => $validated['birth_date'],
                'gender' => $validated['gender'],
                'phone' => $validated['phone'],
                'email' => $validated['email'],
                'symptoms_start' => $validated['symptoms_start'] ?? null,
                'symptoms_type' => isset($validated['symptoms_type']) ? json_encode($validated['symptoms_type']) : null,
                'symptoms_other' => $validated['symptoms_other'] ?? null,
                'past_disease_flag' => $validated['past_disease_flag'] ?? null,
                'past_disease_detail' => $validated['past_disease_detail'] ?? null,
                'allergy_flag' => $validated['allergy_flag'] ?? null,
                'allergy_detail' => $validated['allergy_detail'] ?? null,
                'notes' => $validated['notes'] ?? null,
            ]);

            // 予約情報を作成
            $reservation = Reservation::create([
                'reservation_number' => strtoupper(Str::random(5)),
                'patient_id' => $patient->id,
                'reservation_slot_id' => $validated['reservation_slot_id'],
                'status' => 'reserved',
            ]);

            // メールアドレスが入力されていれば予約確認メールを送信
            if (!empty($validated['email'])) {
                Mail::to($validated['email'])->send(new ReservationConfirmation($patient, $reservation));
            }

            // 予約内容をセッションに保存（完了画面で使う用）
            $request->session()->put('reservation', [
                'reservation_number' => $reservation->reservation_number,
                'date' => $slot->date,
                'start_time' => Carbon::parse($slot->start_time)->format('H:i'),
                'end_time' => Carbon::parse($slot->end_time)->format('H:i'),
            ]);

            // 完了フラグをセッションに保存
            session([
                'reservation_completed' => true,
                'reservation_id' => $reservation->id,
            ]);

            return $reservation;
        });
    }
}
