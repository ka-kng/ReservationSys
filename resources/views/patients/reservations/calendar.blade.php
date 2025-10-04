@extends('layouts.app')

@section('content')
    <div class="mt-5 p-2 border">
        <h1 class="text-xl">○○病院</h1>
    </div>

    <div>
        <table class="border border-gray-400 w-full mt-5 text-sm">
            <tr>
                <th class="border border-gray-400 bg-gray-100 px-2 py-2 text-left w-1/4">住所</th>
                <td class="border border-gray-400 px-2 py-2">群馬県前橋市○○町1-2-3</td>
            </tr>
            <tr>
                <th class="border border-gray-400 bg-gray-100 px-2 py-2 text-left">電話番号</th>
                <td class="border border-gray-400 px-2 py-2">012-345-6789</td>
            </tr>
            <tr>
                <th class="border border-gray-400 bg-gray-100 px-2 py-2 text-left">診療科目</th>
                <td class="border border-gray-400 px-2 py-2">小児科、アレルギー科、内科、外科</td>
            </tr>
            <tr>
                <th class="border border-gray-400 bg-gray-100 px-2 py-2 text-left">お知らせ</th>
                <td class="border border-gray-400 px-2 py-2">ご来院時には、保険証、医療証各種をお持ちください。</td>
            </tr>
        </table>
    </div>

    <div class="mt-5 p-2 border">
        <h1 class="text-xl">診療時間</h1>
    </div>

    <div>
        <table class="border border-gray-400 w-full mt-5 text-sm">
            <thead>
                <tr>
                    <th class="border border-gray-400 bg-gray-100 px-2 py-2">診療時間</th>
                    <th class="border border-gray-400 bg-gray-100 px-2 py-2 w-1/7">月</th>
                    <th class="border border-gray-400 bg-gray-100 px-2 py-2 w-1/7">火</th>
                    <th class="border border-gray-400 bg-gray-100 px-2 py-2 w-1/7">水</th>
                    <th class="border border-gray-400 bg-gray-100 px-2 py-2 w-1/7">木</th>
                    <th class="border border-gray-400 bg-gray-100 px-2 py-2 w-1/7">金</th>
                    <th class="border border-gray-400 bg-gray-100 px-2 py-2 w-1/7">土</th>
                    <th class="border border-gray-400 bg-gray-100 px-2 py-2 w-1/7">日</th>
                </tr>
            </thead>
            <tbody>
                <tr class="text-center">
                    <th class="border border-gray-400 bg-gray-50 px-2 py-2 left-0 z-10">9:00~12:00</th>
                    <td class="border border-gray-400 px-2 py-2">○</td>
                    <td class="border border-gray-400 px-2 py-2">○</td>
                    <td class="border border-gray-400 px-2 py-2"></td>
                    <td class="border border-gray-400 px-2 py-2">○</td>
                    <td class="border border-gray-400 px-2 py-2">○</td>
                    <td class="border border-gray-400 px-2 py-2">○</td>
                    <td class="border border-gray-400 px-2 py-2"></td>
                </tr>
                <tr class="text-center">
                    <th class="border border-gray-400 bg-gray-50 px-2 py-2 left-0 z-10">16:00~18:00</th>
                    <td class="border border-gray-400 px-2 py-2">○</td>
                    <td class="border border-gray-400 px-2 py-2">○</td>
                    <td class="border border-gray-400 px-2 py-2"></td>
                    <td class="border border-gray-400 px-2 py-2">○</td>
                    <td class="border border-gray-400 px-2 py-2">○</td>
                    <td class="border border-gray-400 px-2 py-2">○</td>
                    <td class="border border-gray-400 px-2 py-2"></td>
                </tr>
            </tbody>
        </table>
        <div class="mt-1 text-sm">
          <p>※祝日は休診日です。</p>
          <p>※予定が変更する場合もあります。</p>
        </div>
    </div>

    <div class="mt-5 p-2 border">
        <h1 class="text-xl">予約日の選択</h1>
    </div>

    <div id="calendar" class="w-full mt-5 h-[500px] sm:h-[700px]"></div>

@endsection
