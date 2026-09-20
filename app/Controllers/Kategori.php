<?php

namespace App\Controllers;

use App\Models\KategoriModel;

class Kategori extends BaseController
{
    protected KategoriModel $kategoriModel;

    public function __construct()
    {
        $this->kategoriModel = new KategoriModel();
    }

    public function index()
    {
        $data = [
            'title'      => 'Master Kategori - POS System',
            'kategori'   => $this->kategoriModel->orderBy('id_kategori', 'ASC')->findAll(),
            'nextId'     => $this->kategoriModel->generateId(),
            'validation' => \Config\Services::validation(),
        ];

        return view('kategori/index', $data);
    }

    public function store()
    {
        $rules = [
            'nama_kategori' => [
                'rules'  => 'required|max_length[50]|is_unique[kategori.nama_kategori]',
                'errors' => [
                    'required'  => 'Nama kategori wajib diisi.',
                    'is_unique' => 'Nama kategori ini sudah terdaftar.',
                ],
            ],
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

        $id = $this->request->getPost('id_kategori') ?: $this->kategoriModel->generateId();
        $namaKategori = (string) $this->request->getPost('nama_kategori');

        $this->kategoriModel->insert([
            'id_kategori'   => $id,
            'nama_kategori' => $namaKategori,
        ]);

        if ($this->request->isAJAX()) {
            return $this->response->setJSON([
                'status'  => 'success',
                'message' => 'Kategori baru berhasil ditambahkan.',
                'data'    => ['id_kategori' => $id, 'nama_kategori' => $namaKategori],
                'nextId'  => $this->kategoriModel->generateId(),
            ]);
        }

        return redirect()->to(site_url('kategori'))->with('success', 'Kategori baru berhasil ditambahkan.');
    }

    public function update($id = null)
    {
        if (! $id) {
            return redirect()->to(site_url('kategori'));
        }

        $rules = [
            'nama_kategori' => [
                'rules'  => "required|max_length[50]|is_unique[kategori.nama_kategori,id_kategori,{$id}]",
                'errors' => [
                    'required'  => 'Nama kategori wajib diisi.',
                    'is_unique' => 'Nama kategori ini sudah digunakan.',
                ],
            ],
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

        $namaKategori = (string) $this->request->getPost('nama_kategori');

        $this->kategoriModel->update($id, [
            'nama_kategori' => $namaKategori,
        ]);

        if ($this->request->isAJAX()) {
            return $this->response->setJSON([
                'status'  => 'success',
                'message' => 'Data kategori berhasil diperbarui.',
                'data'    => ['id_kategori' => $id, 'nama_kategori' => $namaKategori],
            ]);
        }

        return redirect()->to(site_url('kategori'))->with('success', 'Data kategori berhasil diperbarui.');
    }

    public function delete($id = null)
    {
        if ($id) {
            try {
                $this->kategoriModel->delete($id);
                if ($this->request->isAJAX()) {
                    return $this->response->setJSON(['status' => 'success', 'message' => 'Kategori berhasil dihapus.']);
                }
                return redirect()->to(site_url('kategori'))->with('success', 'Kategori berhasil dihapus.');
            } catch (\Exception $e) {
                if ($this->request->isAJAX()) {
                    return $this->response->setStatusCode(400)->setJSON(['status' => 'error', 'message' => 'Kategori gagal dihapus karena masih digunakan oleh data barang.']);
                }
                return redirect()->to(site_url('kategori'))->with('error', 'Kategori gagal dihapus karena masih digunakan oleh data barang.');
            }
        }

        return redirect()->to(site_url('kategori'));
    }
}
