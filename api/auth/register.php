<?php
// api/auth/register.php
session_start();
header('Content-Type: application/json');
require_once '../../config/database.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    echo json_encode(['status' => 'error', 'message' => 'Hanya Admin yang dapat menambah pengguna.']);
    exit();
}

$input = json_decode(file_get_contents('php://input'), true);

if (empty($input['name']) || empty($input['username']) || empty($input['password']) || empty($input['role'])) {
    echo json_encode(['status' => 'error', 'message' => 'Semua kolom wajib diisi.']);
    exit();
}

try {
    // Cek apakah username sudah dipakai orang lain
    $stmtCheck = $pdo->prepare("SELECT id FROM users WHERE username = ?");
    $stmtCheck->execute([trim($input['username'])]);
    if ($stmtCheck->rowCount() > 0) {
        echo json_encode(['status' => 'error', 'message' => 'Username sudah digunakan, silakan pilih yang lain.']);
        exit();
    }

    // Enkripsi password menggunakan standar Bcrypt yang sangat aman
    $hashedPassword = password_hash($input['password'], PASSWORD_BCRYPT);
    
    $stmt = $pdo->prepare("INSERT INTO users (name, username, password, role, status) VALUES (?, ?, ?, ?, 'active')");
    $stmt->execute([
        trim($input['name']),
        trim($input['username']),
        $hashedPassword,
        $input['role']
    ]);

    echo json_encode(['status' => 'success', 'message' => 'Akun pengguna berhasil dibuat!']);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['status' => 'error', 'message' => 'Database error: ' . $e->getMessage()]);
}
?>