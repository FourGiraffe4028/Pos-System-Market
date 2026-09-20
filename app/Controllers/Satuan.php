<?php

namespace App\Controllers;

use App\Models\SatuanModel;

class Satuan extends BaseController
{
    protected SatuanModel $satuanModel;

    public function __construct()
    {
        $this->satuanModel = new SatuanModel();
    }

    public function index()
    {
        $data = [
            'title'      => 'Master Satuan - POS System',
            'satuan'     => $this->satuanModel->orderBy('id_satuan', 'ASC')->findAll(),
            'nextId'     => $this->satuanModel->generateId(),
            'validation' => \Config\Services::validation(),
        ];

        return view('satuan/index', $data);
    }

    public function store()
    {
        $rules = [
            'nama_satuan' => [
                'rules'  => 'required|max_length[20]|is_unique[satuan.nama_satuan]',
                'errors' => [
                    'required'  => 'Nama satuan wajib diisi.',
                    'is_unique' => 'Nama satuan ini sudah terdaftar.',
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

        $id = $this->request->getPost('id_satuan') ?: $this->satuanModel->generateId();
        $namaSatuan = (string) $this->request->getPost('nama_satuan');

        $this->satuanModel->insert([
            'id_satuan'   => $id,
            'nama_satuan' => $namaSatuan,
        ]);

        if ($this->request->isAJAX()) {
            return $this->response->setJSON([
                'status'  => 'success',
                'message' => 'Satuan baru berhasil ditambahkan.',
                'data'    => ['id_satuan' => $id, 'nama_satuan' => $namaSatuan],
                'nextId'  => $this->satuanModel->generateId(),
            ]);
        }

        return redirect()->to(site_url('satuan'))->with('success', 'Satuan baru berhasil ditambahkan.');
    }

    public function update($id = null)
    {
        if (! $id) {
            return redirect()->to(site_url('satuan'));
        }

        $rules = [
            'nama_satuan' => [
                'rules'  => "required|max_length[20]|is_unique[satuan.nama_satuan,id_satuan,{$id}]",
                'errors' => [
                    'required'  => 'Nama satuan wajib diisi.',
                    'is_unique' => 'Nama satuan ini sudah digunakan.',
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

        $namaSatuan = (string) $this->request->getPost('nama_satuan');

        $this->satuanModel->update($id, [
            'nama_satuan' => $namaSatuan,
        ]);

        if ($this->request->isAJAX()) {
            return $this->response->setJSON([
                'status'  => 'success',
                'message' => 'Data satuan berhasil diperbarui.',
                'data'    => ['id_satuan' => $id, 'nama_satuan' => $namaSatuan],
            ]);
        }

        return redirect()->to(site_url('satuan'))->with('success', 'Data satuan berhasil diperbarui.');
    }

    public function delete($id = null)
    {
        if ($id) {
            try {
                $this->satuanModel->delete($id);
                if ($this->request->isAJAX()) {
                    return $this->response->setJSON(['status' => 'success', 'message' => 'Satuan berhasil dihapus.']);
                }
                return redirect()->to(site_url('satuan'))->with('success', 'Satuan berhasil dihapus.');
            } catch (\Exception $e) {
                if ($this->request->isAJAX()) {
                    return $this->response->setStatusCode(400)->setJSON(['status' => 'error', 'message' => 'Satuan gagal dihapus karena masih digunakan oleh data barang.']);
                }
                return redirect()->to(site_url('satuan'))->with('error', 'Satuan gagal dihapus karena masih digunakan oleh data barang.');
            }
        }

        return redirect()->to(site_url('satuan'));
    }
}
