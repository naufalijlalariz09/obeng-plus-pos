<?php
// api/products/delete.php
session_start();
header('Content-Type: application/json');
require_once '../../config/database.php';

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['status' => 'error', 'message' => 'Unauthorized']);
    exit();
}

$input = json_decode(file_get_contents('php://input'), true);

if (empty($input['id'])) {
    echo json_encode(['status' => 'error', 'message' => 'ID produk tidak ditemukan.']);
    exit();
}

try {
    $stmt = $pdo->prepare("DELETE FROM products WHERE id = ?");
    $stmt->execute([$input['id']]);

    echo json_encode(['status' => 'success', 'message' => 'Produk berhasil dihapus.']);

} catch (PDOException $e) {
    // Tangkap error Foreign Key Constraint (Code 23000)
    if ($e->getCode() == '23000') {
        http_response_code(400);
        echo json_encode(['status' => 'error', 'message' => 'Gagal: Produk ini sudah memiliki riwayat transaksi dan tidak bisa dihapus.']);
    } else {
        http_response_code(500);
        echo json_encode(['status' => 'error', 'message' => 'Database error: ' . $e->getMessage()]);
    }
}
?>