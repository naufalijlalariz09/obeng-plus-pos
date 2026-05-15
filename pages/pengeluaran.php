<?php
// pages/pengeluaran.php
require_once '../includes/header.php';
require_role(['admin', 'pimpinan']);
?>

<style>
/* ── Layout ── */
.page-header { display:flex; justify-content:space-between; align-items:flex-start; margin-bottom:20px; flex-wrap:wrap; gap:12px; }
.page-title  { font-family:'Space Grotesk',sans-serif; font-size:20px; font-weight:700; color:#fff; display:flex; align-items:center; gap:10px; }
.page-title i { color:#f97316; }
.page-sub    { font-size:12px; color:var(--text-muted); margin-top:3px; }

/* ── Stat cards ── */
.stats-row { display:grid; grid-template-columns:repeat(4,1fr); gap:12px; margin-bottom:20px; }
@media(max-width:900px){ .stats-row{ grid-template-columns:repeat(2,1fr); } }
.stat-pill { background:var(--bg-card); border:1px solid var(--border); border-radius:16px; padding:16px; position:relative; overflow:hidden; transition:border-color .2s; }
.stat-pill:hover { border-color:var(--border-mid); }
.stat-pill::before { content:''; position:absolute; top:0; left:0; right:0; height:2px; }
.stat-pill.s-red::before    { background:linear-gradient(90deg,#ef4444,#f87171); }
.stat-pill.s-orange::before { background:linear-gradient(90deg,#f97316,#f59e0b); }
.stat-pill.s-purple::before { background:linear-gradient(90deg,#7c3aed,#a78bfa); }
.stat-pill.s-blue::before   { background:linear-gradient(90deg,#1d6ae0,#60a5fa); }
.sp-icon { width:36px; height:36px; border-radius:10px; display:flex; align-items:center; justify-content:center; font-size:17px; margin-bottom:12px; }
.sp-val  { font-family:'Space Grotesk',sans-serif; font-size:20px; font-weight:700; color:#fff; margin-bottom:4px; }
.sp-lbl  { font-size:10px; font-weight:700; letter-spacing:1px; text-transform:uppercase; color:var(--text-muted); }

/* ── Filter bar ── */
.filter-bar { display:flex; gap:8px; margin-bottom:16px; flex-wrap:wrap; align-items:center; justify-content:space-between; }
.filter-left { display:flex; gap:8px; flex-wrap:wrap; align-items:center; }

.period-select { padding:9px 32px 9px 14px; background:var(--bg-card); border:1px solid var(--border); border-radius:12px; color:var(--text-sec); font-family:'Plus Jakarta Sans',sans-serif; font-size:13px; font-weight:500; outline:none; cursor:pointer; appearance:none; background-image:url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' fill='none' viewBox='0 0 24 24'%3E%3Cpath stroke='%235a6380' stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='m6 9 6 6 6-6'/%3E%3C/svg%3E"); background-repeat:no-repeat; background-position:right 10px center; }
.period-select option { background:var(--bg-card); }

.cat-filter-btn { padding:8px 14px; background:var(--bg-card); border:1px solid var(--border); border-radius:10px; color:var(--text-muted); font-family:'Plus Jakarta Sans',sans-serif; font-size:12px; font-weight:600; cursor:pointer; transition:all .15s; display:flex; align-items:center; gap:5px; }
.cat-filter-btn:hover { border-color:var(--border-mid); color:var(--text-sec); }
.cat-filter-btn.active { border-color:rgba(249,115,22,.4); color:#f97316; background:rgba(249,115,22,.08); }

.btn-add { display:flex; align-items:center; gap:6px; padding:9px 18px; background:linear-gradient(135deg,#f97316,#f59e0b); border:none; border-radius:12px; color:#fff; font-family:'Plus Jakarta Sans',sans-serif; font-size:13px; font-weight:700; cursor:pointer; transition:opacity .15s; box-shadow:0 4px 16px rgba(249,115,22,.3); white-space:nowrap; }
.btn-add:hover { opacity:.9; }

/* ── Main layout: chart + table side by side ── */
.main-grid { display:grid; grid-template-columns:300px 1fr; gap:16px; margin-bottom:16px; }
@media(max-width:1100px){ .main-grid{ grid-template-columns:1fr; } }

/* ── Donut chart card ── */
.chart-card { background:var(--bg-card); border:1px solid var(--border); border-radius:20px; padding:22px; }
.chart-card h3 { font-family:'Space Grotesk',sans-serif; font-size:14px; font-weight:700; color:#fff; margin-bottom:4px; }
.chart-card .ch-sub { font-size:12px; color:var(--text-muted); margin-bottom:18px; }
.donut-wrap { position:relative; width:160px; height:160px; margin:0 auto 20px; }
.donut-wrap canvas { width:100%!important; height:100%!important; }
.donut-center { position:absolute; inset:0; display:flex; flex-direction:column; align-items:center; justify-content:center; pointer-events:none; }
.donut-center .dc-val { font-family:'Space Grotesk',sans-serif; font-size:16px; font-weight:700; color:#fff; }
.donut-center .dc-lbl { font-size:10px; color:var(--text-muted); margin-top:2px; }
.legend-list { display:flex; flex-direction:column; gap:8px; }
.legend-item { display:flex; align-items:center; justify-content:space-between; gap:8px; }
.legend-dot  { width:10px; height:10px; border-radius:50%; flex-shrink:0; }
.legend-label { font-size:12px; color:var(--text-sec); flex:1; }
.legend-val   { font-family:'Space Grotesk',sans-serif; font-size:12px; font-weight:700; color:#fff; }
.legend-pct   { font-size:11px; color:var(--text-muted); width:36px; text-align:right; }

/* ── Data card / table ── */
.data-card { background:var(--bg-card); border:1px solid var(--border); border-radius:20px; overflow:hidden; }
.data-table { width:100%; border-collapse:collapse; font-size:13px; }
.data-table thead tr { background:rgba(0,0,0,.2); border-bottom:1px solid var(--border); }
.data-table thead th { padding:12px 18px; text-align:left; font-size:10px; font-weight:700; letter-spacing:1.2px; text-transform:uppercase; color:var(--text-muted); white-space:nowrap; cursor:pointer; transition:color .15s; }
.data-table thead th:hover { color:var(--text-pri); }
.data-table tbody tr { border-top:1px solid rgba(255,255,255,.03); transition:background .12s; }
.data-table tbody tr:hover { background:rgba(255,255,255,.03); }
.data-table tbody td { padding:13px 18px; color:var(--text-sec); vertical-align:middle; }
.table-scroll { overflow-x:auto; }

.cat-icon { width:34px; height:34px; border-radius:10px; display:flex; align-items:center; justify-content:center; font-size:16px; flex-shrink:0; }
.badge { display:inline-flex; align-items:center; gap:4px; font-size:10px; font-weight:700; padding:3px 10px; border-radius:20px; border:1px solid transparent; }

.icon-btn { background:transparent; border:1px solid var(--border); border-radius:8px; color:var(--text-muted); width:30px; height:30px; display:inline-flex; align-items:center; justify-content:center; cursor:pointer; font-size:14px; transition:all .15s; }
.icon-btn.edit:hover { border-color:var(--accent-blue); color:var(--accent-blue); background:rgba(96,165,250,.06); }
.icon-btn.del:hover  { border-color:#ef4444; color:#ef4444; background:rgba(239,68,68,.06); }

.sk { height:13px; background:linear-gradient(90deg,#1e2a45 25%,#2d3a55 50%,#1e2a45 75%); background-size:200% 100%; animation:shimmer 1.4s infinite; border-radius:6px; display:inline-block; }
@keyframes shimmer { 0%{background-position:200% 0} 100%{background-position:-200% 0} }

.empty-state { padding:60px 20px; text-align:center; color:var(--text-muted); }
.empty-state i { font-size:44px; display:block; margin-bottom:14px; }

.table-footer { display:flex; justify-content:space-between; align-items:center; padding:12px 18px; border-top:1px solid var(--border); background:rgba(0,0,0,.15); flex-wrap:wrap; gap:8px; }
.footer-info { font-size:12px; color:var(--text-muted); }
.footer-info span { color:var(--text-sec); font-weight:600; margin:0 3px; }

/* pagination */
.pagination { display:flex; align-items:center; gap:4px; }
.page-btn { background:transparent; border:1px solid var(--border); color:var(--text-muted); font-family:'Plus Jakarta Sans',sans-serif; font-size:12px; font-weight:600; padding:5px 10px; border-radius:7px; cursor:pointer; transition:all .15s; min-width:30px; text-align:center; }
.page-btn:hover { border-color:var(--border-mid); color:var(--text-pri); }
.page-btn.active { background:rgba(249,115,22,.12); border-color:rgba(249,115,22,.4); color:#f97316; }

/* ── Modal ── */
.modal-back { position:fixed; inset:0; background:rgba(0,0,0,.75); backdrop-filter:blur(5px); z-index:999; display:none; align-items:center; justify-content:center; padding:16px; }
.modal-back.open { display:flex; }
.modal-box { background:var(--bg-card); border:1px solid var(--border); border-radius:22px; width:100%; max-width:480px; overflow:hidden; animation:mIn .22s ease; max-height:90vh; overflow-y:auto; }
@keyframes mIn { from{opacity:0;transform:scale(.96) translateY(8px)} to{opacity:1;transform:scale(1) translateY(0)} }
.modal-hd { display:flex; justify-content:space-between; align-items:center; padding:16px 20px; border-bottom:1px solid var(--border); background:rgba(255,255,255,.02); position:sticky; top:0; z-index:1; }
.modal-hd h3 { font-family:'Space Grotesk',sans-serif; font-size:15px; font-weight:700; color:#fff; margin:0; display:flex; align-items:center; gap:8px; }
.modal-close-btn { background:transparent; border:1px solid var(--border); color:var(--text-muted); width:30px; height:30px; border-radius:8px; display:flex; align-items:center; justify-content:center; cursor:pointer; font-size:15px; transition:all .15s; }
.modal-close-btn:hover { border-color:#ef4444; color:#ef4444; }
.modal-bd { padding:20px; }

.form-group { margin-bottom:14px; }
.form-label { display:block; font-size:10px; font-weight:800; letter-spacing:1.2px; text-transform:uppercase; color:var(--text-muted); margin-bottom:7px; }
.form-control { width:100%; background:var(--bg-input); border:1px solid var(--border); color:var(--text-pri); font-family:'Plus Jakarta Sans',sans-serif; font-size:13px; padding:10px 14px; border-radius:10px; outline:none; transition:border-color .15s, box-shadow .15s; }
.form-control:focus { border-color:rgba(249,115,22,.5); box-shadow:0 0 0 3px rgba(249,115,22,.08); }
.form-control::placeholder { color:var(--text-muted); }
.form-control option { background:var(--bg-card); }
.form-row { display:grid; grid-template-columns:1fr 1fr; gap:12px; }
@media(max-width:480px){ .form-row{ grid-template-columns:1fr; } }
.prefix-wrap { position:relative; }
.prefix-wrap .pfx { position:absolute; left:12px; top:50%; transform:translateY(-50%); font-size:12px; font-weight:700; color:var(--text-muted); pointer-events:none; }
.prefix-wrap .form-control { padding-left:34px; }

/* category picker */
.cat-picker { display:grid; grid-template-columns:repeat(2,1fr); gap:8px; margin-bottom:4px; }
.cat-pick-btn { display:flex; align-items:center; gap:8px; padding:10px 12px; background:var(--bg-input); border:1px solid var(--border); border-radius:10px; cursor:pointer; transition:all .15s; }
.cat-pick-btn:hover { border-color:var(--border-mid); }
.cat-pick-btn.selected { border-color:rgba(249,115,22,.5); background:rgba(249,115,22,.08); }
.cat-pick-btn .cpb-icon { width:28px; height:28px; border-radius:8px; display:flex; align-items:center; justify-content:center; font-size:14px; flex-shrink:0; }
.cat-pick-btn .cpb-label { font-size:12px; font-weight:600; color:var(--text-sec); }
.cat-pick-btn.selected .cpb-label { color:#f97316; }

.btn-row { display:flex; gap:10px; margin-top:6px; }
.btn-cancel { flex:1; padding:10px; background:transparent; border:1px solid var(--border); border-radius:10px; color:var(--text-sec); font-family:'Plus Jakarta Sans',sans-serif; font-size:13px; font-weight:600; cursor:pointer; transition:all .15s; }
.btn-cancel:hover { border-color:var(--border-mid); color:#fff; }
.btn-save { flex:1; padding:10px; background:linear-gradient(135deg,#f97316,#f59e0b); border:none; border-radius:10px; color:#fff; font-family:'Plus Jakarta Sans',sans-serif; font-size:13px; font-weight:700; cursor:pointer; transition:opacity .15s; }
.btn-save:hover { opacity:.9; }
.btn-save:disabled { opacity:.5; cursor:not-allowed; }
</style>

<!-- ═══ PAGE HEADER ═══ -->
<div class="page-header">
    <div>
        <div class="page-title"><i class="ti ti-wallet"></i> Pengeluaran Operasional</div>
        <div class="page-sub">Catat semua biaya operasional untuk laporan laba/rugi yang akurat</div>
    </div>
    <button class="btn-add" onclick="openModal()">
        <i class="ti ti-plus" style="font-size:14px;"></i> Catat Pengeluaran
    </button>
</div>

<!-- ═══ STATS ═══ -->
<div class="stats-row">
    <div class="stat-pill s-red">
        <div class="sp-icon" style="background:rgba(239,68,68,.12);color:#ef4444;"><i class="ti ti-trending-down"></i></div>
        <div class="sp-val" id="s-bulan-ini">—</div>
        <div class="sp-lbl">Total Bulan Ini</div>
    </div>
    <div class="stat-pill s-orange">
        <div class="sp-icon" style="background:rgba(249,115,22,.12);color:#f97316;"><i class="ti ti-users"></i></div>
        <div class="sp-val" id="s-gaji">—</div>
        <div class="sp-lbl">Gaji & Honor</div>
    </div>
    <div class="stat-pill s-purple">
        <div class="sp-icon" style="background:rgba(124,58,237,.12);color:#a78bfa;"><i class="ti ti-home"></i></div>
        <div class="sp-val" id="s-sewa">—</div>
        <div class="sp-lbl">Sewa & Utilitas</div>
    </div>
    <div class="stat-pill s-blue">
        <div class="sp-icon" style="background:rgba(96,165,250,.12);color:#60a5fa;"><i class="ti ti-dots-circle-horizontal"></i></div>
        <div class="sp-val" id="s-lain">—</div>
        <div class="sp-lbl">Lain-lain</div>
    </div>
</div>

<!-- ═══ FILTER BAR ═══ -->
<div class="filter-bar">
    <div class="filter-left">
        <select id="period-select" class="period-select" onchange="applyFilters()">
            <option value="this_month">Bulan Ini</option>
            <option value="last_month">Bulan Lalu</option>
            <option value="this_year">Tahun Ini</option>
            <option value="all">Semua Data</option>
        </select>
        <button class="cat-filter-btn active" data-cat="" onclick="setCatFilter('', this)">
            <i class="ti ti-list" style="font-size:12px;"></i> Semua
        </button>
        <button class="cat-filter-btn" data-cat="Gaji" onclick="setCatFilter('Gaji', this)">
            <i class="ti ti-users" style="font-size:12px;"></i> Gaji
        </button>
        <button class="cat-filter-btn" data-cat="Utilitas" onclick="setCatFilter('Utilitas', this)">
            <i class="ti ti-bolt" style="font-size:12px;"></i> Utilitas
        </button>
        <button class="cat-filter-btn" data-cat="Sewa" onclick="setCatFilter('Sewa', this)">
            <i class="ti ti-home" style="font-size:12px;"></i> Sewa
        </button>
        <button class="cat-filter-btn" data-cat="Lain-lain" onclick="setCatFilter('Lain-lain', this)">
            <i class="ti ti-dots" style="font-size:12px;"></i> Lain-lain
        </button>
    </div>
</div>

<!-- ═══ MAIN GRID ═══ -->
<div class="main-grid">

    <!-- Donut Chart -->
    <div class="chart-card">
        <h3>Distribusi Pengeluaran</h3>
        <div class="ch-sub" id="chart-period-label">Bulan ini</div>
        <div class="donut-wrap">
            <canvas id="donutChart"></canvas>
            <div class="donut-center">
                <div class="dc-val" id="donut-center-val">Rp 0</div>
                <div class="dc-lbl">Total</div>
            </div>
        </div>
        <div class="legend-list" id="chart-legend">
            <!-- filled by JS -->
        </div>
    </div>

    <!-- Table -->
    <div class="data-card">
        <div class="table-scroll">
            <table class="data-table">
                <thead>
                    <tr>
                        <th onclick="sortData('date')">Tanggal <i class="ti ti-arrows-sort" style="font-size:11px;"></i></th>
                        <th>Kategori</th>
                        <th onclick="sortData('description')">Keterangan <i class="ti ti-arrows-sort" style="font-size:11px;"></i></th>
                        <th onclick="sortData('amount')">Jumlah <i class="ti ti-arrows-sort" style="font-size:11px;"></i></th>
                        <th>Dicatat Oleh</th>
                        <th style="text-align:center;">Aksi</th>
                    </tr>
                </thead>
                <tbody id="exp-tbody">
                    <?php for($i=0;$i<6;$i++): ?>
                    <tr>
                        <td><span class="sk" style="width:80px;"></span></td>
                        <td><span class="sk" style="width:80px;"></span></td>
                        <td><span class="sk" style="width:140px;"></span></td>
                        <td><span class="sk" style="width:90px;"></span></td>
                        <td><span class="sk" style="width:80px;"></span></td>
                        <td style="text-align:center;"><span class="sk" style="width:50px;display:block;margin:auto;"></span></td>
                    </tr>
                    <?php endfor; ?>
                </tbody>
            </table>
        </div>
        <div class="table-footer">
            <div class="footer-info">
                Menampilkan <span id="disp-count">0</span> dari <span id="total-count">0</span> data ·
                Total: <span id="footer-total" style="color:#f97316;">Rp 0</span>
            </div>
            <div class="pagination" id="pagination"></div>
        </div>
    </div>
</div>

<!-- ══════════════════════════════════
     MODAL – Catat Pengeluaran
══════════════════════════════════ -->
<div class="modal-back" id="exp-modal">
    <div class="modal-box">
        <div class="modal-hd">
            <h3 id="modal-title-txt"><i class="ti ti-wallet" style="color:#f97316;"></i> Catat Pengeluaran</h3>
            <button class="modal-close-btn" onclick="closeModal()"><i class="ti ti-x"></i></button>
        </div>
        <form onsubmit="saveExp(event)" class="modal-bd">
            <input type="hidden" id="e-id">

            <!-- Category picker -->
            <div class="form-group">
                <label class="form-label">Kategori <span style="color:#ef4444;">*</span></label>
                <div class="cat-picker">
                    <button type="button" class="cat-pick-btn" data-cat="Gaji" onclick="selectCat(this)">
                        <div class="cpb-icon" style="background:rgba(249,115,22,.12);color:#f97316;"><i class="ti ti-users"></i></div>
                        <span class="cpb-label">Gaji / Honor</span>
                    </button>
                    <button type="button" class="cat-pick-btn" data-cat="Utilitas" onclick="selectCat(this)">
                        <div class="cpb-icon" style="background:rgba(96,165,250,.12);color:#60a5fa;"><i class="ti ti-bolt"></i></div>
                        <span class="cpb-label">Listrik / Air / Internet</span>
                    </button>
                    <button type="button" class="cat-pick-btn" data-cat="Sewa" onclick="selectCat(this)">
                        <div class="cpb-icon" style="background:rgba(167,139,250,.12);color:#a78bfa;"><i class="ti ti-home"></i></div>
                        <span class="cpb-label">Sewa Tempat</span>
                    </button>
                    <button type="button" class="cat-pick-btn" data-cat="Lain-lain" onclick="selectCat(this)">
                        <div class="cpb-icon" style="background:rgba(148,163,184,.1);color:#94a3b8;"><i class="ti ti-dots-circle-horizontal"></i></div>
                        <span class="cpb-label">Lain-lain</span>
                    </button>
                </div>
                <input type="hidden" id="e-category" required>
            </div>

            <div class="form-group">
                <label class="form-label">Keterangan <span style="color:#ef4444;">*</span></label>
                <input type="text" id="e-desc" class="form-control" placeholder="Misal: Gaji karyawan bulan Juni" required>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">Jumlah (Rp) <span style="color:#ef4444;">*</span></label>
                    <div class="prefix-wrap">
                        <span class="pfx">Rp</span>
                        <input type="number" id="e-amount" class="form-control" placeholder="0" min="1" required>
                    </div>
                </div>
                <div class="form-group">
                    <label class="form-label">Tanggal <span style="color:#ef4444;">*</span></label>
                    <input type="date" id="e-date" class="form-control" value="<?php echo date('Y-m-d'); ?>" required>
                </div>
            </div>

            <div class="form-group">
                <label class="form-label">Penerima / Pihak Ketiga</label>
                <input type="text" id="e-recipient" class="form-control" placeholder="Nama karyawan / vendor (opsional)">
            </div>

            <div class="form-group">
                <label class="form-label">Bukti / No. Referensi</label>
                <input type="text" id="e-ref" class="form-control" placeholder="No. kwitansi / bukti transfer (opsional)">
            </div>

            <div class="form-group">
                <label class="form-label">Catatan Tambahan</label>
                <textarea id="e-note" class="form-control" rows="2" placeholder="Catatan tambahan..."></textarea>
            </div>

            <div class="btn-row">
                <button type="button" class="btn-cancel" onclick="closeModal()">Batal</button>
                <button type="submit" class="btn-save" id="btn-save-exp">
                    <i class="ti ti-circle-check" style="font-size:14px;"></i> Simpan
                </button>
            </div>
        </form>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
/* ════════════════════════════════
   CONFIG
════════════════════════════════ */
const CAT_CFG = {
    'Gaji':     { icon:'ti-users',                 color:'#f97316', bg:'rgba(249,115,22,.12)',  badgeCls:'badge-orange' },
    'Utilitas': { icon:'ti-bolt',                  color:'#60a5fa', bg:'rgba(96,165,250,.12)',  badgeCls:'badge-blue'   },
    'Sewa':     { icon:'ti-home',                  color:'#a78bfa', bg:'rgba(167,139,250,.12)', badgeCls:'badge-purple' },
    'Lain-lain':{ icon:'ti-dots-circle-horizontal', color:'#94a3b8', bg:'rgba(148,163,184,.1)',  badgeCls:'badge-gray'   },
};
const CSS_EXTRA = `
    .badge-orange { background:rgba(249,115,22,.1);  color:#f97316; border-color:rgba(249,115,22,.2); }
    .badge-blue   { background:rgba(96,165,250,.1);  color:#60a5fa; border-color:rgba(96,165,250,.2); }
    .badge-purple { background:rgba(167,139,250,.1); color:#a78bfa; border-color:rgba(167,139,250,.2); }
    .badge-gray   { background:rgba(148,163,184,.1); color:#94a3b8; border-color:rgba(148,163,184,.15); }
`;
(function(){ const s = document.createElement('style'); s.textContent = CSS_EXTRA; document.head.appendChild(s); })();

/* ════════════════════════════════
   STATE
════════════════════════════════ */
let allData = [], filteredData = [];
let activeCat = '', sortKey = 'date', sortDir = -1;
let currentPage = 1;
const PER_PAGE = 15;
let donutChart = null;

/* ════════════════════════════════
   HELPERS
════════════════════════════════ */
function fmt(n) { return 'Rp ' + parseInt(n||0).toLocaleString('id-ID'); }
function fmtShort(n) {
    n = parseInt(n||0);
    if (n >= 1000000) return 'Rp' + (n/1000000).toFixed(1) + 'jt';
    if (n >= 1000)    return 'Rp' + (n/1000).toFixed(0) + 'k';
    return fmt(n);
}
function fmtDate(s) { return new Date(s).toLocaleDateString('id-ID',{day:'2-digit',month:'short',year:'numeric'}); }

function inPeriod(dateStr) {
    const d = new Date(dateStr);
    const now = new Date();
    const p = document.getElementById('period-select').value;
    if (p === 'this_month') return d.getMonth()===now.getMonth() && d.getFullYear()===now.getFullYear();
    if (p === 'last_month') {
        const lm = new Date(now.getFullYear(), now.getMonth()-1, 1);
        return d.getMonth()===lm.getMonth() && d.getFullYear()===lm.getFullYear();
    }
    if (p === 'this_year') return d.getFullYear()===now.getFullYear();
    return true;
}

/* ════════════════════════════════
   LOAD
════════════════════════════════ */
async function loadData() {
    try {
        const r = await apiFetch('../api/expenses/get_all.php');
        if (r.status === 'success') { allData = r.data; }
    } catch(e) { allData = []; }
    applyFilters();
}

/* ════════════════════════════════
   FILTER & SORT
════════════════════════════════ */
function applyFilters() {
    filteredData = allData.filter(e => {
        const matchPeriod = inPeriod(e.date);
        const matchCat    = !activeCat || e.category === activeCat;
        return matchPeriod && matchCat;
    });
    filteredData.sort((a,b) => {
        let va = a[sortKey], vb = b[sortKey];
        if (sortKey === 'amount') { va = parseFloat(va); vb = parseFloat(vb); }
        return va > vb ? sortDir : va < vb ? -sortDir : 0;
    });
    updateStats();
    updateChart();
    currentPage = 1;
    renderPage();
}

function setCatFilter(cat, btn) {
    activeCat = cat;
    document.querySelectorAll('.cat-filter-btn').forEach(b => b.classList.remove('active'));
    btn.classList.add('active');
    applyFilters();
}

function sortData(key) {
    if (sortKey === key) sortDir *= -1; else { sortKey = key; sortDir = 1; }
    applyFilters();
}

/* ════════════════════════════════
   STATS
════════════════════════════════ */
function updateStats() {
    // always compute from "this_month" for stat pills regardless of filter
    const now = new Date();
    const thisMonth = allData.filter(e => {
        const d = new Date(e.date);
        return d.getMonth()===now.getMonth() && d.getFullYear()===now.getFullYear();
    });
    const total  = thisMonth.reduce((s,e) => s+parseInt(e.amount||0), 0);
    const gaji   = thisMonth.filter(e=>e.category==='Gaji').reduce((s,e)=>s+parseInt(e.amount||0),0);
    const sewa   = thisMonth.filter(e=>e.category==='Sewa'||e.category==='Utilitas').reduce((s,e)=>s+parseInt(e.amount||0),0);
    const lain   = thisMonth.filter(e=>e.category==='Lain-lain').reduce((s,e)=>s+parseInt(e.amount||0),0);
    document.getElementById('s-bulan-ini').textContent = fmtShort(total);
    document.getElementById('s-gaji').textContent     = fmtShort(gaji);
    document.getElementById('s-sewa').textContent     = fmtShort(sewa);
    document.getElementById('s-lain').textContent     = fmtShort(lain);
}

/* ════════════════════════════════
   DONUT CHART
════════════════════════════════ */
function updateChart() {
    const totals = {};
    filteredData.forEach(e => {
        totals[e.category] = (totals[e.category]||0) + parseInt(e.amount||0);
    });
    const grandTotal = Object.values(totals).reduce((s,v)=>s+v,0);
    const labels  = Object.keys(totals);
    const values  = labels.map(l => totals[l]);
    const colors  = labels.map(l => CAT_CFG[l]?.color || '#94a3b8');

    document.getElementById('donut-center-val').textContent = fmtShort(grandTotal);

    const canvas = document.getElementById('donutChart');
    if (donutChart) donutChart.destroy();

    if (!grandTotal) {
        canvas.style.opacity = '.3';
        document.getElementById('chart-legend').innerHTML = '<div style="font-size:12px;color:var(--text-muted);text-align:center;padding:10px;">Tidak ada data</div>';
        return;
    }
    canvas.style.opacity = '1';

    donutChart = new Chart(canvas, {
        type: 'doughnut',
        data: { labels, datasets: [{ data: values, backgroundColor: colors, borderColor: '#111827', borderWidth: 3, hoverBorderWidth: 3 }] },
        options: {
            responsive: true, maintainAspectRatio: true,
            cutout: '68%',
            plugins: {
                legend: { display: false },
                tooltip: {
                    backgroundColor: '#0d1422', borderColor: '#1e2a45', borderWidth: 1,
                    titleColor: '#8a96b2', bodyColor: '#fff', padding: 10,
                    callbacks: { label: ctx => ' ' + fmt(ctx.raw) + ' (' + Math.round((ctx.raw/grandTotal)*100) + '%)' }
                }
            }
        }
    });

    document.getElementById('chart-legend').innerHTML = labels.map(l => {
        const v = totals[l];
        const pct = grandTotal > 0 ? Math.round((v/grandTotal)*100) : 0;
        const cfg = CAT_CFG[l] || { color:'#94a3b8', icon:'ti-dots' };
        return `<div class="legend-item">
            <div class="legend-dot" style="background:${cfg.color};box-shadow:0 0 6px ${cfg.color}66;"></div>
            <span class="legend-label">${l}</span>
            <span class="legend-val">${fmtShort(v)}</span>
            <span class="legend-pct">${pct}%</span>
        </div>`;
    }).join('');

    // update period label
    const pLabels = { this_month:'Bulan ini', last_month:'Bulan lalu', this_year:'Tahun ini', all:'Semua data' };
    document.getElementById('chart-period-label').textContent = pLabels[document.getElementById('period-select').value] || '';
}

/* ════════════════════════════════
   RENDER TABLE
════════════════════════════════ */
function renderPage() {
    const start    = (currentPage-1)*PER_PAGE;
    const pageData = filteredData.slice(start, start+PER_PAGE);
    const tbody    = document.getElementById('exp-tbody');
    const total    = filteredData.reduce((s,e)=>s+parseInt(e.amount||0),0);

    document.getElementById('disp-count').textContent  = Math.min(start+PER_PAGE, filteredData.length);
    document.getElementById('total-count').textContent = filteredData.length;
    document.getElementById('footer-total').textContent = fmt(total);

    if (!filteredData.length) {
        tbody.innerHTML = `<tr><td colspan="6"><div class="empty-state"><i class="ti ti-wallet"></i><p>Belum ada pengeluaran di periode ini.</p></div></td></tr>`;
        document.getElementById('pagination').innerHTML = '';
        return;
    }

    tbody.innerHTML = pageData.map(e => {
        const cfg = CAT_CFG[e.category] || CAT_CFG['Lain-lain'];
        return `<tr>
            <td style="font-size:12px;color:var(--text-muted);">${fmtDate(e.date)}</td>
            <td>
                <div style="display:flex;align-items:center;gap:8px;">
                    <div class="cat-icon" style="background:${cfg.bg};color:${cfg.color};">
                        <i class="ti ${cfg.icon}"></i>
                    </div>
                    <span class="badge ${cfg.badgeCls}">${e.category}</span>
                </div>
            </td>
            <td>
                <div style="font-weight:600;color:var(--text-pri);">${e.description}</div>
                ${e.recipient ? `<div style="font-size:11px;color:var(--text-muted);margin-top:2px;"><i class="ti ti-user" style="font-size:10px;"></i> ${e.recipient}</div>` : ''}
                ${e.ref_no ? `<div style="font-size:10px;color:var(--text-muted);">Ref: ${e.ref_no}</div>` : ''}
            </td>
            <td style="font-family:'Space Grotesk',sans-serif;font-weight:700;color:#ef4444;">${fmt(e.amount)}</td>
            <td style="font-size:12px;color:var(--text-muted);">${e.created_by || '—'}</td>
            <td style="text-align:center;">
                <div style="display:flex;gap:5px;justify-content:center;">
                    <button class="icon-btn edit" title="Edit" onclick="openEdit(${e.id})"><i class="ti ti-edit"></i></button>
                    <button class="icon-btn del"  title="Hapus" onclick="delExp(${e.id})"><i class="ti ti-trash"></i></button>
                </div>
            </td>
        </tr>`;
    }).join('');

    renderPagination();
}

function renderPagination() {
    const totalPages = Math.ceil(filteredData.length/PER_PAGE);
    const pg = document.getElementById('pagination');
    if (totalPages <= 1) { pg.innerHTML=''; return; }
    let btns = `<button class="page-btn" onclick="goPage(${currentPage-1})" ${currentPage===1?'disabled style="opacity:.4;"':''}><i class="ti ti-chevron-left" style="font-size:12px;"></i></button>`;
    for (let i=1; i<=totalPages; i++) {
        if (i===1||i===totalPages||Math.abs(i-currentPage)<=1) btns += `<button class="page-btn ${i===currentPage?'active':''}" onclick="goPage(${i})">${i}</button>`;
        else if (Math.abs(i-currentPage)===2) btns += `<span style="color:var(--text-muted);padding:0 4px;font-size:12px;">…</span>`;
    }
    btns += `<button class="page-btn" onclick="goPage(${currentPage+1})" ${currentPage===totalPages?'disabled style="opacity:.4;"':''}><i class="ti ti-chevron-right" style="font-size:12px;"></i></button>`;
    pg.innerHTML = btns;
}

function goPage(p) {
    const total = Math.ceil(filteredData.length/PER_PAGE);
    if (p<1||p>total) return;
    currentPage = p; renderPage();
    document.querySelector('.data-card').scrollIntoView({behavior:'smooth',block:'start'});
}

/* ════════════════════════════════
   MODAL
════════════════════════════════ */
function openModal(prefill = null) {
    document.querySelector('#exp-modal form').reset();
    document.getElementById('e-id').value       = prefill?.id || '';
    document.getElementById('e-date').value     = prefill?.date || new Date().toISOString().split('T')[0];
    document.getElementById('e-desc').value      = prefill?.description || '';
    document.getElementById('e-amount').value    = prefill?.amount || '';
    document.getElementById('e-recipient').value = prefill?.recipient || '';
    document.getElementById('e-ref').value       = prefill?.ref_no || '';
    document.getElementById('e-note').value      = prefill?.note || '';
    document.getElementById('e-category').value  = prefill?.category || '';
    document.querySelectorAll('.cat-pick-btn').forEach(b => {
        b.classList.toggle('selected', b.dataset.cat === prefill?.category);
    });
    document.getElementById('modal-title-txt').innerHTML = prefill
        ? '<i class="ti ti-edit" style="color:#f97316;"></i> Edit Pengeluaran'
        : '<i class="ti ti-wallet" style="color:#f97316;"></i> Catat Pengeluaran';
    document.getElementById('exp-modal').classList.add('open');
}

function closeModal() { document.getElementById('exp-modal').classList.remove('open'); }
document.getElementById('exp-modal').addEventListener('click', function(e){ if(e.target===this) closeModal(); });

function selectCat(btn) {
    document.querySelectorAll('.cat-pick-btn').forEach(b => b.classList.remove('selected'));
    btn.classList.add('selected');
    document.getElementById('e-category').value = btn.dataset.cat;
}

function openEdit(id) {
    const e = allData.find(x => x.id == id);
    if (e) openModal(e);
}

async function saveExp(evt) {
    evt.preventDefault();
    if (!document.getElementById('e-category').value) {
        showToast('Pilih kategori pengeluaran terlebih dahulu.', 'warning'); return;
    }
    const id = document.getElementById('e-id').value;
    const payload = {
        id:          id || null,
        category:    document.getElementById('e-category').value,
        description: document.getElementById('e-desc').value,
        amount:      parseInt(document.getElementById('e-amount').value) || 0,
        date:        document.getElementById('e-date').value,
        recipient:   document.getElementById('e-recipient').value,
        ref_no:      document.getElementById('e-ref').value,
        note:        document.getElementById('e-note').value,
    };
    const endpoint = id ? '../api/expenses/update.php' : '../api/expenses/create.php';
    const btn = document.getElementById('btn-save-exp');
    btn.disabled = true; btn.textContent = 'Menyimpan...';
    try {
        const r = await apiFetch(endpoint, { method:'POST', body:JSON.stringify(payload) });
        if (r.status === 'success') {
            showToast(id ? 'Pengeluaran berhasil diperbarui.' : 'Pengeluaran berhasil dicatat!', 'success');
            closeModal();
            await loadData();
        } else {
            showToast(r.message || 'Gagal menyimpan.', 'error');
        }
    } catch(err) {
        showToast('Terjadi kesalahan. Pastikan API sudah dibuat.', 'error');
    } finally {
        btn.disabled = false;
        btn.innerHTML = '<i class="ti ti-circle-check" style="font-size:14px;"></i> Simpan';
    }
}

async function delExp(id) {
    const confirmed = await Swal.fire({
        title: 'Hapus Pengeluaran?', text: 'Data tidak dapat dikembalikan.',
        icon: 'warning', showCancelButton: true,
        confirmButtonText: 'Ya, Hapus', cancelButtonText: 'Batal',
        background: '#111827', color: '#f0f2f8',
        confirmButtonColor: '#ef4444', cancelButtonColor: '#1e2a45',
    });
    if (!confirmed.isConfirmed) return;
    try {
        const r = await apiFetch('../api/expenses/delete.php', { method:'POST', body:JSON.stringify({ id }) });
        if (r.status === 'success') { showToast('Pengeluaran berhasil dihapus.', 'success'); await loadData(); }
        else showToast(r.message || 'Gagal menghapus.', 'error');
    } catch(e) { showToast('Terjadi kesalahan.', 'error'); }
}

/* ════════════════════════════════
   INIT
════════════════════════════════ */
document.getElementById('period-select').addEventListener('change', applyFilters);
document.addEventListener('DOMContentLoaded', loadData);
</script>

<?php require_once '../includes/footer.php'; ?>