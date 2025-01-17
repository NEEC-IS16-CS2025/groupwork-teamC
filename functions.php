<?php
require 'config.php';
function login($email, $password, $pdo)
{
    $stmt = $pdo->prepare('SELECT * FROM teachers WHERE email = ?');
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    if ($user && password_verify($password, $user['password'])) {
        session_start();
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['authority'] = $user['authority'];
        return true;
    }
    return false;
}

function is_logged_in()
{
    return isset($_SESSION['user_id']);
}

function get_user_authority()
{
    return $_SESSION['authority'] ?? null;
}

function logout()
{
    session_start();
    session_destroy();
    header('Location: index.php');
    exit();
}

function has_permission($required_level)
{
    $authority_levels = ['general' => 1, 'operator' => 2, 'admin' => 3];
    $current_level = $authority_levels[get_user_authority()] ?? 0;
    return $current_level >= $authority_levels[$required_level];
}
?>