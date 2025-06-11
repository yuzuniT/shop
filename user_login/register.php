<?php
//ファイルの読み込み
require_once "db_connect.php";
require_once "functions.php";

//セッションの開始
session_start();

//POSTされてきたデータを格納する変数の定義と初期化
$datas = [
    'family_name'  => '',
    'last_name'  => '',
    'email'  => '',
    'password'  => '',
    'confirm_password'  => ''
];

$errors=[];



//GET通信だった場合はセッション変数にトークンを追加
if($_SERVER['REQUEST_METHOD'] != 'POST'){
    setToken();
}
//POST通信だった場合はDBへの新規登録処理を開始
if($_SERVER["REQUEST_METHOD"] == "POST"){
    //CSRF対策
    checkToken();

    // POSTされてきたデータを変数に格納
    foreach($datas as $key => $value) {
        if($value = filter_input(INPUT_POST, $key, FILTER_DEFAULT)) {
            $datas[$key] = $value;
        }
    }

    // バリデーション
    $errors = validation($datas,"register");

    //データベースの中に同一メールアドレスが存在していないか確認
    if(empty($errors['email'])){
        $sql = "SELECT id, email, password FROM shop.members WHERE email = :email";
        $stmt = $pdo->prepare($sql);
        $stmt->bindValue('email',$datas['email'],PDO::PARAM_STR);
        $stmt->execute();
        if($row = $stmt->fetch(PDO::FETCH_ASSOC)){
            $errors['email'] = 'このメールアドレスは既に登録されています。';
        }
    }
    //エラーがなかったらDBへの新規登録を実行
    if(empty($errors)){
        $params = [
            'family_name'=>$datas['family_name'],
            'last_name'=>$datas['last_name'],
            'email'=>$datas['email'],
            'password'=>password_hash($datas['password'], PASSWORD_DEFAULT)
        ];

        $count = 0;
        $columns = '';
        $values = '';
        foreach (array_keys($params) as $key) {
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
            $sql = 'insert into shop.members ('.$columns .')values('.$values.')';
            $stmt = $pdo->prepare($sql);
            $stmt->execute($params);
            $pdo->commit();
            header("location: login.php");
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
    <meta charset="UTF-8">
    <title>Sound Space/新規登録</title>
    <base href="/shop/">
    <link href="style.css" rel="stylesheet">
    <style>
        body{
            font: 14px sans-serif;
        }
        .wrapper{
            width: 400px;
            padding: 20px;
            margin: 0 auto;
        }
    </style>
</head>
<body>
<?php include "../common/header.php"; ?>

<main>
    <div class="wrapper">
        <h2>新規会員登録</h2>
        <p>以下の項目をご入力ください。</p>
        <form class="form_group" action="<?php echo $_SERVER ['SCRIPT_NAME']; ?>" method="post">

            <div class="register_name_box">

                <label for="family_name">姓</label>
                <input type="text" name="family_name" class="form-control <?php echo isset($errors['family_name']) && !empty(h($errors['family_name'])) ? 'is-invalid' : ''; ?>" value="<?php echo h($datas['family_name']); ?>">
                <span class="invalid-feedback"><?php echo isset($errors['family_name']) ? h($errors['family_name']) : ''; ?></span>

                <label for="last_name">名</label>
                <input type="text" name="last_name" class="form-control <?php echo isset($errors['last_name']) && !empty(h($errors['last_name'])) ? 'is-invalid' : ''; ?>" value="<?php echo h($datas['last_name']); ?>">
                <span class="invalid-feedback"><?php echo isset($errors['last_name']) ? h($errors['last_name']) : ''; ?></span>
                
            </div>
            

            <div>
                <label>メールアドレス</label>
                <input type="text" name="email" class="form-control <?php echo isset($errors['email']) && !empty(h($errors['email'])) ? 'is-invalid' : ''; ?>" value="<?php echo h($datas['email']); ?>">
                <span class="invalid-feedback"><?php echo isset($errors['email']) ? h($errors['email']) : ''; ?></span>
            </div>
            <div>
                <label>パスワード</label>
                <input type="password" name="password" class="form-control <?php echo  isset($errors['password']) && !empty(h($errors['password'])) ? 'is-invalid' : ''; ?>" value="<?php echo h($datas['password']); ?>">
                <span class="invalid-feedback"><?php echo isset($errors['password']) ? h($errors['password']) : ''; ?></span>
            </div>
            <div>
                <label>確認用パスワード</label>
                <input type="password" name="confirm_password" class="form-control <?php echo  isset($errors['confirm_password']) && !empty(h($errors['confirm_password'])) ? 'is-invalid' : ''; ?>" value="<?php echo h($datas['confirm_password']); ?>">
                <span class="invalid-feedback"><?php echo isset($errors['confirm_password']) ? h($errors['confirm_password']) : ''; ?></span>
            </div>
            <div style="text-align:end">
                <input type="hidden" name="token" value="<?php echo h($_SESSION['token']); ?>">
                <button type="submit">登録</button>
            </div>
            <p>既にアカウントを持っていますか？ <a href="user_login/login.php">ログイン</a></p>
        </form>
    </div>    

</main>
<?php include "../common/footer.php"; ?>
</body>
</html>
