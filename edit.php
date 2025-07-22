<?php
session_start();
require_once 'db.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$id = $_GET['id'] ?? '';
$user_id = $_SESSION['user_id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // 更新処理
    $date = $_POST['date'];
    $type = $_POST['type'];
    $amount = $_POST['amount'];
    $category = $_POST['category'];
    $memo = $_POST['memo'];

    $stmt = $pdo->prepare("UPDATE records SET date = ?, type = ?, amount = ?, category = ?, memo = ? WHERE id = ? AND user_id = ?");
    $stmt->execute([$date, $type, $amount, $category, $memo, $id, $user_id]);

    header('Location: dashboard.php');
    exit;
}

// 初期表示：該当データの取得
$stmt = $pdo->prepare("SELECT * FROM records WHERE id = ? AND user_id = ?");
$stmt->execute([$id, $user_id]);
$record = $stmt->fetch();

if (!$record) {
    exit('データが見つかりません');
}
?>

<!DOCTYPE html>
<html lang="ja">
<head>
<link rel="icon" href="favicon.png" type="image/png">
<link rel="stylesheet" href="css/style.css">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Hachi+Maru+Pop&family=Kaisei+Decol&family=M+PLUS+Rounded+1c&display=swap" rel="stylesheet">
    <meta charset="UTF-8">
    <title>編集</title>
</head>
<body>
    <h2>家計簿の編集</h2>
    <form method="post">
    <div class="form-row">
        <label for="date">日付：</label>
        <input type="date" name="date" id="date" value="<?php echo $record['date']; ?>">
    </div>

    <div class="form-row">
    <label for="type">区分：</label>
        <select name="type" id="type">
            <option value="income" <?= $record['type'] === 'income' ? 'selected' : '' ?>>収入</option>
            <option value="expense" <?= $record['type'] === 'expense' ? 'selected' : '' ?>>支出</option>
        </select>
    </div>

    <div class="form-row">
        <label for="amount">金額：</label>
        <input type="number" name="amount" value="<?php echo $record['amount']; ?>">
        <span>円</span>
    </div>

    <div class="form-row">
        <label for="category">カテゴリ：</label>
        <input type="text" name="category" value="<?php echo $record['category']; ?>">
    </div>

    <div class="form-row">
        <label for="memo">メモ：</label>
        <input type="text" name="memo" value="<?php echo $record['memo']; ?>">
    </div>
        <button type="submit">更新する</button>
    </form>
    <p><a href="dashboard.php">← 戻る</a></p>
</body>
</html>
