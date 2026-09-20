<?php

namespace App\Models;

use CodeIgniter\Model;

class DetailPenjualanModel extends Model
{
    protected $table            = 'detail_penjualan';
    protected $primaryKey       = 'id_detail';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $protectFields    = true;
    protected $allowedFields    = [
        'id_penjualan',
        'id_barang',
        'harga_satuan',
        'jumlah',
        'diskon_item',
    ];

    protected $useTimestamps = false;

    /**
     * Ambil semua item dari satu transaksi penjualan,
     * di-JOIN dengan nama barang dan satuan.
     */
    public function getByPenjualan(string $idPenjualan): array
    {
        return $this->db->table('detail_penjualan dp')
            ->select('dp.*, b.nama_barang, b.barcode, s.nama_satuan')
            ->join('barang b', 'b.id_barang = dp.id_barang')
            ->join('satuan s', 's.id_satuan = b.id_satuan')
            ->where('dp.id_penjualan', $idPenjualan)
            ->get()
            ->getResultArray();
    }
}

