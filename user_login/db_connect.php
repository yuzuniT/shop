<?php
/* Dotenvを利用し、config/db_info.envから接続に必要な情報を持ってくる */
require __DIR__.'/../vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__.'/../config');
$dotenv->load();

/* 環境変数を含むためconstでなくdefine関数で定数宣言 */ 
define('DB_DSN', 'mysql:dbname=' . $_ENV['DB_NAME'] . ';host=' . $_ENV['DB_HOST'] . ';charset=utf8mb4');
define('DB_USER', $_ENV['DB_USER']);
define('DB_PASSWORD', $_ENV['DB_PASSWORD']);

//②　例外処理を使って、DBにPDO接続する
try {
    $pdo = new PDO(DB_DSN,DB_USER,DB_PASSWORD,[
		PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
		PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
		PDO::ATTR_EMULATE_PREPARES =>false
	]);
} catch (PDOException $e) {
	echo 'エラー：データベースに接続できませんでした'.$e->getMessage()."\n";
	exit();
}


?>