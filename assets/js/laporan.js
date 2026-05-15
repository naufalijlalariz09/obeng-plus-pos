/* ============================================================
   laporan.js — Laporan Penjualan, Laba/Rugi & Histori Transaksi
   Digunakan oleh: laporan.php, laba_rugi.php, transaksi.php,
                   dashboard.php (chart)
   Bergantung pada: main.js (apiFetch, formatRp, showToast,
                             animateCounter, showConfirm)
   ============================================================ */

'use strict';

/* ══════════════════════════════════════════════════════════
   ╔═══════════════════════════════════╗
   ║  MODUL LAPORAN (laporan.php)      ║
   ╚═══════════════════════════════════╝
══════════════════════════════════════════════════════════ */

let salesChart    = null;
let currentReport = [];

document.addEventListener('DOMContentLoaded', () => {
    // Deteksi halaman aktif berdasarkan elemen
    if (document.getElementById('reportChart') || document.getElementById('laporan-chart')) {
        initLaporanPage();
    }
    if (document.getElementById('profit-tbody')) {
        initLabaRugiPage();
    }
    if (document.getElementById('tx-tbody') || document.getElementById('r-recent-tbody')) {
        initTransaksiPage();
    }
    if (document.getElementById('salesChart')) {
        // laporan.php lama — init juga
        initLaporanLama();
    }
});

/* ── LAPORAN BARU (laporan.php dengan filter) ── */
function initLaporanPage() {
    setDefaultDateRange();
    loadReport();
    bindLaporanFilters();
    bindLaporanExport();
}

function setDefaultDateRange() {
    const now = new Date();
    const y   = now.getFullYear();
    const m   = String(now.getMonth() + 1).padStart(2, '0');
    const d   = String(now.getDate()).padStart(2, '0');
    const start = document.getElementById('filter-date-start');
    const end   = document.getElementById('filter-date-end');
    if (start) start.value = `${y}-${m}-01`;
    if (end)   end.value   = `${y}-${m}-${d}`;
}

async function loadReport() {
    const start  = document.getElementById('filter-date-start')?.value || '';
    const end    = document.getElementById('filter-date-end')?.value   || '';
    const method = document.getElementById('filter-method')?.value     || '';

    const loadingEl = document.getElementById('laporan-loading');
    const tableEl   = document.getElementById('laporan-table-wrap');
    if (loadingEl) loadingEl.classList.remove('hidden');
    if (tableEl)   tableEl.classList.add('hidden');

    try {
        const params = new URLSearchParams({ start, end, method });
        const res    = await apiFetch(`../api/reports/get.php?${params}`);

        if (loadingEl) loadingEl.classList.add('hidden');
        if (tableEl)   tableEl.classList.remove('hidden');

        if (res?.status === 'success') {
            currentReport = res.data.transactions || [];
            renderLaporanSummaryCards(res.data.summary   || {});
            renderLaporanChart(res.data.chart            || {});
            renderLaporanTable(currentReport);
            renderTopProducts(res.data.top_products      || []);
        } else {
            showToast('Gagal memuat laporan: ' + (res?.message || ''), 'error');
        }
    } catch (err) {
        if (loadingEl) loadingEl.classList.add('hidden');
        showToast(err.message || 'Gagal menghubungi server.', 'error');
    }
}

function renderLaporanSummaryCards(s) {
    const set = (id, v) => { const el = document.getElementById(id); if (el) el.textContent = v; };
    set('sum-revenue', formatRp(s.total_revenue    || 0));
    set('sum-tx',      s.total_transactions         || 0);
    set('sum-avg',     formatRp(s.avg_transaction  || 0));
    set('sum-items',   s.total_items_sold           || 0);

    // Tambahan animasi counter
    animateCounter('sum-revenue-raw', s.total_revenue || 0, true);
}

