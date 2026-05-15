<?php
// api/auth/change_password.php
session_start();
header('Content-Type: application/json');
require_once '../../config/database.php';

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['status' => 'error', 'message' => 'Unauthorized']);
    exit();
}

$input = json_decode(file_get_contents('php://input'), true);

if (empty($input['current_password']) || empty($input['new_password'])) {
    echo json_encode(['status' => 'error', 'message' => 'Password lama dan baru wajib diisi.']);
    exit();
}

try {
    // 1. Ambil password lama dari database
    $stmt = $pdo->prepare("SELECT password FROM users WHERE id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    $user = $stmt->fetch();

    // 2. Verifikasi apakah password lama cocok
    if (!password_verify($input['current_password'], $user['password'])) {
        echo json_encode(['status' => 'error', 'message' => 'Password lama Anda salah.']);
        exit();
    }

    // 3. Hash password baru dan update ke database
    $newHashedPassword = password_hash($input['new_password'], PASSWORD_BCRYPT);
    $update = $pdo->prepare("UPDATE users SET password = ? WHERE id = ?");
    $update->execute([$newHashedPassword, $_SESSION['user_id']]);

    echo json_encode(['status' => 'success', 'message' => 'Password berhasil diperbarui.']);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['status' => 'error', 'message' => 'Database error: ' . $e->getMessage()]);
}
?>