<?php $this->extend('layout/main'); ?>
<?php $this->section('content'); ?>

<div class="d-flex justify-content-between align-items-center mb-3">
    <h4 class="fw-bold mb-0">Kasir Penjualan</h4>
    <div class="d-flex gap-2">
        <button type="button" class="btn btn-outline-info btn-sm fw-bold" onclick="openCustomerDisplay()">
            <i class="fa-solid fa-desktop me-1"></i>Layar Customer (Dual Display)
        </button>
        <?php if (in_array(session()->get('role'), ['kasir', 'admin'], true)) : ?>
            <a href="<?= site_url('penjualan/riwayat') ?>" class="btn btn-outline-secondary btn-sm">
                <i class="fa-solid fa-clock-rotate-left me-1"></i>Riwayat Transaksi / Struk
            </a>
        <?php endif; ?>
    </div>
</div>

<div class="row g-3">

    <!-- ===================== PANEL KIRI: Daftar Barang ===================== -->
    <div class="col-lg-7">
        <div class="card h-100">
            <div class="card-header d-flex align-items-center gap-2">
                <h5 class="mb-0">Daftar Barang</h5>
            </div>
            <div class="card-body">
                <!-- Search -->
                <div class="input-group mb-3">
                    <span class="input-group-text bg-white"><i class="fa-solid fa-magnifying-glass text-muted"></i></span>
                    <input type="text" id="searchBarang" class="form-control" placeholder="Cari nama barang atau scan barcode...">
                </div>

                <!-- Grid Barang -->
                <div id="barangGrid" class="row g-2" style="max-height: 520px; overflow-y: auto;">
                    <div class="col-12 text-center text-muted py-4">
                        <i class="fa-solid fa-spinner fa-spin fa-2x"></i>
                        <p class="mt-2">Memuat data barang...</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- ===================== PANEL KANAN: Keranjang ===================== -->
    <div class="col-lg-5">
        <div class="card">
            <div class="card-header d-flex align-items-center gap-2">
                <h5 class="mb-0">Keranjang Belanja</h5>
                <span id="badgeJumlahItem" class="badge bg-warning text-dark ms-auto" style="display:none;">0 item</span>
            </div>
            <div class="card-body p-0">

                <!-- Tabel keranjang -->
                <div style="max-height: 280px; overflow-y: auto;">
                    <table class="table table-sm mb-0" id="tabelKeranjang">
                        <thead>
                            <tr>
                                <th style="width:40%">Barang</th>
                                <th style="width:20%" class="text-center">Qty</th>
                                <th style="width:25%" class="text-end">Subtotal</th>
                                <th style="width:15%"></th>
                            </tr>
                        </thead>
                        <tbody id="keranjangBody">
                            <tr id="emptyRow">
                                <td colspan="4" class="text-center text-muted py-4">
                                    <i class="fa-solid fa-cart-shopping fa-2x mb-2 d-block"></i>
                                    Keranjang masih kosong
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Summary & Payment -->
                <div class="p-3 border-top">
                    <div class="d-flex justify-content-between mb-1">
                        <span class="text-muted">Subtotal</span>
                        <strong id="displaySubtotal">Rp 0</strong>
                    </div>
                    <div class="d-flex justify-content-between mb-3">
                        <span class="fw-bold fs-6">Total Belanja</span>
                        <strong id="displayTotal" class="fs-5 text-warning">Rp 0</strong>
                    </div>

                    <hr class="my-2">

                    <form id="formTransaksi" action="<?= site_url('penjualan/store') ?>" method="POST">
                        <?= csrf_field() ?>
                        <input type="hidden" name="items_json" id="itemsJson">

                        <!-- Metode Bayar -->
                        <div class="mb-2">
                            <label class="form-label">Metode Pembayaran</label>
                            <select name="metode_bayar" id="metodeBayar" class="form-select form-select-sm">
                                <option value="tunai">💵 Tunai</option>
                                <option value="debit">💳 Debit</option>
                                <option value="kredit">💳 Kredit</option>
                                <option value="qris">📱 QRIS</option>
                                <option value="ewallet">📲 E-Wallet</option>
                            </select>
                        </div>

                        <!-- Nominal Bayar (hanya tampil untuk tunai) -->
                        <div class="mb-2" id="wrapperBayar">
                            <label class="form-label">Nominal Bayar</label>
                            <div class="input-group input-group-sm">
                                <span class="input-group-text">Rp</span>
                                <input type="number" name="bayar" id="inputBayar" class="form-control"
                                    placeholder="0" min="0" step="500">
                            </div>
                        </div>

                        <!-- Kembalian -->
                        <div class="mb-3" id="wrapperKembali">
                            <div class="d-flex justify-content-between align-items-center p-2 rounded" style="background:#f8f9fc;">
                                <span class="text-muted small">Kembalian</span>
                                <strong id="displayKembali" class="text-success">Rp 0</strong>
                            </div>
                        </div>

                        <!-- Tombol Aksi -->
                        <div class="d-flex gap-2">
                            <button type="button" id="btnKosongkan" class="btn btn-outline-danger btn-sm flex-shrink-0" title="Kosongkan keranjang">
                                <i class="fa-solid fa-trash"></i>
                            </button>
                            <button type="button" id="btnProses" class="btn btn-warning w-100 fw-bold" disabled>
                                <i class="fa-solid fa-check-circle me-1"></i>Proses Transaksi
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

