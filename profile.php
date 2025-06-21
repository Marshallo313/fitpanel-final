<?php
session_start();
require_once "../php/db.php";
$pageTitle = "Profil użytkownika";
include "header.php";

// Pobranie danych użytkownika
$user_id = $_SESSION["user_id"];
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch();

// Historia BMI
$stmt = $pdo->prepare("SELECT * FROM bmi_history WHERE user_id = ? ORDER BY created_at DESC LIMIT 5");
$stmt->execute([$user_id]);
$history = $stmt->fetchAll();

// Historia BMR
$stmt = $pdo->prepare("SELECT * FROM bmr_history WHERE user_id = ? ORDER BY created_at DESC LIMIT 5");
$stmt->execute([$user_id]);
$bmr_history = $stmt->fetchAll();

// Dzisiejsze spożycie kalorii
$stmt = $pdo->prepare("SELECT SUM(kcal) AS total FROM calorie_log WHERE user_id = ? AND log_date = CURDATE()");
$stmt->execute([$user_id]);
$calories_today = $stmt->fetchColumn() ?? 0;

// Zliczanie wizyt
$page = "profile.php";
if (!isset($_SESSION["visited_profile"])) {
    $stmt = $pdo->prepare("UPDATE visits SET visit_count = visit_count + 1 WHERE page = ?");
    $stmt->execute([$page]);
    $_SESSION["visited_profile"] = true;
}
$stmt = $pdo->prepare("SELECT visit_count FROM visits WHERE page = ?");
$stmt->execute([$page]);
$visitCount = $stmt->fetchColumn();

// Powitanie i zegar
$dayName = strftime("%A", time());
?>

<!DOCTYPE html>
<html lang="pl">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Profil użytkownika</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
</head>
<body class="bg-light">

  <div class="container py-5">
    <div class="p-4 mb-5 rounded text-white" style="background: linear-gradient(90deg, #007bff, #6610f2);">
      <h2 class="fw-light">Witaj, <?= htmlspecialchars($user["name"]) ?>!</h2>
      <p class="mb-0">Dziś jest <strong><?= $dayName ?></strong>, godzina <strong id="clock">--:--:--</strong></p>
    </div>

    <h2 class="mb-4">Twój profil</h2>

    <div class="row g-4">
      <div class="col-lg-6">
        <div class="card shadow-sm">
          <div class="card-body">
            <h5 class="card-title fw-bold">Dane osobowe</h5>
            <p>Imię: <strong><?= htmlspecialchars($user["name"]) ?></strong></p>
            <p>Email: <strong><?= htmlspecialchars($user["email"]) ?></strong></p>
            <a href="edit_profile.php" class="btn btn-sm btn-outline-primary">Edytuj dane</a>
          </div>
        </div>
      </div>

      <div class="col-lg-6">
        <div class="card shadow-sm">
          <div class="card-body">
            <h5 class="card-title fw-bold">Twoje ostatnie pomiary BMI</h5>
            <ul class="list-group list-group-flush">
              <?php foreach ($history as $entry): ?>
                <li class="list-group-item">
                  <?= $entry["created_at"] ?> - BMI: <strong><?= $entry["bmi"] ?></strong>
                </li>
              <?php endforeach; ?>
            </ul>
          </div>
        </div>
      </div>
    </div>

    <div class="row mt-4">
      <div class="col-lg-6">
        <div class="card shadow-sm">
          <div class="card-body">
            <h5 class="card-title fw-bold">Zapotrzebowanie kaloryczne</h5>
            <p>Twoje dzienne zapotrzebowanie: <strong><?= $user["daily_kcal"] ?? "Brak danych" ?> kcal</strong></p>
            <p>Dziś spożyto: <strong><?= $calories_today ?> kcal</strong></p>
            <a href="set_target_kcal.php" class="btn btn-sm btn-outline-success">Ustaw cel kaloryczny</a>
            <canvas id="kcalChart" height="100" class="mt-3"></canvas>
          </div>
        </div>
      </div>

      <div class="col-lg-6">
        <div class="card shadow-sm">
          <div class="card-body">
            <h5 class="card-title fw-bold">Ostatnie pomiary BMR</h5>
            <?php if ($bmr_history): ?>
              <ul class="list-group list-group-flush">
                <?php foreach ($bmr_history as $row): ?>
                  <li class="list-group-item">
                    <?= $row["created_at"] ?> - <?= $row["gender"] ?>, <?= $row["age"] ?> lat, <?= $row["weight"] ?> kg → <strong><?= round($row["bmr"]) ?> kcal</strong>
                  </li>
                <?php endforeach; ?>
              </ul>
            <?php else: ?>
              <p>Brak zapisanych pomiarów BMR.</p>
            <?php endif; ?>
          </div>
        </div>
      </div>
    </div>
  </div>

  <div class="position-fixed bottom-0 end-0 p-3 text-muted small text-end" style="z-index: 1030;">
    <div>Czas lokalny: <span id="clock_footer">--:--:--</span></div>
    <div>Liczba odwiedzin: <?= $visitCount ?></div>
  </div>

  <script>
    function updateClockFooter() {
      const now = new Date();
      const h = String(now.getHours()).padStart(2, '0');
      const m = String(now.getMinutes()).padStart(2, '0');
      const s = String(now.getSeconds()).padStart(2, '0');
      document.getElementById('clock').textContent = `${h}:${m}:${s}`;
      document.getElementById('clock_footer').textContent = `${h}:${m}:${s}`;
    }
    setInterval(updateClockFooter, 1000);
    updateClockFooter();
  </script>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <script>
    const ctx = document.getElementById('kcalChart').getContext('2d');
    const dailyTarget = <?= $user['daily_kcal'] ?? 2000 ?>;
    const caloriesToday = <?= $calories_today ?>;
    new Chart(ctx, {
      type: 'bar',
      data: {
        labels: ['Spożycie kcal'],
        datasets: [{
          label: 'Dziś',
          data: [caloriesToday],
          backgroundColor: caloriesToday > dailyTarget ? 'rgba(255, 99, 132, 0.6)' : 'rgba(75, 192, 192, 0.6)',
          borderColor: caloriesToday > dailyTarget ? 'rgba(255, 99, 132, 1)' : 'rgba(75, 192, 192, 1)',
          borderWidth: 1
        }]
      },
      options: {
        indexAxis: 'y',
        scales: {
          x: {
            min: 0,
            max: dailyTarget * 1.5
          }
        }
      }
    });
  </script>
</body>
</html>
