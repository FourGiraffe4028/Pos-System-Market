<?php

namespace App\Controllers;

use App\Models\UserModel;
use App\Models\RecoveryKeyModel;
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
        if (session()->get("isLoggedIn") && ! session()->get("is_recovery_session")) {
            return redirect()->to(site_url("dashboard"));
        }

        $data = [
            "title"      => "Login System - POS System",
            "validation" => \Config\Services::validation(),
        ];

        return view("auth/login", $data);
    }

    /**
     * Process authentication form submission.
     */
    public function processLogin(): RedirectResponse
    {
        $rules = [
            "username" => [
                "rules"  => "required",
                "errors" => [
                    "required" => "Username wajib diisi.",
                ],
            ],
            "password" => [
                "rules"  => "required",
                "errors" => [
                    "required" => "Password wajib diisi.",
                ],
            ],
        ];

        if (! $this->validate($rules)) {
            return redirect()
                ->to(site_url("login"))
                ->withInput()
                ->with("errors", $this->validator->getErrors());
        }

        $username = (string) $this->request->getPost("username");
        $password = (string) $this->request->getPost("password");

        $user = $this->userModel->getActiveUserByUsername($username);

        if (! $user) {
            return redirect()
                ->to(site_url("login"))
                ->withInput()
                ->with("error", "Username tidak ditemukan atau akun non-aktif.");
        }

        if (! password_verify($password, $user["password"])) {
            return redirect()
                ->to(site_url("login"))
                ->withInput()
                ->with("error", "Password yang Anda masukkan salah.");
        }

        // Set User Session
        $sessionData = [
            "user_id"     => $user["user_id"],
            "username"    => $user["username"],
            "nama_user"   => $user["nama_user"],
            "role"        => $user["role"],
            "id_supplier" => $user["id_supplier"],
            "isLoggedIn"  => true,
        ];

        session()->set($sessionData);

        return redirect()
            ->to(site_url("dashboard"))
            ->with("success", "Selamat datang kembali, " . $user["nama_user"] . "!");
    }

    /**
     * Log out user and destroy session.
     */
    public function logout(): RedirectResponse
    {
        session()->destroy();

        return redirect()
            ->to(site_url("login"))
            ->with("success", "Anda telah berhasil keluar dari sistem.");
    }

    /**
     * Tampilkan halaman input Recovery Key.
     */
    public function recovery()
    {
        if (session()->get("isLoggedIn") && ! session()->get("is_recovery_session")) {
            return redirect()->to(site_url("dashboard"));
        }

        return view("auth/recovery", [
            "title" => "Akses Darurat - POS System",
        ]);
    }

    /**
     * Proses verifikasi Recovery Key.
     */
    public function processRecovery(): RedirectResponse
    {
        $plainKey = (string) $this->request->getPost("recovery_key");

        if (empty($plainKey)) {
            return redirect()
                ->to(site_url("recovery"))
                ->with("error", "Recovery Key wajib diisi.");
        }

        $recoveryModel = new RecoveryKeyModel();
        $keyRecord     = $recoveryModel->findValidKey($plainKey);

        if (! $keyRecord) {
            log_message("warning", "[RECOVERY] Percobaan gagal dari IP: " . $this->request->getIPAddress());

            return redirect()
                ->to(site_url("recovery"))
                ->with("error", "Recovery Key tidak valid, sudah pernah digunakan, atau sudah kedaluwarsa.");
        }

        // Invalidate key seketika (one-time use)
        $recoveryModel->invalidate((int) $keyRecord["id"]);

        // Buat recovery session dengan akses terbatas
        session()->set([
            "user_id"             => "RECOVERY",
            "username"            => "recovery_session",
            "nama_user"           => "⚠ Recovery Access",
            "role"                => "admin",
            "is_recovery_session" => true,
            "isLoggedIn"          => true,
        ]);

        log_message("info", "[RECOVERY] Session berhasil dibuat dari IP: " . $this->request->getIPAddress());

        return redirect()
            ->to(site_url("master-users"))
            ->with("warning", "⚠️ Anda masuk menggunakan Recovery Key. Segera buat atau reset akun admin, lalu logout.");
    }
}
