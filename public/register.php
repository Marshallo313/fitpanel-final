<?php
session_start();
require_once "php/db.php";

$success = "";
$error = "";

// Obsługa formularza
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $email = trim($_POST["email"]);
    $password = $_POST["password"];
    $confirm = $_POST["confirm"];

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Nieprawidłowy adres e-mail.";
    } elseif ($password !== $confirm) {
        $error = "Hasła nie są takie same.";
    } elseif (strlen($password) < 6) {
        $error = "Hasło musi mieć min. 6 znaków.";
    } else {
        // Szyfrowanie hasła
        $hashed = password_hash($password, PASSWORD_DEFAULT);

        try {
            $stmt = $pdo->prepare("INSERT INTO users (email, password) VALUES (?, ?)");
            $stmt->execute([$email, $hashed]);
            $success = "Rejestracja zakończona sukcesem. <a href='login.php'>Zaloguj się</a>";
        } catch (PDOException $e) {
            $error = "Błąd: " . ($e->errorInfo[1] == 1062 ? "Ten e-mail już istnieje." : $e->getMessage());
        }
    }
}
?>

<!DOCTYPE html>
<html lang="pl">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Rejestracja - FitPanel</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
  <style>
    body {
      background-color: #f8f9fa;
    }
    .register-container {
      max-width: 400px;
      margin: 80px auto;
      padding: 30px;
      background: white;
      border-radius: 8px;
      box-shadow: 0 4px 12px rgba(0,0,0,0.1);
      animation: fadeIn 0.7s ease forwards;
      opacity: 0;
    }
    @keyframes fadeIn {
      to { opacity: 1; }
    }
  </style>
</head>
<body>

<div class="register-container">
  <h3 class="mb-4 text-center">Rejestracja w FitPanel</h3>

  <?php if ($success): ?>
    <div class="alert alert-success" role="alert">
      <?= $success ?>
    </div>
  <?php elseif ($error): ?>
    <div class="alert alert-danger" role="alert">
      <?= $error ?>
    </div>
  <?php endif; ?>

  <form method="post" action="">
    <div class="mb-3">
      <label for="email" class="form-label">Email:</label>
      <input type="email" class="form-control" id="email" name="email" required autofocus value="<?= isset($email) ? htmlspecialchars($email) : '' ?>">
    </div>
    <div class="mb-3">
      <label for="password" class="form-label">Hasło:</label>
      <input type="password" class="form-control" id="password" name="password" required minlength="6">
    </div>
    <div class="mb-3">
      <label for="confirm" class="form-label">Potwierdź hasło:</label>
      <input type="password" class="form-control" id="confirm" name="confirm" required minlength="6">
    </div>
    <button type="submit" class="btn btn-primary w-100">Zarejestruj</button>
  </form>

  <div class="mt-3 text-center">
    <span>Masz już konto? </span>
    <a href="login.php">Zaloguj się</a>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