function renderLaporanChart(chartData) {
    const canvas = document.getElementById('laporan-chart');
    if (!canvas || typeof Chart === 'undefined') return;

    const labels = chartData.labels || [];
    const values = chartData.values || [];

    if (salesChart) { salesChart.destroy(); salesChart = null; }

    salesChart = new Chart(canvas, {
        type: 'bar',
        data: {
            labels,
            datasets: [{
                label: 'Pendapatan',
                data: values,
                backgroundColor: (ctx) => {
                    const g = ctx.chart.ctx.createLinearGradient(0, 0, 0, 220);
                    g.addColorStop(0, '#1d6ae0');
                    g.addColorStop(1, 'rgba(29,106,224,0.15)');
                    return g;
                },
                borderRadius: 8,
                borderSkipped: false,
                barThickness: 'flex',
                hoverBackgroundColor: '#7c3aed',
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false },
                tooltip: {
                    backgroundColor: '#0d1422',
                    borderColor: '#1e2a45',
                    borderWidth: 1,
                    titleColor: '#8a96b2',
                    bodyColor: '#fff',
                    padding: 12,
                    callbacks: {
                        label: ctx => ' ' + formatRp(ctx.parsed.y)
                    }
                }
            },
            scales: {
                x: {
                    grid: { display: false },
                    ticks: { color: '#3d4f73', font: { size: 11 } },
                    border: { display: false }
                },
                y: {
                    beginAtZero: true,
                    grid: { color: 'rgba(255,255,255,0.04)', drawBorder: false },
                    ticks: {
                        color: '#3d4f73',
                        font: { size: 11 },
                        callback: v => v >= 1_000_000
                            ? 'Rp ' + (v/1_000_000).toFixed(1) + 'jt'
                            : 'Rp ' + (v/1_000).toFixed(0) + 'rb'
                    },
                    border: { display: false }
                }
            }
        }
    });
}

function renderLaporanTable(items) {
    const tbody   = document.getElementById('laporan-tbody');
    const countEl = document.getElementById('laporan-count');
    if (!tbody) return;
    if (countEl) countEl.textContent = items.length + ' transaksi';

    if (!items.length) {
        tbody.innerHTML = `<tr><td colspan="6" style="padding:50px;text-align:center;color:var(--text-muted);">
            <i class="ti ti-file-off" style="font-size:36px;display:block;margin-bottom:10px;opacity:.4;"></i>
            Tidak ada data transaksi pada periode ini.</td></tr>`;
        return;
    }

    tbody.innerHTML = items.map(t => {
        const methodMap = {
            Cash: 'method-tunai', Transfer: 'method-transfer', QRIS: 'method-qris'
        };
        const methodCls = methodMap[t.payment_method] || 'method-tunai';
        return `
        <tr style="cursor:pointer;" onclick="openDetailModal(${t.id})">
            <td style="font-family:'Space Grotesk',sans-serif;font-weight:700;color:var(--accent-blue);">${t.invoice_no}</td>
            <td style="font-size:12px;color:var(--text-muted);">${formatDate(t.created_at, true)}</td>
            <td style="color:var(--text-pri);">${t.customer_name || 'Umum'}</td>
            <td><span class="method-badge ${methodCls}">${t.payment_method || 'Cash'}</span></td>
            <td style="font-family:'Space Grotesk',sans-serif;font-weight:700;color:#10b981;">${formatRp(t.grand_total)}</td>
            <td>
                <span style="font-size:10px;font-weight:700;padding:3px 10px;border-radius:20px;
                    ${t.status === 'selesai' ? 'background:rgba(16,185,129,.1);color:#10b981;' : 'background:rgba(249,115,22,.1);color:#f97316;'}">
                    ${(t.status || 'selesai').toUpperCase()}
                </span>
            </td>
        </tr>`;
    }).join('');
}

function renderTopProducts(items) {
    const list = document.getElementById('top-products-list');
    if (!list) return;

    if (!items.length) {
        list.innerHTML = '<p style="padding:20px;text-align:center;color:var(--text-muted);font-size:12px;">Tidak ada data</p>';
        return;
    }

    const max = items[0]?.total_qty || 1;
    const colors = ['#60a5fa', '#f97316', '#10b981', '#a78bfa', '#f59e0b'];
    list.innerHTML = items.slice(0, 5).map((p, i) => `
        <div style="display:flex;align-items:center;gap:12px;padding:8px 0;border-bottom:1px solid rgba(255,255,255,.04);">
            <div style="width:24px;height:24px;border-radius:6px;background:${colors[i%5]};display:flex;align-items:center;justify-content:center;font-size:11px;font-weight:800;color:#fff;flex-shrink:0;">${i+1}</div>
            <div style="flex:1;min-width:0;">
                <div style="font-size:12px;font-weight:600;color:var(--text-pri);white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">${p.name}</div>
                <div style="height:4px;background:rgba(255,255,255,.06);border-radius:2px;margin-top:5px;">
                    <div style="height:100%;width:${Math.round(p.total_qty / max * 100)}%;background:${colors[i%5]};border-radius:2px;"></div>
                </div>
            </div>
            <span style="font-size:12px;font-weight:700;color:var(--text-muted);flex-shrink:0;">${p.total_qty} unit</span>
        </div>`).join('');
}

