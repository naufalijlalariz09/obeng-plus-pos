<?php
// api/dashboard/stats.php
session_start();
header('Content-Type: application/json');
require_once '../../config/database.php';

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['status' => 'error', 'message' => 'Unauthorized']);
    exit();
}

try {
    $today = date('Y-m-d');

    // 1. Total Pendapatan Hari Ini (Tanpa mengecek payment_status karena kolomnya tidak ada di DB)
    $stmtSales = $pdo->prepare("SELECT SUM(grand_total) as total FROM transactions WHERE DATE(created_at) = ?");
    $stmtSales->execute([$today]);
    $salesToday = $stmtSales->fetch()['total'] ?? 0;

    // 2. Jumlah Transaksi Hari Ini
    $stmtTx = $pdo->prepare("SELECT COUNT(id) as total FROM transactions WHERE DATE(created_at) = ?");
    $stmtTx->execute([$today]);
    $txToday = $stmtTx->fetch()['total'] ?? 0;

    // 3. Stok Menipis (Khusus untuk tipe 'barang', bukan 'jasa')
    $stmtStock = $pdo->query("SELECT COUNT(id) as total FROM products WHERE type = 'barang' AND stock <= min_stock");
    $lowStock = $stmtStock->fetch()['total'] ?? 0;

    // 4. Antrean Mobil (Karena tabel bookings belum ada, kita set 0 dulu agar tidak error)
    $activeQueue = 0; 

    // 5. Histori Transaksi Terbaru (5 Teratas)
    $stmtRecent = $pdo->query("SELECT invoice_no, customer_name, grand_total, DATE_FORMAT(created_at, '%H:%i') as time FROM transactions ORDER BY created_at DESC LIMIT 5");
    $recentTx = $stmtRecent->fetchAll();

    // 6. Data Chart (7 Hari Terakhir)
    $chartLabels = [];
    $chartValues = [];
    for ($i = 6; $i >= 0; $i--) {
        $date = date('Y-m-d', strtotime("-$i days"));
        $chartLabels[] = date('d M', strtotime($date));
        
        $stmtChart = $pdo->prepare("SELECT SUM(grand_total) as total FROM transactions WHERE DATE(created_at) = ?");
        $stmtChart->execute([$date]);
        $chartValues[] = $stmtChart->fetch()['total'] ?? 0;
    }

    echo json_encode([
        'status' => 'success',
        'data' => [
            'sales_today' => $salesToday,
            'tx_today' => $txToday,
            'low_stock' => $lowStock,
            'active_queue' => $activeQueue,
            'recent_tx' => $recentTx,
            'chart' => [
                'labels' => $chartLabels,
                'values' => $chartValues
            ]
        ]
    ]);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
}
?>