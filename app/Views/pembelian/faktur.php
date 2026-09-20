<?php $this->extend('layout/main'); ?>
<?php $this->section('content'); ?>

<div class="row justify-content-center">
    <div class="col-lg-8">

        <div class="d-flex justify-content-between align-items-center mb-3">
            <h4 class="fw-bold mb-0">Detail Faktur Pembelian</h4>
            <div class="d-flex gap-2">
                <button onclick="window.print()" class="btn btn-outline-secondary btn-sm">
                    <i class="fa-solid fa-print me-1"></i>Cetak Faktur
                </button>
                <a href="<?= site_url('pembelian/riwayat') ?>" class="btn btn-outline-secondary btn-sm">
                    <i class="fa-solid fa-clock-rotate-left me-1"></i>Riwayat Pembelian
                </a>
                <a href="<?= site_url('pembelian') ?>" class="btn btn-warning btn-sm fw-bold">
                    <i class="fa-solid fa-plus me-1"></i>Input Pembelian Baru
                </a>
            </div>
        </div>

        <div class="card" id="fakturCard">
            <div class="card-body p-4">

                <!-- Header Faktur -->
                <div class="row border-bottom pb-3 mb-3">
                    <div class="col-6">
                        <h4 class="fw-bold text-warning mb-1">POS SYSTEM</h4>
                        <p class="text-muted small mb-0">Dokumen Penerimaan Stok / Pembelian</p>
                    </div>
                    <div class="col-6 text-end">
                        <h5 class="fw-bold mb-0">FAKTUR PEMBELIAN</h5>
                        <code class="fs-6"><?= esc($pembelian['id_pembelian']) ?></code>
                    </div>
                </div>

                <!-- Info Supplier & Metadata -->
                <div class="row mb-4">
                    <div class="col-6">
                        <small class="text-muted d-block text-uppercase fw-bold" style="font-size: 11px;">Supplier:</small>
                        <h5 class="fw-bold text-dark mb-1"><?= esc($pembelian['nama_supplier']) ?></h5>
                        <p class="text-muted small mb-0">No. Faktur Supplier: <strong><?= esc($pembelian['no_faktur'] ?? '-') ?></strong></p>
                    </div>
                    <div class="col-6 text-end">
                        <small class="text-muted d-block text-uppercase fw-bold" style="font-size: 11px;">Informasi Pembelian:</small>
                        <p class="mb-0 small">Tanggal: <strong><?= date('d/m/Y H:i', strtotime($pembelian['tanggal_beli'])) ?></strong></p>
                        <p class="mb-0 small">Petugas: <strong><?= esc($pembelian['nama_pembeli']) ?></strong></p>
                        <p class="mb-0 small">Status: <span class="badge bg-success">DITERIMA</span></p>
                    </div>
                </div>

                <!-- Tabel Detail Barang -->
                <div class="table-responsive mb-4">
                    <table class="table table-bordered align-middle">
                        <thead class="table-light">
                            <tr>
                                <th style="width: 5%;">#</th>
                                <th>Barang</th>
                                <th class="text-end">Harga Beli</th>
                                <th class="text-center">Jumlah (Qty)</th>
                                <th class="text-end">Subtotal</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($detail as $i => $d) : ?>
                                <tr>
                                    <td><?= $i + 1 ?></td>
                                    <td>
                                        <strong class="d-block"><?= esc($d['nama_barang']) ?></strong>
                                        <small class="text-muted">Kode: <?= esc($d['id_barang']) ?> | Barcode: <?= esc($d['barcode'] ?? '-') ?></small>
                                    </td>
                                    <td class="text-end">Rp <?= number_format($d['harga_beli'], 0, ',', '.') ?></td>
                                    <td class="text-center">
                                        <span class="badge bg-secondary"><?= number_format($d['jumlah']) ?> <?= esc($d['nama_satuan']) ?></span>
                                    </td>
                                    <td class="text-end fw-bold">Rp <?= number_format($d['subtotal'], 0, ',', '.') ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                        <tfoot>
                            <tr class="table-light">
                                <td colspan="4" class="text-end fw-bold fs-6">TOTAL PEMBELIAN:</td>
                                <td class="text-end fw-bold fs-5 text-warning">Rp <?= number_format($pembelian['total_beli'], 0, ',', '.') ?></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>

                <?php if (! empty($pembelian['keterangan'])) : ?>
                    <div class="p-3 rounded bg-light border">
                        <small class="text-muted d-block fw-bold">Catatan / Keterangan:</small>
                        <span class="small"><?= nl2br(esc($pembelian['keterangan'])) ?></span>
                    </div>
                <?php endif; ?>

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
        #fakturCard {
            box-shadow: none !important;
            border: none !important;
        }
    }
</style>
<?php $this->endSection(); ?>

