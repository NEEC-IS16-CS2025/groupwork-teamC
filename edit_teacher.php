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
    $first_name = $_POST['first_name'];
    $last_name = $_POST['last_name'];
    $email = $_POST['email'];
    $authority = $_POST['authority'];
    $notes = $_POST['notes'];

    // メールアドレスの重複チェック
    $stmt = $pdo->prepare('SELECT id FROM teachers WHERE email = ? AND id != ?');
    $stmt->execute([$email, $_GET['id']]);
    if ($stmt->fetch()) {
        $error = "このメールアドレスは既に使用されています。";
    } else {
        // 講師情報の更新
        $stmt = $pdo->prepare('UPDATE teachers SET first_name = ?, last_name = ?, email = ?, authority = ?, notes = ? WHERE id = ?');
        $stmt->execute([$first_name, $last_name, $email, $authority, $notes, $_GET['id']]);

        header('Location: dashboard.php');
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style2.css">
    <title>講師情報編集</title>
</head>

<body>
    <h1>講師情報編集</h1>
    <?php if (isset($error)): ?>
        <div style="color: red; margin-bottom: 10px;">
            <?php echo htmlspecialchars($error); ?>
        </div>
    <?php endif; ?>
    <form method="POST">
        <label for="first_name">姓:</label>
        <input type="text" id="first_name" name="first_name"
            value="<?php echo htmlspecialchars($teacher['first_name']); ?>" required>
        <br>

        <label for="last_name">名:</label>
        <input type="text" id="last_name" name="last_name"
            value="<?php echo htmlspecialchars($teacher['last_name']); ?>" required>
        <br>

        <label for="email">メールアドレス:</label>
        <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($teacher['email']); ?>" required>
        <br>

        <label for="authority">権限:</label>
        <select id="authority" name="authority" required>
            <option value="admin" <?php if ($teacher['authority'] === 'admin')
                echo 'selected'; ?>>admin</option>
            <option value="operator" <?php if ($teacher['authority'] === 'operator')
                echo 'selected'; ?>>operator</option>
            <option value="general" <?php if ($teacher['authority'] === 'general')
                echo 'selected'; ?>>general</option>
        </select>
        <br>

        <label for="notes">特記事項:</label>
        <textarea id="notes" name="notes"><?php echo htmlspecialchars($teacher['notes']); ?></textarea>
        <br>

        <button type="submit">保存</button>
        <a href="dashboard.php" class="button">戻る</a>
    </form>
</body>

</html>