function bindLaporanFilters() {
    document.getElementById('btn-filter')?.addEventListener('click', loadReport);

    // Preset range buttons
    document.querySelectorAll('[data-range]').forEach(btn => {
        btn.addEventListener('click', () => {
            const range = btn.dataset.range;
            const now   = new Date();
            let start;
            const end   = new Date();

            switch (range) {
                case 'today': start = new Date(); break;
                case '7d':    start = new Date(); start.setDate(start.getDate() - 6); break;
                case '30d':   start = new Date(); start.setDate(start.getDate() - 29); break;
                case 'month': start = new Date(now.getFullYear(), now.getMonth(), 1); break;
                case 'year':  start = new Date(now.getFullYear(), 0, 1); break;
                default:      return;
            }

            const fmt = d => d.toISOString().slice(0, 10);
            const startEl = document.getElementById('filter-date-start');
            const endEl   = document.getElementById('filter-date-end');
            if (startEl) startEl.value = fmt(start);
            if (endEl)   endEl.value   = fmt(end);

            document.querySelectorAll('[data-range]').forEach(b => b.classList.remove('active-range'));
            btn.classList.add('active-range');
            loadReport();
        });
    });
}

/* ── Detail Modal ── */
window.openDetailModal = async function(id) {
    const modal = document.getElementById('modal-detail-tx');
    if (!modal) return;
    modal.classList.remove('hidden');
    modal.classList.add('flex');
    const bodyEl = document.getElementById('detail-tx-body');
    if (bodyEl) bodyEl.innerHTML = '<p style="text-align:center;padding:30px;color:var(--text-muted);font-size:13px;"><i class="ti ti-loader-2" style="animation:spin .8s linear infinite;font-size:20px;display:block;margin-bottom:8px;"></i>Memuat detail...</p>';

    try {
        const res = await apiFetch(`../api/transactions/get_detail.php?id=${id}`);
        if (res?.status === 'success') renderDetailModal(res.data);
        else showToast('Gagal memuat detail transaksi.', 'error');
    } catch (err) {
        showToast(err.message, 'error');
    }
};

function renderDetailModal(tx) {
    const body = document.getElementById('detail-tx-body');
    if (!body) return;
    const items    = tx.items || [];
    const subtotal = items.reduce((s, i) => s + parseInt(i.price) * parseInt(i.qty), 0);
    const disc     = Math.round(subtotal * (tx.discount_percent || 0) / 100);

    body.innerHTML = `
        <div style="display:grid;grid-template-columns:1fr 1fr;gap:4px 16px;font-size:12px;margin-bottom:16px;">
            <span style="color:var(--text-muted);">Invoice</span>
            <span style="font-family:'Space Grotesk',sans-serif;font-weight:700;color:var(--accent-blue);">${tx.invoice_no}</span>
            <span style="color:var(--text-muted);">Tanggal</span>
            <span style="font-weight:500;">${formatDate(tx.created_at, true)}</span>
            <span style="color:var(--text-muted);">Customer</span>
            <span style="font-weight:500;">${tx.customer_name || 'Umum'}</span>
            <span style="color:var(--text-muted);">Pembayaran</span>
            <span style="font-weight:500;">${tx.payment_method || '—'}</span>
        </div>
        <div style="border-top:1px solid var(--border);padding-top:12px;display:flex;flex-direction:column;gap:8px;">
            ${items.map(i => `
            <div style="display:flex;justify-content:space-between;font-size:12px;">
                <span style="color:var(--text-pri);font-weight:600;">${i.name} <span style="color:var(--text-muted);">×${i.qty}</span></span>
                <span style="font-weight:700;">${formatRp(parseInt(i.price) * parseInt(i.qty))}</span>
            </div>`).join('')}
        </div>
        <div style="border-top:1px solid var(--border);margin-top:12px;padding-top:12px;display:flex;flex-direction:column;gap:6px;font-size:12px;">
            <div style="display:flex;justify-content:space-between;">
                <span style="color:var(--text-muted);">Subtotal</span>
                <span>${formatRp(subtotal)}</span>
            </div>
            ${disc > 0 ? `
            <div style="display:flex;justify-content:space-between;">
                <span style="color:#f97316;">Diskon (${tx.discount_percent}%)</span>
                <span style="color:#f97316;">- ${formatRp(disc)}</span>
            </div>` : ''}
            <div style="display:flex;justify-content:space-between;font-weight:800;font-size:15px;margin-top:4px;">
                <span style="color:#fff;">Total</span>
                <span style="color:#10b981;">${formatRp(tx.grand_total)}</span>
            </div>
        </div>`;
}

