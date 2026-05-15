<?php
// config/database.php
$host = '127.0.0.1';
$db   = 'obeng_plus_pos'; // Sesuaikan dengan nama database yang sudah dibuat
$user = 'root'; // Sesuaikan dengan user database lokal/server
$pass = '';     // Sesuaikan dengan password database

$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

// config/database.php
try {
    $pdo = new PDO("mysql:host=$host;dbname=$db;charset=utf8mb4", $user, $pass, $options);
} catch (\PDOException $e) {
    // Ubah baris ini untuk melihat error aslinya
    die("Koneksi Gagal: " . $e->getMessage()); 
}
?>