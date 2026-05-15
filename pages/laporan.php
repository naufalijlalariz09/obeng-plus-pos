<?php require_once '../includes/header.php'; ?>

<style>
@import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Space+Grotesk:wght@400;500;600;700&display=swap');

* { box-sizing: border-box; }

.rpt-root {
    font-family: 'Plus Jakarta Sans', sans-serif;
    background: #0a0f1e;
    min-height: 100vh;
    padding: 28px;
    color: #e8eaf2;
}

.rpt-header {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    margin-bottom: 28px;
    flex-wrap: wrap;
    gap: 16px;
}

.rpt-title-block h2 {
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

.rpt-title-block h2 i { color: #f59e0b; }
.rpt-title-block p { font-size: 13px; color: #5a6380; margin: 0; }

.rpt-actions {
    display: flex;
    align-items: center;
    gap: 8px;
}

.rpt-period-select {
    background: #111827;
    border: 1px solid #1e2a45;
    color: #c9d1e8;
    font-family: 'Plus Jakarta Sans', sans-serif;
    font-size: 13px;
    font-weight: 500;
    padding: 8px 14px;
    border-radius: 12px;
    outline: none;
    cursor: pointer;
    appearance: none;
    -webkit-appearance: none;
    background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' fill='none' viewBox='0 0 24 24'%3E%3Cpath stroke='%235a6380' stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='m6 9 6 6 6-6'/%3E%3C/svg%3E");
    background-repeat: no-repeat;
    background-position: right 10px center;
    padding-right: 32px;
}

.rpt-period-select option { background: #111827; }

.rpt-print-btn {
    background: #111827;
    border: 1px solid #1e2a45;
    color: #8a96b2;
    font-family: 'Plus Jakarta Sans', sans-serif;
    font-size: 13px;
    font-weight: 500;
    padding: 8px 16px;
    border-radius: 12px;
    cursor: pointer;
    display: flex;
    align-items: center;
    gap: 6px;
    transition: all 0.15s;
}

.rpt-print-btn:hover { border-color: #2d4070; color: #fff; background: #161e30; }

.rpt-summary-grid {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 14px;
    margin-bottom: 24px;
}

@media (max-width: 1000px) { .rpt-summary-grid { grid-template-columns: repeat(2, 1fr); } }
@media (max-width: 600px) { .rpt-summary-grid { grid-template-columns: 1fr; } }

.rpt-stat-card {
    background: #111827;
    border: 1px solid #1e2a45;
    border-radius: 18px;
    padding: 20px;
    position: relative;
    overflow: hidden;
    transition: border-color 0.2s, transform 0.2s;
}

.rpt-stat-card:hover { border-color: #2d3a65; transform: translateY(-2px); }

.rpt-stat-card .sc-top {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    margin-bottom: 14px;
}

.rpt-stat-card .sc-icon {
    width: 38px; height: 38px;
    border-radius: 10px;
    display: flex; align-items: center; justify-content: center;
    font-size: 17px;
}

.rpt-stat-card .sc-trend {
    font-size: 11px;
    font-weight: 700;
    padding: 3px 9px;
    border-radius: 20px;
}

.sc-trend.up { background: rgba(16,185,129,0.12); color: #10b981; }
.sc-trend.down { background: rgba(239,68,68,0.12); color: #ef4444; }
.sc-trend.neutral { background: rgba(148,163,184,0.1); color: #94a3b8; }

.rpt-stat-card .sc-label {
    font-size: 10px;
    font-weight: 700;
    letter-spacing: 1.2px;
    text-transform: uppercase;
    color: #3d4f73;
    margin-bottom: 6px;
}

.rpt-stat-card .sc-value {
    font-family: 'Space Grotesk', sans-serif;
    font-size: 22px;
    font-weight: 700;
    color: #fff;
    letter-spacing: -0.3px;
    margin-bottom: 2px;
}

.rpt-stat-card .sc-sub { font-size: 11px; color: #3d4f73; }

.sc-green { background: rgba(16,185,129,0.12); color: #10b981; }
.sc-orange { background: rgba(249,115,22,0.12); color: #f97316; }
.sc-blue { background: rgba(29,106,224,0.12); color: #60a5fa; }
.sc-purple { background: rgba(124,58,237,0.12); color: #a78bfa; }

.rpt-chart-section {
    display: grid;
    grid-template-columns: 1fr 340px;
    gap: 16px;
    margin-bottom: 24px;
}

@media (max-width: 1000px) { .rpt-chart-section { grid-template-columns: 1fr; } }

.rpt-chart-card {
    background: #111827;
    border: 1px solid #1e2a45;
    border-radius: 20px;
    padding: 24px;
}

.rpt-chart-card h3 {
    font-family: 'Space Grotesk', sans-serif;
    font-size: 15px;
    font-weight: 700;
    color: #fff;
    margin: 0 0 6px;
}

.rpt-chart-card .chart-sub { font-size: 12px; color: #3d4f73; margin: 0 0 20px; }

.rpt-chart-container { height: 280px; position: relative; }

.rpt-top-card {
    background: #111827;
    border: 1px solid #1e2a45;
    border-radius: 20px;
    padding: 24px;
}

.rpt-top-card h3 {
    font-family: 'Space Grotesk', sans-serif;
    font-size: 15px;
    font-weight: 700;
    color: #fff;
    margin: 0 0 20px;
    display: flex;
    align-items: center;
    gap: 8px;
}

.rpt-top-card h3 i { color: #f59e0b; }

.top-item {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 10px 0;
    border-bottom: 1px solid #1a2235;
}

.top-item:last-child { border-bottom: none; }

.top-rank {
    font-family: 'Space Grotesk', sans-serif;
    font-size: 16px;
    font-weight: 700;
    color: #1e2a45;
    width: 24px;
    text-align: center;
}

.top-rank.r1 { color: #f59e0b; }
.top-rank.r2 { color: #94a3b8; }
.top-rank.r3 { color: #cd7c3a; }

.top-info { flex: 1; min-width: 0; }
.top-name { font-size: 13px; font-weight: 600; color: #c9d1e8; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
.top-count { font-size: 11px; color: #3d4f73; }

.top-bar-wrap {
    width: 60px; height: 4px;
    background: rgba(255,255,255,0.05);
    border-radius: 2px;
    overflow: hidden;
}
.top-bar { height: 100%; background: linear-gradient(90deg, #f59e0b, #f97316); border-radius: 2px; }

.rpt-recent-section {
    background: #111827;
    border: 1px solid #1e2a45;
    border-radius: 20px;
    overflow: hidden;
}

.rpt-recent-head {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 18px 24px 14px;
    border-bottom: 1px solid #1e2a45;
}

.rpt-recent-head h3 {
    font-family: 'Space Grotesk', sans-serif;
    font-size: 14px;
    font-weight: 700;
    color: #fff;
    margin: 0;
}

.rpt-view-all {
    font-size: 12px;
    color: #60a5fa;
    text-decoration: none;
    font-weight: 600;
    display: flex;
    align-items: center;
    gap: 4px;
    cursor: pointer;
    border: none;
    background: transparent;
    padding: 0;
}

.rpt-table {
    width: 100%;
    border-collapse: collapse;
    font-size: 13px;
}

.rpt-table thead tr { background: #0d1422; }

.rpt-table thead th {
    padding: 10px 20px;
    text-align: left;
    font-size: 10px;
    font-weight: 700;
    letter-spacing: 1.2px;
    text-transform: uppercase;
    color: #2d3a55;
}

.rpt-table tbody tr {
    border-top: 1px solid #111827;
    transition: background 0.15s;
}

.rpt-table tbody tr:hover { background: #0f1929; }

.rpt-table tbody td {
    padding: 12px 20px;
    color: #8a96b2;
}

.badge-status {
    display: inline-flex;
    align-items: center;
    gap: 4px;
    font-size: 11px;
    font-weight: 700;
    padding: 3px 10px;
    border-radius: 20px;
}

.badge-lunas { background: rgba(16,185,129,0.12); color: #10b981; }
.badge-pending { background: rgba(249,115,22,0.12); color: #f97316; }

.skeleton-cell {
    height: 13px;
    background: linear-gradient(90deg, #1e2a45 25%, #2d3a55 50%, #1e2a45 75%);
    background-size: 200% 100%;
    animation: shimmer 1.5s infinite;
    border-radius: 6px;
    display: block;
}

@keyframes shimmer {
    0% { background-position: -200% 0; }
    100% { background-position: 200% 0; }
}
</style>

<div class="rpt-root">
    <div class="rpt-header">
        <div class="rpt-title-block">
            <h2>
                <i class="ti ti-chart-line"></i>
                Laporan Penjualan
            </h2>
            <p>Pantau performa pemasukan Obeng Plus Car Audio</p>
        </div>
        <div class="rpt-actions">
            <select id="l-period" onchange="loadReport()" class="rpt-period-select">
                <option value="daily">7 Hari Terakhir</option>
                <option value="monthly" selected>12 Bulan Terakhir</option>
            </select>
            <button onclick="window.print()" class="rpt-print-btn">
                <i class="ti ti-printer" style="font-size:15px;"></i> Cetak
            </button>
        </div>
    </div>

    <div class="rpt-summary-grid">
        <div class="rpt-stat-card">
            <div class="sc-top">
                <div class="sc-icon sc-green"><i class="ti ti-cash"></i></div>
                <div class="sc-trend up" id="r-income-trend">—</div>
            </div>
            <div class="sc-label">Total Pemasukan</div>
            <div class="sc-value" id="r-income">Rp 0</div>
            <div class="sc-sub">Periode terpilih</div>
        </div>
        <div class="rpt-stat-card">
            <div class="sc-top">
                <div class="sc-icon sc-orange"><i class="ti ti-receipt"></i></div>
                <div class="sc-trend neutral">Nota</div>
            </div>
            <div class="sc-label">Total Transaksi</div>
            <div class="sc-value" id="r-tx">0</div>
            <div class="sc-sub">Total nota terbuat</div>
        </div>
        <div class="rpt-stat-card">
            <div class="sc-top">
                <div class="sc-icon sc-blue"><i class="ti ti-calculator"></i></div>
                <div class="sc-trend neutral">Avg</div>
            </div>
            <div class="sc-label">Rata-rata / Transaksi</div>
            <div class="sc-value" id="r-avg">Rp 0</div>
            <div class="sc-sub">Nilai nota rata-rata</div>
        </div>
        <div class="rpt-stat-card">
            <div class="sc-top">
                <div class="sc-icon sc-purple"><i class="ti ti-users"></i></div>
                <div class="sc-trend up">Active</div>
            </div>
            <div class="sc-label">Pelanggan Unik</div>
            <div class="sc-value" id="r-cust">—</div>
            <div class="sc-sub">Periode terpilih</div>
        </div>
    </div>

    <div class="rpt-chart-section">
        <div class="rpt-chart-card">
            <h3>Grafik Pemasukan</h3>
            <p class="chart-sub">Tren penjualan berdasarkan periode</p>
            <div class="rpt-chart-container">
                <canvas id="reportChart"></canvas>
            </div>
        </div>
        <div class="rpt-top-card">
            <h3><i class="ti ti-award"></i> Top Layanan</h3>
            <div id="top-services">
                ${[...Array(4)].map((_, i) => `
                <div class="top-item">
                    <div class="top-rank">—</div>
                    <div class="top-info">
                        <div class="skeleton-cell" style="width:120px;margin-bottom:5px;"></div>
                        <div class="skeleton-cell" style="width:70px;height:10px;"></div>
                    </div>
                </div>`).join('')}
            </div>
        </div>
    </div>

    <div class="rpt-recent-section">
        <div class="rpt-recent-head">
            <h3>Transaksi Terbaru</h3>
            <button class="rpt-view-all"><i class="ti ti-external-link" style="font-size:12px;"></i> Lihat Semua</button>
        </div>
        <table class="rpt-table">
            <thead>
                <tr>
                    <th>No. Invoice</th>
                    <th>Customer</th>
                    <th>Total</th>
                    <th>Metode</th>
                    <th>Waktu</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody id="r-recent-tbody">
                ${[...Array(4)].map(() => `
                <tr>
                    <td><div class="skeleton-cell" style="width:90px;"></div></td>
                    <td><div class="skeleton-cell" style="width:120px;"></div></td>
                    <td><div class="skeleton-cell" style="width:100px;"></div></td>
                    <td><div class="skeleton-cell" style="width:60px;"></div></td>
                    <td><div class="skeleton-cell" style="width:100px;"></div></td>
                    <td><div class="skeleton-cell" style="width:60px;"></div></td>
                </tr>`).join('')}
            </tbody>
        </table>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    let chartObj = null;
    
    async function loadReport() {
        const period = document.getElementById('l-period').value;
        try {
            const res = await fetch(`../api/transactions/get_report.php?period=${period}`);
            const result = await res.json();
            
            if(result.status === 'success') {
                const data = result.data;
                const income = parseInt(data.summary.income);
                const txCount = parseInt(data.summary.transactions);
                const avg = txCount > 0 ? Math.round(income / txCount) : 0;
                
                animateCount('r-income', income, 'Rp ');
                animateCount('r-tx', txCount, '');
                animateCount('r-avg', avg, 'Rp ');
                
                const cust = data.summary.unique_customers || '—';
                document.getElementById('r-cust').textContent = typeof cust === 'number' ? cust : cust;
                
                if(chartObj) chartObj.destroy();
                const ctx = document.getElementById('reportChart').getContext('2d');
                const gradient = ctx.createLinearGradient(0, 0, 0, 280);
                gradient.addColorStop(0, 'rgba(29, 106, 224, 0.25)');
                gradient.addColorStop(1, 'rgba(29, 106, 224, 0.0)');
                
                chartObj = new Chart(ctx, {
                    type: 'line',
                    data: {
                        labels: data.labels,
                        datasets: [{
                            label: 'Pemasukan',
                            data: data.values,
                            borderColor: '#60a5fa',
                            backgroundColor: gradient,
                            fill: true,
                            tension: 0.4,
                            pointBackgroundColor: '#60a5fa',
                            pointBorderColor: '#111827',
                            pointBorderWidth: 2,
                            pointRadius: 4,
                            pointHoverRadius: 6
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
                                bodyFont: { family: 'Space Grotesk', weight: '700', size: 15 },
                                callbacks: {
                                    label: ctx => 'Rp ' + parseInt(ctx.raw).toLocaleString('id-ID')
                                }
                            }
                        },
                        scales: {
                            x: {
                                grid: { color: 'rgba(255,255,255,0.04)', drawBorder: false },
                                ticks: { color: '#3d4f73', font: { size: 11 } }
                            },
                            y: {
                                grid: { color: 'rgba(255,255,255,0.04)', drawBorder: false },
                                ticks: {
                                    color: '#3d4f73',
                                    font: { size: 11 },
                                    callback: v => 'Rp ' + (v >= 1000000 ? (v/1000000).toFixed(1)+'jt' : v.toLocaleString('id-ID'))
                                }
                            }
                        }
                    }
                });
                
                if(data.top_services && data.top_services.length > 0) {
                    const maxVal = data.top_services[0].count;
                    const rankClasses = ['r1','r2','r3'];
                    const rankEmoji = ['🥇','🥈','🥉'];
                    document.getElementById('top-services').innerHTML = data.top_services.slice(0,5).map((s, i) => `
                        <div class="top-item">
                            <div class="top-rank ${rankClasses[i] || ''}">${i < 3 ? rankEmoji[i] : (i+1)}</div>
                            <div class="top-info">
                                <div class="top-name">${s.name}</div>
                                <div class="top-count">${s.count}x terjual</div>
                            </div>
                            <div class="top-bar-wrap"><div class="top-bar" style="width:${Math.round((s.count/maxVal)*100)}%;"></div></div>
                        </div>
                    `).join('');
                } else {
                    document.getElementById('top-services').innerHTML = '<div style="padding:20px;text-align:center;color:#3d4f73;font-size:13px;">Tidak ada data</div>';
                }
                
                if(data.recent && data.recent.length > 0) {
                    document.getElementById('r-recent-tbody').innerHTML = data.recent.slice(0,8).map(t => `
                        <tr>
                            <td style="font-weight:700;color:#60a5fa;font-family:'Space Grotesk',sans-serif;">${t.invoice_no}</td>
                            <td style="color:#c9d1e8;">${t.customer_name || 'Umum'}</td>
                            <td style="font-weight:600;color:#10b981;">Rp ${parseInt(t.grand_total).toLocaleString('id-ID')}</td>
                            <td style="color:#5a6380;font-size:12px;">${t.payment_method || 'Tunai'}</td>
                            <td style="color:#3d4f73;font-size:12px;">${new Date(t.created_at).toLocaleString('id-ID',{day:'2-digit',month:'short',hour:'2-digit',minute:'2-digit'})}</td>
                            <td><span class="badge-status badge-lunas"><i class="ti ti-check" style="font-size:10px;"></i> Lunas</span></td>
                        </tr>
                    `).join('');
                } else {
                    document.getElementById('r-recent-tbody').innerHTML = '<tr><td colspan="6" style="padding:40px;text-align:center;color:#3d4f73;">Tidak ada transaksi</td></tr>';
                }
            }
        } catch(e) {
            console.error('Failed to load report', e);
        }
    }
    
    function animateCount(id, target, prefix='') {
        const el = document.getElementById(id);
        const duration = 700;
        const start = performance.now();
        const step = (now) => {
            const p = Math.min((now - start) / duration, 1);
            const eased = 1 - Math.pow(1 - p, 3);
            const cur = Math.round(eased * target);
            el.textContent = prefix + cur.toLocaleString('id-ID');
            if(p < 1) requestAnimationFrame(step);
        };
        requestAnimationFrame(step);
    }
    
    loadReport();
</script>

<?php require_once '../includes/footer.php'; ?>