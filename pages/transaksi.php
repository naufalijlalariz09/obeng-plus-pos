<?php
// pages/transaksi.php
require_once '../includes/header.php';
?>

<style>
.page-header { display:flex; justify-content:space-between; align-items:flex-start; margin-bottom:16px; flex-wrap:wrap; gap:12px; }
.page-title { font-family:'Space Grotesk',sans-serif; font-size:20px; font-weight:700; color:#fff; display:flex; align-items:center; gap:10px; }
.page-title i { color:var(--accent-blue); }

/* Toolbar */
.tx-toolbar { display:flex; align-items:center; gap:8px; flex-wrap:wrap; }
.search-wrap { position:relative; }
.search-wrap i { position:absolute; left:12px; top:50%; transform:translateY(-50%); color:var(--text-muted); font-size:15px; pointer-events:none; }
.tx-search { background:var(--bg-card); border:1px solid var(--border); color:var(--text-pri); font-family:'Plus Jakarta Sans',sans-serif; font-size:13px; padding:9px 14px 9px 38px; border-radius:12px; outline:none; width:220px; transition:border-color 0.15s; }
.tx-search::placeholder { color:var(--text-muted); }
.tx-search:focus { border-color:rgba(29,106,224,0.4); }

.filter-btn { background:var(--bg-card); border:1px solid var(--border); color:var(--text-sec); font-family:'Plus Jakarta Sans',sans-serif; font-size:12px; font-weight:600; padding:9px 14px; border-radius:12px; cursor:pointer; display:flex; align-items:center; gap:6px; transition:all 0.15s; }
.filter-btn:hover { border-color:var(--border-mid); color:#fff; }
.filter-btn.active { border-color:rgba(29,106,224,0.4); color:var(--accent-blue); background:rgba(29,106,224,0.08); }

.refresh-btn { background:linear-gradient(135deg,#1d6ae0,#7c3aed); border:none; color:#fff; font-family:'Plus Jakarta Sans',sans-serif; font-size:13px; font-weight:700; padding:9px 16px; border-radius:12px; cursor:pointer; display:flex; align-items:center; gap:6px; transition:opacity 0.15s; }
.refresh-btn:hover { opacity:0.9; }

/* Stats bar */
.stats-bar { display:flex; gap:10px; margin-bottom:14px; flex-wrap:wrap; }
.stat-pill { background:var(--bg-card); border:1px solid var(--border); border-radius:10px; padding:8px 14px; display:flex; align-items:center; gap:8px; }
.sp-dot { width:8px; height:8px; border-radius:50%; flex-shrink:0; }
.sp-dot.blue { background:#60a5fa; box-shadow:0 0 6px rgba(96,165,250,0.5); }
.sp-dot.green { background:#10b981; box-shadow:0 0 6px rgba(16,185,129,0.5); }
.sp-dot.orange { background:#f97316; box-shadow:0 0 6px rgba(249,115,22,0.4); }
.sp-label { color:var(--text-muted); font-size:12px; }
.sp-val { font-family:'Space Grotesk',sans-serif; font-size:13px; font-weight:700; color:#fff; }

/* Table */
.data-card { background:var(--bg-card); border:1px solid var(--border); border-radius:20px; overflow:hidden; }
.tx-table { width:100%; border-collapse:collapse; font-size:13px; min-width:700px; }
.tx-table thead tr { background:rgba(0,0,0,0.2); }
.tx-table thead th { padding:12px 18px; text-align:left; font-size:10px; font-weight:700; letter-spacing:1.2px; text-transform:uppercase; color:var(--text-muted); white-space:nowrap; cursor:pointer; user-select:none; transition:color 0.15s; }
.tx-table thead th:hover { color:var(--accent-blue); }
.tx-table tbody tr { border-top:1px solid rgba(255,255,255,0.03); transition:background 0.12s; cursor:pointer; }
.tx-table tbody tr:hover { background:rgba(255,255,255,0.03); }
.tx-table tbody td { padding:14px 18px; vertical-align:middle; }

.td-invoice { font-family:'Space Grotesk',sans-serif; font-weight:700; color:var(--accent-blue); }
.td-customer { color:var(--text-pri); font-weight:500; }
.td-phone { font-size:11px; color:var(--text-muted); display:block; margin-top:2px; }
.td-total { font-family:'Space Grotesk',sans-serif; font-weight:700; color:#10b981; }
.td-time { font-size:11px; color:var(--text-muted); }
.td-time-detail { font-size:10px; color:#2d3a55; }

.method-badge { display:inline-flex; align-items:center; gap:4px; font-size:11px; font-weight:600; padding:3px 10px; border-radius:20px; }
.method-tunai { background:rgba(16,185,129,0.1); color:#10b981; border:1px solid rgba(16,185,129,0.2); }
.method-transfer { background:rgba(96,165,250,0.1); color:#60a5fa; border:1px solid rgba(96,165,250,0.2); }
.method-qris { background:rgba(167,139,250,0.1); color:#a78bfa; border:1px solid rgba(167,139,250,0.2); }

.action-btns { display:flex; align-items:center; justify-content:center; gap:6px; }
.icon-btn { background:transparent; border:1px solid var(--border); border-radius:8px; color:var(--text-muted); width:30px; height:30px; display:flex; align-items:center; justify-content:center; cursor:pointer; font-size:14px; transition:all 0.15s; }
.icon-btn:hover { border-color:var(--accent-blue); color:var(--accent-blue); background:rgba(96,165,250,0.06); }
.icon-btn.print:hover { border-color:#a78bfa; color:#a78bfa; background:rgba(167,139,250,0.06); }

.table-scroll { overflow-x:auto; }

/* Footer / pagination */
.table-footer { display:flex; justify-content:space-between; align-items:center; padding:12px 18px; border-top:1px solid var(--border); background:rgba(0,0,0,0.15); flex-wrap:wrap; gap:8px; }
.footer-info { font-size:12px; color:var(--text-muted); }
.footer-info span { color:var(--text-sec); font-weight:600; margin:0 3px; }
.pagination { display:flex; align-items:center; gap:4px; }
.page-btn { background:transparent; border:1px solid var(--border); color:var(--text-muted); font-family:'Plus Jakarta Sans',sans-serif; font-size:12px; font-weight:600; padding:5px 10px; border-radius:7px; cursor:pointer; transition:all 0.15s; min-width:30px; text-align:center; }
.page-btn:hover { border-color:var(--border-mid); color:var(--text-pri); }
.page-btn.active { background:rgba(29,106,224,0.15); border-color:rgba(29,106,224,0.4); color:var(--accent-blue); }

.sk-cell { height:13px; background:linear-gradient(90deg,#1e2a45 25%,#2d3a55 50%,#1e2a45 75%); background-size:200% 100%; animation:shimmer 1.4s infinite; border-radius:6px; display:inline-block; }
@keyframes shimmer { 0%{background-position:200% 0} 100%{background-position:-200% 0} }

.no-data { padding:60px 20px; text-align:center; color:var(--text-muted); }
.no-data i { font-size:44px; display:block; margin-bottom:14px; }

/* Nota modal */
.nota-overlay { position:fixed; inset:0; background:rgba(0,0,0,0.75); z-index:999; display:none; align-items:center; justify-content:center; backdrop-filter:blur(4px); padding:16px; }
.nota-overlay.open { display:flex; }
.nota-modal { background:var(--bg-card); border:1px solid var(--border); border-radius:24px; padding:24px; width:380px; max-width:100%; max-height:85vh; overflow-y:auto; animation:modalIn 0.2s ease; }
@keyframes modalIn { from{opacity:0;transform:scale(0.96) translateY(8px)} to{opacity:1;transform:scale(1) translateY(0)} }

.nota-head { display:flex; justify-content:space-between; align-items:center; margin-bottom:18px; }
.nota-head h3 { font-family:'Space Grotesk',sans-serif; font-size:15px; font-weight:700; color:#fff; margin:0; display:flex; align-items:center; gap:8px; }
.nota-head h3 i { color:var(--accent-blue); }
.nota-close { background:transparent; border:1px solid var(--border); color:var(--text-muted); width:30px; height:30px; border-radius:8px; display:flex; align-items:center; justify-content:center; cursor:pointer; font-size:15px; transition:all 0.15s; }
.nota-close:hover { border-color:#ef4444; color:#ef4444; }

.nota-receipt { background:var(--bg-root); border:1px solid var(--border); border-radius:14px; padding:18px; font-size:12px; }
.nota-brand { text-align:center; margin-bottom:14px; padding-bottom:14px; border-bottom:1px dashed var(--border-mid); }
.nota-brand .bn { font-family:'Space Grotesk',sans-serif; font-size:17px; font-weight:700; color:#fff; }
.nota-brand .bs { font-size:11px; color:var(--text-muted); margin-top:2px; }
.nota-info-row { display:flex; justify-content:space-between; margin-bottom:5px; }
.nir-l { color:var(--text-muted); }
.nir-v { color:var(--text-pri); font-weight:600; text-align:right; }
.nota-items { margin:12px 0; border-top:1px dashed var(--border-mid); border-bottom:1px dashed var(--border-mid); padding:10px 0; }
.nota-item { display:flex; justify-content:space-between; margin-bottom:8px; gap:8px; }
.ni-name { color:var(--text-pri); flex:1; }
.ni-price { font-size:11px; color:var(--text-muted); margin-top:2px; }
.ni-total { font-weight:700; color:#10b981; white-space:nowrap; }
.nota-total-row { display:flex; justify-content:space-between; margin-bottom:4px; }
.nota-total-row.grand { padding-top:10px; border-top:1px solid var(--border); margin-top:8px; }
.nota-total-row.grand .ntl { font-family:'Space Grotesk',sans-serif; font-size:14px; font-weight:700; color:#fff; }
.nota-total-row.grand .ntv { font-family:'Space Grotesk',sans-serif; font-size:15px; font-weight:700; color:#10b981; }
.ntl { color:var(--text-muted); }
.ntv { color:var(--text-sec); font-weight:600; }
.nota-footer-msg { text-align:center; margin-top:12px; padding-top:12px; border-top:1px dashed var(--border-mid); font-size:11px; color:var(--text-muted); line-height:1.6; }
.nota-footer-msg span { color:var(--accent-blue); }

.nota-actions { display:flex; gap:8px; margin-top:14px; }
.btn-print { flex:1; background:linear-gradient(135deg,#1d6ae0,#7c3aed); border:none; color:#fff; font-family:'Plus Jakarta Sans',sans-serif; font-size:13px; font-weight:700; padding:10px; border-radius:12px; cursor:pointer; display:flex; align-items:center; justify-content:center; gap:6px; transition:opacity 0.15s; }
.btn-print:hover { opacity:0.9; }
.btn-close-nota { background:transparent; border:1px solid var(--border); color:var(--text-sec); font-family:'Plus Jakarta Sans',sans-serif; font-size:13px; font-weight:500; padding:10px 16px; border-radius:12px; cursor:pointer; transition:all 0.15s; }
.btn-close-nota:hover { border-color:var(--border-mid); color:#fff; }
</style>

<div class="page-header">
    <div>
        <div class="page-title"><i class="ti ti-clipboard-list"></i> Histori Transaksi</div>
        <div style="font-size:12px;color:var(--text-muted);margin-top:3px;">Riwayat seluruh nota penjualan</div>
    </div>
    <div class="tx-toolbar">
        <div class="search-wrap">
            <i class="ti ti-search"></i>
            <input type="text" class="tx-search" id="tx-search" placeholder="Cari invoice, nama..." oninput="filterTable()">
        </div>
        <button class="filter-btn" id="btn-today" onclick="toggleFilter('today')">
            <i class="ti ti-calendar-event" style="font-size:13px;"></i> Hari Ini
        </button>
        <button class="filter-btn" id="btn-month" onclick="toggleFilter('month')">
            <i class="ti ti-calendar-month" style="font-size:13px;"></i> Bulan Ini
        </button>
        <button class="refresh-btn" onclick="loadT()">
            <i class="ti ti-refresh" style="font-size:13px;"></i> Refresh
        </button>
    </div>
</div>

<div class="stats-bar">
    <div class="stat-pill"><div class="sp-dot blue"></div><span class="sp-label">Total Transaksi</span><span class="sp-val" id="stat-total">—</span></div>
    <div class="stat-pill"><div class="sp-dot green"></div><span class="sp-label">Total Pemasukan</span><span class="sp-val" id="stat-income">—</span></div>
    <div class="stat-pill"><div class="sp-dot orange"></div><span class="sp-label">Rata-rata Nota</span><span class="sp-val" id="stat-avg">—</span></div>
</div>

<div class="data-card">
    <div class="table-scroll">
        <table class="tx-table">
            <thead>
                <tr>
                    <th onclick="sortTable('invoice_no')">No Invoice <i class="ti ti-arrows-sort" style="font-size:11px;"></i></th>
                    <th onclick="sortTable('customer_name')">Customer <i class="ti ti-arrows-sort" style="font-size:11px;"></i></th>
                    <th onclick="sortTable('grand_total')">Total <i class="ti ti-arrows-sort" style="font-size:11px;"></i></th>
                    <th>Metode</th>
                    <th onclick="sortTable('created_at')">Waktu <i class="ti ti-arrows-sort" style="font-size:11px;"></i></th>
                    <th style="text-align:center;">Aksi</th>
                </tr>
            </thead>
            <tbody id="t-tbody">
                <?php for($i=0;$i<8;$i++): ?>
                <tr>
                    <td><span class="sk-cell" style="width:90px;"></span></td>
                    <td><span class="sk-cell" style="width:120px;"></span></td>
                    <td><span class="sk-cell" style="width:100px;"></span></td>
                    <td><span class="sk-cell" style="width:65px;"></span></td>
                    <td><span class="sk-cell" style="width:110px;"></span></td>
                    <td style="text-align:center;"><span class="sk-cell" style="width:60px;display:block;margin:auto;"></span></td>
                </tr>
                <?php endfor; ?>
            </tbody>
        </table>
    </div>
    <div class="table-footer">
        <div class="footer-info">Menampilkan <span id="disp-count">0</span> dari <span id="total-count">0</span> transaksi</div>
        <div class="pagination" id="pagination"></div>
    </div>
</div>

<!-- Nota Modal -->
<div class="nota-overlay" id="nota-overlay">
    <div class="nota-modal">
        <div class="nota-head">
            <h3><i class="ti ti-receipt-2"></i> Detail Nota</h3>
            <button class="nota-close" onclick="closeNota()"><i class="ti ti-x"></i></button>
        </div>
        <div id="nota-content"></div>
        <div class="nota-actions">
            <button class="btn-print" onclick="printNota()"><i class="ti ti-printer"></i> Cetak Nota</button>
            <button class="btn-close-nota" onclick="closeNota()">Tutup</button>
        </div>
    </div>
</div>

<script>
let allData = [], filteredData = [], activeFilter = null;
let sortKey = 'created_at', sortDir = -1;
let currentPage = 1;
const PER_PAGE = 15;

async function loadT() {
    const tbody = document.getElementById('t-tbody');
    tbody.innerHTML = `${[...Array(5)].map(() => `<tr>
        <td><span class="sk-cell" style="width:90px;"></span></td>
        <td><span class="sk-cell" style="width:120px;"></span></td>
        <td><span class="sk-cell" style="width:100px;"></span></td>
        <td><span class="sk-cell" style="width:65px;"></span></td>
        <td><span class="sk-cell" style="width:110px;"></span></td>
        <td style="text-align:center;"><span class="sk-cell" style="width:60px;display:block;margin:auto;"></span></td>
    </tr>`).join('')}`;

    try {
        const result = await apiFetch('../api/transactions/get_all.php');
        if (result.status === 'success') {
            allData = result.data;
            applyFilters();
        }
    } catch(e) {
        tbody.innerHTML = `<tr><td colspan="6"><div class="no-data" style="color:#ef4444;"><i class="ti ti-wifi-off"></i><p>Gagal memuat data transaksi.</p></div></td></tr>`;
    }
}

function applyFilters() {
    const q = document.getElementById('tx-search').value.toLowerCase();
    const now = new Date();
    filteredData = allData.filter(t => {
        const matchQ = !q || t.invoice_no.toLowerCase().includes(q) || (t.customer_name||'').toLowerCase().includes(q);
        if (!matchQ) return false;
        if (activeFilter === 'today') { const d = new Date(t.created_at); return d.toDateString() === now.toDateString(); }
        if (activeFilter === 'month') { const d = new Date(t.created_at); return d.getMonth() === now.getMonth() && d.getFullYear() === now.getFullYear(); }
        return true;
    });
    filteredData.sort((a, b) => {
        let va = a[sortKey], vb = b[sortKey];
        if (sortKey === 'grand_total') { va = parseFloat(va); vb = parseFloat(vb); }
        return va > vb ? sortDir : va < vb ? -sortDir : 0;
    });
    const total = filteredData.reduce((s, t) => s + parseFloat(t.grand_total || 0), 0);
    const count = filteredData.length;
    document.getElementById('stat-total').textContent = count;
    document.getElementById('stat-income').textContent = 'Rp ' + Math.round(total).toLocaleString('id-ID');
    document.getElementById('stat-avg').textContent = 'Rp ' + (count > 0 ? Math.round(total/count) : 0).toLocaleString('id-ID');
    document.getElementById('total-count').textContent = count;
    currentPage = 1;
    renderPage();
}

function renderPage() {
    const start = (currentPage - 1) * PER_PAGE;
    const pageData = filteredData.slice(start, start + PER_PAGE);
    const tbody = document.getElementById('t-tbody');
    document.getElementById('disp-count').textContent = Math.min(start + PER_PAGE, filteredData.length);
    if (!filteredData.length) {
        tbody.innerHTML = `<tr><td colspan="6"><div class="no-data"><i class="ti ti-ghost"></i><p>Tidak ada transaksi ditemukan.</p></div></td></tr>`;
        document.getElementById('pagination').innerHTML = '';
        return;
    }
    tbody.innerHTML = pageData.map(t => {
        const method = (t.payment_method || 'tunai').toLowerCase();
        const mClass = method.includes('transfer') ? 'method-transfer' : (method.includes('qris') || method.includes('qr')) ? 'method-qris' : 'method-tunai';
        const mLabel = method.includes('transfer') ? '🏦 Transfer' : (method.includes('qris') || method.includes('qr')) ? '📱 QRIS' : '💵 Tunai';
        const d = new Date(t.created_at);
        return `<tr onclick="showN('${t.invoice_no}')">
            <td class="td-invoice">${t.invoice_no}</td>
            <td>
                <div class="td-customer">${t.customer_name || 'Umum'}</div>
                ${t.customer_phone ? `<span class="td-phone"><i class="ti ti-phone" style="font-size:10px;margin-right:3px;"></i>${t.customer_phone}</span>` : ''}
            </td>
            <td class="td-total">Rp ${parseInt(t.grand_total).toLocaleString('id-ID')}</td>
            <td><span class="method-badge ${mClass}">${mLabel}</span></td>
            <td>
                <div class="td-time">${d.toLocaleDateString('id-ID',{day:'2-digit',month:'short',year:'numeric'})}</div>
                <div class="td-time-detail">${d.toLocaleTimeString('id-ID',{hour:'2-digit',minute:'2-digit'})}</div>
            </td>
            <td>
                <div class="action-btns">
                    <button class="icon-btn" title="Lihat Nota" onclick="event.stopPropagation();showN('${t.invoice_no}')"><i class="ti ti-receipt-2"></i></button>
                    <button class="icon-btn print" title="Print" onclick="event.stopPropagation();showN('${t.invoice_no}',true)"><i class="ti ti-printer"></i></button>
                </div>
            </td>
        </tr>`;
    }).join('');
    renderPagination();
}

function renderPagination() {
    const totalPages = Math.ceil(filteredData.length / PER_PAGE);
    const pg = document.getElementById('pagination');
    if (totalPages <= 1) { pg.innerHTML = ''; return; }
    let btns = `<button class="page-btn" onclick="goPage(${currentPage-1})" ${currentPage===1?'disabled style="opacity:0.4;"':''}><i class="ti ti-chevron-left" style="font-size:12px;"></i></button>`;
    for (let i=1; i<=totalPages; i++) {
        if (i===1||i===totalPages||Math.abs(i-currentPage)<=1) btns += `<button class="page-btn ${i===currentPage?'active':''}" onclick="goPage(${i})">${i}</button>`;
        else if (Math.abs(i-currentPage)===2) btns += `<span style="color:var(--text-muted);padding:0 4px;font-size:12px;">…</span>`;
    }
    btns += `<button class="page-btn" onclick="goPage(${currentPage+1})" ${currentPage===totalPages?'disabled style="opacity:0.4;"':''}><i class="ti ti-chevron-right" style="font-size:12px;"></i></button>`;
    pg.innerHTML = btns;
}

function goPage(p) {
    const total = Math.ceil(filteredData.length / PER_PAGE);
    if (p < 1 || p > total) return;
    currentPage = p;
    renderPage();
    document.querySelector('.data-card').scrollIntoView({behavior:'smooth',block:'start'});
}

function filterTable() { applyFilters(); }
function sortTable(key) { if (sortKey===key) sortDir*=-1; else { sortKey=key; sortDir=1; } applyFilters(); }
function toggleFilter(f) {
    activeFilter = activeFilter===f ? null : f;
    document.getElementById('btn-today').classList.toggle('active', activeFilter==='today');
    document.getElementById('btn-month').classList.toggle('active', activeFilter==='month');
    applyFilters();
}

function showN(invoiceNo, autoPrint = false) {
    const t = allData.find(x => x.invoice_no === invoiceNo);
    if (!t) return;
    document.getElementById('nota-content').innerHTML = `
        <div class="nota-receipt" id="nota-printable">
            <div class="nota-brand">
                <div class="bn">⚡ Obeng Plus</div>
                <div class="bs">Car Audio Specialist</div>
                <div class="bs" style="margin-top:3px;">Jl. Contoh No. 123, Semarang</div>
            </div>
            <div class="nota-info-row"><span class="nir-l">No. Invoice</span><span class="nir-v" style="color:var(--accent-blue);font-family:'Space Grotesk',sans-serif;font-weight:700;">${t.invoice_no}</span></div>
            <div class="nota-info-row"><span class="nir-l">Tanggal</span><span class="nir-v">${new Date(t.created_at).toLocaleString('id-ID',{day:'2-digit',month:'long',year:'numeric',hour:'2-digit',minute:'2-digit'})}</span></div>
            <div class="nota-info-row"><span class="nir-l">Customer</span><span class="nir-v">${t.customer_name||'Umum'}</span></div>
            ${t.customer_phone ? `<div class="nota-info-row"><span class="nir-l">Telepon</span><span class="nir-v">${t.customer_phone}</span></div>` : ''}
            <div class="nota-info-row"><span class="nir-l">Kasir</span><span class="nir-v">${t.cashier_name||'Admin'}</span></div>
            <div class="nota-items">
                ${(t.items||[]).map(item => `<div class="nota-item">
                    <div><div class="ni-name">${item.name}</div><div class="ni-price">${parseInt(item.price).toLocaleString('id-ID')} × ${item.qty}</div></div>
                    <div class="ni-total">Rp ${(parseInt(item.price)*parseInt(item.qty)).toLocaleString('id-ID')}</div>
                </div>`).join('') || `<div style="color:var(--text-muted);font-size:12px;text-align:center;padding:8px;">Detail item tidak tersedia</div>`}
            </div>
            ${t.discount > 0 ? `
            <div class="nota-total-row"><span class="ntl">Subtotal</span><span class="ntv">Rp ${(parseInt(t.grand_total)+parseInt(t.discount)).toLocaleString('id-ID')}</span></div>
            <div class="nota-total-row"><span class="ntl" style="color:#f97316;">Diskon</span><span class="ntv" style="color:#f97316;">-Rp ${parseInt(t.discount).toLocaleString('id-ID')}</span></div>` : ''}
            <div class="nota-total-row grand"><span class="ntl">TOTAL BAYAR</span><span class="ntv">Rp ${parseInt(t.grand_total).toLocaleString('id-ID')}</span></div>
            ${t.amount_paid ? `
            <div class="nota-total-row" style="margin-top:6px;"><span class="ntl">Dibayar</span><span class="ntv">Rp ${parseInt(t.amount_paid).toLocaleString('id-ID')}</span></div>
            <div class="nota-total-row"><span class="ntl">Kembalian</span><span class="ntv" style="color:#10b981;">Rp ${(parseInt(t.amount_paid)-parseInt(t.grand_total)).toLocaleString('id-ID')}</span></div>` : ''}
            <div class="nota-total-row" style="margin-top:6px;"><span class="ntl">Metode</span><span class="ntv" style="color:#a78bfa;">${t.payment_method||'Tunai'}</span></div>
            <div class="nota-footer-msg">Terima kasih telah mempercayakan<br>kebutuhan audio mobil Anda kepada kami!<br><span>📱 IG: @obengplus.caraudio</span><br><span style="color:var(--text-muted);">Garansi pemasangan 3 bulan</span></div>
        </div>`;
    document.getElementById('nota-overlay').classList.add('open');
    if (autoPrint) setTimeout(printNota, 300);
}

function closeNota() { document.getElementById('nota-overlay').classList.remove('open'); }

function printNota() {
    const content = document.getElementById('nota-printable').innerHTML;
    const w = window.open('','','width=400,height=600');
    w.document.write(`<html><head><title>Nota</title><style>
        body{font-family:'Courier New',monospace;font-size:12px;max-width:300px;margin:auto;padding:16px;}
        .nota-brand{text-align:center;padding-bottom:12px;border-bottom:1px dashed #ccc;margin-bottom:12px;}
        .bn{font-size:18px;font-weight:bold;}.bs{font-size:10px;color:#666;}
        .nota-info-row{display:flex;justify-content:space-between;margin-bottom:4px;font-size:11px;}
        .nota-items{border-top:1px dashed #ccc;border-bottom:1px dashed #ccc;padding:10px 0;margin:10px 0;}
        .nota-item{display:flex;justify-content:space-between;margin-bottom:6px;}
        .nota-total-row{display:flex;justify-content:space-between;margin-bottom:3px;}
        .nota-total-row.grand{border-top:1px solid #ccc;padding-top:8px;margin-top:6px;font-weight:bold;font-size:14px;}
        .nota-footer-msg{text-align:center;margin-top:14px;padding-top:12px;border-top:1px dashed #ccc;font-size:10px;color:#666;line-height:1.6;}
        @media print{body{max-width:100%;}}
    </style></head><body>${content}<script>window.onload=()=>window.print()<\/script></body></html>`);
    w.document.close();
}

document.getElementById('nota-overlay').addEventListener('click', function(e) { if (e.target === this) closeNota(); });

loadT();
</script>

<?php require_once '../includes/footer.php'; ?>