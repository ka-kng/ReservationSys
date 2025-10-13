@extends('layouts.app')

@section('content')
    <div class="mt-5 p-2 border border-black">
        <h1 class="text-xl">ご予約ありがとうございます。</h1>
    </div>

    <div class="mt-5">
        <table class="border border-gray-400 w-full text-sm">
            <tr>
                <th class="border border-gray-400 bg-gray-100 px-2 py-2 text-left w-1/4">予約番号</th>
                <td class="border border-gray-400 px-2 py-2">{{ $info['reservation_number'] }}</td>
            </tr>
            <tr>
                <th class="border border-gray-400 bg-gray-100 px-2 py-2 text-left">予約日</th>
                <td class="border border-gray-400 px-2 py-2">{{ $info['date'] }}</td>
            </tr>
            <tr>
                <th class="border border-gray-400 bg-gray-100 px-2 py-2 text-left">時間帯</th>
                <td class="border border-gray-400 px-2 py-2">{{ $info['start_time'] }} ～ {{ $info['end_time'] }}</td>
            </tr>
            <tr>
                <th class="border border-gray-400 bg-gray-100 px-2 py-2 text-left">お知らせ</th>
                <td class="border border-gray-400 px-2 py-2">ご来院時には、保険証、医療証各種をお持ちください。</td>
            </tr>
        </table>
    </div>

    <div class="text-center mt-5 text-sm lg:text-xl">
        <p>メールアドレスを入力した場合、予約内容の確認メールを送信しました。</p>
        <p>キャンセルや変更はお電話にてご連絡ください。</p>
    </div>

    <div class="text-center mt-10">
        <a class="p-4 text-2xl border border-black" href="{{ route('reservations.selectDate') }}">ホームに戻る</a>
    </div>

    <script>
        window.history.replaceState(null, '', "{{ route('reservations.selectDate') }}");
    </script>
@endsection
