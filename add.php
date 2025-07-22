<?php
session_start();
require_once 'db.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = $_SESSION['user_id'];
    $date = $_POST['date'] ?? '';
    $type = $_POST['type'] ?? '';
    $amount = $_POST['amount'] ?? '';
    $category = $_POST['category'] ?? '';
    $memo = $_POST['memo'] ?? '';

    if ($date && $type && $amount) {
        $stmt = $pdo->prepare('INSERT INTO records (user_id, date, type, amount, category, memo) VALUES (?, ?, ?, ?, ?, ?)');
        $stmt->execute([$user_id, $date, $type, $amount, $category, $memo]);
    }

    // 登録後にダッシュボードへ戻る
    header('Location: dashboard.php');
    exit;
}
?>
