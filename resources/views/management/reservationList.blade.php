@extends('layouts.app')

@section('content')

    <div>
        <nav>
            <ul class="flex gap-2 mt-3 max-w-screen-lg mx-auto">
                <li class="p-1 px-3 border border-b-0">
                    <a href="{{ route('reservations.index') }}"
                        class="{{ Route::is('reservations.*') ? 'font-bold text-black' : 'text-gray-400' }}">予約一覧</a>
                </li>
                <li class="p-1 px-3 border border-b-0">
                    <a href="{{ route('calendar.index') }}"
                        class="{{ Route::is('calendar.*') ? 'font-bold text-black' : 'text-gray-400' }}">営業日カレンダー</a>
                </li>
            </ul>
        </nav>
    </div>

    <div class="text-center border p-6">
        <p>管理画面から営業日を選択すると、その日を予約可能日として設定できます。</p>
        <p>設定された日には自動的に予約枠が生成され、患者様が予約フォームから選択できるようになります。</p>
    </div>
@endsection
