<?php

namespace App\Models;

use CodeIgniter\Model;

class ReturModel extends Model
{
    protected $table            = 'retur';
    protected $primaryKey       = 'id_retur';
    protected $useAutoIncrement = false;
    protected $returnType       = 'array';
    protected $protectFields    = true;
    protected $allowedFields    = [
        'id_retur',
        'id_penjualan',
        'user_id_spv',
        'user_id_kasir',
        'tanggal_retur',
        'total_refund',
        'alasan',
        'status',
    ];

    protected $useTimestamps = false;

    /**
     * Generate ID Retur: RT + YYYYMMDD + 4-digit urut harian.
     */
    public function generateId(): string
    {
        $today  = date('Ymd');
        $prefix = 'RT' . $today;

        $lastRow = $this->select('id_retur')
            ->like('id_retur', $prefix, 'after')
            ->orderBy('id_retur', 'DESC')
            ->first();

        if (! $lastRow) {
            return $prefix . '0001';
        }

        $num  = (int) substr($lastRow['id_retur'], -4);
        $next = $num + 1;

        return $prefix . str_pad((string) $next, 4, '0', STR_PAD_LEFT);
    }

    /**
     * Ambil data retur lengkap dengan relasi ke penjualan, kasir pengaju, dan supervisor approval.
     */
    public function getReturWithRelations(?string $id = null, ?string $statusFilter = null): array
    {
        $builder = $this->db->table('retur r')
            ->select('r.*, p.tanggal_jual, u_kasir.nama_user as nama_kasir, u_spv.nama_user as nama_spv')
            ->join('penjualan p', 'p.id_penjualan = r.id_penjualan')
            ->join('users u_kasir', 'u_kasir.user_id = r.user_id_kasir', 'left')
            ->join('users u_spv', 'u_spv.user_id = r.user_id_spv', 'left');

        if ($statusFilter !== null) {
            $builder->where('r.status', $statusFilter);
        }

        if ($id !== null) {
            return $builder->where('r.id_retur', $id)->get()->getRowArray() ?? [];
        }

        return $builder->orderBy('r.tanggal_retur', 'DESC')->get()->getResultArray();
    }
}
