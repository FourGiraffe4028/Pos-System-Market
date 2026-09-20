<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>

<?php
    // Defensive variable fallbacks
    $totalOmzet       = $totalOmzet ?? 0;
    $totalBarang      = $totalBarang ?? 0;
    $totalKategori    = $totalKategori ?? 0;
    $totalCustomer    = $totalCustomer ?? 0;
    $barangHabisCount = $barangHabisCount ?? 0;
    $barangHabisList  = $barangHabisList ?? [];
    $catLabels        = $catLabels ?? [];
    $catCounts        = $catCounts ?? [];
?>

<div class="d-flex justify-content-between flex-wrap flex-lg-nowrap align-items-center mb-4 border-bottom pb-3">
    <div class="d-flex flex-column">
        <h4 class="fw-bold mb-1" style="color: var(--secondary);">Dashboard</h4>
        <p class="text-muted small mb-0">Informasi ringkasan penjualan dan stok barang</p>
    </div>
</div>
<!-- Card Stats Row -->
<div class="row">
    <div class="col-md-6 col-xl-3 mb-4">
        <div class="card card-stat card-gradient-primary text-white p-3">
            <div class="card-body">
                <h6 class="text-white opacity-75">Total Omzet</h6>
                <h3 class="text-white font-weight-bold" id="totalSalesText">Rp <?= number_format($totalOmzet, 0, ',', '.') ?></h3>
                <i class="fa-solid fa-money-bill-trend-up stat-icon"></i>
                <p class="m-0 small opacity-75">Penjualan lunas</p>
            </div>
        </div>
    </div>
    <div class="col-md-6 col-xl-3 mb-4">
        <div class="card card-stat card-gradient-success text-white p-3">
            <div class="card-body">
                <h6 class="text-white opacity-75">Total Barang</h6>
                <h3 class="text-white font-weight-bold" id="totalBooksText"><?= esc($totalBarang) ?></h3>
                <i class="fa-solid fa-boxes-stacked stat-icon"></i>
                <p class="m-0 small opacity-75">Item produk aktif</p>
            </div>
        </div>
    </div>
    <div class="col-md-6 col-xl-3 mb-4">
        <div class="card card-stat card-gradient-warning text-white p-3">
            <div class="card-body">
                <h6 class="text-white opacity-75">Kategori Koleksi</h6>
                <h3 class="text-white font-weight-bold" id="totalCategoriesText"><?= esc($totalKategori) ?></h3>
                <i class="fa-solid fa-tags stat-icon"></i>
                <p class="m-0 small opacity-75">Koleksi terdaftar</p>
            </div>
        </div>
    </div>
    <div class="col-md-6 col-xl-3 mb-4">
        <div class="card card-stat card-gradient-danger text-white p-3">
            <div class="card-body">
                <h6 class="text-white opacity-75">Notifikasi Barang Habis</h6>
                <h3 class="text-white font-weight-bold" id="totalUsersText"><?= esc($barangHabisCount) ?> Item</h3>
                <i class="fa-solid fa-triangle-exclamation stat-icon"></i>
                <p class="m-0 small opacity-75"><?= $barangHabisCount > 0 ? 'Restok Segera!' : 'Stok Aman' ?></p>
            </div>
        </div>
    </div>
</div>

<!-- NOTIFIKASI DETAIL BARANG HABIS / STOK MENIPIS -->
<?php if ($barangHabisCount > 0) : ?>
    <div class="alert alert-danger shadow-sm border-0 mb-4 rounded-3 p-3">
        <div class="d-flex align-items-center justify-content-between mb-2">
            <h6 class="fw-bold mb-0 text-danger">
                <i class="fa-solid fa-bell fa-bounce me-2"></i> PERINGATAN STOK: Terdapat <?= esc($barangHabisCount) ?> Barang Mencapai / Dibawah Stok Minimum!
            </h6>
            <span class="badge bg-danger">Restok Segera</span>
        </div>
        <div class="table-responsive bg-white rounded-3 p-2 border">
            <table class="table table-sm table-hover align-middle mb-0">
                <thead>
                    <tr>
                        <th>ID Barang</th>
                        <th>Nama Barang</th>
                        <th class="text-center">Sisa Stok</th>
                        <th class="text-center">Stok Minimum</th>
                        <th>Harga Jual</th>
                        <th class="text-end">Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($barangHabisList as $bh) : ?>
                        <tr>
                            <td><code><?= esc($bh['id_barang']) ?></code></td>
                            <td class="fw-bold"><?= esc($bh['nama_barang']) ?></td>
                            <td class="text-center">
                                <span class="badge <?= $bh['stok'] == 0 ? 'bg-danger' : 'bg-warning text-dark' ?> px-2 py-1">
                                    <?= esc($bh['stok']) ?>
                                </span>
                            </td>
                            <td class="text-center"><?= esc($bh['stok_minimum']) ?></td>
                            <td>Rp <?= number_format($bh['harga_jual'], 2, ',', '.') ?></td>
                            <td class="text-end">
                                <?php if ($bh['stok'] == 0) : ?>
                                    <span class="badge bg-danger"><i class="fa-solid fa-circle-xmark me-1"></i> Habis</span>
                                <?php else : ?>
                                    <span class="badge bg-warning text-dark"><i class="fa-solid fa-triangle-exclamation me-1"></i> Menipis</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
