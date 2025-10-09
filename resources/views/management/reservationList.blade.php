@extends('layouts.app')

@section('content')
    <div>
        <nav>
            <ul class="flex gap-2 mt-3 max-w-screen-lg mx-auto">
                <li class="p-1 px-3 border border-b-0 border-black">
                    <a href="{{ route('reservations.index') }}"
                        class="{{ Route::is('reservations.*') ? 'font-bold text-black' : 'text-gray-400' }}">予約一覧</a>
                </li>
                <li class="p-1 px-3 border border-b-0 border-black">
                    <a href="{{ route('calendar.index') }}"
                        class="{{ Route::is('calendar.*') ? 'font-bold text-black' : 'text-gray-400' }}">営業日カレンダー</a>
                </li>
            </ul>
        </nav>
    </div>

    <form method="GET" action="{{ route('reservations.index') }}">
        <div class="text-center border border-black p-6">
            <div class="grid grid-cols-2 gap-5">
                <input type="text" name="reservation_number" id="reservation_number" value="{{ request('reservation_number') }}" placeholder="予約番号検索">
                <input type="date" name="date" id="date" value="{{ request('date') }}">
                <select name="status" id="status">
                    <option value="all" {{ request('status') == 'all' ? 'selected' : '' }}>すべて</option>
                    <option value="reserved" {{ request('status') == 'reserved' ? 'selected' : '' }}>予約済み</option>
                    <option value="visited" {{ request('status') == 'visited' ? 'selected' : '' }}>来院済み</option>
                    <option value="canceled" {{ request('status') == 'canceled' ? 'selected' : '' }}>キャンセル済み</option>
                </select>
            </div>
            <button type="submit" class="mt-8 p-3 px-5 rounded bg-blue-400 text-white">
                検索
            </button>
        </div>
    </form>

    <table class="border border-gray-400 w-full mt-5 text-sm">
        <thead>
            <tr>
                <th class="border border-gray-400 bg-gray-100 px-2 py-2">予約番号</th>
                <th class="border border-gray-400 bg-gray-100 px-2 py-2 w-1/7">氏名</th>
                <th class="border border-gray-400 bg-gray-100 px-2 py-2 w-1/7">予約日時</th>
                <th class="border border-gray-400 bg-gray-100 px-2 py-2 w-1/7">電話番号</th>
                <th class="border border-gray-400 bg-gray-100 px-2 py-2 w-1/7">状況</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($reservations as $reservation)
                <tr
                    class="text-center @if ($reservation->status === 'canceled') bg-gray-200 opacity-50 line-through @elseif ($reservation->status === 'visited') bg-green-50 @endif">
                    <td class="border border-gray-400 px-2 py-2 underline">
                        <a href="{{ route('reservations.show', $reservation->id) }}" class="hover:text-blue-500">
                            {{ $reservation->reservation_number }}
                        </a>
                    </td>
                    <td class="border border-gray-400 px-2 py-2 ">{{ $reservation->patient->name }}</td>
                    <td class="border border-gray-400 px-2 py-2">
                        <p>{{ $reservation->slot->date }}</p>
                        {{ \Carbon\Carbon::parse($reservation->slot->start_time)->format('G:i') }}
                        ～
                        {{ \Carbon\Carbon::parse($reservation->slot->end_time)->format('G:i') }}
                    </td>
                    <td class="border border-gray-400 px-2 py-2">{{ $reservation->patient->phone }}</td>
                    <td class="border border-gray-400 px-2 py-2">
                        @switch($reservation->status)
                            @case('reserved')
                                <span class="text-blue-600">予約済み</span>
                            @break

                            @case('canceled')
                                <span class="text-red-600">キャンセル済み</span>
                            @break

                            @case('visited')
                                <span class="text-green-600">来院済み</span>
                            @break

                            @default
                                <span class="text-gray-600">{{ $reservation->status }}</span>
                        @endswitch
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
@endsection
