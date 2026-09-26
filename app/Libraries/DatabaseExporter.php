<?php

namespace App\Libraries;

use CodeIgniter\Database\BaseConnection;

/**
 * DatabaseExporter
 *
 * Library untuk melakukan export dan import database secara pure PHP,
 * tanpa memerlukan tool eksternal seperti mysqldump.
 */
class DatabaseExporter
{
    protected BaseConnection $db;

    protected array $excludedTables = [
        'migrations',
    ];

    public function __construct()
    {
        $this->db = \Config\Database::connect();
    }

    /**
     * Export seluruh database ke string SQL.
     */
    public function export(): string
    {
        $dbName  = $this->db->getDatabase();
        $output  = "-- ============================================================\n";
        $output .= "-- POS System Market — Database Backup\n";
        $output .= "-- Generated  : " . date('Y-m-d H:i:s') . "\n";
        $output .= "-- Database   : " . $dbName . "\n";
        $output .= "-- PHP Version: " . PHP_VERSION . "\n";
        $output .= "-- ============================================================\n\n";
        $output .= "SET SQL_MODE = \"NO_AUTO_VALUE_ON_ZERO\";\n";
        $output .= "SET FOREIGN_KEY_CHECKS = 0;\n";
        $output .= "SET NAMES utf8mb4;\n\n";

        $tables = $this->db->listTables();

        foreach ($tables as $table) {
            if (in_array($table, $this->excludedTables, true)) {
                continue;
            }

            $output .= $this->exportTable($table);
        }

        $output .= "SET FOREIGN_KEY_CHECKS = 1;\n";

        return $output;
    }

    /**
     * Export satu tabel (struktur + data).
     */
    protected function exportTable(string $table): string
    {
        $out = "-- --------------------------------------------------------\n";
        $out .= "-- Table: `{$table}`\n";
        $out .= "-- --------------------------------------------------------\n\n";

        $out .= "DROP TABLE IF EXISTS `{$table}`;\n\n";

        $result = $this->db->query("SHOW CREATE TABLE `{$table}`")->getRowArray();
        if (! empty($result['Create Table'])) {
            $out .= $result['Create Table'] . ";\n\n";
        }

        // Ambil daftar kolom dan identifikasi mana VIRTUAL / STORED generated columns
        $colFields = $this->db->getFieldData($table);
        $insertCols = [];

        foreach ($colFields as $field) {
            // Ambil detail kolom via SHOW FULL COLUMNS
            $colDetail = $this->db->query("SHOW FULL COLUMNS FROM `{$table}` LIKE '" . $this->db->escapeString($field->name) . "'")->getRowArray();
            $extra = strtolower($colDetail['Extra'] ?? '');

            // Skip jika VIRTUAL GENERATED atau STORED GENERATED
            if (str_contains($extra, 'generated')) {
                continue;
            }

            $insertCols[] = $field->name;
        }

        // Ambil data hanya untuk kolom yang bukan Generated Column
        if (! empty($insertCols)) {
            $selectCols = '`' . implode('`, `', $insertCols) . '`';
            $rows       = $this->db->query("SELECT {$selectCols} FROM `{$table}`")->getResultArray();

            if (! empty($rows)) {
                $out .= "INSERT INTO `{$table}` ({$selectCols}) VALUES\n";

                $valueLines = [];
                foreach ($rows as $row) {
                    $escaped = [];
                    foreach ($row as $value) {
                        if ($value === null) {
                            $escaped[] = 'NULL';
                        } elseif (is_numeric($value) && ! preg_match('/^0\d/', $value)) {
                            $escaped[] = $value;
                        } else {
                            $escaped[] = "'" . $this->db->escapeString((string) $value) . "'";
                        }
                    }
                    $valueLines[] = '(' . implode(', ', $escaped) . ')';
                }

                $out .= implode(",\n", $valueLines) . ";\n\n";
            }
        }

        return $out;
    }

