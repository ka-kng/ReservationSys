@extends('layouts.app')

@section('content')
    <div class="text-right">
      <form action=""></form>
    </div>
    <table class="border border-gray-400 w-full mt-5 text-sm">
        <tr>
            <th class="border border-gray-400 bg-gray-100 px-2 py-2 text-left w-1/4">診療日</th>
            <td class="border border-gray-400 px-2 py-2">{{ $reservation->slot->date }}</td>
        </tr>
        <tr>
            <th class="border border-gray-400 bg-gray-100 px-2 py-2 text-left">診療時間帯</th>
            <td class="border border-gray-400 px-2 py-2">
                {{ \Carbon\Carbon::parse($reservation->slot->start_time)->format('G:i') }}
                ～
                {{ \Carbon\Carbon::parse($reservation->slot->end_time)->format('G:i') }}
            </td>
        </tr>
        <tr>
            <th class="border border-gray-400 bg-gray-100 px-2 py-2 text-left">予約番号</th>
            <td class="border border-gray-400 px-2 py-2">{{ $reservation->reservation_number }}</td>
        </tr>
    </table>
@endsection
