<?php

namespace App\Models;

use CodeIgniter\Model;

class PenjualanModel extends Model
{
    protected $table            = 'penjualan';
    protected $primaryKey       = 'id_penjualan';
    protected $useAutoIncrement = false;
    protected $returnType       = 'array';
    protected $protectFields    = true;
    protected $allowedFields    = [
        'id_penjualan',
        'user_id',
        'id_customer',
        'tanggal_jual',
        'subtotal',
        'diskon',
        'total_belanja',
        'metode_bayar',
        'bayar',
        'kembali',
        'status',
    ];

    protected $useTimestamps = false;

    /**
     * Generate sequential transaction ID per day.
     * Format: PJ + YYYYMMDD + 4-digit urut harian
     * Contoh: PJ202609170001
     */
    public function generateId(): string
    {
        $today  = date('Ymd');
        $prefix = 'PJ' . $today;

        $lastRow = $this->select('id_penjualan')
            ->like('id_penjualan', $prefix, 'after')
            ->orderBy('id_penjualan', 'DESC')
            ->first();

        if (! $lastRow) {
            return $prefix . '0001';
        }

        $num  = (int) substr($lastRow['id_penjualan'], -4);
        $next = $num + 1;

        return $prefix . str_pad((string) $next, 4, '0', STR_PAD_LEFT);
    }

    /**
     * Ambil data penjualan lengkap dengan relasi user (kasir) dan customer.
     * Jika $id diberikan, kembalikan satu record; jika tidak, kembalikan semua.
     */
    public function getPenjualanWithRelations(?string $id = null): array
    {
        $builder = $this->db->table('penjualan p')
            ->select('p.*, u.nama_user as nama_kasir, c.nama_customer')
            ->join('users u', 'u.user_id = p.user_id')
            ->join('customer c', 'c.id_customer = p.id_customer', 'left');

        if ($id !== null) {
            return $builder->where('p.id_penjualan', $id)->get()->getRowArray() ?? [];
        }

        return $builder->orderBy('p.tanggal_jual', 'DESC')->get()->getResultArray();
    }

    /**
     * Statistik penjualan hari ini (untuk dashboard).
     * Mengembalikan total pendapatan dan jumlah transaksi selesai.
     */
    public function getTodayStats(): array
    {
        $today = date('Y-m-d');

        $result = $this->db->table('penjualan')
            ->selectSum('total_belanja', 'total_pendapatan')
            ->selectCount('id_penjualan', 'jumlah_transaksi')
            ->where('DATE(tanggal_jual)', $today)
            ->where('status', 'selesai')
            ->get()
            ->getRowArray();

        return [
            'total_pendapatan'  => (float) ($result['total_pendapatan'] ?? 0),
            'jumlah_transaksi'  => (int)   ($result['jumlah_transaksi'] ?? 0),
        ];
    }
}

