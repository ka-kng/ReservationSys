<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>予約確認メール</title>
</head>
<body>
    <p>{{ $patient->name }} 様</p>

    <h2>ご予約ありがとうございます</h2>

    <p>以下の内容でご予約を承りました。内容をご確認ください。</p>

    <p>
        【予約番号】 {{ $reservation->reservation_number }}<br>
        【予約日】 {{ $reservation->slot->date ?? '未入力' }}<br>
        【時間帯】 {{ \Carbon\Carbon::parse($reservation->slot->start_time)->format('G:i') }} ～ {{ \Carbon\Carbon::parse($reservation->slot->end_time)->format('G:i') }}<br>
    </p>

    <p>ご来院にあたっての注意事項：</p>
    <ul>
        <li>当日は予約時間の10分前までにお越しください。</li>
        <li>体調に変化がある場合は事前にご連絡ください。</li>
        <li>キャンセルや変更はお電話にて承っております。</li>
    </ul>

    <p>※このメールに心当たりがない場合は、お手数ですが当院までご連絡ください。</p>

    <p>どうぞよろしくお願いいたします。</p>

    <p>――――――――――――――――――<br>
       クリニック名<br>
       住所・電話番号・営業時間など
    </p>
</body>
</html>
