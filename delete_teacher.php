<?php
session_start();
require 'config.php';
require 'functions.php';

// ユーザ認証と権限チェック
if (!is_logged_in() || !has_permission('admin')) {
    echo '権限がありません。';
    exit();
}

if (!isset($_GET['id'])) {
    echo '講師IDが指定されていません。';
    exit();
}

$stmt = $pdo->prepare('SELECT * FROM teachers WHERE id = ?');
$stmt->execute([$_GET['id']]);
$teacher = $stmt->fetch();

if (!$teacher) {
    echo '講師情報が見つかりません。';
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // 講師情報の削除
    $stmt = $pdo->prepare('DELETE FROM teachers WHERE id = ?');
    $stmt->execute([$_GET['id']]);

    header('Location: dashboard.php');
    exit();
}
?>

<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <title>講師情報削除</title>
</head>

<body>
    <h1>講師情報削除</h1>
    <p>本当に削除しますか？</p>
    <form method="POST">
        <button type="submit">削除</button>
    </form>
    <a href="dashboard.php">戻る</a>
</body>

</html>