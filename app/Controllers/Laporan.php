<?php

namespace App\Controllers;

class Laporan extends BaseController
{
    protected $db;

    public function __construct()
    {
        $this->db = \Config\Database::connect();
    }

    /**
     * Dashboard Laporan Keuangan (Owner & Admin).
     */
    public function index()
    {
        $startDate = $this->request->getGet('tgl_awal') ?: date('Y-m-01');
        $endDate   = $this->request->getGet('tgl_akhir') ?: date('Y-m-d');

        $tglAwalFull  = $startDate . ' 00:00:00';
        $tglAkhirFull = $endDate . ' 23:59:59';

        // 1. Total Penjualan (Selesai)
        $penjualanSummary = $this->db->table('penjualan')
            ->selectSum('total_belanja', 'total_omzet')
            ->selectCount('id_penjualan', 'total_transaksi')
            ->where('status', 'selesai')
            ->where('tanggal_jual >=', $tglAwalFull)
            ->where('tanggal_jual <=', $tglAkhirFull)
            ->get()->getRowArray();

        $totalOmzet     = (float) ($penjualanSummary['total_omzet'] ?? 0);
        $totalTransaksi = (int)   ($penjualanSummary['total_transaksi'] ?? 0);

        // Hitung HPP (Modal Barang Terjual dari detail_penjualan & harga_beli_terakhir)
        $hppRow = $this->db->table('detail_penjualan dp')
            ->select('SUM(dp.jumlah * b.harga_beli_terakhir) as total_hpp')
            ->join('penjualan p', 'p.id_penjualan = dp.id_penjualan')
            ->join('barang b', 'b.id_barang = dp.id_barang')
            ->where('p.status', 'selesai')
            ->where('p.tanggal_jual >=', $tglAwalFull)
            ->where('p.tanggal_jual <=', $tglAkhirFull)
            ->get()->getRowArray();

        $totalHpp = (float) ($hppRow['total_hpp'] ?? 0);

        // 2. Total Pembelian Stok (Diterima)
        $pembelianSummary = $this->db->table('pembelian')
            ->selectSum('total_beli', 'total_pengeluaran')
            ->selectCount('id_pembelian', 'total_faktur')
            ->where('status', 'diterima')
            ->where('tanggal_beli >=', $tglAwalFull)
            ->where('tanggal_beli <=', $tglAkhirFull)
            ->get()->getRowArray();

        $totalPembelian = (float) ($pembelianSummary['total_pengeluaran'] ?? 0);
        $totalFaktur    = (int)   ($pembelianSummary['total_faktur'] ?? 0);

        // 3. Total Retur & Refund Customer (Hanya retur disetujui / APPROVED)
        $returSummary = $this->db->table('retur')
            ->selectSum('total_refund', 'total_refund')
            ->selectCount('id_retur', 'total_retur')
            ->where('status', 'approved')
            ->where('tanggal_retur >=', $tglAwalFull)
            ->where('tanggal_retur <=', $tglAkhirFull)
            ->get()->getRowArray();

        $totalRefund = (float) ($returSummary['total_refund'] ?? 0);
        $totalRetur  = (int)   ($returSummary['total_retur'] ?? 0);

        // Omzet Bersih = Omzet Kotor Penjualan dikurangi Total Refund Retur
        $omzetBersih = max(0, $totalOmzet - $totalRefund);

        // 4. Estimasi Laba Kotor (Gross Profit)
        $labaKotor = $omzetBersih - $totalHpp;

        // 5. Daftar Transaksi Penjualan
        $listPenjualan = $this->db->table('penjualan p')
            ->select('p.*, u.nama_user as nama_kasir')
            ->join('users u', 'u.user_id = p.user_id')
            ->where('p.tanggal_jual >=', $tglAwalFull)
            ->where('p.tanggal_jual <=', $tglAkhirFull)
            ->orderBy('p.tanggal_jual', 'DESC')
            ->get()->getResultArray();

        // 6. Daftar Transaksi Pembelian
        $listPembelian = $this->db->table('pembelian pb')
            ->select('pb.*, s.nama_supplier, u.nama_user as nama_pembeli')
            ->join('supplier s', 's.id_supplier = pb.id_supplier')
            ->join('users u', 'u.user_id = pb.user_id')
            ->where('pb.tanggal_beli >=', $tglAwalFull)
            ->where('pb.tanggal_beli <=', $tglAkhirFull)
            ->orderBy('pb.tanggal_beli', 'DESC')
            ->get()->getResultArray();

        // 7. Daftar Retur Customer
        $listRetur = $this->db->table('retur r')
            ->select('r.*, u_spv.nama_user as nama_spv, u_kasir.nama_user as nama_kasir')
            ->join('users u_spv', 'u_spv.user_id = r.user_id_spv', 'left')
            ->join('users u_kasir', 'u_kasir.user_id = r.user_id_kasir', 'left')
            ->where('r.tanggal_retur >=', $tglAwalFull)
            ->where('r.tanggal_retur <=', $tglAkhirFull)
            ->orderBy('r.tanggal_retur', 'DESC')
            ->get()->getResultArray();

        // 8. Produk Terlaris (Top 5 Best Selling Items)
        $topProducts = $this->db->table('detail_penjualan dp')
            ->select('b.nama_barang, b.id_barang, s.nama_satuan, SUM(dp.jumlah) as total_terjual, SUM(dp.subtotal) as total_omzet_barang')
            ->join('penjualan p', 'p.id_penjualan = dp.id_penjualan')
            ->join('barang b', 'b.id_barang = dp.id_barang')
            ->join('satuan s', 's.id_satuan = b.id_satuan')
            ->where('p.status', 'selesai')
            ->where('p.tanggal_jual >=', $tglAwalFull)
            ->where('p.tanggal_jual <=', $tglAkhirFull)
            ->groupBy('dp.id_barang, b.nama_barang, b.id_barang, s.nama_satuan')
            ->orderBy('total_terjual', 'DESC')
            ->get(5)->getResultArray();

        return view('laporan/index', [
            'title'          => 'Laporan Keuangan - POS System',
            'startDate'      => $startDate,
            'endDate'        => $endDate,
            'totalOmzet'     => $totalOmzet,
            'omzetBersih'    => $omzetBersih,
            'totalTransaksi' => $totalTransaksi,
            'totalHpp'       => $totalHpp,
            'totalPembelian' => $totalPembelian,
            'totalFaktur'    => $totalFaktur,
            'totalRefund'    => $totalRefund,
            'totalRetur'     => $totalRetur,
            'labaKotor'      => $labaKotor,
            'listPenjualan'  => $listPenjualan,
            'listPembelian'  => $listPembelian,
            'listRetur'      => $listRetur,
            'topProducts'    => $topProducts,
        ]);
    }

