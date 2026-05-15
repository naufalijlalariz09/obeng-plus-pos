<?php
// pages/kasir.php
require_once '../includes/header.php';
require_role(['admin', 'kasir']);
?>

<style>
/* ── POS Layout ── */
.pos-wrap {
    display: flex;
    gap: 20px;
    height: calc(100vh - 82px);
}

/* Left panel */
.pos-left {
    flex: 1;
    display: flex;
    flex-direction: column;
    background: var(--bg-card);
    border: 1px solid var(--border);
    border-radius: 20px;
    overflow: hidden;
    min-width: 0;
}

.pos-toolbar {
    padding: 14px 16px;
    border-bottom: 1px solid var(--border);
    display: flex;
    flex-wrap: wrap;
    gap: 10px;
    align-items: center;
    justify-content: space-between;
    background: rgba(255,255,255,0.02);
    flex-shrink: 0;
}

.pos-search-wrap { position: relative; flex: 1; min-width: 220px; }
.pos-search-wrap i { position: absolute; left: 12px; top: 50%; transform: translateY(-50%); color: var(--text-muted); font-size: 16px; pointer-events: none; }
.pos-search {
    width: 100%;
    padding: 9px 14px 9px 38px;
    background: var(--bg-input);
    border: 1px solid var(--border);
    border-radius: 12px;
    color: var(--text-pri);
    font-family: 'Plus Jakarta Sans', sans-serif;
    font-size: 13px;
    outline: none;
    transition: border-color 0.15s;
}
.pos-search::placeholder { color: var(--text-muted); }
.pos-search:focus { border-color: rgba(29,106,224,0.5); box-shadow: 0 0 0 3px rgba(29,106,224,0.08); }

.pos-cat {
    padding: 9px 32px 9px 14px;
    background: var(--bg-input);
    border: 1px solid var(--border);
    border-radius: 12px;
    color: var(--text-sec);
    font-family: 'Plus Jakarta Sans', sans-serif;
    font-size: 13px;
    font-weight: 500;
    outline: none;
    cursor: pointer;
    appearance: none;
    background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' fill='none' viewBox='0 0 24 24'%3E%3Cpath stroke='%235a6380' stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='m6 9 6 6 6-6'/%3E%3C/svg%3E");
    background-repeat: no-repeat;
    background-position: right 10px center;
}
.pos-cat option { background: var(--bg-card); }

/* Product grid */
.pos-grid-wrap {
    flex: 1;
    overflow-y: auto;
    padding: 16px;
    background: rgba(0,0,0,0.1);
}

.pos-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(150px, 1fr));
    gap: 12px;
}

.pos-product-card {
    background: var(--bg-card);
    border: 1px solid var(--border);
    border-radius: 14px;
    padding: 12px;
    cursor: pointer;
    transition: all 0.18s;
    position: relative;
    overflow: hidden;
}
.pos-product-card:hover { border-color: var(--accent-blue); transform: translateY(-2px); box-shadow: 0 6px 20px rgba(0,0,0,0.3); }
.pos-product-card.out-of-stock { opacity: 0.45; cursor: not-allowed; }
.pos-product-card.out-of-stock:hover { transform: none; border-color: var(--border); }

.ppc-img {
    width: 100%;
    height: 80px;
    border-radius: 10px;
    background: rgba(29,106,224,0.08);
    display: flex; align-items: center; justify-content: center;
    color: var(--accent-blue);
    font-size: 28px;
    margin-bottom: 10px;
}

