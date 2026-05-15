/* ============================================================
   kasir.js — Logika lengkap halaman Kasir / POS
   Digunakan oleh: kasir.php
   Bergantung pada: main.js (showToast, apiFetch, formatRp,
                             showConfirm, setButtonLoading)
   ============================================================ */

'use strict';

/* ── State ── */
let products      = [];
let cart          = [];
let discount      = 0;       // persen 0–100
let amountPaid    = 0;       // nominal yang dibayarkan (mode cash)
let isCashMode    = true;

/* ── INIT ── */
document.addEventListener('DOMContentLoaded', () => {
    loadPos();
    bindSearch();
    bindDiscount();
    bindPaymentMethod();
    bindCashInput();
    bindKeyboardShortcuts();
});

/* ══════════════════════════════════════════════════════════
   1. LOAD PRODUK DARI API
══════════════════════════════════════════════════════════ */
async function loadPos() {
    renderSkeletonGrid();
    try {
        const result = await apiFetch('../api/products/get_all.php');
        if (result?.status === 'success') {
            products = result.data;
            renderGrid(products);
            populateCategories(products);
        } else {
            showToast('Gagal memuat produk: ' + (result?.message || 'Unknown error'), 'error');
            renderGridError();
        }
    } catch (e) {
        showToast(e.message || 'Tidak dapat menghubungi server.', 'error');
        renderGridError();
    }
}

/* ── Skeleton saat loading ── */
function renderSkeletonGrid() {
    const grid = document.getElementById('pos-grid');
    if (!grid) return;
    grid.innerHTML = Array(12).fill(0).map(() => `
        <div class="ppc-skeleton">
            <div class="sk-block" style="width:100%;height:80px;border-radius:10px;margin-bottom:10px;"></div>
            <div class="sk-block" style="width:75%;height:12px;margin-bottom:6px;"></div>
            <div class="sk-block" style="width:50%;height:14px;"></div>
        </div>`).join('');
}

function renderGridError() {
    const grid = document.getElementById('pos-grid');
    if (grid) grid.innerHTML = `
        <div style="grid-column:1/-1;text-align:center;padding:40px 20px;color:var(--text-muted);">
            <i class="ti ti-wifi-off" style="font-size:40px;display:block;margin-bottom:12px;"></i>
            <p style="font-size:13px;">Gagal memuat produk. <a href="#" onclick="loadPos();return false;" style="color:var(--accent-blue);">Coba lagi</a></p>
        </div>`;
}

/* ══════════════════════════════════════════════════════════
   2. POPULATE KATEGORI
══════════════════════════════════════════════════════════ */
function populateCategories(items) {
    const sel = document.getElementById('pos-cat');
    if (!sel) return;
    const cats = [...new Set(items.map(p => p.category_name).filter(Boolean))].sort();
    sel.innerHTML = '<option value="">Semua Kategori</option>' +
        cats.map(c => `<option value="${c}">${c}</option>`).join('');
}

/* ══════════════════════════════════════════════════════════
   3. RENDER GRID PRODUK
══════════════════════════════════════════════════════════ */
function renderGrid(items) {
    const grid = document.getElementById('pos-grid');
    if (!grid) return;

    if (!items.length) {
        grid.innerHTML = `
            <div style="grid-column:1/-1;text-align:center;padding:40px 20px;color:var(--text-muted);">
                <i class="ti ti-mood-empty" style="font-size:40px;display:block;margin-bottom:10px;opacity:.4;"></i>
                <p style="font-size:13px;">Produk tidak ditemukan</p>
            </div>`;
        return;
    }

    grid.innerHTML = items.map(p => {
        const isJasa      = p.type === 'jasa';
        const outOfStock  = !isJasa && parseInt(p.stock) <= 0;
        const lowStock    = !isJasa && parseInt(p.stock) > 0 && parseInt(p.stock) <= 5;
        const inCart      = cart.find(x => x.id == p.id);

        const stockLabel = isJasa
            ? `<span class="ppc-stock">Jasa</span>`
            : (outOfStock
                ? `<span class="ppc-stock" style="color:#ef4444;font-weight:700;">Habis</span>`
                : `<span class="ppc-stock ${lowStock ? 'low' : ''}">Stok: ${p.stock}</span>`);

        const badgeHtml = outOfStock
            ? '<span class="ppc-badge">HABIS</span>'
            : (inCart ? `<span class="ppc-badge" style="background:#10b981;">${inCart.qty}×</span>` : '');

        return `
        <div data-id="${p.id}"
             onclick="${outOfStock ? '' : `addToCart(${p.id})`}"
             class="pos-product-card ${outOfStock ? 'out-of-stock' : ''} ${inCart ? 'in-cart' : ''}">
            ${badgeHtml}
            <div class="ppc-img"><i class="ti ${isJasa ? 'ti-tool' : 'ti-package'}"></i></div>
            <p class="ppc-name">${p.name}</p>
            <p class="ppc-price">${formatRp(p.sell_price)}</p>
            ${stockLabel}
        </div>`;
    }).join('');
}

