
<?php
// productsテーブルに商品情報を挿入するスクリプト

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


try {
    $pdo = new PDO(DB_DSN, DB_USER, DB_PASSWORD, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]);

    // CSVファイルのパス
    $csv_file = 'products.csv';

    // CSVファイルを開く
    if (($handle = fopen($csv_file, 'r')) !== false) {
        // ヘッダー行をスキップ
        fgetcsv($handle, 1000, ',');

        // INSERTクエリの準備
        $stmt = $pdo->prepare('
            INSERT INTO products (
                id, category_id, product_name, description,
                base_price, stock_quantity, is_active
            ) VALUES (
                :id, :category_id, :product_name, :description,
                :base_price, :stock_quantity, :is_active
            )
        ');

        // トランザクション開始
        $pdo->beginTransaction();

        // 各行を処理
        while (($row = fgetcsv($handle, 1000, ',')) !== false) {
            $stmt->execute([
                ':id' => $row[0],
                ':category_id' => (int)$row[1],
                ':product_name' => $row[2],
                ':base_price' => (float)$row[3],
                ':description' => $row[4],
                ':stock_quantity' => (int)$row[5],
                ':is_active' => (bool)$row[6],
            ]);
        }

        // トランザクションコミット
        $pdo->commit();
        fclose($handle);
        echo "データ挿入が完了しました。";
    } else {
        echo "CSVファイルを開けませんでした。";
    }
} catch (PDOException $e) {
    // エラー発生時にロールバック
    if (isset($pdo) && $pdo->inTransaction()) {
        $pdo->rollBack();
    }
    echo "エラー: " . $e->getMessage();
}
?>