</div><!-- /#main-scroll -->
</div><!-- /#app-shell -->

<!-- ═══════════ SIDEBAR OVERLAY (mobile) ═══════════ -->
<div id="sidebar-overlay"></div>

<!-- ═══════════ SCRIPT UTAMA ═══════════ -->
<script src="../assets/js/main.js"></script>

<script>
/* ════════════════════════════════════════════
   PWA – Service Worker Registration
════════════════════════════════════════════ */
if ('serviceWorker' in navigator) {
    window.addEventListener('load', () => {
        navigator.serviceWorker.register('/obeng-plus-pos/sw.js')
            .catch(err => console.warn('SW gagal didaftarkan:', err));
    });
}

/* ════════════════════════════════════════════
   LIVE CLOCK
   (Fungsi juga didefinisikan di header.php untuk render awal
    yang sama persis — tidak ada duplikasi karena header hanya
    menyediakan updateClock() & setInterval, footer tidak perlu
    mendefinisikannya ulang. Bagian ini dikosongkan dan cukup
    mengandalkan yang ada di header.php.)
════════════════════════════════════════════ */
// updateClock() & setInterval sudah ada di header.php — tidak diulang.

/* ════════════════════════════════════════════
   DARK MODE
   Baca preferensi dari localStorage, lalu terapkan tanpa flicker.
   Tombol #theme-toggle bisa diletakkan di header.php (topbar).
════════════════════════════════════════════ */
(function initTheme() {
    const html        = document.documentElement;
    const stored      = localStorage.getItem('theme');
    const prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
    const isDark      = stored === 'dark' || (!stored && prefersDark);

    function applyTheme(dark) {
        html.classList.toggle('dark', dark);
        const moon = document.getElementById('icon-moon');
        const sun  = document.getElementById('icon-sun');
        if (moon) moon.classList.toggle('hidden', dark);
        if (sun)  sun.classList.toggle('hidden', !dark);
        localStorage.setItem('theme', dark ? 'dark' : 'light');
    }

    applyTheme(isDark);

    const toggleBtn = document.getElementById('theme-toggle');
    if (toggleBtn) {
        toggleBtn.addEventListener('click', () => {
            applyTheme(!html.classList.contains('dark'));
        });
    }
})();

/* ════════════════════════════════════════════
   MOBILE SIDEBAR
   Sinkron dengan logika sidebar di header.php.
════════════════════════════════════════════ */
(function initSidebar() {
    const sidebar  = document.getElementById('sidebar');
    const overlay  = document.getElementById('sidebar-overlay');
    const openBtn  = document.getElementById('open-sidebar');
    const closeBtn = document.getElementById('close-sidebar');

    if (!sidebar || !overlay) return;

    function openSidebar()  { sidebar.classList.add('open');    overlay.classList.add('show'); }
    function closeSidebar() { sidebar.classList.remove('open'); overlay.classList.remove('show'); }

    if (openBtn)  openBtn.addEventListener('click', openSidebar);
    if (closeBtn) closeBtn.addEventListener('click', closeSidebar);
    overlay.addEventListener('click', closeSidebar);

    // Tutup sidebar otomatis saat nav-link diklik (mobile UX)
    sidebar.querySelectorAll('.nav-link').forEach(link => {
        link.addEventListener('click', closeSidebar);
    });
})();

/* ════════════════════════════════════════════
   SESSION TIMEOUT WARNING
   Tampilkan peringatan SweetAlert2 30 detik sebelum sesi habis,
   lalu redirect ke login jika tidak ada respons.
════════════════════════════════════════════ */
(function initSessionTimeout() {
    const TIMEOUT_MS = <?php echo SESSION_TIMEOUT; ?> * 1000;
    const WARN_BEFORE = 30 * 1000; // 30 detik sebelum timeout

    let warnTimer, logoutTimer;

    function resetTimers() {
        clearTimeout(warnTimer);
        clearTimeout(logoutTimer);

        warnTimer = setTimeout(() => {
            Swal.fire({
                icon: 'warning',
                title: 'Sesi Hampir Habis',
                text: 'Sesi Anda akan berakhir dalam 30 detik karena tidak aktif.',
                confirmButtonText: 'Tetap Login',
                confirmButtonColor: '#1d6ae0',
                showCancelButton: true,
                cancelButtonText: 'Logout Sekarang',
                cancelButtonColor: '#374151',
                timer: WARN_BEFORE,
                timerProgressBar: true,
                background: '#111827',
                color: '#f0f2f8',
            }).then(result => {
                if (result.isConfirmed) {
                    // Ping server agar LAST_ACTIVITY ter-refresh
                    fetch('../api/auth/ping.php', { credentials: 'same-origin' }).catch(() => {});
                    resetTimers();
                } else {
                    window.location.href = '../api/auth/logout.php';
                }
            });
        }, TIMEOUT_MS - WARN_BEFORE);

        logoutTimer = setTimeout(() => {
            window.location.href = '../login.php?timeout=1';
        }, TIMEOUT_MS);
    }

    // Reset timer setiap ada aktivitas nyata dari pengguna
    ['click', 'keydown', 'mousemove', 'touchstart', 'scroll'].forEach(evt => {
        document.addEventListener(evt, resetTimers, { passive: true });
    });

    resetTimers();
})();

/* ════════════════════════════════════════════
   KEYBOARD SHORTCUT: ⌘K / Ctrl+K → fokus search
════════════════════════════════════════════ */
document.addEventListener('keydown', e => {
    if ((e.metaKey || e.ctrlKey) && e.key === 'k') {
        e.preventDefault();
        const searchInput = document.querySelector('.tb-search input');
        if (searchInput) searchInput.focus();
    }
});

/* ════════════════════════════════════════════
   GLOBAL CONFIRM DELETE (SweetAlert2)
   Penggunaan: <button onclick="confirmDelete('url/hapus.php?id=5')">
════════════════════════════════════════════ */
function confirmDelete(url, label = 'data ini') {
    Swal.fire({
        icon: 'warning',
        title: 'Konfirmasi Hapus',
        html: `Yakin ingin menghapus <strong>${label}</strong>? Tindakan ini tidak bisa dibatalkan.`,
        confirmButtonText: 'Ya, Hapus!',
        confirmButtonColor: '#ef4444',
        showCancelButton: true,
        cancelButtonText: 'Batal',
        cancelButtonColor: '#374151',
        background: '#111827',
        color: '#f0f2f8',
    }).then(result => {
        if (result.isConfirmed) window.location.href = url;
    });
}

/* ════════════════════════════════════════════
   GLOBAL TOAST (sudah ada di header.php,
   alias tambahan agar bisa dipanggil dari
   script eksternal dengan nama berbeda)
════════════════════════════════════════════ */
function notify(message, type = 'info', duration = 3500) {
    if (typeof showToast === 'function') {
        showToast(message, type, duration);
    } else {
        console.warn('showToast() belum tersedia. Pastikan header.php sudah di-include.');
    }
}

/* ════════════════════════════════════════════
   ACTIVE LINK HIGHLIGHT (fallback)
   Jika PHP tidak bisa set class 'active', JS ini sebagai backup.
════════════════════════════════════════════ */
(function highlightActiveNav() {
    const currentPath = window.location.pathname.split('/').pop().replace('.php', '');
    document.querySelectorAll('.nav-link').forEach(link => {
        const href = link.getAttribute('href')?.replace('.php', '') ?? '';
        if (href === currentPath || href === currentPath + '.php') {
            link.classList.add('active');
        }
    });
})();
</script>

</body>
</html>