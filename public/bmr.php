<?php
session_start();
require_once "../php/db.php";
if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit();
}

// Pobierz dane użytkownika
$stmt = $pdo->prepare("SELECT gender, age, height, weight FROM users WHERE id = ?");
$stmt->execute([$_SESSION["user_id"]]);
$user = $stmt->fetch();

$gender = $user['gender'];
$age = $user['age'];
$height = $user['height'];
$weight = $user['weight'];

$bmr = null;
$formula = "";
$error = "";
$tdee = null;
$activity_label = "";

// Oblicz BMR
if ($gender && $age && $height && $weight) {
    if ($gender === "M") {
        $bmr = 10 * $weight + 6.25 * $height - 5 * $age + 5;
        $formula = "Mifflin-St Jeor (dla mężczyzn)";
    } elseif ($gender === "F") {
        $bmr = 10 * $weight + 6.25 * $height - 5 * $age - 161;
        $formula = "Mifflin-St Jeor (dla kobiet)";
    } else {
        $error = "Nieprawidłowa wartość płci.";
    }

    // Zapisz BMR tylko raz na sesję
    if ($bmr && !isset($_SESSION["bmr_saved"])) {
        $stmt = $pdo->prepare("INSERT INTO bmr_history (user_id, gender, age, height, weight, bmr) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->execute([$_SESSION["user_id"], $gender, $age, $height, $weight, $bmr]);
        $_SESSION["bmr_saved"] = true;
    }
} else {
    $error = "Brakuje danych w profilu. <a href='profile.php'>Uzupełnij dane</a>.";
}

// Oblicz TDEE jeśli wybrano poziom aktywności
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["activity"])) {
    $activity = $_POST["activity"];
    $activity_factors = [
        "1" => ["label" => "Siedzący tryb życia", "factor" => 1.2],
        "2" => ["label" => "Lekka aktywność", "factor" => 1.375],
        "3" => ["label" => "Umiarkowana aktywność", "factor" => 1.55],
        "4" => ["label" => "Wysoka aktywność", "factor" => 1.725],
        "5" => ["label" => "Bardzo intensywny wysiłek", "factor" => 1.9],
    ];

    if (isset($activity_factors[$activity])) {
        $factor = $activity_factors[$activity]["factor"];
        $activity_label = $activity_factors[$activity]["label"];
        $tdee = $bmr * $factor;
    }
}
?>

<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <title>Kalkulator BMR i TDEE</title>
</head>
<body>
    <h2>Kalkulator BMR</h2>

    <?php if ($error): ?>
        <p style="color:red"><?= $error ?></p>
    <?php elseif ($bmr): ?>
        <p>Twoje BMR wynosi: <strong><?= round($bmr, 2) ?> kcal</strong></p>
        <p>Obliczono na podstawie wzoru: <em><?= $formula ?></em></p>

        <hr>

        <h3>Oblicz dzienne zapotrzebowanie energetyczne (TDEE)</h3>
        <form method="post">
            <label>Wybierz poziom aktywności fizycznej:</label><br><br>
            <select name="activity" required>
                <option value="">-- wybierz --</option>
                <option value="1">1 – Siedzący tryb życia</option>
                <option value="2">2 – Lekka aktywność (1–2x w tygodniu)</option>
                <option value="3">3 – Umiarkowana aktywność (3–4x w tygodniu)</option>
                <option value="4">4 – Wysoka aktywność (5–6x w tygodniu)</option>
                <option value="5">5 – Bardzo intensywny wysiłek (codziennie)</option>
            </select><br><br>
            <button type="submit">Oblicz TDEE</button>
        </form>

        <?php if ($tdee): ?>
            <p><br>Twoje <strong>TDEE</strong> (dla poziomu: <em><?= $activity_label ?></em>) wynosi:<br>
            <strong><?= round($tdee, 2) ?> kcal</strong> dziennie.</p>
        <?php endif; ?>
    <?php endif; ?>

<div class="d-flex justify-content-end mt-3">
  <a href="profile.php" class="btn btn-outline-primary btn-sm">Powrót do profilu</a>
</div></body>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

</html>
