<?php $this->extend('layout/main'); ?>
<?php $this->section('content'); ?>

<div class="d-flex justify-content-between align-items-center mb-3">
    <h4 class="fw-bold mb-0">Riwayat Transaksi</h4>
    <a href="<?= site_url('penjualan') ?>" class="btn btn-warning btn-sm fw-bold">
        <i class="fa-solid fa-plus me-1"></i>Transaksi Baru
    </a>
</div>

<!-- Filter Bar -->
<div class="card mb-3">
    <div class="card-body py-2">
        <div class="row g-2 align-items-end">
            <div class="col-md-3">
                <label class="form-label mb-1" style="font-size:12px;">Dari Tanggal</label>
                <input type="date" id="filterDari" class="form-control form-control-sm">
            </div>
            <div class="col-md-3">
                <label class="form-label mb-1" style="font-size:12px;">Sampai Tanggal</label>
                <input type="date" id="filterSampai" class="form-control form-control-sm">
            </div>
            <div class="col-md-3">
                <label class="form-label mb-1" style="font-size:12px;">Status</label>
                <select id="filterStatus" class="form-select form-select-sm">
                    <option value="">Semua Status</option>
                    <option value="selesai">Selesai</option>
                    <option value="void">Void</option>
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label mb-1" style="font-size:12px;">Cari Kasir / No. Transaksi</label>
                <input type="text" id="filterSearch" class="form-control form-control-sm" placeholder="Ketik untuk cari...">
            </div>
        </div>
    </div>
</div>

<!-- Summary Cards -->
<?php
    $totalSelesai  = array_sum(array_column(array_filter($transaksi, fn($t) => $t['status'] === 'selesai'), 'total_belanja'));
    $jumlahSelesai = count(array_filter($transaksi, fn($t) => $t['status'] === 'selesai'));
    $jumlahVoid    = count(array_filter($transaksi, fn($t) => $t['status'] === 'void'));
?>
<div class="row g-3 mb-3">
    <div class="col-md-4">
        <div class="card card-stat card-gradient-primary text-white">
            <div class="card-body py-3">
                <i class="fa-solid fa-money-bill-wave stat-icon"></i>
                <div class="small mb-1 opacity-75">Total Pendapatan</div>
                <div class="fw-bold fs-5">Rp <?= number_format($totalSelesai, 0, ',', '.') ?></div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card card-stat card-gradient-success text-white">
            <div class="card-body py-3">
                <i class="fa-solid fa-circle-check stat-icon"></i>
                <div class="small mb-1 opacity-75">Transaksi Selesai</div>
                <div class="fw-bold fs-5"><?= $jumlahSelesai ?> transaksi</div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card card-stat card-gradient-danger text-white">
            <div class="card-body py-3">
                <i class="fa-solid fa-ban stat-icon"></i>
                <div class="small mb-1 opacity-75">Transaksi Void</div>
                <div class="fw-bold fs-5"><?= $jumlahVoid ?> transaksi</div>
            </div>
        </div>
    </div>
</div>

