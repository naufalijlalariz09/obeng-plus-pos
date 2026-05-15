<?php
// api/products/update.php
session_start();
header('Content-Type: application/json');
require_once '../../config/database.php';

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['status' => 'error', 'message' => 'Unauthorized']);
    exit();
}

$input = json_decode(file_get_contents('php://input'), true);

if (!$input || empty($input['id']) || empty($input['name']) || empty($input['sku'])) {
    echo json_encode(['status' => 'error', 'message' => 'Data tidak lengkap. Pastikan Nama dan SKU terisi.']);
    exit();
}

try {
    // 1. Validasi SKU Duplicate (Pastikan SKU tidak dipakai oleh produk LAIN)
    $stmtCheck = $pdo->prepare("SELECT id FROM products WHERE sku = ? AND id != ?");
    $stmtCheck->execute([$input['sku'], $input['id']]);
    if ($stmtCheck->rowCount() > 0) {
        echo json_encode(['status' => 'error', 'message' => 'SKU (Barcode) sudah digunakan oleh produk lain.']);
        exit();
    }

    // 2. Cari atau Buat Kategori Baru berdasarkan Nama yang diinput dari form
    $categoryId = null;
    $categoryName = trim($input['category'] ?? '');
    
    if ($categoryName !== '') {
        $stmtCat = $pdo->prepare("SELECT id FROM categories WHERE name = ? LIMIT 1");
        $stmtCat->execute([$categoryName]);
        $cat = $stmtCat->fetch();
        
        if ($cat) {
            $categoryId = $cat['id'];
        } else {
            // Jika kategori belum ada, buat otomatis
            $stmtInsertCat = $pdo->prepare("INSERT INTO categories (name) VALUES (?)");
            $stmtInsertCat->execute([$categoryName]);
            $categoryId = $pdo->lastInsertId();
        }
    }

    // 3. Update Data Produk
    $stmt = $pdo->prepare("
        UPDATE products 
        SET category_id = ?, sku = ?, name = ?, brand = ?, type = ?, 
            cost_price = ?, sell_price = ?, stock = ?
        WHERE id = ?
    ");
    
    $stmt->execute([
        $categoryId,
        trim($input['sku']),
        trim($input['name']),
        trim($input['brand'] ?? ''),
        $input['type'],
        (float)($input['buy_price'] ?? 0),
        (float)$input['sell_price'],
        (int)$input['stock'],
        $input['id']
    ]);

    echo json_encode(['status' => 'success', 'message' => 'Produk berhasil diperbarui.']);

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['status' => 'error', 'message' => 'Database error: ' . $e->getMessage()]);
}
?>