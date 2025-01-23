<?php
session_start();
require 'config.php';
require 'functions.php';

if (!is_logged_in()) {
    header('Location: index.php');
    exit();
}

// general権限のみ許可
if (!has_permission('general')) {
    header('Location: dashboard.php');
    exit();
}

// 生徒IDの確認
if (!isset($_GET['id'])) {
    header('Location: dashboard.php');
    exit();
}

$student_id = $_GET['id'];

// 生徒情報の取得
$stmt = $pdo->prepare('SELECT * FROM students WHERE id = ?');
$stmt->execute([$student_id]);
$student = $stmt->fetch();

if (!$student) {
    header('Location: dashboard.php');
    exit();
}

// 現在の講師が担当講師か確認
if ($student['teacher_id'] != $_SESSION['user_id']) {
    header('Location: dashboard.php');
    exit();
}

// フォーム送信時の処理
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $notes = $_POST['notes'] ?? '';

    // 特記事項の更新
    $stmt = $pdo->prepare('UPDATE students SET notes = ? WHERE id = ?');
    $stmt->execute([$notes, $student_id]);

    header('Location: dashboard.php');
    exit();
}
?>

<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <title>特記事項編集</title>
</head>

<body>
    <h1>特記事項編集</h1>
    <a href="dashboard.php">戻る</a>

    <form method="post">
        <table border="1">
            <tr>
                <th>姓</th>
                <td><?php echo htmlspecialchars($student['first_name']); ?></td>
            </tr>
            <tr>
                <th>名</th>
                <td><?php echo htmlspecialchars($student['last_name']); ?></td>
            </tr>
            <tr>
                <th>生年月日</th>
                <td><?php echo htmlspecialchars($student['birth_date']); ?></td>
            </tr>
            <tr>
                <th>特記事項</th>
                <td>
                    <textarea name="notes" rows="5"
                        cols="40"><?php echo htmlspecialchars($student['notes']); ?></textarea>
                </td>
            </tr>
        </table>
        <button type="submit">更新</button>
    </form>
</body>

</html>