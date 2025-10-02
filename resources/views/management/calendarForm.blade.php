@extends('layouts.app')

@section('content')
    <div class="text-center border p-6">
        <p>管理画面から営業日を選択すると、その日を予約可能日として設定できます。</p>
        <p>設定された日には自動的に予約枠が生成され、患者様が予約フォームから選択できるようになります。</p>
    </div>

    <form method="POST" action="{{ route('calendar.store') }}" class="mt-10">
        @csrf
        <div class="flex justify-between items-end">
            <div>
                <a class="border p-1"
                    href="{{ route('calendar.index', ['year' => now()->year, 'month' => now()->month]) }} ">今月</a>
            </div>
            <div class="flex  items-center gap-4 lg:gap-12">
                <a
                    href="{{ route('calendar.index', ['year' => $firstDay->copy()->subMonth()->year, 'month' => $firstDay->copy()->subMonth()->month]) }}">先月</a>
                <p class="text-xl lg:text-2xl">{{ $firstDay->copy()->format('Y年n月') }}</p>
                <a
                    href="{{ route('calendar.index', ['year' => $firstDay->copy()->addMonth()->year, 'month' => $firstDay->copy()->addMonth()->month]) }}">来月</a>
            </div>
            <div class="flex items-center gap-1">
                <label>1枠の予約人数</label>
                <input name="capacity" type="number" min="0" class="w-10 border rounded text-center"><span>名</span>
            </div>
        </div>

        @csrf



        <table class="table-fixed w-full text-center mt-3">
            {{-- 曜日＋一括登録セレクト --}}
            <thead>
                <tr class="h-15">
                    @foreach (['日', '月', '火', '水', '木', '金', '土'] as $i => $day)
                        <th style="width:14.2857%" class="border align-top">
                            <div>{{ $day }}</div>
                            <select name="weekday_bulk[{{ $i }}]" data-weekday="{{ $i }}"
                                class="w-full text-center border border-gray-400 mt-1 weekday-bulk">
                                <option value="holiday"></option>
                                <option value="morning">午前</option>
                                <option value="afternoon">午後</option>
                                <option value="all">1日</option>
                            </select>
                        </th>
                    @endforeach
                </tr>
            </thead>

            {{-- 日付セル --}}
            <tbody>
                @php
                    $firstDay = \Carbon\Carbon::create($year, $month, 1);
                    $lastDay = $firstDay->copy()->endOfMonth();
                    $days = [];
                    for ($d = $firstDay->copy(); $d->lte($lastDay); $d->addDay()) {
                        $days[] = $d->copy();
                    }
                    $weekDay = $firstDay->dayOfWeek; // 0=日曜, 6=土曜
                @endphp

                <tr>
                    {{-- 月初まで空セル --}}
                    @for ($i = 0; $i < $weekDay; $i++)
                        <td class="border"></td>
                    @endfor

                    {{-- 日付セル --}}
                    @foreach ($days as $day)
                        <td class="border relative h-20">
                            <div class="absolute top-1 right-1 text-sm font-bold">
                                {{ $day->day }}
                            </div>

                            @php
                                $dateKey = $day->format('Y-m-d');
                                $value = isset($slotsByDate[$dateKey])
                                    ? $slotsByDate[$dateKey][0]->slot_type
                                    : 'holiday';
                            @endphp


                            <select name="dates[{{ $dateKey }}]"
                                class="w-full mt-6 text-center border border-gray-400 date-select"
                                data-weekday="{{ $day->dayOfWeek }}">
                                <option value="holiday" {{ $value === 'holiday' ? 'selected' : '' }}></option>
                                <option value="morning" {{ $value === 'morning' ? 'selected' : '' }}>午前</option>
                                <option value="afternoon" {{ $value === 'afternoon' ? 'selected' : '' }}>午後</option>
                                <option value="all" {{ $value === 'all' ? 'selected' : '' }}>1日</option>
                            </select>

                        </td>

                        @php
                            $weekDay++;
                            if ($weekDay % 7 == 0) {
                                echo '</tr><tr>';
                            }
                        @endphp
                    @endforeach
                </tr>
            </tbody>

        </table>

        <div class="mt-4 text-center">
            <button type="submit" class="px-4 py-2 bg-blue-500 text-white rounded">
                登録する
            </button>
        </div>
    </form>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const weekdayBulkSelects = document.querySelectorAll('.weekday-bulk');

            weekdayBulkSelects.forEach(select => {
                select.addEventListener('change', function() {
                    const weekday = this.dataset.weekday;
                    const value = this.value;

                    // 同じ曜日の日付セルをすべて更新
                    document.querySelectorAll(`.date-select[data-weekday="${weekday}"]`)
                        .forEach(dateSelect => {
                            dateSelect.value = value;
                        });
                });
            });
        });
    </script>
@endsection
