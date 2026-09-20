<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= esc($title) ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background: #fff; color: #333; }
        .header-print { border-bottom: 3px double #333; padding-bottom: 15px; margin-bottom: 20px; }
        @media print {
            .no-print { display: none !important; }
            body { padding: 0; }
        }
    </style>
</head>
<body class="p-4" onload="window.print()">

    <div class="no-print mb-4 d-flex justify-content-between align-items-center bg-light p-3 border rounded">
        <span>Tekan tombol di samping untuk mencetak Laporan Keuangan ini.</span>
        <button onclick="window.print()" class="btn btn-warning fw-bold">
            Print Laporan
        </button>
    </div>

    <!-- Header Dokumen Cetak -->
    <div class="header-print text-center">
        <h2 class="fw-bold mb-1">POS SYSTEM ENTERPRISE</h2>
        <h5 class="fw-normal mb-1">LAPORAN KEUANGAN &amp; LABA KOTOR</h5>
        <p class="mb-0 text-muted">Periode: <strong><?= date('d/m/Y', strtotime($startDate)) ?></strong> s/d <strong><?= date('d/m/Y', strtotime($endDate)) ?></strong></p>
    </div>

    <!-- Ringkasan Laporan -->
    <h5 class="fw-bold mb-3">I. Ringkasan Eksekutif Keuangan</h5>
    <table class="table table-bordered align-middle mb-4">
        <thead class="table-light">
            <tr>
                <th>Komponen Keuangan</th>
                <th class="text-end">Jumlah Nominal</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td><strong>1. Total Omzet Kotor Penjualan Lunas</strong></td>
                <td class="text-end fw-bold">Rp <?= number_format($totalOmzet, 0, ',', '.') ?></td>
            </tr>
            <tr>
                <td><strong>2. Total Pengembalian Dana Refund (Retur Customer Approved)</strong></td>
                <td class="text-end text-warning">− Rp <?= number_format($totalRefund, 0, ',', '.') ?></td>
            </tr>
            <tr class="table-light">
                <td><strong>3. Total Omzet Bersih Penjualan (Setelah Retur)</strong></td>
                <td class="text-end fw-bold text-success">Rp <?= number_format($omzetBersih ?? ($totalOmzet - $totalRefund), 0, ',', '.') ?></td>
            </tr>
            <tr>
                <td><strong>4. Total HPP (Harga Pokok Pembelian Barang Terjual)</strong></td>
                <td class="text-end text-muted">Rp <?= number_format($totalHpp, 0, ',', '.') ?></td>
            </tr>
            <tr>
                <td><strong>5. Total Pengeluaran Pembelian Stok (Faktur Diterima)</strong></td>
                <td class="text-end text-danger">Rp <?= number_format($totalPembelian, 0, ',', '.') ?></td>
            </tr>
            <tr class="table-light">
                <td class="fw-bold fs-6">ESTIMASI LABA KOTOR (GROSS PROFIT):</td>
                <td class="text-end fw-bold fs-5 text-dark">Rp <?= number_format($labaKotor, 0, ',', '.') ?></td>
            </tr>
        </tbody>
    </table>

    <h5 class="fw-bold mb-3">II. Catatan Penting &amp; Pengesahan</h5>
    <div class="row mt-4">
        <div class="col-6">
            <p class="small text-muted mb-0">Catatan:</p>
            <ul class="small text-muted ps-3">
                <li>Laporan ini secara otomatis di-generate oleh Sistem Informasi POS.</li>
                <li>Laba kotor dihitung berdasarkan formula `Omzet Penjualan - HPP Modal Beli - Refund Retur`.</li>
            </ul>
        </div>
        <div class="col-6 text-center">
            <p class="mb-5 small text-muted">Dicetak pada: <?= date('d F Y H:i') ?></p>
            <p class="fw-bold mb-0">__________________________</p>
            <p class="small text-muted">Management / Owner POS System</p>
        </div>
    </div>

</body>
</html>