window.closeModal = function(id) {
    const el = document.getElementById(id);
    if (!el) return;
    el.classList.add('hidden');
    el.classList.remove('flex', 'open');
};

/* ── Export ── */
function bindLaporanExport() {
    document.getElementById('btn-export-laporan')?.addEventListener('click', exportLaporanCSV);
    document.getElementById('btn-print-laporan')?.addEventListener('click', () => window.print());
}

function exportLaporanCSV() {
    if (!currentReport.length) { showToast('Tidak ada data untuk diekspor.', 'warning'); return; }

    const header = ['Invoice', 'Tanggal', 'Customer', 'Metode Bayar', 'Total', 'Status'];
    const rows   = currentReport.map(t =>
        [t.invoice_no, formatDate(t.created_at, true), t.customer_name || 'Umum',
         t.payment_method || '-', t.grand_total, t.status || 'selesai']
        .map(v => `"${String(v).replace(/"/g, '""')}"`)
        .join(',')
    );
    const startVal = document.getElementById('filter-date-start')?.value || '';
    const endVal   = document.getElementById('filter-date-end')?.value   || '';

    downloadCSV([header, ...rows], `laporan_${startVal}_sd_${endVal}`);
    showToast(`${currentReport.length} data berhasil diekspor!`, 'success');
}

/* ══════════════════════════════════════════════════════════
   ╔═══════════════════════════════════╗
   ║  LAPORAN LAMA (laporan.php versi  ║
   ║  dengan select period)            ║
   ╚═══════════════════════════════════╝
══════════════════════════════════════════════════════════ */
function initLaporanLama() {
    const periodSel = document.getElementById('l-period');
    periodSel?.addEventListener('change', loadReportLama);
    loadReportLama();
}

async function loadReportLama() {
    const period = document.getElementById('l-period')?.value || 'this_month';
    try {
        const res = await apiFetch(`../api/transactions/get_report.php?period=${period}`);
        if (!res?.status === 'success') return;
        const data   = res.data;
        const income = parseInt(data.summary?.income || 0);
        const txCount = parseInt(data.summary?.transactions || 0);
        const avg    = txCount > 0 ? Math.round(income / txCount) : 0;

        animateCounter('r-income', income, true);
        animateCounter('r-tx', txCount, false);
        animateCounter('r-avg', avg, true);

        const cust = data.summary?.unique_customers;
        const custEl = document.getElementById('r-cust');
        if (custEl) custEl.textContent = cust ?? '—';

        // Chart
        renderLaporanLamaChart(data.labels || [], data.values || []);

        // Top services
        if (data.top_services?.length > 0) {
            const maxVal = data.top_services[0].count;
            const rankEmoji = ['🥇','🥈','🥉'];
            const rankCls   = ['r1','r2','r3'];
            document.getElementById('top-services').innerHTML = data.top_services.slice(0,5).map((s, i) => `
                <div class="top-item">
                    <div class="top-rank ${rankCls[i] || ''}">${i < 3 ? rankEmoji[i] : (i+1)}</div>
                    <div class="top-info">
                        <div class="top-name">${s.name}</div>
                        <div class="top-count">${s.count}x terjual · ${formatRp(s.revenue || 0)}</div>
                    </div>
                    <div class="top-bar-wrap"><div class="top-bar" style="width:${Math.round((s.count/maxVal)*100)}%;"></div></div>
                </div>`).join('');
        }

        // Recent table
        const tbodyEl = document.getElementById('r-recent-tbody');
        if (tbodyEl && data.recent?.length) {
            tbodyEl.innerHTML = data.recent.slice(0,8).map(t => `
                <tr>
                    <td style="font-weight:700;color:#60a5fa;font-family:'Space Grotesk',sans-serif;">${t.invoice_no}</td>
                    <td style="color:#c9d1e8;">${t.customer_name || 'Umum'}</td>
                    <td style="font-weight:600;color:#10b981;">${formatRp(t.grand_total)}</td>
                    <td style="color:#5a6380;font-size:12px;">${t.payment_method || 'Tunai'}</td>
                    <td style="color:#3d4f73;font-size:12px;">${formatDate(t.created_at, true)}</td>
                    <td><span class="badge-status badge-lunas"><i class="ti ti-check" style="font-size:10px;"></i> Lunas</span></td>
                </tr>`).join('');
        }
    } catch (e) {
        console.error('loadReportLama error:', e);
    }
}

