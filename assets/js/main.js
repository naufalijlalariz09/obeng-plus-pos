/* ============================================================
   main.js — Global utilities untuk Obeng Plus POS
   Digunakan oleh: SEMUA halaman (dashboard, kasir, produk,
   jasa, transaksi, laporan, laba_rugi, pengguna)
   ============================================================ */

'use strict';

/* ══════════════════════════════════════════════════════════
   1. LIVE CLOCK
   Digunakan: header.php (semua halaman)
══════════════════════════════════════════════════════════ */
function updateClock() {
    const el = document.getElementById('live-clock');
    if (!el) return;
    el.textContent = new Date().toLocaleTimeString('id-ID', {
        hour: '2-digit', minute: '2-digit', second: '2-digit'
    });
}
setInterval(updateClock, 1000);
updateClock();

/* ══════════════════════════════════════════════════════════
   2. TOAST NOTIFICATION SYSTEM
   Digunakan: semua halaman via showToast()
══════════════════════════════════════════════════════════ */
(function initToast() {
    if (document.getElementById('toast-container')) return; // sudah ada di header.php

    const style = document.createElement('style');
    style.textContent = `
        #toast-container {
            position: fixed; bottom: 24px; right: 24px;
            display: flex; flex-direction: column-reverse; gap: 10px;
            z-index: 99999; pointer-events: none;
        }
        .toast {
            display: flex; align-items: center; gap: 10px;
            padding: 12px 16px; border-radius: 12px;
            font-family: 'Plus Jakarta Sans', sans-serif;
            font-size: 13px; font-weight: 500;
            color: #fff; min-width: 260px; max-width: 380px;
            pointer-events: all; cursor: pointer;
            animation: toastIn .28s cubic-bezier(.4,0,.2,1) forwards;
            box-shadow: 0 8px 24px rgba(0,0,0,.35);
            border: 1px solid rgba(255,255,255,0.08);
            backdrop-filter: blur(8px);
        }
        .toast.hide { animation: toastOut .28s cubic-bezier(.4,0,.2,1) forwards; }
        .toast-success { background: rgba(22,163,74,.92); }
        .toast-error   { background: rgba(220,38,38,.92); }
        .toast-warning { background: rgba(234,88,12,.92); }
        .toast-info    { background: rgba(29,106,224,.92); }
        .toast i { font-size: 18px; flex-shrink: 0; }
        .toast-progress {
            position: absolute; bottom: 0; left: 0;
            height: 3px; border-radius: 0 0 12px 12px;
            background: rgba(255,255,255,0.4);
            animation: toastProgress linear forwards;
        }
        @keyframes toastIn  { from{opacity:0;transform:translateX(16px)} to{opacity:1;transform:translateX(0)} }
        @keyframes toastOut { from{opacity:1;transform:translateX(0)} to{opacity:0;transform:translateX(16px)} }
        @keyframes toastProgress { from{width:100%} to{width:0%} }
    `;
    document.head.appendChild(style);

    const container = document.createElement('div');
    container.id = 'toast-container';
    document.body.appendChild(container);
})();

/**
 * Tampilkan toast notification
 * @param {string} message
 * @param {'success'|'error'|'warning'|'info'} type
 * @param {number} duration ms
 */
window.showToast = function(message, type = 'info', duration = 3200) {
    const icons = {
        success: 'ti-circle-check',
        error:   'ti-circle-x',
        warning: 'ti-alert-triangle',
        info:    'ti-info-circle'
    };
    const container = document.getElementById('toast-container');
    if (!container) return;

    const toast = document.createElement('div');
    toast.className = `toast toast-${type}`;
    toast.style.position = 'relative';
    toast.style.overflow = 'hidden';
    toast.innerHTML = `
        <i class="ti ${icons[type] || 'ti-info-circle'}"></i>
        <span style="flex:1;">${message}</span>
        <i class="ti ti-x" style="font-size:13px;opacity:0.6;flex-shrink:0;" onclick="this.closest('.toast').remove()"></i>
        <div class="toast-progress" style="animation-duration:${duration}ms;"></div>
    `;
    container.appendChild(toast);
    setTimeout(() => dismissToast(toast), duration);
};

