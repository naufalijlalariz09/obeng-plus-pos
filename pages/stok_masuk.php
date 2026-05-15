<?php
// pages/stok_masuk.php
require_once '../includes/header.php';
require_role(['admin', 'pimpinan']);
?>

<style>
/* ── Page layout ── */
.page-header { display:flex; justify-content:space-between; align-items:flex-start; margin-bottom:20px; flex-wrap:wrap; gap:12px; }
.page-title  { font-family:'Space Grotesk',sans-serif; font-size:20px; font-weight:700; color:#fff; display:flex; align-items:center; gap:10px; }
.page-title i { color:#10b981; }
.page-sub    { font-size:12px; color:var(--text-muted); margin-top:3px; }

/* ── Tab switcher ── */
.tab-bar { display:flex; gap:4px; background:var(--bg-card); border:1px solid var(--border); border-radius:14px; padding:4px; margin-bottom:20px; width:fit-content; }
.tab-btn {
    padding:8px 20px; border-radius:10px; border:none; cursor:pointer;
    font-family:'Plus Jakarta Sans',sans-serif; font-size:13px; font-weight:600;
    color:var(--text-muted); background:transparent; transition:all 0.18s;
    display:flex; align-items:center; gap:6px;
}
.tab-btn.active { background:linear-gradient(135deg,#1d6ae0,#7c3aed); color:#fff; box-shadow:0 4px 14px rgba(29,106,224,0.3); }
.tab-btn:not(.active):hover { color:var(--text-pri); background:rgba(255,255,255,0.04); }

/* ── Tab panels ── */
.tab-panel { display:none; }
.tab-panel.active { display:block; }

/* ── Stats row ── */
.stats-row { display:grid; grid-template-columns:repeat(4,1fr); gap:12px; margin-bottom:20px; }
@media(max-width:900px){ .stats-row{ grid-template-columns:repeat(2,1fr); } }
.stat-pill { background:var(--bg-card); border:1px solid var(--border); border-radius:16px; padding:16px; position:relative; overflow:hidden; transition:border-color 0.2s; }
.stat-pill:hover { border-color:var(--border-mid); }
.stat-pill::before { content:''; position:absolute; top:0; left:0; right:0; height:2px; }
.stat-pill.s-green::before  { background:linear-gradient(90deg,#10b981,#34d399); }
.stat-pill.s-blue::before   { background:linear-gradient(90deg,#1d6ae0,#60a5fa); }
.stat-pill.s-orange::before { background:linear-gradient(90deg,#f97316,#f59e0b); }
.stat-pill.s-purple::before { background:linear-gradient(90deg,#7c3aed,#a78bfa); }
.sp-icon { width:36px; height:36px; border-radius:10px; display:flex; align-items:center; justify-content:center; font-size:17px; margin-bottom:12px; }
.sp-val  { font-family:'Space Grotesk',sans-serif; font-size:20px; font-weight:700; color:#fff; margin-bottom:4px; }
.sp-lbl  { font-size:10px; font-weight:700; letter-spacing:1px; text-transform:uppercase; color:var(--text-muted); }

/* ── Toolbar ── */
.toolbar { display:flex; gap:8px; margin-bottom:16px; flex-wrap:wrap; align-items:center; justify-content:space-between; }
.search-wrap { position:relative; flex:1; min-width:200px; }
.search-wrap i { position:absolute; left:12px; top:50%; transform:translateY(-50%); color:var(--text-muted); font-size:15px; pointer-events:none; }
.search-input { width:100%; padding:9px 14px 9px 38px; background:var(--bg-card); border:1px solid var(--border); border-radius:12px; color:var(--text-pri); font-family:'Plus Jakarta Sans',sans-serif; font-size:13px; outline:none; transition:border-color .15s; }
.search-input::placeholder { color:var(--text-muted); }
.search-input:focus { border-color:rgba(29,106,224,.5); box-shadow:0 0 0 3px rgba(29,106,224,.08); }

.btn-add { display:flex; align-items:center; gap:6px; padding:9px 18px; background:linear-gradient(135deg,#1d6ae0,#7c3aed); border:none; border-radius:12px; color:#fff; font-family:'Plus Jakarta Sans',sans-serif; font-size:13px; font-weight:700; cursor:pointer; transition:opacity .15s; box-shadow:0 4px 16px rgba(29,106,224,.3); white-space:nowrap; }
.btn-add:hover { opacity:.9; }
.btn-add.green { background:linear-gradient(135deg,#059669,#10b981); box-shadow:0 4px 16px rgba(16,185,129,.25); }

/* ── Data card / table ── */
.data-card { background:var(--bg-card); border:1px solid var(--border); border-radius:20px; overflow:hidden; }
.data-table { width:100%; border-collapse:collapse; font-size:13px; }
.data-table thead tr { background:rgba(0,0,0,.2); border-bottom:1px solid var(--border); }
.data-table thead th { padding:12px 18px; text-align:left; font-size:10px; font-weight:700; letter-spacing:1.2px; text-transform:uppercase; color:var(--text-muted); white-space:nowrap; }
.data-table tbody tr { border-top:1px solid rgba(255,255,255,.03); transition:background .12s; }
.data-table tbody tr:hover { background:rgba(255,255,255,.03); }
.data-table tbody td { padding:13px 18px; color:var(--text-sec); vertical-align:middle; }
.table-scroll { overflow-x:auto; }

/* ── Inline cells ── */
.td-bold { font-weight:700; color:var(--text-pri); }
.td-mono { font-family:'Space Grotesk',sans-serif; font-weight:700; }
.td-green  { color:#10b981; }
.td-blue   { color:var(--accent-blue); }
.td-orange { color:#f97316; }

.badge { display:inline-flex; align-items:center; gap:4px; font-size:10px; font-weight:700; padding:3px 10px; border-radius:20px; border:1px solid transparent; }
.badge-green  { background:rgba(16,185,129,.1);  color:#10b981; border-color:rgba(16,185,129,.2); }
.badge-blue   { background:rgba(96,165,250,.1);  color:#60a5fa; border-color:rgba(96,165,250,.2); }
.badge-orange { background:rgba(249,115,22,.1);  color:#f97316; border-color:rgba(249,115,22,.2); }

/* icon action buttons */
.icon-btn { background:transparent; border:1px solid var(--border); border-radius:8px; color:var(--text-muted); width:30px; height:30px; display:inline-flex; align-items:center; justify-content:center; cursor:pointer; font-size:14px; transition:all .15s; }
.icon-btn.view:hover  { border-color:var(--accent-blue); color:var(--accent-blue); background:rgba(96,165,250,.06); }
.icon-btn.del:hover   { border-color:#ef4444; color:#ef4444; background:rgba(239,68,68,.06); }

/* skeleton */
.sk { height:13px; background:linear-gradient(90deg,#1e2a45 25%,#2d3a55 50%,#1e2a45 75%); background-size:200% 100%; animation:shimmer 1.4s infinite; border-radius:6px; display:inline-block; }
@keyframes shimmer { 0%{background-position:200% 0} 100%{background-position:-200% 0} }

/* empty state */
.empty-state { padding:60px 20px; text-align:center; color:var(--text-muted); }
.empty-state i { font-size:44px; display:block; margin-bottom:14px; }

/* table footer */
.table-footer { display:flex; justify-content:space-between; align-items:center; padding:12px 18px; border-top:1px solid var(--border); background:rgba(0,0,0,.15); flex-wrap:wrap; gap:8px; }
.footer-info { font-size:12px; color:var(--text-muted); }
.footer-info span { color:var(--text-sec); font-weight:600; margin:0 3px; }

/* ── MODAL base ── */
.modal-back { position:fixed; inset:0; background:rgba(0,0,0,.75); backdrop-filter:blur(5px); z-index:999; display:none; align-items:center; justify-content:center; padding:16px; }
.modal-back.open { display:flex; }
.modal-box { background:var(--bg-card); border:1px solid var(--border); border-radius:22px; width:100%; overflow:hidden; animation:mIn .22s ease; max-height:90vh; overflow-y:auto; }
.modal-box.sm { max-width:420px; }
.modal-box.md { max-width:600px; }
.modal-box.lg { max-width:780px; }
@keyframes mIn { from{opacity:0;transform:scale(.96) translateY(8px)} to{opacity:1;transform:scale(1) translateY(0)} }

.modal-hd { display:flex; justify-content:space-between; align-items:center; padding:16px 20px; border-bottom:1px solid var(--border); background:rgba(255,255,255,.02); position:sticky; top:0; z-index:1; }
.modal-hd h3 { font-family:'Space Grotesk',sans-serif; font-size:15px; font-weight:700; color:#fff; margin:0; display:flex; align-items:center; gap:8px; }
.modal-hd h3 i { color:var(--accent-blue); }
.modal-close-btn { background:transparent; border:1px solid var(--border); color:var(--text-muted); width:30px; height:30px; border-radius:8px; display:flex; align-items:center; justify-content:center; cursor:pointer; font-size:15px; transition:all .15s; }
.modal-close-btn:hover { border-color:#ef4444; color:#ef4444; }
.modal-bd { padding:20px; }

/* form helpers */
.form-group { margin-bottom:14px; }
.form-label { display:block; font-size:10px; font-weight:800; letter-spacing:1.2px; text-transform:uppercase; color:var(--text-muted); margin-bottom:7px; }
.form-control { width:100%; background:var(--bg-input); border:1px solid var(--border); color:var(--text-pri); font-family:'Plus Jakarta Sans',sans-serif; font-size:13px; padding:10px 14px; border-radius:10px; outline:none; transition:border-color .15s, box-shadow .15s; }
.form-control:focus { border-color:rgba(29,106,224,.5); box-shadow:0 0 0 3px rgba(29,106,224,.08); }
.form-control::placeholder { color:var(--text-muted); }
.form-control option { background:var(--bg-card); }
.form-row { display:grid; grid-template-columns:1fr 1fr; gap:12px; }
@media(max-width:480px){ .form-row{ grid-template-columns:1fr; } }
.prefix-wrap { position:relative; }
.prefix-wrap .pfx { position:absolute; left:12px; top:50%; transform:translateY(-50%); font-size:12px; font-weight:700; color:var(--text-muted); pointer-events:none; }
.prefix-wrap .form-control { padding-left:34px; }

.btn-row { display:flex; gap:10px; margin-top:6px; }
.btn-cancel { flex:1; padding:10px; background:transparent; border:1px solid var(--border); border-radius:10px; color:var(--text-sec); font-family:'Plus Jakarta Sans',sans-serif; font-size:13px; font-weight:600; cursor:pointer; transition:all .15s; }
.btn-cancel:hover { border-color:var(--border-mid); color:#fff; }
.btn-save { flex:1; padding:10px; background:linear-gradient(135deg,#1d6ae0,#7c3aed); border:none; border-radius:10px; color:#fff; font-family:'Plus Jakarta Sans',sans-serif; font-size:13px; font-weight:700; cursor:pointer; transition:opacity .15s; }
.btn-save:hover { opacity:.9; }
.btn-save:disabled { opacity:.5; cursor:not-allowed; }
.btn-save.green { background:linear-gradient(135deg,#059669,#10b981); }

/* ── NOTA PEMBELIAN: item rows ── */
.nota-items-wrap { border:1px solid var(--border); border-radius:12px; overflow:hidden; margin-bottom:14px; }
.nota-items-head { background:rgba(0,0,0,.2); display:grid; grid-template-columns:1fr 90px 120px 30px; gap:8px; padding:8px 12px; font-size:10px; font-weight:700; letter-spacing:1px; text-transform:uppercase; color:var(--text-muted); }
.nota-item-row { display:grid; grid-template-columns:1fr 90px 120px 30px; gap:8px; padding:8px 10px; border-top:1px solid rgba(255,255,255,.04); align-items:center; }
.nota-item-row select,
.nota-item-row input { width:100%; background:var(--bg-input); border:1px solid var(--border); color:var(--text-pri); font-family:'Plus Jakarta Sans',sans-serif; font-size:12px; padding:7px 10px; border-radius:8px; outline:none; transition:border-color .15s; }
.nota-item-row select:focus,
.nota-item-row input:focus { border-color:rgba(29,106,224,.4); }
.nota-item-row select option { background:var(--bg-card); }
.btn-rm-row { background:transparent; border:none; color:var(--text-muted); font-size:16px; cursor:pointer; padding:0; transition:color .15s; line-height:1; width:24px; height:24px; display:flex; align-items:center; justify-content:center; border-radius:6px; }
.btn-rm-row:hover { color:#ef4444; background:rgba(239,68,68,.08); }
.btn-add-row { display:flex; align-items:center; gap:6px; width:100%; padding:9px 14px; background:transparent; border:1px dashed var(--border-mid); border-radius:10px; color:var(--text-muted); font-family:'Plus Jakarta Sans',sans-serif; font-size:12px; font-weight:600; cursor:pointer; transition:all .15s; margin-bottom:14px; }
.btn-add-row:hover { border-color:var(--accent-blue); color:var(--accent-blue); }
.nota-subtotal-wrap { display:flex; justify-content:space-between; align-items:center; background:rgba(29,106,224,.06); border:1px solid rgba(29,106,224,.15); border-radius:10px; padding:10px 14px; }
.nota-subtotal-wrap .lbl { font-size:12px; color:var(--text-muted); }
.nota-subtotal-wrap .val { font-family:'Space Grotesk',sans-serif; font-size:18px; font-weight:700; color:var(--accent-blue); }

/* ── Detail nota popup ── */
.detail-section { margin-bottom:16px; }
.detail-section h4 { font-size:10px; font-weight:800; letter-spacing:1.2px; text-transform:uppercase; color:var(--text-muted); margin-bottom:10px; border-bottom:1px solid var(--border); padding-bottom:8px; }
.detail-row { display:flex; justify-content:space-between; align-items:center; padding:6px 0; border-bottom:1px solid rgba(255,255,255,.03); }
.detail-row:last-child { border-bottom:none; }
.dr-label { font-size:12px; color:var(--text-muted); }
.dr-val   { font-size:12px; font-weight:600; color:var(--text-pri); }
.detail-items-table { width:100%; border-collapse:collapse; font-size:12px; margin-top:4px; }
.detail-items-table th { padding:6px 10px; text-align:left; font-size:10px; font-weight:700; letter-spacing:1px; text-transform:uppercase; color:var(--text-muted); background:rgba(0,0,0,.2); }
.detail-items-table td { padding:8px 10px; color:var(--text-sec); border-top:1px solid rgba(255,255,255,.03); }
.detail-items-table tr:hover td { background:rgba(255,255,255,.02); }
</style>

<!-- ═══ PAGE HEADER ═══ -->
<div class="page-header">
    <div>
        <div class="page-title"><i class="ti ti-package-import"></i> Stok Masuk</div>
        <div class="page-sub">Catat pembelian barang per-produk maupun per-nota pembelian</div>
    </div>
</div>

<!-- ═══ STATS ═══ -->
<div class="stats-row">
    <div class="stat-pill s-green">
        <div class="sp-icon" style="background:rgba(16,185,129,.12);color:#10b981;"><i class="ti ti-package-import"></i></div>
        <div class="sp-val" id="s-total-nota">—</div>
        <div class="sp-lbl">Total Nota Masuk</div>
    </div>
    <div class="stat-pill s-blue">
        <div class="sp-icon" style="background:rgba(96,165,250,.12);color:#60a5fa;"><i class="ti ti-box"></i></div>
        <div class="sp-val" id="s-total-unit">—</div>
        <div class="sp-lbl">Unit Masuk Bulan Ini</div>
    </div>
    <div class="stat-pill s-orange">
        <div class="sp-icon" style="background:rgba(249,115,22,.12);color:#f97316;"><i class="ti ti-cash"></i></div>
        <div class="sp-val" id="s-total-nilai">—</div>
        <div class="sp-lbl">Nilai Pembelian Bulan Ini</div>
    </div>
    <div class="stat-pill s-purple">
        <div class="sp-icon" style="background:rgba(124,58,237,.12);color:#a78bfa;"><i class="ti ti-truck"></i></div>
        <div class="sp-val" id="s-total-supplier">—</div>
        <div class="sp-lbl">Total Supplier</div>
    </div>
</div>

<!-- ═══ TABS ═══ -->
<div class="tab-bar">
    <button class="tab-btn active" onclick="switchTab('nota', this)">
        <i class="ti ti-file-invoice" style="font-size:14px;"></i> Per Nota Pembelian
    </button>
    <button class="tab-btn" onclick="switchTab('quick', this)">
        <i class="ti ti-bolt" style="font-size:14px;"></i> Tambah Cepat (Per Produk)
    </button>
</div>

<!-- ══════════════════════════════════
     TAB 1 – NOTA PEMBELIAN
══════════════════════════════════ -->
<div class="tab-panel active" id="tab-nota">
    <div class="toolbar">
        <div class="search-wrap">
            <i class="ti ti-search"></i>
            <input type="text" class="search-input" id="nota-search" placeholder="Cari no. nota, supplier..." oninput="filterNota()">
        </div>
        <button class="btn-add" onclick="openNotaModal()">
            <i class="ti ti-plus" style="font-size:14px;"></i> Buat Nota Pembelian
        </button>
    </div>

    <div class="data-card">
        <div class="table-scroll">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>No. Nota</th>
                        <th>Tanggal</th>
                        <th>Supplier</th>
                        <th>Jumlah Item</th>
                        <th>Total Nilai</th>
                        <th>Keterangan</th>
                        <th style="text-align:center;">Aksi</th>
                    </tr>
                </thead>
                <tbody id="nota-tbody">
                    <?php for($i=0;$i<5;$i++): ?>
                    <tr>
                        <td><span class="sk" style="width:90px;"></span></td>
                        <td><span class="sk" style="width:80px;"></span></td>
                        <td><span class="sk" style="width:110px;"></span></td>
                        <td><span class="sk" style="width:50px;"></span></td>
                        <td><span class="sk" style="width:100px;"></span></td>
                        <td><span class="sk" style="width:120px;"></span></td>
                        <td style="text-align:center;"><span class="sk" style="width:50px;display:block;margin:auto;"></span></td>
                    </tr>
                    <?php endfor; ?>
                </tbody>
            </table>
        </div>
        <div class="table-footer">
            <div class="footer-info">Total: <span id="nota-count">0</span> nota</div>
        </div>
    </div>
</div>

<!-- ══════════════════════════════════
     TAB 2 – TAMBAH CEPAT
══════════════════════════════════ -->
<div class="tab-panel" id="tab-quick">
    <div class="toolbar">
        <div class="search-wrap">
            <i class="ti ti-search"></i>
            <input type="text" class="search-input" id="quick-search" placeholder="Cari nama produk..." oninput="filterQuick()">
        </div>
        <button class="btn-add green" onclick="openQuickModal()">
            <i class="ti ti-plus" style="font-size:14px;"></i> Tambah Stok Cepat
        </button>
    </div>

    <div class="data-card">
        <div class="table-scroll">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Produk</th>
                        <th>Stok Saat Ini</th>
                        <th>Stok Min.</th>
                        <th>Terakhir Diisi</th>
                        <th>Total Masuk</th>
                        <th style="text-align:center;">Aksi</th>
                    </tr>
                </thead>
                <tbody id="quick-tbody">
                    <?php for($i=0;$i<5;$i++): ?>
                    <tr>
                        <td><span class="sk" style="width:130px;"></span></td>
                        <td><span class="sk" style="width:60px;"></span></td>
                        <td><span class="sk" style="width:50px;"></span></td>
                        <td><span class="sk" style="width:90px;"></span></td>
                        <td><span class="sk" style="width:70px;"></span></td>
                        <td style="text-align:center;"><span class="sk" style="width:60px;display:block;margin:auto;"></span></td>
                    </tr>
                    <?php endfor; ?>
                </tbody>
            </table>
        </div>
        <div class="table-footer">
            <div class="footer-info">Total produk: <span id="quick-count">0</span></div>
        </div>
    </div>
</div>

<!-- ══════════════════════════════════
     MODAL – Buat Nota Pembelian
══════════════════════════════════ -->
<div class="modal-back" id="nota-modal">
    <div class="modal-box lg">
        <div class="modal-hd">
            <h3><i class="ti ti-file-invoice"></i> Buat Nota Pembelian</h3>
            <button class="modal-close-btn" onclick="closeNotaModal()"><i class="ti ti-x"></i></button>
        </div>
        <form onsubmit="saveNota(event)" class="modal-bd">
            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">No. Nota / Ref</label>
                    <input type="text" id="n-ref" class="form-control" placeholder="PO-2025-001">
                </div>
                <div class="form-group">
                    <label class="form-label">Tanggal Pembelian</label>
                    <input type="date" id="n-date" class="form-control" value="<?php echo date('Y-m-d'); ?>">
                </div>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">Nama Supplier</label>
                    <input type="text" id="n-supplier" class="form-control" placeholder="Nama distributor / toko" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Kontak Supplier</label>
                    <input type="text" id="n-supplier-phone" class="form-control" placeholder="No. HP / Telepon">
                </div>
            </div>

            <!-- Item rows -->
            <div class="form-group">
                <label class="form-label">Daftar Produk <span style="color:#ef4444;">*</span></label>
                <div class="nota-items-wrap">
                    <div class="nota-items-head">
                        <div>Produk</div><div>Qty</div><div>Harga Beli (Rp)</div><div></div>
                    </div>
                    <div id="nota-item-rows">
                        <!-- rows injected by JS -->
                    </div>
                </div>
                <button type="button" class="btn-add-row" onclick="addNotaRow()">
                    <i class="ti ti-plus" style="font-size:13px;"></i> Tambah Produk
                </button>
                <div class="nota-subtotal-wrap">
                    <span class="lbl">Total Nilai Pembelian</span>
                    <span class="val" id="nota-grand-total">Rp 0</span>
                </div>
            </div>

            <div class="form-group">
                <label class="form-label">Keterangan</label>
                <input type="text" id="n-note" class="form-control" placeholder="Catatan pembelian (opsional)">
            </div>

            <div class="btn-row">
                <button type="button" class="btn-cancel" onclick="closeNotaModal()">Batal</button>
                <button type="submit" class="btn-save" id="btn-save-nota">
                    <i class="ti ti-circle-check" style="font-size:14px;"></i> Simpan & Update Stok
                </button>
            </div>
        </form>
    </div>
</div>

<!-- ══════════════════════════════════
     MODAL – Tambah Cepat per Produk
══════════════════════════════════ -->
<div class="modal-back" id="quick-modal">
    <div class="modal-box sm">
        <div class="modal-hd">
            <h3><i class="ti ti-bolt" style="color:#10b981;"></i> Tambah Stok Cepat</h3>
            <button class="modal-close-btn" onclick="closeQuickModal()"><i class="ti ti-x"></i></button>
        </div>
        <form onsubmit="saveQuick(event)" class="modal-bd">
            <div class="form-group">
                <label class="form-label">Pilih Produk <span style="color:#ef4444;">*</span></label>
                <select id="q-product" class="form-control" required onchange="onQuickProductChange()">
                    <option value="">-- Pilih Produk --</option>
                </select>
            </div>
            <div id="q-stock-info" style="display:none; background:rgba(96,165,250,.08); border:1px solid rgba(96,165,250,.2); border-radius:10px; padding:10px 14px; margin-bottom:14px; font-size:12px; color:var(--text-sec);">
                Stok saat ini: <strong id="q-cur-stock" style="color:#fff;"></strong>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">Jumlah Ditambah <span style="color:#ef4444;">*</span></label>
                    <input type="number" id="q-qty" class="form-control" placeholder="10" min="1" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Harga Beli Baru (Rp)</label>
                    <div class="prefix-wrap">
                        <span class="pfx">Rp</span>
                        <input type="number" id="q-cost" class="form-control" placeholder="0" min="0">
                    </div>
                </div>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">Supplier</label>
                    <input type="text" id="q-supplier" class="form-control" placeholder="Nama supplier">
                </div>
                <div class="form-group">
                    <label class="form-label">Tanggal</label>
                    <input type="date" id="q-date" class="form-control" value="<?php echo date('Y-m-d'); ?>">
                </div>
            </div>
            <div class="form-group">
                <label class="form-label">Keterangan</label>
                <input type="text" id="q-note" class="form-control" placeholder="Catatan (opsional)">
            </div>
            <div class="btn-row">
                <button type="button" class="btn-cancel" onclick="closeQuickModal()">Batal</button>
                <button type="submit" class="btn-save green" id="btn-save-quick">
                    <i class="ti ti-package-import" style="font-size:14px;"></i> Simpan
                </button>
            </div>
        </form>
    </div>
</div>

<!-- ══════════════════════════════════
     MODAL – Detail Nota
══════════════════════════════════ -->
<div class="modal-back" id="detail-modal">
    <div class="modal-box md">
        <div class="modal-hd">
            <h3><i class="ti ti-file-invoice"></i> Detail Nota Pembelian</h3>
            <button class="modal-close-btn" onclick="closeDetailModal()"><i class="ti ti-x"></i></button>
        </div>
        <div class="modal-bd" id="detail-content">
            <!-- filled by JS -->
        </div>
    </div>
</div>

<script>
/* ════════════════════════════════
   STATE & HELPERS
════════════════════════════════ */
let allNota = [], allProducts = [], allQuickLog = [];

function fmt(n) { return 'Rp ' + parseInt(n||0).toLocaleString('id-ID'); }
function fmtDate(s) { return new Date(s).toLocaleDateString('id-ID',{day:'2-digit',month:'short',year:'numeric'}); }

function switchTab(tab, btn) {
    document.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));
    document.querySelectorAll('.tab-panel').forEach(p => p.classList.remove('active'));
    btn.classList.add('active');
    document.getElementById('tab-' + tab).classList.add('active');
}

/* ════════════════════════════════
   LOAD DATA
════════════════════════════════ */
async function loadAll() {
    await loadProducts();
    await loadNota();
    await loadQuickLog();
    updateStats();
    populateQuickSelect();
}

async function loadProducts() {
    try {
        const r = await apiFetch('../api/products/get_all.php');
        if (r.status === 'success') allProducts = r.data.filter(p => p.type !== 'jasa');
    } catch(e) { allProducts = []; }
}

async function loadNota() {
    try {
        const r = await apiFetch('../api/stock/get_purchase_orders.php');
        if (r.status === 'success') { allNota = r.data; renderNota(allNota); }
    } catch(e) {
        // API belum ada – tampilkan empty state
        allNota = [];
        renderNota([]);
    }
}

async function loadQuickLog() {
    try {
        const r = await apiFetch('../api/stock/get_quick_log.php');
        if (r.status === 'success') { allQuickLog = r.data; renderQuick(r.data); }
    } catch(e) {
        allQuickLog = [];
        renderQuickFromProducts();
    }
}

/* fallback: tampilkan tabel produk jika API log belum ada */
function renderQuickFromProducts() {
    renderQuick(allProducts.map(p => ({
        product_id: p.id, product_name: p.name, category_name: p.category_name,
        current_stock: p.stock, min_stock: p.min_stock,
        last_restock: null, total_in: 0
    })));
}

function updateStats() {
    document.getElementById('s-total-nota').textContent = allNota.length;
    const now = new Date();
    const thisMonth = allNota.filter(n => {
        const d = new Date(n.date);
        return d.getMonth()===now.getMonth() && d.getFullYear()===now.getFullYear();
    });
    const units = thisMonth.reduce((s,n) => s + parseInt(n.total_qty||0), 0);
    const nilai = thisMonth.reduce((s,n) => s + parseInt(n.grand_total||0), 0);
    const suppliers = [...new Set(allNota.map(n => n.supplier_name).filter(Boolean))];
    document.getElementById('s-total-unit').textContent = units + ' unit';
    document.getElementById('s-total-nilai').textContent = nilai >= 1000000
        ? 'Rp' + (nilai/1000000).toFixed(1) + 'jt'
        : fmt(nilai);
    document.getElementById('s-total-supplier').textContent = suppliers.length;
}

/* ════════════════════════════════
   RENDER NOTA TABLE
════════════════════════════════ */
function renderNota(data) {
    const tbody = document.getElementById('nota-tbody');
    document.getElementById('nota-count').textContent = data.length;
    if (!data.length) {
        tbody.innerHTML = `<tr><td colspan="7"><div class="empty-state"><i class="ti ti-file-invoice"></i><p>Belum ada nota pembelian.</p></div></td></tr>`;
        return;
    }
    tbody.innerHTML = data.map(n => `<tr>
        <td class="td-bold td-blue td-mono">${n.ref_no || '—'}</td>
        <td style="font-size:12px;color:var(--text-muted);">${fmtDate(n.date)}</td>
        <td>
            <div class="td-bold">${n.supplier_name}</div>
            ${n.supplier_phone ? `<div style="font-size:11px;color:var(--text-muted);">${n.supplier_phone}</div>` : ''}
        </td>
        <td><span class="badge badge-blue">${n.total_qty || 0} unit</span></td>
        <td class="td-bold td-green td-mono">${fmt(n.grand_total)}</td>
        <td style="font-size:12px;color:var(--text-muted);max-width:160px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">${n.note || '—'}</td>
        <td style="text-align:center;">
            <button class="icon-btn view" title="Lihat Detail" onclick="showDetail(${n.id})"><i class="ti ti-eye"></i></button>
        </td>
    </tr>`).join('');
}

function filterNota() {
    const q = document.getElementById('nota-search').value.toLowerCase();
    renderNota(allNota.filter(n =>
        (n.ref_no||'').toLowerCase().includes(q) ||
        (n.supplier_name||'').toLowerCase().includes(q)
    ));
}

/* ════════════════════════════════
   RENDER QUICK TABLE
════════════════════════════════ */
function renderQuick(data) {
    const tbody = document.getElementById('quick-tbody');
    document.getElementById('quick-count').textContent = data.length;
    if (!data.length) {
        tbody.innerHTML = `<tr><td colspan="6"><div class="empty-state"><i class="ti ti-package"></i><p>Belum ada produk.</p></div></td></tr>`;
        return;
    }
    tbody.innerHTML = data.map(p => {
        const isLow = parseInt(p.current_stock) <= parseInt(p.min_stock||5);
        return `<tr>
            <td>
                <div class="td-bold">${p.product_name}</div>
                <div style="font-size:10px;font-weight:700;color:var(--accent-orange);text-transform:uppercase;">${p.category_name||'UMUM'}</div>
            </td>
            <td>
                <span class="badge ${isLow?'badge-orange':'badge-green'}">
                    ${isLow?'<i class="ti ti-alert-triangle" style="font-size:10px;"></i>':''} ${p.current_stock} unit
                </span>
            </td>
            <td style="font-size:12px;color:var(--text-muted);">${p.min_stock||5} unit</td>
            <td style="font-size:12px;color:var(--text-muted);">${p.last_restock ? fmtDate(p.last_restock) : '—'}</td>
            <td class="td-bold" style="color:var(--accent-blue);">${p.total_in ? p.total_in + ' unit' : '—'}</td>
            <td style="text-align:center;">
                <button class="icon-btn view" style="color:#10b981;border-color:rgba(16,185,129,.25);" title="Tambah Stok"
                    onclick="openQuickModal(${p.product_id})">
                    <i class="ti ti-plus"></i>
                </button>
            </td>
        </tr>`;
    }).join('');
}

function filterQuick() {
    const q = document.getElementById('quick-search').value.toLowerCase();
    const filtered = allProducts.filter(p => p.name.toLowerCase().includes(q));
    renderQuick(filtered.map(p => ({
        product_id: p.id, product_name: p.name, category_name: p.category_name,
        current_stock: p.stock, min_stock: p.min_stock,
        last_restock: null, total_in: 0
    })));
}

/* ════════════════════════════════
   NOTA MODAL
════════════════════════════════ */
let notaRowCount = 0;

function openNotaModal() {
    notaRowCount = 0;
    document.getElementById('nota-item-rows').innerHTML = '';
    document.getElementById('nota-grand-total').textContent = 'Rp 0';
    document.querySelector('#nota-modal form').reset();
    document.getElementById('n-date').value = new Date().toISOString().split('T')[0];
    addNotaRow();
    document.getElementById('nota-modal').classList.add('open');
}
function closeNotaModal() { document.getElementById('nota-modal').classList.remove('open'); }
document.getElementById('nota-modal').addEventListener('click', function(e){ if(e.target===this) closeNotaModal(); });

function addNotaRow() {
    const id = ++notaRowCount;
    const options = allProducts.map(p => `<option value="${p.id}" data-cost="${p.cost_price||0}">${p.name}</option>`).join('');
    const row = document.createElement('div');
    row.className = 'nota-item-row';
    row.id = 'nrow-' + id;
    row.innerHTML = `
        <select onchange="recalcNota()" class="nrow-product">
            <option value="">-- Pilih Produk --</option>${options}
        </select>
        <input type="number" class="nrow-qty" placeholder="1" min="1" value="1" oninput="recalcNota()">
        <input type="number" class="nrow-cost" placeholder="0" min="0" oninput="recalcNota()">
        <button type="button" class="btn-rm-row" onclick="removeNotaRow(${id})"><i class="ti ti-x"></i></button>
    `;
    /* auto-fill harga beli saat produk dipilih */
    row.querySelector('.nrow-product').addEventListener('change', function(){
        const opt = this.options[this.selectedIndex];
        const cost = opt?.dataset?.cost || 0;
        row.querySelector('.nrow-cost').value = cost;
        recalcNota();
    });
    document.getElementById('nota-item-rows').appendChild(row);
}

function removeNotaRow(id) {
    const el = document.getElementById('nrow-' + id);
    if (el) el.remove();
    if (!document.getElementById('nota-item-rows').children.length) addNotaRow();
    recalcNota();
}

function recalcNota() {
    let total = 0;
    document.querySelectorAll('.nota-item-row').forEach(row => {
        const qty  = parseInt(row.querySelector('.nrow-qty').value) || 0;
        const cost = parseInt(row.querySelector('.nrow-cost').value) || 0;
        total += qty * cost;
    });
    document.getElementById('nota-grand-total').textContent = fmt(total);
}

async function saveNota(e) {
    e.preventDefault();
    const rows = [...document.querySelectorAll('.nota-item-row')];
    const items = rows.map(r => ({
        product_id: r.querySelector('.nrow-product').value,
        qty:  parseInt(r.querySelector('.nrow-qty').value) || 0,
        cost: parseInt(r.querySelector('.nrow-cost').value) || 0,
    })).filter(i => i.product_id && i.qty > 0);

    if (!items.length) { showToast('Tambahkan minimal 1 produk.', 'warning'); return; }

    const payload = {
        ref_no:         document.getElementById('n-ref').value,
        date:           document.getElementById('n-date').value,
        supplier_name:  document.getElementById('n-supplier').value,
        supplier_phone: document.getElementById('n-supplier-phone').value,
        note:           document.getElementById('n-note').value,
        items
    };

    const btn = document.getElementById('btn-save-nota');
    btn.disabled = true; btn.textContent = 'Menyimpan...';

    try {
        const r = await apiFetch('../api/stock/create_purchase_order.php', { method:'POST', body: JSON.stringify(payload) });
        if (r.status === 'success') {
            showToast('Nota pembelian berhasil disimpan & stok diperbarui!', 'success');
            closeNotaModal();
            await loadAll();
        } else {
            showToast(r.message || 'Gagal menyimpan nota.', 'error');
        }
    } catch(err) {
        showToast('Terjadi kesalahan. Pastikan API sudah dibuat.', 'error');
    } finally {
        btn.disabled = false;
        btn.innerHTML = '<i class="ti ti-circle-check" style="font-size:14px;"></i> Simpan & Update Stok';
    }
}

/* ════════════════════════════════
   QUICK MODAL
════════════════════════════════ */
function populateQuickSelect() {
    const sel = document.getElementById('q-product');
    sel.innerHTML = '<option value="">-- Pilih Produk --</option>' +
        allProducts.map(p => `<option value="${p.id}" data-stock="${p.stock}" data-cost="${p.cost_price||0}">${p.name} (stok: ${p.stock})</option>`).join('');
}

function openQuickModal(preSelectId = null) {
    document.querySelector('#quick-modal form').reset();
    document.getElementById('q-date').value = new Date().toISOString().split('T')[0];
    document.getElementById('q-stock-info').style.display = 'none';
    if (preSelectId) {
        document.getElementById('q-product').value = preSelectId;
        onQuickProductChange();
    }
    document.getElementById('quick-modal').classList.add('open');
}
function closeQuickModal() { document.getElementById('quick-modal').classList.remove('open'); }
document.getElementById('quick-modal').addEventListener('click', function(e){ if(e.target===this) closeQuickModal(); });

function onQuickProductChange() {
    const sel = document.getElementById('q-product');
    const opt = sel.options[sel.selectedIndex];
    if (!opt || !opt.value) { document.getElementById('q-stock-info').style.display = 'none'; return; }
    document.getElementById('q-cur-stock').textContent = (opt.dataset.stock || 0) + ' unit';
    document.getElementById('q-cost').value = opt.dataset.cost || 0;
    document.getElementById('q-stock-info').style.display = 'block';
}

async function saveQuick(e) {
    e.preventDefault();
    const payload = {
        product_id: document.getElementById('q-product').value,
        qty:        parseInt(document.getElementById('q-qty').value) || 0,
        cost:       parseInt(document.getElementById('q-cost').value) || 0,
        supplier:   document.getElementById('q-supplier').value,
        date:       document.getElementById('q-date').value,
        note:       document.getElementById('q-note').value,
    };
    if (!payload.product_id || payload.qty < 1) { showToast('Pilih produk dan masukkan jumlah.', 'warning'); return; }

    const btn = document.getElementById('btn-save-quick');
    btn.disabled = true; btn.textContent = 'Menyimpan...';

    try {
        const r = await apiFetch('../api/stock/quick_add.php', { method:'POST', body: JSON.stringify(payload) });
        if (r.status === 'success') {
            showToast('Stok berhasil ditambahkan!', 'success');
            closeQuickModal();
            await loadAll();
        } else {
            showToast(r.message || 'Gagal menyimpan.', 'error');
        }
    } catch(err) {
        showToast('Terjadi kesalahan. Pastikan API sudah dibuat.', 'error');
    } finally {
        btn.disabled = false;
        btn.innerHTML = '<i class="ti ti-package-import" style="font-size:14px;"></i> Simpan';
    }
}

/* ════════════════════════════════
   DETAIL MODAL
════════════════════════════════ */
function showDetail(id) {
    const n = allNota.find(x => x.id == id);
    if (!n) return;
    document.getElementById('detail-content').innerHTML = `
        <div class="detail-section">
            <h4>Informasi Nota</h4>
            <div class="detail-row"><span class="dr-label">No. Nota</span><span class="dr-val td-blue td-mono">${n.ref_no||'—'}</span></div>
            <div class="detail-row"><span class="dr-label">Tanggal</span><span class="dr-val">${fmtDate(n.date)}</span></div>
            <div class="detail-row"><span class="dr-label">Supplier</span><span class="dr-val">${n.supplier_name}</span></div>
            ${n.supplier_phone ? `<div class="detail-row"><span class="dr-label">Kontak</span><span class="dr-val">${n.supplier_phone}</span></div>` : ''}
            <div class="detail-row"><span class="dr-label">Keterangan</span><span class="dr-val">${n.note||'—'}</span></div>
        </div>
        <div class="detail-section">
            <h4>Daftar Produk</h4>
            <table class="detail-items-table">
                <thead><tr><th>Produk</th><th>Qty</th><th>Harga Beli</th><th>Subtotal</th></tr></thead>
                <tbody>
                ${(n.items||[]).map(i => `<tr>
                    <td style="color:var(--text-pri);font-weight:600;">${i.product_name}</td>
                    <td>${i.qty} unit</td>
                    <td>${fmt(i.cost_price)}</td>
                    <td style="color:#10b981;font-weight:700;">${fmt(i.qty * i.cost_price)}</td>
                </tr>`).join('')}
                </tbody>
            </table>
        </div>
        <div style="display:flex;justify-content:space-between;align-items:center;background:rgba(16,185,129,.06);border:1px solid rgba(16,185,129,.15);border-radius:10px;padding:12px 16px;">
            <div>
                <div style="font-size:10px;font-weight:700;letter-spacing:1px;text-transform:uppercase;color:var(--text-muted);margin-bottom:3px;">Total Nilai Pembelian</div>
                <div style="font-size:10px;color:var(--text-muted);">${n.total_qty||0} unit dari ${(n.items||[]).length} produk</div>
            </div>
            <div style="font-family:'Space Grotesk',sans-serif;font-size:22px;font-weight:700;color:#10b981;">${fmt(n.grand_total)}</div>
        </div>
    `;
    document.getElementById('detail-modal').classList.add('open');
}
function closeDetailModal() { document.getElementById('detail-modal').classList.remove('open'); }
document.getElementById('detail-modal').addEventListener('click', function(e){ if(e.target===this) closeDetailModal(); });

/* ════════════════════════════════
   INIT
════════════════════════════════ */
document.addEventListener('DOMContentLoaded', loadAll);
</script>

<?php require_once '../includes/footer.php'; ?>