<?php

namespace App\Controllers;

use CodeIgniter\Database\BaseConnection;

class Dashboard extends BaseController
{
    protected BaseConnection $db;

    public function __construct()
    {
        $this->db = \Config\Database::connect();
    }

    /**
     * Dashboard Main Home Page (Universal for all roles)
     */
    public function index()
    {
        $sessionData = [
            'user_id'     => session()->get('user_id'),
            'username'    => session()->get('username'),
            'nama_user'   => session()->get('nama_user'),
            'role'        => session()->get('role'),
            'id_supplier' => session()->get('id_supplier'),
        ];

        $supplierName = null;
        if (! empty($sessionData['id_supplier'])) {
            $sup = $this->db->table('supplier')
                ->where('id_supplier', $sessionData['id_supplier'])
                ->get()
                ->getRowArray();
            if ($sup) {
                $supplierName = $sup['nama_supplier'];
            }
        }

        // 1. Total Omzet Bersih (Penjualan Selesai dikurangi Retur Customer Approved)
        $omzetRow = $this->db->table('penjualan')
            ->selectSum('total_belanja', 'total')
            ->where('status', 'selesai')
            ->get()
            ->getRowArray();
        $grossOmzet = (float) ($omzetRow['total'] ?? 0);

        $returRow = $this->db->table('retur')
            ->selectSum('total_refund', 'total')
            ->where('status', 'approved')
            ->get()
            ->getRowArray();
        $totalRefundApproved = (float) ($returRow['total'] ?? 0);

        $totalOmzet = max(0, $grossOmzet - $totalRefundApproved);

        // 2. Total Barang (Jumlah Barang Aktif)
        $totalBarang = $this->db->table('barang')
            ->where('aktif', 1)
            ->countAllResults();

        // 3. Total Kategori
        $totalKategori = $this->db->table('kategori')->countAllResults();

        // 4. Total Customer
        $totalCustomer = $this->db->table('customer')->countAllResults();

        // 5. NOTIFIKASI BARANG HABIS / STOK MENIPIS (stok <= stok_minimum)
        $barangHabisCount = $this->db->table('barang')
            ->where('stok <= stok_minimum')
            ->where('aktif', 1)
            ->countAllResults();

        $barangHabisList = $this->db->table('barang')
            ->select('id_barang, nama_barang, stok, stok_minimum, harga_jual')
            ->where('stok <= stok_minimum')
            ->where('aktif', 1)
            ->orderBy('stok', 'ASC')
            ->get(5)
            ->getResultArray();

        // 6. Distribution of Barang by Kategori
        $catDist = $this->db->table('barang b')
            ->select('k.nama_kategori, COUNT(b.id_barang) as total_item')
            ->join('kategori k', 'k.id_kategori = b.id_kategori')
            ->groupBy('k.id_kategori, k.nama_kategori')
            ->get()
            ->getResultArray();

        // Prepare Category chart labels & data
        $catLabels = [];
        $catCounts = [];
        foreach ($catDist as $cd) {
            $catLabels[] = $cd['nama_kategori'];
            $catCounts[] = (int) $cd['total_item'];
        }

        $data = [
            'title'            => 'Dashboard - POS System',
            'user'             => $sessionData,
            'supplierName'     => $supplierName,
            'totalOmzet'       => $totalOmzet,
            'totalBarang'      => $totalBarang,
            'totalKategori'    => $totalKategori,
            'totalCustomer'    => $totalCustomer,
            'barangHabisCount' => $barangHabisCount,
            'barangHabisList'  => $barangHabisList,
            'catLabels'        => $catLabels,
            'catCounts'        => $catCounts,
        ];

        return view('dashboard/index', $data);
    }
}