<?php endif; ?>

<!-- Charts Row -->
<div class="row mt-2">
    <div class="col-xl-8 mb-4">
        <div class="card shadow-sm h-100">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5>Analisis Penjualan Bulanan</h5>
                <i class="fa-solid fa-ellipsis-vertical" style="color: #888; cursor: pointer;"></i>
            </div>
            <div class="card-body">
                <canvas id="lineChart" style="max-height: 350px;"></canvas>
            </div>
        </div>
    </div>

    <div class="col-xl-4 mb-4">
        <div class="card shadow-sm h-100">
            <div class="card-header">
                <h5>Distribusi Kategori</h5>
            </div>
            <div class="card-body">
                <canvas id="donutChart" style="max-height: 350px;"></canvas>
            </div>
        </div>
    </div>
</div>

<!-- Weekly Visitors Statistics Row -->
<div class="row">
    <div class="col-12 mb-4">
        <div class="card shadow-sm">
            <div class="card-header">
                <h5>Statistik Pengunjung Mingguan</h5>
            </div>
            <div class="card-body">
                <canvas id="barChart" style="max-height: 250px;"></canvas>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // --- 1. LINE CHART (Penjualan) ---
        const ctxLine = document.getElementById('lineChart').getContext('2d');
        const totalOmzetVal = <?= json_encode($totalOmzet) ?>;
        
        let monthlySales = [1.5, 2.5, 1.8, 3.2, 4.5, 6.0];
        if (totalOmzetVal > 0) {
            monthlySales[5] = parseFloat((totalOmzetVal / 1000000).toFixed(2));
        }

        new Chart(ctxLine, {
            type: 'line',
            data: {
                labels: ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun'],
                datasets: [{
                    label: 'Omzet Penjualan (Juta Rp)',
                    data: monthlySales,
                    borderColor: '#a8732d',
                    backgroundColor: 'rgba(168, 115, 45, 0.1)',
                    fill: true,
                    tension: 0.4,
                    pointRadius: 6,
                    pointBackgroundColor: '#fff',
                    pointBorderColor: '#a8732d',
                    pointBorderWidth: 3
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: {
                            color: 'rgba(0,0,0,0.05)'
                        }
                    },
                    x: {
                        grid: {
                            display: false
                        }
                    }
                }
            }
        });

        // --- 2. DONUT CHART (Kategori Distribution) ---
        const ctxDonut = document.getElementById('donutChart').getContext('2d');
        const dbCatLabels = <?= json_encode($catLabels) ?>;
        const dbCatCounts = <?= json_encode($catCounts) ?>;

        const finalLabels = dbCatLabels.length ? dbCatLabels : ['Alat Tulis', 'Makanan & Minuman', 'Kebutuhan Rumah'];
        const finalData = dbCatCounts.length ? dbCatCounts : [5, 4, 3];
        const colors = ['#a8732d', '#1cc88a', '#f6c23e', '#e74a3b', '#4e73df', '#36b9cc', '#5a5c69'];

        new Chart(ctxDonut, {
            type: 'doughnut',
            data: {
                labels: finalLabels,
                datasets: [{
                    data: finalData,
                    backgroundColor: colors.slice(0, finalLabels.length),
                    hoverOffset: 10,
                    borderWidth: 0
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            boxWidth: 12,
                            padding: 15
                        }
                    }
                },
                cutout: '72%'
            }
        });

        // --- 3. BAR CHART (Weekly Visitors) ---
        const ctxBar = document.getElementById('barChart').getContext('2d');
        new Chart(ctxBar, {
            type: 'bar',
            data: {
                labels: ['Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab', 'Min'],
                datasets: [{
                    label: 'Pengunjung Web',
                    data: [430, 590, 300, 700, 850, 920, 1100],
                    backgroundColor: '#a8732d',
                    borderRadius: 8,
                    barThickness: 15
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: {
                            color: 'rgba(0,0,0,0.05)'
                        }
                    },
                    x: {
                        grid: {
                            display: false
                        }
                    }
                }
            }
        });
    });
</script>
<?= $this->endSection() ?>
