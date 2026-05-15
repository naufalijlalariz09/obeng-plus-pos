<?php
// api/transactions/get_all.php
session_start();
header('Content-Type: application/json');
require_once '../../config/database.php';

// Pastikan user sudah login
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['status' => 'error', 'message' => 'Unauthorized']);
    exit();
}

try {
    // Ambil data transaksi diurutkan dari yang terbaru
    $stmt = $pdo->query("
        SELECT id, invoice_no, customer_name, grand_total, payment_method, created_at 
        FROM transactions 
        ORDER BY created_at DESC
    ");
    $transactions = $stmt->fetchAll();
    
    echo json_encode(['status' => 'success', 'data' => $transactions]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
}
?>