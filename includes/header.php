<?php
// includes/header.php
require_once 'auth_check.php';
$current_page = basename($_SERVER['PHP_SELF'], ".php");
$user_role = $_SESSION['role'];

// Page title map
$page_labels = [
    'dashboard'   => 'Dashboard',
    'kasir'       => 'Kasir / POS',
    'jasa'        => 'Pekerjaan Teknisi',
    'produk'      => 'Master Produk',
    'stok_masuk'  => 'Stok Masuk',
    'transaksi'   => 'Histori Transaksi',
    'laporan'     => 'Laporan Penjualan',
    'laba_rugi'   => 'Laporan Laba / Rugi',
    'pengeluaran' => 'Pengeluaran Operasional',
    'pengguna'    => 'Manajemen User',
];
$page_title = $page_labels[$current_page] ?? ucfirst(str_replace('_', ' ', $current_page));
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Obeng Plus · <?php echo $page_title; ?></title>

    <link rel="manifest" href="../manifest.json">
    <meta name="theme-color" content="#0a0f1e">

    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Space+Grotesk:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@latest/tabler-icons.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        :root {
            --bg-root:    #080c17;
            --bg-sidebar: #0c1220;
            --bg-card:    #111827;
            --bg-input:   #0f1929;
            --border:     #1e2a45;
            --border-mid: #2d3a55;
            --text-pri:   #f0f2f8;
            --text-sec:   #8a96b2;
            --text-muted: #3d4f73;
            --accent-blue: #60a5fa;
            --accent-orange: #f97316;
            --accent-purple: #a78bfa;
            --glow-blue: rgba(96,165,250,0.15);
            --sidebar-w: 240px;
        }

        html, body { height: 100%; overflow: hidden; }

        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background: var(--bg-root);
            color: var(--text-pri);
            display: flex;
            antialiased: true;
        }

        /* ── Scrollbar ── */
        ::-webkit-scrollbar { width: 5px; height: 5px; }
        ::-webkit-scrollbar-track { background: transparent; }
        ::-webkit-scrollbar-thumb { background: var(--border-mid); border-radius: 10px; }

        /* ══════════════════════════════
           SIDEBAR
        ══════════════════════════════ */
        #sidebar {
            width: var(--sidebar-w);
            min-width: var(--sidebar-w);
            background: var(--bg-sidebar);
            border-right: 1px solid var(--border);
            display: flex;
            flex-direction: column;
            height: 100vh;
            flex-shrink: 0;
            position: relative;
            z-index: 40;
            transition: transform 0.3s cubic-bezier(0.4,0,0.2,1);
        }

        @media (max-width: 768px) {
            #sidebar {
                position: fixed;
                top: 0; left: 0; bottom: 0;
                transform: translateX(-100%);
            }
            #sidebar.open { transform: translateX(0); }
        }

        /* Brand / Logo */
        .sb-brand {
            padding: 20px 16px 16px;
            border-bottom: 1px solid var(--border);
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .sb-logo {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .sb-logo-icon {
            width: 38px; height: 38px;
            background: linear-gradient(135deg, #1d6ae0, #7c3aed);
            border-radius: 11px;
            display: flex; align-items: center; justify-content: center;
            font-size: 18px;
            color: #fff;
            box-shadow: 0 4px 14px rgba(29,106,224,0.35);
            flex-shrink: 0;
        }

        .sb-logo-text { line-height: 1.2; }
        .sb-logo-name {
            font-family: 'Space Grotesk', sans-serif;
            font-size: 14px;
            font-weight: 700;
            color: #fff;
            letter-spacing: -0.2px;
        }
        .sb-logo-sub {
            font-size: 10px;
            font-weight: 700;
            letter-spacing: 1.5px;
            text-transform: uppercase;
            color: var(--accent-orange);
        }

        #close-sidebar {
            background: transparent;
            border: none;
            color: var(--text-muted);
            font-size: 18px;
            cursor: pointer;
            display: none;
            width: 28px; height: 28px;
            align-items: center; justify-content: center;
            border-radius: 6px;
            transition: color 0.15s;
        }

        #close-sidebar:hover { color: var(--text-pri); }

        @media (max-width: 768px) { #close-sidebar { display: flex; } }

        /* Nav */
        .sb-nav {
            flex: 1;
            overflow-y: auto;
            padding: 8px 10px;
        }

        .sb-section-label {
            font-size: 9px;
            font-weight: 800;
            letter-spacing: 1.8px;
            text-transform: uppercase;
            color: var(--text-muted);
            padding: 18px 8px 6px;
        }

        .nav-link {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 9px 12px;
            border-radius: 10px;
            font-size: 13px;
            font-weight: 500;
            color: var(--text-sec);
            text-decoration: none;
            margin-bottom: 1px;
            position: relative;
            transition: all 0.18s cubic-bezier(0.4,0,0.2,1);
            border: 1px solid transparent;
        }

        .nav-link i { font-size: 17px; opacity: 0.7; transition: opacity 0.18s; flex-shrink: 0; }

        .nav-link:hover {
            background: rgba(255,255,255,0.04);
            color: #fff;
            border-color: var(--border);
        }

        .nav-link:hover i { opacity: 1; }

        .nav-link.active {
            background: rgba(29,106,224,0.12);
            color: var(--accent-blue);
            border-color: rgba(29,106,224,0.25);
            font-weight: 700;
        }

        .nav-link.active i {
            opacity: 1;
            color: var(--accent-blue);
        }

        .nav-link.active::before {
            content: '';
            position: absolute;
            left: 0; top: 6px; bottom: 6px;
            width: 3px;
            background: var(--accent-blue);
            border-radius: 0 4px 4px 0;
            box-shadow: 0 0 8px var(--accent-blue);
        }

        /* User profile bottom */
        .sb-user {
            padding: 12px;
            border-top: 1px solid var(--border);
        }

        .sb-user-card {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 10px 12px;
            background: rgba(255,255,255,0.03);
            border: 1px solid var(--border);
            border-radius: 12px;
            cursor: pointer;
            transition: all 0.15s;
        }

        .sb-user-card:hover {
            background: rgba(255,255,255,0.06);
            border-color: var(--border-mid);
        }

        .sb-avatar {
            width: 32px; height: 32px;
            border-radius: 50%;
            background: linear-gradient(135deg, var(--accent-orange), #f59e0b);
            color: #fff;
            font-size: 12px;
            font-weight: 800;
            text-transform: uppercase;
            display: flex; align-items: center; justify-content: center;
            flex-shrink: 0;
            box-shadow: 0 2px 10px rgba(249,115,22,0.3);
        }

        .sb-user-info { flex: 1; min-width: 0; }
        .sb-user-name {
            font-size: 12px;
            font-weight: 700;
            color: #fff;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        .sb-user-role {
            font-size: 10px;
            font-weight: 600;
            color: var(--accent-orange);
            text-transform: capitalize;
        }

        .sb-user-action {
            color: var(--text-muted);
            font-size: 15px;
        }

        /* ══════════════════════════════
           TOPBAR
        ══════════════════════════════ */
        #topbar {
            height: 58px;
            background: rgba(8,12,23,0.92);
            backdrop-filter: blur(16px);
            border-bottom: 1px solid var(--border);
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 20px;
            flex-shrink: 0;
            z-index: 10;
        }

        .tb-left { display: flex; align-items: center; gap: 14px; }

        #open-sidebar {
            background: var(--bg-card);
            border: 1px solid var(--border);
            color: var(--text-sec);
            width: 36px; height: 36px;
            border-radius: 10px;
            display: none;
            align-items: center; justify-content: center;
            font-size: 18px;
            cursor: pointer;
            transition: all 0.15s;
        }

        #open-sidebar:hover { border-color: var(--border-mid); color: #fff; }

        @media (max-width: 768px) { #open-sidebar { display: flex; } }

        .tb-page-title {
            font-family: 'Space Grotesk', sans-serif;
            font-size: 16px;
            font-weight: 700;
            color: #fff;
            letter-spacing: -0.2px;
        }

        .tb-breadcrumb {
            font-size: 11px;
            color: var(--text-muted);
            display: flex;
            align-items: center;
            gap: 4px;
        }

        .tb-right { display: flex; align-items: center; gap: 8px; }

        /* Search bar */
        .tb-search {
            display: flex;
            align-items: center;
            gap: 8px;
            background: var(--bg-card);
            border: 1px solid var(--border);
            border-radius: 10px;
            padding: 7px 12px;
            width: 220px;
            transition: border-color 0.15s, box-shadow 0.15s;
        }

        .tb-search:focus-within {
            border-color: rgba(29,106,224,0.5);
            box-shadow: 0 0 0 3px rgba(29,106,224,0.1);
        }

        .tb-search i { color: var(--text-muted); font-size: 15px; flex-shrink: 0; }

        .tb-search input {
            background: transparent;
            border: none;
            outline: none;
            font-family: 'Plus Jakarta Sans', sans-serif;
            font-size: 12px;
            color: var(--text-pri);
            width: 100%;
        }

        .tb-search input::placeholder { color: var(--text-muted); }

        .tb-shortcut {
            font-size: 9px;
            font-weight: 700;
            color: var(--text-muted);
            background: var(--bg-root);
            border: 1px solid var(--border);
            border-radius: 5px;
            padding: 2px 6px;
            flex-shrink: 0;
        }

        @media (max-width: 900px) { .tb-search { display: none; } }

        /* Clock */
        .tb-clock {
            display: flex;
            align-items: center;
            gap: 6px;
            background: rgba(29,106,224,0.1);
            border: 1px solid rgba(29,106,224,0.2);
            border-radius: 10px;
            padding: 6px 12px;
        }

        .tb-clock i { color: var(--accent-blue); font-size: 13px; }
        #live-clock {
            font-family: 'Space Grotesk', sans-serif;
            font-size: 12px;
            font-weight: 700;
            color: var(--accent-blue);
            letter-spacing: 0.5px;
        }

        @media (max-width: 640px) { .tb-clock { display: none; } }

        /* Icon buttons */
        .tb-icon-btn {
            width: 36px; height: 36px;
            border-radius: 10px;
            background: var(--bg-card);
            border: 1px solid var(--border);
            color: var(--text-sec);
            font-size: 17px;
            display: flex; align-items: center; justify-content: center;
            cursor: pointer;
            transition: all 0.15s;
            position: relative;
        }

        .tb-icon-btn:hover { border-color: var(--border-mid); color: #fff; }

        .tb-icon-btn.notif-btn:hover { border-color: rgba(245,158,11,0.4); color: #f59e0b; }
        .tb-icon-btn.logout-btn:hover { border-color: rgba(239,68,68,0.4); color: #ef4444; background: rgba(239,68,68,0.06); }

        .notif-dot {
            position: absolute;
            top: 7px; right: 7px;
            width: 7px; height: 7px;
            border-radius: 50%;
            background: var(--accent-orange);
            border: 1.5px solid var(--bg-card);
            box-shadow: 0 0 6px rgba(249,115,22,0.6);
        }

        /* ══════════════════════════════
           MAIN CONTENT WRAPPER
        ══════════════════════════════ */
        #app-shell {
            flex: 1;
            display: flex;
            flex-direction: column;
            height: 100vh;
            overflow: hidden;
            min-width: 0;
        }

        #main-scroll {
            flex: 1;
            overflow-y: auto;
            padding: 24px 28px;
            scroll-behavior: smooth;
        }

        @media (max-width: 640px) { #main-scroll { padding: 16px; } }

        /* ══════════════════════════════
           SIDEBAR OVERLAY (mobile)
        ══════════════════════════════ */
        #sidebar-overlay {
            position: fixed;
            inset: 0;
            background: rgba(0,0,0,0.65);
            z-index: 30;
            display: none;
            backdrop-filter: blur(2px);
        }

        #sidebar-overlay.show { display: block; }

        /* ══════════════════════════════
           MODAL PASSWORD
        ══════════════════════════════ */
        .modal-overlay {
            position: fixed;
            inset: 0;
            background: rgba(0,0,0,0.7);
            backdrop-filter: blur(6px);
            z-index: 999;
            display: none;
            align-items: center;
            justify-content: center;
        }

        .modal-overlay.open { display: flex; }

        .modal-box {
            background: var(--bg-card);
            border: 1px solid var(--border);
            border-radius: 20px;
            width: 100%;
            max-width: 380px;
            overflow: hidden;
            animation: modalIn 0.2s ease;
        }

        @keyframes modalIn {
            from { opacity: 0; transform: scale(0.96) translateY(8px); }
            to   { opacity: 1; transform: scale(1) translateY(0); }
        }

        .modal-head {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 16px 20px;
            border-bottom: 1px solid var(--border);
            background: rgba(255,255,255,0.02);
        }

        .modal-head h3 {
            font-family: 'Space Grotesk', sans-serif;
            font-size: 15px;
            font-weight: 700;
            color: #fff;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .modal-head h3 i { color: var(--accent-blue); }

        .modal-close-btn {
            background: transparent;
            border: 1px solid var(--border);
            color: var(--text-muted);
            width: 30px; height: 30px;
            border-radius: 8px;
            display: flex; align-items: center; justify-content: center;
            cursor: pointer;
            font-size: 15px;
            transition: all 0.15s;
        }

        .modal-close-btn:hover { border-color: #ef4444; color: #ef4444; }

        .modal-body { padding: 20px; }

        .form-group { margin-bottom: 16px; }

        .form-label {
            display: block;
            font-size: 10px;
            font-weight: 800;
            letter-spacing: 1.2px;
            text-transform: uppercase;
            color: var(--text-muted);
            margin-bottom: 7px;
        }

        .form-input {
            width: 100%;
            background: var(--bg-input);
            border: 1px solid var(--border);
            color: var(--text-pri);
            font-family: 'Plus Jakarta Sans', sans-serif;
            font-size: 13px;
            padding: 10px 14px;
            border-radius: 10px;
            outline: none;
            transition: border-color 0.15s, box-shadow 0.15s;
        }

        .form-input:focus {
            border-color: rgba(29,106,224,0.5);
            box-shadow: 0 0 0 3px rgba(29,106,224,0.1);
        }

        .form-input::placeholder { color: var(--text-muted); }

        .btn-primary {
            width: 100%;
            background: linear-gradient(135deg, #1d6ae0, #7c3aed);
            border: none;
            color: #fff;
            font-family: 'Plus Jakarta Sans', sans-serif;
            font-size: 13px;
            font-weight: 700;
            padding: 11px;
            border-radius: 12px;
            cursor: pointer;
            transition: opacity 0.15s, transform 0.15s;
            margin-top: 4px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
        }

        .btn-primary:hover { opacity: 0.9; }
        .btn-primary:active { transform: scale(0.98); }
        .btn-primary:disabled { opacity: 0.5; cursor: not-allowed; }

        /* ══════════════════════════════
           TOAST NOTIFICATION
        ══════════════════════════════ */
        #toast-container {
            position: fixed;
            bottom: 24px;
            right: 24px;
            z-index: 9999;
            display: flex;
            flex-direction: column;
            gap: 8px;
            pointer-events: none;
        }

        .toast {
            display: flex;
            align-items: center;
            gap: 10px;
            background: var(--bg-card);
            border: 1px solid var(--border);
            border-radius: 14px;
            padding: 12px 16px;
            font-size: 13px;
            font-weight: 500;
            color: var(--text-pri);
            box-shadow: 0 8px 30px rgba(0,0,0,0.4);
            min-width: 260px;
            max-width: 360px;
            pointer-events: all;
            animation: toastIn 0.3s cubic-bezier(0.34,1.56,0.64,1);
        }

        .toast.removing { animation: toastOut 0.25s ease forwards; }

        @keyframes toastIn {
            from { opacity: 0; transform: translateX(20px) scale(0.95); }
            to   { opacity: 1; transform: translateX(0) scale(1); }
        }

        @keyframes toastOut {
            to { opacity: 0; transform: translateX(20px) scale(0.95); }
        }

        .toast .toast-icon { font-size: 18px; flex-shrink: 0; }
        .toast.success { border-left: 3px solid #10b981; }
        .toast.success .toast-icon { color: #10b981; }
        .toast.error { border-left: 3px solid #ef4444; }
        .toast.error .toast-icon { color: #ef4444; }
        .toast.info { border-left: 3px solid var(--accent-blue); }
        .toast.info .toast-icon { color: var(--accent-blue); }
        .toast.warning { border-left: 3px solid #f59e0b; }
        .toast.warning .toast-icon { color: #f59e0b; }
    </style>
</head>
<body>

<div id="sidebar-overlay"></div>

<!-- ═══════════ SIDEBAR ═══════════ -->
<aside id="sidebar">

    <div class="sb-brand">
        <div class="sb-logo">
            <div class="sb-logo-icon"><i class="ti ti-speakerphone"></i></div>
            <div class="sb-logo-text">
                <div class="sb-logo-name">Obeng Plus</div>
                <div class="sb-logo-sub">Car Audio</div>
            </div>
        </div>
        <button id="close-sidebar"><i class="ti ti-x"></i></button>
    </div>

    <nav class="sb-nav">
        <?php
        $menu_sections = [
            'Aktivitas Utama' => [
                'dashboard' => ['icon' => 'ti-layout-dashboard', 'label' => 'Dashboard',        'roles' => ['admin', 'pimpinan', 'kasir']],
                'kasir'     => ['icon' => 'ti-cash-register',    'label' => 'Kasir / POS',       'roles' => ['admin', 'kasir']],
            ],
            'Inventaris & Jasa' => [
                'jasa'       => ['icon' => 'ti-tool',             'label' => 'Pekerjaan Teknisi', 'roles' => ['admin']],
                'produk'     => ['icon' => 'ti-package',          'label' => 'Master Produk',     'roles' => ['admin', 'pimpinan', 'kasir']],
                'stok_masuk' => ['icon' => 'ti-package-import',   'label' => 'Stok Masuk',        'roles' => ['admin', 'pimpinan']],
            ],
            'Laporan & Keuangan' => [
                'transaksi'   => ['icon' => 'ti-receipt',                   'label' => 'Histori Transaksi',         'roles' => ['admin', 'pimpinan', 'kasir']],
                'laporan'     => ['icon' => 'ti-chart-pie',                 'label' => 'Laporan Penjualan',         'roles' => ['admin', 'pimpinan']],
                'laba_rugi'   => ['icon' => 'ti-chart-arrows-vertical',     'label' => 'Laporan Laba/Rugi',         'roles' => ['admin', 'pimpinan']],
                'pengeluaran' => ['icon' => 'ti-wallet',                    'label' => 'Pengeluaran Operasional',   'roles' => ['admin', 'pimpinan']],
            ],
            'Pengaturan Sistem' => [
                'pengguna'  => ['icon' => 'ti-users', 'label' => 'Manajemen User', 'roles' => ['admin']],
            ]
        ];

        foreach ($menu_sections as $section_title => $items):
            $has_access = false;
            foreach ($items as $item) {
                if (in_array($user_role, $item['roles'])) { $has_access = true; break; }
            }
            if ($has_access):
        ?>
            <div class="sb-section-label"><?php echo $section_title; ?></div>
            <?php foreach ($items as $key => $item): ?>
                <?php if (in_array($user_role, $item['roles'])): ?>
                <a href="<?php echo $key; ?>.php" class="nav-link <?php echo ($current_page === $key) ? 'active' : ''; ?>">
                    <i class="ti <?php echo $item['icon']; ?>"></i>
                    <span><?php echo $item['label']; ?></span>
                </a>
                <?php endif; ?>
            <?php endforeach; ?>
        <?php
            endif;
        endforeach;
        ?>
    </nav>

    <div class="sb-user">
        <div class="sb-user-card" onclick="openPasswordModal()" title="Klik untuk ganti password">
            <div class="sb-avatar"><?php echo substr($_SESSION['name'], 0, 1); ?></div>
            <div class="sb-user-info">
                <div class="sb-user-name"><?php echo htmlspecialchars($_SESSION['name']); ?></div>
                <div class="sb-user-role"><?php echo htmlspecialchars($_SESSION['role']); ?></div>
            </div>
            <i class="ti ti-chevron-right sb-user-action"></i>
        </div>
    </div>

</aside>

<!-- ═══════════ MAIN SHELL ═══════════ -->
<div id="app-shell">

    <!-- TOPBAR -->
    <header id="topbar">
        <div class="tb-left">
            <button id="open-sidebar"><i class="ti ti-menu-2"></i></button>
            <div>
                <div class="tb-page-title"><?php echo $page_title; ?></div>
                <div class="tb-breadcrumb">
                    <i class="ti ti-home" style="font-size:10px;"></i>
                    Obeng Plus
                    <i class="ti ti-chevron-right" style="font-size:10px;"></i>
                    <?php echo $page_title; ?>
                </div>
            </div>
        </div>

        <div class="tb-right">
            <div class="tb-search">
                <i class="ti ti-search"></i>
                <input type="text" placeholder="Cari invoice, nama customer...">
                <span class="tb-shortcut">⌘K</span>
            </div>

            <div class="tb-clock">
                <i class="ti ti-clock"></i>
                <span id="live-clock">--:--:--</span>
            </div>

            <button class="tb-icon-btn notif-btn" title="Notifikasi">
                <i class="ti ti-bell"></i>
                <span class="notif-dot"></span>
            </button>

            <a href="../api/auth/logout.php" class="tb-icon-btn logout-btn" title="Logout">
                <i class="ti ti-logout"></i>
            </a>
        </div>
    </header>

    <!-- MAIN CONTENT -->
    <div class="flex-1 overflow-y-auto" id="main-scroll" style="flex:1;overflow-y:auto;padding:24px 28px;scroll-behavior:smooth;">

<!-- ═══════════ MODAL PASSWORD ═══════════ -->
<div class="modal-overlay" id="modal-password">
    <div class="modal-box">
        <div class="modal-head">
            <h3><i class="ti ti-lock"></i> Ganti Password</h3>
            <button class="modal-close-btn" onclick="closePasswordModal()"><i class="ti ti-x"></i></button>
        </div>
        <div class="modal-body">
            <form id="form-change-password" onsubmit="submitChangePassword(event)">
                <div class="form-group">
                    <label class="form-label">Password Saat Ini</label>
                    <input type="password" id="current_password" class="form-input" placeholder="Masukkan password lama" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Password Baru</label>
                    <input type="password" id="new_password" class="form-input" placeholder="Min. 6 karakter" required minlength="6">
                </div>
                <button type="submit" class="btn-primary" id="btn-save-pass">
                    <i class="ti ti-check"></i> Simpan Password Baru
                </button>
            </form>
        </div>
    </div>
</div>

<!-- TOAST CONTAINER -->
<div id="toast-container"></div>

<script>
    /* ── Clock ── */
    function updateClock() {
        const now = new Date();
        document.getElementById('live-clock').textContent = now.toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit', second: '2-digit' });
    }
    updateClock();
    setInterval(updateClock, 1000);

    /* ── Sidebar toggle (mobile) ── */
    const sidebar = document.getElementById('sidebar');
    const overlay = document.getElementById('sidebar-overlay');

    document.getElementById('open-sidebar').addEventListener('click', () => {
        sidebar.classList.add('open');
        overlay.classList.add('show');
    });

    function closeSidebar() {
        sidebar.classList.remove('open');
        overlay.classList.remove('show');
    }

    document.getElementById('close-sidebar').addEventListener('click', closeSidebar);
    overlay.addEventListener('click', closeSidebar);

    /* ── Toast system ── */
    function showToast(message, type = 'info', duration = 3500) {
        const icons = { success: 'ti-circle-check', error: 'ti-circle-x', info: 'ti-info-circle', warning: 'ti-alert-triangle' };
        const container = document.getElementById('toast-container');
        const toast = document.createElement('div');
        toast.className = `toast ${type}`;
        toast.innerHTML = `<i class="ti ${icons[type] || icons.info} toast-icon"></i><span>${message}</span>`;
        container.appendChild(toast);
        setTimeout(() => {
            toast.classList.add('removing');
            setTimeout(() => toast.remove(), 280);
        }, duration);
    }

    /* ── Modal password ── */
    function openPasswordModal() {
        document.getElementById('modal-password').classList.add('open');
    }

    function closePasswordModal() {
        document.getElementById('modal-password').classList.remove('open');
        document.getElementById('form-change-password').reset();
    }

    document.getElementById('modal-password').addEventListener('click', function(e) {
        if (e.target === this) closePasswordModal();
    });

    async function submitChangePassword(e) {
        e.preventDefault();
        const btn = document.getElementById('btn-save-pass');
        const original = btn.innerHTML;
        btn.innerHTML = '<i class="ti ti-loader-2" style="animation:spin 1s linear infinite;"></i> Memproses...';
        btn.disabled = true;

        try {
            const res = await apiFetch('../api/auth/change_password.php', {
                method: 'POST',
                body: JSON.stringify({
                    current_password: document.getElementById('current_password').value,
                    new_password: document.getElementById('new_password').value
                })
            });
            if (res.status === 'success') {
                showToast(res.message || 'Password berhasil diubah!', 'success');
                closePasswordModal();
            } else {
                showToast(res.message || 'Gagal mengubah password.', 'error');
            }
        } catch (err) {
            showToast(err.message || 'Terjadi kesalahan.', 'error');
        } finally {
            btn.innerHTML = original;
            btn.disabled = false;
        }
    }

    /* ── Global apiFetch helper ── */
    async function apiFetch(url, options = {}) {
        const defaultHeaders = { 'Content-Type': 'application/json' };
        const res = await fetch(url, { ...options, headers: { ...defaultHeaders, ...(options.headers || {}) } });
        if (!res.ok) throw new Error(`HTTP error! status: ${res.status}`);
        return await res.json();
    }

    /* Loader spin keyframe */
    const styleEl = document.createElement('style');
    styleEl.textContent = `@keyframes spin { to { transform: rotate(360deg); } }`;
    document.head.appendChild(styleEl);
</script>