    /**
     * Halaman Printable Cetak Laporan Keuangan.
     */
    public function cetak()
    {
        $startDate = $this->request->getGet('tgl_awal') ?: date('Y-m-01');
        $endDate   = $this->request->getGet('tgl_akhir') ?: date('Y-m-d');

        $tglAwalFull  = $startDate . ' 00:00:00';
        $tglAkhirFull = $endDate . ' 23:59:59';

        $penjualanSummary = $this->db->table('penjualan')
            ->selectSum('total_belanja', 'total_omzet')
            ->selectCount('id_penjualan', 'total_transaksi')
            ->where('status', 'selesai')
            ->where('tanggal_jual >=', $tglAwalFull)
            ->where('tanggal_jual <=', $tglAkhirFull)
            ->get()->getRowArray();

        $totalOmzet = (float) ($penjualanSummary['total_omzet'] ?? 0);

        $hppRow = $this->db->table('detail_penjualan dp')
            ->select('SUM(dp.jumlah * b.harga_beli_terakhir) as total_hpp')
            ->join('penjualan p', 'p.id_penjualan = dp.id_penjualan')
            ->join('barang b', 'b.id_barang = dp.id_barang')
            ->where('p.status', 'selesai')
            ->where('p.tanggal_jual >=', $tglAwalFull)
            ->where('p.tanggal_jual <=', $tglAkhirFull)
            ->get()->getRowArray();

        $totalHpp = (float) ($hppRow['total_hpp'] ?? 0);

        $pembelianSummary = $this->db->table('pembelian')
            ->selectSum('total_beli', 'total_pengeluaran')
            ->where('status', 'diterima')
            ->where('tanggal_beli >=', $tglAwalFull)
            ->where('tanggal_beli <=', $tglAkhirFull)
            ->get()->getRowArray();

        $totalPembelian = (float) ($pembelianSummary['total_pengeluaran'] ?? 0);

        $returSummary = $this->db->table('retur')
            ->selectSum('total_refund', 'total_refund')
            ->where('status', 'approved')
            ->where('tanggal_retur >=', $tglAwalFull)
            ->where('tanggal_retur <=', $tglAkhirFull)
            ->get()->getRowArray();

        $totalRefund = (float) ($returSummary['total_refund'] ?? 0);
        $omzetBersih = max(0, $totalOmzet - $totalRefund);
        $labaKotor   = $omzetBersih - $totalHpp;

        return view('laporan/cetak', [
            'title'          => 'Cetak Laporan Keuangan - POS System',
            'startDate'      => $startDate,
            'endDate'        => $endDate,
            'totalOmzet'     => $totalOmzet,
            'omzetBersih'    => $omzetBersih,
            'totalHpp'       => $totalHpp,
            'totalPembelian' => $totalPembelian,
            'totalRefund'    => $totalRefund,
            'labaKotor'      => $labaKotor,
        ]);
    }
}