/* ══════════════════════════════════════════════════════════
   4. SEARCH & FILTER
══════════════════════════════════════════════════════════ */
function bindSearch() {
    const search = document.getElementById('pos-search');
    const cat    = document.getElementById('pos-cat');
    if (!search) return;

    let debounceTimer;
    const filter = () => {
        clearTimeout(debounceTimer);
        debounceTimer = setTimeout(() => {
            const term = search.value.toLowerCase();
            const c    = cat?.value || '';
            renderGrid(products.filter(p =>
                (p.name.toLowerCase().includes(term) ||
                 (p.sku || '').toLowerCase().includes(term)) &&
                (!c || p.category_name === c)
            ));
        }, 150);
    };

    search.addEventListener('input', filter);
    cat?.addEventListener('change', filter);
}

/* ══════════════════════════════════════════════════════════
   5. TAMBAH KE KERANJANG
══════════════════════════════════════════════════════════ */
window.addToCart = function(id) {
    const p  = products.find(x => x.id == id);
    if (!p) return;
    const ex = cart.find(x => x.id == id);

    if (ex) {
        if (p.type !== 'jasa' && ex.qty >= parseInt(p.stock)) {
            showToast(`Stok "${p.name}" tidak mencukupi! Maksimal ${p.stock} unit.`, 'warning');
            return;
        }
        ex.qty++;
    } else {
        if (p.type !== 'jasa' && parseInt(p.stock) <= 0) {
            showToast(`Stok "${p.name}" sudah habis!`, 'error');
            return;
        }
        cart.push({ ...p, qty: 1 });
        showToast(`"${p.name}" ditambahkan ke keranjang`, 'success', 1500);
    }

    renderCart();
    updateGridBadges();
    animateCartBadge();
};

/** Update badge "Nx" di kartu produk setelah cart berubah */
function updateGridBadges() {
    products.forEach(p => {
        const card = document.querySelector(`.pos-product-card[data-id="${p.id}"]`);
        if (!card) return;
        const inCart = cart.find(x => x.id == p.id);
        let badge = card.querySelector('.ppc-badge');

        if (inCart && !card.classList.contains('out-of-stock')) {
            if (!badge) {
                badge = document.createElement('span');
                badge.className = 'ppc-badge';
                badge.style.background = '#10b981';
                card.appendChild(badge);
            }
            badge.textContent = inCart.qty + '×';
            card.classList.add('in-cart');
        } else if (badge && !card.classList.contains('out-of-stock')) {
            badge.remove();
            card.classList.remove('in-cart');
        }
    });
}

/* ══════════════════════════════════════════════════════════
   6. UBAH KUANTITAS
══════════════════════════════════════════════════════════ */
window.changeQty = function(id, delta) {
    const item = cart.find(x => x.id == id);
    if (!item) return;

    if (delta > 0 && item.type !== 'jasa' && item.qty >= parseInt(item.stock)) {
        showToast('Stok maksimal tercapai!', 'warning');
        return;
    }

    item.qty += delta;
    if (item.qty <= 0) {
        cart = cart.filter(x => x.id != id);
        showToast('Item dihapus dari keranjang.', 'info', 1500);
    }

    renderCart();
    updateGridBadges();
};

/* ══════════════════════════════════════════════════════════
   7. HAPUS ITEM DARI KERANJANG
══════════════════════════════════════════════════════════ */
window.removeFromCart = function(id) {
    const item = cart.find(x => x.id == id);
    cart = cart.filter(x => x.id != id);
    if (item) showToast(`"${item.name}" dihapus dari keranjang.`, 'info', 1800);
    renderCart();
    updateGridBadges();
};

/* ══════════════════════════════════════════════════════════
   8. DISCOUNT INPUT
══════════════════════════════════════════════════════════ */
function bindDiscount() {
    const inp = document.getElementById('c-discount');
    if (!inp) return;
    inp.addEventListener('input', () => {
        discount = Math.min(100, Math.max(0, parseInt(inp.value) || 0));
        inp.value = discount;
        renderCart();
    });
}

