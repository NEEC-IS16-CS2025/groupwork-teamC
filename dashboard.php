<?php
session_start();
require 'config.php';
require 'functions.php';

if (!is_logged_in()) {
    header('Location: index.php');
    exit();
}

$authority = get_user_authority();
$students = $pdo->query('SELECT * FROM students')->fetchAll();
$teachers = has_permission('admin') ? $pdo->query('SELECT * FROM teachers')->fetchAll() : [];
?>

<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <title>ダッシュボード</title>
</head>

<body>
    <h1>ダッシュボード</h1>
    <p>権限: <?php echo $authority; ?></p>
    <a href="logout.php">ログアウト</a>

    <?php if (has_permission('operator')): ?>
        <h2>生徒一覧</h2>
        <a href="add_student.php">生徒を追加</a>
        <table border="1">
            <tr>
                <th>姓</th>
                <th>名</th>
                <th>担当講師</th>
                <th>生年月日</th>
                <th>特記事項</th>
                <?php if (has_permission('operator')): ?>
                    <th>操作</th>
                <?php endif; ?>
            </tr>
            <?php foreach ($students as $student): ?>
                <tr>
                    <td><?php echo htmlspecialchars($student['first_name']); ?></td>
                    <td><?php echo htmlspecialchars($student['last_name']); ?></td>
                    <td><?php echo htmlspecialchars($student['teacher_id']); ?></td>
                    <td><?php echo htmlspecialchars($student['birth_date']); ?></td>
                    <td><?php echo htmlspecialchars($student['notes']); ?></td>
                    <?php if (has_permission('operator')): ?>
                        <td>
                            <a href="edit_student.php?id=<?php echo $student['id']; ?>">編集</a>
                            <?php if (has_permission('admin')): ?>
                                <a href="delete_student.php?id=<?php echo $student['id']; ?>">削除</a>
                            <?php endif; ?>
                        </td>
                    <?php endif; ?>
                </tr>
            <?php endforeach; ?>
        </table>
    <?php endif; ?>

    <?php if (has_permission('admin')): ?>
        <h2>講師一覧</h2>
        <a href="add_teacher.php">講師を追加</a>
        <table border="1">
            <tr>
                <th>姓</th>
                <th>名</th>
                <th>メールアドレス</th>
                <th>権限</th>
                <th>特記事項</th>
                <th>操作</th>
            </tr>
            <?php foreach ($teachers as $teacher): ?>
                <tr>
                    <td><?php echo htmlspecialchars($teacher['first_name']); ?></td>
                    <td><?php echo htmlspecialchars($teacher['last_name']); ?></td>
                    <td><?php echo htmlspecialchars($teacher['email']); ?></td>
                    <td><?php echo htmlspecialchars($teacher['authority']); ?></td>
                    <td><?php echo htmlspecialchars($teacher['notes']); ?></td>
                    <td>
                        <a href="edit_teacher.php?id=<?php echo $teacher['id']; ?>">編集</a>
                        <a href="delete_teacher.php?id=<?php echo $teacher['id']; ?>">削除</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </table>
    <?php endif; ?>
</body>

</html>