.ppc-name { font-size: 12px; font-weight: 600; color: var(--text-pri); margin-bottom: 4px; line-height: 1.3; }
.ppc-price { font-family: 'Space Grotesk', sans-serif; font-size: 13px; font-weight: 700; color: #10b981; }
.ppc-stock { font-size: 10px; color: var(--text-muted); margin-top: 2px; }
.ppc-stock.low { color: #f97316; }

.ppc-badge {
    position: absolute;
    top: 8px; right: 8px;
    background: #ef4444;
    color: #fff;
    font-size: 9px;
    font-weight: 700;
    padding: 2px 7px;
    border-radius: 20px;
    letter-spacing: 0.5px;
}

/* Loading skeleton */
.ppc-skeleton {
    background: var(--bg-card);
    border: 1px solid var(--border);
    border-radius: 14px;
    padding: 12px;
}
.sk-block {
    background: linear-gradient(90deg, #1e2a45 25%, #2d3a55 50%, #1e2a45 75%);
    background-size: 200% 100%;
    animation: shimmer 1.4s infinite;
    border-radius: 6px;
}
@keyframes shimmer { 0%{background-position:200% 0} 100%{background-position:-200% 0} }

/* Right panel - Cart */
.pos-right {
    width: 320px;
    flex-shrink: 0;
    display: flex;
    flex-direction: column;
    background: var(--bg-card);
    border: 1px solid var(--border);
    border-radius: 20px;
    overflow: hidden;
}

@media (max-width: 900px) {
    .pos-wrap { flex-direction: column; height: auto; }
    .pos-left { min-height: 50vh; }
    .pos-right { width: 100%; }
}

.cart-head {
    padding: 14px 16px;
    border-bottom: 1px solid var(--border);
    background: rgba(29,106,224,0.08);
    flex-shrink: 0;
}

.cart-head-top {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 12px;
}

.cart-title {
    font-family: 'Space Grotesk', sans-serif;
    font-size: 13px;
    font-weight: 700;
    color: #fff;
    display: flex;
    align-items: center;
    gap: 8px;
}

.cart-count {
    background: var(--accent-blue);
    color: #fff;
    font-size: 10px;
    font-weight: 700;
    padding: 2px 8px;
    border-radius: 20px;
    font-family: 'Space Grotesk', sans-serif;
}

.cart-clear-btn {
    background: transparent;
    border: 1px solid rgba(255,255,255,0.15);
    color: rgba(255,255,255,0.5);
    font-family: 'Plus Jakarta Sans', sans-serif;
    font-size: 11px;
    font-weight: 600;
    padding: 4px 10px;
    border-radius: 8px;
    cursor: pointer;
    transition: all 0.15s;
    display: flex;
    align-items: center;
    gap: 4px;
}
.cart-clear-btn:hover { color: #ef4444; border-color: rgba(239,68,68,0.4); background: rgba(239,68,68,0.05); }

.cart-inputs { display: flex; gap: 8px; }
.cart-input {
    flex: 1;
    padding: 8px 12px;
    background: rgba(255,255,255,0.06);
    border: 1px solid rgba(255,255,255,0.1);
    border-radius: 10px;
    color: #fff;
    font-family: 'Plus Jakarta Sans', sans-serif;
    font-size: 12px;
    outline: none;
    transition: border-color 0.15s;
}
.cart-input::placeholder { color: rgba(255,255,255,0.3); }
.cart-input:focus { border-color: rgba(96,165,250,0.5); }

/* Cart list */
.cart-list {
    flex: 1;
    overflow-y: auto;
    padding: 12px;
}

.cart-empty {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    height: 140px;
    text-align: center;
    opacity: 0.5;
}

.cart-empty i { font-size: 36px; color: var(--text-muted); margin-bottom: 8px; }
.cart-empty p { font-size: 12px; color: var(--text-muted); margin: 2px 0; }

/* Cart item */
.cart-item {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 10px;
    background: rgba(255,255,255,0.03);
    border: 1px solid var(--border);
    border-radius: 12px;
    margin-bottom: 8px;
    transition: border-color 0.15s;
}
.cart-item:hover { border-color: var(--border-mid); }

.ci-info { flex: 1; min-width: 0; }
.ci-name { font-size: 12px; font-weight: 600; color: var(--text-pri); white-space: nowrap; overflow: hidden; text-overflow: ellipsis; margin-bottom: 2px; }
.ci-price { font-size: 11px; color: var(--text-muted); }

.ci-controls { display: flex; align-items: center; gap: 6px; }
.ci-btn {
    width: 24px; height: 24px;
    border-radius: 7px;
    background: var(--bg-input);
    border: 1px solid var(--border);
    color: var(--text-sec);
    font-size: 14px;
    cursor: pointer;
    display: flex; align-items: center; justify-content: center;
    transition: all 0.15s;
    line-height: 1;
}
.ci-btn:hover { border-color: var(--accent-blue); color: var(--accent-blue); }
.ci-qty {
    font-family: 'Space Grotesk', sans-serif;
    font-size: 13px;
    font-weight: 700;
    color: #fff;
    min-width: 20px;
    text-align: center;
}
.ci-total { font-family: 'Space Grotesk', sans-serif; font-size: 12px; font-weight: 700; color: #10b981; white-space: nowrap; }
.ci-remove { background: transparent; border: none; color: var(--text-muted); cursor: pointer; font-size: 14px; padding: 0; margin-left: 2px; transition: color 0.15s; }
.ci-remove:hover { color: #ef4444; }

/* Cart footer */
.cart-footer {
    padding: 14px 16px;
    border-top: 1px solid var(--border);
    background: rgba(0,0,0,0.15);
    flex-shrink: 0;
}

/* Payment method buttons */
.pay-methods { display: flex; gap: 6px; margin-bottom: 14px; }
.pay-btn {
    flex: 1;
    padding: 7px 6px;
    font-family: 'Plus Jakarta Sans', sans-serif;
    font-size: 11px;
    font-weight: 700;
    border-radius: 10px;
    background: var(--bg-input);
    border: 1px solid var(--border);
    color: var(--text-muted);
    cursor: pointer;
    transition: all 0.15s;
    letter-spacing: 0.3px;
}
.pay-btn:hover { border-color: var(--border-mid); color: var(--text-sec); }
.pay-btn.active { background: rgba(29,106,224,0.12); border-color: rgba(29,106,224,0.4); color: var(--accent-blue); }

/* Summary rows */
.sum-row { display: flex; justify-content: space-between; align-items: center; margin-bottom: 8px; }
.sum-label { font-size: 12px; color: var(--text-muted); }
.sum-val { font-size: 12px; font-weight: 700; font-family: 'Space Grotesk', sans-serif; color: var(--text-pri); }

.disc-input {
    width: 60px;
    padding: 4px 8px;
    background: var(--bg-input);
    border: 1px solid var(--border);
    border-radius: 8px;
    color: var(--text-pri);
    font-family: 'Space Grotesk', sans-serif;
    font-size: 12px;
    font-weight: 700;
    text-align: right;
    outline: none;
    transition: border-color 0.15s;
}
.disc-input:focus { border-color: rgba(29,106,224,0.4); }

.total-row {
    display: flex;
    justify-content: space-between;
    align-items: flex-end;
    padding-top: 10px;
    border-top: 1px solid var(--border);
    margin-top: 4px;
    margin-bottom: 14px;
}
.total-label { font-size: 12px; font-weight: 700; color: #fff; }
.total-val { font-family: 'Space Grotesk', sans-serif; font-size: 22px; font-weight: 700; color: var(--accent-blue); }

.pay-btn-main {
    width: 100%;
    padding: 13px;
    background: linear-gradient(135deg, #1d6ae0, #7c3aed);
    border: none;
    border-radius: 14px;
    color: #fff;
    font-family: 'Plus Jakarta Sans', sans-serif;
    font-size: 14px;
    font-weight: 700;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    transition: opacity 0.15s, transform 0.15s;
    box-shadow: 0 4px 20px rgba(29,106,224,0.35);
}
.pay-btn-main:hover { opacity: 0.9; }
.pay-btn-main:active { transform: scale(0.98); }
.pay-btn-main:disabled { opacity: 0.45; cursor: not-allowed; }
</style>

<div class="pos-wrap">

    <!-- ═══ LEFT: Product Panel ═══ -->
    <div class="pos-left">
        <div class="pos-toolbar">
            <div class="pos-search-wrap">
                <i class="ti ti-barcode"></i>
                <input type="text" id="pos-search" class="pos-search" placeholder="Cari nama barang, SKU, atau scan barcode..." autofocus>
            </div>
            <select id="pos-cat" class="pos-cat">
                <option value="">Semua Kategori</option>
            </select>
        </div>

        <div class="pos-grid-wrap">
            <div class="pos-grid" id="pos-grid">
                <?php for($i=0;$i<8;$i++): ?>
                <div class="ppc-skeleton">
                    <div class="sk-block" style="width:100%;height:80px;border-radius:10px;margin-bottom:10px;"></div>
                    <div class="sk-block" style="width:75%;height:12px;margin-bottom:6px;"></div>
                    <div class="sk-block" style="width:50%;height:14px;"></div>
                </div>
                <?php endfor; ?>
            </div>
        </div>
    </div>

    <!-- ═══ RIGHT: Cart Panel ═══ -->
    <div class="pos-right">
        <div class="cart-head">
            <div class="cart-head-top">
                <div class="cart-title">
                    <i class="ti ti-shopping-cart" style="font-size:15px;"></i>
                    Detail Pesanan
                    <span class="cart-count" id="cart-count-badge">0 item</span>
                </div>
                <button class="cart-clear-btn" onclick="clearCart()">
                    <i class="ti ti-trash" style="font-size:12px;"></i> Kosongkan
                </button>
            </div>
            <div class="cart-inputs">
                <input type="text" id="c-name" class="cart-input" placeholder="Nama Customer">
                <input type="text" id="c-phone" class="cart-input" placeholder="No. HP / Plat" style="flex:none;width:130px;">
            </div>
        </div>

        <div class="cart-list" id="cart-list">
            <div class="cart-empty">
                <i class="ti ti-shopping-cart"></i>
                <p style="font-weight:600;">Keranjang masih kosong</p>
                <p style="font-size:11px;">Silakan pilih produk di samping</p>
            </div>
        </div>

        <div class="cart-footer">
            <div class="pay-methods">
                <button class="pay-btn active" data-method="Cash" onclick="setPayMethod(this)">Cash</button>
                <button class="pay-btn" data-method="Transfer" onclick="setPayMethod(this)">Transfer</button>
                <button class="pay-btn" data-method="QRIS" onclick="setPayMethod(this)">QRIS</button>
            </div>

            <div class="sum-row">
                <span class="sum-label">Subtotal</span>
                <span class="sum-val" id="c-subtotal">Rp 0</span>
            </div>

            <div class="sum-row">
                <span class="sum-label">Diskon (%)</span>
                <input type="number" id="c-discount" class="disc-input" value="0" min="0" max="100">
            </div>

            <div class="sum-row" id="discount-row" style="display:none;">
                <span class="sum-label" style="color:#f97316;">Potongan</span>
                <span class="sum-val" style="color:#f97316;" id="c-discount-amount">- Rp 0</span>
            </div>

            <div class="total-row">
                <span class="total-label">Total Bayar</span>
                <span class="total-val" id="c-total">Rp 0</span>
            </div>

            <button id="btn-pay" class="pay-btn-main" onclick="processTx()">
                <i class="ti ti-receipt" style="font-size:16px;"></i>
                Bayar & Cetak Struk
            </button>
        </div>
    </div>
</div>

<script>
function setPayMethod(btn) {
    document.querySelectorAll('.pay-btn').forEach(b => b.classList.remove('active'));
    btn.classList.add('active');
}
</script>

<?php require_once '../includes/footer.php'; ?>

<script src="/../../assets/js/kasir.js"></script>