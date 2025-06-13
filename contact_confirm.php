<?php

// ファイルの読み込み
require_once "user_login/functions.php";
require_once "user_login/db_connect.php";

session_start();

// 受付メールのお問い合わせ日時用
date_default_timezone_set('Asia/Tokyo');

// 問い合わせ種類用
$contact_type=[
    "product"=>"商品について",
    "order"=>"注文・発送について",
    "return"=>"返品・交換",
    "payment"=>"支払いについて",
    "other"=>"その他"
];

//お問い合わせ情報の中身を格納する変数の定義と初期化
$contact_datas = [
    'family_name'  => '',
    'last_name'  => '',
    'email'  => '',
    'phone_number'  => '',
    'contact_type' => '',
    'contact_title' => '',
    'message' => ''
];

// お問い合わせ情報の中身を変数に格納
foreach($contact_datas as $key => $value) {
    if(isset($_SESSION["contact_datas"][$key]) && !empty($_SESSION["contact_datas"][$key])) {
        $contact_datas[$key] = $_SESSION["contact_datas"][$key];
    }
}

//GET通信だった場合はセッション変数にトークンを追加
if($_SERVER['REQUEST_METHOD'] != 'POST'){
    setToken();
}

if($_SERVER["REQUEST_METHOD"] == "POST"){

    //CSRF対策
    checkToken();


    //注文確定ボタンが押された時、DBへの新規登録を実行
    if($_POST["confirm"]==TRUE){


        $count = 0;
        $columns = '';
        $values = '';
        foreach (array_keys($contact_datas) as $key) {
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
            $sql_contacts = 'insert into shop.contacts ('.$columns .')values('.$values.')';
            $stmt_contacts = $pdo->prepare($sql_contacts);
            $stmt_contacts->execute($contact_datas);

            $contact_id=$pdo->lastInsertId();

            $pdo->commit();

            // 利用者・管理者にお問い合わせ受付メールを送信
            $stmt_admin=$pdo->query("SELECT email FROM shop.admin");
            $result=$stmt_admin->fetch(PDO::FETCH_ASSOC);
            $email_admin=$result["email"];

            $to=$contact_datas["email"];

            $contact_date = date('Y-m-d H:i:s');

            $contact_id_zerofill=str_pad($contact_id,8,"0",STR_PAD_LEFT);

            $subject='お問い合わせ受付完了のお知らせ（お問い合わせ番号: ' . $contact_id_zerofill . '）';
            $message = <<<EOD

            {$contact_datas['family_name']} {$contact_datas['last_name']} 様

            この度は、Sound Space にお問い合わせいただき、誠にありがとうございます。
            以下の内容でお問い合わせを受付いたしました。

            ---

            お問い合わせ番号： {$contact_id_zerofill}
            受付日時： {$contact_date}

            [お問い合わせ情報]
            お名前： {$contact_datas['family_name']} {$contact_datas['last_name']}
            メールアドレス： {$contact_datas['email']}
            電話番号： {$contact_datas['phone_number']}

            [お問い合わせ内容]
            お問い合わせの種類： {$contact_datas['contact_type']}
            件名： {$contact_datas['contact_title']}
            メッセージ：
            {$contact_datas['message']}

            ---

            [対応予定]
            お問い合わせは通常、2～3営業日以内にご返信いたします。
            内容によっては、お時間をいただく場合がございます。あらかじめご了承ください。

            [お問い合わせ]
            追加のご質問やご連絡がございましたら、以下までお願いいたします。
            メール: support@soundspace.com
            電話: 0120-XXX-XXXX（受付時間: 平日9:00～17:00）

            今後ともSound Spaceをよろしくお願いいたします。

            ---
            Sound Space カスタマーサポート
            https://www.soundspace.com

            EOD;
            $headers="From:ridgeintojp@gmail.com"."\r\n";
            $headers.="Bcc:".$email_admin;// BCCに管理者のメールアドレスを挿入

            mb_language("Japanese");
            mb_internal_encoding("UTF-8");
            mb_send_mail($to, $subject, $message, $headers);


            unset($_SESSION["contact_datas"]);
            unset($_SESSION["token"]);
            header("location: contact_complete.php?contact_id=".$contact_id);
            exit;
        } catch (PDOException $e) {
            echo 'エラー：送信処理に失敗しました。';
            echo $e->getMessage();
            $pdo->rollBack();
        }
    }

}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Sound Space/お問い合わせ内容確認</title>
    <link href='style.css' rel='stylesheet'>
</head>
    
<body>

<?php include "common/header.php"; ?>

<main>


<div class="confirmation_container">
    <h2>お問い合わせ内容確認</h2>
    <p>以下の内容でよろしければ、「送信する」ボタンを押してください。</p>

    <div class="contact_info_detail">
        <table>

            <tr>
                <th>お名前</th>
                <td><?php echo $contact_datas["family_name"]." ".$contact_datas["last_name"];?></td>
            </tr>
            <tr>
                <th>メールアドレス</th>
                <td><?php echo $contact_datas["email"];?></td>
            </tr>
            <tr>
                <th>電話番号</th>
                <td><?php echo $contact_datas["phone_number"];?></td>
            </tr>
            <tr>
                <th>お問い合わせの種類</th>
                <td><?php echo $contact_type[$contact_datas["contact_type"]];?></td>
            </tr>
            <tr>
                <th>件名</th>
                <td><?php echo $contact_datas["contact_title"];?></td>
            </tr>
            <tr>
                <th>メッセージ</th>
                <td><?php echo $contact_datas["message"];?></td>
            </tr>

        </table>
    </div>


    <div class="contact_btn_container">
        <button type="button" onclick="location.href='contact.php'">戻る</button>
        <form action="contact_confirm.php" method="post">
            <input type="hidden" name="token" value="<?php echo h($_SESSION['token']); ?>">
            <input type="hidden" name="confirm" value="1">
            <button type="submit">送信</button>
        </form>
    </div>

</div>


</main>


<?php include "common/footer.php"; ?>

</body>
</html>