let laporanLamaChart = null;
function renderLaporanLamaChart(labels, values) {
    const canvas = document.getElementById('reportChart') || document.getElementById('laporan-chart');
    if (!canvas || typeof Chart === 'undefined') return;
    if (laporanLamaChart) laporanLamaChart.destroy();

    const ctx = canvas.getContext('2d');
    const gradient = ctx.createLinearGradient(0, 0, 0, 280);
    gradient.addColorStop(0, 'rgba(29,106,224,0.25)');
    gradient.addColorStop(1, 'rgba(29,106,224,0)');

    laporanLamaChart = new Chart(ctx, {
        type: 'line',
        data: {
            labels,
            datasets: [{
                label: 'Pemasukan',
                data: values,
                borderColor: '#60a5fa',
                backgroundColor: gradient,
                fill: true,
                tension: 0.4,
                pointBackgroundColor: '#60a5fa',
                pointBorderColor: '#111827',
                pointBorderWidth: 2,
                pointRadius: 4,
                pointHoverRadius: 7
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false },
                tooltip: {
                    backgroundColor: '#0d1422', borderColor: '#1e2a45', borderWidth: 1,
                    titleColor: '#8a96b2', bodyColor: '#fff',
                    bodyFont: { family: 'Space Grotesk', weight: '700', size: 14 },
                    callbacks: { label: ctx => formatRp(parseInt(ctx.raw)) }
                }
            },
            scales: {
                x: { grid: { color: 'rgba(255,255,255,0.04)' }, ticks: { color: '#3d4f73', font: { size: 11 } } },
                y: {
                    beginAtZero: true,
                    grid: { color: 'rgba(255,255,255,0.04)' },
                    ticks: {
                        color: '#3d4f73', font: { size: 11 },
                        callback: v => v >= 1_000_000 ? 'Rp ' + (v/1_000_000).toFixed(1)+'jt' : 'Rp '+v.toLocaleString('id-ID')
                    }
                }
            }
        }
    });
}

/* ══════════════════════════════════════════════════════════
   ╔═══════════════════════════════════╗
   ║  MODUL LABA / RUGI (laba_rugi.php)║
   ╚═══════════════════════════════════╝
══════════════════════════════════════════════════════════ */

function initLabaRugiPage() {
    loadProfit();
    document.getElementById('btn-export-profit')?.addEventListener('click', exportProfitCSV);
    document.getElementById('btn-print-lr')?.addEventListener('click', () => window.print());
}

let allProfitData = [];

