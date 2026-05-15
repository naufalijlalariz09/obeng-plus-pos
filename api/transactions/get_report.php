<?php
// api/transactions/get_report.php
session_start();
header('Content-Type: application/json');
require_once '../../config/database.php';

// Proteksi akses
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['status' => 'error', 'message' => 'Unauthorized']);
    exit();
}

// Tangkap parameter filter dari laporan.js
$start_date = $_GET['start'] ?? date('Y-m-01'); // Default: awal bulan ini
$end_date   = $_GET['end'] ?? date('Y-m-d');    // Default: hari ini
$method     = $_GET['method'] ?? '';            // Cash / Transfer / QRIS

try {
    $params = [$start_date . ' 00:00:00', $end_date . ' 23:59:59'];
    $methodFilter = "";
    
    // Jika user memfilter metode pembayaran
    if ($method !== '') {
        $methodFilter = " AND payment_method = ?";
        $params[] = $method;
    }

    // 1. Ambil Data Tabel (Diurutkan dari terbaru)
    $stmtTable = $pdo->prepare("
        SELECT invoice_no, customer_name, payment_method, grand_total, created_at
        FROM transactions
        WHERE created_at BETWEEN ? AND ? $methodFilter
        ORDER BY created_at DESC
    ");
    $stmtTable->execute($params);
    $tableData = $stmtTable->fetchAll();

    // 2. Kalkulasi Ringkasan (Summary)
    $totalIncome = 0;
    $totalTx = count($tableData);
    foreach ($tableData as $row) {
        $totalIncome += $row['grand_total'];
    }

    // 3. Buat Data Grafik (Dikelompokkan per hari)
    $chartLabels = [];
    $chartValues = [];

    $stmtChart = $pdo->prepare("
        SELECT DATE(created_at) as date, SUM(grand_total) as total
        FROM transactions
        WHERE created_at BETWEEN ? AND ? $methodFilter
        GROUP BY DATE(created_at)
        ORDER BY DATE(created_at) ASC
    ");
    $stmtChart->execute($params);
    // Fetch Key-Pair akan menghasilkan array seperti ['2026-05-10' => 5000000]
    $chartData = $stmtChart->fetchAll(PDO::FETCH_KEY_PAIR);

    // Looping tanggal dari Start sampai End agar grafik tidak terputus jika ada hari libur/kosong
    $currentDateTS = strtotime($start_date);
    $endDateTS = strtotime($end_date);
    
    while ($currentDateTS <= $endDateTS) {
        $dateStr = date('Y-m-d', $currentDateTS);
        $chartLabels[] = date('d M', $currentDateTS); // Format: 10 May
        // Jika ada penjualan di tanggal tersebut, masukkan nilainya. Jika tidak, isi 0.
        $chartValues[] = isset($chartData[$dateStr]) ? (float)$chartData[$dateStr] : 0;
        
        $currentDateTS = strtotime('+1 day', $currentDateTS);
    }

    // Kembalikan JSON sesuai struktur yang diminta oleh laporan.js
    echo json_encode([
        'status' => 'success',
        'data' => [
            'summary' => [
                'income' => $totalIncome,
                'transactions' => $totalTx
            ],
            'chart' => [
                'labels' => $chartLabels,
                'values' => $chartValues
            ],
            'table' => $tableData
        ]
    ]);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
}
?>