<?php
session_start();
require 'config.php';
require 'functions.php';

if (isset($_SESSION['user_id'])) {
    header('Location: dashboard.php');
    exit();
}

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'];
    $password = $_POST['password'];

    if (login($email, $password, $pdo)) {
        header('Location: dashboard.php');
        exit();
    } else {
        $error = 'ログインに失敗しました。';
    }
}
?>

<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style2.css">
    <title>ログイン</title>
</head>

<body>
    <h1>ログイン</h1>
    <form method="POST">
        <label for="email">メールアドレス</label>
        <input type="email" id="email" name="email" required>
        <br>
        <label for="password">パスワード</label>
        <input type="password" id="password" name="password" required>
        <br>
        <button type="submit">ログイン</button>
        <p style="color:red;"><?php echo $error; ?></p>
    </form>
</body>

</html>