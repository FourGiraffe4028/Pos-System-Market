<?php

namespace App\Commands;

use App\Models\RecoveryKeyModel;
use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;

class RecoveryGenerate extends BaseCommand
{
    protected $group       = "App";
    protected $name        = "recovery:generate";
    protected $description = "Generate a new one-time emergency recovery key for admin access.";
    protected $usage       = "recovery:generate [options]";
    protected $options     = [
        "--label" => "Label/nama untuk key ini (opsional). Contoh: --label=PakReihan",
        "--days"  => "Masa berlaku dalam hari (default: 30). Contoh: --days=60",
    ];

    public function run(array $params)
    {
        $model = new RecoveryKeyModel();

        $label = CLI::getOption("label") ?? ("Key-" . date("YmdHis"));
        $days  = (int) (CLI::getOption("days") ?? 30);

        if ($days <= 0) {
            CLI::error("Nilai --days harus lebih dari 0.");
            return;
        }

        $plainKey  = bin2hex(random_bytes(16));
        $keyHash   = password_hash($plainKey, PASSWORD_DEFAULT);
        $expiresAt = date("Y-m-d H:i:s", strtotime("+{$days} days"));

        $model->insert([
            "key_hash"   => $keyHash,
            "label"      => $label,
            "expires_at" => $expiresAt,
        ]);

        $activeCount = $model->countActiveKeys();

        CLI::newLine();
        CLI::write("======================================================", "yellow");
        CLI::write("    RECOVERY KEY GENERATED — SIMPAN BAIK-BAIK!       ", "yellow");
        CLI::write("======================================================", "yellow");
        CLI::newLine();
        CLI::write("  Label        : " . $label, "white");
        CLI::write("  Dibuat pada  : " . date("d M Y H:i:s"), "white");
        CLI::write("  Berlaku s/d  : " . date("d M Y H:i:s", strtotime($expiresAt)) . " (+" . $days . " hari)", "white");
        CLI::write("  Total key aktif sekarang: " . $activeCount, "white");
        CLI::newLine();
        CLI::write("  RECOVERY KEY (SALIN SEKARANG!):", "green");
        CLI::write("  " . $plainKey, "green");
        CLI::newLine();
        CLI::write("  ⚠  PERINGATAN:", "red");
        CLI::write("  • Key ini TIDAK AKAN ditampilkan lagi setelah ini.", "red");
        CLI::write("  • Simpan di password manager atau tempat aman.", "red");
        CLI::write("  • Key ini hanya bisa dipakai SATU KALI.", "red");
        CLI::write("  • Akses via browser: " . site_url("recovery"), "red");
        CLI::newLine();
        CLI::write("======================================================", "yellow");
        CLI::newLine();
    }
}
