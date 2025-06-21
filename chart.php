<?php
session_start();
require_once "../php/db.php";
include "navbar.php";

// Sprawdź czy użytkownik jest zalogowany
if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION["user_id"];

// Pobierz dane dziennego spożycia kalorii z ostatnich 7 dni
$stmt = $pdo->prepare("
    SELECT log_date, SUM(kcal) as total_kcal 
    FROM calorie_log 
    WHERE user_id = ? 
      AND log_date >= CURDATE() - INTERVAL 6 DAY 
    GROUP BY log_date 
    ORDER BY log_date ASC
");
$stmt->execute([$user_id]);
$calorieData = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Pobierz dzienny cel kaloryczny
$stmt = $pdo->prepare("SELECT daily_kcal FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$dailyTarget = $stmt->fetchColumn() ?? 2000;

// Przygotuj dane do JS
$dates = [];
$totals = [];
foreach ($calorieData as $row) {
    $dates[] = date('d-m', strtotime($row['log_date']));
    $totals[] = (float)$row['total_kcal'];
}

$pageTitle = "Wykres spożycia kalorii - ostatnie 7 dni";
?>

<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title><?= htmlspecialchars($pageTitle) ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="css/style.css" />
</head>
<body class="bg-light">

<div class="container py-5">
    <h2 class="mb-4"><?= htmlspecialchars($pageTitle) ?></h2>

    <div class="card shadow-sm">
        <div class="card-body">
            <canvas id="calorieChart" height="150"></canvas>
        </div>
    </div>

    <a href="profile.php" class="btn btn-outline-primary btn-sm mt-4">Powrót do profilu</a>
</div>

<div class="position-fixed bottom-0 end-0 p-3 text-muted small text-end" style="z-index: 1030;">
    <div>Czas lokalny: <span id="clock_footer">--:--:--</span></div>
</div>

<script>
function updateClockFooter() {
    const now = new Date();
    const h = String(now.getHours()).padStart(2, '0');
    const m = String(now.getMinutes()).padStart(2, '0');
    const s = String(now.getSeconds()).padStart(2, '0');
    document.getElementById('clock_footer').textContent = `${h}:${m}:${s}`;
}
setInterval(updateClockFooter, 1000);
updateClockFooter();
</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
const ctx = document.getElementById('calorieChart').getContext('2d');
const dailyTarget = <?= json_encode($dailyTarget) ?>;
const labels = <?= json_encode($dates) ?>;
const data = <?= json_encode($totals) ?>;

new Chart(ctx, {
    type: 'bar',
    data: {
        labels: labels,
        datasets: [{
            label: 'Spożycie kcal',
            data: data,
            backgroundColor: data.map(kcal => kcal > dailyTarget ? 'rgba(255, 99, 132, 0.6)' : 'rgba(75, 192, 192, 0.6)'),
            borderColor: data.map(kcal => kcal > dailyTarget ? 'rgba(255, 99, 132, 1)' : 'rgba(75, 192, 192, 1)'),
            borderWidth: 1,
            borderRadius: 5
        }]
    },
    options: {
        scales: {
            y: {
                beginAtZero: true,
                max: dailyTarget * 1.5,
                title: {
                    display: true,
                    text: 'Kalorie (kcal)'
                }
            },
            x: {
                title: {
                    display: true,
                    text: 'Data'
                }
            }
        },
        plugins: {
            legend: {
                display: true
            },
            annotation: {
                annotations: {
                    line1: {
                        type: 'line',
                        yMin: dailyTarget,
                        yMax: dailyTarget,
                        borderColor: 'rgba(255, 206, 86, 1)',
                        borderWidth: 2,
                        label: {
                            content: 'Cel kaloryczny',
                            enabled: true,
                            position: 'start'
                        }
                    }
                }
            }
        }
    }
});
</script>

</body>
</html>
