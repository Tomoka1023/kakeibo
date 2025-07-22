<?php
session_start();
require_once 'db.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$username = $_SESSION['username'];
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
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <title>ダッシュボード</title>
</head>
<body>
    <h2>ようこそ、<?php echo htmlspecialchars($username); ?>さん！</h2>

    <p><a href="logout.php">ログアウト</a></p>

<div class="budget-section">
    <h3>月の予算を設定</h3>
    <form action="set_budget.php" method="post">
        <div class="form-row">
            <label for="budget-month">対象月：</label>
            <input type="month" name="month" id="month">
        </div>
        <div class="form-row">
            <label for="budget">予算：</label>
            <input type="number" name="budget" id="budget"> 円
        </div>

        <button type="submit">登録</button>
    </form>
    <?php
    // 今月の年月（例：2025-07）
    $currentMonth = date('Y-m');

    // 今月の予算取得
    $stmt = $pdo->prepare("SELECT budget FROM budgets WHERE user_id = ? AND month = ?");
    $stmt->execute([$_SESSION['user_id'], $currentMonth]);
    $budgetData = $stmt->fetch();
    $budget = $budgetData ? (int)$budgetData['budget'] : 0;

    // 今月の支出合計取得
    $stmt = $pdo->prepare("SELECT SUM(amount) FROM records WHERE user_id = ? AND type = 'expense' AND DATE_FORMAT(date, '%Y-%m') = ?");
    $stmt->execute([$_SESSION['user_id'], $currentMonth]);
    $expense = (int) $stmt->fetchColumn();

    // 残高計算
    $balance = $budget - $expense;
    ?>

    <!-- 表示部分 -->
    <div class="budget-box">
        <h4><?php echo htmlspecialchars($currentMonth); ?> の予算と残高</h4>
        <p>予算：<?php echo number_format($budget); ?> 円</p>
        <p>使った金額：<?php echo number_format($expense); ?> 円</p>
        <p>残り：<strong><?php echo number_format($balance); ?> 円</strong></p>
    </div>
