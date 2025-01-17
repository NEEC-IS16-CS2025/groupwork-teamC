<?php
session_start();
require 'config.php';
require 'functions.php';

// ユーザ認証と権限チェック
if (!is_logged_in() || !has_permission('admin')) {
    echo '権限がありません。';
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $first_name = $_POST['first_name'];
    $last_name = $_POST['last_name'];
    $email = $_POST['email'];
    $authority = $_POST['authority'];
    $notes = $_POST['notes'];

    // 講師情報の追加
    $stmt = $pdo->prepare('INSERT INTO teachers (first_name, last_name, email, authority, notes) VALUES (?, ?, ?, ?, ?)');
    $stmt->execute([$first_name, $last_name, $email, $authority, $notes]);

    header('Location: dashboard.php');
    exit();
}
?>

<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <title>講師情報追加</title>
</head>

<body>
    <h1>講師情報追加</h1>
    <form method="POST">
        <label for="first_name">姓:</label>
        <input type="text" id="first_name" name="first_name" required>
        <br>

        <label for="last_name">名:</label>
        <input type="text" id="last_name" name="last_name" required>
        <br>

        <label for="email">メールアドレス:</label>
        <input type="email" id="email" name="email" required>
        <br>

        <label for="authority">権限:</label>
        <select id="authority" name="authority" required>
            <option value="admin">admin</option>
            <option value="operator">operator</option>
            <option value="general">general</option>
        </select>
        <br>

        <label for="notes">特記事項:</label>
        <textarea id="notes" name="notes"></textarea>
        <br>

        <button type="submit">追加</button>
    </form>

    <a href="dashboard.php">戻る</a>
</body>

</html>