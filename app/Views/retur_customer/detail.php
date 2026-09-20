<?php $this->extend('layout/main'); ?>
<?php $this->section('content'); ?>

<div class="row justify-content-center">
    <div class="col-lg-8">

        <div class="d-flex justify-content-between align-items-center mb-3">
            <h4 class="fw-bold mb-0">Bukti Retur Customer</h4>
            <div class="d-flex gap-2">
                <button onclick="window.print()" class="btn btn-outline-secondary btn-sm">
                    <i class="fa-solid fa-print me-1"></i>Cetak Bukti
                </button>
                <a href="<?= site_url('retur-customer/riwayat') ?>" class="btn btn-outline-secondary btn-sm">
                    <i class="fa-solid fa-clock-rotate-left me-1"></i>Riwayat Retur
                </a>
                <a href="<?= site_url('retur-customer') ?>" class="btn btn-warning btn-sm fw-bold">
                    <i class="fa-solid fa-plus me-1"></i>Retur Baru
                </a>
            </div>
        </div>

        <div class="card" id="returCard">
            <div class="card-body p-4">

                <!-- Header -->
                <div class="row border-bottom pb-3 mb-3">
                    <div class="col-6">
                        <h4 class="fw-bold text-warning mb-1">POS SYSTEM</h4>
                        <p class="text-muted small mb-0">Bukti Pengembalian Barang &amp; Refund Customer</p>
                    </div>
                    <div class="col-6 text-end">
                        <h5 class="fw-bold mb-0">NOTA RETUR</h5>
                        <code class="fs-6"><?= esc($retur['id_retur']) ?></code>
                    </div>
                </div>

                <!-- Info Meta -->
                <div class="row mb-4">
                    <div class="col-6">
                        <small class="text-muted d-block text-uppercase fw-bold" style="font-size: 11px;">Ref Transaksi Penjualan:</small>
                        <h6 class="fw-bold text-dark mb-1"><code><?= esc($retur['id_penjualan']) ?></code></h6>
                        <p class="text-muted small mb-0">Tanggal Retur: <strong><?= date('d/m/Y H:i', strtotime($retur['tanggal_retur'])) ?></strong></p>
                    </div>
                    <div class="col-6 text-end">
                        <small class="text-muted d-block text-uppercase fw-bold" style="font-size: 11px;">Persetujuan &amp; Otorisasi:</small>
                        <p class="mb-0 small">Approved by SPV: <strong class="text-success"><?= esc($retur['nama_spv']) ?></strong></p>
                        <p class="mb-0 small">Status: <span class="badge bg-success">DISERAHKAN / REFUND</span></p>
                    </div>
                </div>

                <!-- Tabel Item Barang Retur -->
                <div class="table-responsive mb-3">
                    <table class="table table-bordered align-middle">
                        <thead class="table-light">
                            <tr>
                                <th style="width: 5%;">#</th>
                                <th>Barang</th>
                                <th class="text-end">Harga Satuan</th>
                                <th class="text-center">Jumlah Diretur</th>
                                <th>Kondisi Barang</th>
                                <th class="text-end">Subtotal Refund</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($detail as $i => $d) : ?>
                                <tr>
                                    <td><?= $i + 1 ?></td>
                                    <td>
                                        <strong class="d-block"><?= esc($d['nama_barang']) ?></strong>
                                        <small class="text-muted">Kode: <?= esc($d['id_barang']) ?></small>
                                    </td>
                                    <td class="text-end">Rp <?= number_format($d['harga_satuan'], 0, ',', '.') ?></td>
                                    <td class="text-center">
                                        <span class="badge bg-secondary"><?= number_format($d['jumlah_retur']) ?> <?= esc($d['nama_satuan']) ?></span>
                                    </td>
                                    <td>
                                        <?php if ($d['kondisi_barang'] === 'kembali_stok') : ?>
                                            <span class="badge bg-info text-dark">🔄 Kembali ke Stok</span>
                                        <?php else : ?>
                                            <span class="badge bg-danger">❌ Reject / Rusak</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-end fw-bold">Rp <?= number_format($d['subtotal_refund'], 0, ',', '.') ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                        <tfoot>
                            <tr class="table-light">
                                <td colspan="5" class="text-end fw-bold fs-6">TOTAL DANA REFUND:</td>
                                <td class="text-end fw-bold fs-5 text-danger">Rp <?= number_format($retur['total_refund'], 0, ',', '.') ?></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>

                <div class="p-3 rounded bg-light border">
                    <small class="text-muted d-block fw-bold">Alasan Pengembalian:</small>
                    <span class="small"><?= nl2br(esc($retur['alasan'])) ?></span>
                </div>

            </div>
        </div>

    </div>
</div>

<?php $this->endSection(); ?>

<?php $this->section('scripts'); ?>
<style>
    @media print {
        .pc-sidebar, .pc-header, .pc-footer, .d-flex.justify-content-between.align-items-center.mb-3 {
            display: none !important;
        }
        .pc-container {
            margin: 0 !important;
            padding: 10px !important;
        }
        #returCard {
            box-shadow: none !important;
            border: none !important;
        }
    }
</style>
<?php $this->endSection(); ?>

