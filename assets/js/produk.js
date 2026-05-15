/* ============================================================
   produk.js — Manajemen Produk (CRUD + Search + Filter)
   ============================================================ */

let allProducts = [];
let editingId   = null;

document.addEventListener('DOMContentLoaded', () => {
    loadProducts();
    bindModal();
    bindSearch();
    bindImportExport();
});

/* ── 1. LOAD SEMUA PRODUK ── */
async function loadProducts() {
    const tbody = document.getElementById('produk-tbody');
    if (!tbody) return;
    tbody.innerHTML = `<tr><td colspan="6" class="text-center py-8 text-brand-gray text-xs">
        <i class="ti ti-loader animate-spin text-2xl block mb-2"></i>Memuat data...</td></tr>`;

    try {
        const res = await apiFetch('../api/products/get_all.php');
        if (res.status === 'success') {
            allProducts = res.data;
            renderTable(allProducts);
        } else {
            showToast('Gagal memuat produk.', 'error');
        }
    } catch {
        showToast('Tidak dapat menghubungi server.', 'error');
    }
}

/* ── 2. RENDER TABEL ── */
function renderTable(items) {
    const tbody  = document.getElementById('produk-tbody');
    const countEl = document.getElementById('produk-count');
    if (!tbody) return;
    if (countEl) countEl.textContent = items.length + ' produk';

    if (!items.length) {
        tbody.innerHTML = `<tr><td colspan="6" class="text-center py-12 text-brand-gray text-xs">
            <i class="ti ti-mood-empty text-4xl block mb-2 opacity-40"></i>Tidak ada produk</td></tr>`;
        return;
    }

    tbody.innerHTML = items.map(p => {
        const stockBadge = p.type === 'barang'
            ? (p.stock <= 0
                ? `<span class="text-[10px] font-semibold text-red-700 bg-red-50 px-2 py-0.5 rounded-full">Habis</span>`
                : (p.stock <= 5
                    ? `<span class="text-[10px] font-semibold text-amber-700 bg-amber-50 px-2 py-0.5 rounded-full">${p.stock} (Menipis)</span>`
                    : `<span class="text-xs text-brand-graydark font-medium">${p.stock}</span>`))
            : `<span class="text-[10px] text-green-700 bg-green-50 px-2 py-0.5 rounded-full">Jasa</span>`;

        return `
        <tr class="border-b border-brand-border hover:bg-blue-50/20 transition-colors">
            <td class="px-4 py-3">
                <p class="text-xs font-semibold text-brand-graydark">${p.name}</p>
                <p class="text-[10px] text-brand-gray">${p.sku || '-'}</p>
            </td>
            <td class="px-4 py-3 text-xs text-brand-gray">${p.category_name || '-'}</td>
            <td class="px-4 py-3 text-xs font-medium text-brand-graydark">${formatRp(p.sell_price)}</td>
            <td class="px-4 py-3 text-xs text-brand-gray">${formatRp(p.buy_price || 0)}</td>
            <td class="px-4 py-3">${stockBadge}</td>
            <td class="px-4 py-3">
                <div class="flex gap-2">
                    <button onclick="openEditModal(${p.id})"
                        class="px-3 py-1.5 text-[11px] font-semibold text-brand-pri bg-brand-light rounded-lg hover:bg-blue-100 transition-colors">
                        <i class="ti ti-edit text-xs mr-1"></i>Edit
                    </button>
                    <button onclick="deleteProduct(${p.id}, '${p.name.replace(/'/g, "\\'")}')"
                        class="px-3 py-1.5 text-[11px] font-semibold text-brand-danger bg-red-50 rounded-lg hover:bg-red-100 transition-colors">
                        <i class="ti ti-trash text-xs mr-1"></i>Hapus
                    </button>
                </div>
            </td>
        </tr>`;
    }).join('');
}

/* ── 3. SEARCH & FILTER ── */
function bindSearch() {
    const inp = document.getElementById('produk-search');
    const cat = document.getElementById('produk-cat-filter');
    const typ = document.getElementById('produk-type-filter');
    if (!inp) return;

    const filter = () => {
        const term = inp.value.toLowerCase();
        const c    = cat ? cat.value : '';
        const t    = typ ? typ.value : '';
        renderTable(allProducts.filter(p =>
            (p.name.toLowerCase().includes(term) || (p.sku || '').toLowerCase().includes(term)) &&
            (!c || p.category_name === c) &&
            (!t || p.type === t)
        ));
    };
    inp.addEventListener('input', filter);
    if (cat) cat.addEventListener('change', filter);
    if (typ) typ.addEventListener('change', filter);
}

/* ── 4. BUKA MODAL TAMBAH ── */
window.openAddModal = function() {
    editingId = null;
    document.getElementById('modal-produk-title').textContent = 'Tambah Produk';
    document.getElementById('form-produk').reset();
    toggleStockField();
    document.getElementById('modal-produk').classList.remove('hidden');
    document.getElementById('modal-produk').classList.add('flex');
};

