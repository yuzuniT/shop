<?php
session_start();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Sound Space/お届け先情報の入力</title>
    <link href='style.css' rel='stylesheet'>
</head>
    
<body>

<?php include "common/header.php"; ?>

<main>


    <h1>お届け先情報の入力</h1>
    <form class="form_group" action="order_confirm.php" method="post">

        <div class="name_box">
            <label for="family_name">姓</label>
            <input type="text" name="family_name" placeholder="山田">

            <label for="last_name">名</label>
            <input type="text" name="last_name" placeholder="太郎">
            
        </div>

        <div>
            <label for="postal_number">郵便番号</label>
            <input type="text" name="postal_number" placeholder="1234567">
        </div>

        <div>
            <label for="address">住所</label>
            <input type="text" name="address" placeholder="東京都新宿区西新宿1-2-3">
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
            <label for="payment_method">お支払い方法</label>
            <br>
            <select name="payment_method">
                <option value="credit_card">クレジットカード</option>
                <option value="convenient_store">コンビニ決済</option>
                <option value="cash_on_delivery">代金引換</option>
                <option value="bank_transfer">銀行振込</option>
            </select>
        </div>

        


        <div style="text-align:end">
            <button type="button" onclick="location.href='view_cart.php'">戻る</button>
            <button type="submit">次へ進む</button>
        </div>






    </form>



</main>


<?php include "common/footer.php"; ?>

</body>
</html>