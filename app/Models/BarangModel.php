<?php

namespace App\Models;

use CodeIgniter\Model;

class BarangModel extends Model
{
    protected $table            = 'barang';
    protected $primaryKey       = 'id_barang';
    protected $useAutoIncrement = false;
    protected $returnType       = 'array';
    protected $protectFields    = true;
    protected $allowedFields    = [
        'id_barang',
        'barcode',
        'nama_barang',
        'id_kategori',
        'id_satuan',
        'harga_beli_terakhir',
        'harga_jual',
        'stok',
        'stok_minimum',
        'aktif',
        'created_by',
    ];

    // Dates
    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    /**
     * Generate sequential item ID: B0001, B0002, etc.
     */
    public function generateId(): string
    {
        $lastRow = $this->select('id_barang')
            ->orderBy('id_barang', 'DESC')
            ->first();

        if (! $lastRow) {
            return 'B0001';
        }

        $num = (int) substr($lastRow['id_barang'], 1);
        $next = $num + 1;

        return 'B' . str_pad((string) $next, 4, '0', STR_PAD_LEFT);
    }

    /**
     * Get items joined with category, unit, and creator details.
     */
    public function getBarangWithRelations(?string $id = null): array
    {
        $builder = $this->db->table('barang b')
            ->select('b.*, k.nama_kategori, s.nama_satuan, u.nama_user as pembuat')
            ->join('kategori k', 'k.id_kategori = b.id_kategori')
            ->join('satuan s', 's.id_satuan = b.id_satuan')
            ->join('users u', 'u.user_id = b.created_by', 'left');

        if ($id !== null) {
            return $builder->where('b.id_barang', $id)->get()->getRowArray() ?? [];
        }

        return $builder->orderBy('b.created_at', 'DESC')->get()->getResultArray();
    }
}
