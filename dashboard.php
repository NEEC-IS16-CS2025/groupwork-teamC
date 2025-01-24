<?php
session_start();
require 'config.php';
require 'functions.php';

if (!is_logged_in()) {
    header('Location: index.php');
    exit();
}

// ----- 情報の取得 ----- //
$authority = get_user_authority();
// 生徒情報の取得 + 担当講師idから講師の名前を取得
$students = $pdo->query('
    SELECT s.*, t.first_name AS teacher_first_name, t.last_name AS teacher_last_name 
    FROM students s
    LEFT JOIN teachers t ON s.teacher_id = t.id
')->fetchAll();
// 権限に対応した講師情報を取得
if (has_permission('admin')) {
    $teachers = $pdo->query('SELECT * FROM teachers')->fetchAll();
} elseif (has_permission('operator')) {
    $teachers = $pdo->query('SELECT * FROM teachers WHERE authority IN ("general", "operator")')->fetchAll();
} else {
    $teachers = [];
}
?>

<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <title>ダッシュボード</title>
    <link rel="stylesheet" href="style.css">
</head>

<body>
    <h1>ダッシュボード</h1>
    <p>権限: <?php echo $authority; ?></p>
    <a href="logout.php" class="button">ログアウト</a>

    <!-- 権限がgeneral以上の場合表示される -->
    <?php if (has_permission('general')): ?>
        <h2>生徒一覧</h2>
        <a href="add_student.php" class="button">生徒を追加</a>
        <div class="table-container">
        <table border="1">
            <thead>
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
                    <td>
                        <!-- 生徒情報の講師情報はidから講師の名前を取得して表示する -->
                        <?php if ($student['teacher_first_name']): ?>
                            <?php echo htmlspecialchars($student['teacher_first_name'] . ' ' . $student['teacher_last_name']); ?>
                        <?php else: ?>
                            未設定
                        <?php endif; ?>
                    </td>
                    <td><?php echo htmlspecialchars($student['birth_date']); ?></td>
                    <td><?php echo htmlspecialchars($student['notes']); ?></td>
                    <td>
                        <?php if (has_permission('operator')): ?>
                            <a href="edit_student.php?id=<?php echo $student['id']; ?>"class="button">編集</a>
                            <a href="delete_student.php?id=<?php echo $student['id']; ?>"class="button">削除</a>
                        <?php elseif (has_permission('general') && $student['teacher_id'] == $_SESSION['user_id']): ?>
                            <a href="edit_student_note.php?id=<?php echo $student['id']; ?>"class="button">特記事項編集</a>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
            </thead>
        </table>
        </div>
    <?php endif; ?>

    <!--権限がoperator以上の場合表示される-->
    <?php if (has_permission('operator')): ?>
        <h2>講師一覧</h2>
        <a href="add_teacher.php" class="button">講師を追加</a>
        <div class="table-container">
        <table border="1">
            <thead>
            <tr>
                <!--権限がadmin以上の場合idとpassの項目を追加する-->
                <?php if (has_permission("admin")): ?>
                    <th>id</th>
                    <th>パスワード</th>
                <?php endif; ?>

                <th>姓</th>
                <th>名</th>
                <th>メールアドレス</th>
                <th>権限</th>
                <th>特記事項</th>

                <!--権限がadmin以上の場合編集ボタンを表示する-->
                <?php if (has_permission("admin")): ?>
                    <th>編集</th>
                <?php endif; ?>
            </tr>
            <?php foreach ($teachers as $teacher): ?>
                <tr>
                    <!--権限がadmin以上の場合idとパスワードを表示する-->
                    <?php if (has_permission("admin")): ?>
                        <td><?php echo htmlspecialchars($teacher['id']); ?></td>
                        <td><?php echo htmlspecialchars($teacher['password']); ?></td>
                    <?php endif; ?>
                    <td><?php echo htmlspecialchars($teacher['first_name']); ?></td>
                    <td><?php echo htmlspecialchars($teacher['last_name']); ?></td>
                    <td><?php echo htmlspecialchars($teacher['email']); ?></td>
                    <td><?php echo htmlspecialchars($teacher['authority']); ?></td>
                    <td><?php echo htmlspecialchars($teacher['notes']); ?></td>

                    <!--権限がadmin以上の場合 編集・削除ボタンを表示する-->
                    <?php if (has_permission("admin")): ?>
                        <td>
                            <a href="edit_teacher.php?id=<?php echo $teacher['id']; ?>" class="button">編集</a>
                            <a href="delete_teacher.php?id=<?php echo $teacher['id']; ?>"class="button">削除</a>
                        </td>
                    <?php endif; ?>
                </tr>
            <?php endforeach; ?>
            </thead>
        </table>
        </div>
    <?php endif; ?>
</body>

</html>