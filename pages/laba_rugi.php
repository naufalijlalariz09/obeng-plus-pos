<?php 
require_once '../includes/header.php'; 
require_role(['admin', 'pimpinan']);
?>

<style>
@import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Space+Grotesk:wght@400;500;600;700&display=swap');

* { box-sizing: border-box; }

.lr-root {
    font-family: 'Plus Jakarta Sans', sans-serif;
    background: #0a0f1e;
    min-height: 100vh;
    padding: 28px;
    color: #e8eaf2;
}

.lr-header {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    margin-bottom: 32px;
    flex-wrap: wrap;
    gap: 16px;
}

.lr-title-block h2 {
    font-family: 'Space Grotesk', sans-serif;
    font-size: 22px;
    font-weight: 700;
    color: #fff;
    letter-spacing: -0.3px;
    margin: 0 0 4px;
    display: flex;
    align-items: center;
    gap: 10px;
}

.lr-title-block h2 span.badge {
    font-size: 10px;
    font-weight: 700;
    letter-spacing: 1.5px;
    text-transform: uppercase;
    background: linear-gradient(135deg, #f59e0b, #f97316);
    color: #0a0f1e;
    padding: 3px 10px;
    border-radius: 20px;
}

.lr-title-block p {
    font-size: 13px;
    color: #5a6380;
    margin: 0;
}

.lr-filter-bar {
    display: flex;
    align-items: center;
    gap: 8px;
    background: #111827;
    border: 1px solid #1e2a45;
    border-radius: 14px;
    padding: 8px 12px;
}

.lr-filter-bar input[type="date"] {
    background: transparent;
    border: none;
    outline: none;
    color: #c9d1e8;
    font-family: 'Plus Jakarta Sans', sans-serif;
    font-size: 13px;
    cursor: pointer;
    color-scheme: dark;
}

.lr-filter-bar .sep {
    color: #2d3a55;
    font-size: 13px;
}

.lr-filter-btn {
    background: linear-gradient(135deg, #1d6ae0, #7c3aed);
    border: none;
    color: #fff;
    font-family: 'Plus Jakarta Sans', sans-serif;
    font-size: 13px;
    font-weight: 700;
    padding: 8px 18px;
    border-radius: 10px;
    cursor: pointer;
    letter-spacing: 0.3px;
    transition: opacity 0.15s, transform 0.15s;
    display: flex;
    align-items: center;
    gap: 6px;
}

.lr-filter-btn:hover { opacity: 0.9; transform: translateY(-1px); }
.lr-filter-btn:active { transform: scale(0.97); }

.lr-cards {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 16px;
    margin-bottom: 28px;
}

@media (max-width: 900px) { .lr-cards { grid-template-columns: 1fr; } }

.lr-card {
    background: #111827;
    border: 1px solid #1e2a45;
    border-radius: 20px;
    padding: 24px;
    position: relative;
    overflow: hidden;
    transition: border-color 0.2s, transform 0.2s;
}

.lr-card:hover { border-color: #2d3a65; transform: translateY(-2px); }

.lr-card::before {
    content: '';
    position: absolute;
    top: 0; left: 0; right: 0;
    height: 2px;
}

.lr-card.revenue::before { background: linear-gradient(90deg, #10b981, #34d399); }
.lr-card.cogs::before { background: linear-gradient(90deg, #ef4444, #f97316); }
.lr-card.profit::before { background: linear-gradient(90deg, #1d6ae0, #7c3aed); }

.lr-card .card-icon {
    width: 40px; height: 40px;
    border-radius: 12px;
    display: flex; align-items: center; justify-content: center;
    font-size: 18px;
    margin-bottom: 14px;
}

.lr-card.revenue .card-icon { background: rgba(16,185,129,0.12); color: #10b981; }
.lr-card.cogs .card-icon { background: rgba(239,68,68,0.12); color: #ef4444; }
.lr-card.profit .card-icon { background: rgba(29,106,224,0.12); color: #60a5fa; }

.lr-card .card-label {
    font-size: 10px;
    font-weight: 700;
    letter-spacing: 1.5px;
    text-transform: uppercase;
    color: #3d4f73;
    margin-bottom: 8px;
}

.lr-card .card-value {
    font-family: 'Space Grotesk', sans-serif;
    font-size: 26px;
    font-weight: 700;
    letter-spacing: -0.5px;
    color: #fff;
}

.lr-card.revenue .card-value { color: #10b981; }
.lr-card.cogs .card-value { color: #f87171; }
.lr-card.profit .card-value { 
    font-size: 30px;
    background: linear-gradient(135deg, #60a5fa, #a78bfa);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
}

.lr-card .card-margin {
    font-size: 12px;
    color: #3d4f73;
    margin-top: 6px;
}

.lr-card .card-margin span { color: #10b981; font-weight: 600; }

.lr-table-wrap {
    background: #111827;
    border: 1px solid #1e2a45;
    border-radius: 20px;
    overflow: hidden;
}

.lr-table-head {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 18px 24px 14px;
    border-bottom: 1px solid #1e2a45;
}

.lr-table-head h3 {
    font-family: 'Space Grotesk', sans-serif;
    font-size: 14px;
    font-weight: 700;
    color: #fff;
    margin: 0;
    display: flex;
    align-items: center;
    gap: 8px;
}

.lr-table-head h3 i { color: #7c3aed; }

.lr-export-btn {
    background: transparent;
    border: 1px solid #1e2a45;
    color: #5a6380;
    font-family: 'Plus Jakarta Sans', sans-serif;
    font-size: 12px;
    font-weight: 600;
    padding: 6px 14px;
    border-radius: 8px;
    cursor: pointer;
    display: flex;
    align-items: center;
    gap: 6px;
    transition: all 0.15s;
}

.lr-export-btn:hover { border-color: #2d4070; color: #c9d1e8; background: #161e30; }

.lr-table-scroll { overflow-x: auto; }

.lr-table {
    width: 100%;
    border-collapse: collapse;
    font-size: 13px;
}

.lr-table thead tr {
    background: #0d1422;
}

.lr-table thead th {
    padding: 12px 20px;
    text-align: left;
    font-size: 10px;
    font-weight: 700;
    letter-spacing: 1.2px;
    text-transform: uppercase;
    color: #2d3a55;
    white-space: nowrap;
}

.lr-table tbody tr {
    border-top: 1px solid #111827;
    transition: background 0.15s;
}

.lr-table tbody tr:hover { background: #0f1929; }

.lr-table tbody td {
    padding: 14px 20px;
    vertical-align: middle;
    color: #8a96b2;
}

.lr-table .td-time { font-size: 12px; color: #3d4f73; }
.lr-table .td-invoice { font-weight: 700; color: #60a5fa; font-family: 'Space Grotesk', sans-serif; }
.lr-table .td-revenue { color: #c9d1e8; font-weight: 600; }
.lr-table .td-cogs { color: #f87171; }
.lr-table .td-profit { font-weight: 700; color: #34d399; }
.lr-table .td-margin { }

.profit-pill {
    display: inline-flex;
    align-items: center;
    gap: 4px;
    background: rgba(52,211,153,0.1);
    color: #34d399;
    font-size: 11px;
    font-weight: 700;
    padding: 3px 10px;
    border-radius: 20px;
    border: 1px solid rgba(52,211,153,0.2);
}

.td-profit-wrap { display: flex; align-items: center; gap: 10px; }

.empty-state {
    padding: 60px 24px;
    text-align: center;
    color: #2d3a55;
}

.empty-state i { font-size: 40px; margin-bottom: 12px; display: block; }
.empty-state p { font-size: 14px; }

/* Loading skeleton */
.skeleton-row td { padding: 14px 20px; }
.skeleton-cell {
    height: 14px;
    background: linear-gradient(90deg, #1e2a45 25%, #2d3a55 50%, #1e2a45 75%);
    background-size: 200% 100%;
    animation: shimmer 1.5s infinite;
    border-radius: 6px;
}

@keyframes shimmer {
    0% { background-position: -200% 0; }
    100% { background-position: 200% 0; }
}

.summary-footer {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 14px 24px;
    border-top: 1px solid #1e2a45;
    background: #0d1422;
}

.summary-footer .sf-item { font-size: 12px; color: #3d4f73; }
.summary-footer .sf-item span { color: #c9d1e8; font-weight: 600; margin-left: 6px; }

.profit-bar-wrap {
    height: 4px;
    background: rgba(255,255,255,0.05);
    border-radius: 2px;
    overflow: hidden;
    width: 80px;
}

.profit-bar-fill {
    height: 100%;
    background: linear-gradient(90deg, #10b981, #34d399);
    border-radius: 2px;
    transition: width 0.5s ease;
}
</style>

<div class="lr-root">
    <div class="lr-header">
        <div class="lr-title-block">
            <h2>
                <i class="ti ti-chart-area-line" style="color:#7c3aed;font-size:22px;"></i>
                Laba / Rugi
                <span class="badge">Profit Report</span>
            </h2>
            <p>Analisis keuntungan bersih Obeng Plus Car Audio</p>
        </div>
        <div class="lr-filter-bar">
            <i class="ti ti-calendar" style="color:#3d4f73;font-size:16px;"></i>
            <input type="date" id="start-date" value="<?php echo date('Y-m-01'); ?>">
            <span class="sep">—</span>
            <input type="date" id="end-date" value="<?php echo date('Y-m-d'); ?>">
            <button onclick="loadProfit()" class="lr-filter-btn">
                <i class="ti ti-filter" style="font-size:14px;"></i> Filter
            </button>
        </div>
    </div>

    <div class="lr-cards">
        <div class="lr-card revenue">
            <div class="card-icon"><i class="ti ti-trending-up"></i></div>
            <div class="card-label">Total Penjualan</div>
            <div class="card-value" id="txt-revenue">Rp 0</div>
            <div class="card-margin">Omzet periode ini</div>
        </div>
        <div class="lr-card cogs">
            <div class="card-icon"><i class="ti ti-shopping-cart"></i></div>
            <div class="card-label">Modal / HPP</div>
            <div class="card-value" id="txt-cogs">Rp 0</div>
            <div class="card-margin">Harga pokok penjualan</div>
        </div>
        <div class="lr-card profit">
            <div class="card-icon"><i class="ti ti-cash"></i></div>
            <div class="card-label">Laba Bersih</div>
            <div class="card-value" id="txt-profit">Rp 0</div>
            <div class="card-margin">Margin: <span id="txt-margin">0%</span></div>
        </div>
    </div>

    <div class="lr-table-wrap">
        <div class="lr-table-head">
            <h3><i class="ti ti-receipt-2"></i> Detail Transaksi</h3>
            <button onclick="window.print()" class="lr-export-btn">
                <i class="ti ti-printer" style="font-size:14px;"></i> Cetak
            </button>
        </div>
        <div class="lr-table-scroll">
            <table class="lr-table">
                <thead>
                    <tr>
                        <th>Waktu</th>
                        <th>No. Invoice</th>
                        <th>Penjualan</th>
                        <th>Modal</th>
                        <th>Laba Bersih</th>
                        <th>Margin</th>
                    </tr>
                </thead>
                <tbody id="profit-tbody">
                    <tr class="skeleton-row">
                        <td><div class="skeleton-cell" style="width:110px;"></div></td>
                        <td><div class="skeleton-cell" style="width:90px;"></div></td>
                        <td><div class="skeleton-cell" style="width:100px;"></div></td>
                        <td><div class="skeleton-cell" style="width:90px;"></div></td>
                        <td><div class="skeleton-cell" style="width:100px;"></div></td>
                        <td><div class="skeleton-cell" style="width:60px;"></div></td>
                    </tr>
                </tbody>
            </table>
        </div>
        <div class="summary-footer" id="summary-footer" style="display:none;">
            <div class="sf-item">Total data: <span id="sf-count">0</span> transaksi</div>
            <div class="sf-item">Rata-rata laba/nota: <span id="sf-avg">Rp 0</span></div>
            <div class="sf-item">Margin rata-rata: <span id="sf-avgmargin">0%</span></div>
        </div>
    </div>
</div>

<script>
    async function loadProfit() {
        const start = document.getElementById('start-date').value;
        const end = document.getElementById('end-date').value;
        const tbody = document.getElementById('profit-tbody');
        const footer = document.getElementById('summary-footer');
        
        footer.style.display = 'none';
        tbody.innerHTML = `
            ${[...Array(5)].map(() => `
            <tr class="skeleton-row">
                <td><div class="skeleton-cell" style="width:110px;"></div></td>
                <td><div class="skeleton-cell" style="width:90px;"></div></td>
                <td><div class="skeleton-cell" style="width:100px;"></div></td>
                <td><div class="skeleton-cell" style="width:90px;"></div></td>
                <td><div class="skeleton-cell" style="width:100px;"></div></td>
                <td><div class="skeleton-cell" style="width:60px;"></div></td>
            </tr>`).join('')}
        `;
        
        try {
            const res = await fetch(`../api/transactions/get_profit.php?start=${start}&end=${end}`);
            const result = await res.json();
            
            if(result.status === 'success') {
                const s = result.summary;
                const revenue = parseInt(s.total_revenue);
                const cogs = parseInt(s.total_cogs);
                const profit = parseInt(s.total_profit);
                const margin = revenue > 0 ? ((profit / revenue) * 100).toFixed(1) : 0;
                
                animateCount('txt-revenue', revenue, 'Rp ');
                animateCount('txt-cogs', cogs, 'Rp ');
                animateCount('txt-profit', profit, 'Rp ');
                document.getElementById('txt-margin').textContent = margin + '%';
                
                if(result.data.length === 0) {
                    tbody.innerHTML = `<tr><td colspan="6"><div class="empty-state"><i class="ti ti-receipt-off"></i><p>Tidak ada data transaksi pada periode ini.</p></div></td></tr>`;
                    return;
                }
                
                tbody.innerHTML = result.data.map(row => {
                    const rev = parseInt(row.revenue);
                    const cost = parseInt(row.total_cost);
                    const pft = parseInt(row.profit);
                    const pctg = rev > 0 ? ((pft / rev) * 100).toFixed(1) : 0;
                    const barWidth = Math.min(100, Math.max(0, pctg));
                    return `
                    <tr>
                        <td class="td-time">${new Date(row.created_at).toLocaleString('id-ID', {day:'2-digit',month:'short',year:'numeric',hour:'2-digit',minute:'2-digit'})}</td>
                        <td class="td-invoice">${row.invoice_no}</td>
                        <td class="td-revenue">Rp ${rev.toLocaleString('id-ID')}</td>
                        <td class="td-cogs">Rp ${cost.toLocaleString('id-ID')}</td>
                        <td class="td-profit">
                            <div class="td-profit-wrap">
                                Rp ${pft.toLocaleString('id-ID')}
                            </div>
                        </td>
                        <td>
                            <div style="display:flex;align-items:center;gap:8px;">
                                <div class="profit-bar-wrap"><div class="profit-bar-fill" style="width:${barWidth}%;"></div></div>
                                <span class="profit-pill"><i class="ti ti-arrow-up-right" style="font-size:10px;"></i>${pctg}%</span>
                            </div>
                        </td>
                    </tr>
                `}).join('');
                
                const count = result.data.length;
                const avgPft = count > 0 ? Math.round(profit / count) : 0;
                document.getElementById('sf-count').textContent = count;
                document.getElementById('sf-avg').textContent = 'Rp ' + avgPft.toLocaleString('id-ID');
                document.getElementById('sf-avgmargin').textContent = margin + '%';
                footer.style.display = 'flex';
            }
        } catch(e) {
            tbody.innerHTML = `<tr><td colspan="6"><div class="empty-state" style="color:#f87171;"><i class="ti ti-wifi-off"></i><p>Gagal memuat data. Periksa koneksi Anda.</p></div></td></tr>`;
        }
    }
    
    function animateCount(id, target, prefix='') {
        const el = document.getElementById(id);
        const duration = 800;
        const start = performance.now();
        const step = (now) => {
            const progress = Math.min((now - start) / duration, 1);
            const eased = 1 - Math.pow(1 - progress, 3);
            const current = Math.round(eased * target);
            el.textContent = prefix + current.toLocaleString('id-ID');
            if(progress < 1) requestAnimationFrame(step);
        };
        requestAnimationFrame(step);
    }
    
    loadProfit();
</script>

<?php require_once '../includes/footer.php'; ?>