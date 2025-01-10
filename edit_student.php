<?php
session_start();
require 'config.php';
require 'functions.php';

// ユーザ認証チェック
if (!is_logged_in()) {
    header('Location: index.php');
    exit();
}

// 権限チェック
if (!has_permission('operator')) {
    echo '権限がありません。';
    exit();
}

$is_new = false;

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    if (isset($_GET['id'])) {
        // 既存データの編集
        $stmt = $pdo->prepare('SELECT * FROM students WHERE id = ?');
        $stmt->execute([$_GET['id']]);
        $student = $stmt->fetch();

        if (!$student) {
            echo '生徒情報が見つかりません。';
            exit();
        }
    } else {
        // 新規作成
        $is_new = true;
        $student = [
            'id' => '',
            'first_name' => '',
            'last_name' => '',
            'teacher_id' => '',
            'birth_date' => '',
            'notes' => ''
        ];
    }
} elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['delete'])) {
        // 削除処理
        $stmt = $pdo->prepare('DELETE FROM students WHERE id = ?');
        $stmt->execute([$_POST['id']]);
        header('Location: dashboard.php');
        exit();
    }

    $id = $_POST['id'];
    $first_name = $_POST['first_name'];
    $last_name = $_POST['last_name'];
    $teacher_id = $_POST['teacher_id'];
    $birth_date = $_POST['birth_date'];
    $notes = $_POST['notes'];

    if (empty($id)) {
        // 新規追加
        $stmt = $pdo->prepare('INSERT INTO students (first_name, last_name, teacher_id, birth_date, notes) VALUES (?, ?, ?, ?, ?)');
        $stmt->execute([$first_name, $last_name, $teacher_id, $birth_date, $notes]);
    } else {
        // 更新処理
        $stmt = $pdo->prepare('UPDATE students SET first_name = ?, last_name = ?, teacher_id = ?, birth_date = ?, notes = ? WHERE id = ?');
        $stmt->execute([$first_name, $last_name, $teacher_id, $birth_date, $notes, $id]);
    }

    header('Location: dashboard.php');
    exit();
}
?>

<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <title><?php echo $is_new ? '生徒情報追加' : '生徒情報編集'; ?></title>
</head>

<body>
    <h1><?php echo $is_new ? '生徒情報追加' : '生徒情報編集'; ?></h1>
    <form method="POST">
        <input type="hidden" name="id" value="<?php echo htmlspecialchars($student['id']); ?>">

        <label for="first_name">姓:</label>
        <input type="text" id="first_name" name="first_name"
            value="<?php echo htmlspecialchars($student['first_name']); ?>" required>
        <br>

        <label for="last_name">名:</label>
        <input type="text" id="last_name" name="last_name"
            value="<?php echo htmlspecialchars($student['last_name']); ?>" required>
        <br>

        <label for="teacher_id">担当講師:</label>
        <input type="text" id="teacher_id" name="teacher_id"
            value="<?php echo htmlspecialchars($student['teacher_id']); ?>" required>
        <br>

        <label for="birth_date">生年月日:</label>
        <input type="date" id="birth_date" name="birth_date"
            value="<?php echo htmlspecialchars($student['birth_date']); ?>" required>
        <br>

        <label for="notes">特記事項:</label>
        <textarea id="notes" name="notes"><?php echo htmlspecialchars($student['notes']); ?></textarea>
        <br>

        <button type="submit"><?php echo $is_new ? '追加' : '保存'; ?></button>

        <?php if (!$is_new): ?>
            <button type="submit" name="delete" onclick="return confirm('本当に削除しますか？')">削除</button>
        <?php endif; ?>
    </form>

    <a href="dashboard.php">戻る</a>
</body>

</html>