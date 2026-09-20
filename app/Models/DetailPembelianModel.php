<?php

namespace App\Models;

use CodeIgniter\Model;

class DetailPembelianModel extends Model
{
    protected $table            = 'detail_pembelian';
    protected $primaryKey       = 'id_detail_beli';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $protectFields    = true;
    protected $allowedFields    = [
        'id_pembelian',
        'id_barang',
        'harga_beli',
        'jumlah',
        'subtotal',
        'tgl_kadaluarsa',
    ];

    protected $useTimestamps = false;

    /**
     * Ambil item dari satu transaksi pembelian JOIN barang dan satuan.
     */
    public function getByPembelian(string $idPembelian): array
    {
        return $this->db->table('detail_pembelian dp')
            ->select('dp.*, b.nama_barang, b.barcode, s.nama_satuan')
            ->join('barang b', 'b.id_barang = dp.id_barang')
            ->join('satuan s', 's.id_satuan = b.id_satuan')
            ->where('dp.id_pembelian', $idPembelian)
            ->get()
            ->getResultArray();
    }
}

