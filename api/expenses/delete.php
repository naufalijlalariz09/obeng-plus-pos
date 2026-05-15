<?php
// api/expenses/delete.php
require_once '../../includes/auth_check.php';
require_role(['admin', 'pimpinan']);
require_once '../../config/database.php';
header('Content-Type: application/json');

$data = json_decode(file_get_contents('php://input'), true);

if (empty($data['id'])) {
    echo json_encode(['status' => 'error', 'message' => 'ID wajib disertakan.']);
    exit;
}

try {
    $stmt = $pdo->prepare("DELETE FROM expenses WHERE id = ?");
    $stmt->execute([$data['id']]);

    if ($stmt->rowCount() === 0) {
        echo json_encode(['status' => 'error', 'message' => 'Data tidak ditemukan.']);
        exit;
    }

    echo json_encode(['status' => 'success', 'message' => 'Pengeluaran berhasil dihapus.']);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
}
