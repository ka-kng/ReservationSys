@extends('layouts.app')

@section('content')
    <div class="mt-5 p-2 border">
        <h1 class="text-xl">予約確認</h1>
    </div>

    <form action="{{ route('reservations.store') }}" method="POST" class="mt-5">
        @csrf

        {{-- 診療時間帯 --}}
        <div>
            <div class="flex items-center gap-2">
                <h3>診療時間帯を選択してください。</h3>
                <label class="bg-red-500 py-1 px-2 text-white text-sm font-bold">必須</label>
            </div>
            <table class="border border-gray-400 w-full text-sm mt-2">
                <tr>
                    <th class="border border-gray-400 bg-gray-100 px-2 py-2 text-left w-1/4">診療日</th>
                    <td class="border border-gray-400 px-2 py-2">
                        {{ \Carbon\Carbon::parse($slot->date)->format('Y-m-d') }}
                    </td>
                </tr>
                <tr>
                    <th class="border border-gray-400 bg-gray-100 px-2 py-2 text-left">予約時間</th>
                    <td class="border border-gray-400 px-2 py-2">
                        {{ \Carbon\Carbon::parse($slot->start_time)->format('G:i') }}
                        ～
                        {{ \Carbon\Carbon::parse($slot->end_time)->format('G:i') }}
                    </td>
                </tr>
            </table>
        </div>

        {{-- 患者情報 --}}
        <div class="mt-5">
            <div class="flex items-center gap-2">
                <h3>患者様情報</h3>
                <label class="bg-red-500 py-1 px-2 text-white text-sm font-bold">必須</label>
            </div>
            <table class="border border-gray-400 w-full text-sm mt-2">
                <tr>
                    <th class="border border-gray-400 bg-gray-100 px-2 py-2 text-left w-1/3">氏名</th>
                    <td class="border border-gray-400 px-2 py-2">
                        {{ $validated['name'] }}
                    </td>
                </tr>
                <tr>
                    <th class="border border-gray-400 bg-gray-100 px-2 py-2 text-left w-1/3">氏名(ふりがな)</th>
                    <td class="border border-gray-400 px-2 py-1">
                        {{ $validated['name_kana'] }}
                    </td>
                </tr>
                <tr>
                    <th class="border border-gray-400 bg-gray-100 px-2 py-2 text-left w-1/3">生年月日</th>
                    <td class="border border-gray-400 px-2 py-1">
                        {{ $validated['birth_date'] }}
                    </td>
                </tr>
                <tr>
                    <th class="border border-gray-400 bg-gray-100 px-2 py-2 text-left w-1/3">性別</th>
                    <td class="px-2 py-2 flex gap-5 items-center">
                        {{ $validated['gender'] }}
                    </td>
                </tr>
            </table>
        </div>

        {{-- 連絡先 --}}
        <div class="mt-5">
            <h3>ご連絡先</h3>
            <div class="mt-3">
                <div class="flex items-center gap-2">
                    <label for="phone">電話番号</label>
                    <label class="bg-red-500 py-1 px-2 text-white text-sm font-bold">必須</label>
                </div>
                <p class="mt-3">{{ $validated['phone'] }}</p>
            </div>

            <div class="mt-3">
                <label for="email_main">メールアドレス【任意】</label>
                <div>
                    <p>{{ $validated['email'] }}</p>
                </div>
            </div>
        </div>

        {{-- 症状 --}}
        <div class="mt-10">
            <h3>診療の参考となりますのでご記入ください。</h3>

            <div class="mt-3">
                <h3>1.いつ頃から症状がありますか？</h3>
                <div class="mt-2">
                    {{ $validated['symptoms_start'] ?? '未選択' }}
                </div>
            </div>

            <div class="mt-5">
                <h3>2.どのような症状ですか？</h3>
                <div class="flex flex-wrap gap-5 mt-2">
                    @if (!empty($validated['symptoms_type']))
                        {{ implode('・', $validated['symptoms_type']) }}
                    @else
                        未選択
                    @endif
                </div>
            </div>
        </div>

        {{-- その他症状 --}}
        <div class="mt-5">
            <h3>3.その他の症状の場合は、ご記入ください。</h3>
            <div>
                {{ $validated['symptoms_other'] ?? 'なし' }}
            </div>
        </div>

        {{-- 既往歴 --}}
        <div class="mt-5">
            <h3>4-1.既往歴・治療中の病気はありますか？</h3>
            <div>
                {{ $validated['past_disease_flag'] ?? '未選択' }}
            </div>
        </div>

        <div class="mt-5">
            <h3>4-2.【はい】の方のみ、治療中の病気や服用中のお薬を記入してください。</h3>
            <div>
                {{ $validated['past_disease_detail'] ?? 'なし' }}
            </div>
        </div>

        {{-- アレルギー --}}
        <div class="mt-5">
            <h3>5-1.お薬や食べ物のアレルギーはありますか？</h3>
            <div>
                {{ $validated['allergy_flag'] ?? '未選択' }}
            </div>
        </div>

        <div class="mt-5">
            <h3>5-2.【はい】の方のみ、お薬名や食べ物を記入してください。</h3>
            <div>
                {{ $validated['allergy_detail'] ?? 'なし' }}
            </div>
        </div>

        {{-- 事前連絡 --}}
        <div class="mt-5">
            <h3>6.事前にお伝えしたい内容がございましたらご記入ください。</h3>
            <div>
                {{ $validated['notes'] ?? 'なし' }}
            </div>
        </div>

        {{-- ボタン --}}
        <div class="mt-3 text-center">
            <button type="submit" class="w-full mt-4 py-2 bg-blue-500 text-lg text-white rounded">送信する</button>
        </div>

    </form>
    <div class="mt-1 text-center w-full bg-gray-400 rounded mt-4 py-2  ">
        <a href="{{ url()->previous() }}" class="text-lg text-white ">
            戻る
        </a>
    </div>
@endsection
