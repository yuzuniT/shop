<?php
//ファイルの読み込み
require_once "db_connect.php";
require_once "functions.php";
//セッション開始
session_start();

// セッション変数 $_SESSION["loggedin"]を確認。ログイン済だったらトップページへリダイレクト
if(isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true){
    header("location: ../index.php");
    exit;
}

//POSTされてきたデータを格納する変数の定義と初期化
$datas = [
    'email'  => '',
    'password'  => ''
];
$errors=[];
$login_err = "";

//GET通信だった場合はセッション変数にトークンを追加
if($_SERVER['REQUEST_METHOD'] != 'POST'){
    setToken();
}

//POST通信だった場合はログイン処理を開始
if($_SERVER["REQUEST_METHOD"] == "POST"){
    ////CSRF対策
    checkToken();

    // POSTされてきたデータを変数に格納
    foreach($datas as $key => $value) {
        if($value = filter_input(INPUT_POST, $key, FILTER_DEFAULT)) {
            $datas[$key] = $value;
        }
    }

    // バリデーション
    $errors = validation($datas,"login");
    if(empty($errors)){
        //メールアドレスから該当するユーザー情報を取得
        $sql = "SELECT id, email, password, family_name, last_name FROM members WHERE email = :email";
        $stmt = $pdo->prepare($sql);
        $stmt->bindValue('email',$datas['email'],PDO::PARAM_STR);
        $stmt->execute();

        //ユーザー情報があれば変数に格納
        if($row = $stmt->fetch(PDO::FETCH_ASSOC)){
            //ハッシュ化されたパスワードがあっているか確認。
            if (password_verify($datas['password'],$row['password'])) {
                //セッションIDをふりなおす
                session_regenerate_id(true);
                //セッション変数にログイン情報を格納
                $_SESSION["loggedin"] = true;
                $_SESSION["email"] = $row['email'];
                $_SESSION["id"] =  $row['id'];
                $_SESSION["name"] = $row['family_name'].' '.$row['last_name'];
                
                //トップページへリダイレクト
                header("location: ../index.php");
                exit();
            } else {
                $login_err = 'メールアドレスかパスワードが間違っています。';
            }
        }else {
            $login_err = 'メールアドレスかパスワードが間違っています。';
        }
    }
}
?>
 
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <base href="/shop/">
    <title>Sound Space/ログイン</title>
    <link href="style.css" rel="stylesheet">
</head>
<body>


<?php include "../common/header.php"; ?>

<main>

    

    <div class="wrapper">
        <h2>ログイン</h2>

        <p>メールアドレスとパスワードを入力してください。</p>

        <?php 
        if(!empty($login_err)){
            echo '<div class="alert alert-danger">' . $login_err . '</div>';
        }        
        ?>

        <form class="form_group" action="<?php echo $_SERVER ['SCRIPT_NAME']; ?>" method="post">

            <div>
                <label for="email">メールアドレス</label>
                <!-- isset($errors['email'])を条件に追加
                isset関数の引数は変数や配列のキーのみ。関数の戻り値であるh($errors['email'])は引数にできないため、ここではh関数を使わない。 -->
                <input type="text" name="email" class="form-control <?php echo isset($errors['email']) && !empty(h($errors['email'])) ? 'is-invalid' : ''; ?>" value="<?php echo h($datas['email']); ?>" required>
                <span class="invalid-feedback"><?php echo isset($errors['email']) ? h($errors['email']) : ''; ?></span>
            </div>

            <div>
                <label for="password">パスワード</label>
                <!-- isset($errors['password'])を条件に追加 -->
                <input type="password" name="password" class="form-control <?php echo isset($errors['password']) && !empty(h($errors['password'])) ? 'is-invalid' : ''; ?>" value="<?php echo h($datas['password']); ?>" required>
                <span class="invalid-feedback"><?php echo isset($errors['password']) ? h($errors['password']) : ''; ?></span>
            </div>

            <div style="text-align:end">
                <input type="hidden" name="token" value="<?php echo h($_SESSION['token']); ?>">
                <button type="submit">ログイン</button>
            </div>


    

            <p>アカウントを持っていませんか？ <a href='user_login/register.php'>新規登録</a></p>
        </form>
    </div>


</main>
<?php include "../common/footer.php"; ?>


</body>
</html>
