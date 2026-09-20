<?php

namespace App\Controllers;

use App\Models\UserModel;
use CodeIgniter\HTTP\RedirectResponse;

class Auth extends BaseController
{
    protected UserModel $userModel;

    public function __construct()
    {
        $this->userModel = new UserModel();
    }

    /**
     * Render Login View.
     * Redirects to dashboard if already authenticated.
     */
    public function login()
    {
        if (session()->get('isLoggedIn')) {
            return redirect()->to(site_url('dashboard'));
        }

        $data = [
            'title'      => 'Login System - POS System',
            'validation' => \Config\Services::validation(),
        ];

        return view('auth/login', $data);
    }

    /**
     * Process authentication form submission.
     */
    public function processLogin(): RedirectResponse
    {
        $rules = [
            'username' => [
                'rules'  => 'required',
                'errors' => [
                    'required' => 'Username wajib diisi.',
                ],
            ],
            'password' => [
                'rules'  => 'required',
                'errors' => [
                    'required' => 'Password wajib diisi.',
                ],
            ],
        ];

        if (! $this->validate($rules)) {
            return redirect()
                ->to(site_url('login'))
                ->withInput()
                ->with('errors', $this->validator->getErrors());
        }

        $username = (string) $this->request->getPost('username');
        $password = (string) $this->request->getPost('password');

        $user = $this->userModel->getActiveUserByUsername($username);

        if (! $user) {
            return redirect()
                ->to(site_url('login'))
                ->withInput()
                ->with('error', 'Username tidak ditemukan atau akun non-aktif.');
        }

        if (! password_verify($password, $user['password'])) {
            return redirect()
                ->to(site_url('login'))
                ->withInput()
                ->with('error', 'Password yang Anda masukkan salah.');
        }

        // Set User Session
        $sessionData = [
            'user_id'     => $user['user_id'],
            'username'    => $user['username'],
            'nama_user'   => $user['nama_user'],
            'role'        => $user['role'],
            'id_supplier' => $user['id_supplier'],
            'isLoggedIn'  => true,
        ];

        session()->set($sessionData);

        return redirect()
            ->to(site_url('dashboard'))
            ->with('success', 'Selamat datang kembali, ' . $user['nama_user'] . '!');
    }

    /**
     * Log out user and destroy session.
     */
    public function logout(): RedirectResponse
    {
        session()->destroy();

        return redirect()
            ->to(site_url('login'))
            ->with('success', 'Anda telah berhasil keluar dari sistem.');
    }
}
