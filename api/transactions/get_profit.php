<?php
// api/transactions/get_profit.php
session_start();
header('Content-Type: application/json');
require_once '../../config/database.php';

// Proteksi: Hanya Admin dan Pimpinan yang boleh melihat data laba
if (!isset($_SESSION['user_id']) || !in_array($_SESSION['role'], ['admin', 'pimpinan'])) {
    echo json_encode(['status' => 'error', 'message' => 'Akses ditolak.']);
    exit();
}

$start_date = $_GET['start'] ?? date('Y-m-01');
$end_date   = $_GET['end'] ?? date('Y-m-d');

try {
    // Query untuk mengambil data per transaksi beserta modalnya
    // Laba kotor per item = (harga_jual - harga_beli) * qty
    $stmt = $pdo->prepare("
        SELECT 
            t.invoice_no, 
            t.created_at, 
            t.grand_total as revenue,
            t.discount,
            (SELECT SUM(td.qty * p.cost_price) 
             FROM transaction_details td 
             JOIN products p ON td.product_id = p.id 
             WHERE td.transaction_id = t.id) as total_cost
        FROM transactions t
        WHERE t.created_at BETWEEN ? AND ?
        ORDER BY t.created_at DESC
    ");
    $stmt->execute([$start_date . ' 00:00:00', $end_date . ' 23:59:59']);
    $transactions = $stmt->fetchAll();

    $summary = [
        'total_revenue' => 0,
        'total_cogs'    => 0, // Modal/HPP
        'total_profit'  => 0  // Untung Bersih
    ];

    foreach ($transactions as &$row) {
        $row['profit'] = $row['revenue'] - $row['total_cost'];
        
        $summary['total_revenue'] += $row['revenue'];
        $summary['total_cogs']    += $row['total_cost'];
        $summary['total_profit']  += $row['profit'];
    }

    echo json_encode([
        'status' => 'success',
        'summary' => $summary,
        'data' => $transactions
    ]);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
}
?>