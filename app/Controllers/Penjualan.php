<?php

namespace App\Controllers;

use App\Models\BarangModel;
use App\Models\PenjualanModel;
use App\Models\DetailPenjualanModel;

class Penjualan extends BaseController
{
    protected PenjualanModel       $penjualanModel;
    protected DetailPenjualanModel $detailModel;
    protected BarangModel          $barangModel;
    protected                      $db;

    public function __construct()
    {
        $this->db             = \Config\Database::connect();
        $this->penjualanModel = new PenjualanModel();
        $this->detailModel    = new DetailPenjualanModel();
        $this->barangModel    = new BarangModel();
    }

    // -------------------------------------------------------------------------
    // SHARED: Admin & Kasir
    // -------------------------------------------------------------------------

    /**
     * Halaman utama POS — antarmuka kasir untuk input transaksi.
     */
    public function index()
    {
        return view('penjualan/index', [
            'title' => 'Kasir Penjualan - POS System',
        ]);
    }

    /**
     * Layar Tampilan Customer (Customer Display / Dual Monitor Display).
     */
    public function customerDisplay()
    {
        return view('penjualan/customer_display', [
            'title' => 'Customer Display - POS System',
        ]);
    }

    /**
     * AJAX endpoint: Simpan survei kepuasan pelanggan (Puas / Tidak Puas).
     */
    public function saveSurvey()
    {
        $idPenjualan = trim((string) $this->request->getPost('id_penjualan'));
        $kepuasan    = trim((string) $this->request->getPost('kepuasan'));

        if (empty($idPenjualan) || ! in_array($kepuasan, ['puas', 'tidak_puas'], true)) {
            return $this->response->setStatusCode(400)->setJSON([
                'status'  => 'error',
                'message' => 'Parameter survei tidak valid.',
            ]);
        }

        $this->penjualanModel->update($idPenjualan, [
            'kepuasan' => $kepuasan,
        ]);

        return $this->response->setJSON([
            'status'  => 'success',
            'message' => 'Terima kasih atas penilaian Anda!',
        ]);
    }

