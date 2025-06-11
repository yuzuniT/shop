<?php

// ファイルの読み込み
require_once "user_login/functions.php";
require_once "user_login/db_connect.php";

session_start();

// 注文受付メールの注文日時用
date_default_timezone_set('Asia/Tokyo');

// 支払い方法表示用
$payment_method=[
    "credit_card"=>"クレジットカード",
    "convenient_store"=>"コンビニ決済",
    "cash_on_delivery"=>"代金引換",
    "bank_transfer"=>"銀行振込"
];
//注文情報の中身を格納する変数の定義と初期化
$order_datas = [
    'family_name'  => '',
    'last_name'  => '',
    'postal_code'  => '',
    'address'  => '',
    'email'  => '',
    'phone_number'  => '',
    'total_amount' => '',
    'payment_method'  => ''
];

//注文商品情報の中身を格納する変数の定義と初期化
$item_datas=[];


// 注文情報の中身を変数に格納
foreach($order_datas as $key => $value) {
    if(isset($_SESSION["order_datas"][$key]) && !empty($_SESSION["order_datas"][$key])) {
        $order_datas[$key] = $_SESSION["order_datas"][$key];
    }
}

// 注文商品情報の中身を変数に格納
foreach(array_keys($_SESSION["cart_items"]) as $product_id) {
        $item_datas[$product_id]["product_name"]=$_SESSION["cart_items"][$product_id]["product_name"];
        $item_datas[$product_id]["quantity"]=$_SESSION["cart_items"][$product_id]["quantity"];
        $item_datas[$product_id]["base_price"]=$_SESSION["cart_items"][$product_id]["base_price"];

}



if($_SERVER["REQUEST_METHOD"] == "POST"){


    //注文確定ボタンが押された時、DBへの新規登録を実行
    if($_POST["confirm"]==TRUE){


        $count = 0;
        $columns = '';
        $values = '';
        foreach (array_keys($order_datas) as $key) {
            if($count > 0){
                $columns .= ',';
                $values .= ',';
            }
            $columns .= $key;
            $values .= ':'.$key;
            $count++;
        }

        $pdo->beginTransaction();//トランザクション処理
        try {
            $sql_orders = 'insert into shop.orders ('.$columns .')values('.$values.')';
            $sql_items = 'insert into shop.order_items(order_id,product_id,base_price,quantity) values (:order_id,:product_id,:base_price,:quantity)';
            $stmt_orders = $pdo->prepare($sql_orders);
            $stmt_orders->execute($order_datas);


            $order_id=$pdo->lastInsertId();

            $stmt_items = $pdo->prepare($sql_items);
            foreach($item_datas as $product_id =>$item){
                $stmt_items->bindValue(":order_id",$order_id,PDO::PARAM_STR);
                $stmt_items->bindValue(":product_id",$product_id,PDO::PARAM_STR);
                $stmt_items->bindValue(":base_price",$item["base_price"],PDO::PARAM_STR);
                $stmt_items->bindValue(":quantity",$item["quantity"],PDO::PARAM_INT);
                $stmt_items->execute();

                // 現在の商品在庫数を取得
                $stmt_select=$pdo->prepare("SELECT stock_quantity FROM shop.products WHERE id=:id");
                $stmt_select->execute(["id"=>$product_id]);
                $result=$stmt_select->fetch(PDO::FETCH_ASSOC);
                $stock_quantity=$result["stock_quantity"];

                // 在庫数が足りるかチェック
                if ($stock_quantity<$item["quantity"]){
                    echo 'エラー：在庫数が足りません。';
                    $pdo->rollBack();
                // 在庫数から購入数を減らして更新
                }else{

                    $stock_quantity-=$item["quantity"];

                    // もし在庫が0になったら商品のアクティブ状態をFALSEへ
                    if($stock_quantity==0){
                        $stmt_update=$pdo->prepare("UPDATE shop.products SET is_active=0 WHERE id=:id");
                        $stmt_update->execute([":id"=>$product_id]);

                    }
                        
                    $stmt_update=$pdo->prepare("UPDATE shop.products SET stock_quantity=:stock_quantity WHERE id=:id");
                    $stmt_update->execute([":stock_quantity"=>$stock_quantity, ":id"=>$product_id]);


                    }
                }

            $pdo->commit();

            // 利用者・管理者に注文受付メールを送信


            // 管理者のメールアドレスを取得
            $stmt_admin=$pdo->query("SELECT email FROM shop.admin");
            $result=$stmt_admin->fetch(PDO::FETCH_ASSOC);
            $email_admin=$result["email"];

            $to=$order_datas["email"];

            $item_list="";
            $subtotal=0;

            foreach($item_datas as $product_id => $item){
                $item_total=$item["base_price"]*$item["quantity"];
                $subtotal += $item_total;
                $item_list .= "商品名：{$item["product_name"]} | 価格：¥ ". number_format($item["base_price"])." | 数量：{$item["quantity"]} | 小計：¥ ". number_format($item_total) ."\n";
            }

            $total=number_format($subtotal + 610);
            $subtotal_number=number_format($subtotal);
            $shipping_fee=number_format(610);

            $order_date = date('Y-m-d H:i:s');

            $order_id_zerofill=str_pad($order_id,8,"0",STR_PAD_LEFT);

            $subject='ご注文ありがとうございます！注文受付完了のお知らせ（注文番号: ' . $order_id_zerofill . '）';
            $message=<<<EOD

            {$order_datas['family_name']} {$order_datas['last_name']} 様

            この度は、Sound Space でのご注文をいただき、誠にありがとうございます。
            以下の内容でご注文を受付いたしました。

            ---

            注文番号： {$order_id_zerofill}
            注文日時： {$order_date}

            [お届け先情報]
            お名前： {$order_datas['family_name']} {$order_datas['last_name']}
            郵便番号： {$order_datas['postal_code']}
            住所： {$order_datas['address']}
            メールアドレス： {$order_datas['email']}
            電話番号： {$order_datas['phone_number']}
            お支払い方法： {$payment_method[$order_datas['payment_method']]}

            [ご注文内容]
            {$item_list}
            小計： ¥ {$subtotal_number}
            送料： ¥ {$shipping_fee}
            合計： ¥ {$total}

            ---

            [配送予定]
            商品は通常、ご注文から3～5営業日以内に発送いたします。
            発送が完了しましたら、改めて発送完了メールをお送りいたします。

            [お問い合わせ]
            ご注文内容の確認や変更、その他ご質問がございましたら、以下までご連絡ください。
            メール: support@soundspace.com
            電話: 0120-XXX-XXXX（受付時間: 平日9:00～17:00）

            今後ともSound Spaceをよろしくお願いいたします。

            ---
            Sound Space カスタマーサポート
            https://www.soundspace.com





            EOD;
            $headers="From:ridgeintojp@gmail.com"."\r\n";
            $headers.="Bcc:".$email_admin;// BCCに管理者のメールアドレスを挿入

            mb_language("Japanese");
            mb_internal_encoding("UTF-8");
            mb_send_mail($to, $subject, $message, $headers);


            unset($_SESSION["cart_items"]);
            unset($_SESSION["order_datas"]);
            header("location: order_complete.php?order_id=".$order_id);
            exit;
        } catch (PDOException $e) {
            echo 'エラー：登録処理に失敗しました。';
            echo $e->getMessage();
            $pdo->rollBack();
        }
    }

}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Sound Space/ご注文情報の確認</title>
    <link href='style.css' rel='stylesheet'>
