<?php


//ファイルの読み込み
require_once "user_login/db_connect.php";
require_once "user_login/functions.php";

session_start();

// POSTされてきたデータを格納する変数の定義と初期化
$contact_datas = [
    'family_name'  => '',
    'last_name'  => '',
    'email'  => '',
    'phone_number'  => '',
    'contact_type' => '',
    'contact_title' => '',
    'message' => ''
];


$errors=[];

//GET通信だった場合はセッション変数にトークンを追加
if($_SERVER['REQUEST_METHOD'] != 'POST'){
    setToken();
}

if($_SERVER["REQUEST_METHOD"] == "POST"){

    //CSRF対策
    checkToken();

    // POSTされてきたデータを変数に格納
    foreach($contact_datas as $key => $value) {
        if($value = filter_input(INPUT_POST, $key, FILTER_DEFAULT)) {
            $contact_datas[$key] = $value;
        }
    }

    // このページで「次へ進む」ボタンが押された後
    if(isset($_POST["submit"]) && $_POST["submit"]==TRUE){
  
        // バリデーション
        $errors = validation($contact_datas,"contact");
        unset($_POST["submit"]);

        // エラーがなければセッション変数にお問い合わせデータを格納してお問い合わせ確認ページへ
        if(empty($errors)){
            $_SESSION["contact_datas"]=$contact_datas;
            header("location:contact_confirm.php");
            exit();
        }

    }


    



}

?>

<!DOCTYPE html>
<html>
<head>
    <title>Sound Space/お問い合わせ</title>
    <link href='style.css' rel='stylesheet'>
</head>
    
<body>

<?php include "common/header.php"; ?>

<main>

    <div id="contact_title">
        <h1>お問い合わせ</h1>
    </div>

    <form class="form_group" action="contact.php" method="post">

        <div class="name_box">
            <label for="family_name">姓<span class="input_required">（必須）</span></label>
            <input type="text" name="family_name" placeholder="山田" value="<?php echo orderValue("family_name",$contact_datas)?>" required>
            <span class="invalid-feedback"><?php echo isset($errors['family_name']) ? h($errors['family_name']) : ''; ?></span>

            <label for="last_name">名<span class="input_required">（必須）</span></label>
            <input type="text" name="last_name" placeholder="太郎" value="<?php echo orderValue("last_name",$contact_datas)?>" required>
            <span class="invalid-feedback"><?php echo isset($errors['last_name']) ? h($errors['last_name']) : ''; ?></span>

        </div>

        <div>
            <label for="email">メールアドレス<span class="input_required">（必須）</span></label>
            <br>
            <input type="text" name="email" placeholder="example@example.com" value="<?php echo orderValue("email",$contact_datas)?>" required>
            <span class="invalid-feedback"><?php echo isset($errors['email']) ? h($errors['email']) : ''; ?></span>

        </div>

        <div>
            <label for="phone_number">電話番号</label>
            <br>
            <input type="text" name="phone_number" placeholder="09012345678" value="<?php echo orderValue("phone_number",$contact_datas)?>">
            <span class="invalid-feedback"><?php echo isset($errors['phone_number']) ? h($errors['phone_number']) : ''; ?></span>
        </div>

        <div>
            <label for="contact_type">お問い合わせの種類<span class="input_required">（必須）</span></label>
            <br>
            <select name="contact_type" id="c_type" required>
                <option value="product">商品について</option>
                <option value="order">注文・発送について</option>
                <option value="return">返品・交換</option>
                <option value="payment">支払いについて</option>
                <option value="other">その他</option>
            </select>
            <span class="invalid-feedback"><?php echo isset($errors['contact_type']) ? h($errors['contact_type']) : ''; ?></span>

        </div>

        <div>
            <label for="contact_title">件名<span class="input_required">（必須）</span></label>
            <br>
            <input type="text" name="contact_title" placeholder="商品についてのお問い合わせ" required>
            <span class="invalid-feedback"><?php echo isset($errors['contact_title']) ? h($errors['contact_title']) : ''; ?></span>

        </div>

        <div>
            <label for="message">メッセージ<span class="input_required">（必須）</span></label>
            <br>
            <textarea name="message" rows="8" required></textarea>
            <span class="invalid-feedback"><?php echo isset($errors['message']) ? h($errors['message']) : ''; ?></span>

        </div>

        <div style="text-align:end">
            <input type="hidden" name="submit" value="1">
            <input type="hidden" name="token" value="<?php echo h($_SESSION['token']); ?>">
            <button type="submit">次へ進む</button>
        </div>






    </form>


</main>


<?php include "common/footer.php"; ?>

</body>
</html>