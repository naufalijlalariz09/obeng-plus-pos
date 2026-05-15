<?php
// pages/produk.php
require_once '../includes/header.php';
?>

<style>
.page-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 20px;
    flex-wrap: wrap;
    gap: 12px;
}
.page-title {
    font-family: 'Space Grotesk', sans-serif;
    font-size: 20px;
    font-weight: 700;
    color: #fff;
}

.btn-add {
    display: flex;
    align-items: center;
    gap: 6px;
    padding: 9px 18px;
    background: linear-gradient(135deg, #1d6ae0, #7c3aed);
    border: none;
    border-radius: 12px;
    color: #fff;
    font-family: 'Plus Jakarta Sans', sans-serif;
    font-size: 13px;
    font-weight: 700;
    cursor: pointer;
    transition: opacity 0.15s;
    box-shadow: 0 4px 16px rgba(29,106,224,0.3);
}
.btn-add:hover { opacity: 0.9; }

.data-card {
    background: var(--bg-card);
    border: 1px solid var(--border);
    border-radius: 20px;
    overflow: hidden;
}

.data-table {
    width: 100%;
    border-collapse: collapse;
    font-size: 13px;
}

.data-table thead tr {
    background: rgba(0,0,0,0.2);
    border-bottom: 1px solid var(--border);
}

.data-table thead th {
    padding: 12px 18px;
    text-align: left;
    font-size: 10px;
    font-weight: 700;
    letter-spacing: 1.2px;
    text-transform: uppercase;
    color: var(--text-muted);
    white-space: nowrap;
}

.data-table tbody tr {
    border-top: 1px solid rgba(255,255,255,0.03);
    transition: background 0.12s;
}

.data-table tbody tr:hover { background: rgba(255,255,255,0.03); }

.data-table tbody td { padding: 14px 18px; color: var(--text-sec); vertical-align: middle; }

.td-name { font-weight: 700; color: var(--text-pri); margin-bottom: 2px; }
.td-cat { font-size: 10px; font-weight: 700; color: var(--accent-orange); text-transform: uppercase; letter-spacing: 0.5px; }

.stock-pill {
    display: inline-flex;
    align-items: center;
    gap: 4px;
    font-size: 10px;
    font-weight: 700;
    padding: 3px 10px;
    border-radius: 20px;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}
.stock-ok { background: rgba(16,185,129,0.1); color: #10b981; border: 1px solid rgba(16,185,129,0.2); }
.stock-low { background: rgba(239,68,68,0.1); color: #ef4444; border: 1px solid rgba(239,68,68,0.2); }

.action-btn {
    background: transparent;
    border: 1px solid var(--border);
    border-radius: 8px;
    color: var(--text-muted);
    width: 30px; height: 30px;
    display: inline-flex; align-items: center; justify-content: center;
    cursor: pointer;
    font-size: 14px;
    transition: all 0.15s;
}
.action-btn:hover { border-color: #ef4444; color: #ef4444; background: rgba(239,68,68,0.06); }

/* Empty / loading state */
.table-message td {
    padding: 60px 20px;
    text-align: center;
    color: var(--text-muted);
}
.table-message i { font-size: 40px; display: block; margin-bottom: 12px; }
.table-message p { font-size: 14px; }

.sk-row { animation: none; }
.sk-cell {
    height: 13px;
    background: linear-gradient(90deg, #1e2a45 25%, #2d3a55 50%, #1e2a45 75%);
    background-size: 200% 100%;
    animation: shimmer 1.4s infinite;
    border-radius: 6px;
    display: inline-block;
}
@keyframes shimmer { 0%{background-position:200% 0} 100%{background-position:-200% 0} }

/* Modal */
.modal-backdrop {
    position: fixed;
    inset: 0;
    background: rgba(0,0,0,0.7);
    backdrop-filter: blur(4px);
    z-index: 999;
    display: none;
    align-items: center;
    justify-content: center;
}
.modal-backdrop.open { display: flex; }

.modal-inner {
    background: var(--bg-card);
    border: 1px solid var(--border);
    border-radius: 22px;
    width: 100%;
    max-width: 480px;
    overflow: hidden;
    animation: modalIn 0.22s ease;
    max-height: 90vh;
    overflow-y: auto;
}
@keyframes modalIn { from{opacity:0;transform:scale(0.96) translateY(8px)} to{opacity:1;transform:scale(1) translateY(0)} }

.modal-hd {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 16px 20px;
    border-bottom: 1px solid var(--border);
    background: rgba(255,255,255,0.02);
    position: sticky;
    top: 0;
    z-index: 1;
}
.modal-hd h3 {
    font-family: 'Space Grotesk', sans-serif;
    font-size: 15px;
    font-weight: 700;
    color: #fff;
    margin: 0;
}
.modal-close {
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
.modal-close:hover { border-color: #ef4444; color: #ef4444; }

.modal-body { padding: 20px; }

.form-row { display: grid; grid-template-columns: 1fr 1fr; gap: 12px; }
@media (max-width: 480px) { .form-row { grid-template-columns: 1fr; } }

.form-group { margin-bottom: 14px; }
.form-label {
    display: block;
    font-size: 10px;
    font-weight: 800;
    letter-spacing: 1.2px;
    text-transform: uppercase;
    color: var(--text-muted);
    margin-bottom: 7px;
}
.form-control {
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
.form-control:focus { border-color: rgba(29,106,224,0.5); box-shadow: 0 0 0 3px rgba(29,106,224,0.08); }
.form-control::placeholder { color: var(--text-muted); }

.modal-footer { padding: 16px 20px; border-top: 1px solid var(--border); display: flex; gap: 10px; justify-content: flex-end; }
.btn-cancel {
    padding: 9px 18px;
    background: transparent;
    border: 1px solid var(--border);
    border-radius: 10px;
    color: var(--text-sec);
    font-family: 'Plus Jakarta Sans', sans-serif;
    font-size: 13px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.15s;
}
.btn-cancel:hover { border-color: var(--border-mid); color: #fff; }
.btn-save {
    padding: 9px 22px;
    background: linear-gradient(135deg, #1d6ae0, #7c3aed);
    border: none;
    border-radius: 10px;
    color: #fff;
    font-family: 'Plus Jakarta Sans', sans-serif;
    font-size: 13px;
    font-weight: 700;
    cursor: pointer;
    transition: opacity 0.15s;
}
.btn-save:hover { opacity: 0.9; }
</style>

<div class="page-header">
    <div>
        <div class="page-title">Master Produk</div>
        <div style="font-size:12px;color:var(--text-muted);margin-top:3px;">Kelola data stok dan harga produk</div>
    </div>
    <button class="btn-add" onclick="openM()">
        <i class="ti ti-plus" style="font-size:14px;"></i> Produk Baru
    </button>
</div>

<div class="data-card">
    <table class="data-table">
        <thead>
            <tr>
                <th>Produk</th>
                <th>Brand</th>
                <th>Harga Jual</th>
                <th>HPP</th>
                <th>Stok</th>
                <th style="text-align:center;">Aksi</th>
            </tr>
        </thead>
        <tbody id="p-tbody">
            <?php for($i=0;$i<6;$i++): ?>
            <tr>
                <td><span class="sk-cell" style="width:120px;"></span></td>
                <td><span class="sk-cell" style="width:80px;"></span></td>
                <td><span class="sk-cell" style="width:90px;"></span></td>
                <td><span class="sk-cell" style="width:90px;"></span></td>
                <td><span class="sk-cell" style="width:60px;"></span></td>
                <td style="text-align:center;"><span class="sk-cell" style="width:40px;margin:auto;display:block;"></span></td>
            </tr>
            <?php endfor; ?>
        </tbody>
    </table>
</div>

<!-- Add/Edit Product Modal -->
<div class="modal-backdrop" id="prod-modal">
    <div class="modal-inner">
        <div class="modal-hd">
            <h3 id="modal-title-text"><i class="ti ti-package" style="color:var(--accent-blue);margin-right:6px;"></i>Tambah Produk</h3>
            <button class="modal-close" onclick="closeM()"><i class="ti ti-x"></i></button>
        </div>
        <form onsubmit="saveP(event)" class="modal-body">
            <input type="hidden" id="p-id">
            <div class="form-row">
                <div class="form-group" style="grid-column:1/-1;">
                    <label class="form-label">Nama Produk <span style="color:#ef4444;">*</span></label>
                    <input type="text" id="p-name" class="form-control" placeholder="Misal: Pioneer DEH-S7250BT" required>
                </div>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">SKU / Kode</label>
                    <input type="text" id="p-sku" class="form-control" placeholder="SKU-001">
                </div>
                <div class="form-group">
                    <label class="form-label">Brand</label>
                    <input type="text" id="p-brand" class="form-control" placeholder="Pioneer, JBL, dll">
                </div>
            </div>
            <div class="form-group">
                <label class="form-label">Kategori</label>
                <select id="p-cat" class="form-control">
                    <option value="1">Umum</option>
                </select>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">Harga Pokok (Rp) <span style="color:#ef4444;">*</span></label>
                    <input type="number" id="p-cost" class="form-control" placeholder="0" min="0" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Harga Jual (Rp) <span style="color:#ef4444;">*</span></label>
                    <input type="number" id="p-price" class="form-control" placeholder="0" min="0" required>
                </div>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">Stok Awal</label>
                    <input type="number" id="p-stock" class="form-control" placeholder="0" min="0" value="0">
                </div>
                <div class="form-group">
                    <label class="form-label">Stok Minimum</label>
                    <input type="number" id="p-minstock" class="form-control" placeholder="5" min="0" value="5">
                </div>
            </div>
            <div class="modal-footer" style="padding:0;border:none;margin-top:4px;">
                <button type="button" class="btn-cancel" onclick="closeM()">Batal</button>
                <button type="submit" class="btn-save" id="btn-save-prod">Simpan Produk</button>
            </div>
        </form>
    </div>
</div>

<script>
let allProducts = [];

async function loadP() {
    try {
        const result = await apiFetch('../api/products/get_all.php');
        if (result.status === 'success') {
            allProducts = result.data;
            renderProducts(allProducts);
            loadCategories();
        }
    } catch(e) {
        document.getElementById('p-tbody').innerHTML = `<tr class="table-message"><td colspan="6"><i class="ti ti-alert-circle" style="color:#ef4444;"></i><p style="color:#ef4444;">Gagal memuat data produk.</p></td></tr>`;
    }
}

function renderProducts(data) {
    const tbody = document.getElementById('p-tbody');
    if (!data.length) {
        tbody.innerHTML = `<tr class="table-message"><td colspan="6"><i class="ti ti-package"></i><p>Belum ada produk. Klik "+ Produk Baru" untuk menambahkan.</p></td></tr>`;
        return;
    }
    tbody.innerHTML = data.map(p => {
        const isLow = parseInt(p.stock) <= parseInt(p.min_stock || 5);
        return `<tr>
            <td>
                <div class="td-name">${p.name}</div>
                <div class="td-cat">${p.category_name || 'UMUM'}</div>
            </td>
            <td style="color:var(--text-sec);">${p.brand || '—'}</td>
            <td style="font-family:'Space Grotesk',sans-serif;font-weight:700;color:#10b981;">Rp ${parseInt(p.sell_price).toLocaleString('id-ID')}</td>
            <td style="font-size:12px;color:var(--text-muted);">Rp ${parseInt(p.cost_price || 0).toLocaleString('id-ID')}</td>
            <td>
                <span class="stock-pill ${isLow ? 'stock-low' : 'stock-ok'}">
                    ${isLow ? '<i class="ti ti-alert-triangle" style="font-size:10px;"></i>' : '<i class="ti ti-check" style="font-size:10px;"></i>'}
                    ${p.stock} unit
                </span>
            </td>
            <td style="text-align:center;">
                <button class="action-btn" title="Hapus" onclick="delP(${p.id})">
                    <i class="ti ti-trash"></i>
                </button>
            </td>
        </tr>`;
    }).join('');
}

async function loadCategories() {
    try {
        const result = await apiFetch('../api/products/get_categories.php');
        if (result.status === 'success') {
            const sel = document.getElementById('p-cat');
            sel.innerHTML = result.data.map(c => `<option value="${c.id}">${c.name}</option>`).join('');
        }
    } catch(e) { /* silent fail */ }
}

async function delP(id) {
    const confirmed = await Swal.fire({
        title: 'Hapus Produk?',
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
        if (result.status === 'success') {
            showToast('Produk berhasil dihapus', 'success');
            loadP();
        } else {
            showToast(result.message || 'Gagal menghapus produk', 'error');
        }
    } catch(e) {
        showToast('Terjadi kesalahan saat menghapus.', 'error');
    }
}

function openM() {
    document.getElementById('p-id').value = '';
    document.getElementById('modal-title-text').innerHTML = '<i class="ti ti-package" style="color:var(--accent-blue);margin-right:6px;"></i>Tambah Produk';
    document.querySelector('.modal-inner form').reset();
    document.getElementById('prod-modal').classList.add('open');
}

function closeM() {
    document.getElementById('prod-modal').classList.remove('open');
}

document.getElementById('prod-modal').addEventListener('click', function(e) {
    if (e.target === this) closeM();
});

async function saveP(e) {
    e.preventDefault();
    const btn = document.getElementById('btn-save-prod');
    btn.disabled = true;
    btn.textContent = 'Menyimpan...';

    const payload = {
        id: document.getElementById('p-id').value || null,
        name: document.getElementById('p-name').value,
        sku: document.getElementById('p-sku').value,
        brand: document.getElementById('p-brand').value,
        category_id: document.getElementById('p-cat').value,
        cost_price: document.getElementById('p-cost').value,
        sell_price: document.getElementById('p-price').value,
        stock: document.getElementById('p-stock').value,
        min_stock: document.getElementById('p-minstock').value,
        type: 'product'
    };

    const endpoint = payload.id ? '../api/products/update.php' : '../api/products/create.php';

    try {
        const result = await apiFetch(endpoint, { method: 'POST', body: JSON.stringify(payload) });
        if (result.status === 'success') {
            showToast(payload.id ? 'Produk berhasil diperbarui' : 'Produk baru berhasil ditambahkan', 'success');
            closeM();
            loadP();
        } else {
            showToast(result.message || 'Gagal menyimpan produk', 'error');
        }
    } catch(e) {
        showToast('Terjadi kesalahan server.', 'error');
    } finally {
        btn.disabled = false;
        btn.textContent = 'Simpan Produk';
    }
}

loadP();
</script>

<?php require_once '../includes/footer.php'; ?>