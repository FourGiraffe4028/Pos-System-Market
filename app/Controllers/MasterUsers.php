<?php

namespace App\Controllers;

use App\Models\UserModel;
use App\Models\SupplierModel;

class MasterUsers extends BaseController
{
    protected UserModel    $userModel;
    protected SupplierModel $supplierModel;

    public function __construct()
    {
        $this->userModel     = new UserModel();
        $this->supplierModel = new SupplierModel();
    }

    /**
     * Tampilkan data master users.
     */
    public function index()
    {
        return view('master_users/index', [
            'title'     => 'Master Data Users - POS System',
            'users'     => $this->userModel->getUsersWithSupplier(),
            'suppliers' => $this->supplierModel->where('aktif', 1)->orderBy('nama_supplier', 'ASC')->findAll(),
            'nextId'    => $this->userModel->generateId(),
        ]);
    }

    /**
     * AJAX Endpoint: Get single user data for Edit Modal.
     */
    public function getJson($id = null)
    {
        if (! $id) {
            return $this->response->setStatusCode(404)->setJSON(['status' => 'error', 'message' => 'User ID tidak valid.']);
        }

        $user = $this->userModel->find($id);
        if (! $user) {
            return $this->response->setStatusCode(404)->setJSON(['status' => 'error', 'message' => 'User tidak ditemukan.']);
        }

        // Jangan sertakan password hash di respon JSON
        unset($user['password']);

        return $this->response->setJSON(['status' => 'success', 'data' => $user]);
    }

    /**
     * Simpan user baru.
     */
    public function store()
    {
        $rules = [
            'username'  => 'required|max_length[30]|is_unique[users.username]',
            'nama_user' => 'required|max_length[50]',
            'password'  => 'required|min_length[6]',
            'role'      => 'required|in_list[admin,kasir,supervisor,owner,inventory]',
        ];

        if (! $this->validate($rules)) {
            if ($this->request->isAJAX()) {
                return $this->response->setStatusCode(400)->setJSON([
                    'status' => 'error',
                    'errors' => $this->validator->getErrors(),
                ]);
            }
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $role       = (string) $this->request->getPost('role');
        $idSupplier = $role === 'inventory' ? ($this->request->getPost('id_supplier') ?: null) : null;
        $userId     = $this->request->getPost('user_id') ?: $this->userModel->generateId();

        $dataInsert = [
            'user_id'     => $userId,
            'username'    => trim((string) $this->request->getPost('username')),
            'nama_user'   => trim((string) $this->request->getPost('nama_user')),
            'password'    => password_hash((string) $this->request->getPost('password'), PASSWORD_DEFAULT),
            'role'        => $role,
            'id_supplier' => $idSupplier,
            'aktif'       => 1,
        ];

        $this->userModel->insert($dataInsert);

        $insertedRow = $this->userModel->getUsersWithSupplier($userId);

        if ($this->request->isAJAX()) {
            return $this->response->setJSON([
                'status'  => 'success',
                'message' => 'User baru berhasil ditambahkan.',
                'data'    => $insertedRow,
                'nextId'  => $this->userModel->generateId(),
            ]);
        }

        return redirect()->to(site_url('master-users'))->with('success', 'User baru berhasil ditambahkan.');
    }

    /**
     * Update user.
     */
    public function update($id = null)
    {
        if (! $id) {
            return $this->response->setStatusCode(400)->setJSON(['status' => 'error', 'message' => 'User ID tidak valid.']);
        }

        $rules = [
            'username'  => "required|max_length[30]|is_unique[users.username,user_id,{$id}]",
            'nama_user' => 'required|max_length[50]',
            'role'      => 'required|in_list[admin,kasir,supervisor,owner,inventory]',
        ];

        // Jika password diisi, validasi min 6 karakter
        $newPassword = (string) $this->request->getPost('password');
        if (! empty($newPassword)) {
            $rules['password'] = 'min_length[6]';
        }

        if (! $this->validate($rules)) {
            if ($this->request->isAJAX()) {
                return $this->response->setStatusCode(400)->setJSON([
                    'status' => 'error',
                    'errors' => $this->validator->getErrors(),
                ]);
            }
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $role       = (string) $this->request->getPost('role');
        $idSupplier = $role === 'inventory' ? ($this->request->getPost('id_supplier') ?: null) : null;

        $dataUpdate = [
            'username'    => trim((string) $this->request->getPost('username')),
            'nama_user'   => trim((string) $this->request->getPost('nama_user')),
            'role'        => $role,
            'id_supplier' => $idSupplier,
            'aktif'       => (int) ($this->request->getPost('aktif') ?? 1),
        ];

        if (! empty($newPassword)) {
            $dataUpdate['password'] = password_hash($newPassword, PASSWORD_DEFAULT);
        }

        $this->userModel->update($id, $dataUpdate);

        $updatedRow = $this->userModel->getUsersWithSupplier($id);

        if ($this->request->isAJAX()) {
            return $this->response->setJSON([
                'status'  => 'success',
                'message' => 'Data user berhasil diperbarui.',
                'data'    => $updatedRow,
            ]);
        }

        return redirect()->to(site_url('master-users'))->with('success', 'Data user berhasil diperbarui.');
    }

    /**
     * Delete user.
     */
    public function delete($id = null)
    {
        if ($id) {
            // Hindari menghapus akun yang sedang dipakai sendiri
            if ($id === session()->get('user_id')) {
                if ($this->request->isAJAX()) {
                    return $this->response->setStatusCode(400)->setJSON(['status' => 'error', 'message' => 'Anda tidak dapat menghapus akun Anda sendiri.']);
                }
                return redirect()->to(site_url('master-users'))->with('error', 'Anda tidak dapat menghapus akun Anda sendiri.');
            }

            try {
                $this->userModel->delete($id);
                if ($this->request->isAJAX()) {
                    return $this->response->setJSON(['status' => 'success', 'message' => 'User berhasil dihapus.']);
                }
                return redirect()->to(site_url('master-users'))->with('success', 'User berhasil dihapus.');
            } catch (\Exception $e) {
                if ($this->request->isAJAX()) {
                    return $this->response->setStatusCode(400)->setJSON(['status' => 'error', 'message' => 'User gagal dihapus karena sudah memiliki riwayat transaksi di sistem.']);
                }
                return redirect()->to(site_url('master-users'))->with('error', 'User gagal dihapus karena sudah memiliki riwayat transaksi.');
            }
        }

        return redirect()->to(site_url('master-users'));
    }
}