/* ══════════════════════════════════════════════════════════
   9. METODE PEMBAYARAN & CASH INPUT
══════════════════════════════════════════════════════════ */
function bindPaymentMethod() {
    document.querySelectorAll('.pay-btn').forEach(btn => {
        btn.addEventListener('click', () => {
            document.querySelectorAll('.pay-btn').forEach(b => b.classList.remove('active'));
            btn.classList.add('active');
            isCashMode = btn.dataset.method === 'Cash';
            toggleCashSection();
        });
    });
}

function bindCashInput() {
    const inp = document.getElementById('c-paid');
    if (!inp) return;
    inp.addEventListener('input', () => {
        amountPaid = parseInt(inp.value.replace(/\D/g,'')) || 0;
        updateChangeDisplay();
    });
}

function toggleCashSection() {
    const section = document.getElementById('cash-section');
    if (section) section.style.display = isCashMode ? 'block' : 'none';
}

function updateChangeDisplay() {
    const grand   = getGrandTotal();
    const change  = amountPaid - grand;
    const el      = document.getElementById('c-change');
    if (!el) return;
    el.textContent  = change >= 0 ? formatRp(change) : '—';
    el.style.color  = change >= 0 ? '#10b981' : '#ef4444';
}

function getPaymentMethod() {
    return document.querySelector('.pay-btn.active')?.dataset?.method || 'Cash';
}

function getGrandTotal() {
    const subtotal = cart.reduce((s, i) => s + parseInt(i.sell_price) * i.qty, 0);
    const disc     = Math.round(subtotal * discount / 100);
    return subtotal - disc;
}

/* Preset bayar pas / dibulatkan */
window.setQuickPay = function(amount) {
    const inp = document.getElementById('c-paid');
    if (!inp) return;
    const grand    = getGrandTotal();
    const val      = amount === 0 ? grand : amount;
    inp.value      = val;
    amountPaid     = val;
    updateChangeDisplay();
};

/* ══════════════════════════════════════════════════════════
   10. RENDER KERANJANG
══════════════════════════════════════════════════════════ */
function renderCart() {
    const list  = document.getElementById('cart-list');
    const badge = document.getElementById('cart-count-badge');
    if (!list) return;

    if (!cart.length) {
        if (badge) badge.textContent = '0 item';
        const totalEl = document.getElementById('c-total');
        const subEl   = document.getElementById('c-subtotal');
        const discRow = document.getElementById('discount-row');
        if (totalEl) totalEl.textContent = formatRp(0);
        if (subEl)   subEl.textContent   = formatRp(0);
        if (discRow) discRow.style.display = 'none';

        list.innerHTML = `
            <div class="cart-empty">
                <i class="ti ti-shopping-cart"></i>
                <p style="font-weight:600;">Keranjang masih kosong</p>
                <p style="font-size:11px;margin-top:4px;">Pilih produk di panel kiri</p>
            </div>`;
        updateChangeDisplay();
        return;
    }

    let subtotal = 0, totalQty = 0;

    list.innerHTML = cart.map(i => {
        const itemTotal = parseInt(i.sell_price) * i.qty;
        subtotal  += itemTotal;
        totalQty  += i.qty;

        return `
        <div class="cart-item">
            <div class="ci-info">
                <div class="ci-name" title="${i.name}">${i.name}</div>
                <div class="ci-price">${formatRp(i.sell_price)} × ${i.qty} = <strong style="color:#10b981;">${formatRp(itemTotal)}</strong></div>
            </div>
            <div class="ci-controls">
                <button class="ci-btn" onclick="changeQty(${i.id}, -1)" aria-label="Kurang">−</button>
                <span class="ci-qty">${i.qty}</span>
                <button class="ci-btn" onclick="changeQty(${i.id}, 1)" aria-label="Tambah">+</button>
                <button class="ci-remove" onclick="removeFromCart(${i.id})" title="Hapus item" aria-label="Hapus">
                    <i class="ti ti-x" style="font-size:12px;"></i>
                </button>
            </div>
        </div>`;
    }).join('');

    if (badge) badge.textContent = totalQty + ' item';

    const disc       = discount > 0 ? Math.round(subtotal * discount / 100) : 0;
    const grandTotal = subtotal - disc;

    const setEl = (id, val) => { const el = document.getElementById(id); if (el) el.textContent = val; };
    setEl('c-subtotal', formatRp(subtotal));
    setEl('c-total',    formatRp(grandTotal));
    setEl('c-discount-amount', '- ' + formatRp(disc));

    const discRow = document.getElementById('discount-row');
    if (discRow) discRow.style.display = discount > 0 ? 'flex' : 'none';

    // Quick pay buttons
    const grand = grandTotal;
    const qpBtn = document.getElementById('qp-exact');
    if (qpBtn) qpBtn.textContent = formatRp(grand);

    updateChangeDisplay();
}

