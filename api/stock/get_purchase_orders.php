<?php
// api/stock/get_purchase_orders.php
require_once '../../includes/auth_check.php';
require_role(['admin', 'pimpinan']);
require_once '../../config/database.php';
header('Content-Type: application/json');

try {
    // Ambil semua purchase orders dengan total item
    $orders = $pdo->query("
        SELECT po.*,
               u.name AS created_by_name,
               COUNT(poi.id) AS item_count
        FROM purchase_orders po
        LEFT JOIN users u ON po.created_by = u.id
        LEFT JOIN purchase_order_items poi ON poi.purchase_order_id = po.id
        GROUP BY po.id
        ORDER BY po.created_at DESC
    ")->fetchAll(PDO::FETCH_ASSOC);

    // Untuk setiap order, ambil detail items-nya
    $stmt = $pdo->prepare("
        SELECT poi.*, p.name AS product_name, p.sku
        FROM purchase_order_items poi
        JOIN products p ON p.id = poi.product_id
        WHERE poi.purchase_order_id = ?
    ");

    foreach ($orders as &$order) {
        $stmt->execute([$order['id']]);
        $order['items'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    echo json_encode(['status' => 'success', 'data' => $orders]);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
}