    /**
     * Simpan backup ke file di writable/backups/.
     */
    public function save(): string
    {
        $backupDir = WRITEPATH . 'backups' . DIRECTORY_SEPARATOR;

        if (! is_dir($backupDir)) {
            mkdir($backupDir, 0755, true);
        }

        $htaccess = $backupDir . '.htaccess';
        if (! file_exists($htaccess)) {
            file_put_contents($htaccess, "Deny from all\n");
        }

        $filename = 'backup_' . date('Ymd_His') . '.sql';
        $filepath = $backupDir . $filename;

        file_put_contents($filepath, $this->export());

        return $filename;
    }

    /**
     * Restore database dari file SQL.
     */
    public function restore(string $filename): bool
    {
        $filepath = WRITEPATH . 'backups' . DIRECTORY_SEPARATOR . basename($filename);

        if (! file_exists($filepath)) {
            log_message('error', '[DatabaseExporter] File backup tidak ditemukan: ' . $filename);
            return false;
        }

        $sql        = file_get_contents($filepath);
        $statements = $this->parseSql($sql);

        try {
            $this->db->query('SET FOREIGN_KEY_CHECKS = 0');

            foreach ($statements as $stmt) {
                $stmt = trim($stmt);
                if ($stmt !== '' && ! str_starts_with($stmt, '--')) {
                    $this->db->query($stmt);
                }
            }

            $this->db->query('SET FOREIGN_KEY_CHECKS = 1');

            return true;
        } catch (\Throwable $e) {
            log_message('error', '[DatabaseExporter] Restore gagal: ' . $e->getMessage());
            $this->db->query('SET FOREIGN_KEY_CHECKS = 1');
            return false;
        }
    }

    /**
     * Parse string SQL menjadi array of statements.
     */
    protected function parseSql(string $sql): array
    {
        $statements = [];
        $buffer     = '';
        $delimiter  = ';';

        $lines = explode("\n", $sql);

        foreach ($lines as $line) {
            $trimmed = trim($line);

            if ($trimmed === '' || str_starts_with($trimmed, '--')) {
                continue;
            }

            if (preg_match('/^DELIMITER\s+(\S+)\s*$/i', $trimmed, $matches)) {
                $delimiter = $matches[1];
                continue;
            }

            $buffer .= $line . "\n";

            if (str_ends_with(rtrim($line), $delimiter)) {
                $stmt = rtrim($buffer);

                if ($delimiter !== ';') {
                    $stmt = rtrim(substr($stmt, 0, -strlen($delimiter)));
                } else {
                    $stmt = rtrim(substr($stmt, 0, -1));
                }

                if (trim($stmt) !== '') {
                    $statements[] = trim($stmt);
                }

                $buffer    = '';
                $delimiter = ';';
            }
        }

        if (trim($buffer) !== '') {
            $statements[] = trim($buffer);
        }

        return $statements;
    }

    /**
     * Daftar semua file backup yang tersedia.
     */
    public function listBackups(): array
    {
        $backupDir = WRITEPATH . 'backups' . DIRECTORY_SEPARATOR;

        if (! is_dir($backupDir)) {
            return [];
        }

        $files = glob($backupDir . 'backup_*.sql');
        if (! $files) {
            return [];
        }

        usort($files, fn ($a, $b) => filemtime($b) - filemtime($a));

        return array_map(function (string $f): array {
            $sizeBytes = filesize($f);
            $sizeLabel = $sizeBytes >= 1048576
                ? round($sizeBytes / 1048576, 2) . ' MB'
                : round($sizeBytes / 1024, 1) . ' KB';

            return [
                'filename' => basename($f),
                'size'     => $sizeLabel,
                'created'  => date('d M Y H:i:s', filemtime($f)),
                'filepath' => $f,
            ];
        }, $files);
    }

    /**
     * Hapus file backup.
     */
    public function deleteBackup(string $filename): bool
    {
        $filepath = WRITEPATH . 'backups' . DIRECTORY_SEPARATOR . basename($filename);

        if (file_exists($filepath)) {
            return unlink($filepath);
        }

        return false;
    }

    /**
     * Path file backup.
     */
    public function getBackupPath(string $filename): string
    {
        return WRITEPATH . 'backups' . DIRECTORY_SEPARATOR . basename($filename);
    }
}