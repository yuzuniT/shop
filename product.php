<?php
//ファイルの読み込み
require_once "user_login/db_connect.php";
require_once "user_login/functions.php";

session_start();

// 商品IDの存在確認
if (!isset($_GET["id"])||empty($_GET["id"])){
    exit("エラー：商品IDが指定されていません。");
}

// 商品データの取得
try{
    $stmt=$pdo->prepare("SELECT id, product_name, description, base_price, stock_quantity FROM products WHERE id=:id");
    $stmt->execute(["id"=>$_GET['id']]);
    $product=$stmt->fetch();

    if (!$product){
        exit("エラー：指定された商品が見つかりません。");
    }

}catch(PDOException $e){

    exit("エラー：商品データの取得に失敗しました。".$e->getMessage());

}

?>

<!DOCTYPE html>

<html>

<head>
    <title>Sound Space/<?php echo h($product["product_name"]);?></title>
    <link href='style.css' rel='stylesheet'>
   
</head>

<body>

<?php include "common/header.php"; ?>


<main>
    <div class="product_info">
        <img src="img/products/<?php echo h($product["id"])?>.jpg">

        <form class="detail" action="view_cart.php" method="post">
            <h2><?php echo h($product['product_name'])?></h2>
            <h3><?php echo h($product['description'])?></h3>
            <h3>¥ <?php echo number_format($product['base_price'])?></h3>
            <p>在庫：<?php echo $product["stock_quantity"];?> 個</p>
            <input type="hidden" name="product_id" value="<?php echo h($product["id"]);?>">
            <div class="quantity_and_button">
                <input type="number" name="quantity" value="1" min="1" max="<?php echo $product["stock_quantity"];?>">
                <button type="submit">カートに追加する</button>
            </div>
        </form>
    </div>






</main>






<?php include "common/footer.php"; ?>


</body>
</html>