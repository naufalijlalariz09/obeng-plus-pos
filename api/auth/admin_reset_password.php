<?php
// api/auth/admin_reset_password.php
session_start();
header('Content-Type: application/json');
require_once '../../config/database.php';

// Pastikan hanya Admin yang bisa mengakses fitur ini
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    echo json_encode(['status' => 'error', 'message' => 'Hanya Admin yang diizinkan mereset password.']);
    exit();
}

$input = json_decode(file_get_contents('php://input'), true);

if (empty($input['user_id']) || empty($input['new_password'])) {
    echo json_encode(['status' => 'error', 'message' => 'Data tidak lengkap.']);
    exit();
}

try {
    // Hash password baru
    $newHashedPassword = password_hash($input['new_password'], PASSWORD_BCRYPT);
    
    $update = $pdo->prepare("UPDATE users SET password = ? WHERE id = ?");
    $update->execute([$newHashedPassword, $input['user_id']]);

    echo json_encode(['status' => 'success', 'message' => 'Password pengguna berhasil direset!']);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['status' => 'error', 'message' => 'Database error: ' . $e->getMessage()]);
}
?>