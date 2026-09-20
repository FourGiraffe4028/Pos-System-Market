<?php $this->extend('layout/main'); ?>
<?php $this->section('content'); ?>

<div class="d-flex justify-content-between align-items-center mb-3">
    <h4 class="fw-bold mb-0">Pengajuan Retur Customer</h4>
    <a href="<?= site_url('retur-customer/riwayat') ?>" class="btn btn-outline-secondary btn-sm">
        <i class="fa-solid fa-clock-rotate-left me-1"></i>Riwayat Retur
    </a>
</div>

<?php if (! empty($pendingList) && session()->get('role') === 'admin') : ?>
    <div class="alert alert-warning d-flex align-items-center justify-content-between mb-3" role="alert">
        <div>
            <i class="fa-solid fa-clock me-2"></i>Terdapat <strong><?= count($pendingList) ?> pengajuan retur</strong> yang sedang menunggu persetujuan Supervisor.
        </div>
        <a href="<?= site_url('retur-customer/riwayat') ?>" class="btn btn-sm btn-dark">Lihat Riwayat &amp; Approval</a>
    </div>
<?php endif; ?>

<!-- Step 1: Cari Transaksi Penjualan -->
<div class="card mb-3">
    <div class="card-header bg-white">
        <h5 class="mb-0 fs-6 fw-bold text-muted"><i class="fa-solid fa-magnifying-glass me-2"></i>1. Cari Nota Transaksi Penjualan</h5>
    </div>
    <div class="card-body">
        <div class="row g-2 align-items-center">
            <div class="col-md-6">
                <div class="input-group">
                    <span class="input-group-text bg-white"><i class="fa-solid fa-receipt text-muted"></i></span>
                    <input type="text" id="inputNoPenjualan" class="form-control" placeholder="Contoh: PJ202609170001">
                    <button type="button" id="btnCariNota" class="btn btn-warning fw-bold">
                        <i class="fa-solid fa-search me-1"></i>Cari Nota
                    </button>
                </div>
            </div>
            <div class="col-md-6">
                <small class="text-muted d-block"><i class="fa-solid fa-circle-info me-1"></i>Masukkan nomor transaksi yang tercetak di struk customer untuk memilih barang yang diretur.</small>
            </div>
        </div>
    </div>
</div>

<!-- Form Retur (Hidden awal sampai Nota ditemukan) -->
<form id="formRetur" action="<?= site_url('retur-customer/store') ?>" method="POST" style="display: none;">
    <?= csrf_field() ?>
    <input type="hidden" name="id_penjualan" id="hiddenIdPenjualan">
    <input type="hidden" name="items_json" id="itemsJson">

    <!-- Information Header -->
    <div class="card mb-3">
        <div class="card-header bg-white">
            <h5 class="mb-0 fs-6 fw-bold text-muted"><i class="fa-solid fa-file-invoice me-2"></i>2. Informasi Transaksi &amp; Alasan</h5>
        </div>
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-4">
                    <label class="form-label">No. Retur</label>
                    <input type="text" class="form-control" value="<?= esc($nextId) ?>" readonly style="background-color: #e9ecef;">
                </div>
                <div class="col-md-4">
                    <label class="form-label">No. Penjualan</label>
                    <input type="text" id="displayNoPenjualan" class="form-control" readonly style="background-color: #e9ecef;">
                </div>
                <div class="col-md-4">
                    <label class="form-label">Tanggal Penjualan</label>
                    <input type="text" id="displayTglJual" class="form-control" readonly style="background-color: #e9ecef;">
                </div>
                <div class="col-12">
                    <label class="form-label">Alasan Pengembalian Barang <span class="text-danger">*</span></label>
                    <input type="text" name="alasan" class="form-control" placeholder="Contoh: Barang cacat dari pabrik / Salah beli..." required>
                </div>
            </div>
        </div>
    </div>

    <!-- Table Item Retur -->
    <div class="card mb-3">
        <div class="card-header bg-white">
            <h5 class="mb-0 fs-6 fw-bold text-muted"><i class="fa-solid fa-boxes-packing me-2"></i>3. Pilih Barang yang Diretur</h5>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-bordered align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th style="width: 5%;" class="text-center">Pilih</th>
                            <th>Nama Barang</th>
                            <th class="text-center" style="width: 12%;">Qty Dibeli</th>
                            <th class="text-center" style="width: 12%;">Maks Retur</th>
                            <th style="width: 15%;">Harga Satuan</th>
                            <th style="width: 15%;">Qty Retur</th>
                            <th style="width: 20%;">Kondisi Barang</th>
                            <th class="text-end" style="width: 15%;">Subtotal Refund</th>
                        </tr>
                    </thead>
                    <tbody id="itemsReturBody">
                        <!-- Loaded dynamically via JS -->
                    </tbody>
                    <tfoot>
                        <tr class="table-light">
                            <td colspan="7" class="text-end fw-bold fs-6">TOTAL REFUND CUSTOMER:</td>
                            <td class="text-end fw-bold fs-5 text-warning" id="displayTotalRefund">Rp 0</td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>

    <div class="d-flex justify-content-end mb-4">
        <button type="button" id="btnSimpanRetur" class="btn btn-warning btn-lg px-5 fw-bold">
            <i class="fa-solid fa-paper-plane me-2"></i>Kirim Pengajuan ke Supervisor
        </button>
    </div>
