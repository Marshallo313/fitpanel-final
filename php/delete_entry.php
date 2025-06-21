<?php
session_start();
require_once "db.php";

if (!isset($_SESSION["user_id"])) {
    header("Location: ../login.php");
    exit();
}

if (isset($_GET["id"])) {
    $id = intval($_GET["id"]);
    $stmt = $pdo->prepare("DELETE FROM calorie_log WHERE id = ? AND user_id = ?");
    $stmt->execute([$id, $_SESSION["user_id"]]);
}

header("Location: ../calories.php");
exit();
