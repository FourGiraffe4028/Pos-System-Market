<?php $this->extend('layout/main'); ?>
<?php $this->section('content'); ?>

<div class="d-flex justify-content-between align-items-center mb-3">
    <h4 class="fw-bold mb-0">Riwayat Pembelian / Stok</h4>
    <a href="<?= site_url('pembelian') ?>" class="btn btn-warning btn-sm fw-bold">
        <i class="fa-solid fa-plus me-1"></i>Input Pembelian Baru
    </a>
</div>

<!-- Tabel Riwayat Pembelian -->
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">Daftar Transaksi Pembelian</h5>
        <span class="badge bg-secondary"><?= count($pembelian) ?> Faktur</span>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th style="width: 5%;">#</th>
                        <th>No. Pembelian</th>
                        <th>No. Faktur</th>
                        <th>Tanggal Beli</th>
                        <th>Supplier</th>
                        <th class="text-end">Total Pembelian</th>
                        <th>Status</th>
                        <th>Petugas</th>
                        <th class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($pembelian)) : ?>
                        <tr>
                            <td colspan="9" class="text-center text-muted py-4">
                                <i class="fa-solid fa-inbox fa-2x mb-2 d-block"></i>
                                Belum ada riwayat pembelian barang.
                            </td>
                        </tr>
                    <?php else : ?>
                        <?php foreach ($pembelian as $i => $p) : ?>
                            <tr>
                                <td><?= $i + 1 ?></td>
                                <td><code><?= esc($p['id_pembelian']) ?></code></td>
                                <td><?= esc($p['no_faktur'] ?? '-') ?></td>
                                <td>
                                    <?= date('d/m/Y', strtotime($p['tanggal_beli'])) ?>
                                    <small class="text-muted d-block"><?= date('H:i', strtotime($p['tanggal_beli'])) ?></small>
                                </td>
                                <td><strong class="text-dark"><?= esc($p['nama_supplier']) ?></strong></td>
                                <td class="text-end fw-bold text-warning">
                                    Rp <?= number_format($p['total_beli'], 0, ',', '.') ?>
                                </td>
                                <td>
                                    <?php if ($p['status'] === 'diterima') : ?>
                                        <span class="badge bg-success">Diterima</span>
                                    <?php elseif ($p['status'] === 'draft') : ?>
                                        <span class="badge bg-warning text-dark">Draft</span>
                                    <?php else : ?>
                                        <span class="badge bg-danger">Batal</span>
                                    <?php endif; ?>
                                </td>
                                <td><small><?= esc($p['nama_pembeli']) ?></small></td>
                                <td class="text-center">
                                    <a href="<?= site_url('pembelian/faktur/' . $p['id_pembelian']) ?>" class="btn btn-outline-secondary btn-sm" title="Lihat Detail Faktur">
                                        <i class="fa-solid fa-file-invoice"></i> Detail
                                    </a>
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

