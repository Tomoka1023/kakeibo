<?php
require_once 'db.php';

$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';

    if (!empty($username) && !empty($password)) {
        // パスワードをハッシュ化
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        // 重複チェック
        $stmt = $pdo->prepare('SELECT id FROM users WHERE username = ?');
        $stmt->execute([$username]);
        if ($stmt->fetch()) {
            $message = 'このユーザー名はすでに使われています。';
        } else {
            $stmt = $pdo->prepare('INSERT INTO users (username, password) VALUES (?, ?)');
            if ($stmt->execute([$username, $hashedPassword])) {
                $message = '登録成功！ログインしてね✨';
            } else {
                $message = '登録に失敗しました。';
            }
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
    <title>ユーザー登録</title>
</head>
<body>
    <h2>ユーザー登録</h2>
    <div class="form-row">
    <form method="post">
        ユーザー名<input type="text" name="username"><br>
        パスワード<input type="password" name="password"><br>
        <button type="submit">登録</button>
    </form>
    </div>
    <p style="color: red;"><?php echo htmlspecialchars($message); ?></p>
    <p><a href="login.php">→ ログインはこちら</a></p>
</body>
</html>
