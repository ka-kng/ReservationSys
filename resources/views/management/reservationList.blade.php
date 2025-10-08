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

    <div class="text-center border border-black p-6">
        <p>管理画面から営業日を選択すると、その日を予約可能日として設定できます。</p>
        <p>設定された日には自動的に予約枠が生成され、患者様が予約フォームから選択できるようになります。</p>
    </div>

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
                <tr class="text-center">
                    <td class="border border-gray-400 px-2 py-2">
                        <a href="{{ route('reservations.show', $reservation->id) }}" class="hover:text-blue-500">
                            {{ $reservation->reservation_number }}
                        </a>
                    </td>
                    <td class="border border-gray-400 px-2 py-2">{{ $reservation->patient->name }}</td>
                    <td class="border border-gray-400 px-2 py-2">
                        <p>{{ $reservation->slot->date }}</p>
                        {{ \Carbon\Carbon::parse($reservation->slot->start_time)->format('G:i') }}
                        ～
                        {{ \Carbon\Carbon::parse($reservation->slot->end_time)->format('G:i') }}
                    </td>
                    <td class="border border-gray-400 px-2 py-2">{{ $reservation->patient->phone }}</td>
                    <td class="border border-gray-400 px-2 py-2">
                        {{ match ($reservation->status) {
                            'reserved' => '予約済み',
                            'cancelled' => 'キャンセル済み',
                            'attended' => '来院済み',
                            default => $reservation->status,
                        } }}
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
@endsection
