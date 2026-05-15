<?php
// api/stock/create_purchase_order.php
require_once '../../includes/auth_check.php';
require_role(['admin', 'pimpinan']);
require_once '../../config/database.php';
header('Content-Type: application/json');

$data  = json_decode(file_get_contents('php://input'), true);
$items = $data['items'] ?? [];

// Validasi
if (empty($data['supplier_name'])) {
    echo json_encode(['status' => 'error', 'message' => 'Nama supplier wajib diisi.']);
    exit;
}
if (empty($data['date'])) {
    echo json_encode(['status' => 'error', 'message' => 'Tanggal wajib diisi.']);
    exit;
}
if (empty($items)) {
    echo json_encode(['status' => 'error', 'message' => 'Nota harus memiliki minimal 1 produk.']);
    exit;
}
foreach ($items as $item) {
    if (empty($item['product_id']) || empty($item['qty']) || $item['qty'] < 1) {
        echo json_encode(['status' => 'error', 'message' => 'Data produk tidak valid. Periksa kembali qty setiap item.']);
        exit;
    }
}

try {
    $pdo->beginTransaction();

    // Hitung grand total & total qty
    $grand_total = array_sum(array_map(fn($i) => ($i['qty'] ?? 0) * ($i['cost'] ?? 0), $items));
    $total_qty   = array_sum(array_column($items, 'qty'));

    // Generate ref_no jika tidak diisi
    $ref_no = $data['ref_no'] ?? null;
    if (empty($ref_no)) {
        $ref_no = 'PO-' . date('Ymd') . '-' . strtoupper(substr(uniqid(), -5));
    }

    // Insert purchase order
    $stmt = $pdo->prepare("
        INSERT INTO purchase_orders
            (ref_no, date, supplier_name, supplier_phone, grand_total, total_qty, note, created_by)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?)
    ");
    $stmt->execute([
        $ref_no,
        $data['date'],
        clean($data['supplier_name']),
        $data['supplier_phone'] ?? null,
        $grand_total,
        $total_qty,
        $data['note'] ?? null,
        $_SESSION['user_id'],
    ]);
    $po_id = $pdo->lastInsertId();

    // Insert items + update stok produk
    $stmtItem = $pdo->prepare("
        INSERT INTO purchase_order_items
            (purchase_order_id, product_id, qty, cost_price)
        VALUES (?, ?, ?, ?)
    ");

    foreach ($items as $item) {
        $qty  = (int)$item['qty'];
        $cost = (float)($item['cost'] ?? 0);

        $stmtItem->execute([$po_id, $item['product_id'], $qty, $cost]);

        // Tambah stok & update cost_price jika diisi
        if ($cost > 0) {
            $pdo->prepare("UPDATE products SET stock = stock + ?, cost_price = ? WHERE id = ?")
                ->execute([$qty, $cost, $item['product_id']]);
        } else {
            $pdo->prepare("UPDATE products SET stock = stock + ? WHERE id = ?")
                ->execute([$qty, $item['product_id']]);
        }
    }

    $pdo->commit();

    echo json_encode([
        'status'  => 'success',
        'message' => 'Nota pembelian berhasil disimpan dan stok diperbarui.',
        'po_id'   => $po_id,
        'ref_no'  => $ref_no,
    ]);

} catch (Exception $e) {
    $pdo->rollBack();
    http_response_code(500);
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
}