/* ── 5. BUKA MODAL EDIT ── */
window.openEditModal = function(id) {
    const p = allProducts.find(x => x.id == id);
    if (!p) return;
    editingId = id;
    document.getElementById('modal-produk-title').textContent = 'Edit Produk';

    document.getElementById('f-name').value       = p.name;
    document.getElementById('f-sku').value        = p.sku        || '';
    document.getElementById('f-category').value   = p.category_name || '';
    document.getElementById('f-type').value       = p.type       || 'barang';
    document.getElementById('f-sell-price').value = p.sell_price || 0;
    document.getElementById('f-buy-price').value  = p.buy_price  || 0;
    document.getElementById('f-stock').value      = p.stock      || 0;
    document.getElementById('f-desc').value       = p.description || '';

    toggleStockField();
    document.getElementById('modal-produk').classList.remove('hidden');
    document.getElementById('modal-produk').classList.add('flex');
};

/* ── 6. TUTUP MODAL ── */
window.closeModal = function(id) {
    const el = document.getElementById(id);
    if (el) { el.classList.add('hidden'); el.classList.remove('flex'); }
};

/* ── 7. TOGGLE STOK FIELD BERDASARKAN TIPE ── */
window.toggleStockField = function() {
    const type     = document.getElementById('f-type');
    const stockRow = document.getElementById('stock-row');
    if (!type || !stockRow) return;
    stockRow.style.display = type.value === 'barang' ? 'block' : 'none';
};

/* ── 8. BIND MODAL ── */
function bindModal() {
    const typeEl = document.getElementById('f-type');
    if (typeEl) typeEl.addEventListener('change', toggleStockField);

    const form = document.getElementById('form-produk');
    if (form) form.addEventListener('submit', submitProduct);

    const overlay = document.getElementById('modal-produk');
    if (overlay) overlay.addEventListener('click', (e) => {
        if (e.target === overlay) closeModal('modal-produk');
    });
}

/* ── 9. SUBMIT PRODUK (TAMBAH / EDIT) ── */
async function submitProduct(e) {
    e.preventDefault();
    const btn  = document.getElementById('btn-save-produk');
    const url  = editingId ? '../api/products/update.php' : '../api/products/create.php';

    const payload = {
        id:           editingId,
        name:         document.getElementById('f-name').value.trim(),
        sku:          document.getElementById('f-sku').value.trim(),
        category:     document.getElementById('f-category').value.trim(),
        type:         document.getElementById('f-type').value,
        sell_price:   parseInt(document.getElementById('f-sell-price').value) || 0,
        buy_price:    parseInt(document.getElementById('f-buy-price').value)  || 0,
        stock:        parseInt(document.getElementById('f-stock').value)      || 0,
        description:  document.getElementById('f-desc').value.trim(),
    };

    if (!payload.name) { showToast('Nama produk wajib diisi.', 'warning'); return; }
    if (payload.sell_price <= 0) { showToast('Harga jual harus lebih dari 0.', 'warning'); return; }

    setButtonLoading(btn, true);
    try {
        const res = await apiFetch(url, { method: 'POST', body: JSON.stringify(payload) });
        setButtonLoading(btn, false);
        if (res.status === 'success') {
            showToast(editingId ? 'Produk berhasil diperbarui!' : 'Produk berhasil ditambahkan!', 'success');
            closeModal('modal-produk');
            loadProducts();
        } else {
            showToast('Gagal: ' + res.message, 'error');
        }
    } catch (err) {
        setButtonLoading(btn, false);
        showToast(err.message, 'error');
    }
}

/* ── 10. HAPUS PRODUK ── */
window.deleteProduct = function(id, name) {
    showConfirm(
        'Hapus Produk',
        `Produk "${name}" akan dihapus permanen. Lanjutkan?`,
        async () => {
            try {
                const res = await apiFetch('../api/products/delete.php', {
                    method: 'POST', body: JSON.stringify({ id })
                });
                if (res.status === 'success') {
                    showToast('Produk berhasil dihapus.', 'success');
                    loadProducts();
                } else {
                    showToast('Gagal menghapus: ' + res.message, 'error');
                }
            } catch (err) {
                showToast(err.message, 'error');
            }
        }
    );
};

/* ── 11. EXPORT CSV ── */
function bindImportExport() {
    const exportBtn = document.getElementById('btn-export-csv');
    if (exportBtn) exportBtn.addEventListener('click', exportCSV);
}

function exportCSV() {
    if (!allProducts.length) { showToast('Tidak ada data untuk diekspor.', 'warning'); return; }
    const header = ['Nama', 'SKU', 'Kategori', 'Tipe', 'Harga Jual', 'Harga Beli', 'Stok'];
    const rows   = allProducts.map(p =>
        [p.name, p.sku || '', p.category_name || '', p.type, p.sell_price, p.buy_price || 0, p.stock || 0]
        .map(v => `"${String(v).replace(/"/g, '""')}"`)
        .join(',')
    );
    const csv  = [header.join(','), ...rows].join('\n');
    const blob = new Blob(['\uFEFF' + csv], { type: 'text/csv;charset=utf-8;' });
    const link = document.createElement('a');
    link.href  = URL.createObjectURL(blob);
    link.download = `produk_${new Date().toISOString().slice(0,10)}.csv`;
    link.click();
    showToast('CSV berhasil diekspor!', 'success');
}