function dismissToast(toast) {
    if (!toast || !toast.isConnected) return;
    toast.classList.add('hide');
    toast.addEventListener('animationend', () => toast.remove(), { once: true });
}

/* ══════════════════════════════════════════════════════════
   3. CONFIRM DIALOG (SweetAlert2)
   Digunakan: kasir (clearCart), produk (delete),
              jasa (delete), pengguna (reset), transaksi
══════════════════════════════════════════════════════════ */
/**
 * Dialog konfirmasi berbasis SweetAlert2
 * @param {string} title
 * @param {string} message  - bisa mengandung HTML
 * @param {function} onConfirm
 * @param {'danger'|'info'|'warning'} type
 * @param {string} confirmText
 */
window.showConfirm = function(title, message, onConfirm, type = 'danger', confirmText = 'Ya, Lanjutkan') {
    const iconMap  = { danger: 'warning', info: 'question', warning: 'warning' };
    const colorMap = { danger: '#ef4444', info: '#1d6ae0', warning: '#f97316' };

    Swal.fire({
        title,
        html: message,
        icon: iconMap[type] || 'warning',
        showCancelButton: true,
        confirmButtonText: confirmText,
        cancelButtonText: 'Batal',
        confirmButtonColor: colorMap[type] || '#ef4444',
        cancelButtonColor: '#374151',
        background: '#111827',
        color: '#f0f2f8',
        customClass: { popup: 'swal-dark' },
    }).then(result => {
        if (result.isConfirmed) onConfirm();
    });
};

/* ══════════════════════════════════════════════════════════
   4. FORMAT UTILITIES
   Digunakan: semua halaman
══════════════════════════════════════════════════════════ */
/** Format angka ke format Rupiah: Rp 1.250.000 */
window.formatRp = (num) =>
    new Intl.NumberFormat('id-ID', {
        style: 'currency',
        currency: 'IDR',
        minimumFractionDigits: 0,
        maximumFractionDigits: 0
    }).format(num || 0);

/** Format angka singkat: 1.250.000 → 1,25jt | 250.000 → 250rb */
window.formatRpShort = (num) => {
    if (num >= 1_000_000_000) return 'Rp ' + (num / 1_000_000_000).toFixed(1).replace('.0','') + 'M';
    if (num >= 1_000_000)     return 'Rp ' + (num / 1_000_000).toFixed(1).replace('.0','') + 'jt';
    if (num >= 1_000)         return 'Rp ' + (num / 1_000).toFixed(0) + 'rb';
    return 'Rp ' + num;
};

/** Format datetime ke lokal Indonesia: 08 Jan 2025 14:30 */
window.formatDate = (dateStr, withTime = false) => {
    if (!dateStr) return '—';
    const opts = { day: '2-digit', month: 'short', year: 'numeric' };
    if (withTime) { opts.hour = '2-digit'; opts.minute = '2-digit'; }
    return new Date(dateStr).toLocaleDateString('id-ID', opts);
};

/** Format hanya waktu: 14:30 */
window.formatTime = (dateStr) => {
    if (!dateStr) return '—';
    return new Date(dateStr).toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' });
};

/* ══════════════════════════════════════════════════════════
   5. LOADING BUTTON HELPER
   Digunakan: kasir (btn-pay), produk (btn-save),
              jasa (modal-btn), pengguna (btn-save)
══════════════════════════════════════════════════════════ */
/**
 * Toggle state loading pada button
 * @param {HTMLButtonElement} btn
 * @param {boolean} isLoading
 * @param {string} [loadingText] - teks saat loading
 */
