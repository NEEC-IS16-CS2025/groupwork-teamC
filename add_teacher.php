<?php
session_start();
require 'config.php';
require 'functions.php';

// ユーザ認証と権限チェック
if (!is_logged_in() || !has_permission('admin')) {
    echo '権限がありません。';
    exit();
}

// 講師情報の登録処理
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $first_name = $_POST['first_name'] ?? '';
    $last_name = $_POST['last_name'] ?? '';
    $email = $_POST['email'] ?? '';
    $authority = $_POST['authority'] ?? '';
    $notes = $_POST['notes'] ?? '';
    $password = $_POST['password'] ?? '';

    // 入力チェック
    if (empty($first_name) || empty($last_name) || empty($email) || empty($authority) || empty($password)) {
        $error = "すべての必須フィールドを入力してください。";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "有効なメールアドレスを入力してください。";
    } else {
        try {
            // パスワードをハッシュ化
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);

            // データベースに挿入
            $stmt = $pdo->prepare("INSERT INTO teachers (first_name, last_name, email, authority, notes, password) VALUES (?, ?, ?, ?, ?, ?)");
            $stmt->execute([$first_name, $last_name, $email, $authority, $notes, $hashed_password]);

            $success = "講師情報を登録しました。";
        } catch (PDOException $e) {
            $error = "講師情報の登録に失敗しました: " . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>講師情報登録</title>
</head>

<body>
    <h1>講師情報登録</h1>

    <?php if (!empty($error)): ?>
        <p style="color: red;"><?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?></p>
    <?php elseif (!empty($success)): ?>
        <p style="color: green;"><?= htmlspecialchars($success, ENT_QUOTES, 'UTF-8') ?></p>
    <?php endif; ?>

    <form method="post">
        <label for="first_name">姓：</label>
        <input type="text" id="first_name" name="first_name" required><br>

        <label for="last_name">名：</label>
        <input type="text" id="last_name" name="last_name" required><br>

        <label for="email">メールアドレス：</label>
        <input type="email" id="email" name="email" required><br>

        <label for="role">権限：</label>
        <select id="role" name="authority" required>
            <option value="admin">admin</option>
            <option value="operator">operator</option>
            <option value="general">general</option>
        </select><br>

        <label for="password">パスワード：</label>
        <input type="password" id="password" name="password" required><br>

        <label for="notes">特記事項：</label>
        <textarea id="notes" name="notes"></textarea><br>

        <button type="submit">登録</button>
    </form>
    <a href="dashboard.php">戻る</a>
</body>

</html>