</head>
    
<body>

<?php include "common/header.php"; ?>

<main>

<h1 style="margin-top:0;">ご注文情報の確認</h1>

<div class="order_and_sum_price">

    <div class="delivery_and_cart">

        <div class="delivery_info">
            <h2>お届け先情報</h2>
            <div class="delivery_info_detail">
                <table>

                    <tr>
                        <th>お名前</th>
                        <td><?php echo $order_datas["family_name"]." ".$order_datas["last_name"];?></td>
                    </tr>
                    <tr>
                        <th>郵便番号</th>
                        <td><?php echo $order_datas["postal_code"];?></td>
                    </tr>
                    <tr>
                        <th>住所</th>
                        <td><?php echo $order_datas["address"];?></td>
                    </tr>
                    <tr>
                        <th>メールアドレス</th>
                        <td><?php echo $order_datas["email"];?></td>
                    </tr>
                    <tr>
                        <th>電話番号</th>
                        <td><?php echo $order_datas["phone_number"];?></td>
                    </tr>
                    <tr>
                        <th>お支払い方法</th>
                        <td><?php echo $payment_method[$order_datas["payment_method"]];?></td>
                    </tr>
                </table>
            </div>

        </div>

        <div class="cart_and_sum_price">
            <div class="cart_info">
                <h2>買い物かご</h2>
                <p>価格</p>

                <div class="products_in_cart">


                    <?php

                    $sum=0; //合計金額用

                    foreach($item_datas as $product_id => $item):
                    
                    ?>

                    <div class="cart_item">
                        <img src="img/products/<?php echo h($product_id);?>.jpg">
                        <p class="cart_item_name"><?php echo h($item["product_name"]);?></p>
                        <p class="cart_item_price">¥ <?php echo number_format($item["base_price"]);?></p>
                        <p class="cart_item_quantity">数量：<?php echo h($item["quantity"]);?> 個</p>

                    </div>

                    <?php $sum += $item["base_price"]*(int)(h($item["quantity"]));?>

                    <?php endforeach;?>


                </div>

            </div>

        </div>
    </div>
    <div class="sum_price_info">
        <div class="confirm_btn_container">
            <button onclick="location.href='delivery_form.php'">戻る</button>
            <form action="order_confirm.php" method="post">
                <input type="hidden" name="total_amount" value="<?php echo $sum;?>">
                <input type="hidden" name="confirm" value="1">
                <button type="submit">注文確定</button>
            </form>
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