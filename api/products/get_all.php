<?php
// api/products/get_all.php
session_start();
header('Content-Type: application/json');
require_once '../../config/database.php';

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['status' => 'error', 'message' => 'Unauthorized']);
    exit();
}

try {
    // Ambil data produk beserta nama kategorinya
    $stmt = $pdo->query("
        SELECT p.*, c.name as category_name 
        FROM products p 
        LEFT JOIN categories c ON p.category_id = c.id 
        WHERE p.stock > 0 OR p.type = 'jasa'
        ORDER BY p.name ASC
    ");
    $products = $stmt->fetchAll();
    
    echo json_encode(['status' => 'success', 'data' => $products]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
}
?>