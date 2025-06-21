<?php
session_start();
if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <title>Menu główne</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <link href="style.css" rel="stylesheet">
</head>
<body>
<div class="container py-5">
    <h2 class="text-center mb-4">Witaj w Twoim panelu</h2>

    <div class="row g-4">
        <div class="col-md-4">
            <a href="profile.php" class="text-decoration-none">
                <div class="card menu-card text-center shadow-sm transition">
                    <div class="card-body">
                        <i class="bi bi-person fs-1 text-primary"></i>
                        <h5 class="card-title mt-2">Twój profil</h5>
                    </div>
                </div>
            </a>
        </div>
        <div class="col-md-4">
            <a href="bmi.php" class="text-decoration-none">
                <div class="card menu-card text-center shadow-sm transition">
                    <div class="card-body">
                        <i class="bi bi-calculator fs-1 text-success"></i>
                        <h5 class="card-title mt-2">BMI & BMR</h5>
                    </div>
                </div>
            </a>
        </div>
        <div class="col-md-4">
            <a href="calories.php" class="text-decoration-none">
                <div class="card menu-card text-center shadow-sm transition">
                    <div class="card-body">
                        <i class="bi bi-journal-text fs-1 text-warning"></i>
                        <h5 class="card-title mt-2">Dziennik kalorii</h5>
                    </div>
                </div>
            </a>
        </div>
        <div class="col-md-4">
            <a href="chart.php" class="text-decoration-none">
                <div class="card menu-card text-center shadow-sm transition">
                    <div class="card-body">
                        <i class="bi bi-bar-chart-line fs-1 text-info"></i>
                        <h5 class="card-title mt-2">Wykres kalorii</h5>
                    </div>
                </div>
            </a>
        </div>
        <div class="col-md-4">
            <a href="logout.php" class="text-decoration-none">
                <div class="card menu-card text-center shadow-sm transition">
                    <div class="card-body">
                        <i class="bi bi-box-arrow-right fs-1 text-danger"></i>
                        <h5 class="card-title mt-2">Wyloguj się</h5>
                    </div>
                </div>
            </a>
        </div>
    </div>
</div>
</body>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

</html>
