<?php $this->extend('layout/main'); ?>
<?php $this->section('content'); ?>

<div class="d-flex justify-content-between align-items-center mb-3">
    <h4 class="fw-bold mb-0">Laporan Keuangan &amp; Operasional</h4>
    <a href="<?= site_url('laporan/cetak?tgl_awal=' . $startDate . '&tgl_akhir=' . $endDate) ?>" target="_blank" class="btn btn-warning btn-sm fw-bold">
        <i class="fa-solid fa-print me-1"></i>Cetak Laporan Keuangan
    </a>
</div>

<!-- Filter Periode Bar -->
<div class="card mb-3">
    <div class="card-body py-2">
        <form action="<?= site_url('laporan') ?>" method="GET" class="row g-2 align-items-end">
            <div class="col-md-4">
                <label class="form-label mb-1" style="font-size: 12px;">Tanggal Awal</label>
                <input type="date" name="tgl_awal" class="form-control form-control-sm" value="<?= esc($startDate) ?>">
            </div>
            <div class="col-md-4">
                <label class="form-label mb-1" style="font-size: 12px;">Tanggal Akhir</label>
                <input type="date" name="tgl_akhir" class="form-control form-control-sm" value="<?= esc($endDate) ?>">
            </div>
            <div class="col-md-4 d-flex gap-2">
                <button type="submit" class="btn btn-warning btn-sm w-100 fw-bold">
                    <i class="fa-solid fa-filter me-1"></i>Terapkan Filter
                </button>
                <a href="<?= site_url('laporan') ?>" class="btn btn-outline-secondary btn-sm" title="Reset Filter">
                    <i class="fa-solid fa-rotate"></i>
                </a>
            </div>
        </form>
    </div>
</div>

<!-- 4 Summary Stat Cards -->
<div class="row g-3 mb-4">
    <div class="col-md-3">
        <div class="card card-stat card-gradient-primary text-white">
            <div class="card-body py-3">
                <i class="fa-solid fa-cash-register stat-icon"></i>
                <div class="small mb-1 opacity-75">Total Omzet Bersih</div>
                <div class="fw-bold fs-5">Rp <?= number_format($omzetBersih ?? $totalOmzet, 0, ',', '.') ?></div>
                <small class="opacity-75">Kotor: Rp <?= number_format($totalOmzet, 0, ',', '.') ?> (<?= $totalTransaksi ?> tx)</small>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card card-stat card-gradient-danger text-white">
            <div class="card-body py-3">
                <i class="fa-solid fa-boxes-packing stat-icon"></i>
                <div class="small mb-1 opacity-75">Pengeluaran Pembelian Stok</div>
                <div class="fw-bold fs-5">Rp <?= number_format($totalPembelian, 0, ',', '.') ?></div>
                <small class="opacity-75"><?= $totalFaktur ?> faktur diterima</small>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card card-stat card-gradient-warning text-white">
            <div class="card-body py-3">
                <i class="fa-solid fa-hand-holding-dollar stat-icon"></i>
                <div class="small mb-1 opacity-75">Total Refund Retur</div>
                <div class="fw-bold fs-5">Rp <?= number_format($totalRefund, 0, ',', '.') ?></div>
                <small class="opacity-75"><?= $totalRetur ?> transaksi retur</small>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card card-stat card-gradient-success text-white">
            <div class="card-body py-3">
                <i class="fa-solid fa-chart-pie stat-icon"></i>
                <div class="small mb-1 opacity-75">Estimasi Laba Kotor</div>
                <div class="fw-bold fs-5">Rp <?= number_format($labaKotor, 0, ',', '.') ?></div>
                <small class="opacity-75">Omzet - HPP - Refund</small>
            </div>
        </div>
    </div>
</div>

