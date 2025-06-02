
<?php


//ファイルの読み込み
require_once "user_login/db_connect.php";
require_once "user_login/functions.php";


// セッション変数 $_SESSION["cart_items"]を確認。空であればcart_empty.phpへリダイレクト

session_start();


if($_SERVER["REQUEST_METHOD"]=="GET"){

    if(!isset($_SESSION["cart_items"]) || empty($_SESSION["cart_items"])){
        header("location:cart_empty.php");
        exit();
    }


}elseif($_SERVER["REQUEST_METHOD"]=="POST"){


    // 買い物かごで削除ボタンを押した時の処理

    if(isset($_POST["product_id"])&&isset($_POST["delete_item"])){
        // IDの検証
        if(!preg_match('/^[A-Z0-9]{10}$/', $_POST["product_id"])){
            exit("エラー：商品IDが正しくありません。");
        }
        $product_id=$_POST["product_id"];
        unset($_SESSION["cart_items"][$product_id]); //商品削除処理
        unset($_POST["delete_item"]);
        header("location:view_cart.php"); //ページ更新・getにリダイレクト
        exit();
    }

    /* POSTの中身が適切か確認スタート */

    if(isset($_POST["product_id"])&&isset($_POST["quantity"])){
        $product_id=$_POST["product_id"];
        $quantity=(int)$_POST["quantity"];

    // ID・数量の存在確認
    }else{
        exit("エラー：商品のIDまたは数量が指定されていません。");
    }

    // IDの検証
    if(!preg_match('/^[A-Z0-9]{10}$/', $product_id)){
        exit("エラー：商品IDが正しくありません。");
    }

    // 数量の検証
    if($quantity<1){
        exit("エラー：数量は1以上を指定してください。");
    }


    // データベースに指定の商品IDがあるか確認

    try{
        $sql="SELECT id, product_name, base_price, stock_quantity FROM products WHERE id=:id";
        $stmt=$pdo->prepare($sql);
        $stmt->execute(["id"=>$product_id]);
        $product=$stmt->fetch();

        if(!$product){
            exit("エラー：指定された商品が見つかりません。");
        }

    }catch(PDOException $e){

        exit("エラー：商品データの取得に失敗しました。".$e->getMessage());

    }

    // 在庫確認
    if($quantity>$product["stock_quantity"]){

        exit("エラー：在庫が不足しています。");
    }

    /* POSTの中身が適切か確認終わり */

    /* SESSION変数にPOSTの中身を追加する作業開始 */
    

    // すでに商品がカート内にある場合
    if(isset($_SESSION["cart_items"][$product_id])){
        $_SESSION["cart_items"][$product_id]["quantity"]+=$quantity;

    // 商品がカート内にない場合（新規追加）
    }else{
        $_SESSION["cart_items"][$product_id]=
        [
            "quantity"=>$quantity,
            "product_name"=>$product["product_name"],
            "base_price"=>$product["base_price"]

        ];
    }


    // PRGパターンで二重送信防止
    header("Location: view_cart.php");
    exit();



}


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
        <p>価格</p>

        <div class="products_in_cart">


            <?php

            $sum=0; //合計金額用

            foreach($_SESSION["cart_items"] as $id => $item):
            
            ?>

            <div class="cart_item">
                <img src="img/products/<?php echo h($id);?>.jpg">
                <p class="cart_item_name"><?php echo h($item["product_name"]);?></p>
                <p class="cart_item_price">¥ <?php echo number_format($item["base_price"]);?></p>
                <p class="cart_item_quantity">数量：<?php echo h($item["quantity"]);?> 個</p>
                <form class="delete_cart_item" action="view_cart.php" method="post">
                    <input type="hidden" name="product_id" value="<?php echo h($id);?>">
                    <input type="hidden" name="delete_item" value="1">
                    <button class="link_style_btn" type="submit">削除</button>
                </form>
            </div>

            <?php $sum += $item["base_price"]*(int)(h($item["quantity"]));?>

            <?php endforeach;?>


        </div>

    </div>

    <div class="sum_price_info">
        <div style="text-align:center">
            <button onclick="location.href='delivery_form.php'">ご購入手続き</button>
        </div>
        <h3>小計 ¥ <?php echo number_format($sum);?></h3>
        <h3>送料 ¥ 610</h3> <!--今回は地域ごとの送料は考慮しない-->
        <h2>合計 ¥ <?php echo number_format($sum+610);?></h2>

    </div>
</div>

</main>


<?php include "common/footer.php"; ?>

</body>
</html>