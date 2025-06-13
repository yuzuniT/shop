<?php
//ファイルの読み込み
require_once "user_login/functions.php";

session_start();

$order_id_zerofill=str_pad($_GET["order_id"],8,"0",STR_PAD_LEFT);
?>

<!DOCTYPE html>
<html>
<head>
    <title>注文完了</title>
    <link href='style.css' rel='stylesheet'>
</head>
    
<body>

<?php include "common/header.php"; ?>

<main>

    <h2>ご注文ありがとうございます。</h2>

    <div class="message_box">
        お客様のご注文を無事に受け付けました。
        ご注文番号： [<?php echo h($order_id_zerofill);?>]
        ご注文確認メールを、ご登録いただいたメールアドレスにお送りしました。  
        商品の発送準備が整い次第、発送完了メールをお送りします。
        通常、2〜5営業日以内に発送いたします。
        ※メールが届かない場合は、迷惑メールフォルダをご確認いただくか、お問い合わせください。
        引き続き、Sound Spaceをどうぞよろしくお願いいたします。

        <a href="index.php">トップページはこちら</a>
    </div>


</main>


<?php include "common/footer.php"; ?>

</body>
</html>