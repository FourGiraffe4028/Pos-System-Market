<?php

namespace App\Controllers;

use App\Libraries\DatabaseExporter;

/**
 * Backup Controller
 */
class Backup extends BaseController
{
    protected DatabaseExporter $exporter;

    public function __construct()
    {
        $this->exporter = new DatabaseExporter();
    }

    /**
     * Tampilkan daftar semua backup.
     */
    public function index()
    {
        return view('backup/index', [
            'title'   => 'Manajemen Backup Database - POS System',
            'backups' => $this->exporter->listBackups(),
        ]);
    }

    /**
     * Buat backup baru.
     */
    public function create()
    {
        try {
            $filename = $this->exporter->save();

            log_message('info', '[BACKUP] Backup dibuat: ' . $filename . ' oleh: ' . session()->get('username'));

            return redirect()
                ->to(site_url('backup'))
                ->with('success', "✅ Backup berhasil dibuat: <strong>{$filename}</strong>");
        } catch (\Throwable $e) {
            log_message('error', '[BACKUP] Gagal membuat backup: ' . $e->getMessage());

            return redirect()
                ->to(site_url('backup'))
                ->with('error', 'Backup gagal dibuat. Periksa log untuk detail.');
        }
    }

    /**
     * Download file backup.
     */
    public function download(string $filename = '')
    {
        if (empty($filename)) {
            return redirect()->to(site_url('backup'))->with('error', 'Nama file tidak valid.');
        }

        $filepath = $this->exporter->getBackupPath($filename);

        if (! file_exists($filepath)) {
            return redirect()
                ->to(site_url('backup'))
                ->with('error', 'File backup tidak ditemukan.');
        }

        log_message('info', '[BACKUP] Download: ' . $filename . ' oleh: ' . session()->get('username'));

        return $this->response
            ->setHeader('Content-Type', 'application/octet-stream')
            ->setHeader('Content-Disposition', 'attachment; filename="' . basename($filename) . '"')
            ->setHeader('Content-Length', (string) filesize($filepath))
            ->setBody(file_get_contents($filepath));
    }

    /**
     * Restore database dari file backup.
     */
    public function restore(string $filename = '')
    {
        if (empty($filename)) {
            return redirect()->to(site_url('backup'))->with('error', 'Nama file tidak valid.');
        }

        $filepath = $this->exporter->getBackupPath($filename);

        if (! file_exists($filepath)) {
            return redirect()
                ->to(site_url('backup'))
                ->with('error', 'File backup tidak ditemukan.');
        }

        try {
            // Safety backup
            $safetyFile = $this->exporter->save();

            log_message('info', '[BACKUP] Safety backup sebelum restore: ' . $safetyFile);

            $success = $this->exporter->restore($filename);

            if ($success) {
                log_message('info', '[BACKUP] Restore berhasil dari: ' . $filename . ' oleh: ' . session()->get('username'));

                return redirect()
                    ->to(site_url('backup'))
                    ->with('success', "✅ Data berhasil di-restore dari: <strong>{$filename}</strong>.<br>Safety backup sebelum restore tersimpan sebagai: <strong>{$safetyFile}</strong>.");
            }

            return redirect()
                ->to(site_url('backup'))
                ->with('error', 'Restore gagal. File mungkin corrupt.');
        } catch (\Throwable $e) {
            log_message('error', '[BACKUP] Restore gagal: ' . $e->getMessage());

            return redirect()
                ->to(site_url('backup'))
                ->with('error', 'Restore gagal: ' . esc($e->getMessage()));
        }
    }

    /**
     * Hapus file backup.
     */
    public function delete(string $filename = '')
    {
        if (empty($filename)) {
            if ($this->request->isAJAX()) {
                return $this->response->setStatusCode(400)->setJSON(['status' => 'error', 'message' => 'Nama file tidak valid.']);
            }
            return redirect()->to(site_url('backup'))->with('error', 'Nama file tidak valid.');
        }

        $deleted = $this->exporter->deleteBackup($filename);

        if ($this->request->isAJAX()) {
            if ($deleted) {
                log_message('info', '[BACKUP] Hapus: ' . $filename . ' oleh: ' . session()->get('username'));
                return $this->response->setJSON(['status' => 'success', 'message' => 'Backup berhasil dihapus.']);
            }
            return $this->response->setStatusCode(404)->setJSON(['status' => 'error', 'message' => 'File tidak ditemukan.']);
        }

        if ($deleted) {
            return redirect()->to(site_url('backup'))->with('success', 'Backup berhasil dihapus.');
        }
        return redirect()->to(site_url('backup'))->with('error', 'File backup tidak ditemukan.');
    }
}