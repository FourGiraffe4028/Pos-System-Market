<?php $this->extend('layout/main'); ?>
<?php $this->section('content'); ?>

<div class="d-flex justify-content-between align-items-center mb-3">
    <h4 class="fw-bold mb-0">Riwayat Pengajuan &amp; Approval Retur</h4>
    <?php if (in_array(session()->get('role'), ['kasir', 'admin'], true)) : ?>
        <a href="<?= site_url('retur-customer') ?>" class="btn btn-warning btn-sm fw-bold">
            <i class="fa-solid fa-plus me-1"></i>Pengajuan Retur Baru
        </a>
    <?php endif; ?>
</div>

<!-- Tabel Riwayat Retur -->
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">Daftar Seluruh Transaksi Retur</h5>
        <span class="badge bg-secondary"><?= count($returList) ?> Data Retur</span>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th style="width: 5%;">#</th>
                        <th>No. Retur</th>
                        <th>No. Penjualan</th>
                        <th>Tanggal Pengajuan</th>
                        <th>Diajukan Oleh</th>
                        <th class="text-center">Status</th>
                        <th>Approved SPV</th>
                        <th class="text-end">Total Refund</th>
                        <th class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($returList)) : ?>
                        <tr>
                            <td colspan="9" class="text-center text-muted py-4">
                                <i class="fa-solid fa-inbox fa-2x mb-2 d-block"></i>
                                Belum ada riwayat transaksi retur customer.
                            </td>
                        </tr>
                    <?php else : ?>
                        <?php foreach ($returList as $i => $r) : ?>
                            <tr>
                                <td><?= $i + 1 ?></td>
                                <td><code><?= esc($r['id_retur']) ?></code></td>
                                <td><code><?= esc($r['id_penjualan']) ?></code></td>
                                <td>
                                    <?= date('d/m/Y', strtotime($r['tanggal_retur'])) ?>
                                    <small class="text-muted d-block"><?= date('H:i', strtotime($r['tanggal_retur'])) ?></small>
                                </td>
                                <td><small class="fw-bold"><?= esc($r['nama_kasir'] ?? 'Staff') ?></small></td>
                                <td class="text-center">
                                    <?php if ($r['status'] === 'approved') : ?>
                                        <span class="badge bg-success"><i class="fa-solid fa-check me-1"></i>Approved</span>
                                    <?php elseif ($r['status'] === 'pending') : ?>
                                        <span class="badge bg-warning text-dark"><i class="fa-solid fa-clock me-1"></i>Pending SPV</span>
                                    <?php else : ?>
                                        <span class="badge bg-danger"><i class="fa-solid fa-xmark me-1"></i>Rejected</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if (! empty($r['nama_spv'])) : ?>
                                        <small class="text-success fw-bold"><i class="fa-solid fa-user-check me-1"></i><?= esc($r['nama_spv']) ?></small>
                                    <?php else : ?>
                                        <small class="text-muted">-</small>
                                    <?php endif; ?>
                                </td>
                                <td class="text-end fw-bold text-danger">
                                    Rp <?= number_format($r['total_refund'], 0, ',', '.') ?>
                                </td>
                                <td class="text-center">
                                    <div class="btn-group btn-group-sm">
                                        <a href="<?= site_url('retur-customer/detail/' . $r['id_retur']) ?>" class="btn btn-outline-secondary" title="Lihat Bukti Retur">
                                            <i class="fa-solid fa-file-invoice-dollar"></i> Detail
                                        </a>
                                        <?php if ($r['status'] === 'pending' && session()->get('role') === 'supervisor') : ?>
                                            <a href="<?= site_url('retur-customer/approve/' . $r['id_retur']) ?>" class="btn btn-success" title="Setujui">
                                                <i class="fa-solid fa-check"></i>
                                            </a>
                                            <a href="<?= site_url('retur-customer/reject/' . $r['id_retur']) ?>" class="btn btn-danger" title="Tolak">
                                                <i class="fa-solid fa-xmark"></i>
                                            </a>
                                        <?php endif; ?>
                                    </div>
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
