<?php
session_start();
require_once "db.php";

if (!isset($_SESSION["user_id"])) {
    header("Location: ../login.php");
    exit();
}

$id = $_SESSION["user_id"];
$name = $_POST["name"] ?? null;
$age = ($_POST["age"] >= 1 && $_POST["age"] <= 99) ? $_POST["age"] : null;
$gender = $_POST["gender"] ?? null;
$height = $_POST["height"] ?? null;
$weight = $_POST["weight"] ?? null;

$stmt = $pdo->prepare("
    UPDATE users 
    SET name = ?, age = ?, gender = ?, height = ?, weight = ?
    WHERE id = ?
");
$stmt->execute([$name, $age, $gender, $height, $weight, $id]);

header("Location: ../bmi.php");
exit();
