<?php
// pages/jasa.php
require_once '../includes/header.php';
require_role(['admin']);
?>

<style>
.page-header { display:flex; justify-content:space-between; align-items:flex-start; margin-bottom:20px; flex-wrap:wrap; gap:12px; }
.page-title { font-family:'Space Grotesk',sans-serif; font-size:20px; font-weight:700; color:#fff; }

.btn-add { display:flex; align-items:center; gap:6px; padding:9px 18px; background:linear-gradient(135deg,#1d6ae0,#7c3aed); border:none; border-radius:12px; color:#fff; font-family:'Plus Jakarta Sans',sans-serif; font-size:13px; font-weight:700; cursor:pointer; transition:opacity 0.15s; box-shadow:0 4px 16px rgba(29,106,224,0.3); }
.btn-add:hover { opacity:0.9; }

/* Stats row */
.stats-row { display:grid; grid-template-columns:repeat(4,1fr); gap:12px; margin-bottom:20px; }
@media (max-width:900px) { .stats-row { grid-template-columns:repeat(2,1fr); } }
@media (max-width:480px) { .stats-row { grid-template-columns:1fr 1fr; } }

.stat-pill { background:var(--bg-card); border:1px solid var(--border); border-radius:16px; padding:16px; text-align:center; }
.stat-pill-val { font-family:'Space Grotesk',sans-serif; font-size:22px; font-weight:700; color:#fff; margin-bottom:4px; }
.stat-pill-label { font-size:10px; font-weight:700; letter-spacing:1px; text-transform:uppercase; color:var(--text-muted); }

/* Search */
.search-wrap { position:relative; margin-bottom:16px; }
.search-wrap i { position:absolute; left:14px; top:50%; transform:translateY(-50%); color:var(--text-muted); font-size:15px; pointer-events:none; }
.search-input { width:100%; padding:10px 14px 10px 40px; background:var(--bg-card); border:1px solid var(--border); border-radius:12px; color:var(--text-pri); font-family:'Plus Jakarta Sans',sans-serif; font-size:13px; outline:none; transition:border-color 0.15s; }
.search-input::placeholder { color:var(--text-muted); }
.search-input:focus { border-color:rgba(29,106,224,0.5); box-shadow:0 0 0 3px rgba(29,106,224,0.08); }

/* Table */
.data-card { background:var(--bg-card); border:1px solid var(--border); border-radius:20px; overflow:hidden; }
.data-table { width:100%; border-collapse:collapse; font-size:13px; }
.data-table thead tr { background:rgba(0,0,0,0.2); border-bottom:1px solid var(--border); }
.data-table thead th { padding:12px 18px; text-align:left; font-size:10px; font-weight:700; letter-spacing:1.2px; text-transform:uppercase; color:var(--text-muted); white-space:nowrap; }
.data-table tbody tr { border-top:1px solid rgba(255,255,255,0.03); transition:background 0.12s; }
.data-table tbody tr:hover { background:rgba(255,255,255,0.03); }
.data-table tbody td { padding:14px 18px; color:var(--text-sec); vertical-align:middle; }

.cat-badge { display:inline-flex; align-items:center; font-size:10px; font-weight:700; padding:3px 10px; border-radius:20px; border:1px solid transparent; }
.cat-instalasi { background:rgba(96,165,250,0.1); color:#60a5fa; border-color:rgba(96,165,250,0.2); }
.cat-servis { background:rgba(249,115,22,0.1); color:#f97316; border-color:rgba(249,115,22,0.2); }
.cat-konsultasi { background:rgba(167,139,250,0.1); color:#a78bfa; border-color:rgba(167,139,250,0.2); }
.cat-pemasangan { background:rgba(16,185,129,0.1); color:#10b981; border-color:rgba(16,185,129,0.2); }

.active-badge { display:inline-flex; align-items:center; gap:5px; font-size:10px; font-weight:700; padding:3px 10px; border-radius:20px; background:rgba(16,185,129,0.1); color:#10b981; border:1px solid rgba(16,185,129,0.2); }

.action-btns { display:flex; align-items:center; justify-content:center; gap:6px; }
.icon-btn { background:transparent; border:1px solid var(--border); border-radius:8px; color:var(--text-muted); width:30px; height:30px; display:flex; align-items:center; justify-content:center; cursor:pointer; font-size:14px; transition:all 0.15s; }
.icon-btn.edit:hover { border-color:#60a5fa; color:#60a5fa; background:rgba(96,165,250,0.06); }
.icon-btn.del:hover { border-color:#ef4444; color:#ef4444; background:rgba(239,68,68,0.06); }

/* Loading skeleton */
.sk-cell { height:13px; background:linear-gradient(90deg,#1e2a45 25%,#2d3a55 50%,#1e2a45 75%); background-size:200% 100%; animation:shimmer 1.4s infinite; border-radius:6px; display:inline-block; }
@keyframes shimmer { 0%{background-position:200% 0} 100%{background-position:-200% 0} }

/* Empty state */
.empty-state { padding:60px 20px; text-align:center; color:var(--text-muted); }
.empty-state i { font-size:44px; display:block; margin-bottom:14px; }
.empty-state p { font-size:13px; }

/* Modal */
.modal-backdrop { position:fixed; inset:0; background:rgba(0,0,0,0.7); backdrop-filter:blur(4px); z-index:999; display:none; align-items:center; justify-content:center; padding:16px; }
.modal-backdrop.open { display:flex; }
.modal-inner { background:var(--bg-card); border:1px solid var(--border); border-radius:22px; width:100%; max-width:460px; overflow:hidden; animation:modalIn 0.22s ease; max-height:90vh; overflow-y:auto; }
@keyframes modalIn { from{opacity:0;transform:scale(0.96) translateY(8px)} to{opacity:1;transform:scale(1) translateY(0)} }
.modal-hd { display:flex; justify-content:space-between; align-items:center; padding:16px 20px; border-bottom:1px solid var(--border); background:rgba(255,255,255,0.02); position:sticky; top:0; }
.modal-hd h3 { font-family:'Space Grotesk',sans-serif; font-size:15px; font-weight:700; color:#fff; margin:0; display:flex; align-items:center; gap:8px; }
.modal-hd h3 i { color:var(--accent-blue); }
.modal-close { background:transparent; border:1px solid var(--border); color:var(--text-muted); width:30px; height:30px; border-radius:8px; display:flex; align-items:center; justify-content:center; cursor:pointer; font-size:15px; transition:all 0.15s; }
.modal-close:hover { border-color:#ef4444; color:#ef4444; }
.modal-body { padding:20px; }
.form-group { margin-bottom:14px; }
.form-label { display:block; font-size:10px; font-weight:800; letter-spacing:1.2px; text-transform:uppercase; color:var(--text-muted); margin-bottom:7px; }
.form-control { width:100%; background:var(--bg-input); border:1px solid var(--border); color:var(--text-pri); font-family:'Plus Jakarta Sans',sans-serif; font-size:13px; padding:10px 14px; border-radius:10px; outline:none; transition:border-color 0.15s, box-shadow 0.15s; }
.form-control:focus { border-color:rgba(29,106,224,0.5); box-shadow:0 0 0 3px rgba(29,106,224,0.08); }
.form-control::placeholder { color:var(--text-muted); }
.price-wrap { position:relative; }
.price-wrap .prefix { position:absolute; left:12px; top:50%; transform:translateY(-50%); font-size:12px; font-weight:700; color:var(--text-muted); pointer-events:none; }
.price-wrap .form-control { padding-left:36px; }
.form-row { display:grid; grid-template-columns:1fr 1fr; gap:12px; }
.modal-footer-btns { display:flex; gap:10px; margin-top:6px; }
.btn-cancel { flex:1; padding:10px; background:transparent; border:1px solid var(--border); border-radius:10px; color:var(--text-sec); font-family:'Plus Jakarta Sans',sans-serif; font-size:13px; font-weight:600; cursor:pointer; transition:all 0.15s; }
.btn-cancel:hover { border-color:var(--border-mid); color:#fff; }
.btn-save { flex:1; padding:10px; background:linear-gradient(135deg,#1d6ae0,#7c3aed); border:none; border-radius:10px; color:#fff; font-family:'Plus Jakarta Sans',sans-serif; font-size:13px; font-weight:700; cursor:pointer; transition:opacity 0.15s; }
.btn-save:hover { opacity:0.9; }
.btn-save:disabled { opacity:0.5; cursor:not-allowed; }
</style>

<div class="page-header">
    <div>
        <div class="page-title">Manajemen Jasa</div>
        <div style="font-size:12px;color:var(--text-muted);margin-top:3px;">Kelola tarif pemasangan dan servis audio kendaraan</div>
    </div>
    <button class="btn-add" onclick="openJasaModal()">
        <i class="ti ti-plus" style="font-size:14px;"></i> Tambah Jasa
    </button>
</div>

<!-- Stats -->
<div class="stats-row">
    <div class="stat-pill">
        <div class="stat-pill-val" id="count-total">—</div>
        <div class="stat-pill-label">Total Jasa</div>
    </div>
    <div class="stat-pill">
        <div class="stat-pill-val" style="color:#10b981;" id="count-active">—</div>
        <div class="stat-pill-label">Aktif</div>
    </div>
    <div class="stat-pill">
        <div class="stat-pill-val" style="color:var(--accent-blue);" id="count-avg">—</div>
        <div class="stat-pill-label">Rata-rata Tarif</div>
    </div>
    <div class="stat-pill">
        <div class="stat-pill-val" style="color:#f97316;" id="count-top">—</div>
        <div class="stat-pill-label">Tarif Tertinggi</div>
    </div>
</div>

<!-- Search -->
<div class="search-wrap">
    <i class="ti ti-search"></i>
    <input type="text" id="j-search" class="search-input" oninput="filterJasa()" placeholder="Cari nama jasa...">
</div>

<!-- Table -->
<div class="data-card">
    <table class="data-table">
        <thead>
            <tr>
                <th>#</th>
                <th>Nama Jasa</th>
                <th>Tarif</th>
                <th>Estimasi</th>
                <th style="text-align:center;">Status</th>
                <th style="text-align:center;">Aksi</th>
            </tr>
        </thead>
        <tbody id="j-tbody">
            <?php for($i=0;$i<5;$i++): ?>
            <tr>
                <td><span class="sk-cell" style="width:16px;"></span></td>
                <td><span class="sk-cell" style="width:160px;"></span></td>
                <td><span class="sk-cell" style="width:100px;"></span></td>
                <td><span class="sk-cell" style="width:70px;"></span></td>
                <td style="text-align:center;"><span class="sk-cell" style="width:55px;"></span></td>
                <td style="text-align:center;"><span class="sk-cell" style="width:50px;margin:auto;display:block;"></span></td>
            </tr>
            <?php endfor; ?>
        </tbody>
    </table>
    <div id="j-empty" style="display:none;" class="empty-state">
        <i class="ti ti-tool"></i>
        <p>Belum ada jasa. Tambahkan jasa baru untuk memulai.</p>
    </div>
</div>

<!-- Add/Edit Modal -->
<div class="modal-backdrop" id="jasa-modal">
    <div class="modal-inner">
        <div class="modal-hd">
            <h3><i class="ti ti-tool"></i><span id="modal-title">Tambah Jasa Baru</span></h3>
            <button class="modal-close" onclick="closeJasaModal()"><i class="ti ti-x"></i></button>
        </div>
        <form onsubmit="saveJasa(event)" class="modal-body">
            <input type="hidden" id="j-id">
            <div class="form-group">
                <label class="form-label">Nama Jasa <span style="color:#ef4444;">*</span></label>
                <input type="text" id="j-name" class="form-control" placeholder="Misal: Pasang Head Unit Pioneer" required>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">Tarif (Rp) <span style="color:#ef4444;">*</span></label>
                    <div class="price-wrap">
                        <span class="prefix">Rp</span>
                        <input type="number" id="j-price" class="form-control" placeholder="150000" min="0" required>
                    </div>
                </div>
                <div class="form-group">
                    <label class="form-label">Estimasi</label>
                    <select id="j-duration" class="form-control">
                        <option value="30 menit">30 menit</option>
                        <option value="1 jam">1 jam</option>
                        <option value="2 jam">2 jam</option>
                        <option value="3 jam" selected>3 jam</option>
                        <option value="4 jam">4 jam</option>
                        <option value="1 hari">1 hari</option>
                        <option value="2 hari">2 hari</option>
                    </select>
                </div>
            </div>
            <div class="form-group">
                <label class="form-label">Kategori</label>
                <select id="j-category" class="form-control">
                    <option value="Instalasi">Instalasi</option>
                    <option value="Servis">Servis</option>
                    <option value="Konsultasi">Konsultasi</option>
                    <option value="Pemasangan">Pemasangan</option>
                </select>
            </div>
            <div class="modal-footer-btns">
                <button type="button" class="btn-cancel" onclick="closeJasaModal()">Batal</button>
                <button type="submit" class="btn-save" id="modal-btn">Simpan Jasa</button>
            </div>
        </form>
    </div>
</div>

<script>
let allJasa = [];

const CAT_CLASS = {
    'Instalasi': 'cat-instalasi',
    'Servis': 'cat-servis',
    'Konsultasi': 'cat-konsultasi',
    'Pemasangan': 'cat-pemasangan',
};

async function loadJasa() {
    try {
        const result = await apiFetch('../api/services/get_all.php');
        if (result.status === 'success') {
            allJasa = result.data;
            updateStats(allJasa);
            renderTable(allJasa);
        }
    } catch(e) {
        document.getElementById('j-tbody').innerHTML = `<tr><td colspan="6"><div class="empty-state" style="color:#ef4444;"><i class="ti ti-wifi-off"></i><p>Gagal memuat data. Periksa koneksi.</p></div></td></tr>`;
    }
}

function updateStats(data) {
    document.getElementById('count-total').textContent = data.length;
    document.getElementById('count-active').textContent = data.length;
    if (data.length) {
        const avg = data.reduce((s,j) => s + parseInt(j.sell_price), 0) / data.length;
        const top = Math.max(...data.map(j => parseInt(j.sell_price)));
        document.getElementById('count-avg').textContent = 'Rp' + Math.round(avg/1000) + 'k';
        document.getElementById('count-top').textContent = 'Rp' + Math.round(top/1000) + 'k';
    }
}

function renderTable(data) {
    const tbody = document.getElementById('j-tbody');
    const empty = document.getElementById('j-empty');
    if (!data.length) { tbody.innerHTML = ''; empty.style.display = 'block'; return; }
    empty.style.display = 'none';
    tbody.innerHTML = data.map((j, i) => {
        const cat = j.category || 'Instalasi';
        const catCls = CAT_CLASS[cat] || 'cat-instalasi';
        return `<tr>
            <td style="font-size:12px;font-weight:600;color:var(--text-muted);">${i+1}</td>
            <td>
                <div style="display:flex;align-items:center;gap:10px;">
                    <div style="width:36px;height:36px;border-radius:10px;background:rgba(29,106,224,0.1);display:flex;align-items:center;justify-content:center;color:var(--accent-blue);font-size:16px;flex-shrink:0;">
                        <i class="ti ti-tool"></i>
                    </div>
                    <div>
                        <div style="font-size:13px;font-weight:700;color:var(--text-pri);margin-bottom:3px;">${j.name}</div>
                        <span class="cat-badge ${catCls}">${cat}</span>
                    </div>
                </div>
            </td>
            <td style="font-family:'Space Grotesk',sans-serif;font-weight:700;color:#10b981;">Rp ${parseInt(j.sell_price).toLocaleString('id-ID')}</td>
            <td>
                <div style="display:flex;align-items:center;gap:5px;font-size:12px;color:var(--text-muted);">
                    <i class="ti ti-clock" style="color:var(--accent-blue);font-size:13px;"></i>
                    ${j.duration || '1 jam'}
                </div>
            </td>
            <td style="text-align:center;">
                <span class="active-badge">
                    <span style="width:6px;height:6px;border-radius:50%;background:#10b981;animation:pulse 2s infinite;"></span>
                    AKTIF
                </span>
            </td>
            <td>
                <div class="action-btns">
                    <button class="icon-btn edit" title="Edit" onclick="editJasa(${j.id})"><i class="ti ti-edit"></i></button>
                    <button class="icon-btn del" title="Hapus" onclick="confirmDel(${j.id})"><i class="ti ti-trash"></i></button>
                </div>
            </td>
        </tr>`;
    }).join('');
}

function filterJasa() {
    const q = document.getElementById('j-search').value.toLowerCase();
    renderTable(allJasa.filter(j => j.name.toLowerCase().includes(q)));
}

function openJasaModal(prefill = null) {
    document.getElementById('modal-title').textContent = prefill ? 'Edit Jasa' : 'Tambah Jasa Baru';
    document.getElementById('j-id').value = prefill?.id || '';
    document.getElementById('j-name').value = prefill?.name || '';
    document.getElementById('j-price').value = prefill?.sell_price || '';
    document.getElementById('j-duration').value = prefill?.duration || '3 jam';
    document.getElementById('j-category').value = prefill?.category || 'Instalasi';
    document.getElementById('jasa-modal').classList.add('open');
}

function closeJasaModal() { document.getElementById('jasa-modal').classList.remove('open'); }

document.getElementById('jasa-modal').addEventListener('click', function(e) { if (e.target === this) closeJasaModal(); });

function editJasa(id) {
    const j = allJasa.find(x => x.id == id);
    if (j) openJasaModal(j);
}

async function saveJasa(e) {
    e.preventDefault();
    const btn = document.getElementById('modal-btn');
    btn.disabled = true;
    btn.textContent = 'Menyimpan...';
    const id = document.getElementById('j-id').value;
    const payload = {
        category_id: 1,
        sku: 'JASA-' + Math.floor(Math.random() * 90000 + 10000),
        name: document.getElementById('j-name').value,
        brand: 'Service', type: 'jasa',
        cost_price: 0,
        sell_price: document.getElementById('j-price').value,
        duration: document.getElementById('j-duration').value,
        category: document.getElementById('j-category').value,
        stock: 999, min_stock: 0
    };
    if (id) payload.id = id;
    const endpoint = id ? '../api/products/update.php' : '../api/products/create.php';
    try {
        const result = await apiFetch(endpoint, { method: 'POST', body: JSON.stringify(payload) });
        if (result.status === 'success') {
            closeJasaModal();
            loadJasa();
            showToast(id ? 'Jasa berhasil diperbarui' : 'Jasa baru berhasil ditambahkan', 'success');
        } else {
            showToast(result.message || 'Gagal menyimpan jasa', 'error');
        }
    } catch(e) {
        showToast('Terjadi kesalahan server.', 'error');
    } finally {
        btn.disabled = false;
        btn.textContent = 'Simpan Jasa';
    }
}

async function confirmDel(id) {
    const confirmed = await Swal.fire({
        title: 'Hapus Jasa?',
        text: 'Tindakan ini tidak dapat dibatalkan.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Ya, Hapus',
        cancelButtonText: 'Batal',
        background: '#111827',
        color: '#f0f2f8',
        confirmButtonColor: '#ef4444',
        cancelButtonColor: '#1e2a45',
    });
    if (!confirmed.isConfirmed) return;
    try {
        const result = await apiFetch('../api/products/delete.php', { method: 'POST', body: JSON.stringify({ id }) });
        if (result.status === 'success') { loadJasa(); showToast('Jasa berhasil dihapus', 'success'); }
        else showToast(result.message || 'Gagal menghapus jasa', 'error');
    } catch(e) { showToast('Terjadi kesalahan.', 'error'); }
}

loadJasa();
</script>

<?php require_once '../includes/footer.php'; ?>