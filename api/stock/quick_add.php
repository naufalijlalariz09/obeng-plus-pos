<?php
// api/stock/quick_add.php
require_once '../../includes/auth_check.php';
require_role(['admin', 'pimpinan']);
require_once '../../config/database.php';
header('Content-Type: application/json');

$data = json_decode(file_get_contents('php://input'), true);

// Validasi
if (empty($data['product_id'])) {
    echo json_encode(['status' => 'error', 'message' => 'Produk wajib dipilih.']);
    exit;
}
$qty = (int)($data['qty'] ?? 0);
if ($qty < 1) {
    echo json_encode(['status' => 'error', 'message' => 'Jumlah stok harus minimal 1.']);
    exit;
}
if (empty($data['date'])) {
    echo json_encode(['status' => 'error', 'message' => 'Tanggal wajib diisi.']);
    exit;
}

try {
    $pdo->beginTransaction();

    $cost = (float)($data['cost'] ?? 0);

    // Catat ke log
    $pdo->prepare("
        INSERT INTO stock_quick_log
            (product_id, qty, cost_price, supplier, date, note, created_by)
        VALUES (?, ?, ?, ?, ?, ?, ?)
    ")->execute([
        $data['product_id'],
        $qty,
        $cost,
        $data['supplier'] ?? null,
        $data['date'],
        $data['note'] ?? null,
        $_SESSION['user_id'],
    ]);

    // Update stok produk (dan cost_price jika diisi)
    if ($cost > 0) {
        $pdo->prepare("UPDATE products SET stock = stock + ?, cost_price = ? WHERE id = ?")
            ->execute([$qty, $cost, $data['product_id']]);
    } else {
        $pdo->prepare("UPDATE products SET stock = stock + ? WHERE id = ?")
            ->execute([$qty, $data['product_id']]);
    }

    // Ambil stok terbaru untuk dikembalikan ke frontend
    $newStock = $pdo->prepare("SELECT stock, name FROM products WHERE id = ?");
    $newStock->execute([$data['product_id']]);
    $product  = $newStock->fetch(PDO::FETCH_ASSOC);

    $pdo->commit();

    echo json_encode([
        'status'    => 'success',
        'message'   => "Stok \"{$product['name']}\" berhasil diperbarui. Stok sekarang: {$product['stock']} unit.",
        'new_stock' => $product['stock'],
    ]);

} catch (Exception $e) {
    $pdo->rollBack();
    http_response_code(500);
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
}
