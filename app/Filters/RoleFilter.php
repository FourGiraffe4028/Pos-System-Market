<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

class RoleFilter implements FilterInterface
{
    /**
     * Check if logged in user has one of the specified allowed roles.
     *
     * Usage in routes:
     * $routes->get('kasir', 'Kasir::index', ['filter' => 'role:kasir,admin']);
     *
     * @param RequestInterface $request
     * @param array|null       $arguments Allowed roles passed from route configuration
     *
     * @return mixed
     */
    public function before(RequestInterface $request, $arguments = null)
    {
        $session = session();

        if (! $session->get('isLoggedIn')) {
            if ($request->isAJAX()) {
                return response()
                    ->setStatusCode(ResponseInterface::HTTP_UNAUTHORIZED)
                    ->setJSON([
                        'status'  => 'error',
                        'message' => 'Sesi Anda telah berakhir. Silakan login kembali.',
                    ]);
            }

            return redirect()
                ->to(site_url('login'))
                ->with('error', 'Silakan login terlebih dahulu.');
        }

        $userRole = $session->get('role');

        if (! empty($arguments)) {
            // Normalize allowed roles
            $allowedRoles = array_map('trim', $arguments);

            if (! in_array($userRole, $allowedRoles, true)) {
                if ($request->isAJAX()) {
                    return response()
                        ->setStatusCode(ResponseInterface::HTTP_FORBIDDEN)
                        ->setJSON([
                            'status'  => 'error',
                            'message' => 'Akses ditolak. Peran ' . strtoupper($userRole) . ' tidak diizinkan.',
                        ]);
                }

                return redirect()
                    ->to(site_url('dashboard'))
                    ->with('error', 'Akses Ditolak: Peran Anda (' . strtoupper($userRole) . ') tidak memiliki hak akses ke halaman tersebut.');
            }
        }
    }

    /**
     * @param RequestInterface  $request
     * @param ResponseInterface $response
     * @param array|null        $arguments
     *
     * @return mixed
     */
    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // Do nothing
    }
}
