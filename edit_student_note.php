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
    <link rel="stylesheet" href="style2.css">
    <title>特記事項編集</title>
</head>

<body>
    <h1>特記事項編集</h1>

    <form method="post">
        <label for="first_name">姓:</label>
        <input type="text" id="first_name" value="<?php echo htmlspecialchars($student['first_name']); ?>" readonly>
        <br>

        <label for="last_name">名:</label>
        <input type="text" id="last_name" value="<?php echo htmlspecialchars($student['last_name']); ?>" readonly>
        <br>

        <label for="birth_date">生年月日:</label>
        <input type="text" id="birth_date" value="<?php echo htmlspecialchars($student['birth_date']); ?>" readonly>
        <br>

        <label for="notes">特記事項:</label>
        <textarea id="notes" name="notes" rows="5"><?php echo htmlspecialchars($student['notes']); ?></textarea>
        <br>

        <button type="submit">更新</button>
        <a href="dashboard.php" class="button">戻る</a>
    </form>
</body>

</html>