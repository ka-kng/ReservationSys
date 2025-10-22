<?php

namespace App\Services;

use App\Models\ReservationSlot;
use Carbon\Carbon;

class ReservationSlotService
{
  // 午前と午後の時間帯を定数として定義
  private const MORNING = ['09:00', '12:00'];
  private const AFTERNOON = ['16:00', '18:00'];

  // 日付ごとにスロットを作成・更新
  public function processDates(array $dates, int $capacity): void
  {
    foreach ($dates as $date => $type) {
      $this->processDate($date, $type, $capacity);
    }
  }

  //  1日分のスロットを作成・更新する
  private function processDate(string $date, string $type, int $capacity): void
  {
    // その日の既存スロットを取得（予約データも一緒に）
    $slots = ReservationSlot::with('reservations')->where('date', $date)->get();

    // 既存スロットのうち、予約が1件もないものを削除
    foreach ($slots as $slot) {
      if ($slot->reservations->isEmpty()) {
        $slot->delete();
      }
    }

    // 午前・午後などの時間帯ごとの枠を取得してループ
    foreach ($this->getTimeRanges($type) as [$start, $end]) {

      // 各時間帯のスロットを作成
      $this->createSlots($date, $start, $end, $type, $capacity);
    }
  }

  // 指定されたタイプ（morning / afternoon / all）に応じて時間帯を返す
  private function getTimeRanges(string $type): array
  {
    return match ($type) {
      'morning' => [self::MORNING],                   // [['09:00','12:00']]
      'afternoon' => [self::AFTERNOON],               // [['16:00','18:00']]
      'all' => [self::MORNING, self::AFTERNOON],     // [['09:00','12:00'], ['16:00','18:00']]
      default => [],
    };
  }

  // 指定した日付と時間帯のスロットを30分ごとに作成する
  private function createSlots(string $date, string $start, string $end, string $type, int $capacity): void
  {
    // 開始時刻と終了時刻をCarbon（日時操作ライブラリ）に変換
    $current = Carbon::parse($start);
    $endTime = Carbon::parse($end);

    // 終了時刻になるまで30分刻みでループ
    while ($current < $endTime) {
      // 30分後の時刻を作成
      $slotEnd = $current->copy()->addMinutes(30);

      // 同じ時間帯のスロットが既にあれば取得、なければ新規作成準備
      $slot = ReservationSlot::firstOrNew([
        'date' => $date,
        'start_time' => $current,
        'end_time' => $slotEnd,
      ]);

      // 既存予約がある場合は capacity 更新しない
      if ($slot->exists && $slot->reservations->isNotEmpty()) {

        $slot->is_available = false;
      } else {
        // 新規または予約なしスロットは内容を上書き
        $slot->fill([
          'slot_type' => $type,
          'capacity' => $capacity,
          'is_available' => true,
        ]);
      }

      $slot->save();

      // 次の30分枠へ進む
      $current = $slotEnd;
    }
  }
}