</form>

<?php $this->endSection(); ?>

<?php $this->section('scripts'); ?>
<script>
let currentItems = [];

document.getElementById('btnCariNota').addEventListener('click', function() {
    const idPenjualan = document.getElementById('inputNoPenjualan').value.trim();
    if (!idPenjualan) {
        Swal.fire({ icon: 'warning', title: 'Nomor Transaksi Kosong', text: 'Ketikkan nomor transaksi penjualan lebih dulu.' });
        return;
    }

    Swal.showLoading();
    fetch(`<?= site_url('retur-customer/get-penjualan') ?>?id_penjualan=${encodeURIComponent(idPenjualan)}`)
        .then(r => r.json())
        .then(res => {
            Swal.close();
            if (res.status === 'error') {
                Swal.fire({ icon: 'error', title: 'Nota Tidak Ditemukan', text: res.message });
                document.getElementById('formRetur').style.display = 'none';
                return;
            }

            // Fill Header Info
            document.getElementById('hiddenIdPenjualan').value = res.penjualan.id_penjualan;
            document.getElementById('displayNoPenjualan').value = res.penjualan.id_penjualan;
            document.getElementById('displayTglJual').value = res.penjualan.tanggal_jual;

            currentItems = res.items || [];
            renderItemTable(currentItems);
            document.getElementById('formRetur').style.display = '';
        })
        .catch(() => {
            Swal.close();
            Swal.fire({ icon: 'error', title: 'Error', text: 'Terjadi kesalahan sistem saat mencari nota.' });
        });
});

function renderItemTable(items) {
    const tbody = document.getElementById('itemsReturBody');
    if (items.length === 0) {
        tbody.innerHTML = '<tr><td colspan="8" class="text-center text-muted py-3">Tidak ada item barang dalam nota ini.</td></tr>';
        return;
    }

    tbody.innerHTML = items.map((item, idx) => {
        const canRetur = item.sisa_dapat_diretur > 0;
        return `
            <tr id="row_${item.id_barang}">
                <td class="text-center">
                    <input type="checkbox" class="form-check-input item-checkbox" data-id="${item.id_barang}"
                        ${!canRetur ? 'disabled' : ''} onchange="toggleRow('${item.id_barang}')">
                </td>
                <td>
                    <strong class="d-block">${escHtml(item.nama_barang)}</strong>
                    <small class="text-muted">Kode: ${escHtml(item.id_barang)} | Satuan: ${escHtml(item.nama_satuan)}</small>
                </td>
                <td class="text-center">${item.jumlah}</td>
                <td class="text-center"><span class="badge ${canRetur ? 'bg-success' : 'bg-secondary'}">${item.sisa_dapat_diretur}</span></td>
                <td>${formatRupiah(item.harga_satuan)}</td>
                <td>
                    <input type="number" class="form-control form-control-sm text-center input-qty" id="qty_${item.id_barang}"
                        value="1" min="1" max="${item.sisa_dapat_diretur}" disabled onchange="hitungRefund()" onkeyup="hitungRefund()">
                </td>
                <td>
                    <select class="form-select form-select-sm select-kondisi" id="kondisi_${item.id_barang}" disabled>
                        <option value="reject_rusak">❌ Reject / Rusak (Tidak Tambah Stok)</option>
                        <option value="kembali_stok">🔄 Kembali ke Stok (Tambah Stok)</option>
                    </select>
                </td>
                <td class="text-end fw-bold" id="subtotal_${item.id_barang}">Rp 0</td>
            </tr>
        `;
    }).join('');

    hitungRefund();
}

