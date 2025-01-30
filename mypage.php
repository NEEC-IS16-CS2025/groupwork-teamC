<?php
session_start();
require 'config.php';
require 'functions.php';

// ログインしていない場合はindex.phpにリダイレクト
if (!is_logged_in()) {
    header('Location: index.php');
    exit();
}

// ユーザー情報を取得
$user_id = $_SESSION['user_id'];
$stmt = $pdo->prepare('SELECT * FROM teachers WHERE id = ?');
$stmt->execute([$user_id]);
$user = $stmt->fetch();

if (!$user) {
    header('Location: index.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $first_name = $_POST['first_name'];
    $last_name = $_POST['last_name'];
    $email = $_POST['email'];
    $notes = $_POST['notes'];

    // メールアドレスの重複チェック
    $stmt = $pdo->prepare('SELECT id FROM teachers WHERE email = ? AND id != ?');
    $stmt->execute([$email, $_SESSION['user_id']]);
    if ($stmt->fetch()) {
        $error = "このメールアドレスは既に使用されています。";
    } else {
        // 講師情報の更新
        $stmt = $pdo->prepare('UPDATE teachers SET first_name = ?, last_name = ?, email = ?, notes = ? WHERE id = ?');
        $stmt->execute([$first_name, $last_name, $email, $notes, $_SESSION['user_id']]);

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
    <title>マイページ</title>
</head>

<body>
    <h1>マイページ</h1>
    <?php if (isset($error)): ?>
        <div style="color: red; margin-bottom: 10px;">
            <?php echo htmlspecialchars($error); ?>
        </div>
    <?php endif; ?>
    <form method="POST">
        <label for="first_name">姓:</label>
        <input type="text" id="first_name" name="first_name"
            value="<?php echo htmlspecialchars($user['first_name']); ?>">
        <br>

        <label for="last_name">名:</label>
        <input type="text" id="last_name" name="last_name" value="<?php echo htmlspecialchars($user['last_name']); ?>">
        <br>

        <label for="email">メールアドレス:</label>
        <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>">
        <br>

        <label for="authority">権限:</label>
        <input type="text" id="authority" name="authority" value="<?php echo htmlspecialchars($user['authority']); ?>"
            readonly>
        <br>

        <label for="notes">特記事項:</label>
        <textarea id="notes" name="notes"><?php echo htmlspecialchars($user['notes']); ?></textarea>
        <br>

        <button type="submit">更新</button>
        <a href="dashboard.php" class="button">戻る</a>
    </form>
</body>

</html>