<?php
// api/stock/get_quick_log.php
require_once '../../includes/auth_check.php';
require_role(['admin', 'pimpinan']);
require_once '../../config/database.php';
header('Content-Type: application/json');

try {
    $rows = $pdo->query("
        SELECT sl.*, p.name AS product_name, p.sku, p.stock AS current_stock,
               u.name AS created_by_name
        FROM stock_quick_log sl
        JOIN products p ON p.id = sl.product_id
        LEFT JOIN users u ON u.id = sl.created_by
        ORDER BY sl.created_at DESC
        LIMIT 200
    ")->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode(['status' => 'success', 'data' => $rows]);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
}
