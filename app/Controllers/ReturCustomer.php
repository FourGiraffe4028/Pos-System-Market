<?php

namespace App\Controllers;

use App\Models\ReturModel;
use App\Models\DetailReturModel;
use App\Models\PenjualanModel;
use App\Models\DetailPenjualanModel;
use App\Models\UserModel;

class ReturCustomer extends BaseController
{
    protected ReturModel           $returModel;
    protected DetailReturModel     $detailReturModel;
    protected PenjualanModel       $penjualanModel;
    protected DetailPenjualanModel $detailPenjualanModel;
    protected UserModel            $userModel;

    public function __construct()
    {
        $this->returModel           = new ReturModel();
        $this->detailReturModel     = new DetailReturModel();
        $this->penjualanModel       = new PenjualanModel();
        $this->detailPenjualanModel = new DetailPenjualanModel();
        $this->userModel            = new UserModel();
    }

    /**
     * Halaman Form Pengajuan Retur Customer (Kasir & Admin).
     */
    public function index()
    {
        $role = session()->get('role');

        if ($role === 'supervisor') {
            return redirect()->to(site_url('retur-customer/approval'));
        }

        // Untuk Kasir atau Admin (Form Pengajuan)
        $pendingList = $role === 'admin' ? $this->returModel->getReturWithRelations(null, 'pending') : [];

        return view('retur_customer/index', [
            'title'       => 'Pengajuan Retur Customer - POS System',
            'nextId'      => $this->returModel->generateId(),
            'pendingList' => $pendingList,
        ]);
    }

    /**
     * Halaman Antrean Approval Retur (Supervisor & Admin).
     */
    public function approvalQueue()
    {
        $role = session()->get('role');
        if (! in_array($role, ['supervisor', 'admin'], true)) {
            return redirect()->to(site_url('retur-customer'))->with('error', 'Akses ditolak. Antrean approval hanya untuk Supervisor & Admin.');
        }

        $pendingList = $this->returModel->getReturWithRelations(null, 'pending');

        return view('retur_customer/spv_queue', [
            'title'       => 'Antrean Approval Retur - POS System',
            'pendingList' => $pendingList,
        ]);
    }

    /**
     * AJAX Endpoint: Cari Nota Penjualan & ketersediaan item untuk retur.
     */
    public function getPenjualan()
    {
        $idPenjualan = trim((string) $this->request->getGet('id_penjualan'));

        if (empty($idPenjualan)) {
            return $this->response->setStatusCode(400)->setJSON([
                'status'  => 'error',
                'message' => 'Masukkan No. Transaksi Penjualan.',
            ]);
        }

        $penjualan = $this->penjualanModel->getPenjualanWithRelations($idPenjualan);
        if (empty($penjualan)) {
            return $this->response->setStatusCode(404)->setJSON([
                'status'  => 'error',
                'message' => "Transaksi penjualan {$idPenjualan} tidak ditemukan.",
            ]);
        }

        if ($penjualan['status'] === 'void') {
            return $this->response->setStatusCode(400)->setJSON([
                'status'  => 'error',
                'message' => "Transaksi {$idPenjualan} berstatus VOID dan tidak dapat diretur.",
            ]);
        }

        $items = $this->detailPenjualanModel->getByPenjualan($idPenjualan);

        // Cari item yang sudah pernah diretur sebelumnya
        $db = \Config\Database::connect();
        $returSebelumnya = $db->table('retur r')
            ->select('dr.id_barang, SUM(dr.jumlah_retur) as total_diretur')
            ->join('detail_retur dr', 'dr.id_retur = r.id_retur')
            ->where('r.id_penjualan', $idPenjualan)
            ->whereIn('r.status', ['pending', 'approved'])
            ->groupBy('dr.id_barang')
            ->get()
            ->getResultArray();

        $sudahDireturMap = [];
        foreach ($returSebelumnya as $r) {
            $sudahDireturMap[$r['id_barang']] = (int) $r['total_diretur'];
        }

        foreach ($items as &$item) {
            $already = $sudahDireturMap[$item['id_barang']] ?? 0;
            $item['sisa_dapat_diretur'] = max(0, (int)$item['jumlah'] - $already);
        }

        return $this->response->setJSON([
            'status'    => 'success',
            'penjualan' => $penjualan,
            'items'     => $items,
        ]);
    }