</div>
<?php $this->endSection(); ?>

<?php $this->section('scripts'); ?>
<script>
// =============================================================================
//  STATE — Keranjang belanja (disimpan di memori JS)
// =============================================================================
let keranjang = {}; // { id_barang: { id_barang, nama_barang, harga_satuan, jumlah, stok_max } }
let semuaBarang = [];

// =============================================================================
//  LOAD BARANG via AJAX
// =============================================================================
function loadBarang(keyword = '') {
    const grid = document.getElementById('barangGrid');
    grid.innerHTML = `<div class="col-12 text-center text-muted py-4">
        <i class="fa-solid fa-spinner fa-spin fa-2x"></i><p class="mt-2">Memuat...</p></div>`;

    fetch(`<?= site_url('penjualan/get-barang') ?>?q=${encodeURIComponent(keyword)}`)
        .then(r => r.json())
        .then(res => {
            semuaBarang = res.data || [];
            renderGrid(semuaBarang);
        })
        .catch(() => {
            grid.innerHTML = `<div class="col-12 text-center text-danger py-4">
                <i class="fa-solid fa-triangle-exclamation fa-2x"></i><p class="mt-2">Gagal memuat barang.</p></div>`;
        });
}

function renderGrid(barang) {
    const grid = document.getElementById('barangGrid');
    if (barang.length === 0) {
        grid.innerHTML = `<div class="col-12 text-center text-muted py-4">
            <i class="fa-solid fa-box-open fa-2x mb-2 d-block"></i>Tidak ada barang ditemukan.</div>`;
        return;
    }

    grid.innerHTML = barang.map(b => {
        const stokBadge = b.stok <= 5
            ? `<span class="badge bg-danger">Stok: ${b.stok}</span>`
            : `<span class="badge bg-success">Stok: ${b.stok}</span>`;
        const harga = formatRupiah(b.harga_jual);

        return `<div class="col-6 col-xl-4">
            <div class="card card-barang h-100" style="cursor:pointer; transition:.15s;"
                 onclick="tambahKeKeranjang('${b.id_barang}','${escHtml(b.nama_barang)}',${b.harga_jual},${b.stok})">
                <div class="card-body p-2 text-center">
                    <div class="mb-1">${stokBadge}</div>
                    <p class="mb-1 small fw-semibold" style="font-size:12px; line-height:1.3;">${escHtml(b.nama_barang)}</p>
                    <p class="mb-0 fw-bold text-warning" style="font-size:13px;">${harga}</p>
                    <small class="text-muted" style="font-size:11px;">${escHtml(b.nama_satuan)}</small>
                </div>
            </div>
        </div>`;
    }).join('');
}

