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
    <script>
        function showTable(tableType) {
            document.querySelectorAll('.table-container').forEach(table => {
                table.style.display = 'none';
            });
            document.getElementById(tableType + '-table').style.display = 'block';
        }
    </script>
</head>

<body>
    <div class="sidebar">
        <h2>ダッシュボード</h2>
        <ul>
            <li class="menu-item">
                <a href="#" onclick="showTable('student')">
                    <i class="fas fa-user-graduate">生徒情報</i>
                </a>
            </li>
            <li class="menu-item">
                <a href="#" onclick="showTable('teacher')">
                    <i class="fas fa-chalkboard-teacher">講師情報</i>
                </a>
            </li>
        </ul>
        <ul>
            <li class="menu-item"><a href="profile.php">マイページ</a></li>
            <li class="menu-item"><a href="logout.php">ログアウト</a></li>
        </ul>
    </div>
    <div class="main-content">
        <!-- 生徒情報テーブル -->
        <div id="student-table" class="table-container">
            <div class="title-container">
                <h2>生徒一覧</h2>
                <?php if (has_permission('operator')): ?>
                    <a href="add_student.php" class="button">生徒を追加</a>
                <?php endif; ?>
            </div>
            <table>
                <thead>
                    <tr>
                        <th>姓</th>
                        <th>名</th>
                        <th>担当講師</th>
                        <th>生年月日</th>
                        <th>特記事項</th>
                        <th>操作</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($students as $student): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($student['first_name']); ?></td>
                            <td><?php echo htmlspecialchars($student['last_name']); ?></td>
                            <td>
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
                                    <a href="edit_student.php?id=<?php echo $student['id']; ?>" class="button">編集</a>
                                    <a href="delete_student.php?id=<?php echo $student['id']; ?>" class="button">削除</a>
                                <?php elseif (has_permission('general') && $student['teacher_id'] == $_SESSION['user_id']): ?>
                                    <a href="edit_student_note.php?id=<?php echo $student['id']; ?>" class="button">特記事項編集</a>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <!-- 講師情報テーブル -->
        <div id="teacher-table" class="table-container" style="display: none;">
            <div class="title-container">
                <h2>講師一覧</h2>
                <?php if (has_permission('admin')): ?>
                    <a href="add_teacher.php" class="button">講師を追加</a>
                <?php endif; ?>
            </div>
            <table>
                <thead>
                    <tr>
                        <th>姓</th>
                        <th>名</th>
                        <th>メールアドレス</th>
                        <th>権限</th>
                        <th>特記事項</th>
                        <?php if (has_permission("admin")): ?>
                            <th>編集</th>
                        <?php endif; ?>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($teachers as $teacher): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($teacher['first_name']); ?></td>
                            <td><?php echo htmlspecialchars($teacher['last_name']); ?></td>
                            <td><?php echo htmlspecialchars($teacher['email']); ?></td>
                            <td><?php echo htmlspecialchars($teacher['authority']); ?></td>
                            <td><?php echo htmlspecialchars($teacher['notes']); ?></td>
                            <?php if (has_permission("admin")): ?>
                                <td>
                                    <a href="edit_teacher.php?id=<?php echo $teacher['id']; ?>" class="button">編集</a>
                                    <a href="delete_teacher.php?id=<?php echo $teacher['id']; ?>" class="button">削除</a>
                                </td>
                            <?php endif; ?>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>

</html>