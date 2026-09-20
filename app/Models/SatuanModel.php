<?php

namespace App\Models;

use CodeIgniter\Model;

class SatuanModel extends Model
{
    protected $table            = 'satuan';
    protected $primaryKey       = 'id_satuan';
    protected $useAutoIncrement = false;
    protected $returnType       = 'array';
    protected $protectFields    = true;
    protected $allowedFields    = [
        'id_satuan',
        'nama_satuan',
    ];

    /**
     * Generate sequential unit ID: ST001, ST002, etc.
     */
    public function generateId(): string
    {
        $lastRow = $this->select('id_satuan')
            ->orderBy('id_satuan', 'DESC')
            ->first();

        if (! $lastRow) {
            return 'ST001';
        }

        $num = (int) substr($lastRow['id_satuan'], 2);
        $next = $num + 1;

        return 'ST' . str_pad((string) $next, 3, '0', STR_PAD_LEFT);
    }
}
