<?php
// api/expenses/get_all.php
require_once '../../includes/auth_check.php';
require_role(['admin', 'pimpinan']);
require_once '../../config/database.php';
header('Content-Type: application/json');

try {
    // Ambil semua pengeluaran dengan summary per kategori
    $rows = $pdo->query("
        SELECT * FROM expenses
        ORDER BY date DESC, created_at DESC
    ")->fetchAll(PDO::FETCH_ASSOC);

    // Summary per kategori (untuk chart donut)
    $summary = $pdo->query("
        SELECT
            category,
            COUNT(*) AS count,
            SUM(amount) AS total
        FROM expenses
        GROUP BY category
        ORDER BY total DESC
    ")->fetchAll(PDO::FETCH_ASSOC);

    // Total keseluruhan
    $totals = $pdo->query("
        SELECT
            SUM(amount)                                            AS grand_total,
            SUM(CASE WHEN MONTH(date)=MONTH(CURDATE()) AND YEAR(date)=YEAR(CURDATE()) THEN amount ELSE 0 END) AS this_month,
            SUM(CASE WHEN date = CURDATE()             THEN amount ELSE 0 END) AS today,
            COUNT(*)                                               AS record_count
        FROM expenses
    ")->fetch(PDO::FETCH_ASSOC);

    echo json_encode([
        'status'  => 'success',
        'data'    => $rows,
        'summary' => $summary,
        'totals'  => $totals,
    ]);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
}