// =============================================================================
//  KERANJANG — Tambah, Ubah Qty, Hapus, Render
// =============================================================================
function tambahKeKeranjang(idBarang, namaBarang, harga, stokMax) {
    if (keranjang[idBarang]) {
        if (keranjang[idBarang].jumlah >= stokMax) {
            Swal.fire({ icon:'warning', title:'Stok Penuh', text:`Stok ${namaBarang} hanya tersedia ${stokMax}.`, timer:2000, showConfirmButton:false });
            return;
        }
        keranjang[idBarang].jumlah++;
    } else {
        keranjang[idBarang] = { id_barang: idBarang, nama_barang: namaBarang, harga_satuan: harga, jumlah: 1, stok_max: stokMax };
    }
    renderKeranjang();
}

function ubahQty(idBarang, delta) {
    if (!keranjang[idBarang]) return;
    keranjang[idBarang].jumlah += delta;
    if (keranjang[idBarang].jumlah <= 0) {
        delete keranjang[idBarang];
    } else if (keranjang[idBarang].jumlah > keranjang[idBarang].stok_max) {
        keranjang[idBarang].jumlah = keranjang[idBarang].stok_max;
    }
    renderKeranjang();
}

function hapusDariKeranjang(idBarang) {
    delete keranjang[idBarang];
    renderKeranjang();
}

// =============================================================================
//  DUAL DISPLAY BROADCAST CHANNEL
// =============================================================================
const displayChannel = new BroadcastChannel('pos_customer_display');

function openCustomerDisplay() {
    window.open('<?= site_url('penjualan/customer-display') ?>', 'CustomerDisplayTab', 'width=1280,height=720');
}

function syncCustomerDisplay() {
    const items = Object.values(keranjang);
    const total = items.reduce((sum, i) => sum + i.harga_satuan * i.jumlah, 0);
    displayChannel.postMessage({
        type: 'CART_UPDATE',
        items: items,
        total: total
    });
}

function renderKeranjang() {
    const tbody  = document.getElementById('keranjangBody');
    const items  = Object.values(keranjang);
    const badge  = document.getElementById('badgeJumlahItem');
    const btnProses = document.getElementById('btnProses');

    if (items.length === 0) {
        tbody.innerHTML = `<tr id="emptyRow"><td colspan="4" class="text-center text-muted py-4">
            <i class="fa-solid fa-cart-shopping fa-2x mb-2 d-block"></i>Keranjang masih kosong</td></tr>`;
        badge.style.display = 'none';
        btnProses.disabled = true;
        hitungTotal();
        syncCustomerDisplay();
        return;
    }

    badge.textContent = items.length + ' item';
    badge.style.display = '';
    btnProses.disabled = false;

    tbody.innerHTML = items.map(item => {
        const subtotal = item.harga_satuan * item.jumlah;
        return `<tr>
            <td>
                <div style="font-size:13px; font-weight:600; line-height:1.3;">${escHtml(item.nama_barang)}</div>
                <small class="text-muted">${formatRupiah(item.harga_satuan)}</small>
            </td>
            <td class="text-center">
                <div class="d-flex align-items-center justify-content-center gap-1">
                    <button class="btn btn-outline-secondary btn-sm py-0 px-1" onclick="ubahQty('${item.id_barang}',-1)">−</button>
                    <span style="min-width:20px; text-align:center;">${item.jumlah}</span>
                    <button class="btn btn-outline-secondary btn-sm py-0 px-1" onclick="ubahQty('${item.id_barang}',1)">+</button>
                </div>
            </td>
            <td class="text-end fw-semibold" style="font-size:13px;">${formatRupiah(subtotal)}</td>
            <td class="text-center">
                <button class="btn btn-outline-danger btn-sm py-0 px-1" onclick="hapusDariKeranjang('${item.id_barang}')">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </td>
        </tr>`;
    }).join('');

    hitungTotal();
    syncCustomerDisplay();
}