</div>

    <h3>家計簿を記録する</h3>
    <form action="add.php" method="post">
    <div class="form-row">
        <label for="date">日付：</label>
        <input type="date" name="date" id="date">
    </div>

    <div class="form-row">
        <label for="type">区分：</label>
        <select name="type" id="type">
            <option value="income">収入</option>
            <option value="expense">支出</option>
        </select>
    </div>

    <div class="form-row">
        <label for="amount">金額：</label>
            <input type="number" name="amount" id="amount">
            <span>円</span>
    </div>

    <div class="form-row">
        <label for="category">カテゴリ：</label>
        <select name="category" id="category">
            <option value="食費">食費</option>
            <option value="交通費">交通費</option>
            <option value="日用品">日用品</option>
            <option value="娯楽費">娯楽費</option>
            <option value="趣味">趣味</option>
            <option value="給料">給料</option>
            <option value="お小遣い">お小遣い</option>
            <option value="その他">その他</option>
        </select>
    </div>

    <div class="form-row">
        <label for="memo">メモ：</label>
        <input type="text" name="memo" id="memo">
    </div>

        <button type="submit">登録</button>
    </form>

    <h3>これまでの記録</h3>

    <div id="month-nav">
    <button onclick="prevMonth()">◀︎</button>
    <span id="current-month"></span>
    <button onclick="nextMonth()">▶︎</button>
    </div>

    <table class="till">
        <thead>
        <tr>
            <th>日付</th>
            <th>区分</th>
            <th>金額</th>
            <th>カテゴリ</th>
            <th>メモ</th>
            <th>操作</th>
        </tr>
        </thead>
    <?php
    $stmt = $pdo->prepare('SELECT * FROM records WHERE user_id = ? ORDER BY date DESC');
    $stmt->execute([$_SESSION['user_id']]);
    $records = $stmt->fetchAll();
    $lastMonth = '';
    foreach ($records as $row):
        $currentMonth = substr($row['date'], 0, 7);

        // 月が変わったら tbody を切り替える
        if ($currentMonth !== $lastMonth):
            if ($lastMonth !== '') echo "</tbody>";
            echo "<tbody data-month='$currentMonth'" . ($lastMonth !== '' ? " style='display:none;'" : "") . ">";
            $lastMonth = $currentMonth;
        endif;
    ?>
    <tr>
        <td><?php echo htmlspecialchars($row['date']); ?></td>
        <td><?php echo $row['type'] === 'income' ? '収入' : '支出'; ?></td>
        <td><?php echo number_format($row['amount']); ?> 円</td>
        <td><?php echo htmlspecialchars($row['category']); ?></td>
        <td><?php echo htmlspecialchars($row['memo']); ?></td>
        <td>
            <a href="edit.php?id=<?php echo $row['id']; ?>" title="編集">✏️</a>
            <a href="delete.php?id=<?php echo $row['id']; ?>" onclick="return confirm('削除してもいい？');" title="削除">🗑️</a>
        </td>
    </tr>
    <?php endforeach; ?>
    <?php if ($lastMonth !== '') echo "</tbody>"; ?>
    </table>

    <script>
    let currentIndex = 0;
    const bodies = document.querySelectorAll("table.till tbody");
    const monthLabel = document.getElementById("current-month");

    function updateDisplay() {
        bodies.forEach((tbody, i) => {
            tbody.style.display = (i === currentIndex) ? "" : "none";
            if (i === currentIndex) {
                monthLabel.textContent = tbody.dataset.month;
            }
        });
    }

    function nextMonth() {
        if (currentIndex < bodies.length - 1) {
            currentIndex++;
            updateDisplay();
        }
    }

    function prevMonth() {
        if (currentIndex > 0) {
            currentIndex--;
            updateDisplay();
        }
    }

    updateDisplay(); // 最初に表示
    </script>

    
    <?php
    // 月別の集計
    $stmt = $pdo->prepare("
        SELECT 
            DATE_FORMAT(date, '%Y-%m') AS month,
            SUM(CASE WHEN type = 'income' THEN amount ELSE 0 END) AS total_income,
            SUM(CASE WHEN type = 'expense' THEN amount ELSE 0 END) AS total_expense
        FROM records
        WHERE user_id = ?
        GROUP BY month
        ORDER BY month DESC
    ");
    $stmt->execute([$_SESSION['user_id']]);
    $monthly = $stmt->fetchAll();
    ?>

    <h3>月別の収支</h3>
    <table class="month">
        <tr>
            <th>月</th>
            <th>収入</th>
            <th>支出</th>
        </tr>
        <?php foreach ($monthly as $m): ?>
        <tr>
            <td><?php echo htmlspecialchars($m['month']); ?></td>
            <td><?php echo number_format($m['total_income']); ?> 円</td>
            <td><?php echo number_format($m['total_expense']); ?> 円</td>
        </tr>
        <?php endforeach; ?>
    </table>

<div class="flex-container">
    <div class="left-box">    
    <h3>月別グラフ</h3>
    <div class="chart-wrapper">
    <canvas id="monthlyChart"></canvas>
    </div>

    <script>
    const ctx = document.getElementById('monthlyChart').getContext('2d');
    const chart = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: <?php echo json_encode(array_column($monthly, 'month')); ?>,
            datasets: [
                {
                    label: '収入',
                    backgroundColor: 'rgba(75, 192, 192, 0.5)',
                    data: <?php echo json_encode(array_column($monthly, 'total_income')); ?>
                },
                {
                    label: '支出',
                    backgroundColor: 'rgba(255, 99, 132, 0.5)',
                    data: <?php echo json_encode(array_column($monthly, 'total_expense')); ?>
                }
            ]
        },
        options: {
            responsive: true,
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });
    </script>

    <?php
    // カテゴリ別支出の集計
    $stmt = $pdo->prepare("
        SELECT category, SUM(amount) AS total
        FROM records
        WHERE user_id = ? AND type = 'expense'
        GROUP BY category
        ORDER BY total DESC
    ");
    $stmt->execute([$_SESSION['user_id']]);
    $category_data = $stmt->fetchAll();
    ?>
    </div>
    <div class="right-box">
    <h3>カテゴリ別支出（円グラフ）</h3>
    <div class="chart-wrapper">
    <canvas id="categoryPieChart"></canvas>
    </div>

    <script>
const pieCtx = document.getElementById('categoryPieChart').getContext('2d');

const pieChart = new Chart(pieCtx, {
    type: 'pie',
    data: {
        labels: <?php echo json_encode(array_column($category_data, 'category')); ?>,
        datasets: [{
            label: '支出',
            data: <?php echo json_encode(array_column($category_data, 'total')); ?>,
            backgroundColor: [
                'rgba(255, 99, 132, 0.6)',
                'rgba(54, 162, 235, 0.6)',
                'rgba(255, 206, 86, 0.6)',
                'rgba(75, 192, 192, 0.6)',
                'rgba(153, 102, 255, 0.6)',
                'rgba(255, 159, 64, 0.6)',
                'rgba(100, 100, 100, 0.6)'
            ]
        }]
    },
    options: {
        responsive: true,
        plugins: {
            legend: {
                position: 'bottom'
            }
        }
    }
});
</script>
</div>
</div>

</body>
</html>
