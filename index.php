<?php
session_start();
$loggedIn = isset($_SESSION['user_id']);
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
    <title>家計簿アプリ まさ帳</title>
    <style>
        .kaisei-decol-regular {
            font-family: "Kaisei Decol", serif;
            font-weight: 400;
            font-style: normal;
        } 
        body {
            font-family: "Kaisei Decol", serif;
            text-align: center;
            background-color: #f0f8ff;
            padding: 50px;
        }
        body > img {
            width: 400px;
            height: 100px;
        }
        h1 {
            color: #333;
        }
        a.button {
            display: inline-block;
            margin: 10px;
            padding: 12px 24px;
            background-color: #ff7824;
            color: white;
            text-decoration: none;
            border-radius: 8px;
        }
        a.button:hover {
            background-color: #d4631d;
        }

        .logo {
            margin-bottom: 30px;
        }

        @media screen and (max-width: 600px) {

        body > img {
            width: 300px;
            height: auto;
        }

        h1 {
            font-size: 16px;
        }

        button {
            font-size: 14px;
            padding: 10px;
        }

        input, select {
            font-size: 14px;
        }
        }

    </style>
</head>
<body>
    <img src="img/logo.png" alt="家計簿アプリのロゴ">
    <h1>家計簿アプリ「まさ帳」へようこそ！</h1>

    <?php if ($loggedIn): ?>
        <p>こんにちは、<?php echo htmlspecialchars($_SESSION['username']); ?>さん！</p>
        <a class="button" href="dashboard.php">家計簿を見る</a>
        <a class="button" href="logout.php">ログアウト</a>
    <?php else: ?>
        <a class="button" href="login.php">ログイン</a>
        <a class="button" href="register.php">新規登録</a>
    <?php endif; ?>
</body>
</html>
