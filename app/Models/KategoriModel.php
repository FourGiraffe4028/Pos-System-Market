<?php

namespace App\Models;

use CodeIgniter\Model;

class KategoriModel extends Model
{
    protected $table            = 'kategori';
    protected $primaryKey       = 'id_kategori';
    protected $useAutoIncrement = false;
    protected $returnType       = 'array';
    protected $protectFields    = true;
    protected $allowedFields    = [
        'id_kategori',
        'nama_kategori',
    ];

    /**
     * Generate sequential category ID: K0001, K0002, etc.
     */
    public function generateId(): string
    {
        $lastRow = $this->select('id_kategori')
            ->orderBy('id_kategori', 'DESC')
            ->first();

        if (! $lastRow) {
            return 'K0001';
        }

        $num = (int) substr($lastRow['id_kategori'], 1);
        $next = $num + 1;

        return 'K' . str_pad((string) $next, 4, '0', STR_PAD_LEFT);
    }
}
