<?php

// 初期値
$user_name="ゲスト";

if (isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true && isset($_SESSION["name"])){
    $user_name=$_SESSION["name"];
}

?>

<header>

    <div class="header_container">

        <a class="logo" href="index.php">
            <img src="img/logo/SoundSpace.jpg">
        </a>

        <div class="search_form">

            <form action="" method="GET">
                <input id="header_search_input" type="search" placeholder="商品を検索">
                <button type="submit" name="submit">検索</button>
            </form>

        </div>

        <div class="welcome">

            <!--ログイン済 : ようこそ！◯◯さん
                未ログイン : 「ログイン」リンクで誘導-->
            <p>ようこそ！ <?php echo $user_name?>  さん</p>
            <?php if (!(isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true && isset($_SESSION["name"]))){
                echo "<a href='user_login/login.php'>ログイン</a>";
            }
            ?>

        </div>

        <div class="main_buttons_container">

            <!--カートを見る-->
            <a class="main_button" href="view_cart.php">
                <img class="main_button" src="img/main_buttons/cart.png">
            </a>
            <!--お問い合わせ-->
            <a class="main_button" href="contact.php">
                <img class="main_button" src="img/main_buttons/question.png">
            </a>
            <!--ログアウト-->
            <a class="main_button" href="user_login/logout.php">
                <img class="main_button" src="img/main_buttons/logout.png">
            </a>

        </div>


    </div>

</header>