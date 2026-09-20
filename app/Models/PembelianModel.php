<?php

namespace App\Models;

use CodeIgniter\Model;

class PembelianModel extends Model
{
    protected $table            = 'pembelian';
    protected $primaryKey       = 'id_pembelian';
    protected $useAutoIncrement = false;
    protected $returnType       = 'array';
    protected $protectFields    = true;
    protected $allowedFields    = [
        'id_pembelian',
        'id_supplier',
        'user_id',
        'no_faktur',
        'tanggal_beli',
        'total_beli',
        'status',
        'keterangan',
    ];

    protected $useTimestamps = false;

    /**
     * Generate sequential purchase ID per day.
     * Format: PB + YYYYMMDD + 4-digit urut harian
     * Contoh: PB202609180001
     */
    public function generateId(): string
    {
        $today  = date('Ymd');
        $prefix = 'PB' . $today;

        $lastRow = $this->select('id_pembelian')
            ->like('id_pembelian', $prefix, 'after')
            ->orderBy('id_pembelian', 'DESC')
            ->first();

        if (! $lastRow) {
            return $prefix . '0001';
        }

        $num  = (int) substr($lastRow['id_pembelian'], -4);
        $next = $num + 1;

        return $prefix . str_pad((string) $next, 4, '0', STR_PAD_LEFT);
    }

    /**
     * Ambil data pembelian dengan relasi supplier dan user (pembeli).
     */
    public function getPembelianWithRelations(?string $id = null, ?string $idSupplier = null): array
    {
        $builder = $this->db->table('pembelian p')
            ->select('p.*, s.nama_supplier, u.nama_user as nama_pembeli')
            ->join('supplier s', 's.id_supplier = p.id_supplier')
            ->join('users u', 'u.user_id = p.user_id');

        if ($idSupplier !== null) {
            $builder->where('p.id_supplier', $idSupplier);
        }

        if ($id !== null) {
            return $builder->where('p.id_pembelian', $id)->get()->getRowArray() ?? [];
        }

        return $builder->orderBy('p.tanggal_beli', 'DESC')->get()->getResultArray();
    }
}

