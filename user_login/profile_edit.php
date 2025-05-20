<?php
//ファイルの読み込み
require_once "db_connect.php";
require_once "functions.php";

//セッションの開始
session_start();

// セッション変数 $_SESSION["loggedin"]を確認。ログインしていなければログインページへリダイレクト
if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
    header("location: login.php");
    exit;
}

//POSTされてきたデータを格納する変数の定義と初期化
$datas = [
    'name'  => '',
    'password'  => '',
    'confirm_password'  => ''
];



//GET通信だった場合はセッション変数にトークンを追加
if($_SERVER['REQUEST_METHOD'] != 'POST'){
    setToken();
}
//POST通信だった場合はDBへの登録情報変更処理を開始
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
    $errors = validation($datas);

    //データベースの中に同一ユーザー名が存在していないか確認
    if(empty($errors['name'])){
        $sql = "SELECT id FROM users WHERE name = :name AND id != :id";
        $stmt = $pdo->prepare($sql);
        $stmt->bindValue('name',$datas['name'],PDO::PARAM_STR);
        $stmt->bindValue('id',$_SESSION['id'],PDO::PARAM_INT);
        $stmt->execute();
        if($_SESSION['name']==$datas['name']){

            //現在のユーザー名から変更しない場合。何もしない。

        }
        elseif($row=$stmt->fetch(PDO::FETCH_ASSOC)){
            $errors['name'] = 'This username is already taken.';
        }
    }
    //エラーがなかったらDBへの登録情報変更を実行
    if(empty($errors)){
        $params = [
            'name'=>$datas['name'],
            'password'=>password_hash($datas['password'], PASSWORD_DEFAULT),
            'old_name'=>$_SESSION['name']
        ];


        try {
            $sql = "UPDATE users SET name=:name, password=:password WHERE name=:old_name";
            $stmt = $pdo->prepare($sql);
            $stmt->execute($params);
            $_SESSION['name']=$datas['name'];
            $_SESSION['profile_updated']=True;
            header("location: welcome.php");
            exit;
        } catch (PDOException $e) {
            echo 'ERROR: Could not edit profile.';
        }
    }
}
?>
 
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Your Profile</title>
    <!-- bootstrap読み込み -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
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
    <div class="wrapper">
        <h2>Edit Your Profile</h2>
        <p>Please fill this form to edit your profile.</p>
        <form action="<?php echo $_SERVER ['SCRIPT_NAME']; ?>" method="post">
            <div class="form-group">
                <label>Username (Your current name is <b><?php echo h($_SESSION["name"]); ?></b>)</label>
                <input type="text" name="name" class="form-control <?php echo isset($errors['name']) && !empty(h($errors['name'])) ? 'is-invalid' : ''; ?>" value="<?php echo h($_SESSION['name']) ; ?>">
                <span class="invalid-feedback"><?php echo h($errors['name']); ?></span>
            </div>    
            <div class="form-group">
                <label>Password</label>
                <input type="password" name="password" class="form-control <?php echo  isset($errors['password']) && !empty(h($errors['password'])) ? 'is-invalid' : ''; ?>">
                <span class="invalid-feedback"><?php echo h($errors['password']); ?></span>
            </div>
            <div class="form-group">
                <label>Confirm Password</label>
                <input type="password" name="confirm_password" class="form-control <?php echo  isset($errors['confirm_password']) && !empty(h($errors['confirm_password'])) ? 'is-invalid' : ''; ?>">
                <span class="invalid-feedback"><?php echo h($errors['confirm_password']); ?></span>
            </div>
            <div class="form-group">
                <input type="hidden" name="token" value="<?php echo h($_SESSION['token']); ?>">
                <input type="submit" class="btn btn-primary" value="Submit">
                <a href="welcome.php" class="btn btn-secondary ml-3">Back</a>
            </div>
        </form>
    </div>    
</body>
</html>