function toggleRow(idBarang) {
    const chk = document.querySelector(`.item-checkbox[data-id="${idBarang}"]`);
    const qtyInput = document.getElementById(`qty_${idBarang}`);
    const kondisiSelect = document.getElementById(`kondisi_${idBarang}`);

    if (chk.checked) {
        qtyInput.disabled = false;
        kondisiSelect.disabled = false;
    } else {
        qtyInput.disabled = true;
        kondisiSelect.disabled = true;
    }

    hitungRefund();
}

function hitungRefund() {
    let totalRefund = 0;

    currentItems.forEach(item => {
        const chk = document.querySelector(`.item-checkbox[data-id="${item.id_barang}"]`);
        const subCell = document.getElementById(`subtotal_${item.id_barang}`);
        if (!chk || !subCell) return;

        if (chk.checked) {
            const qtyInput = document.getElementById(`qty_${item.id_barang}`);
            let qty = parseInt(qtyInput.value) || 0;
            if (qty > item.sisa_dapat_diretur) {
                qty = item.sisa_dapat_diretur;
                qtyInput.value = qty;
            }
            const subtotal = qty * parseFloat(item.harga_satuan);
            totalRefund += subtotal;
            subCell.textContent = formatRupiah(subtotal);
        } else {
            subCell.textContent = 'Rp 0';
        }
    });

    document.getElementById('displayTotalRefund').textContent = formatRupiah(totalRefund);
}

function formatRupiah(num) {
    return 'Rp ' + Number(num).toLocaleString('id-ID');
}

function escHtml(str) {
    const d = document.createElement('div');
    d.textContent = str;
    return d.innerHTML;
}

document.getElementById('btnSimpanRetur').addEventListener('click', function() {
    const chks = document.querySelectorAll('.item-checkbox:checked');
    if (chks.length === 0) {
        Swal.fire({ icon: 'warning', title: 'Belum Ada Barang', text: 'Pilih minimal satu barang yang akan diretur.' });
        return;
    }

    const selectedPayload = [];
    chks.forEach(chk => {
        const id = chk.dataset.id;
        const itemObj = currentItems.find(i => i.id_barang === id);
        const qty = parseInt(document.getElementById(`qty_${id}`).value) || 1;
        const kondisi = document.getElementById(`kondisi_${id}`).value;

        selectedPayload.push({
            id_barang: id,
            harga_satuan: itemObj.harga_satuan,
            jumlah_retur: qty,
            kondisi_barang: kondisi
        });
    });

    Swal.fire({
        title: 'Kirim Pengajuan Retur?',
        text: 'Pengajuan retur ini akan dikirimkan ke Supervisor untuk diperiksa & disetujui.',
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: 'Ya, Kirim Pengajuan!',
        cancelButtonText: 'Batal',
        confirmButtonColor: '#a8732d',
    }).then(r => {
        if (r.isConfirmed) {
            document.getElementById('itemsJson').value = JSON.stringify(selectedPayload);
            document.getElementById('formRetur').submit();
        }
    });
});
</script>
<?php $this->endSection(); ?>
