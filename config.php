<?php
// データベース接続情報
$host = 'localhost';
$dbname = 'teamc';
$user = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $user, password: $password);
} catch (PDOException $e) {
    echo "Database connection failed: " . $e->getMessage();
    exit;
}