<?php
// includes/auth_check.php

// ── Pastikan session belum berjalan sebelum start ──
if (session_status() === PHP_SESSION_NONE) {
    session_set_cookie_params([
        'lifetime' => 0,
        'path'     => '/',
        'secure'   => isset($_SERVER['HTTPS']),   // HTTPS-only jika produksi
        'httponly' => true,                        // Blokir akses JS ke cookie
        'samesite' => 'Strict',                    // Cegah CSRF via cookie
    ]);
    session_start();
}

// ════════════════════════════════════════════
// 1. SESSION TIMEOUT (auto-logout 60 menit idle)
// ════════════════════════════════════════════
define('SESSION_TIMEOUT', 3600);

if (isset($_SESSION['LAST_ACTIVITY'])) {
    $idle = time() - $_SESSION['LAST_ACTIVITY'];
    if ($idle > SESSION_TIMEOUT) {
        session_unset();
        session_destroy();
        _redirect_login('timeout=1');
    }
}
$_SESSION['LAST_ACTIVITY'] = time();

// ════════════════════════════════════════════
// 2. SESSION FIXATION PROTECTION
//    Regenerasi ID tiap 15 menit agar tidak bisa dibajak
// ════════════════════════════════════════════
define('SESSION_REGEN_INTERVAL', 900);

if (!isset($_SESSION['CREATED'])) {
    $_SESSION['CREATED'] = time();
} elseif (time() - $_SESSION['CREATED'] > SESSION_REGEN_INTERVAL) {
    session_regenerate_id(true);
    $_SESSION['CREATED'] = time();
}

// ════════════════════════════════════════════
// 3. CEK STATUS LOGIN
// ════════════════════════════════════════════
if (!isset($_SESSION['user_id'])) {
    _redirect_login();
}

// ════════════════════════════════════════════
// 4. CSRF TOKEN
// ════════════════════════════════════════════
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// ════════════════════════════════════════════
// 5. ROLE-BASED ACCESS CONTROL (RBAC)
// ════════════════════════════════════════════

/**
 * Batasi akses halaman berdasarkan role.
 * Contoh: require_role(['admin', 'pimpinan']);
 */
function require_role(array $allowed_roles): void {
    $role = $_SESSION['role'] ?? 'guest';
    if (!in_array($role, $allowed_roles, true)) {
        http_response_code(403);
        $name = htmlspecialchars($_SESSION['name'] ?? 'Pengguna');
        $role_label = htmlspecialchars(ucfirst($role));
        echo <<<HTML
        <!DOCTYPE html>
        <html lang="id">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>403 – Akses Ditolak · Obeng Plus</title>
            <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;800&display=swap" rel="stylesheet">
            <style>
                *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
                body {
                    font-family: 'Plus Jakarta Sans', sans-serif;
                    background: #080c17;
                    color: #f0f2f8;
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    min-height: 100vh;
                    text-align: center;
                    padding: 24px;
                }
                .card {
                    background: #111827;
                    border: 1px solid #1e2a45;
                    border-radius: 16px;
                    padding: 48px 40px;
                    max-width: 420px;
                    width: 100%;
                }
                .code { font-size: 72px; font-weight: 800; color: #f97316; line-height: 1; margin-bottom: 8px; }
                h1 { font-size: 20px; font-weight: 700; margin-bottom: 8px; }
                p  { font-size: 14px; color: #8a96b2; line-height: 1.6; margin-bottom: 28px; }
                a  {
                    display: inline-flex; align-items: center; gap: 6px;
                    background: #1d6ae0; color: #fff;
                    padding: 10px 22px; border-radius: 8px;
                    text-decoration: none; font-size: 14px; font-weight: 600;
                    transition: background 0.2s;
                }
                a:hover { background: #1558c0; }
            </style>
        </head>
        <body>
            <div class="card">
                <div class="code">403</div>
                <h1>Akses Ditolak</h1>
                <p>Hai <strong>{$name}</strong>, role <strong>{$role_label}</strong> Anda tidak memiliki izin untuk mengakses halaman ini.</p>
                <a href="../pages/dashboard.php">← Kembali ke Dashboard</a>
            </div>
        </body>
        </html>
        HTML;
        exit();
    }
}

// ════════════════════════════════════════════
// 6. CSRF VALIDATION (untuk POST request)
//    Panggil validate_csrf() di endpoint yang menerima form POST.
// ════════════════════════════════════════════

/**
 * Validasi CSRF token dari request body (JSON atau form-data).
 * Lempar exception jika token tidak valid.
 */
function validate_csrf(?string $token = null): void {
    $expected = $_SESSION['csrf_token'] ?? '';
    $received  = $token ?? ($_POST['csrf_token'] ?? '');

    if (empty($expected) || !hash_equals($expected, $received)) {
        http_response_code(419);
        header('Content-Type: application/json');
        echo json_encode(['status' => 'error', 'message' => 'Token CSRF tidak valid. Muat ulang halaman dan coba lagi.']);
        exit();
    }
}

// ════════════════════════════════════════════
// 7. HELPER: REDIRECT KE LOGIN
// ════════════════════════════════════════════
function _redirect_login(string $query = ''): void {
    $url = '../login.php' . ($query ? "?{$query}" : '');
    header("Location: {$url}");
    exit();
}

// ════════════════════════════════════════════
// 8. HELPER: JSON RESPONSE (untuk API endpoint)
// ════════════════════════════════════════════

/**
 * Kirim respons JSON dan langsung exit.
 * Contoh: json_response('success', 'Data disimpan.', ['id' => 5]);
 */
function json_response(string $status, string $message, array $data = [], int $http_code = 200): void {
    http_response_code($http_code);
    header('Content-Type: application/json');
    echo json_encode(array_merge(['status' => $status, 'message' => $message], $data));
    exit();
}

// ════════════════════════════════════════════
// 9. HELPER: SANITIZE INPUT
// ════════════════════════════════════════════

/**
 * Bersihkan string input dari XSS.
 * Gunakan untuk semua data yang datang dari user sebelum ditampilkan.
 */
function clean(string $value): string {
    return htmlspecialchars(trim($value), ENT_QUOTES | ENT_HTML5, 'UTF-8');
}