<?php
session_start();
?>


<!DOCTYPE html>
<html>
<head>
    <title>Sound Space/イヤホン・ヘッドホン専門店</title>
    <link href='style.css' rel='stylesheet'>
   
</head>
    
<body>

<?php include "common/header.php"; ?>

<main>

    <h2>商品一覧</h2>

    <div class="product_list">

        <div class="product_box">
            <img class="product_img" src="img/products/placeholder.png">
            <h3>商品a</h3>
            <p>商品説明</p>
            <p>価格</p>
            <a href="product.php">購入</a>
        </div>



        <div class="product_box">
            <img class="product_img" src="img/products/placeholder.png">
            <h3>商品b</h3>
            <p>商品説明</p>
            <p>価格</p>
           <a href="product.php">購入</a>
        </div>



        <div class="product_box">
            <img class="product_img" src="img/products/placeholder.png">
            <h3>商品c</h3>
            <p>商品説明</p>
            <p>価格</p>
           <a href="product.php">購入</a>
        </div>

    </div>


            

</main>


<?php include "common/footer.php"; ?>

</body>
</html>

