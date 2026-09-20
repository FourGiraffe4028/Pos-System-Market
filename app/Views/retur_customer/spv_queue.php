<?php $this->extend('layout/main'); ?>
<?php $this->section('content'); ?>

<div class="d-flex justify-content-between align-items-center mb-3">
    <h4 class="fw-bold mb-0">Antrean Approval Retur (Supervisor)</h4>
    <a href="<?= site_url('retur-customer/riwayat') ?>" class="btn btn-outline-secondary btn-sm">
        <i class="fa-solid fa-clock-rotate-left me-1"></i>Riwayat Seluruh Retur
    </a>
</div>

<!-- Alert Banner -->
<div class="alert alert-warning d-flex align-items-center mb-3" role="alert">
    <i class="fa-solid fa-triangle-exclamation fa-2x me-3"></i>
    <div>
        <strong>Perhatian Supervisor:</strong> Berikut adalah daftar pengajuan pengembalian barang dari kasir yang membutuhkan persetujuan/otentikasi Anda. Tinjau barang dan alasan sebelum memberikan keputusan.
    </div>
</div>

<!-- Tabel Antrean Pending -->
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0 fw-bold"><i class="fa-solid fa-list-check me-2"></i>Pengajuan Menunggu Persetujuan</h5>
        <span class="badge bg-warning text-dark fs-6"><?= count($pendingList) ?> Menunggu Approval</span>
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
                        <th>Alasan Retur</th>
                        <th class="text-end">Total Refund</th>
                        <th class="text-center" style="width: 20%;">Aksi Keputusan</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($pendingList)) : ?>
                        <tr>
                            <td colspan="8" class="text-center text-muted py-5">
                                <i class="fa-solid fa-circle-check text-success fa-3x mb-2 d-block"></i>
                                Tidak ada antrean pengajuan retur yang menunggu approval saat ini.
                            </td>
                        </tr>
                    <?php else : ?>
                        <?php foreach ($pendingList as $i => $p) : ?>
                            <tr>
                                <td><?= $i + 1 ?></td>
                                <td><code><?= esc($p['id_retur']) ?></code></td>
                                <td><code><?= esc($p['id_penjualan']) ?></code></td>
                                <td>
                                    <?= date('d/m/Y', strtotime($p['tanggal_retur'])) ?>
                                    <small class="text-muted d-block"><?= date('H:i', strtotime($p['tanggal_retur'])) ?></small>
                                </td>
                                <td><strong class="text-dark"><?= esc($p['nama_kasir'] ?? 'Kasir') ?></strong></td>
                                <td><span class="text-muted small"><?= esc($p['alasan']) ?></span></td>
                                <td class="text-end fw-bold text-danger fs-6">
                                    Rp <?= number_format($p['total_refund'], 0, ',', '.') ?>
                                </td>
                                <td class="text-center">
                                    <div class="btn-group btn-group-sm">
                                        <a href="<?= site_url('retur-customer/detail/' . $p['id_retur']) ?>" class="btn btn-outline-secondary" title="Tinjau Detail Item">
                                            <i class="fa-solid fa-eye"></i> Detail
                                        </a>
                                        <button type="button" class="btn btn-success fw-bold" onclick="confirmApprove('<?= esc($p['id_retur']) ?>')" title="Setujui Retur">
                                            <i class="fa-solid fa-check me-1"></i>Setujui
                                        </button>
                                        <button type="button" class="btn btn-danger fw-bold" onclick="confirmReject('<?= esc($p['id_retur']) ?>')" title="Tolak Retur">
                                            <i class="fa-solid fa-xmark me-1"></i>Tolak
                                        </button>
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

<?php $this->section('scripts'); ?>
<script>
function confirmApprove(id) {
    Swal.fire({
        title: 'Setujui Retur?',
        html: `Anda akan menyetujui pengajuan retur <strong>${id}</strong>.<br><span class="text-success small">Dana refund diserahkan &amp; stok barang disesuaikan.</span>`,
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: 'Ya, Setujui!',
        cancelButtonText: 'Batal',
        confirmButtonColor: '#1cc88a',
    }).then(r => {
        if (r.isConfirmed) {
            window.location.href = `<?= site_url('retur-customer/approve') ?>/${id}`;
        }
    });
}

function confirmReject(id) {
    Swal.fire({
        title: 'Tolak Pengajuan Retur?',
        html: `Pengajuan retur <strong>${id}</strong> akan ditolak dan dibatalkan.`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Ya, Tolak!',
        cancelButtonText: 'Batal',
        confirmButtonColor: '#e74a3b',
    }).then(r => {
        if (r.isConfirmed) {
            window.location.href = `<?= site_url('retur-customer/reject') ?>/${id}`;
        }
    });
}
</script>
<?php $this->endSection(); ?>

