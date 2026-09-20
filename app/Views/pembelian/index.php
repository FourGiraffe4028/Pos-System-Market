<?php $this->extend('layout/main'); ?>
<?php $this->section('content'); ?>

<div class="d-flex justify-content-between align-items-center mb-3">
    <h4 class="fw-bold mb-0">Input Pembelian / Stok</h4>
    <a href="<?= site_url('pembelian/riwayat') ?>" class="btn btn-outline-secondary btn-sm">
        <i class="fa-solid fa-clock-rotate-left me-1"></i>Riwayat Pembelian
    </a>
</div>

<form id="formPembelian" action="<?= site_url('pembelian/store') ?>" method="POST">
    <?= csrf_field() ?>
    <input type="hidden" name="items_json" id="itemsJson">

    <!-- Header Transaksi -->
    <div class="card mb-3">
        <div class="card-header bg-white">
            <h5 class="mb-0 fs-6 fw-bold text-muted"><i class="fa-solid fa-file-invoice me-2"></i>Header Faktur Pembelian</h5>
        </div>
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-3">
                    <label class="form-label">No. Transaksi</label>
                    <input type="text" class="form-control" value="<?= esc($nextId) ?>" readonly style="background-color: #e9ecef;">
                </div>
                <div class="col-md-3">
                    <label class="form-label">Supplier <span class="text-danger">*</span></label>
                    <select name="id_supplier" id="idSupplier" class="form-select" required>
                        <?php if (count($suppliers) > 1) : ?>
                            <option value="">-- Pilih Supplier --</option>
                        <?php endif; ?>
                        <?php foreach ($suppliers as $sup) : ?>
                            <option value="<?= esc($sup['id_supplier']) ?>"><?= esc($sup['nama_supplier']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">No. Faktur Supplier</label>
                    <input type="text" name="no_faktur" class="form-control" placeholder="Contoh: FKT-99128">
                </div>
                <div class="col-md-3">
                    <label class="form-label">Tanggal Pembelian</label>
                    <input type="datetime-local" name="tanggal_beli" class="form-control" value="<?= date('Y-m-d\TH:i') ?>">
                </div>
                <div class="col-12">
                    <label class="form-label">Keterangan / Catatan</label>
                    <input type="text" name="keterangan" class="form-control" placeholder="Catatan opsional pembelian...">
                </div>
            </div>
        </div>
    </div>

    <!-- Detail Items Pembelian -->
    <div class="card mb-3">
        <div class="card-header bg-white d-flex justify-content-between align-items-center">
            <h5 class="mb-0 fs-6 fw-bold text-muted"><i class="fa-solid fa-list me-2"></i>Item Barang yang Dibeli</h5>
            <button type="button" class="btn btn-warning btn-sm fw-bold" data-bs-toggle="modal" data-bs-target="#modalPilihBarang">
                <i class="fa-solid fa-plus me-1"></i>Tambah Barang
            </button>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-bordered align-middle mb-0" id="tabelItems">
                    <thead class="table-light">
                        <tr>
                            <th style="width: 35%;">Nama Barang</th>
                            <th style="width: 20%;">Harga Beli (Satuan)</th>
                            <th style="width: 15%;">Jumlah (Qty)</th>
                            <th style="width: 20%;">Subtotal</th>
                            <th style="width: 10%;" class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody id="itemsBody">
                        <tr id="emptyRow">
                            <td colspan="5" class="text-center text-muted py-4">
                                <i class="fa-solid fa-box-open fa-2x mb-2 d-block"></i>
                                Belum ada barang yang ditambahkan. Klik tombol <strong>+ Tambah Barang</strong>.
                            </td>
                        </tr>
                    </tbody>
                    <tfoot id="itemsFoot" style="display: none;">
                        <tr class="table-light">
                            <td colspan="3" class="text-end fw-bold fs-6">TOTAL PEMBELIAN:</td>
                            <td class="fw-bold fs-5 text-warning" id="displayTotalBeli">Rp 0</td>
                            <td></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>

    <div class="d-flex justify-content-end gap-2 mb-4">
        <button type="button" id="btnSimpan" class="btn btn-warning btn-lg px-5 fw-bold" disabled>
            <i class="fa-solid fa-floppy-disk me-2"></i>Simpan Pembelian
        </button>
    </div>
</form>

<!-- Modal Pilih Barang -->
<div class="modal fade" id="modalPilihBarang" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title fw-bold"><i class="fa-solid fa-boxes-stacked me-2"></i>Pilih Barang</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="input-group mb-3">
                    <span class="input-group-text bg-white"><i class="fa-solid fa-magnifying-glass text-muted"></i></span>
                    <input type="text" id="searchBarangModal" class="form-control" placeholder="Cari nama barang atau barcode...">
                </div>
                <div class="table-responsive">
                    <table class="table table-hover align-middle" id="tabelBarangModal">
                        <thead>
                            <tr>
                                <th>Kode / Barcode</th>
                                <th>Nama Barang</th>
                                <th>Stok Saat Ini</th>
                                <th>Harga Beli Terakhir</th>
                                <th class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody id="barangModalBody">
                            <tr>
                                <td colspan="5" class="text-center text-muted py-3">Memuat data barang...</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<?php $this->endSection(); ?>

<?php $this->section('scripts'); ?>
<script>
let selectedItems = {}; // { id_barang: { id_barang, nama_barang, harga_beli, jumlah } }

function loadBarangModal(q = '') {
    const tbody = document.getElementById('barangModalBody');
    tbody.innerHTML = '<tr><td colspan="5" class="text-center text-muted py-3"><i class="fa-solid fa-spinner fa-spin me-2"></i>Memuat data...</td></tr>';

    fetch(`<?= site_url('pembelian/get-barang') ?>?q=${encodeURIComponent(q)}`)
        .then(r => r.json())
        .then(res => {
            const data = res.data || [];
            if (data.length === 0) {
                tbody.innerHTML = '<tr><td colspan="5" class="text-center text-muted py-3">Barang tidak ditemukan.</td></tr>';
                return;
            }

            tbody.innerHTML = data.map(b => `
                <tr>
                    <td>
                        <strong class="d-block">${escHtml(b.id_barang)}</strong>
                        <small class="text-muted">${escHtml(b.barcode || '-')}</small>
                    </td>
                    <td>
                        <strong class="d-block">${escHtml(b.nama_barang)}</strong>
                        <small class="text-muted">${escHtml(b.nama_satuan)}</small>
                    </td>
                    <td><span class="badge bg-secondary">${b.stok} ${escHtml(b.nama_satuan)}</span></td>
                    <td>${formatRupiah(b.harga_beli_terakhir)}</td>
                    <td class="text-center">
                        <button type="button" class="btn btn-warning btn-sm fw-bold"
                            onclick="tambahItem('${b.id_barang}', '${escHtml(b.nama_barang)}', ${b.harga_beli_terakhir})">
                            <i class="fa-solid fa-plus me-1"></i>Pilih
                        </button>
                    </td>
                </tr>
            `).join('');
        });
}

function tambahItem(id, nama, hargaTerakhir) {
    if (selectedItems[id]) {
        selectedItems[id].jumlah++;
    } else {
        selectedItems[id] = {
            id_barang: id,
            nama_barang: nama,
            harga_beli: hargaTerakhir > 0 ? hargaTerakhir : 0,
            jumlah: 1
        };
    }

    renderItems();
    bootstrap.Modal.getInstance(document.getElementById('modalPilihBarang')).hide();
}

function updateItem(id, field, value) {
    if (!selectedItems[id]) return;
    selectedItems[id][field] = parseFloat(value) || 0;
    renderItems();
}

function hapusItem(id) {
    delete selectedItems[id];
    renderItems();
}

function renderItems() {
    const tbody = document.getElementById('itemsBody');
    const foot = document.getElementById('itemsFoot');
    const btnSimpan = document.getElementById('btnSimpan');
    const items = Object.values(selectedItems);

    if (items.length === 0) {
        tbody.innerHTML = `
            <tr id="emptyRow">
                <td colspan="5" class="text-center text-muted py-4">
                    <i class="fa-solid fa-box-open fa-2x mb-2 d-block"></i>
                    Belum ada barang yang ditambahkan. Klik tombol <strong>+ Tambah Barang</strong>.
                </td>
            </tr>`;
        foot.style.display = 'none';
        btnSimpan.disabled = true;
        return;
    }

    foot.style.display = '';
    btnSimpan.disabled = false;

    let totalBeli = 0;

    tbody.innerHTML = items.map(item => {
        const subtotal = item.harga_beli * item.jumlah;
        totalBeli += subtotal;

        return `
            <tr>
                <td>
                    <strong class="d-block">${escHtml(item.nama_barang)}</strong>
                    <small class="text-muted">ID: ${escHtml(item.id_barang)}</small>
                </td>
                <td>
                    <div class="input-group input-group-sm">
                        <span class="input-group-text">Rp</span>
                        <input type="number" class="form-control" value="${item.harga_beli}" min="0" step="100"
                            onchange="updateItem('${item.id_barang}', 'harga_beli', this.value)"
                            onkeyup="updateItem('${item.id_barang}', 'harga_beli', this.value)">
                    </div>
                </td>
                <td>
                    <input type="number" class="form-control form-control-sm text-center" value="${item.jumlah}" min="1"
                        onchange="updateItem('${item.id_barang}', 'jumlah', this.value)"
                        onkeyup="updateItem('${item.id_barang}', 'jumlah', this.value)">
                </td>
                <td class="fw-bold text-end">${formatRupiah(subtotal)}</td>
                <td class="text-center">
                    <button type="button" class="btn btn-outline-danger btn-sm" onclick="hapusItem('${item.id_barang}')">
                        <i class="fa-solid fa-trash"></i>
                    </button>
                </td>
            </tr>
        `;
    }).join('');

    document.getElementById('displayTotalBeli').textContent = formatRupiah(totalBeli);
}

function formatRupiah(num) {
    return 'Rp ' + Number(num).toLocaleString('id-ID');
}

function escHtml(str) {
    const d = document.createElement('div');
    d.textContent = str;
    return d.innerHTML;
}

document.getElementById('searchBarangModal').addEventListener('input', function() {
    clearTimeout(this._t);
    this._t = setTimeout(() => loadBarangModal(this.value.trim()), 300);
});

document.getElementById('modalPilihBarang').addEventListener('show.bs.modal', function() {
    loadBarangModal();
});

document.getElementById('btnSimpan').addEventListener('click', function() {
    const items = Object.values(selectedItems);
    if (items.length === 0) return;

    const idSupplier = document.getElementById('idSupplier').value;
    if (!idSupplier) {
        Swal.fire({ icon: 'warning', title: 'Supplier Wajib Dipilih', text: 'Silakan pilih supplier terlebih dahulu.' });
        return;
    }

    Swal.fire({
        title: 'Simpan Pembelian?',
        text: 'Stok barang akan otomatis bertambah ke sistem.',
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: 'Ya, Simpan!',
        cancelButtonText: 'Batal',
        confirmButtonColor: '#a8732d',
    }).then(r => {
        if (r.isConfirmed) {
            document.getElementById('itemsJson').value = JSON.stringify(items);
            document.getElementById('formPembelian').submit();
        }
    });
});
</script>
<?php $this->endSection(); ?>

