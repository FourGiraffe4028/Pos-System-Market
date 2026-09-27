<?php

namespace App\Controllers;

use App\Models\RecoveryKeyModel;

/**
 * RecoveryKeys Controller
 *
 * Mengelola fitur UI Manajemen Recovery Key khusus untuk role Owner.
 */
class RecoveryKeys extends BaseController
{
    protected RecoveryKeyModel $recoveryModel;

    public function __construct()
    {
        $this->recoveryModel = new RecoveryKeyModel();
    }

    /**
     * Tampilkan daftar seluruh Recovery Keys.
     */
    public function index()
    {
        $keys = $this->recoveryModel->orderBy('created_at', 'DESC')->findAll();

        return view('recovery_keys/index', [
            'title' => 'Kelola Recovery Key - POS System',
            'keys'  => $keys,
        ]);
    }

    /**
     * Generate Recovery Key baru via AJAX.
     */
    public function generate()
    {
        $label = trim((string) $this->request->getPost('label'));
        if ($label === '') {
            $label = 'Key-' . date('YmdHis');
        }

        $days = (int) ($this->request->getPost('days') ?? 30);

        if ($days <= 0) {
            return $this->response->setStatusCode(400)->setJSON([
                'status'  => 'error',
                'message' => 'Masa berlaku (hari) harus lebih dari 0.',
            ]);
        }

        // Generate 32-character hex key (128-bit entropy)
        $plainKey  = bin2hex(random_bytes(16));
        $keyHash   = password_hash($plainKey, PASSWORD_DEFAULT);
        $expiresAt = date('Y-m-d H:i:s', strtotime("+{$days} days"));

        $this->recoveryModel->insert([
            'key_hash'   => $keyHash,
            'label'      => $label,
            'expires_at' => $expiresAt,
        ]);

        log_message('info', '[RECOVERY_UI] Key baru di-generate oleh Owner: ' . session()->get('username') . ' (Label: ' . $label . ')');

        return $this->response->setJSON([
            'status'     => 'success',
            'message'    => 'Recovery Key baru berhasil di-generate!',
            'plain_key'  => $plainKey,
            'label'      => $label,
            'expires_at' => date('d M Y H:i:s', strtotime($expiresAt)),
        ]);
    }

    /**
     * Hapus / Revoke Recovery Key.
     */
    public function delete($id = null)
    {
        if (! $id) {
            if ($this->request->isAJAX()) {
                return $this->response->setStatusCode(400)->setJSON(['status' => 'error', 'message' => 'ID tidak valid.']);
            }
            return redirect()->to(site_url('recovery-keys'));
        }

        $this->recoveryModel->delete($id);

        log_message('info', '[RECOVERY_UI] Key ID ' . $id . ' dihapus oleh Owner: ' . session()->get('username'));

        if ($this->request->isAJAX()) {
            return $this->response->setJSON([
                'status'  => 'success',
                'message' => 'Recovery Key berhasil dihapus/dibatalkan.',
            ]);
        }

        return redirect()->to(site_url('recovery-keys'))->with('success', 'Recovery Key berhasil dihapus.');
    }
}