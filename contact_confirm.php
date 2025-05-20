<!DOCTYPE html>
<html>
<head>
    <title>Sound Space/お問い合わせ内容確認</title>
    <link href='style.css' rel='stylesheet'>
</head>
    
<body>

<?php include "common/header.php"; ?>

<main>


<div class="confirmation_container">
    <h2>お問い合わせ内容確認</h2>
    <p>以下の内容でよろしければ、「送信する」ボタンを押してください。</p>

    <dl class="confirmation_grid">

        <dt class="label">お名前</dt>
        <dd class="value">山田　太郎</dd>

        <dt class="label">メールアドレス</dt>
        <dd class="value">example@example.com</dd>

        <dt class="label">お電話番号</dt>
        <dd class="value">09012345678</dd>

        <dt class="label">お問い合わせの種類</dt>
        <dd class="value">商品について</dd>

        <dt class="label">件名</dt>
        <dd class="value">商品についてのお問い合わせ</dd>

        <dt class="label">メッセージ</dt>
        <dd class="value">test</dd>
        
    </dl>


    <div style="text-align:end">
        <button type="button" onclick="location.href='contact.php'">戻る</button>
        <button type="button" onclick="location.href='contact_complete.php'">送信する</button>
    </div>

</div>


</main>


<?php include "common/footer.php"; ?>

</body>
</html>