/* ══════════════════════════════════════════════════════════
   11. ANIMASI BADGE KERANJANG
══════════════════════════════════════════════════════════ */
function animateCartBadge() {
    const badge = document.getElementById('cart-count-badge');
    if (!badge) return;
    badge.style.transform = 'scale(1.3)';
    badge.style.transition = 'transform .15s';
    setTimeout(() => badge.style.transform = '', 200);
}

/* ══════════════════════════════════════════════════════════
   12. PROSES TRANSAKSI
══════════════════════════════════════════════════════════ */
window.processTx = async function() {
    if (!cart.length) {
        showToast('Keranjang masih kosong!', 'warning');
        return;
    }

    const grandTotal = getGrandTotal();

    // Validasi cash
    if (isCashMode && amountPaid < grandTotal) {
        showToast(`Nominal pembayaran (${formatRp(amountPaid)}) kurang dari total (${formatRp(grandTotal)})`, 'warning', 4000);
        document.getElementById('c-paid')?.focus();
        return;
    }

    const btn = document.getElementById('btn-pay');
    setButtonLoading(btn, true, 'Memproses...');

    const subtotal = cart.reduce((s, i) => s + parseInt(i.sell_price) * i.qty, 0);
    const disc     = Math.round(subtotal * discount / 100);

    const payload = {
        customer_name:    (document.getElementById('c-name')?.value.trim()  || 'Umum'),
        customer_phone:   (document.getElementById('c-phone')?.value.trim() || '-'),
        items:            cart.map(i => ({ id: i.id, qty: i.qty, price: parseInt(i.sell_price) })),
        payment_method:   getPaymentMethod(),
        discount_percent: discount,
        discount_amount:  disc,
        grand_total:      grandTotal,
        amount_paid:      isCashMode ? amountPaid : grandTotal,
        change:           isCashMode ? (amountPaid - grandTotal) : 0,
    };

    try {
        const res = await apiFetch('../api/transactions/create.php', {
            method: 'POST',
            body: JSON.stringify(payload)
        });
        setButtonLoading(btn, false);

        if (res?.status === 'success') {
            showToast(`✅ Transaksi sukses! Invoice: ${res.invoice_no}`, 'success', 5000);
            await printStruk({ ...res, ...payload, invoice_no: res.invoice_no });
            resetCart();
            loadPos();
        } else {
            showToast('Transaksi gagal: ' + (res?.message || 'Silakan coba lagi.'), 'error', 5000);
        }
    } catch (e) {
        setButtonLoading(btn, false);
        showToast(e.message || 'Gagal menghubungi server.', 'error', 5000);
    }
};

/* ══════════════════════════════════════════════════════════
   13. RESET KERANJANG
══════════════════════════════════════════════════════════ */
function resetCart() {
    cart       = [];
    discount   = 0;
    amountPaid = 0;

    const setVal = (id, v) => { const el = document.getElementById(id); if (el) el.value = v; };
    setVal('c-discount', 0);
    setVal('c-name',     '');
    setVal('c-phone',    '');
    setVal('c-paid',     '');

    renderCart();
    updateGridBadges();
}

/* ══════════════════════════════════════════════════════════
   14. HAPUS SEMUA KERANJANG
══════════════════════════════════════════════════════════ */
window.clearCart = function() {
    if (!cart.length) return;
    showConfirm(
        'Kosongkan Keranjang',
        'Semua item akan dihapus dari keranjang. Lanjutkan?',
        () => { resetCart(); showToast('Keranjang dikosongkan.', 'info'); },
        'danger',
        'Ya, Kosongkan'
    );
};