window.setButtonLoading = function(btn, isLoading, loadingText = 'Memproses...') {
    if (!btn) return;
    if (isLoading) {
        btn._originalHTML    = btn.innerHTML;
        btn._originalDisabled = btn.disabled;
        btn.innerHTML = `<i class="ti ti-loader-2" style="animation:spin .8s linear infinite;font-size:15px;"></i> ${loadingText}`;
        btn.disabled  = true;
        btn.style.opacity = '.65';
        btn.style.cursor  = 'not-allowed';
    } else {
        btn.innerHTML = btn._originalHTML   || btn.innerHTML;
        btn.disabled  = btn._originalDisabled ?? false;
        btn.style.opacity = '';
        btn.style.cursor  = '';
    }
};

// Keyframe spin (diperlukan setButtonLoading)
if (!document.querySelector('#spin-style')) {
    const s = document.createElement('style');
    s.id = 'spin-style';
    s.textContent = `@keyframes spin { to { transform: rotate(360deg); } }`;
    document.head.appendChild(s);
}

/* ══════════════════════════════════════════════════════════
   6. API HELPER
   Digunakan: semua halaman
══════════════════════════════════════════════════════════ */
/**
 * Fetch JSON dari API internal dengan CSRF token otomatis
 * @param {string} url
 * @param {RequestInit} options
 * @returns {Promise<object>}
 */
window.apiFetch = async function(url, options = {}) {
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content || '';
    const headers = {
        'Content-Type': 'application/json',
        'X-CSRF-Token': csrfToken,
        ...(options.headers || {})
    };

    let res;
    try {
        res = await fetch(url, { credentials: 'same-origin', ...options, headers });
    } catch (networkErr) {
        throw new Error('Tidak dapat menghubungi server. Periksa koneksi internet Anda.');
    }

    // Handle non-2xx HTTP
    if (res.status === 401) { window.location.href = '../login.php'; return; }
    if (res.status === 419) { showToast('Sesi keamanan kedaluwarsa. Muat ulang halaman.', 'warning', 6000); return; }

    const text = await res.text();
    try {
        return JSON.parse(text);
    } catch {
        console.error('[apiFetch] Non-JSON response from', url, '\nResponse:', text.slice(0, 400));
        throw new Error('Respons server tidak valid. Buka console (F12) untuk detail.');
    }
};

/* ══════════════════════════════════════════════════════════
   7. COUNTER ANIMATION
   Digunakan: dashboard (stat cards), laporan, laba_rugi
══════════════════════════════════════════════════════════ */
/**
 * Animasikan nilai numerik dari 0 ke target
 * @param {string|HTMLElement} target  - ID elemen atau elemen langsung
 * @param {number} endValue
 * @param {boolean} isCurrency
 * @param {number} duration ms
 */
window.animateCounter = function(target, endValue, isCurrency = false, duration = 900) {
    const el = typeof target === 'string' ? document.getElementById(target) : target;
    if (!el || !endValue) {
        if (el) el.textContent = isCurrency ? formatRp(0) : '0';
        return;
    }
    const start     = performance.now();
    const formatter = isCurrency ? formatRp : (n) => Math.round(n).toLocaleString('id-ID');

    function step(now) {
        const progress = Math.min((now - start) / duration, 1);
        const eased    = 1 - Math.pow(1 - progress, 3); // ease-out cubic
        el.textContent = formatter(Math.round(eased * endValue));
        if (progress < 1) requestAnimationFrame(step);
    }
    requestAnimationFrame(step);
};

/* ══════════════════════════════════════════════════════════
   8. LOW STOCK BADGE
   Digunakan: semua halaman (topbar notifikasi)
══════════════════════════════════════════════════════════ */
window.checkLowStockBadge = async function() {
    try {
        const res = await apiFetch('../api/dashboard/stats.php');
        if (res?.status === 'success' && res.data?.low_stock > 0) {
            const dot = document.querySelector('.notif-dot');
            if (dot) {
                dot.textContent = res.data.low_stock;
                dot.title = `${res.data.low_stock} produk stok menipis`;
            }
        }
    } catch { /* silent — badge tidak kritis */ }
};

