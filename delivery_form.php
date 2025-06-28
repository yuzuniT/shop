<?php

//ファイルの読み込み
require_once "user_login/db_connect.php";
require_once "user_login/functions.php";

session_start();

// POSTされてきたデータを格納する変数の定義と初期化
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

// 合計金額をセッションから取得
$total_amount=isset($_SESSION["total_amount"]) ? $_SESSION["total_amount"] : 0;

// $_POST["total_amount"]があれば上書き
if(isset($_POST["total_amount"])){
    $total_amount=$_POST["total_amount"];
    $_SESSION["total_amount"]=$total_amount;// セッションをPOSTの値で上書き
}


$errors=[];



if($_SERVER["REQUEST_METHOD"] == "POST"){

    //CSRF対策
    checkToken();

    // POSTされてきたデータを変数に格納
    foreach($order_datas as $key => $value) {
        if($value = filter_input(INPUT_POST, $key, FILTER_DEFAULT)) {
            $order_datas[$key] = $value;
        }
    }

    // このページで「次へ進む」ボタンが押された後
    if(isset($_POST["submit"]) && $_POST["submit"]==TRUE){
  
        // バリデーション
        $errors = validation($order_datas,"delivery_info");
        unset($_POST["submit"]);

        // エラーがなければセッション変数に注文データを格納して注文確認ページへ
        if(empty($errors)){
            $_SESSION["order_datas"]=$order_datas;
            header("location:order_confirm.php");
            exit();
        }

    }


    



}
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
    <form class="form_group" action="delivery_form.php" method="post">

        <div class="name_box">
            <label for="family_name">姓<span class="input_required">（必須）</span></label>
            <input type="text" name="family_name" placeholder="山田" value="<?php echo orderValue("family_name",$order_datas)?>" required>
            <span class="invalid-feedback"><?php echo isset($errors['family_name']) ? h($errors['family_name']) : ''; ?></span>

            <label for="last_name">名<span class="input_required">（必須）</span></label>
            <input type="text" name="last_name" placeholder="太郎" value="<?php echo orderValue("last_name",$order_datas)?>" required>
            <span class="invalid-feedback"><?php echo isset($errors['last_name']) ? h($errors['last_name']) : ''; ?></span>

        </div>

        <div>
            <label for="postal_code">郵便番号<span class="input_required">（必須）</span></label>
            <input type="text" name="postal_code" placeholder="1234567" value="<?php echo orderValue("postal_code",$order_datas)?>" required>
            <span class="invalid-feedback"><?php echo isset($errors['postal_code']) ? h($errors['postal_code']) : ''; ?></span>
        </div>

        <div>
            <label for="address">住所<span class="input_required">（必須）</span></label>
            <input type="text" name="address" placeholder="東京都新宿区西新宿1-2-3" value="<?php echo orderValue("address",$order_datas)?>" required>
            <span class="invalid-feedback"><?php echo isset($errors['address']) ? h($errors['address']) : ''; ?></span>
        </div>

        <div>
            <label for="email">メールアドレス<span class="input_required">（必須）</span></label>
            <br>
            <input type="text" name="email" placeholder="example@example.com" value="<?php echo orderValue("email",$order_datas)?>" required>
            <span class="invalid-feedback"><?php echo isset($errors['email']) ? h($errors['email']) : ''; ?></span>

        </div>

        <div>
            <label for="phone_number">電話番号</label>
            <br>
            <input type="text" name="phone_number" placeholder="09012345678" value="<?php echo orderValue("phone_number",$order_datas)?>">
            <span class="invalid-feedback"><?php echo isset($errors['phone_number']) ? h($errors['phone_number']) : ''; ?></span>
        </div>

        <div>
            <label for="payment_method">お支払い方法<span class="input_required">（必須）</span></label>
            <br>
            <select name="payment_method" required>
                <option value="credit_card">クレジットカード</option>
                <option value="convenient_store">コンビニ決済</option>
                <option value="cash_on_delivery">代金引換</option>
                <option value="bank_transfer">銀行振込</option>
            </select>
            <span class="invalid-feedback"><?php echo isset($errors['payment_method']) ? h($errors['payment_method']) : ''; ?></span>

        </div>

        


        <div style="text-align:end">
            <button type="button" onclick="location.href='view_cart.php'">戻る</button>
            <input type="hidden" name="token" value="<?php echo h($_SESSION['token']); ?>">
            <input type="hidden" name="total_amount" value="<?php echo h($total_amount); ?>">
            <input type="hidden" name="submit" value="1">
            <button type="submit">次へ進む</button>
        </div>






    </form>



</main>


<?php include "common/footer.php"; ?>

</body>
</html>