/* ══════════════════════════════════════════════════════════
   15. CETAK STRUK
══════════════════════════════════════════════════════════ */
async function printStruk(data) {
    const items     = data.items || cart.map(i => ({ ...i, price: i.sell_price }));
    const subtotal  = items.reduce((s, i) => s + parseInt(i.price || i.sell_price) * parseInt(i.qty), 0);
    const disc      = data.discount_amount ?? Math.round(subtotal * (data.discount_percent || 0) / 100);
    const total     = subtotal - disc;
    const paid      = data.amount_paid || total;
    const change    = data.change ?? (paid - total);
    const now       = new Date().toLocaleString('id-ID', {
        day: '2-digit', month: 'long', year: 'numeric',
        hour: '2-digit', minute: '2-digit'
    });

    const w = window.open('', '_blank', 'width=400,height=650');
    if (!w) { showToast('Popup diblokir browser. Ijinkan popup untuk mencetak struk.', 'warning', 5000); return; }

    w.document.write(`<!DOCTYPE html>
<html><head><title>Struk ${data.invoice_no}</title>
<style>
    * { margin:0;padding:0;box-sizing:border-box; }
    body { font-family:'Courier New',monospace; font-size:12px; padding:16px; color:#000; max-width:300px; margin:auto; }
    h2  { text-align:center; font-size:16px; font-weight:800; margin-bottom:3px; }
    .sub{ text-align:center; font-size:10px; color:#555; margin-bottom:2px; }
    .sep{ border-top:1px dashed #000; margin:8px 0; }
    table { width:100%; }
    td   { padding:2px 0; vertical-align:top; font-size:11px; }
    .r   { text-align:right; }
    .b   { font-weight:700; }
    .grand td { font-size:14px; font-weight:800; padding-top:6px; }
    .footer { text-align:center; margin-top:12px; font-size:10px; color:#555; line-height:1.7; }
    @media print { body { margin:0; } }
</style></head><body>
<h2>⚡ Obeng Plus</h2>
<div class="sub">Car Audio Specialist</div>
<div class="sub">Semarang · IG: @obengplus.caraudio</div>
<div class="sep"></div>
<table>
    <tr><td>Invoice</td><td class="r b" style="color:#1d6ae0;">${data.invoice_no}</td></tr>
    <tr><td>Tanggal</td><td class="r">${now}</td></tr>
    <tr><td>Customer</td><td class="r">${data.customer_name || 'Umum'}</td></tr>
    ${data.customer_phone && data.customer_phone !== '-' ? `<tr><td>Telepon</td><td class="r">${data.customer_phone}</td></tr>` : ''}
    <tr><td>Metode</td><td class="r">${data.payment_method || 'Cash'}</td></tr>
</table>
<div class="sep"></div>
<table>
    ${items.map(i => `
    <tr><td colspan="2">${i.name}</td></tr>
    <tr>
        <td style="padding-left:8px;">${i.qty} × Rp ${parseInt(i.price || i.sell_price).toLocaleString('id-ID')}</td>
        <td class="r">Rp ${(parseInt(i.price || i.sell_price) * parseInt(i.qty)).toLocaleString('id-ID')}</td>
    </tr>`).join('')}
</table>
<div class="sep"></div>
<table>
    <tr><td>Subtotal</td><td class="r">Rp ${subtotal.toLocaleString('id-ID')}</td></tr>
    ${disc > 0 ? `<tr><td>Diskon (${data.discount_percent}%)</td><td class="r">- Rp ${disc.toLocaleString('id-ID')}</td></tr>` : ''}
    <tr class="grand"><td class="b">TOTAL BAYAR</td><td class="r b">Rp ${total.toLocaleString('id-ID')}</td></tr>
    <tr><td>Dibayar</td><td class="r">Rp ${paid.toLocaleString('id-ID')}</td></tr>
    ${change > 0 ? `<tr><td>Kembalian</td><td class="r b">Rp ${change.toLocaleString('id-ID')}</td></tr>` : ''}
</table>
<div class="sep"></div>
<div class="footer">
    Terima kasih atas kepercayaan Anda!<br>
    Garansi pemasangan 3 bulan<br>
    <strong>Simpan struk ini sebagai bukti pembelian</strong>
</div>
<script>window.onload = () => { window.print(); setTimeout(() => window.close(), 800); }<\/script>
</body></html>`);
    w.document.close();
}

/* ══════════════════════════════════════════════════════════
   16. KEYBOARD SHORTCUTS
   Digunakan: kasir.php
══════════════════════════════════════════════════════════ */
function bindKeyboardShortcuts() {
    document.addEventListener('keydown', e => {
        // F2 → fokus pencarian produk
        if (e.key === 'F2') {
            e.preventDefault();
            document.getElementById('pos-search')?.focus();
        }
        // F5 → reload produk
        if (e.key === 'F5' && e.shiftKey) {
            e.preventDefault();
            loadPos();
            showToast('Daftar produk diperbarui.', 'info', 1500);
        }
        // Ctrl/Cmd + Enter → proses transaksi
        if ((e.ctrlKey || e.metaKey) && e.key === 'Enter') {
            e.preventDefault();
            processTx();
        }
        // Escape → kosongkan keranjang
        if (e.key === 'Escape' && document.activeElement.tagName !== 'INPUT') {
            clearCart();
        }
    });
}