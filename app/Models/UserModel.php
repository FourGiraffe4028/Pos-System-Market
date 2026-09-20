<?php

namespace App\Models;

use CodeIgniter\Model;

class UserModel extends Model
{
    protected $table            = 'users';
    protected $primaryKey       = 'user_id';
    protected $useAutoIncrement = false;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'user_id',
        'username',
        'nama_user',
        'password',
        'role',
        'id_supplier',
        'aktif',
    ];

    // Dates
    protected $useTimestamps = false;
    protected $createdField  = 'created_at';

    /**
     * Get active user by username (used for auth login).
     */
    public function getActiveUserByUsername(string $username): ?array
    {
        return $this->where('username', $username)
                    ->where('aktif', 1)
                    ->first();
    }

    /**
     * Generate sequential user ID.
     * Format: usr01, usr02, dst.
     */
    public function generateId(): string
    {
        $lastRow = $this->select('user_id')
            ->like('user_id', 'usr', 'after')
            ->orderBy('user_id', 'DESC')
            ->first();

        if (! $lastRow) {
            return 'usr01';
        }

        $num  = (int) substr($lastRow['user_id'], 3);
        $next = $num + 1;

        return 'usr' . str_pad((string) $next, 2, '0', STR_PAD_LEFT);
    }

    /**
     * Get users joined with supplier name.
     */
    public function getUsersWithSupplier(?string $id = null): array
    {
        $builder = $this->db->table('users u')
            ->select('u.*, s.nama_supplier')
            ->join('supplier s', 's.id_supplier = u.id_supplier', 'left');

        if ($id !== null) {
            return $builder->where('u.user_id', $id)->get()->getRowArray() ?? [];
        }

        return $builder->orderBy('u.created_at', 'DESC')->get()->getResultArray();
    }
}
