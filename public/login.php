<?php
session_start();
require_once "../php/db.php";
$error = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $email = trim($_POST["email"]);
    $password = $_POST["password"];

    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    if ($user && password_verify($password, $user["password"])) {
        $_SESSION["user_id"] = $user["id"];
        $_SESSION["user_email"] = $user["email"];
        header("Location: profile.php");
        exit();
    } else {
        $error = "Nieprawidłowy e-mail lub hasło.";
    }
}
?>

<!DOCTYPE html>
<html lang="pl">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Logowanie - FitPanel</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
  <style>
    body {
      background-color: #f8f9fa;
    }
    .login-container {
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

<div class="login-container">
  <h3 class="mb-4 text-center">Logowanie do FitPanel</h3>

  <?php if ($error): ?>
    <div class="alert alert-danger" role="alert">
      <?= htmlspecialchars($error) ?>
    </div>
  <?php endif; ?>

  <form method="post" action="">
    <div class="mb-3">
      <label for="email" class="form-label">Email:</label>
      <input type="email" class="form-control" id="email" name="email" required autofocus value="<?= isset($email) ? htmlspecialchars($email) : '' ?>">
    </div>
    <div class="mb-3">
      <label for="password" class="form-label">Hasło:</label>
      <input type="password" class="form-control" id="password" name="password" required>
    </div>
    <button type="submit" class="btn btn-primary w-100">Zaloguj się</button>
  </form>

  <div class="mt-3 text-center">
    <span>Nie masz konta? </span>
    <a href="register.php">Zarejestruj się</a>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
