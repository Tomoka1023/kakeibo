<?php
session_start();
require_once 'db.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$id = $_GET['id'] ?? '';
$user_id = $_SESSION['user_id'];

$stmt = $pdo->prepare("DELETE FROM records WHERE id = ? AND user_id = ?");
$stmt->execute([$id, $user_id]);

header('Location: dashboard.php');
exit;
?>