<!-- Tabel Riwayat -->
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">Daftar Semua Transaksi</h5>
        <span id="jumlahTampil" class="badge bg-secondary">-</span>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table mb-0" id="tabelRiwayat">
                <thead>
                    <tr>
                        <th style="width:5%">#</th>
                        <th>No. Transaksi</th>
                        <th>Tanggal &amp; Jam</th>
                        <th>Kasir</th>
                        <th class="text-end">Total</th>
                        <th class="text-center">Metode</th>
                        <th class="text-center">Status</th>
                        <th class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody id="riwayatBody">
                    <?php if (empty($transaksi)) : ?>
                        <tr>
                            <td colspan="8" class="text-center text-muted py-4">
                                <i class="fa-solid fa-inbox fa-2x mb-2 d-block"></i>
                                Belum ada data transaksi.
                            </td>
                        </tr>
                    <?php else : ?>
                        <?php foreach ($transaksi as $i => $t) :
                            $isVoid = $t['status'] === 'void';
                        ?>
                            <tr class="tr-riwayat <?= $isVoid ? 'table-secondary' : '' ?>"
                                data-id="<?= esc($t['id_penjualan']) ?>"
                                data-kasir="<?= strtolower(esc($t['nama_kasir'])) ?>"
                                data-tanggal="<?= date('Y-m-d', strtotime($t['tanggal_jual'])) ?>"
                                data-status="<?= esc($t['status']) ?>">
                                <td class="text-muted"><?= $i + 1 ?></td>
                                <td><code><?= esc($t['id_penjualan']) ?></code></td>
                                <td style="font-size:13px;">
                                    <?= date('d/m/Y', strtotime($t['tanggal_jual'])) ?>
                                    <br><small class="text-muted"><?= date('H:i:s', strtotime($t['tanggal_jual'])) ?></small>
                                </td>
                                <td><?= esc($t['nama_kasir']) ?></td>
                                <td class="text-end fw-semibold">
                                    Rp <?= number_format($t['total_belanja'], 0, ',', '.') ?>
                                </td>
                                <td class="text-center">
                                    <span class="badge bg-info text-dark text-uppercase">
                                        <?= esc($t['metode_bayar']) ?>
                                    </span>
                                </td>
                                <td class="text-center">
                                    <?php if ($isVoid) : ?>
                                        <span class="badge bg-danger">VOID</span>
                                    <?php else : ?>
                                        <span class="badge bg-success">Selesai</span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-center">
                                    <a href="<?= site_url('penjualan/struk/' . $t['id_penjualan']) ?>"
                                       class="btn btn-outline-secondary btn-sm py-0 px-2"
                                       title="Lihat Struk">
                                        <i class="fa-solid fa-eye"></i>
                                    </a>
                                    <?php if (session()->get('role') === 'admin' && ! $isVoid) : ?>
                                        <button type="button"
                                            class="btn btn-outline-danger btn-sm py-0 px-2 btn-void"
                                            data-id="<?= esc($t['id_penjualan']) ?>"
                                            title="Void Transaksi">
                                            <i class="fa-solid fa-ban"></i>
                                        </button>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php $this->endSection(); ?>

<?php $this->section('scripts'); ?>
<script>
// =============================================================================
//  FILTER (client-side)
// =============================================================================
function applyFilter() {
    const dari    = document.getElementById('filterDari').value;
    const sampai  = document.getElementById('filterSampai').value;
    const status  = document.getElementById('filterStatus').value;
    const keyword = document.getElementById('filterSearch').value.toLowerCase();

    const rows = document.querySelectorAll('.tr-riwayat');
    let visible = 0;

    rows.forEach(row => {
        const tglRow  = row.dataset.tanggal;
        const statRow = row.dataset.status;
        const idRow   = row.dataset.id.toLowerCase();
        const kasirRow = row.dataset.kasir;

        let show = true;
        if (dari    && tglRow < dari)       show = false;
        if (sampai  && tglRow > sampai)     show = false;
        if (status  && statRow !== status)  show = false;
        if (keyword && ! idRow.includes(keyword) && ! kasirRow.includes(keyword)) show = false;

        row.style.display = show ? '' : 'none';
        if (show) visible++;
    });

    document.getElementById('jumlahTampil').textContent = visible + ' data';
}

['filterDari','filterSampai','filterStatus','filterSearch'].forEach(id => {
    document.getElementById(id).addEventListener('input', applyFilter);
});

// Init: hitung total tampil
applyFilter();

// =============================================================================
//  VOID via SweetAlert2
// =============================================================================
document.querySelectorAll('.btn-void').forEach(btn => {
    btn.addEventListener('click', function () {
        const id = this.dataset.id;
        Swal.fire({
            title: 'Void Transaksi?',
            html: `Transaksi <strong>${id}</strong> akan dibatalkan.<br>
                   <span class="text-danger small">Stok barang akan dikembalikan.</span>`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Ya, Void!',
            cancelButtonText: 'Batal',
            confirmButtonColor: '#e74a3b',
        }).then(result => {
            if (result.isConfirmed) {
                window.location.href = `<?= site_url('penjualan/void') ?>/${id}`;
            }
        });
    });
});
</script>
<?php $this->endSection(); ?>

