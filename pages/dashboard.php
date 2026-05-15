<?php
// pages/dashboard.php
require_once '../includes/header.php';
?>

<style>
.skeleton {
    background: linear-gradient(90deg, #1e2a45 25%, #2d3a55 50%, #1e2a45 75%);
    background-size: 200% 100%;
    animation: shimmer 1.4s infinite;
    border-radius: 6px;
    display: inline-block;
}
@keyframes shimmer { 0%{background-position:200% 0} 100%{background-position:-200% 0} }

/* Stat Cards */
.stat-card {
    background: var(--bg-card);
    border: 1px solid var(--border);
    border-radius: 18px;
    padding: 20px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    transition: border-color 0.2s, transform 0.2s;
    position: relative;
    overflow: hidden;
}
.stat-card:hover { border-color: var(--border-mid); transform: translateY(-2px); }
.stat-card::before {
    content: '';
    position: absolute;
    top: 0; left: 0; right: 0;
    height: 2px;
    background: var(--accent-color, var(--accent-blue));
}

.stat-label {
    font-size: 10px;
    font-weight: 700;
    letter-spacing: 1.5px;
    text-transform: uppercase;
    color: var(--text-muted);
    margin-bottom: 8px;
}

.stat-value {
    font-family: 'Space Grotesk', sans-serif;
    font-size: 20px;
    font-weight: 700;
    color: #fff;
    margin-bottom: 4px;
}

.stat-sub { font-size: 11px; color: var(--text-muted); }

.stat-icon {
    width: 44px; height: 44px;
    border-radius: 14px;
    display: flex; align-items: center; justify-content: center;
    font-size: 20px;
    flex-shrink: 0;
}

/* Section cards */
.section-card {
    background: var(--bg-card);
    border: 1px solid var(--border);
    border-radius: 20px;
    padding: 24px;
}

.section-title {
    font-family: 'Space Grotesk', sans-serif;
    font-size: 14px;
    font-weight: 700;
    color: #fff;
    margin: 0 0 4px;
}

.section-sub { font-size: 12px; color: var(--text-muted); margin: 0; }

/* Quick action buttons */
.quick-action {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 8px;
    padding: 16px 12px;
    border-radius: 14px;
    border: 1px solid var(--border);
    text-decoration: none;
    text-align: center;
    transition: all 0.18s;
}
.quick-action:hover { border-color: var(--border-mid); background: rgba(255,255,255,0.03); }
.quick-action .qa-icon {
    width: 38px; height: 38px;
    border-radius: 12px;
    display: flex; align-items: center; justify-content: center;
    font-size: 17px;
    transition: transform 0.18s;
}
.quick-action:hover .qa-icon { transform: scale(1.1); }
.quick-action span { font-size: 11px; font-weight: 600; color: var(--text-sec); }

/* Target bar */
.target-bar-wrap {
    width: 100%;
    height: 6px;
    background: rgba(255,255,255,0.06);
    border-radius: 3px;
    overflow: hidden;
    margin-bottom: 14px;
}
.target-bar-fill {
    height: 100%;
    border-radius: 3px;
    background: linear-gradient(90deg, #1d6ae0, #7c3aed);
    transition: width 1s ease-out;
    box-shadow: 0 0 10px rgba(29,106,224,0.4);
}

/* Recent tx items */
.tx-item {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 8px 0;
    border-bottom: 1px solid rgba(255,255,255,0.04);
}
.tx-item:last-child { border-bottom: none; }

/* Top product items */
.tp-item {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 8px 0;
    border-bottom: 1px solid rgba(255,255,255,0.04);
}
.tp-item:last-child { border-bottom: none; }

/* Welcome bar button */
.btn-outline-dark {
    display: flex;
    align-items: center;
    gap: 6px;
    padding: 8px 14px;
    border-radius: 12px;
    background: var(--bg-card);
    border: 1px solid var(--border);
    color: var(--text-sec);
    font-family: 'Plus Jakarta Sans', sans-serif;
    font-size: 12px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.15s;
    text-decoration: none;
}
.btn-outline-dark:hover { border-color: var(--border-mid); color: #fff; }

.btn-gradient {
    display: flex;
    align-items: center;
    gap: 6px;
    padding: 8px 16px;
    border-radius: 12px;
    background: linear-gradient(135deg, #1d6ae0, #7c3aed);
    border: none;
    color: #fff;
    font-family: 'Plus Jakarta Sans', sans-serif;
    font-size: 12px;
    font-weight: 700;
    cursor: pointer;
    transition: opacity 0.15s;
    text-decoration: none;
    box-shadow: 0 4px 16px rgba(29,106,224,0.3);
}
.btn-gradient:hover { opacity: 0.9; }

/* Responsive grid helpers */
.grid-4 {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 14px;
    margin-bottom: 20px;
}
@media (max-width: 1100px) { .grid-4 { grid-template-columns: repeat(2, 1fr); } }
@media (max-width: 600px) { .grid-4 { grid-template-columns: 1fr 1fr; } }

.grid-main {
    display: grid;
    grid-template-columns: 1fr 320px;
    gap: 16px;
    margin-bottom: 16px;
}
@media (max-width: 1050px) { .grid-main { grid-template-columns: 1fr; } }

.grid-bottom {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 16px;
}
@media (max-width: 1050px) { .grid-bottom { grid-template-columns: 1fr 1fr; } }
@media (max-width: 680px) { .grid-bottom { grid-template-columns: 1fr; } }
</style>

<!-- Welcome Bar -->
<div style="display:flex;align-items:flex-start;justify-content:space-between;gap:12px;flex-wrap:wrap;margin-bottom:24px;">
    <div>
        <div style="font-family:'Space Grotesk',sans-serif;font-size:22px;font-weight:700;color:#fff;margin-bottom:5px;">
            Halo, <?php echo htmlspecialchars(explode(' ', $_SESSION['name'])[0]); ?>! 👋
        </div>
        <div style="font-size:12px;color:var(--text-muted);display:flex;align-items:center;gap:6px;">
            <span style="width:7px;height:7px;border-radius:50%;background:#10b981;box-shadow:0 0 6px rgba(16,185,129,0.6);flex-shrink:0;"></span>
            <?php echo date('l, d F Y'); ?> · Sistem aktif
        </div>
    </div>
    <div style="display:flex;gap:8px;flex-wrap:wrap;">
        <button onclick="fetchDashboardData()" class="btn-outline-dark">
            <i class="ti ti-refresh" style="font-size:14px;"></i> Refresh
        </button>
        <a href="transaksi.php" class="btn-gradient">
            <i class="ti ti-plus" style="font-size:14px;"></i> Transaksi Baru
        </a>
    </div>
</div>

<!-- Stat Cards -->
<div class="grid-4">
    <div class="stat-card" style="--accent-color:#60a5fa;">
        <div>
            <div class="stat-label">Penjualan Hari Ini</div>
            <div class="stat-value" id="stat-sales"><span class="skeleton" style="width:110px;height:22px;"></span></div>
            <div class="stat-sub" id="stat-sales-sub">dari kemarin</div>
        </div>
        <div class="stat-icon" style="background:rgba(96,165,250,0.12);color:#60a5fa;">
            <i class="ti ti-cash"></i>
        </div>
    </div>
    <div class="stat-card" style="--accent-color:#10b981;">
        <div>
            <div class="stat-label">Total Transaksi</div>
            <div class="stat-value" id="stat-tx"><span class="skeleton" style="width:70px;height:22px;"></span></div>
            <div class="stat-sub">nota hari ini</div>
        </div>
        <div class="stat-icon" style="background:rgba(16,185,129,0.12);color:#10b981;">
            <i class="ti ti-receipt"></i>
        </div>
    </div>
    <div class="stat-card" style="--accent-color:#f97316;">
        <div>
            <div class="stat-label">Mobil Dikerjakan</div>
            <div class="stat-value" id="stat-queue"><span class="skeleton" style="width:70px;height:22px;"></span></div>
            <div class="stat-sub">antrian aktif</div>
        </div>
        <div class="stat-icon" style="background:rgba(249,115,22,0.12);color:#f97316;">
            <i class="ti ti-car"></i>
        </div>
    </div>
    <div class="stat-card" style="--accent-color:#ef4444;">
        <div>
            <div class="stat-label">Stok Menipis</div>
            <div class="stat-value" id="stat-stock"><span class="skeleton" style="width:70px;height:22px;"></span></div>
            <div class="stat-sub">item perlu restock</div>
        </div>
        <div class="stat-icon" style="background:rgba(239,68,68,0.12);color:#ef4444;">
            <i class="ti ti-alert-triangle"></i>
        </div>
    </div>
</div>

<!-- Main Grid -->
<div class="grid-main">
    <!-- Sales Chart -->
    <div class="section-card">
        <div style="display:flex;align-items:flex-start;justify-content:space-between;margin-bottom:20px;">
            <div>
                <div class="section-title">Grafik Penjualan</div>
                <div class="section-sub">7 hari terakhir</div>
            </div>
            <div style="display:flex;align-items:center;gap:6px;background:rgba(16,185,129,0.08);border:1px solid rgba(16,185,129,0.15);border-radius:8px;padding:5px 10px;font-size:11px;color:#10b981;font-weight:600;">
                <i class="ti ti-trending-up" style="font-size:13px;"></i>
                <span id="chart-growth">Memuat...</span>
            </div>
        </div>
        <div style="height:220px;position:relative;">
            <canvas id="salesChart"></canvas>
        </div>
    </div>

    <!-- Recent Transactions -->
    <div class="section-card" style="display:flex;flex-direction:column;">
        <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:16px;">
            <div class="section-title">Transaksi Terbaru</div>
            <a href="transaksi.php" style="font-size:11px;color:var(--accent-blue);font-weight:600;text-decoration:none;">Lihat Semua →</a>
        </div>
        <div style="flex:1;overflow-y:auto;max-height:210px;" id="recent-tx-list">
            <?php for($i=0;$i<4;$i++): ?>
            <div class="tx-item">
                <span class="skeleton" style="width:36px;height:36px;border-radius:10px;flex-shrink:0;"></span>
                <div style="flex:1;"><span class="skeleton" style="width:100px;height:12px;display:block;margin-bottom:5px;"></span><span class="skeleton" style="width:70px;height:10px;"></span></div>
                <span class="skeleton" style="width:70px;height:12px;"></span>
            </div>
            <?php endfor; ?>
        </div>
    </div>
</div>

<!-- Bottom Grid -->
<div class="grid-bottom">
    <!-- Top Products -->
    <div class="section-card">
        <div class="section-title" style="margin-bottom:16px;">Produk Terlaris</div>
        <div id="top-products">
            <?php for($i=0;$i<4;$i++): ?>
            <div class="tp-item">
                <span class="skeleton" style="width:32px;height:32px;border-radius:10px;flex-shrink:0;"></span>
                <div style="flex:1;"><span class="skeleton" style="width:100%;height:12px;display:block;margin-bottom:5px;"></span><span class="skeleton" style="width:60px;height:10px;"></span></div>
            </div>
            <?php endfor; ?>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="section-card">
        <div class="section-title" style="margin-bottom:16px;">Aksi Cepat</div>
        <div style="display:grid;grid-template-columns:1fr 1fr;gap:10px;">
            <a href="kasir.php" class="quick-action">
                <div class="qa-icon" style="background:rgba(96,165,250,0.12);color:#60a5fa;"><i class="ti ti-shopping-cart"></i></div>
                <span>Kasir</span>
            </a>
            <a href="produk.php" class="quick-action">
                <div class="qa-icon" style="background:rgba(249,115,22,0.12);color:#f97316;"><i class="ti ti-package"></i></div>
                <span>Produk</span>
            </a>
            <a href="jasa.php" class="quick-action">
                <div class="qa-icon" style="background:rgba(16,185,129,0.12);color:#10b981;"><i class="ti ti-tool"></i></div>
                <span>Jasa</span>
            </a>
            <a href="laporan.php" class="quick-action">
                <div class="qa-icon" style="background:rgba(167,139,250,0.12);color:#a78bfa;"><i class="ti ti-chart-bar"></i></div>
                <span>Laporan</span>
            </a>
        </div>
    </div>

    <!-- Target Progress -->
    <div class="section-card">
        <div class="section-title" style="margin-bottom:16px;">Target Omzet Harian</div>
        <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:8px;">
            <span style="font-size:11px;color:var(--text-muted);">Tercapai</span>
            <span style="font-family:'Space Grotesk',sans-serif;font-size:13px;font-weight:700;color:#fff;" id="target-pct">0%</span>
        </div>
        <div class="target-bar-wrap">
            <div class="target-bar-fill" id="target-bar" style="width:0%;"></div>
        </div>
        <div style="display:flex;justify-content:space-between;font-size:11px;color:var(--text-muted);margin-bottom:14px;">
            <div>
                <div style="font-family:'Space Grotesk',sans-serif;font-size:14px;font-weight:700;color:#fff;margin-bottom:2px;" id="target-actual">Rp 0</div>
                <div>Penjualan hari ini</div>
            </div>
            <div style="text-align:right;">
                <div style="font-family:'Space Grotesk',sans-serif;font-size:14px;font-weight:700;color:#fff;margin-bottom:2px;">Rp 5.000.000</div>
                <div>Target harian</div>
            </div>
        </div>
        <div style="background:rgba(29,106,224,0.08);border:1px solid rgba(29,106,224,0.2);border-radius:10px;padding:10px 14px;font-size:11px;color:var(--accent-blue);display:flex;align-items:center;gap:8px;" id="target-msg">
            <i class="ti ti-target" style="font-size:14px;flex-shrink:0;"></i>
            <span>Memuat data...</span>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
const TARGET = 5000000;
let chartInstance = null;

async function fetchDashboardData() {
    try {
        const response = await apiFetch('../api/dashboard/stats.php');
        if (response.status === 'success') {
            const data = response.data;
            animateCounter('stat-sales', data.sales_today, true);
            document.getElementById('stat-tx').innerText = data.tx_today + ' nota';
            document.getElementById('stat-stock').innerText = data.low_stock + ' item';
            document.getElementById('stat-queue').innerText = data.active_queue + ' mobil';
            updateTarget(data.sales_today);
            renderChart(data.chart.labels, data.chart.values);
            renderRecentTx(data.recent_tx);
            renderTopProducts(data.top_products);
        }
    } catch (e) {
        document.getElementById('recent-tx-list').innerHTML = '<p style="font-size:12px;color:#ef4444;text-align:center;padding:20px;"><i class="ti ti-alert-circle"></i> Gagal memuat data</p>';
    }
}

function animateCounter(id, target, isCurrency = false) {
    const el = document.getElementById(id);
    if (!el) return;
    const dur = 900; const steps = dur / 16; const inc = target / steps;
    let cur = 0;
    const timer = setInterval(() => {
        cur = Math.min(cur + inc, target);
        el.innerText = isCurrency
            ? new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', maximumFractionDigits: 0 }).format(cur)
            : Math.round(cur) + ' nota';
        if (cur >= target) clearInterval(timer);
    }, 16);
}

function updateTarget(sales) {
    const pct = Math.min(Math.round((sales / TARGET) * 100), 100);
    setTimeout(() => { document.getElementById('target-bar').style.width = pct + '%'; }, 100);
    document.getElementById('target-pct').innerText = pct + '%';
    document.getElementById('target-actual').innerText = new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', maximumFractionDigits: 0 }).format(sales);
    const msgEl = document.querySelector('#target-msg span');
    const fmt = v => new Intl.NumberFormat('id-ID',{style:'currency',currency:'IDR',maximumFractionDigits:0}).format(v);
    if (pct >= 100) msgEl.innerText = '🎉 Target hari ini sudah tercapai!';
    else if (pct >= 75) msgEl.innerText = `Hampir! Butuh ${fmt(TARGET - sales)} lagi`;
    else msgEl.innerText = `Semangat! Masih ada ${fmt(TARGET - sales)} menuju target`;
}

function renderChart(labels, values) {
    const ctx = document.getElementById('salesChart');
    if (!ctx) return;
    if (chartInstance) chartInstance.destroy();
    const gridColor = 'rgba(255,255,255,0.05)';
    const textColor = '#3d4f73';
    chartInstance = new Chart(ctx, {
        type: 'bar',
        data: {
            labels,
            datasets: [{
                label: 'Pendapatan',
                data: values,
                backgroundColor: (ctx) => {
                    const g = ctx.chart.ctx.createLinearGradient(0, 0, 0, 220);
                    g.addColorStop(0, '#1d6ae0'); g.addColorStop(1, 'rgba(29,106,224,0.15)');
                    return g;
                },
                borderRadius: 8, borderSkipped: false, barThickness: 20,
                hoverBackgroundColor: '#7c3aed'
            }]
        },
        options: {
            responsive: true, maintainAspectRatio: false,
            plugins: {
                legend: { display: false },
                tooltip: {
                    backgroundColor: '#0d1422', borderColor: '#1e2a45', borderWidth: 1,
                    titleColor: '#8a96b2', bodyColor: '#fff', padding: 10,
                    callbacks: { label: (ctx) => ' Rp ' + ctx.parsed.y.toLocaleString('id-ID') }
                }
            },
            scales: {
                y: { beginAtZero: true, grid: { color: gridColor, drawBorder: false }, ticks: { color: textColor, callback: (v) => 'Rp ' + (v/1000) + 'k', font: { size: 11 } }, border: { display: false } },
                x: { grid: { display: false }, ticks: { color: textColor, font: { size: 11 } }, border: { display: false } }
            }
        }
    });
    const last7sum = values.reduce((a,b)=>a+b,0);
    const avg = last7sum / values.length;
    const today = values[values.length - 1];
    const growthText = today > avg
        ? `+${Math.round(((today-avg)/avg)*100)}% dari rata-rata`
        : `${Math.round(((today-avg)/avg)*100)}% dari rata-rata`;
    document.getElementById('chart-growth').innerText = growthText;
}

function renderRecentTx(txList) {
    const el = document.getElementById('recent-tx-list');
    if (!txList || txList.length === 0) {
        el.innerHTML = '<p style="font-size:12px;color:var(--text-muted);text-align:center;padding:24px;">Belum ada transaksi hari ini.</p>';
        return;
    }
    el.innerHTML = txList.map(tx => `
        <div class="tx-item">
            <div style="width:36px;height:36px;border-radius:10px;background:rgba(29,106,224,0.12);display:flex;align-items:center;justify-content:center;color:var(--accent-blue);flex-shrink:0;">
                <i class="ti ti-receipt" style="font-size:15px;"></i>
            </div>
            <div style="flex:1;min-width:0;">
                <div style="font-size:12px;font-weight:700;color:#fff;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">${tx.invoice_no}</div>
                <div style="font-size:10px;color:var(--text-muted);">${tx.customer_name || 'Pelanggan Umum'} · ${tx.time}</div>
            </div>
            <span style="font-size:12px;font-weight:700;color:#10b981;flex-shrink:0;">Rp ${parseInt(tx.grand_total).toLocaleString('id-ID')}</span>
        </div>
    `).join('');
}

function renderTopProducts(products) {
    const el = document.getElementById('top-products');
    if (!products || products.length === 0) {
        el.innerHTML = '<p style="font-size:12px;color:var(--text-muted);text-align:center;padding:24px;">Belum ada data.</p>';
        return;
    }
    const colors = ['#60a5fa','#f97316','#10b981','#a78bfa'];
    el.innerHTML = products.map((p, i) => `
        <div class="tp-item">
            <div style="width:30px;height:30px;border-radius:8px;display:flex;align-items:center;justify-content:center;font-size:11px;font-weight:800;color:#fff;flex-shrink:0;background:${colors[i%4]};">${i+1}</div>
            <div style="flex:1;min-width:0;">
                <div style="font-size:12px;font-weight:600;color:var(--text-pri);white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">${p.name}</div>
                <div style="font-size:10px;color:var(--text-muted);">${p.qty} terjual</div>
            </div>
            <span style="font-size:11px;font-weight:700;color:var(--text-muted);">Rp ${parseInt(p.total).toLocaleString('id-ID')}</span>
        </div>
    `).join('');
}

document.addEventListener('DOMContentLoaded', fetchDashboardData);
</script>

<?php require_once '../includes/footer.php'; ?>