<?php
session_start();
require 'config.php';
require 'functions.php';

// ユーザ認証と権限チェック
if (!is_logged_in() || !has_permission('operator')) {
    echo '権限がありません。';
    exit();
}

if (!isset($_GET['id'])) {
    echo '生徒IDが指定されていません。';
    exit();
}

$stmt = $pdo->prepare('SELECT * FROM students WHERE id = ?');
$stmt->execute([$_GET['id']]);
$student = $stmt->fetch();

if (!$student) {
    echo '生徒情報が見つかりません。';
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // 生徒情報の削除
    $stmt = $pdo->prepare('DELETE FROM students WHERE id = ?');
    $stmt->execute([$_GET['id']]);

    header('Location: dashboard.php');
    exit();
}
?>

<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style2.css">
    <title>生徒情報削除</title>
</head>

<body>
    <h1>生徒情報削除</h1>
    <form method="POST">
        <p>本当に削除しますか？</p>
        <button type="submit">削除</button>
        <a href="dashboard.php" class="button">戻る</a>
    </form>
</body>

</html>