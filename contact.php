<?php
session_start();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Sound Space/お問い合わせ</title>
    <link href='style.css' rel='stylesheet'>
</head>
    
<body>

<?php include "common/header.php"; ?>

<main>

    <div id="contact_title">
        <h1>お問い合わせ</h1>
    </div>

    <form class="form_group" action="contact_confirm.php" method="post">

        <div class="name_box">
            <label for="family_name">姓</label>
            <input type="text" name="family_name" placeholder="山田">

            <label for="last_name">名</label>
            <input type="text" name="last_name" placeholder="太郎">
            
        </div>

        <div>
            <label for="email">メールアドレス</label>
            <br>
            <input type="text" name="email" placeholder="example@example.com">
        </div>

        <div>
            <label for="phone_number">電話番号</label>
            <br>
            <input type="text" name="phone_number" placeholder="09012345678">
        </div>

        <div>
            <label for="contact_type">お問い合わせの種類</label>
            <br>
            <select name="contact_type" id="c_type">
                <option value="product">商品について</option>
                <option value="order">注文・発送について</option>
                <option value="return">返品・交換</option>
                <option value="payment">支払いについて</option>
                <option value="other">その他</option>
            </select>
        </div>

        <div>
            <label for="contact_title">件名</label>
            <br>
            <input type="text" name="contact_title" placeholder="商品についてのお問い合わせ">
        </div>

        <div>
            <label for="message">メッセージ</label>
            <br>
            <textarea name="message" rows="8"></textarea>
        </div>

        <div style="text-align:end">
            <button>送信する</button>
        </div>






    </form>


</main>


<?php include "common/footer.php"; ?>

</body>
</html>