<?php
/* Dotenvを利用し、config/db_info.envから接続に必要な情報を持ってくる */
require __DIR__.'/../vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__.'/../config');
$dotenv->load();

const DB_dsn = 'mysql:dbname=' . $_ENV['DB_NAME'] . ';host=' . $_ENV['DB_HOST'] . ';charset=utf8mb4';
const DB_USER = $_ENV['DB_USER'];
const DB_PASSWORD = $_ENV['DB_PASSWORD'];

//②　例外処理を使って、DBにPDO接続する
try {
    $pdo = new PDO(DB_dsn,DB_USER,DB_PASSWORD,[
		PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
		PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
		PDO::ATTR_EMULATE_PREPARES =>false
	]);
} catch (PDOException $e) {
	echo 'エラー：データベースに接続できませんでした'.$e->getMessage()."\n";
	exit();
}


?>