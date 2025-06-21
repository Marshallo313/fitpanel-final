<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
?>

<nav class="navbar navbar-expand-lg navbar-light bg-light shadow-sm">
  <div class="container">
    <a class="navbar-brand fw-bold text-primary" href="index.php">
      <i class="bi bi-activity"></i> FitPanel
    </a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse justify-content-end" id="navbarNav">
      <ul class="navbar-nav">
        <li class="nav-item">
          <a class="nav-link" href="profile.php"><i class="bi bi-person"></i> Profil</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="bmi.php"><i class="bi bi-calculator"></i> BMI & BMR</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="calories.php"><i class="bi bi-journal-text"></i> Kalorie</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="chart.php"><i class="bi bi-bar-chart-line"></i> Wykres</a>
        </li>
        <li class="nav-item">
          <a class="nav-link text-danger" href="logout.php"><i class="bi bi-box-arrow-right"></i> Wyloguj</a>
        </li>
      </ul>
    </div>
  </div>
</nav>
