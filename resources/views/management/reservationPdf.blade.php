<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <title>予約情報PDF</title>
    <style>
        @font-face {
            font-family: 'NotoSansJP';
            font-style: normal;
            font-weight: normal;
            src: url('file://{!! storage_path('fonts/NotoSansJP-Regular.ttf') !!}');

        }

        html,
        body,
        textarea,
        table {
            font-family: 'NotoSansJP', sans-serif;
            font-size: 10px;
        }

        h2,
        h3 {
            font-family: 'NotoSansJP', sans-serif;
            font-weight: normal;
        }

        table {
            border-collapse: collapse;
            width: 100%;
            margin-bottom: 10px;
        }

        th,
        td {
            border: 1px solid #000;
            padding: 3px;
            text-align: left;
            font-weight: normal;
        }

        th {
            background-color: #f0f0f0;
            font-family: 'NotoSansJP', sans-serif;
            width: 30%;
            table-layout: fixed;
        }

        h3 {
            margin-top: 20px;
            margin-bottom: 5px;
        }

        p {
            margin: 0;
            line-height: 1.4;
            overflow-wrap: break-word;
            white-space: normal;
        }

        .border-b {
            border-bottom: 1px solid #000;
            padding-bottom: 5px;
            /* あると見やすい */
            margin-bottom: 10px;
        }
    </style>
</head>

<body>

    <h2>問診票</h2>

    <table>
        <tr>
            <th>診療日</th>
            <td>{{ $reservation->slot->date }}</td>
        </tr>
        <tr>
            <th>診療時間帯</th>
            <td>{{ \Carbon\Carbon::parse($reservation->slot->start_time)->format('G:i') }}
                ~
                {{ \Carbon\Carbon::parse($reservation->slot->end_time)->format('G:i') }}</td>
        </tr>
        <tr>
            <th>予約番号</th>
            <td>{{ $reservation->reservation_number }}</td>
        </tr>
    </table>

    <p>患者情報</p>
    <table>
        <tr>
            <th>氏名</th>
            <td>{{ $reservation->patient->name }}</td>
        </tr>
        <tr>
            <th>氏名(ふりがな)</th>
            <td>{{ $reservation->patient->name_kana }}</td>
        </tr>
        <tr>
            <th>生年月日</th>
            <td>{{ $reservation->patient->birth_date }}</td>
        </tr>
        <tr>
            <th>性別</th>
            <td>{{ $reservation->patient->gender == 0 ? '女性' : '男性' }}</td>
        </tr>
        <tr>
            <th>電話番号</th>
            <td>{{ $reservation->patient->phone }}</td>
        </tr>
        <tr>
            <th>メールアドレス</th>
            <td>{{ $reservation->patient->email ?? '未入力' }}</td>
        </tr>
    </table>

    <h3 class="border-b">症状</h3>
    <div class="border-b">
        <p>1.いつ頃から症状がありますか？</p>
        <p>{{ $reservation->patient->symptoms_start ?? '未入力' }}</p>
    </div>

    <div class="border-b">
        <p>2.どのような症状ですか？</p>
        @if ($reservation->patient->symptoms_type)
            {{ implode(', ', json_decode($reservation->patient->symptoms_type)) }}
        @else
            未入力
        @endif
    </div>

    <div class="border-b">
        <p>3.その他の症状</p>
        <p>{{ $reservation->patient->symptoms_other ?? '未入力' }}</p>
    </div>

    <div class="border-b">
        <p>4-1.既往歴・治療中の病気はありますか？</p>
        <p>{{ $reservation->patient->past_disease_flag == 0 ? 'はい' : 'いいえ' }}</p>
    </div>

    <div class="border-b">
        <p>4-2.治療中の病気・服薬</p>
        <p>{{ $reservation->patient->past_disease_detail ?? '未入力' }}</p>
    </div>

    <div class="border-b">
        <p>5-1.お薬や食べ物のアレルギーはありますか？</p>
        <p>{{ $reservation->patient->allergy_flag == 0 ? 'はい' : 'いいえ' }}</p>
    </div>

    <div class="border-b">
        <p>5-2.お薬名や食べ物を記入してください。</p>
        <p>{{ $reservation->patient->allergy_detail ?? '未入力' }}</p>
    </div>

    <div class="border-b">
        <p>6.事前にお伝えしたい内容がございましたらご記入ください。</p>
        <p>{{ $reservation->patient->notes ?? '未入力' }}</p>
    </div>

</body>

</html>
