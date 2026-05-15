<?php
// api/products/create.php
session_start();
header('Content-Type: application/json');
require_once '../../config/database.php';

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['status' => 'error', 'message' => 'Unauthorized']);
    exit();
}

$input = json_decode(file_get_contents('php://input'), true);

if (!$input || empty($input['name']) || empty($input['sku'])) {
    echo json_encode(['status' => 'error', 'message' => 'Data tidak lengkap.']);
    exit();
}

try {
    // Validasi SKU Duplicate
    $stmtCheck = $pdo->prepare("SELECT id FROM products WHERE sku = ?");
    $stmtCheck->execute([$input['sku']]);
    if ($stmtCheck->rowCount() > 0) {
        echo json_encode(['status' => 'error', 'message' => 'SKU (Barcode) sudah digunakan.']);
        exit();
    }

    $stmt = $pdo->prepare("INSERT INTO products (category_id, sku, name, brand, type, cost_price, sell_price, stock, min_stock) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
    
    $stmt->execute([
        $input['category_id'],
        trim($input['sku']),
        trim($input['name']),
        trim($input['brand']),
        $input['type'],
        (float)$input['cost_price'],
        (float)$input['sell_price'],
        (int)$input['stock'],
        (int)$input['min_stock']
    ]);

    echo json_encode(['status' => 'success', 'message' => 'Produk berhasil ditambahkan.']);

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['status' => 'error', 'message' => 'Database error: ' . $e->getMessage()]);
}
?>