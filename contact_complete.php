<?php
//ファイルの読み込み
require_once "user_login/functions.php";

//セッションの開始
session_start();

$contact_id_zerofill=str_pad($_GET["contact_id"],8,"0",STR_PAD_LEFT);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Sound Space/お問い合わせ受付完了</title>
    <link href='style.css' rel='stylesheet'>
</head>
    
<body>

<?php include "common/header.php"; ?>

<main>

    <h2>お問い合わせありがとうございます。</h2>

    <div class="message_box">
        お客様のお問い合わせを無事に受け付けました。
        ご入力いただいた内容を確認後、通常2〜3営業日以内にご返信いたします。
        お問い合わせ番号：[<?php echo h($contact_id_zerofill);?>]
        ※ご返信はご登録いただいたメールアドレスにお送りします。
        万が一、返信が届かない場合は、迷惑メールフォルダをご確認いただくか、再度お問い合わせください。

        引き続き、Sound Spaceをどうぞよろしくお願いいたします。

        <a href="index.php">トップページはこちら</a>
    </div>

</main>


<?php include "common/footer.php"; ?>

</body>
</html>