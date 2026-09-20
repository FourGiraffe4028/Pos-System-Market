<?php $this->extend('layout/main'); ?>
<?php $this->section('content'); ?>

<div class="row justify-content-center">
    <div class="col-lg-6 col-md-8">

        <!-- Aksi Atas -->
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h4 class="fw-bold mb-0">Struk Transaksi</h4>
            <div class="d-flex gap-2">
                <button onclick="window.print()" class="btn btn-outline-secondary btn-sm">
                    <i class="fa-solid fa-print me-1"></i>Cetak
                </button>
                <a href="<?= site_url('penjualan') ?>" class="btn btn-warning btn-sm fw-bold">
                    <i class="fa-solid fa-plus me-1"></i>Transaksi Baru
                </a>
                <?php if (session()->get('role') === 'admin') : ?>
                    <a href="<?= site_url('penjualan/riwayat') ?>" class="btn btn-outline-secondary btn-sm">
                        <i class="fa-solid fa-clock-rotate-left me-1"></i>Riwayat
                    </a>
                <?php endif; ?>
            </div>
        </div>

        <!-- Struk Card -->
        <div class="card" id="strukCard">
            <div class="card-body p-4">

                <!-- Header Toko -->
                <div class="text-center mb-3">
                    <h5 class="fw-bold mb-0">POS System</h5>
                    <small class="text-muted">Sistem Penjualan Modern</small>
                    <div class="my-2" style="border-top: 2px dashed #ddd;"></div>
                </div>

                <!-- Info Transaksi -->
                <div class="row mb-3">
                    <div class="col-6">
                        <small class="text-muted d-block">No. Transaksi</small>
                        <strong style="font-size:13px;"><?= esc($penjualan['id_penjualan']) ?></strong>
                    </div>
                    <div class="col-6 text-end">
                        <small class="text-muted d-block">Tanggal</small>
                        <strong style="font-size:13px;">
                            <?= date('d/m/Y H:i', strtotime($penjualan['tanggal_jual'])) ?>
                        </strong>
                    </div>
                    <div class="col-12 mt-1">
                        <small class="text-muted">Kasir: </small>
                        <strong style="font-size:13px;"><?= esc($penjualan['nama_kasir']) ?></strong>
                    </div>
                </div>

                <div style="border-top: 1px dashed #ddd;" class="mb-3"></div>

                <!-- Detail Items -->
                <table class="table table-sm mb-0" style="font-size:13px;">
                    <tbody>
                        <?php foreach ($detail as $item) : ?>
                            <tr>
                                <td class="ps-0 border-0">
                                    <div class="fw-semibold"><?= esc($item['nama_barang']) ?></div>
                                    <small class="text-muted">
                                        <?= number_format($item['jumlah']) ?> × <?= 'Rp ' . number_format($item['harga_satuan'], 0, ',', '.') ?>
                                    </small>
                                </td>
                                <td class="text-end pe-0 border-0 fw-semibold" style="white-space:nowrap;">
                                    Rp <?= number_format($item['subtotal'], 0, ',', '.') ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>

                <div style="border-top: 1px dashed #ddd;" class="my-2"></div>

                <!-- Total -->
                <div class="d-flex justify-content-between mb-1" style="font-size:14px;">
                    <span class="text-muted">Subtotal</span>
                    <span>Rp <?= number_format($penjualan['subtotal'], 0, ',', '.') ?></span>
                </div>
                <?php if ((float)$penjualan['diskon'] > 0) : ?>
                    <div class="d-flex justify-content-between mb-1" style="font-size:14px;">
                        <span class="text-muted">Diskon</span>
                        <span class="text-danger">- Rp <?= number_format($penjualan['diskon'], 0, ',', '.') ?></span>
                    </div>
                <?php endif; ?>
                <div class="d-flex justify-content-between fw-bold" style="font-size:16px;">
                    <span>TOTAL</span>
                    <span class="text-warning">Rp <?= number_format($penjualan['total_belanja'], 0, ',', '.') ?></span>
                </div>

                <div style="border-top: 1px dashed #ddd;" class="my-2"></div>

                <!-- Pembayaran -->
                <div class="d-flex justify-content-between mb-1" style="font-size:13px;">
                    <span class="text-muted">Metode Bayar</span>
                    <span class="badge bg-secondary text-uppercase"><?= esc($penjualan['metode_bayar']) ?></span>
                </div>
                <?php if ($penjualan['metode_bayar'] === 'tunai') : ?>
                    <div class="d-flex justify-content-between mb-1" style="font-size:13px;">
                        <span class="text-muted">Bayar</span>
                        <span>Rp <?= number_format($penjualan['bayar'], 0, ',', '.') ?></span>
                    </div>
                    <div class="d-flex justify-content-between fw-bold" style="font-size:14px;">
                        <span>Kembalian</span>
                        <span class="text-success">Rp <?= number_format($penjualan['kembali'], 0, ',', '.') ?></span>
                    </div>
                <?php endif; ?>

                <div style="border-top: 2px dashed #ddd;" class="mt-3 mb-3"></div>

                <!-- Footer Struk -->
                <div class="text-center">
                    <small class="text-muted">*** Terima kasih telah berbelanja ***</small>
                    <?php if ($penjualan['status'] === 'void') : ?>
                        <div class="mt-2">
                            <span class="badge bg-danger fs-6 px-3">TRANSAKSI VOID</span>
                        </div>
                    <?php endif; ?>
                </div>

            </div>
        </div>

    </div>
</div>

<?php $this->endSection(); ?>

<?php $this->section('scripts'); ?>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const displayChannel = new BroadcastChannel('pos_customer_display');
        displayChannel.postMessage({
            type: 'TRANSACTION_COMPLETE',
            id_penjualan: '<?= esc($penjualan['id_penjualan']) ?>',
            total: <?= (float) $penjualan['total_belanja'] ?>,
            bayar: <?= (float) $penjualan['bayar'] ?>,
            kembali: <?= (float) $penjualan['kembali'] ?>,
            metode_bayar: '<?= esc($penjualan['metode_bayar']) ?>'
        });
    });
</script>
<style>
    @media print {
        .pc-sidebar, .pc-header, .pc-footer, .d-flex.justify-content-between.align-items-center.mb-3 {
            display: none !important;
        }
        .pc-container {
            margin: 0 !important;
            padding: 10px !important;
        }
        #strukCard {
            box-shadow: none !important;
            border: none !important;
        }
        .col-lg-6 { max-width: 100% !important; flex: 0 0 100% !important; }
    }
</style>
<?php $this->endSection(); ?>

