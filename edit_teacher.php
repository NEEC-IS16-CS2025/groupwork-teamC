<?php
session_start();
require 'config.php';
require 'functions.php';

// ユーザ認証チェック4
if (!is_logged_in()) {
    header('Location: index.php');
    exit();
}

// 権限チェック
if (!has_permission('admin')) {
    echo '権限がありません。';
    exit();
}

$is_new = false;

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    if (isset($_GET['id'])) {
        // 既存データの編集
        $stmt = $pdo->prepare('SELECT * FROM teachers WHERE id = ?');
        $stmt->execute([$_GET['id']]);
        $teacher = $stmt->fetch();

        if (!$teacher) {
            echo '講師情報が見つかりません。';
            exit();
        }
    } else {
        // 新規作成
        $is_new = true;
        $teacher = [
            'id' => '',
            'password' => '',
            'first_name' => '',
            'last_name' => '',
            'email' => '',
            'authority' => 'general',
            'notes' => ''
        ];
    }
} elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['delete'])) {
        // 削除処理
        $stmt = $pdo->prepare('DELETE FROM teachers WHERE id = ?');
        $stmt->execute([$_POST['id']]);
        header('Location: dashboard.php');
        exit();
    }

    $id = $_POST['id'];
    $password = $_POST['password'];
    $first_name = $_POST['first_name'];
    $last_name = $_POST['last_name'];
    $email = $_POST['email'];
    $authority = $_POST['authority'];
    $notes = $_POST['notes'];

    if (empty($id)) {
        // 新規追加
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $pdo->prepare('INSERT INTO teachers (password, first_name, last_name, email, authority, notes) VALUES (?,?, ?, ?, ?, ?)');
        $stmt->execute([$hashed_password, $first_name, $last_name, $email, $authority, $notes]);
    } else {
        // 更新処理
        // パスワードを変更する場合のみハッシュ化
        if ($password !== $teacher['password']) {
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare('UPDATE teachers SET password = ?, first_name = ?, last_name = ?, email = ?, authority = ?, notes = ? WHERE id = ?');
            $stmt->execute([$hashed_password, $first_name, $last_name, $email, $authority, $notes, $id]);
        } else {
            // パスワードを変更しない場合は、ハッシュ化せずに更新
            $stmt = $pdo->prepare('UPDATE teachers SET first_name = ?, last_name = ?, email = ?, authority = ?, notes = ? WHERE id = ?');
            $stmt->execute([$first_name, $last_name, $email, $authority, $notes, $id]);
        }
    }

    header('Location: dashboard.php');
    exit();
}
?>

<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <title><?php echo $is_new ? '講師情報追加' : '講師情報編集'; ?></title>
</head>

<body>
    <h1><?php echo $is_new ? '講師情報追加' : '講師情報編集'; ?></h1>
    <form method="POST">
        <input type="hidden" name="id" value="<?php echo htmlspecialchars($teacher['id']); ?>">
        
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
            <option value="general" <?php echo $teacher['authority'] === 'general' ? 'selected' : ''; ?>>general</option>
            <option value="operator" <?php echo $teacher['authority'] === 'operator' ? 'selected' : ''; ?>>operator
            </option>
            <option value="admin" <?php echo $teacher['authority'] === 'admin' ? 'selected' : ''; ?>>admin</option>
        </select>
        <br>

        <label for="notes">特記事項:</label>
        <textarea id="notes" name="notes"><?php echo htmlspecialchars($teacher['notes']); ?></textarea>
        <br>

        <button type="submit"><?php echo $is_new ? '追加' : '保存'; ?></button>

        <?php if (!$is_new): ?>
            <button type="submit" name="delete" onclick="return confirm('本当に削除しますか？')">削除</button>
        <?php endif; ?>
    </form>

    <a href="dashboard.php">戻る</a>
</body>

</html>