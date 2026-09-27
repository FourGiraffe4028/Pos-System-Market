<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

/**
 * RecoveryFilter
 *
 * Membatasi akses recovery session hanya ke master-users dan logout.
 */
class RecoveryFilter implements FilterInterface
{
    protected array $allowedPaths = [
        'master-users',
        'logout',
    ];

    public function before(RequestInterface $request, $arguments = null)
    {
        // Hanya aktif jika ini adalah recovery session
        if (! session()->get('is_recovery_session')) {
            return;
        }

        $uri = ltrim((string) $request->getUri()->getPath(), '/');

        // Normalisasi jika ada subfolder
        $scriptName = ltrim((string) $request->getServer('SCRIPT_NAME'), '/');
        $baseDir    = trim(dirname($scriptName), '/');
        if ($baseDir !== '' && $baseDir !== '.') {
            if (str_starts_with($uri, $baseDir . '/')) {
                $uri = substr($uri, strlen($baseDir) + 1);
            } elseif ($uri === $baseDir) {
                $uri = '';
            }
        }
        $uri = ltrim($uri, '/');

        // Cek apakah URI yang diminta termasuk yang diizinkan
        foreach ($this->allowedPaths as $path) {
            if ($uri === $path || str_starts_with($uri, $path . '/')) {
                return; // Izinkan akses
            }
        }

        // Redirect ke master-users dengan peringatan
        return redirect()
            ->to(site_url('master-users'))
            ->with('warning', '⚠ Recovery session hanya bisa mengakses halaman Master Users. Segera buat atau reset akun admin.');
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // Do nothing
    }
}