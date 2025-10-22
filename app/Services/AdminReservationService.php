<?php

namespace App\Services;

use App\Models\Reservation;

class AdminReservationService
{
    // 管理画面で許可される予約ステータス
    private const VALID_STATUSES = ['reserved', 'visited', 'canceled'];

    // 予約ステータスを更新する
    public function updateStatus(Reservation $reservation, string $newStatus): void
    {
        // 指定されたステータスが有効かチェック
        if (!in_array($newStatus, self::VALID_STATUSES)) {
            throw new \InvalidArgumentException('無効なステータスです');
        }

        $this->adjustSlotCapacity($reservation, $newStatus);

        $reservation->update(['status' => $newStatus]);
    }

    // 予約の枠数調整
    private function adjustSlotCapacity(Reservation $reservation, string $newStatus): void
    {
        // 予約に紐づくスロットを取得
        $slot = $reservation->slot;
        if (!$slot) return;

        // 変更前のステータス
        $oldStatus = $reservation->status;

        // 予約→キャンセルの場合は枠を増やす
        if ($oldStatus === 'reserved' && $newStatus === 'canceled') {
            $slot->increment('capacity');

        // キャンセル→予約に戻す場合は枠を減らす
        } elseif ($oldStatus === 'canceled' && $newStatus === 'reserved') {
            if ($slot->capacity <= 0) {
                throw new \Exception('空き枠がありません');
            }
            $slot->decrement('capacity');
        }
    }
}
