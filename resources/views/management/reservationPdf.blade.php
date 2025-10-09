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
            font-size: 12px;
        }

        table {
            border-collapse: collapse;
            width: 100%;
            margin-bottom: 10px;
        }

        th,
        td {
            border: 1px solid #000;
            padding: 5px;
            text-align: left;
        }

        th {
            background-color: #f0f0f0;
        }

        h3 {
            margin-top: 20px;
            margin-bottom: 5px;
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

    <h3>症状</h3>
    <p>いつ頃から症状がありますか？ {{ $reservation->patient->symptoms_start ?? '未入力' }}</p>
    <p>症状の種類: {{ $reservation->patient->symptoms_type ?? '未入力' }}</p>
    <p>その他症状: {{ $reservation->patient->symptoms_other ?? '未入力' }}</p>

    <h3>既往歴・アレルギー・備考</h3>
    <p>既往歴: {{ $reservation->patient->past_disease_flag ?? '未入力' }}</p>
    <p>治療中の病気・服薬: {{ $reservation->patient->past_disease_detail ?? '未入力' }}</p>
    <p>アレルギー: {{ $reservation->patient->allergy_flag ?? '未入力' }}</p>
    <p>アレルギー詳細: {{ $reservation->patient->allergy_detail ?? '未入力' }}</p>
    <p>備考: {{ $reservation->patient->notes ?? '未入力' }}</p>

</body>

</html>
