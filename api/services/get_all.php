<?php
// api/services/get_all.php
session_start();
header('Content-Type: application/json');
require_once '../../config/database.php';

// Pastikan user sudah login
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['status' => 'error', 'message' => 'Unauthorized']);
    exit();
}

try {
    // Ambil HANYA produk yang memiliki tipe 'jasa'
    $stmt = $pdo->query("
        SELECT id, name, sell_price 
        FROM products 
        WHERE type = 'jasa' 
        ORDER BY name ASC
    ");
    $services = $stmt->fetchAll();
    
    echo json_encode(['status' => 'success', 'data' => $services]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['status' => 'error', 'message' => 'Database error: ' . $e->getMessage()]);
}
?>