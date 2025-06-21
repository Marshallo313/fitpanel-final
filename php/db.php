<?php
$host = 'localhost';
$dbname = 'bmi_app';
$username = 'root';
$password = ''; // domyślnie w XAMPP bez hasła

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    // Włącz raportowanie błędów
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Błąd połączenia z bazą danych: " . $e->getMessage());
}
?>
