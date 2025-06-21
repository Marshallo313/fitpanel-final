<?php
session_start();
require_once "../php/db.php";
$pageTitle = "BMI Calculator";
include "header.php";


if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit();
}

// Pobierz dane użytkownika
$stmt = $pdo->prepare("SELECT height, weight FROM users WHERE id = ?");
$stmt->execute([$_SESSION["user_id"]]);
$user = $stmt->fetch();

$height = $user['height']; // w cm
$weight = $user['weight']; // w kg
$bmi = null;
$bmi_class = "";

if ($height && $weight) {
    $height_m = $height / 100; // konwersja cm -> metry
    $bmi = $weight / ($height_m * $height_m);
if (!isset($_SESSION["bmi_saved"])) {
    $stmt = $pdo->prepare("INSERT INTO bmi_history (user_id, height, weight, bmi) VALUES (?, ?, ?, ?)");
    $stmt->execute([$_SESSION["user_id"], $height, $weight, $bmi]);
    $_SESSION["bmi_saved"] = true;
}
    if ($bmi < 18.5) {
        $bmi_class = "Niedowaga";
    } elseif ($bmi < 25) {
        $bmi_class = "Prawidłowa waga";
    } elseif ($bmi < 30) {
        $bmi_class = "Nadwaga";
    } else {
        $bmi_class = "Otyłość";
    }
}
?>

<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <title>Kalkulator BMI</title>
    <link rel="stylesheet" href="css/style.css" />

</head>
<body>
    <h2>Kalkulator BMI</h2>

    <?php if ($bmi): ?>
        <p>Twoje BMI wynosi: <strong><?= round($bmi, 2) ?></strong></p>
        <p>Interpretacja: <strong><?= $bmi_class ?></strong></p>
    <?php else: ?>
        <p>Brakuje danych w profilu. <a href="profile.php">Uzupełnij swój profil</a>, aby skorzystać z kalkulatora.</p>
    <?php endif; ?>

<div class="d-flex justify-content-end mt-3">
  <a href="profile.php" class="btn btn-outline-primary btn-sm">Powrót do profilu</a>
</div></body>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<?php include "footer.php"; ?>
</html>
