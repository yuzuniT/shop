<?php
session_start();
// セッション変数 $_SESSION["loggedin"]を確認。ログインしていなければログインページへリダイレクト
if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
    header("location: login.php");
    exit;
}
?>
 
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Welcome</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        body{ 
            font: 14px sans-serif;
            text-align: center; 
        }
    </style>
</head>
<body>
    <h1 class="my-5">Hi,<b><?php echo htmlspecialchars($_SESSION["name"]); ?></b>. Welcome to our site.</h1>

    <?php if (isset($_SESSION['profile_updated']) && $_SESSION['profile_updated']): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            Your profile has been updated!
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
        <?php unset($_SESSION['profile_updated']); ?>
    <?php endif; ?>


    <p>
        <a href="profile_edit.php" class="btn btn-primary ml-3">Edit Your Profile</a>
    </p>
    <p>
        <a href="logout.php" class="btn btn-danger ml-3">Sign Out of Your Account</a>
    </p>
</body>
</html>