// =============================================================================
//  HITUNG TOTAL & KEMBALIAN
// =============================================================================
function hitungTotal() {
    const items = Object.values(keranjang);
    const total = items.reduce((sum, i) => sum + i.harga_satuan * i.jumlah, 0);

    document.getElementById('displaySubtotal').textContent = formatRupiah(total);
    document.getElementById('displayTotal').textContent    = formatRupiah(total);

    hitungKembali();
}

function hitungKembali() {
    const total  = Object.values(keranjang).reduce((sum, i) => sum + i.harga_satuan * i.jumlah, 0);
    const metode = document.getElementById('metodeBayar').value;
    const bayar  = parseFloat(document.getElementById('inputBayar').value) || 0;

    if (metode === 'tunai') {
        const kembali = bayar - total;
        document.getElementById('displayKembali').textContent = formatRupiah(Math.max(0, kembali));
        document.getElementById('displayKembali').className   = kembali >= 0 ? 'text-success fw-bold' : 'text-danger fw-bold';
    } else {
        document.getElementById('displayKembali').textContent = '-';
        document.getElementById('displayKembali').className   = 'text-muted';
    }
}

// =============================================================================
//  HELPER
// =============================================================================
function formatRupiah(angka) {
    return 'Rp ' + Number(angka).toLocaleString('id-ID');
}

function escHtml(str) {
    const d = document.createElement('div');
    d.textContent = str;
    return d.innerHTML;
}

// =============================================================================
//  EVENT LISTENERS
// =============================================================================
document.getElementById('searchBarang').addEventListener('input', function () {
    clearTimeout(this._t);
    this._t = setTimeout(() => loadBarang(this.value.trim()), 350);
});

document.getElementById('metodeBayar').addEventListener('change', function () {
    const isTunai = this.value === 'tunai';
    document.getElementById('wrapperBayar').style.display  = isTunai ? '' : 'none';
    document.getElementById('wrapperKembali').style.display = isTunai ? '' : 'none';
    if (!isTunai) document.getElementById('inputBayar').value = '';
    hitungKembali();
});

document.getElementById('inputBayar').addEventListener('input', hitungKembali);

document.getElementById('btnKosongkan').addEventListener('click', function () {
    if (Object.keys(keranjang).length === 0) return;
    Swal.fire({
        title: 'Kosongkan keranjang?',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Ya, kosongkan',
        cancelButtonText: 'Batal',
        confirmButtonColor: '#e74a3b',
    }).then(r => { if (r.isConfirmed) { keranjang = {}; renderKeranjang(); } });
});

document.getElementById('btnProses').addEventListener('click', function () {
    const items = Object.values(keranjang);
    if (items.length === 0) return;

    const total  = items.reduce((sum, i) => sum + i.harga_satuan * i.jumlah, 0);
    const metode = document.getElementById('metodeBayar').value;
    const bayar  = parseFloat(document.getElementById('inputBayar').value) || 0;

    if (metode === 'tunai' && bayar < total) {
        Swal.fire({ icon:'warning', title:'Pembayaran Kurang', text:`Nominal bayar (${formatRupiah(bayar)}) kurang dari total (${formatRupiah(total)}).` });
        return;
    }

    Swal.fire({
        title: 'Proses Transaksi?',
        html: `Total: <strong>${formatRupiah(total)}</strong><br>Metode: <strong>${metode.toUpperCase()}</strong>`,
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: 'Ya, Proses!',
        cancelButtonText: 'Batal',
        confirmButtonColor: '#a8732d',
    }).then(r => {
        if (r.isConfirmed) {
            document.getElementById('itemsJson').value = JSON.stringify(
                items.map(i => ({ id_barang: i.id_barang, harga_satuan: i.harga_satuan, jumlah: i.jumlah }))
            );
            document.getElementById('formTransaksi').submit();
        }
    });
});

// =============================================================================
//  INIT
// =============================================================================
loadBarang();
</script>

<style>
    .card-barang:hover { transform: translateY(-2px); box-shadow: 0 4px 12px rgba(168,115,45,.2); border-color: #a8732d !important; }
</style>
<?php $this->endSection(); ?>

