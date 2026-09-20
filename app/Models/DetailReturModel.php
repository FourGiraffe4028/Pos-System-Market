<?php

namespace App\Models;

use CodeIgniter\Model;

class DetailReturModel extends Model
{
    protected $table            = 'detail_retur';
    protected $primaryKey       = 'id_detail_retur';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $protectFields    = true;
    protected $allowedFields    = [
        'id_retur',
        'id_barang',
        'harga_satuan',
        'jumlah_retur',
        'subtotal_refund',
        'kondisi_barang',
    ];

    protected $useTimestamps = false;

    /**
     * Ambil item detail retur JOIN tabel barang dan satuan.
     */
    public function getByRetur(string $idRetur): array
    {
        return $this->db->table('detail_retur dr')
            ->select('dr.*, b.nama_barang, b.barcode, s.nama_satuan')
            ->join('barang b', 'b.id_barang = dr.id_barang')
            ->join('satuan s', 's.id_satuan = b.id_satuan')
            ->where('dr.id_retur', $idRetur)
            ->get()
            ->getResultArray();
    }
}

