<?php
// api/products/get_pos.php
session_start();
header('Content-Type: application/json');
require_once '../../config/database.php';

// Pastikan user sudah login
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['status' => 'error', 'message' => 'Unauthorized']);
    exit();
}

try {
    // Ambil parameter pencarian jika ada
    $search = isset($_GET['search']) ? trim($_GET['search']) : '';
    $type = isset($_GET['type']) ? trim($_GET['type']) : ''; // 'barang', 'jasa', atau kosong

    $query = "SELECT p.id, p.sku, p.name, p.brand, p.type, p.sell_price, p.stock, c.name as category_name 
              FROM products p 
              LEFT JOIN categories c ON p.category_id = c.id 
              WHERE 1=1";
    $params = [];

    // Filter Pencarian Text
    if ($search !== '') {
        $query .= " AND (p.name LIKE ? OR p.sku LIKE ?)";
        $params[] = "%$search%";
        $params[] = "%$search%";
    }

    // Filter Tipe (Barang / Jasa)
    if ($type !== '' && in_array($type, ['barang', 'jasa'])) {
        $query .= " AND p.type = ?";
        $params[] = $type;
    }

    $query .= " ORDER BY p.name ASC LIMIT 50"; // Batasi 50 agar tidak berat di frontend

    $stmt = $pdo->prepare($query);
    $stmt->execute($params);
    $products = $stmt->fetchAll();

    echo json_encode([
        'status' => 'success',
        'data' => $products
    ]);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
}
?>