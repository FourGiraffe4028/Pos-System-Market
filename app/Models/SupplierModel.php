<?php

namespace App\Models;

use CodeIgniter\Model;

class SupplierModel extends Model
{
    protected $table            = 'supplier';
    protected $primaryKey       = 'id_supplier';
    protected $useAutoIncrement = false;
    protected $returnType       = 'array';
    protected $protectFields    = true;
    protected $allowedFields    = [
        'id_supplier',
        'nama_supplier',
        'alamat',
        'kota',
        'no_telepon',
        'aktif',
    ];

    /**
     * Generate sequential supplier ID: S0001, S0002, etc.
     */
    public function generateId(): string
    {
        $lastRow = $this->select('id_supplier')
            ->orderBy('id_supplier', 'DESC')
            ->first();

        if (! $lastRow) {
            return 'S0001';
        }

        $num = (int) substr($lastRow['id_supplier'], 1);
        $next = $num + 1;

        return 'S' . str_pad((string) $next, 4, '0', STR_PAD_LEFT);
    }
}
