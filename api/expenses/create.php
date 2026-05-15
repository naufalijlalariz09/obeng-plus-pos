<?php
// api/expenses/create.php
require_once '../../includes/auth_check.php';
require_role(['admin', 'pimpinan']);
require_once '../../config/database.php';
header('Content-Type: application/json');

$data = json_decode(file_get_contents('php://input'), true);

// Validasi wajib
$allowed_categories = ['Gaji', 'Utilitas', 'Sewa', 'Lain-lain'];
if (empty($data['category']) || !in_array($data['category'], $allowed_categories)) {
    echo json_encode(['status' => 'error', 'message' => 'Kategori tidak valid.']);
    exit;
}
if (empty($data['description'])) {
    echo json_encode(['status' => 'error', 'message' => 'Deskripsi wajib diisi.']);
    exit;
}
$amount = (float)($data['amount'] ?? 0);
if ($amount <= 0) {
    echo json_encode(['status' => 'error', 'message' => 'Nominal harus lebih dari 0.']);
    exit;
}
if (empty($data['date'])) {
    echo json_encode(['status' => 'error', 'message' => 'Tanggal wajib diisi.']);
    exit;
}

try {
    $pdo->prepare("
        INSERT INTO expenses
            (category, description, amount, date, recipient, ref_no, note, created_by)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?)
    ")->execute([
        $data['category'],
        clean($data['description']),
        $amount,
        $data['date'],
        $data['recipient'] ?? null,
        $data['ref_no']    ?? null,
        $data['note']      ?? null,
        $_SESSION['name'],
    ]);

    echo json_encode([
        'status'  => 'success',
        'message' => 'Pengeluaran berhasil dicatat.',
        'id'      => $pdo->lastInsertId(),
    ]);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
}
