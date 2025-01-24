<?php
session_start();
require 'config.php';
require 'functions.php';

// ユーザ認証と権限チェック
if (!is_logged_in() || !has_permission('operator')) {
    echo '権限がありません。';
    exit();
}

// 講師一覧を取得
$teachers = $pdo->query('SELECT id, first_name, last_name FROM teachers')->fetchAll();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $first_name = $_POST['first_name'];
    $last_name = $_POST['last_name'];
    $teacher_id = $_POST['teacher_id'];
    $birth_date = $_POST['birth_date'];
    $notes = $_POST['notes'];

    // 生徒情報の追加
    $stmt = $pdo->prepare('INSERT INTO students (first_name, last_name, teacher_id, birth_date, notes) VALUES (?, ?, ?, ?, ?)');
    $stmt->execute([$first_name, $last_name, $teacher_id, $birth_date, $notes]);

    header('Location: dashboard.php');
    exit();
}
?>

<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <title>生徒情報追加</title>
</head>

<body>
    <h1>生徒情報追加</h1>
    <form method="POST">
        <label for="first_name">姓:</label>
        <input type="text" id="first_name" name="first_name" required>
        <br>

        <label for="last_name">名:</label>
        <input type="text" id="last_name" name="last_name" required>
        <br>

        <label for="teacher_id">担当講師:</label>
        <select id="teacher_id" name="teacher_id" required>
            <option value="">選択してください</option>
            <?php foreach ($teachers as $teacher): ?>
                <option value="<?php echo $teacher['id']; ?>">
                    <?php echo htmlspecialchars($teacher['first_name'] . ' ' . $teacher['last_name']); ?>
                </option>
            <?php endforeach; ?>
        </select>
        <br>

        <label for="birth_date">生年月日:</label>
        <input type="date" id="birth_date" name="birth_date" required>
        <br>

        <label for="notes">特記事項:</label>
        <textarea id="notes" name="notes"></textarea>
        <br>

        <button type="submit">追加</button>
    </form>

    <a href="dashboard.php">戻る</a>
</body>

</html>