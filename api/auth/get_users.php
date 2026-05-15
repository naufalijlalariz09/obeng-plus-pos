<?php
// api/auth/get_users.php
session_start();
header('Content-Type: application/json');
require_once '../../config/database.php';

// Pastikan hanya admin yang bisa melihat daftar user
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    echo json_encode(['status' => 'error', 'message' => 'Unauthorized']);
    exit();
}

try {
    // Ambil data user, urutkan berdasarkan nama
    $stmt = $pdo->query("SELECT id, name, username, role, last_login, status FROM users ORDER BY name ASC");
    $users = $stmt->fetchAll();
    
    echo json_encode(['status' => 'success', 'data' => $users]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
}
?>