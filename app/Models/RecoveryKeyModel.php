<?php

namespace App\Models;

use CodeIgniter\Model;

class RecoveryKeyModel extends Model
{
    protected $table            = "recovery_keys";
    protected $primaryKey       = "id";
    protected $useAutoIncrement = true;
    protected $returnType       = "array";
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        "key_hash",
        "label",
        "expires_at",
        "used_at",
        "is_used",
    ];

    protected $useTimestamps = false;

    public function findValidKey(string $plainKey): ?array
    {
        $activeKeys = $this
            ->where("is_used", 0)
            ->where("expires_at >=", date("Y-m-d H:i:s"))
            ->findAll();

        foreach ($activeKeys as $row) {
            if (password_verify($plainKey, $row["key_hash"])) {
                return $row;
            }
        }

        return null;
    }

    public function invalidate(int $id): void
    {
        $this->update($id, [
            "is_used" => 1,
            "used_at" => date("Y-m-d H:i:s"),
        ]);
    }

    public function countActiveKeys(): int
    {
        return $this
            ->where("is_used", 0)
            ->where("expires_at >=", date("Y-m-d H:i:s"))
            ->countAllResults();
    }
}
