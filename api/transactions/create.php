<?php
// api/transactions/create.php
session_start();
header('Content-Type: application/json');
require_once '../../config/database.php';

// Proteksi keamanan
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['status' => 'error', 'message' => 'Sesi berakhir, silakan login kembali.']);
    exit();
}

$input = json_decode(file_get_contents('php://input'), true);

if (!$input || empty($input['items'])) {
    echo json_encode(['status' => 'error', 'message' => 'Keranjang belanja kosong.']);
    exit();
}

try {
    // Mulai Database Transaction
    $pdo->beginTransaction();

    // 1. Generate Nomor Invoice Otomatis (Format: INV-TahunBulanTanggal-JamMenitDetik)
    $invoice_no = 'INV-' . date('Ymd-His');
    
    // 2. Hitung Subtotal dari keranjang secara aman di sisi server
    $subtotal = 0;
    foreach ($input['items'] as $item) {
        $subtotal += ($item['price'] * $item['qty']);
    }
    
    // 3. Hitung Diskon
    $discount_percent = isset($input['discount_percent']) ? (float)$input['discount_percent'] : 0;
    $discount_amount = ($subtotal * $discount_percent) / 100;
    
    // 4. Hitung Grand Total
    $grand_total = $subtotal - $discount_amount;

    // 5. Insert ke tabel 'transactions'
    $stmtTx = $pdo->prepare("
        INSERT INTO transactions (
            invoice_no, customer_name, customer_phone, 
            subtotal, discount, grand_total, 
            payment_method, user_id, created_at
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, NOW())
    ");

    $stmtTx->execute([
        $invoice_no,
        trim($input['customer_name'] ?? 'Umum'),
        trim($input['customer_phone'] ?? '-'),
        $subtotal,
        $discount_amount,
        $grand_total,
        $input['payment_method'] ?? 'Cash',
        $_SESSION['user_id']
    ]);

    $transactionId = $pdo->lastInsertId();

    // 6. Insert detail barang dan update stok
    $stmtDetail = $pdo->prepare("
        INSERT INTO transaction_details (
            transaction_id, product_id, qty, price, total
        ) VALUES (?, ?, ?, ?, ?)
    ");

    $stmtUpdateStock = $pdo->prepare("
        UPDATE products 
        SET stock = stock - ? 
        WHERE id = ? AND type = 'barang'
    ");

    foreach ($input['items'] as $item) {
        $itemTotal = $item['price'] * $item['qty'];
        
        // Simpan ke transaction_details
        $stmtDetail->execute([
            $transactionId,
            $item['id'],
            $item['qty'],
            $item['price'],
            $itemTotal
        ]);

        // Potong stok (hanya untuk barang fisik)
        $stmtUpdateStock->execute([
            $item['qty'],
            $item['id']
        ]);
    }

    // Jika semua proses di atas sukses tanpa error, simpan permanen
    $pdo->commit();

    echo json_encode([
        'status' => 'success', 
        'message' => 'Transaksi berhasil disimpan.',
        'invoice_no' => $invoice_no
    ]);

} catch (Exception $e) {
    // Jika ada yang error (misal koneksi terputus di tengah jalan), batalkan semua!
    $pdo->rollBack();
    http_response_code(500);
    echo json_encode(['status' => 'error', 'message' => 'Gagal simpan transaksi: ' . $e->getMessage()]);
}
?>