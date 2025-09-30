@extends('layouts.app')

@section('content')
    <table class="table table-bordered table-fixed w-full text-center">
        {{-- 曜日＋セレクト --}}
        <thead>
            <tr>
                @foreach (['日', '月', '火', '水', '木', '金', '土'] as $i => $day)
                    <th style="width:14.2857%">
                        <select data-weekday="{{ $i }}" class="w-full mt-1 text-center border">
                            <option value=""></option>
                            <option value="morning">午前</option>
                            <option value="afternoon">午後</option>
                            <option value="all">一日</option>
                        </select>
                        {{ $day }}
                    </th>
                @endforeach
            </tr>
        </thead>

        {{-- 日付行・スロット行 --}}
        <tbody>
            @php
                $firstDay = \Carbon\Carbon::now()->startOfMonth();
                $lastDay = \Carbon\Carbon::now()->endOfMonth();
                $days = [];
                for ($d = $firstDay; $d->lte($lastDay); $d->addDay()) {
                    $days[] = $d->copy();
                }
                $slotsByDate = $slots->groupBy('date');
                $weekDay = 0;
            @endphp

            <tr>
                @foreach ($days as $day)
                    <td class="border relative h-15 sm:h-25 lg:h-30">
                        <div class="absolute top-1 right-1 text-sm font-bold">
                            {{ $day->format('j') }}
                        </div>

                        {{-- スロット --}}
                        @if (isset($slotsByDate[$day->format('Y-m-d')]))
                            @foreach ($slotsByDate[$day->format('Y-m-d')] as $slot)
                                <div class="mt-4 text-xs">
                                    {{ $slot->start_time }}〜{{ $slot->end_time }}
                                </div>
                            @endforeach
                        @endif
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
@endsection
