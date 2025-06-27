<?php
//ファイルの読み込み
require_once "user_login/db_connect.php";
require_once "user_login/functions.php";

session_start();

// ページネーションの設定
$items_per_page= 8; //1ページに表示する商品数(4列*2行)

// ヘッダーから商品が検索された場合
if(isset($_GET["search"]) && !empty($_GET["search"])){

    $keyword='%'.h($_GET["search"]).'%';

    try{
        // キーワードを含む商品の数を取得 (ページネーション用)
        $stmt=$pdo->prepare("SELECT COUNT(*) FROM products WHERE is_active=TRUE and product_name LIKE :keyword");
        $stmt->execute(["keyword"=>$keyword]);
        $total_items=$stmt->fetchcolumn();
        $total_pages=ceil($total_items / $items_per_page);
        $total_pages=$total_pages==0 ? 1 : $total_pages;

        // 現在ページ数の設定
        if (isset($_GET["page"]) && is_numeric($_GET["page"]) && $_GET["page"] >=1 && $_GET["page"] <= $total_pages){
            $page=$_GET["page"];
        }
        else{
            $page=1;
        }

        // 何番目の商品から取得するかを表す
        $offset=($page-1)*$items_per_page;

        // 商品データを取得
        $stmt=$pdo->prepare("SELECT id, product_name, description, base_price FROM products WHERE is_active=TRUE and product_name LIKE ? LIMIT ? OFFSET ?");
        $stmt->execute([$keyword, (int)$items_per_page, (int)$offset]);
        $products=$stmt->fetchall();

    }catch(PDOException $e){

        exit("データベースエラー：".$e->getMessage());

    }

// 商品が検索されていない場合
}else{

    try{
        // 全商品の数を取得 (ページネーション用)
        $stmt=$pdo->query("SELECT COUNT(*) FROM products WHERE is_active=TRUE");
        $total_items=$stmt->fetchcolumn();
        $total_pages=ceil($total_items / $items_per_page);
        $total_pages=$total_pages==0 ? 1 : $total_pages;

        // 現在ページ数の設定
        if (isset($_GET["page"]) && is_numeric($_GET["page"]) && $_GET["page"] >=1 && $_GET["page"] <= $total_pages){
            $page=$_GET["page"];
        }
        else{
            $page=1;
        }

        // 何番目の商品から取得するかを表す
        $offset=($page-1)*$items_per_page;

        // 商品データを取得
        $stmt=$pdo->prepare("SELECT id, product_name, description, base_price FROM products WHERE is_active=TRUE LIMIT ? OFFSET ?");
        $stmt->execute([(int)$items_per_page, (int)$offset]);
        $products=$stmt->fetchall();

    }catch(PDOException $e){

        exit("データベースエラー：".$e->getMessage());

    }

}
/* 商品検索結果表示用 */

// 何番目の商品からそのページに表示するかを表す
$min=$total_items == 0 ? 0 : $offset + 1;

// 何番目の商品までそのページに表示するかを表す
$max = $offset + $items_per_page;
$max= $max > $total_items ? $total_items : $max;

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

    <?php if(isset($_GET["search"]) && !empty($_GET["search"])):?>

        <h2>検索結果：<span class="search_keyword">"<?php echo h($_GET["search"])?>"</span></h2>

        <p><?php echo $total_items. " 件のうち " .$min. "-" .$max. "件" ;?></p>

    <?php endif;?>

    <h1>商品一覧</h1>

    <div class="product_list">

        <?php if (empty($products)):?>
            <p>商品がありません。</p>
        <?php else:?>

            <?php foreach($products as $product):?>


            <div class="product_box">
                <img class="product_img" src="img/products/<?php echo h($product['id'])?>.jpg" alt="<?php echo h($product['product_name'])?>">
                <h3 class="index_product_name"><?php echo h($product['product_name'])?></h3>
                <p class="index_product_desc"><?php echo h($product['description'])?></p>
                <h4 class="index_product_price">¥ <?php echo number_format($product['base_price'])?></h4> <!--数値なのでエスケープの必要なし-->
                <a class="index_purchase_button" href="product.php?id=<?php echo h($product["id"])?>">購入</a>
            </div>

            <?php endforeach;?>
        <?php endif;?>

    </div>



    <!-- ページネーション用 -->


    <!-- 検索した場合 -->
    <?php if(isset($_GET["search"]) && !empty($_GET["search"])):?>

        <div class="pagination">
            <?php if ($page>1) :?>
                <a href="?search=<?php echo h($_GET["search"])?>&page=<?php echo $page-1;?>">前へ</a>
            <?php endif;?>

            <span>ページ <?php echo $page?> / <?php echo $total_pages?></span>

            <?php if ($page<$total_pages) :?>
                <a href="?search=<?php echo h($_GET["search"])?>&page=<?php echo $page+1;?>">次へ</a>
            <?php endif;?>

        </div>

    
    <!-- 検索しなかった場合 -->
    <?php else:?>


        <div class="pagination">
            <?php if ($page>1) :?>
                <a href="?page=<?php echo $page-1;?>">前へ</a>
            <?php endif;?>

            <span>ページ <?php echo $page?> / <?php echo $total_pages?></span>

            <?php if ($page<$total_pages) :?>
                <a href="?page=<?php echo $page+1;?>">次へ</a>
            <?php endif;?>

        </div>

    <?php endif;?>
            

</main>


<?php include "common/footer.php"; ?>

</body>
</html>