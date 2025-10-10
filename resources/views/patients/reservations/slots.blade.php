@extends('layouts.app')

@section('content')
    <div class="mt-5 p-2 border border-black">
        <h1 class="text-xl">情報入力</h1>
    </div>

    <form action="{{ route('reservations.store') }}" method="POST" x-data="{ open: false }" class="mt-5">
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
                        {{ $date }}
                        <input type="hidden" name="reservation_date" value="{{ $date }}">
                    </td>
                </tr>
                <tr>
                    <th class="border border-gray-400 bg-gray-100 px-2 py-2 text-left">予約時間</th>
                    <td class="border border-gray-400 px-2">
                        <select name="reservation_slot_id" id="reservation_slot_id" class="p-1 w-full">
                            @foreach ($times as $time)
                                <option value="{{ $time->id }}"
                                    {{ old('reservation_slot_id') == $time->id ? 'selected' : '' }}>
                                    {{ \Carbon\Carbon::parse($time->start_time)->format('G:i') }}
                                    ～
                                    {{ \Carbon\Carbon::parse($time->end_time)->format('G:i') }}
                                </option>
                            @endforeach
                        </select>
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
                    <th class="border border-gray-400 bg-gray-100 px-2 py-2 text-left">
                        氏名
                        @if ($errors->has('name'))
                            <p class="text-red-500 text-sm mt-1">{{ $errors->first('name') }}</p>
                        @endif
                    </th>
                    <td class="border border-gray-400 px-2 py-2">
                        <input name="name" type="text" class="border px-1 w-full lg:w-100" placeholder="山田太郎"
                            value="{{ old('name') }}">
                    </td>
                </tr>
                <tr>
                    <th class="border border-gray-400 bg-gray-100 px-2 py-2 text-left">
                        氏名(ふりがな)
                        @if ($errors->has('name_kana'))
                            <p class="text-red-500 text-sm mt-1">{{ $errors->first('name_kana') }}</p>
                        @endif
                    </th>
                    <td class="border border-gray-400 px-2 py-1">
                        <input name="name_kana" type="text" class="border px-1 w-full lg:w-100" placeholder="やまだたろう"
                            value="{{ old('name_kana') }}">
                    </td>
                </tr>
                <tr>
                    <th class="border border-gray-400 bg-gray-100 px-2 py-2 text-left"><label for="birth_date">
                            生年月日
                            @if ($errors->has('birth_date'))
                                <p class="text-red-500 text-sm mt-1">{{ $errors->first('birth_date') }}</p>
                            @endif
                        </label>
                    </th>
                    <td class="border border-gray-400 px-2 py-1">
                        <input name="birth_date" id="birth_date" type="text" class="border px-1 w-full lg:w-100"
                            placeholder="生年月日を選択" value="{{ old('birth_date') }}">
                    </td>
                </tr>
                <tr>
                    <th class="border border-gray-400 bg-gray-100 px-2 py-2 text-left">
                        性別
                        @if ($errors->has('gender'))
                            <p class="text-red-500 text-sm mt-1">{{ $errors->first('gender') }}</p>
                        @endif
                    </th>
                    <td class="px-2 py-2 flex gap-5 items-center">
                        <div class="flex gap-1 items-center">
                            <input id="male" name="gender" type="radio" value="1"
                                {{ old('gender') == '男性' ? 'checked' : '' }}>
                            <label for="male">男</label>
                        </div>
                        <div class="flex gap-1 items-center">
                            <input id="female" name="gender" type="radio" value="0"
                                {{ old('gender') == '女性' ? 'checked' : '' }}>
                            <label for="female">女</label>
                        </div>
                    </td>
                </tr>
            </table>
        </div>

        {{-- 連絡先 --}}
        <div class="mt-5">
            <h3>ご連絡先</h3>
            <div class="flex flex-col mt-3">
                <div class="flex items-center gap-2">
                    <label for="phone">電話番号</label>
                    <label class="bg-red-500 py-1 px-2 text-white text-sm font-bold">必須</label>
                </div>
                <input type="text" name="phone" class="border w-full lg:w-100 px-1 mt-2" value="{{ old('phone') }}">
                <p class="text-sm mt-1">※ハイフンなしで、固定電話の場合は市外局番から入力してください。</p>
                @if ($errors->has('phone'))
                    <p class="text-red-500 text-sm mt-1">{{ $errors->first('phone') }}</p>
                @endif
            </div>

            <div class="flex flex-col mt-3">
                <label for="email_main">メールアドレス【任意】</label>
                <input id="email_main" name="email" type="email" class="border w-full lg:w-100 px-1"
                    value="{{ old('email') }}">
            </div>
            <p class="text-sm mt-1">予約フォーム送信後、メールアドレスを入力された場合には確認メールをお送りします。</p>
            <p class="text-sm mt-1">※予約をキャンセルされる場合は、お電話でのみ受け付けております。</p>

            <div class="flex flex-col mt-3">
                <label for="email_sub">メールアドレス（確認用）</label>
                <input id="email_sub" name="email_confirmation" type="email" class="border w-full lg:w-100 px-1"
                    value="{{ old('email_confirmation') }}">
            </div>
            <p class="text-red-500 text-sm mt-1">{{ $errors->first('email') }}</p>
        </div>

        {{-- 症状 --}}
        <div class="mt-10">
            <h3>診療の参考となりますのでご記入ください。</h3>

            <div class="mt-3">
                <h3>1.いつ頃から症状がありますか？</h3>
                @php $symptomsStart = old('symptoms_start'); @endphp
                <div>
                    <div class="mt-2">
                        <input type="radio" name="symptoms_start" id="today" value="今日（来院日 当日）"
                            {{ $symptomsStart == '今日' ? 'checked' : '' }}>
                        <label for="today">今日（来院日 当日）</label>
                    </div>
                    <div class="mt-2">
                        <input type="radio" name="symptoms_start" id="yesterday" value="昨日（来院日 前日）"
                            {{ $symptomsStart == '昨日' ? 'checked' : '' }}>
                        <label for="yesterday">昨日（来院日 前日）</label>
                    </div>
                    <div class="mt-2">
                        <input type="radio" name="symptoms_start" id="symptom_2_3days" value="2、3日前"
                            {{ $symptomsStart == '2、3日前' ? 'checked' : '' }}>
                        <label for="symptom_2_3days">2、3日前</label>
                    </div>
                    <div class="mt-2">
                        <input type="radio" name="symptoms_start" id="symptom_4days" value="4日以上前"
                            {{ $symptomsStart == '4日以上前' ? 'checked' : '' }}>
                        <label for="symptom_4days">4日以上前</label>
                    </div>
                </div>
            </div>

            <div class="mt-5">
                <h3>2.どのような症状ですか？※複数選択可能</h3>
                @php $symptomsType = old('symptoms_type', []); @endphp
                <div class="flex flex-wrap gap-5 mt-2">
                    @foreach (['発熱', '頭痛', '鼻水', '咳', '鼻づまり', '倦怠感', '下痢', '味覚障害', '吐き気', 'めまい', '関節痛', 'その他'] as $symptom)
                        <div class="mt-2">
                            <input type="checkbox" name="symptoms_type[]" id="{{ $symptom }}"
                                value="{{ $symptom }}" {{ in_array($symptom, $symptomsType) ? 'checked' : '' }}>
                            <label for="{{ $symptom }}">{{ $symptom }}</label>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        {{-- その他症状 --}}
        <div class="mt-5">
            <h3>3.その他の症状の場合は、ご記入ください。</h3>
            <textarea name="symptoms_other" cols="30" rows="4" class="border w-full">{{ old('symptoms_other') }}</textarea>
        </div>

        {{-- 既往歴 --}}
        <div class="mt-5">
            <h3>4-1.既往歴・治療中の病気はありますか？</h3>
            @php $pastDiseaseFlag = old('past_disease_flag'); @endphp
            <div class="flex items-center gap-3 mt-3">
                <div>
                    <input id="disease_yes" type="radio" name="past_disease_flag" value="0"
                        {{ $pastDiseaseFlag == 'はい' ? 'checked' : '' }}>
                    <label for="disease_yes">はい</label>
                </div>
                <div>
                    <input id="disease_no" type="radio" name="past_disease_flag" value="1"
                        {{ $pastDiseaseFlag == 'いいえ' ? 'checked' : '' }}>
                    <label for="disease_no">いいえ</label>
                </div>
            </div>
        </div>

        <div class="mt-5">
            <h3>4-2.【はい】の方のみ、治療中の病気や服用中のお薬を記入してください。</h3>
            <textarea name="past_disease_detail" cols="30" rows="4" class="border w-full">{{ old('past_disease_detail') }}</textarea>
        </div>

        {{-- アレルギー --}}
        <div class="mt-5">
            <h3>5-1.お薬や食べ物のアレルギーはありますか？</h3>
            @php $allergyFlag = old('allergy_flag'); @endphp
            <div class="flex items-center gap-3 mt-3">
                <div>
                    <input id="allergy_yes" type="radio" name="allergy_flag" value="0"
                        {{ $allergyFlag == 'はい' ? 'checked' : '' }}>
                    <label for="allergy_yes">はい</label>
                </div>
                <div>
                    <input id="allergy_no" type="radio" name="allergy_flag" value="1"
                        {{ $allergyFlag == 'いいえ' ? 'checked' : '' }}>
                    <label for="allergy_no">いいえ</label>
                </div>
            </div>
        </div>

        <div class="mt-5">
            <h3>5-2.【はい】の方のみ、お薬名や食べ物を記入してください。</h3>
            <textarea name="allergy_detail" cols="30" rows="4" class="border w-full">{{ old('allergy_detail') }}</textarea>
        </div>

        {{-- 事前連絡 --}}
        <div class="mt-5">
            <h3>6.事前にお伝えしたい内容がございましたらご記入ください。</h3>
            <textarea name="notes" cols="30" rows="4" class="border w-full">{{ old('notes') }}</textarea>
        </div>

        {{-- ボタン --}}
        <div class="mt-3 text-center">
            <button type="button" class="w-full mt-4 py-2 bg-blue-500 text-lg text-white rounded" @click="open = true">送信する</button>
        </div>
            <a href="{{ route('reservations.selectDate') }}" class="mt-4 text-center text-lg text-white bg-gray-400 py-2 rounded block w-full">
                戻る
            </a>

        <div x-show="open" class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50">
            <div class="bg-white rounded p-6 w-11/12 max-w-md">
                <h2 class="text-lg font-bold mb-4">確認</h2>
                <p class="mb-6">送信してもよろしいですか？</p>
                <div class="flex justify-end gap-4">
                    <button type="button" @click="open = false" class="px-4 py-2 bg-gray-300 rounded">キャンセル</button>
                    <button type="submit" class="px-4 py-2 bg-blue-500 text-white rounded">送信</button>
                </div>
            </div>
        </div>

    </form>
@endsection
