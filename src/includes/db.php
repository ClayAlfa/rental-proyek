<?php
/**
 * db.php – Koneksi PDO ke MySQL
 * -------------------------------------
 * Sesuaikan nilai host, db, user, pass.
 */
$host = 'localhost';
$db   = 'rental_kendaraan';
$user = 'root';
$pass = '';

$dsn = "mysql:host=$host;dbname=$db;charset=utf8mb4";

try {
    $pdo = new PDO($dsn, $user, $pass, [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES   => false,
    ]);
} catch (PDOException $e) {
    die('Koneksi DB gagal: ' . $e->getMessage());
}