<!-- Tab Navigation -->
<div class="card">
    <div class="card-header bg-white">
        <ul class="nav nav-tabs card-header-tabs" id="laporanTab" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-item nav-link active fw-bold" id="penjualan-tab" data-bs-toggle="tab" data-bs-target="#penjualanTabPane" type="button" role="tab">
                    <i class="fa-solid fa-cart-shopping me-1 text-warning"></i> Penjualan (<?= count($listPenjualan) ?>)
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-item nav-link fw-bold" id="pembelian-tab" data-bs-toggle="tab" data-bs-target="#pembelianTabPane" type="button" role="tab">
                    <i class="fa-solid fa-truck-ramp-box me-1 text-danger"></i> Pembelian Stok (<?= count($listPembelian) ?>)
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-item nav-link fw-bold" id="retur-tab" data-bs-toggle="tab" data-bs-target="#returTabPane" type="button" role="tab">
                    <i class="fa-solid fa-rotate-left me-1 text-warning"></i> Retur Customer (<?= count($listRetur) ?>)
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-item nav-link fw-bold" id="top-tab" data-bs-toggle="tab" data-bs-target="#topTabPane" type="button" role="tab">
                    <i class="fa-solid fa-award me-1 text-warning"></i> Produk Terlaris
                </button>
            </li>
        </ul>
    </div>
    <div class="card-body p-0">
        <div class="tab-content" id="laporanTabContent">

            <!-- TAB 1: PENJUALAN -->
            <div class="tab-pane fade show active" id="penjualanTabPane" role="tabpanel">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>#</th>
                                <th>No. Transaksi</th>
                                <th>Tanggal</th>
                                <th>Kasir</th>
                                <th>Metode Bayar</th>
                                <th>Status</th>
                                <th class="text-end">Total Belanja</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($listPenjualan)) : ?>
                                <tr><td colspan="7" class="text-center text-muted py-4">Tidak ada data penjualan pada periode ini.</td></tr>
                            <?php else : ?>
                                <?php foreach ($listPenjualan as $i => $pj) : ?>
                                    <tr>
                                        <td><?= $i + 1 ?></td>
                                        <td><code><?= esc($pj['id_penjualan']) ?></code></td>
                                        <td><?= date('d/m/Y H:i', strtotime($pj['tanggal_jual'])) ?></td>
                                        <td><?= esc($pj['nama_kasir']) ?></td>
                                        <td><span class="badge bg-secondary text-uppercase"><?= esc($pj['metode_bayar']) ?></span></td>
                                        <td>
                                            <?php if ($pj['status'] === 'selesai') : ?>
                                                <span class="badge bg-success">Selesai</span>
                                            <?php else : ?>
                                                <span class="badge bg-danger">VOID</span>
                                            <?php endif; ?>
                                        </td>
                                        <td class="text-end fw-bold">Rp <?= number_format($pj['total_belanja'], 0, ',', '.') ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- TAB 2: PEMBELIAN -->
            <div class="tab-pane fade" id="pembelianTabPane" role="tabpanel">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>#</th>
                                <th>No. Pembelian</th>
                                <th>No. Faktur</th>
                                <th>Tanggal Beli</th>
                                <th>Supplier</th>
                                <th>Petugas</th>
                                <th class="text-end">Total Pembelian</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($listPembelian)) : ?>
                                <tr><td colspan="7" class="text-center text-muted py-4">Tidak ada data pembelian pada periode ini.</td></tr>
                            <?php else : ?>
                                <?php foreach ($listPembelian as $i => $pb) : ?>
                                    <tr>
                                        <td><?= $i + 1 ?></td>
                                        <td><code><?= esc($pb['id_pembelian']) ?></code></td>
                                        <td><?= esc($pb['no_faktur'] ?? '-') ?></td>
                                        <td><?= date('d/m/Y H:i', strtotime($pb['tanggal_beli'])) ?></td>
                                        <td><strong><?= esc($pb['nama_supplier']) ?></strong></td>
                                        <td><?= esc($pb['nama_pembeli']) ?></td>
                                        <td class="text-end fw-bold text-danger">Rp <?= number_format($pb['total_beli'], 0, ',', '.') ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- TAB 3: RETUR -->
            <div class="tab-pane fade" id="returTabPane" role="tabpanel">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>#</th>
                                <th>No. Retur</th>
                                <th>Ref Penjualan</th>
                                <th>Tanggal Retur</th>
                                <th>Approved SPV</th>
                                <th>Alasan</th>
                                <th class="text-end">Total Refund</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($listRetur)) : ?>
                                <tr><td colspan="7" class="text-center text-muted py-4">Tidak ada data retur pada periode ini.</td></tr>
                            <?php else : ?>
                                <?php foreach ($listRetur as $i => $rt) : ?>
                                    <tr>
                                        <td><?= $i + 1 ?></td>
                                        <td><code><?= esc($rt['id_retur']) ?></code></td>
                                        <td><code><?= esc($rt['id_penjualan']) ?></code></td>
                                        <td><?= date('d/m/Y H:i', strtotime($rt['tanggal_retur'])) ?></td>
                                        <td><strong class="text-success"><?= esc($rt['nama_spv']) ?></strong></td>
                                        <td><small class="text-muted"><?= esc($rt['alasan']) ?></small></td>
                                        <td class="text-end fw-bold text-warning">Rp <?= number_format($rt['total_refund'], 0, ',', '.') ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- TAB 4: TOP PRODUK -->
            <div class="tab-pane fade" id="topTabPane" role="tabpanel">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Peringkat</th>
                                <th>Kode Barang</th>
                                <th>Nama Barang</th>
                                <th class="text-center">Total Terjual</th>
                                <th class="text-end">Total Omzet Barang</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($topProducts)) : ?>
                                <tr><td colspan="5" class="text-center text-muted py-4">Belum ada data produk terjual pada periode ini.</td></tr>
                            <?php else : ?>
                                <?php foreach ($topProducts as $i => $tp) : ?>
                                    <tr>
                                        <td>
                                            <span class="badge rounded-pill bg-warning text-dark px-3 fs-6">#<?= $i + 1 ?></span>
                                        </td>
                                        <td><code><?= esc($tp['id_barang']) ?></code></td>
                                        <td><strong><?= esc($tp['nama_barang']) ?></strong></td>
                                        <td class="text-center">
                                            <span class="badge bg-success fs-6"><?= number_format($tp['total_terjual']) ?> <?= esc($tp['nama_satuan']) ?></span>
                                        </td>
                                        <td class="text-end fw-bold">Rp <?= number_format($tp['total_omzet_barang'], 0, ',', '.') ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </div>
</div>

<?php $this->endSection(); ?>