async function loadProfit() {
    const start = document.getElementById('start-date')?.value || '';
    const end   = document.getElementById('end-date')?.value   || '';
    const tbody = document.getElementById('profit-tbody');
    const footer = document.getElementById('summary-footer');

    if (footer) footer.style.display = 'none';

    if (tbody) tbody.innerHTML = Array(5).fill(0).map(() => `
        <tr><td><div class="skeleton-cell" style="width:110px;"></div></td>
            <td><div class="skeleton-cell" style="width:90px;"></div></td>
            <td><div class="skeleton-cell" style="width:100px;"></div></td>
            <td><div class="skeleton-cell" style="width:90px;"></div></td>
            <td><div class="skeleton-cell" style="width:100px;"></div></td>
            <td><div class="skeleton-cell" style="width:60px;"></div></td></tr>`).join('');

    try {
        const res = await apiFetch(`../api/transactions/get_profit.php?start=${start}&end=${end}`);
        if (res?.status === 'success') {
            const s       = res.summary;
            const revenue = parseInt(s.total_revenue || 0);
            const cogs    = parseInt(s.total_cogs    || 0);
            const profit  = parseInt(s.total_profit  || 0);
            const margin  = revenue > 0 ? ((profit / revenue) * 100).toFixed(1) : 0;

            animateCounter('txt-revenue', revenue, true);
            animateCounter('txt-cogs',    cogs,    true);
            animateCounter('txt-profit',  profit,  true);
            const marginEl = document.getElementById('txt-margin');
            if (marginEl) marginEl.textContent = margin + '%';

            allProfitData = res.data || [];

            if (!allProfitData.length) {
                tbody.innerHTML = `<tr><td colspan="6">
                    <div style="padding:60px;text-align:center;color:#2d3a55;">
                        <i class="ti ti-receipt-off" style="font-size:40px;display:block;margin-bottom:12px;"></i>
                        <p style="font-size:14px;">Tidak ada data transaksi pada periode ini.</p>
                    </div></td></tr>`;
                return;
            }

            tbody.innerHTML = allProfitData.map(row => {
                const rev   = parseInt(row.revenue  || 0);
                const cost  = parseInt(row.total_cost || 0);
                const pft   = parseInt(row.profit   || 0);
                const pctg  = rev > 0 ? ((pft / rev) * 100).toFixed(1) : '0.0';
                const bar   = Math.min(100, Math.max(0, parseFloat(pctg)));
                const isLoss = pft < 0;

                return `<tr>
                    <td class="td-time">${formatDate(row.created_at, true)}</td>
                    <td class="td-invoice" style="font-family:'Space Grotesk',sans-serif;font-weight:700;color:var(--accent-blue);">${row.invoice_no}</td>
                    <td class="td-revenue" style="color:#10b981;font-weight:600;">${formatRp(rev)}</td>
                    <td class="td-cogs" style="color:#f87171;">${formatRp(cost)}</td>
                    <td class="td-profit">
                        <div style="display:flex;align-items:center;gap:8px;">
                            <span style="font-weight:700;color:${isLoss ? '#f87171' : '#10b981'};">${formatRp(pft)}</span>
                        </div>
                    </td>
                    <td>
                        <div style="display:flex;align-items:center;gap:8px;">
                            <div class="profit-bar-wrap"><div class="profit-bar-fill" style="width:${bar}%;background:${isLoss ? '#ef4444' : ''};"></div></div>
                            <span class="profit-pill" style="${isLoss ? 'background:rgba(239,68,68,.1);color:#f87171;border-color:rgba(239,68,68,.2);' : ''}">
                                <i class="ti ti-arrow-${isLoss ? 'down-right' : 'up-right'}" style="font-size:10px;"></i>${pctg}%
                            </span>
                        </div>
                    </td>
                </tr>`;
            }).join('');

            // Footer summary
            const count     = allProfitData.length;
            const avgPft    = count > 0 ? Math.round(profit / count) : 0;
            const setEl     = (id, v) => { const el = document.getElementById(id); if (el) el.textContent = v; };
            setEl('sf-count',     count);
            setEl('sf-avg',       formatRp(avgPft));
            setEl('sf-avgmargin', margin + '%');
            if (footer) footer.style.display = 'flex';

        } else {
            tbody.innerHTML = `<tr><td colspan="6" style="padding:40px;text-align:center;color:#ef4444;">Gagal memuat data: ${res?.message || ''}</td></tr>`;
        }
    } catch (e) {
        tbody.innerHTML = `<tr><td colspan="6">
            <div style="padding:60px;text-align:center;color:#3d4f73;">
                <i class="ti ti-wifi-off" style="font-size:40px;display:block;margin-bottom:12px;"></i>
                <p style="font-size:14px;">Gagal memuat data. Periksa koneksi Anda.</p>
            </div></td></tr>`;
    }
}

// Expose ke laba_rugi.php
window.loadProfit = loadProfit;

function exportProfitCSV() {
    if (!allProfitData.length) { showToast('Tidak ada data untuk diekspor.', 'warning'); return; }
    const header = ['Waktu', 'Invoice', 'Penjualan', 'Modal', 'Laba Bersih', 'Margin (%)'];
    const rows   = allProfitData.map(r => {
        const rev  = parseInt(r.revenue  || 0);
        const cost = parseInt(r.total_cost || 0);
        const pft  = parseInt(r.profit   || 0);
        const pct  = rev > 0 ? ((pft/rev)*100).toFixed(1) : 0;
        return [formatDate(r.created_at, true), r.invoice_no, rev, cost, pft, pct + '%']
            .map(v => `"${String(v).replace(/"/g,'""')}"`).join(',');
    });
    const start = document.getElementById('start-date')?.value || '';
    const end   = document.getElementById('end-date')?.value   || '';
    downloadCSV([header, ...rows], `laba_rugi_${start}_sd_${end}`);
    showToast('Data laba/rugi berhasil diekspor!', 'success');
}

/* ══════════════════════════════════════════════════════════
   ╔═══════════════════════════════════╗
   ║  MODUL TRANSAKSI (transaksi.php)  ║
   ╚═══════════════════════════════════╝
══════════════════════════════════════════════════════════ */

let allTxData     = [];
let filteredData  = [];
let currentPage   = 1;
const PER_PAGE    = 15;
let sortKey       = 'created_at';
let sortDir       = -1;
let activeFilter  = null;

function initTransaksiPage() {
    loadTransaksi();
    bindTxSearch();
    bindTxExport();
}

