<?php
//ファイルの読み込み
require_once "user_login/db_connect.php";
require_once "user_login/functions.php";

session_start();

// ページネーションの設定
$items_per_page= 8; //1ページに表示する商品数(4列*2行)

if (isset($_GET["page"]) && is_numeric($_GET["page"])){
    $page=$_GET["page"];
}
else{
    $page=1;
}

$offset=($page-1)*$items_per_page; //全商品中何番目の商品からそのページに表示するかを表す


// 全商品を取得 (ページネーション用)
$stmt=$pdo->query("SELECT COUNT(*) FROM products WHERE is_active=TRUE");
$total_items=$stmt->fetchcolumn();
$total_pages=ceil($total_items / $items_per_page);

// 商品データを取得
$stmt=$pdo->prepare("SELECT id, product_name, description, base_price FROM products WHERE is_active=TRUE LIMIT ? OFFSET ?");
$stmt->execute(["$items_per_page", $offset]);
$products=$stmt->fetchall();

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

    <h1>商品一覧</h1>

    <div class="product_list">

        <?php if (empty($products)):?>
            <p>商品がありません。</p>
        <?php else:?>

            <?php foreach($products as $product):?>


            <div class="product_box">
                <img class="product_img" src="img/products/<?php echo h($product['id']).'.jpg'?>" alt="<?php echo h($product['product_name'])?>">
                <h3 class="index_product_name"><?php echo h($product['product_name'])?></h3>
                <p class="index_product_desc"><?php echo h($product['description'])?></p>
                <h4 class="index_product_price">¥ <?php echo number_format($product['base_price'])?></h4> <!--数値なのでエスケープの必要なし-->
                <a class="index_purchase_button" href="product.php?id=<?php echo h($product["id"])?>">購入</a>
            </div>

            <?php endforeach;?>
        <?php endif;?>

    </div>



    <!-- ページネーション用 -->
    <div class="pagination">
        <?php if ($page>1) :?>
            <a href="?page=<?php echo $page-1;?>">前へ</a>
        <?php endif;?>

        <span>ページ <?php echo $page?> / <?php echo $total_pages?></span>

        <?php if ($page<$total_pages) :?>
            <a href="?page=<?php echo $page+1;?>">次へ</a>
        <?php endif;?>

    </div>
            

</main>


<?php include "common/footer.php"; ?>

</body>
</html>

