<?php
// api/auth/login.php
session_start();
require_once '../../config/database.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Ambil dan bersihkan input
    $username = trim($_POST['username'] ?? '');
    $password = trim($_POST['password'] ?? '');

    // Pastikan input tidak kosong
    if (empty($username) || empty($password)) {
        header("Location: ../../login.php?error=1");
        exit();
    }

    try {
        // Ambil data user berdasarkan username
        // Menggunakan kolom 'status' sesuai dengan file register.php dan get_users.php Anda
        $stmt = $pdo->prepare("SELECT id, name, username, password, role, status FROM users WHERE username = :username LIMIT 1");
        $stmt->execute(['username' => $username]);
        $user = $stmt->fetch();

        // Verifikasi keberadaan user, status aktif, dan kecocokan password
        if ($user && $user['status'] === 'active' && password_verify($password, $user['password'])) {
            
            // Mencegah Session Fixation Attack
            session_regenerate_id(true);
            
            // Set data ke dalam session untuk digunakan di seluruh sistem
            $_SESSION['user_id']  = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['name']     = $user['name'];
            $_SESSION['role']     = $user['role'];
            $_SESSION['LAST_ACTIVITY'] = time(); // Digunakan untuk fitur auto-timeout jika ditambahkan
            
            // Catat waktu login terakhir ke database
            $update = $pdo->prepare("UPDATE users SET last_login = NOW() WHERE id = :id");
            $update->execute(['id' => $user['id']]);

            // Opsional: Catat ke activity_log jika tabelnya sudah siap
            // $log = $pdo->prepare("INSERT INTO activity_log (user_id, action, description, ip_address) VALUES (?, ?, ?, ?)");
            // $log->execute([$user['id'], 'login', 'Berhasil masuk ke sistem', $_SERVER['REMOTE_ADDR']]);

            // Alihkan ke dashboard
            header("Location: ../../pages/dashboard.php");
            exit();
        } else {
            // Jika gagal (username salah, password salah, atau akun tidak aktif)
            header("Location: ../../login.php?error=1");
            exit();
        }
    } catch (PDOException $e) {
        // Catat error ke log server, jangan tampilkan detailnya ke user
        error_log("Login Error: " . $e->getMessage());
        header("Location: ../../login.php?error=1");
        exit();
    }
} else {
    // Jika file diakses langsung tanpa melalui form POST
    header("Location: ../../login.php");
    exit();
}
?>