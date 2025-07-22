<?php
session_start();
require_once 'db.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$user_id = $_SESSION['user_id'];
$month = $_POST['month'];
$budget = (int) $_POST['budget'];

// 既に予算が登録されていれば更新、なければINSERT
$stmt = $pdo->prepare("
    INSERT INTO budgets (user_id, month, budget)
    VALUES (?, ?, ?)
    ON DUPLICATE KEY UPDATE budget = VALUES(budget)
");
$stmt->execute([$user_id, $month, $budget]);

header('Location: dashboard.php');
exit;
?>
