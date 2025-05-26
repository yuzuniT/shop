<?php
session_start();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Sound Space/注文情報の確認</title>
    <link href='style.css' rel='stylesheet'>
</head>
    
<body>

<?php include "common/header.php"; ?>

<main>

<div class="order_and_sum_price">

    <div class="delivery_and_cart">

        <div class="delivery_info">
            <h2>お届け先情報</h2>
            <div class="delivery_info_detail">
                <p>お名前：</p>
                <p>郵便番号：</p>
                <p>住所：</p>
                <p>メールアドレス：</p>
                <p>電話番号：</p>
                <p>お支払い方法：</p>
            </div>

        </div>

        

            <div class="cart_info">
                <h2>買い物かご</h2>

                <div class="products_in_cart">

                    <div class="detail_in_cart">
                        <img class="img_in_cart" src="img/products/placeholder.png">
                        <h3>商品名</h3>
                        <h4>価格</h4>


                    </div>

                </div>

            </div>

    </div>


    <div class="sum_price_info">
        <div style="text-align:center">
            <button onclick="location.href='order_complete.php'">注文確定</button>
        </div>
        <h3>小計　円</h3>
        <h4>送料　円</h4>
    </div>


</div>

</main>


<?php include "common/footer.php"; ?>

</body>
</html>