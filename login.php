<?php
session_start();
require_once 'db.php';

$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';

    if (!empty($username) && !empty($password)) {
        $stmt = $pdo->prepare('SELECT * FROM users WHERE username = ?');
        $stmt->execute([$username]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password'])) {
            // ログイン成功
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            header('Location: dashboard.php');
            exit;
        } else {
            $message = 'ユーザー名またはパスワードが違います。';
        }
    } else {
        $message = 'すべての項目を入力してください。';
    }
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
    <title>ログイン</title>
</head>
<body>
    <h2>ログイン</h2>
    <div class="form-row">
    <form method="post">
        ユーザー名<input type="text" name="username"><br>
        パスワード<input type="password" name="password"><br>
        <button type="submit">ログイン</button>
    </form>
    </div>
    <p style="color: red;"><?php echo htmlspecialchars($message); ?></p>
    <p><a href="register.php">→ 新規登録はこちら</a></p>
</body>
</html>