async function loadTransaksi() {
    const tbody = document.getElementById('tx-tbody');
    if (!tbody) return;

    tbody.innerHTML = Array(8).fill(0).map(() => `
        <tr>${Array(6).fill(0).map(() =>
            `<td><span class="sk-cell" style="width:${50+Math.random()*80|0}px;"></span></td>`
        ).join('')}</tr>`).join('');

    try {
        const res = await apiFetch('../api/transactions/get_all.php');
        if (res?.status === 'success') {
            allTxData = res.data || [];
            updateTxStats(allTxData);
            applyTxFilters();
        } else {
            showToast('Gagal memuat histori transaksi.', 'error');
        }
    } catch (e) {
        showToast(e.message, 'error');
        tbody.innerHTML = `<tr><td colspan="6" style="padding:50px;text-align:center;color:#ef4444;">Gagal memuat data.</td></tr>`;
    }
}

window.loadT = loadTransaksi; // alias untuk transaksi.php

function updateTxStats(data) {
    const today     = new Date().toDateString();
    const todayData = data.filter(t => new Date(t.created_at).toDateString() === today);
    const monthData = data.filter(t => {
        const d = new Date(t.created_at);
        const n = new Date();
        return d.getMonth() === n.getMonth() && d.getFullYear() === n.getFullYear();
    });

    const setEl = (id, v) => { const el = document.getElementById(id); if (el) el.textContent = v; };
    setEl('stat-today-count',  todayData.length);
    setEl('stat-today-total',  formatRp(todayData.reduce((s,t) => s + parseInt(t.grand_total||0), 0)));
    setEl('stat-month-total',  formatRp(monthData.reduce((s,t) => s + parseInt(t.grand_total||0), 0)));
    setEl('stat-all-count',    data.length);
}

function applyTxFilters() {
    const searchVal = (document.getElementById('tx-search')?.value || '').toLowerCase();
    const today     = new Date().toDateString();
    const now       = new Date();

    filteredData = allTxData.filter(t => {
        const matchSearch = !searchVal ||
            (t.invoice_no   || '').toLowerCase().includes(searchVal) ||
            (t.customer_name|| '').toLowerCase().includes(searchVal) ||
            (t.customer_phone||'').toLowerCase().includes(searchVal) ||
            (t.payment_method||'').toLowerCase().includes(searchVal);

        const d = new Date(t.created_at);
        const matchFilter = !activeFilter
            || (activeFilter === 'today' && d.toDateString() === today)
            || (activeFilter === 'month' && d.getMonth() === now.getMonth() && d.getFullYear() === now.getFullYear());

        return matchSearch && matchFilter;
    });

    // Sort
    filteredData.sort((a, b) => {
        let av = a[sortKey], bv = b[sortKey];
        if (sortKey === 'grand_total') { av = parseInt(av); bv = parseInt(bv); }
        else if (typeof av === 'string') av = av.toLowerCase();
        return (av < bv ? -1 : av > bv ? 1 : 0) * sortDir;
    });

    currentPage = 1;
    renderTxPage();
    renderTxStats(filteredData);
}

function renderTxStats(data) {
    const countEl = document.getElementById('tx-count-label');
    if (countEl) countEl.textContent = data.length + ' transaksi';
}

function renderTxPage() {
    const tbody = document.getElementById('tx-tbody');
    if (!tbody) return;

    const start = (currentPage - 1) * PER_PAGE;
    const items = filteredData.slice(start, start + PER_PAGE);

    if (!items.length) {
        tbody.innerHTML = `<tr><td colspan="6" class="no-data">
            <i class="ti ti-receipt-off"></i>
            <p>${allTxData.length ? 'Tidak ada transaksi yang cocok dengan filter.' : 'Belum ada data transaksi.'}</p>
        </td></tr>`;
        renderTxPagination();
        return;
    }

    const methodMap = { Cash: 'method-tunai', Transfer: 'method-transfer', QRIS: 'method-qris' };

    tbody.innerHTML = items.map(t => `
        <tr onclick="showN('${t.invoice_no}')">
            <td class="td-invoice">${t.invoice_no}</td>
            <td>
                <div class="td-customer">${t.customer_name || 'Umum'}</div>
                ${t.customer_phone ? `<span class="td-phone">${t.customer_phone}</span>` : ''}
            </td>
            <td class="td-total">${formatRp(t.grand_total)}</td>
            <td><span class="method-badge ${methodMap[t.payment_method] || 'method-tunai'}">${t.payment_method || 'Cash'}</span></td>
            <td>
                <div class="td-time">${formatDate(t.created_at, true)}</div>
            </td>
            <td>
                <div class="action-btns">
                    <button class="icon-btn" title="Lihat Nota"
                        onclick="event.stopPropagation();showN('${t.invoice_no}')">
                        <i class="ti ti-receipt-2"></i>
                    </button>
                    <button class="icon-btn print" title="Cetak Struk"
                        onclick="event.stopPropagation();showN('${t.invoice_no}', true)">
                        <i class="ti ti-printer"></i>
                    </button>
                </div>
            </td>
        </tr>`).join('');

    renderTxPagination();
    updateTxFooter();
}