    /**
     * AJAX endpoint: kembalikan daftar barang aktif dengan stok > 0.
     * Mendukung pencarian by nama_barang atau barcode via query param ?q=
     */
    public function getBarang()
    {
        $keyword = $this->request->getGet('q');

        $builder = $this->db->table('barang b')
            ->select('b.id_barang, b.barcode, b.nama_barang, b.harga_jual, b.stok, s.nama_satuan')
            ->join('satuan s', 's.id_satuan = b.id_satuan')
            ->where('b.aktif', 1)
            ->where('b.stok >', 0);

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
     * Proses transaksi penjualan.
     *
     * Payload POST (JSON dari JS):
     *   items       : array of { id_barang, harga_satuan, jumlah }
     *   metode_bayar: string (tunai|debit|kredit|qris|ewallet)
     *   bayar       : float
     *
     * Alur:
     *   1. Decode JSON payload dari hidden field
     *   2. Validasi items tidak kosong
     *   3. Hitung subtotal & total_belanja
     *   4. Buka DB transaction
     *   5. Insert header penjualan
     *   6. Insert tiap detail (trigger DB otomatis kurangi stok)
     *   7. Commit — jika gagal (stok kurang), rollback & tampilkan error
     */
    public function store()
    {
        $itemsJson   = $this->request->getPost('items_json');
        $metodeBayar = $this->request->getPost('metode_bayar');
        $bayar       = (float) $this->request->getPost('bayar');

        // Decode items
        $items = json_decode($itemsJson, true);
        if (empty($items) || ! is_array($items)) {
            return redirect()->back()->with('error', 'Keranjang belanja kosong. Tambahkan barang terlebih dahulu.');
        }

        // Validasi metode bayar
        $allowedMetode = ['tunai', 'debit', 'kredit', 'qris', 'ewallet'];
        if (! in_array($metodeBayar, $allowedMetode, true)) {
            return redirect()->back()->with('error', 'Metode pembayaran tidak valid.');
        }

        // Hitung total
        $subtotal = 0;
        foreach ($items as $item) {
            $subtotal += (float) $item['harga_satuan'] * (int) $item['jumlah'];
        }
        $totalBelanja = $subtotal; // diskon = 0 untuk saat ini
        $kembali      = $bayar - $totalBelanja;

        // Validasi uang bayar (hanya untuk tunai)
        if ($metodeBayar === 'tunai' && $bayar < $totalBelanja) {
            return redirect()->back()->with('error', 'Nominal pembayaran kurang dari total belanja.');
        }

        $db = \Config\Database::connect();
        $db->transStart();

        try {
            $idPenjualan = $this->penjualanModel->generateId();

            // Insert header
            $this->penjualanModel->insert([
                'id_penjualan'  => $idPenjualan,
                'user_id'       => session()->get('user_id'),
                'id_customer'   => null,
                'tanggal_jual'  => date('Y-m-d H:i:s'),
                'subtotal'      => $subtotal,
                'diskon'        => 0,
                'total_belanja' => $totalBelanja,
                'metode_bayar'  => $metodeBayar,
                'bayar'         => $bayar,
                'kembali'       => max(0, $kembali),
                'status'        => 'selesai',
            ]);

            // Insert detail items
            foreach ($items as $item) {
                $this->detailModel->insert([
                    'id_penjualan' => $idPenjualan,
                    'id_barang'    => $item['id_barang'],
                    'harga_satuan' => (float) $item['harga_satuan'],
                    'jumlah'       => (int) $item['jumlah'],
                    'diskon_item'  => 0,
                ]);
            }

            $db->transComplete();

            if ($db->transStatus() === false) {
                return redirect()->back()->with('error', 'Transaksi gagal. Kemungkinan stok salah satu barang tidak mencukupi.');
            }

            return redirect()->to(site_url('penjualan/struk/' . $idPenjualan))
                ->with('success', 'Transaksi berhasil diproses!');

        } catch (\Exception $e) {
            $db->transRollback();

            // Tangkap error dari trigger DB (stok tidak mencukupi)
            $msg = str_contains($e->getMessage(), 'Stok barang tidak mencukupi')
                ? 'Transaksi dibatalkan: stok salah satu barang tidak mencukupi.'
                : 'Terjadi kesalahan saat memproses transaksi. Silakan coba lagi.';

            return redirect()->back()->with('error', $msg);
        }
    }

    /**
     * Tampilkan struk transaksi setelah berhasil.
     */
    public function struk(string $id = '')
    {
        $penjualan = $this->penjualanModel->getPenjualanWithRelations($id);

        if (empty($penjualan)) {
            return redirect()->to(site_url('penjualan'))->with('error', 'Data transaksi tidak ditemukan.');
        }

        $detail = $this->detailModel->getByPenjualan($id);

        return view('penjualan/struk', [
            'title'     => 'Struk Transaksi - POS System',
            'penjualan' => $penjualan,
            'detail'    => $detail,
        ]);
    }

    // -------------------------------------------------------------------------
    // ADMIN ONLY
    // -------------------------------------------------------------------------

    /**
     * Daftar semua transaksi penjualan (monitoring) — hanya admin.
     */
    public function riwayat()
    {
        $role = session()->get('role');
        if ($role !== 'admin' && $role !== 'kasir') {
            return redirect()->to(site_url('penjualan'))->with('error', 'Akses ditolak.');
        }

        $transaksi = $this->penjualanModel->getPenjualanWithRelations();

        return view('penjualan/riwayat', [
            'title'     => 'Riwayat Transaksi - POS System',
            'transaksi' => $transaksi,
        ]);
    }

    /**
     * Void (batalkan) transaksi — hanya admin.
     * Ubah status jadi 'void' dan kembalikan stok semua item secara manual.
     */
    public function void(string $id = '')
    {
        if (session()->get('role') !== 'admin') {
            return redirect()->to(site_url('penjualan'))->with('error', 'Akses ditolak.');
        }

        $penjualan = $this->penjualanModel->find($id);

        if (! $penjualan) {
            return redirect()->to(site_url('penjualan/riwayat'))->with('error', 'Data transaksi tidak ditemukan.');
        }

        if ($penjualan['status'] === 'void') {
            return redirect()->to(site_url('penjualan/riwayat'))->with('error', 'Transaksi ini sudah berstatus void.');
        }

        $detail = $this->detailModel->getByPenjualan($id);

        $db = \Config\Database::connect();
        $db->transStart();

        try {
            // Ubah status penjualan menjadi void
            $this->penjualanModel->update($id, ['status' => 'void']);

            // Kembalikan stok tiap item secara manual
            // (trigger DB hanya aktif saat INSERT, tidak pada UPDATE)
            foreach ($detail as $item) {
                $db->table('barang')
                    ->where('id_barang', $item['id_barang'])
                    ->set('stok', 'stok + ' . (int) $item['jumlah'], false)
                    ->update();
            }

            $db->transComplete();

            if ($db->transStatus() === false) {
                return redirect()->to(site_url('penjualan/riwayat'))->with('error', 'Gagal melakukan void transaksi.');
            }

            return redirect()->to(site_url('penjualan/riwayat'))
                ->with('success', "Transaksi {$id} berhasil di-void. Stok barang telah dikembalikan.");

        } catch (\Exception $e) {
            $db->transRollback();
            return redirect()->to(site_url('penjualan/riwayat'))->with('error', 'Terjadi kesalahan saat melakukan void.');
        }
    }
}

