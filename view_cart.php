
<?php
// セッション変数 $_SESSION["cart_items"]を確認。空であればcart_empty.phpへリダイレクト

/*
session_start();
if(!isset($_SESSION["cart_items"]) || $_SESSION["cart_items"] !== true){
    header("location:cart_empty.php");
    exit;
}
*/
?>

<!DOCTYPE html>
<html>
<head>
    <title>Sound Space/買い物かご</title>
    <link href='style.css' rel='stylesheet'>
</head>
    
<body>

<?php include "common/header.php"; ?>

<main>

<div class="cart_and_sum_price">
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

    <div class="sum_price_info">
        <div style="text-align:center">
            <button onclick="location.href='delivery_form.php'">ご購入手続き</button>
        </div>
        <h3>小計　円</h3>
        <h4>送料　円</h4>
    </div>
</div>

</main>


<?php include "common/footer.php"; ?>

</body>
</html>