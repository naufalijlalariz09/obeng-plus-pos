<?php
session_start();
// Jika sudah login, langsung arahkan ke dashboard
if (isset($_SESSION['user_id'])) {
    header("Location: pages/dashboard.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Obeng Plus POS</title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&family=Space+Grotesk:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@latest/tabler-icons.min.css">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: { sans: ['"Plus Jakarta Sans"', 'sans-serif'], heading: ['"Space Grotesk"', 'sans-serif'] },
                    colors: { brand: { pri: '#1E6FD9', dark: '#0C44A0', orange: '#F97316' } }
                }
            }
        }
    </script>
    <style>
        :root {
            --pri: #1E6FD9;
            --dark: #0C44A0;
            --orange: #F97316;
        }

        * { box-sizing: border-box; }

        body {
            background: #f0f4fc;
        }

        /* ---------- LEFT PANEL ---------- */
        .left-panel {
            background: #ffffff;
        }

        /* Floating card shadow */
        .form-card {
            background: #fff;
            border-radius: 28px;
            padding: 48px 44px;
            box-shadow:
                0 0 0 1px rgba(30,111,217,.07),
                0 8px 24px -4px rgba(30,111,217,.10),
                0 32px 64px -16px rgba(30,111,217,.13);
            position: relative;
            overflow: hidden;
        }

        .form-card::before {
            content: '';
            position: absolute;
            top: -60px; right: -60px;
            width: 180px; height: 180px;
            background: radial-gradient(circle, rgba(30,111,217,.08) 0%, transparent 70%);
            border-radius: 50%;
            pointer-events: none;
        }

        /* Right panel logo */
        .rp-logo-wrap {
            width: 140px; height: 140px;
            background: rgba(255,255,255,.92);
            border-radius: 32px;
            display: flex; align-items: center; justify-content: center;
            margin: 0 auto 28px;
            padding: 16px;
            box-shadow:
                0 0 0 1px rgba(255,255,255,.15),
                0 8px 32px rgba(0,0,0,.25),
                0 0 60px rgba(30,111,217,.3);
            position: relative;
            backdrop-filter: blur(8px);
            animation: floatLogo 4s ease-in-out infinite alternate;
        }
        .rp-logo-wrap::before {
            content: '';
            position: absolute; inset: -3px;
            border-radius: 35px;
            background: linear-gradient(135deg, rgba(255,255,255,.25) 0%, rgba(249,115,22,.2) 100%);
            z-index: -1;
        }
        .rp-logo-img {
            width: 100%; height: 100%;
            object-fit: contain;
        }
        @keyframes floatLogo {
            0%   { transform: translateY(0px); box-shadow: 0 8px 32px rgba(0,0,0,.25), 0 0 60px rgba(30,111,217,.3); }
            100% { transform: translateY(-10px); box-shadow: 0 20px 50px rgba(0,0,0,.3), 0 0 80px rgba(30,111,217,.4); }
        }

        /* Input wrapper */
        .input-wrap {
            position: relative;
        }
        .input-wrap input {
            width: 100%;
            padding: 13px 16px 13px 46px;
            background: #f7f9fd;
            border: 1.5px solid #e4eaf5;
            border-radius: 14px;
            font-size: 14px;
            color: #1a2540;
            outline: none;
            transition: border-color .2s, box-shadow .2s, background .2s;
            font-family: inherit;
        }
        .input-wrap input:focus {
            border-color: var(--pri);
            background: #fff;
            box-shadow: 0 0 0 4px rgba(30,111,217,.10);
        }
        .input-wrap input::placeholder { color: #a8b4cc; }
        .input-icon {
            position: absolute; left: 14px; top: 50%; transform: translateY(-50%);
            color: #a8b4cc; font-size: 18px; pointer-events: none;
            transition: color .2s;
        }
        .input-wrap:focus-within .input-icon { color: var(--pri); }
        .toggle-pw {
            position: absolute; right: 14px; top: 50%; transform: translateY(-50%);
            color: #a8b4cc; background: none; border: none; cursor: pointer;
            font-size: 18px; padding: 2px; transition: color .2s;
        }
        .toggle-pw:hover { color: var(--pri); }

        /* Submit button */
        .btn-submit {
            width: 100%;
            padding: 14px;
            background: linear-gradient(135deg, var(--pri) 0%, var(--dark) 100%);
            color: #fff;
            font-weight: 600;
            font-size: 14px;
            border: none;
            border-radius: 14px;
            cursor: pointer;
            letter-spacing: .3px;
            transition: transform .15s, box-shadow .15s;
            box-shadow: 0 6px 20px -4px rgba(30,111,217,.45);
            font-family: inherit;
            position: relative;
            overflow: hidden;
        }
        .btn-submit::before {
            content: '';
            position: absolute; inset: 0;
            background: linear-gradient(135deg, rgba(255,255,255,.15) 0%, transparent 60%);
            opacity: 0;
            transition: opacity .2s;
        }
        .btn-submit:hover { transform: translateY(-1px); box-shadow: 0 10px 28px -4px rgba(30,111,217,.5); }
        .btn-submit:hover::before { opacity: 1; }
        .btn-submit:active { transform: scale(.985); }

        /* Alert */
        .alert-error {
            background: #fff5f5;
            border: 1.5px solid #fecdca;
            border-radius: 14px;
            padding: 12px 16px;
            display: flex; align-items: center; gap: 10px;
            color: #b91c1c; font-size: 13.5px;
        }
        .alert-timeout {
            background: #eff6ff;
            border: 1.5px solid #bfdbfe;
            border-radius: 14px;
            padding: 12px 16px;
            display: flex; align-items: center; gap: 10px;
            color: var(--pri); font-size: 13.5px;
        }

        /* ---------- RIGHT PANEL ---------- */
        .right-panel {
            background: linear-gradient(145deg, #0b2d6b 0%, #0c44a0 45%, #1e6fd9 100%);
            position: relative;
            overflow: hidden;
        }

        /* Animated orbs */
        .orb {
            position: absolute;
            border-radius: 50%;
            filter: blur(70px);
            opacity: .35;
            animation: drift 8s ease-in-out infinite alternate;
        }
        .orb-1 { width: 420px; height: 420px; background: #1e6fd9; top: -120px; right: -120px; animation-delay: 0s; }
        .orb-2 { width: 320px; height: 320px; background: #f97316; bottom: -80px; left: -80px; opacity: .2; animation-delay: -3s; }
        .orb-3 { width: 200px; height: 200px; background: #38bdf8; top: 40%; left: 20%; opacity: .15; animation-delay: -6s; }

        @keyframes drift {
            0%   { transform: translate(0, 0) scale(1); }
            100% { transform: translate(30px, 20px) scale(1.07); }
        }

        /* Dot grid */
        .dot-grid {
            position: absolute; inset: 0;
            background-image: radial-gradient(circle, rgba(255,255,255,.12) 1px, transparent 0);
            background-size: 28px 28px;
        }

        /* Lines decoration */
        .lines-deco {
            position: absolute;
            inset: 0;
            background-image:
                linear-gradient(rgba(255,255,255,.03) 1px, transparent 1px),
                linear-gradient(90deg, rgba(255,255,255,.03) 1px, transparent 1px);
            background-size: 80px 80px;
        }

        /* Feature pills */
        .pill {
            display: inline-flex; align-items: center; gap: 7px;
            background: rgba(255,255,255,.08);
            border: 1px solid rgba(255,255,255,.12);
            border-radius: 999px;
            padding: 8px 16px;
            font-size: 12.5px;
            color: rgba(255,255,255,.8);
            backdrop-filter: blur(6px);
            transition: background .2s;
        }
        .pill:hover { background: rgba(255,255,255,.14); }
        .pill i { font-size: 15px; color: rgba(255,255,255,.9); }

        /* Fade-in animation for the whole card */
        @keyframes fadeUp {
            from { opacity: 0; transform: translateY(24px); }
            to   { opacity: 1; transform: translateY(0); }
        }
        .animate-fadeup { animation: fadeUp .55s ease both; }
        .delay-100 { animation-delay: .10s; }
        .delay-200 { animation-delay: .20s; }
        .delay-300 { animation-delay: .30s; }

        /* Label */
        .field-label {
            font-size: 13px; font-weight: 600; color: #3d5278; margin-bottom: 7px; display: block;
        }
    </style>
</head>
<body class="flex min-h-screen font-sans antialiased">

    <!-- ====== LEFT PANEL ====== -->
    <div class="left-panel w-full lg:w-1/2 flex flex-col justify-center items-center px-6 sm:px-12 py-12 relative z-10">

        <div class="form-card w-full max-w-sm animate-fadeup">

            <!-- Logo -->
            <div class="flex items-center gap-3 mb-9">
                <img src="assets/img/icon-192.png" alt="Obeng Plus Logo" class="h-14 w-auto drop-shadow-md">
            </div>

            <!-- Heading -->
            <div class="mb-7 delay-100 animate-fadeup">
                <h2 class="text-[28px] font-bold text-gray-900 leading-tight mb-1.5" style="font-family:'Space Grotesk',sans-serif">Selamat Datang 👋</h2>
                <p class="text-sm text-gray-400 leading-relaxed">Silakan masuk menggunakan akun Anda untuk mengelola kasir dan jadwal teknisi.</p>
            </div>

            <!-- Alerts -->
            <?php if (isset($_GET['error'])): ?>
                <div class="alert-error mb-5 delay-200 animate-fadeup">
                    <i class="ti ti-alert-circle text-[18px] flex-shrink-0"></i>
                    <span>Username atau password yang Anda masukkan salah.</span>
                </div>
            <?php elseif (isset($_GET['timeout'])): ?>
                <div class="alert-timeout mb-5 delay-200 animate-fadeup">
                    <i class="ti ti-clock text-[18px] flex-shrink-0"></i>
                    <span>Sesi Anda telah berakhir demi keamanan. Silakan login kembali.</span>
                </div>
            <?php endif; ?>

            <!-- Form -->
            <form action="api/auth/login.php" method="POST" class="space-y-4 delay-200 animate-fadeup">
                <div>
                    <label class="field-label">Username</label>
                    <div class="input-wrap">
                        <i class="ti ti-user input-icon"></i>
                        <input type="text" name="username" required autocomplete="off" placeholder="Masukkan username Anda">
                    </div>
                </div>

                <div>
                    <label class="field-label">Password</label>
                    <div class="input-wrap">
                        <i class="ti ti-lock input-icon"></i>
                        <input type="password" name="password" id="password" required placeholder="••••••••">
                        <button type="button" onclick="togglePassword()" class="toggle-pw">
                            <i class="ti ti-eye" id="eye-icon"></i>
                        </button>
                    </div>
                </div>

                <div class="pt-2">
                    <button type="submit" class="btn-submit">
                        Masuk ke Sistem
                    </button>
                </div>
            </form>

            <p class="text-center text-[11.5px] text-gray-300 mt-7">
                &copy; <?php echo date('Y'); ?> Obeng Plus Car Audio. All rights reserved.
            </p>
        </div>
    </div>

    <!-- ====== RIGHT PANEL ====== -->
    <div class="hidden lg:flex w-1/2 right-panel items-center justify-center">
        <div class="dot-grid"></div>
        <div class="lines-deco"></div>
        <div class="orb orb-1"></div>
        <div class="orb orb-2"></div>
        <div class="orb orb-3"></div>

        <div class="relative z-10 text-center px-14 max-w-md">
            <div class="rp-logo-wrap">
                <img src="assets/img/icon-512.png" alt="Obeng Plus" class="rp-logo-img">
            </div>

            <h2 class="text-[32px] font-bold text-white mb-4 leading-tight" style="font-family:'Space Grotesk',sans-serif">
                Sistem POS<br>Terintegrasi
            </h2>
            <p class="text-sm leading-relaxed mb-8" style="color:rgba(255,255,255,.6)">
                Kelola penjualan, pantau stok barang, dan jadwalkan pengerjaan instalasi audio kendaraan dalam satu platform modern yang cepat dan aman.
            </p>

            <!-- Feature pills -->
            <div class="flex flex-wrap justify-center gap-2.5">
                <span class="pill"><i class="ti ti-shopping-cart"></i> Kasir Digital</span>
                <span class="pill"><i class="ti ti-package"></i> Manajemen Stok</span>
                <span class="pill"><i class="ti ti-calendar-event"></i> Jadwal Teknisi</span>
                <span class="pill"><i class="ti ti-chart-bar"></i> Laporan Real-time</span>
            </div>
        </div>
    </div>

    <script>
        function togglePassword() {
            const passInput = document.getElementById('password');
            const eyeIcon = document.getElementById('eye-icon');
            if (passInput.type === 'password') {
                passInput.type = 'text';
                eyeIcon.classList.remove('ti-eye');
                eyeIcon.classList.add('ti-eye-off');
            } else {
                passInput.type = 'password';
                eyeIcon.classList.remove('ti-eye-off');
                eyeIcon.classList.add('ti-eye');
            }
        }
    </script>
</body>
</html>