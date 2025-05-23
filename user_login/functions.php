<?php
//XSS対策
function h($s){
    return htmlspecialchars($s, ENT_QUOTES, "UTF-8");
}

//セッションにトークンセット
function setToken(){
    $token = sha1(uniqid(mt_rand(), true));
    $_SESSION['token'] = $token;
}

//セッション変数のトークンとPOSTされたトークンをチェック
function checkToken(){
    if(empty($_SESSION['token']) || ($_SESSION['token'] != $_POST['token'])){
        echo <<<EOD
                セッションの有効期限が切れたか、操作に問題が発生しました。
                ページを更新し、もう一度お試しください。
                それでも解決しない場合は、お問い合わせください。
EOD;
        exit;
    }
}

//POSTされた値のバリデーション


// メールアドレスのバリデーション
function ValidateEmail(string $email): ?string {

    $pattern='/^[a-zA-Z0-9.!#$%&\'*+\/=?^_`{|}~-]+@[a-zA-Z0-9](?:[a-zA-Z0-9-]{0,61}[a-zA-Z0-9])?(?:\.[a-zA-Z0-9](?:[a-zA-Z0-9-]{0,61}[a-zA-Z0-9])?)*$/';

    //メールアドレスのチェック
    if(empty($email)) {
        return 'メールアドレスを入力してください。';
    }else if(!preg_match($pattern,$email)){
        return '有効なメールアドレスを入力してください。';
    }
    return null;

}

// パスワードのバリデーション
function ValidatePassword(string $password): ?string {

    $pattern='/^[a-zA-Z0-9!@#$%^&*]{8,100}$/';

    //パスワードのチェック（正規表現）
    if(empty($password)){
        return "パスワードを入力してください。";
    }else if(!preg_match($pattern,$password)){
        return "8文字以上100文字以下のパスワードを入力してください。";
    }
    return null;

}

// パスワード確認のバリデーション
function ValidateConfirmPassword(string $confirm_password, string $password): ?string {


    if(empty($confirm_password)){
        return "パスワードを確認してください。";
    }else if(($confirm_password != $password)){
        return "パスワードが一致しません。";
    }
    
    return null;
}

// 名前のバリデーション
function ValidateName(string $name): ?string {

    if (empty($name)) {
        return "名前を入力してください。";
    }
    elseif (!preg_match('/^.+$/u', $name)) {
        return "有効な名前を入力してください。";
    }
    return null;
}

// 郵便番号のバリデーション
function ValidatePostalCode(string $postal_code): ?string {
    if (empty($postal_code)) {
        return "郵便番号を入力してください。";
    }
    elseif (!preg_match('/^\d{7]$/', $postal_code)) {
        return "郵便番号は「1234567」の形式で入力してください。";
    }
    return null;
}

// 住所のバリデーション
function ValidateAddress(string $address): ?string {
    if (empty($address)) {
        return "住所を入力してください。";
    }
    elseif (!preg_match('/^.+$/u', $address)) {
        return "有効な住所を入力してください。";
    }
    return null;
}

// 電話番号のバリデーション
function ValidatePhoneNumber(string $phone_number): ?string {
    if (empty($phone_number)) {
        return "電話番号を入力してください。";
    }
    if (!preg_match('/^\d{4.15}$/', $phone_number)) {
        return "電話番号は「09012345678」の形式で入力してください。";
    }
    return null;
}

// 支払い方法のバリデーション
function ValidatePayment(string $payment_method): ?string {

    $valid_methods=["credit_card","convenient_store","cash_on_delivery","bank_transfer"];

    if (empty($payment_method)){
        return "支払い方法を選択してください。";
    }
    elseif (!In_array($payment_method, $valid_methods)){
        return "無効な支払い方法が選択されました。";
    }
    return null;
}

// お問い合わせ種類のバリデーション
function ValidateContactType(string $contact_type): ?string {

    $valid_types=["product","order","return","payment","other"];

    if (empty($contact_type)){
        return "お問い合わせの種類を選択してください。";
    }
    elseif (!in_array($contact_type, $valid_types)){
        return "無効なお問い合わせ種類が選択されました。";
    }
    return null;
}

// お問い合わせ件名のバリデーション
function ValidateContactTitle(string $contact_title): ?string {
    if (empty($contact_title)) {
        return "件名を入力してください。";
    }
    elseif (!preg_match('/^.+$/u', $contact_title)) {
        return "有効な件名を入力してください。";
    }
    return null;
}

// お問い合わせメッセージのバリデーション
function ValidateContactMessage(string $message): ?string {
    if (empty($message)) {
        return "お問い合わせメッセージを入力してください。";
    }
    elseif (!preg_match('/^.+$/u', $message)) {
        return "有効なメッセージを入力してください。";
    }
    return null;
}

// フォームの種類によって処理を分岐させる。初期値はregister
function validation(array $datas,string $formtype="register"): array{

    $errors = [];

    // 新規登録時
    if($formtype=="register"){

        $errors["email"]=ValidateEmail($datas["email"]);
        $errors["password"]=ValidatePassword($datas["password"]);
        $errors["confirm_password"]=ValidateConfirmPassword($datas["confirm_password"],$datas["password"]);

    }

    // ログイン時
    elseif($formtype=="login"){

        $errors["email"]=ValidateEmail($datas["email"]);
        $errors["password"]=ValidatePassword($datas["password"]);

    }

    // 送り先情報入力時
    elseif($formtype=="delivery_info"){

        $errors["name"]=ValidateName($datas["name"]);
        $errors["postal_code"]=ValidatePostalCode($datas["postal_code"]);
        $errors["address"]=ValidateAddress($datas["address"]);
        $errors["phone_number"]=ValidatePhoneNumber($datas["phone_number"]);
        $errors["payment_method"]=ValidatePayment($datas["payment_method"]);

    }

    // お問い合わせ時
    elseif($formtype=="contact"){

        $errors["name"]=ValidateName($datas["name"]);
        $errors["email"]=ValidateEmail($datas["email"]);
        $errors["phone_number"]=ValidatePhoneNumber($datas["phone_number"]);
        $errors["contact_type"]=ValidateContactType($datas["contact_type"]);
        $errors["contact_title"]=ValidateContactTitle($datas["contact_title"]);
        $errors["message"]=ValidateContactMessage($datas["message"]);

    }

    else{
        $errors["formtype"]="無効なフォームタイプです。";
    }

    return $errors;
}
