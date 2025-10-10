<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>予約確認メール</title>
</head>
<body>
    <h2>ご予約ありがとうございます。</h2>

    <p>以下の内容で予約を承りました。</p>

    <table border="1" cellpadding="5" cellspacing="0">
        <tr>
            <th>予約番号</th>
            <td>{{ $patient->reservation_number ?? '未入力' }}</td>
        </tr>
        <tr>
            <th>予約日</th>
            <td>{{ $patient->slot->date ?? '未入力' }}</td>
        </tr>
        <tr>
            <th>時間帯</th>
            <td>{{ \Carbon\Carbon::parse($reservation->slot->start_time)->format('G:i') }} ～ {{ \Carbon\Carbon::parse($reservation->slot->end_time)->format('G:i') }}</td>
        </tr>
        <tr>
            <th>お名前</th>
            <td>{{ $patient->name }}</td>
        </tr>
        <tr>
            <th>メールアドレス</th>
            <td>{{ $patient->email ?? '未入力' }}</td>
        </tr>
    </table>

    <p>キャンセルや変更はお電話にてご連絡ください。</p>
</body>
</html>