function updateTxFooter() {
    const start = (currentPage - 1) * PER_PAGE + 1;
    const end   = Math.min(currentPage * PER_PAGE, filteredData.length);
    const footEl = document.getElementById('tx-footer-info');
    if (footEl) footEl.innerHTML = `Menampilkan <span>${start}–${end}</span> dari <span>${filteredData.length}</span> transaksi`;
}

function renderTxPagination() {
    const totalPages = Math.ceil(filteredData.length / PER_PAGE);
    const pg = document.getElementById('pagination');
    if (!pg) return;
    if (totalPages <= 1) { pg.innerHTML = ''; return; }

    let btns = `<button class="page-btn" onclick="goPage(${currentPage-1})" ${currentPage===1?'disabled style="opacity:.4;pointer-events:none;"':''}>
                    <i class="ti ti-chevron-left" style="font-size:12px;"></i></button>`;
    for (let i=1; i<=totalPages; i++) {
        if (i===1||i===totalPages||Math.abs(i-currentPage)<=1)
            btns += `<button class="page-btn ${i===currentPage?'active':''}" onclick="goPage(${i})">${i}</button>`;
        else if (Math.abs(i-currentPage)===2)
            btns += `<span style="color:var(--text-muted);padding:0 4px;">…</span>`;
    }
    btns += `<button class="page-btn" onclick="goPage(${currentPage+1})" ${currentPage===totalPages?'disabled style="opacity:.4;pointer-events:none;"':''}>
                 <i class="ti ti-chevron-right" style="font-size:12px;"></i></button>`;
    pg.innerHTML = btns;
}

window.goPage = function(p) {
    const total = Math.ceil(filteredData.length / PER_PAGE);
    if (p < 1 || p > total) return;
    currentPage = p;
    renderTxPage();
    document.querySelector('.data-card')?.scrollIntoView({ behavior: 'smooth', block: 'start' });
};

function bindTxSearch() {
    let timer;
    document.getElementById('tx-search')?.addEventListener('input', () => {
        clearTimeout(timer);
        timer = setTimeout(applyTxFilters, 200);
    });
    document.getElementById('refresh-tx')?.addEventListener('click', () => {
        loadTransaksi();
        showToast('Data diperbarui.', 'info', 1500);
    });
}

window.filterTable  = applyTxFilters;
window.sortTable    = function(key) {
    if (sortKey === key) sortDir *= -1;
    else { sortKey = key; sortDir = 1; }
    applyTxFilters();
};
window.toggleFilter = function(f) {
    activeFilter = activeFilter === f ? null : f;
    document.getElementById('btn-today')?.classList.toggle('active', activeFilter === 'today');
    document.getElementById('btn-month')?.classList.toggle('active', activeFilter === 'month');
    applyTxFilters();
};

function bindTxExport() {
    document.getElementById('btn-export-tx')?.addEventListener('click', exportTxCSV);
}

function exportTxCSV() {
    if (!filteredData.length) { showToast('Tidak ada data untuk diekspor.', 'warning'); return; }
    const header = ['Invoice', 'Customer', 'Telepon', 'Total', 'Metode', 'Tanggal'];
    const rows   = filteredData.map(t =>
        [t.invoice_no, t.customer_name||'Umum', t.customer_phone||'—',
         t.grand_total, t.payment_method||'Cash', formatDate(t.created_at, true)]
        .map(v => `"${String(v).replace(/"/g,'""')}"`).join(',')
    );
    downloadCSV([header, ...rows], `transaksi_${new Date().toISOString().slice(0,10)}`);
    showToast(`${filteredData.length} data berhasil diekspor!`, 'success');
}

/* ══════════════════════════════════════════════════════════
   UTILITY: CSV DOWNLOAD
══════════════════════════════════════════════════════════ */
function downloadCSV(rows, filename = 'export') {
    const csv  = rows.map(r => Array.isArray(r) ? r.join(',') : r).join('\n');
    const blob = new Blob(['\uFEFF' + csv], { type: 'text/csv;charset=utf-8;' });
    const link = document.createElement('a');
    link.href     = URL.createObjectURL(blob);
    link.download = filename + '.csv';
    link.click();
    setTimeout(() => URL.revokeObjectURL(link.href), 5000);
}