<?php

namespace App\Controllers;

use App\Models\BarangModel;
use App\Models\PembelianModel;
use App\Models\DetailPembelianModel;
use App\Models\SupplierModel;

class Pembelian extends BaseController
{
    protected PembelianModel       $pembelianModel;
    protected DetailPembelianModel $detailModel;
    protected BarangModel          $barangModel;
    protected SupplierModel        $supplierModel;
    protected                      $db;

    public function __construct()
    {
        $this->db             = \Config\Database::connect();
        $this->pembelianModel = new PembelianModel();
        $this->detailModel    = new DetailPembelianModel();
        $this->barangModel    = new BarangModel();
        $this->supplierModel  = new SupplierModel();
    }

    /**
     * Form transaksi pembelian baru.
     */
    public function index()
    {
        $role       = session()->get('role');
        $idSupplier = session()->get('id_supplier');

        // Dropdown supplier filter
        if ($role === 'inventory' && ! empty($idSupplier)) {
            $suppliers = $this->supplierModel->where('id_supplier', $idSupplier)->where('aktif', 1)->findAll();
        } else {
            $suppliers = $this->supplierModel->where('aktif', 1)->orderBy('nama_supplier', 'ASC')->findAll();
        }

        return view('pembelian/index', [
            'title'     => 'Input Pembelian / Stok - POS System',
            'suppliers' => $suppliers,
            'nextId'    => $this->pembelianModel->generateId(),
        ]);
    }

    /**
     * AJAX Endpoint: Get barang aktif untuk dropdown pilihan barang.
     */
    public function getBarang()
    {
        $keyword = $this->request->getGet('q');

        $builder = $this->db->table('barang b')
            ->select('b.id_barang, b.barcode, b.nama_barang, b.harga_beli_terakhir, b.harga_jual, b.stok, s.nama_satuan')
            ->join('satuan s', 's.id_satuan = b.id_satuan')
            ->where('b.aktif', 1);

        if ($keyword) {
            $builder->groupStart()
                ->like('b.nama_barang', $keyword)
                ->orLike('b.barcode', $keyword)
                ->groupEnd();
        }

        $barang = $builder->orderBy('b.nama_barang', 'ASC')->get()->getResultArray();

        return $this->response->setJSON(['status' => 'success', 'data' => $barang]);
    }

    /**
     * Store transaksi pembelian.
     */
    public function store()
    {
        $idSupplier  = $this->request->getPost('id_supplier');
        $noFaktur    = trim((string) $this->request->getPost('no_faktur'));
        $tanggalBeli = $this->request->getPost('tanggal_beli') ?: date('Y-m-d H:i:s');
        $keterangan  = trim((string) $this->request->getPost('keterangan'));
        $itemsJson   = $this->request->getPost('items_json');

        $items = json_decode($itemsJson, true);
        if (empty($items) || ! is_array($items)) {
            return redirect()->back()->withInput()->with('error', 'Daftar item pembelian masih kosong.');
        }

        if (empty($idSupplier)) {
            return redirect()->back()->withInput()->with('error', 'Supplier wajib dipilih.');
        }

        // Cek batasan role inventory
        $role           = session()->get('role');
        $userSupplierId = session()->get('id_supplier');
        if ($role === 'inventory' && ! empty($userSupplierId) && $userSupplierId !== $idSupplier) {
            return redirect()->back()->withInput()->with('error', 'Anda hanya berhak memilih supplier yang ditugaskan kepada Anda.');
        }

        // Hitung Total Beli
        $totalBeli = 0;
        foreach ($items as $item) {
            $totalBeli += (float) $item['harga_beli'] * (int) $item['jumlah'];
        }

        $db = \Config\Database::connect();
        $db->transStart();

        try {
            $idPembelian = $this->pembelianModel->generateId();

            // Insert Header
            $this->pembelianModel->insert([
                'id_pembelian' => $idPembelian,
                'id_supplier'  => $idSupplier,
                'user_id'      => session()->get('user_id'),
                'no_faktur'    => ! empty($noFaktur) ? $noFaktur : null,
                'tanggal_beli' => date('Y-m-d H:i:s', strtotime($tanggalBeli)),
                'total_beli'   => $totalBeli,
                'status'       => 'diterima',
                'keterangan'   => ! empty($keterangan) ? $keterangan : null,
            ]);

            // Insert Details (Trigger trg_beli_masuk akan otomatis update stok & harga_beli_terakhir)
            foreach ($items as $item) {
                $this->detailModel->insert([
                    'id_pembelian'   => $idPembelian,
                    'id_barang'      => $item['id_barang'],
                    'harga_beli'     => (float) $item['harga_beli'],
                    'jumlah'         => (int) $item['jumlah'],
                    'tgl_kadaluarsa' => ! empty($item['tgl_kadaluarsa']) ? $item['tgl_kadaluarsa'] : null,
                ]);
            }

            $db->transComplete();

            if ($db->transStatus() === false) {
                return redirect()->back()->withInput()->with('error', 'Gagal menyimpan transaksi pembelian.');
            }

            return redirect()->to(site_url('pembelian/faktur/' . $idPembelian))
                ->with('success', 'Transaksi pembelian berhasil disimpan. Stok barang telah diperbarui!');

        } catch (\Exception $e) {
            $db->transRollback();
            return redirect()->back()->withInput()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * Riwayat pembelian / faktur.
     */
    public function riwayat()
    {
        $role       = session()->get('role');
        $idSupplier = ($role === 'inventory') ? session()->get('id_supplier') : null;

        $pembelian = $this->pembelianModel->getPembelianWithRelations(null, $idSupplier);

        return view('pembelian/riwayat', [
            'title'     => 'Riwayat Pembelian / Stok - POS System',
            'pembelian' => $pembelian,
        ]);
    }

    /**
     * Detail faktur pembelian.
     */
    public function faktur(string $id = '')
    {
        $pembelian = $this->pembelianModel->getPembelianWithRelations($id);

        if (empty($pembelian)) {
            return redirect()->to(site_url('pembelian/riwayat'))->with('error', 'Faktur pembelian tidak ditemukan.');
        }

        // Cek hak akses role inventory
        $role       = session()->get('role');
        $idSupplier = session()->get('id_supplier');
        if ($role === 'inventory' && ! empty($idSupplier) && $pembelian['id_supplier'] !== $idSupplier) {
            return redirect()->to(site_url('pembelian/riwayat'))->with('error', 'Akses ditolak.');
        }

        $detail = $this->detailModel->getByPembelian($id);

        return view('pembelian/faktur', [
            'title'     => 'Detail Faktur Pembelian - POS System',
            'pembelian' => $pembelian,
            'detail'    => $detail,
        ]);
    }
}