/* ══════════════════════════════════════════════════════════
   9. TOPBAR SEARCH — quick navigation (⌘K / Ctrl+K)
   Digunakan: semua halaman (topbar)
══════════════════════════════════════════════════════════ */
(function initTopbarSearch() {
    document.addEventListener('DOMContentLoaded', () => {
        const input = document.querySelector('.tb-search input');
        if (!input) return;

        // Index page untuk navigasi cepat
        const pageIndex = [
            { keywords: ['dashboard','beranda'],    href: 'dashboard.php',  label: 'Dashboard' },
            { keywords: ['kasir','pos','jual'],     href: 'kasir.php',      label: 'Kasir / POS' },
            { keywords: ['produk','barang','stok'], href: 'produk.php',     label: 'Master Produk' },
            { keywords: ['jasa','servis','pasang'], href: 'jasa.php',       label: 'Manajemen Jasa' },
            { keywords: ['transaksi','histori'],    href: 'transaksi.php',  label: 'Histori Transaksi' },
            { keywords: ['laporan','penjualan'],    href: 'laporan.php',    label: 'Laporan Penjualan' },
            { keywords: ['laba','rugi','profit'],   href: 'laba_rugi.php',  label: 'Laporan Laba/Rugi' },
            { keywords: ['user','pengguna','akun'], href: 'pengguna.php',   label: 'Manajemen User' },
        ];

        let dropdown = null;

        input.addEventListener('input', () => {
            const q = input.value.toLowerCase().trim();
            if (dropdown) { dropdown.remove(); dropdown = null; }
            if (!q) return;

            const matches = pageIndex.filter(p => p.keywords.some(k => k.includes(q)));
            if (!matches.length) return;

            dropdown = document.createElement('div');
            dropdown.style.cssText = `
                position:absolute; top:calc(100% + 6px); left:0; right:0;
                background:#0d1422; border:1px solid #1e2a45; border-radius:12px;
                overflow:hidden; z-index:9999; box-shadow:0 12px 40px rgba(0,0,0,.5);`;
            matches.slice(0,5).forEach(p => {
                const item = document.createElement('a');
                item.href  = p.href;
                item.style.cssText = 'display:flex;align-items:center;gap:10px;padding:10px 14px;color:#c9d1e8;font-size:13px;text-decoration:none;transition:background .12s;';
                item.innerHTML = `<i class="ti ti-arrow-right" style="font-size:12px;color:#3d4f73;"></i>${p.label}`;
                item.addEventListener('mouseenter', () => item.style.background = 'rgba(29,106,224,.08)');
                item.addEventListener('mouseleave', () => item.style.background = '');
                dropdown.appendChild(item);
            });
            input.closest('.tb-search').style.position = 'relative';
            input.closest('.tb-search').appendChild(dropdown);
        });

        document.addEventListener('click', e => {
            if (dropdown && !e.target.closest('.tb-search')) {
                dropdown.remove(); dropdown = null; input.value = '';
            }
        });

        input.addEventListener('keydown', e => {
            if (e.key === 'Escape') { input.value = ''; if (dropdown) { dropdown.remove(); dropdown = null; } }
        });
    });
})();

/* ══════════════════════════════════════════════════════════
   10. INISIALISASI DI DOMContentLoaded
══════════════════════════════════════════════════════════ */
document.addEventListener('DOMContentLoaded', () => {
    // Badge notifikasi stok rendah
    checkLowStockBadge();

    // Tooltip sederhana via title (tambahkan class .has-tooltip)
    document.querySelectorAll('[title]').forEach(el => {
        el.setAttribute('aria-label', el.title);
    });
});