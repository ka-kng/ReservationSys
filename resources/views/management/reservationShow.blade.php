@extends('layouts.app')

@section('content')
    <div class="text-right">
        PDFダウンロード
    </div>
    <div>
        <table class="border border-gray-400 w-full mt-5 text-sm">
            <tr>
                <th class="border border-gray-400 bg-gray-100 px-2 py-2 text-left w-1/3">診療日</th>
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
    </div>

    <div class="mt-10">
        <p>患者者情報</p>
        <table class="border border-gray-400 w-full text-sm">
            <tr>
                <th class="border border-gray-400 bg-gray-100 px-2 py-2 text-left w-1/3">氏名</th>
                <td class="border border-gray-400 px-2 py-2">{{ $reservation->patient->name }}</td>
            </tr>
            <tr>
                <th class="border border-gray-400 bg-gray-100 px-2 py-2 text-left">氏名(ふりがな)</th>
                <td class="border border-gray-400 px-2 py-2">
                    {{ $reservation->patient->name_kana }}
                </td>
            </tr>
            <tr>
                <th class="border border-gray-400 bg-gray-100 px-2 py-2 text-left">生年月日</th>
                <td class="border border-gray-400 px-2 py-2">{{ $reservation->patient->birth_date }}</td>
            </tr>
            <tr>
                <th class="border border-gray-400 bg-gray-100 px-2 py-2 text-left">性別</th>
                <td class="border border-gray-400 px-2 py-2">{{ $reservation->patient->gender == 0 ? '女性' : '男性' }}</td>
            </tr>
            <tr>
                <th class="border border-gray-400 bg-gray-100 px-2 py-2 text-left">電話番号</th>
                <td class="border border-gray-400 px-2 py-2">{{ $reservation->patient->phone }}</td>
            </tr>
            <tr>
                <th class="border border-gray-400 bg-gray-100 px-2 py-2 text-left">メールアドレス</th>
                <td class="border border-gray-400 px-2 py-2">{{ $reservation->patient->email ?? '未入力' }}</td>
            </tr>
        </table>
    </div>

    <div class="mt-10">
        <h3>診療の参考となりますのでご記入ください。</h3>

        <div class="mt-3">
            <h3>1.いつ頃から症状がありますか？</h3>
            <p>{{ $reservation->patient->symptoms_start ?? '未入力' }}</p>
        </div>

        <div class="mt-5">
            <h3>2.どのような症状ですか？</h3>
            <p>{{ $reservation->patient->symptoms_type ?? '未入力' }}</p>
        </div>
    </div>

    {{-- その他症状 --}}
    <div class="mt-5">
        <h3>3.その他の症状の場合は、ご記入ください。</h3>
        <textarea name="symptoms_other" cols="30" rows="4" class="border w-full">{{ $reservation->patient->symptoms_other ?? '未入力' }}</textarea>
    </div>

    {{-- 既往歴 --}}
    <div class="mt-5">
        <h3>4-1.既往歴・治療中の病気はありますか？</h3>
        <p>{{ $reservation->patient->past_disease_flag ?? '未入力' }}</p>
    </div>

    <div class="mt-5">
        <h3>4-2.【はい】の方のみ、治療中の病気や服用中のお薬を記入してください。</h3>
        <textarea name="past_disease_detail" cols="30" rows="4" class="border w-full">{{ $reservation->patient->past_disease_detail ?? '未入力' }}</textarea>
    </div>

    {{-- アレルギー --}}
    <div class="mt-5">
        <h3>5-1.お薬や食べ物のアレルギーはありますか？</h3>
        <p>{{ $reservation->patient->allergy_flag ?? '未入力' }}</p>
    </div>

    <div class="mt-5">
        <h3>5-2.【はい】の方のみ、お薬名や食べ物を記入してください。</h3>
        <textarea name="allergy_detail" cols="30" rows="4" class="border w-full">{{ $reservation->patient->allergy_detail ?? '未入力' }}</textarea>
    </div>

    {{-- 事前連絡 --}}
    <div class="mt-5">
        <h3>6.事前にお伝えしたい内容がございましたらご記入ください。</h3>
        <textarea name="notes" cols="30" rows="4" class="border w-full">{{ $reservation->patient->notes ?? '未入力' }}</textarea>
    </div>

@endsection