    /**
     * Submit Pengajuan Retur (Status default: PENDING).
     */
    public function store()
    {
        $idPenjualan = trim((string) $this->request->getPost('id_penjualan'));
        $alasan      = trim((string) $this->request->getPost('alasan'));
        $itemsJson   = $this->request->getPost('items_json');

        $items = json_decode($itemsJson, true);
        if (empty($items) || ! is_array($items)) {
            return redirect()->back()->withInput()->with('error', 'Tidak ada barang yang dipilih untuk diretur.');
        }

        if (empty($idPenjualan)) {
            return redirect()->back()->withInput()->with('error', 'Nomor Transaksi Penjualan wajib diisi.');
        }

        if (empty($alasan)) {
            return redirect()->back()->withInput()->with('error', 'Alasan pengembalian barang wajib diisi.');
        }

        // Calculate Total Refund
        $totalRefund = 0;
        foreach ($items as $item) {
            $totalRefund += (float) $item['harga_satuan'] * (int) $item['jumlah_retur'];
        }

        $db = \Config\Database::connect();
        $db->transStart();

        try {
            $idRetur = $this->returModel->generateId();

            // Insert Retur Header dengan status PENDING
            $this->returModel->insert([
                'id_retur'      => $idRetur,
                'id_penjualan'  => $idPenjualan,
                'user_id_spv'   => null,
                'user_id_kasir' => session()->get('user_id'),
                'tanggal_retur' => date('Y-m-d H:i:s'),
                'total_refund'  => $totalRefund,
                'alasan'        => $alasan,
                'status'        => 'pending',
            ]);

            // Insert Detail Retur
            foreach ($items as $item) {
                $this->detailReturModel->insert([
                    'id_retur'       => $idRetur,
                    'id_barang'      => $item['id_barang'],
                    'harga_satuan'   => (float) $item['harga_satuan'],
                    'jumlah_retur'   => (int) $item['jumlah_retur'],
                    'kondisi_barang' => $item['kondisi_barang'],
                ]);
            }

            $db->transComplete();

            if ($db->transStatus() === false) {
                return redirect()->back()->withInput()->with('error', 'Gagal mengajukan retur customer.');
            }

            return redirect()->to(site_url('retur-customer/riwayat'))
                ->with('success', "Pengajuan retur {$idRetur} berhasil dikirim dan menunggu persetujuan Supervisor.");

        } catch (\Exception $e) {
            $db->transRollback();
            return redirect()->back()->withInput()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * Supervisor APPROVE Retur: Ubah status ke 'approved' & update stok barang jika kembali_stok.
     */
    public function approve(string $id = '')
    {
        $role = session()->get('role');
        if (! in_array($role, ['supervisor', 'admin'], true)) {
            return redirect()->to(site_url('retur-customer/riwayat'))->with('error', 'Akses ditolak. Persetujuan retur hanya wewenang Supervisor & Admin.');
        }

        $retur = $this->returModel->find($id);
        if (! $retur) {
            return redirect()->to(site_url('retur-customer/riwayat'))->with('error', 'Data pengajuan retur tidak ditemukan.');
        }

        if ($retur['status'] !== 'pending') {
            return redirect()->to(site_url('retur-customer/riwayat'))->with('error', "Pengajuan retur {$id} sudah diproses.");
        }

        $details = $this->detailReturModel->getByRetur($id);

        $db = \Config\Database::connect();
        $db->transStart();

        try {
            // Update Header Retur -> Status Approved & simpan SPV ID
            $this->returModel->update($id, [
                'status'      => 'approved',
                'user_id_spv' => session()->get('user_id'),
            ]);

            // Kembalikan Stok Barang jika kondisi_barang = 'kembali_stok'
            foreach ($details as $d) {
                if ($d['kondisi_barang'] === 'kembali_stok') {
                    $db->table('barang')
                        ->where('id_barang', $d['id_barang'])
                        ->set('stok', 'stok + ' . (int) $d['jumlah_retur'], false)
                        ->update();
                }
            }

            $db->transComplete();

            if ($db->transStatus() === false) {
                return redirect()->to(site_url('retur-customer/riwayat'))->with('error', 'Gagal menyetujui pengajuan retur.');
            }

            return redirect()->to(site_url('retur-customer/detail/' . $id))
                ->with('success', "Pengajuan retur {$id} BERHASIL DI-APPROVE. Stok barang telah disesuaikan.");

        } catch (\Exception $e) {
            $db->transRollback();
            return redirect()->to(site_url('retur-customer/riwayat'))->with('error', 'Terjadi kesalahan saat menyetujui retur: ' . $e->getMessage());
        }
    }

    /**
     * Supervisor / Admin REJECT Retur: Ubah status ke 'rejected'.
     */
    public function reject(string $id = '')
    {
        $role = session()->get('role');
        if (! in_array($role, ['supervisor', 'admin'], true)) {
            return redirect()->to(site_url('retur-customer/riwayat'))->with('error', 'Akses ditolak. Penolakan retur hanya wewenang Supervisor & Admin.');
        }

        $retur = $this->returModel->find($id);
        if (! $retur || $retur['status'] !== 'pending') {
            return redirect()->to(site_url('retur-customer'))->with('error', 'Pengajuan retur tidak valid atau sudah diproses.');
        }

        $this->returModel->update($id, [
            'status'      => 'rejected',
            'user_id_spv' => session()->get('user_id'),
        ]);

        return redirect()->to(site_url('retur-customer'))
            ->with('success', "Pengajuan retur {$id} telah DITOLAK oleh Supervisor.");
    }

    /**
     * Riwayat Seluruh Transaksi Retur.
     */
    public function riwayat()
    {
        $returList = $this->returModel->getReturWithRelations();

        return view('retur_customer/riwayat', [
            'title'     => 'Riwayat Retur Customer - POS System',
            'returList' => $returList,
        ]);
    }

    /**
     * Detail Nota Retur & Refund Customer.
     */
    public function detail(string $id = '')
    {
        $retur = $this->returModel->getReturWithRelations($id);

        if (empty($retur)) {
            return redirect()->to(site_url('retur-customer/riwayat'))->with('error', 'Data retur tidak ditemukan.');
        }

        $detail = $this->detailReturModel->getByRetur($id);

        return view('retur_customer/detail', [
            'title'  => 'Bukti Retur Customer - POS System',
            'retur'  => $retur,
            'detail' => $detail,
        ]);
    }
}
