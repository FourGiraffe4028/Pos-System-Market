<?php

namespace App\Controllers;

use App\Models\SupplierModel;

class Supplier extends BaseController
{
    protected SupplierModel $supplierModel;

    public function __construct()
    {
        $this->supplierModel = new SupplierModel();
    }

    public function index()
    {
        $data = [
            'title'      => 'Master Supplier - POS System',
            'supplier'   => $this->supplierModel->orderBy('id_supplier', 'ASC')->findAll(),
            'nextId'     => $this->supplierModel->generateId(),
            'validation' => \Config\Services::validation(),
        ];

        return view('supplier/index', $data);
    }

    public function store()
    {
        $rules = [
            'nama_supplier' => [
                'rules'  => 'required|max_length[50]',
                'errors' => ['required' => 'Nama supplier wajib diisi.'],
            ],
            'alamat' => [
                'rules'  => 'required|max_length[100]',
                'errors' => ['required' => 'Alamat supplier wajib diisi.'],
            ],
            'kota' => [
                'rules'  => 'required|max_length[25]',
                'errors' => ['required' => 'Kota supplier wajib diisi.'],
            ],
            'no_telepon' => [
                'rules'  => 'required|max_length[20]',
                'errors' => ['required' => 'No telepon wajib diisi.'],
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

        $id = $this->request->getPost('id_supplier') ?: $this->supplierModel->generateId();

        $dataInsert = [
            'id_supplier'   => $id,
            'nama_supplier' => (string) $this->request->getPost('nama_supplier'),
            'alamat'        => (string) $this->request->getPost('alamat'),
            'kota'          => (string) $this->request->getPost('kota'),
            'no_telepon'    => (string) $this->request->getPost('no_telepon'),
            'aktif'         => 1,
        ];

        $this->supplierModel->insert($dataInsert);

        if ($this->request->isAJAX()) {
            return $this->response->setJSON([
                'status'  => 'success',
                'message' => 'Supplier baru berhasil ditambahkan.',
                'data'    => $dataInsert,
                'nextId'  => $this->supplierModel->generateId(),
            ]);
        }

        return redirect()->to(site_url('supplier'))->with('success', 'Supplier baru berhasil ditambahkan.');
    }

    public function update($id = null)
    {
        if (! $id) {
            return redirect()->to(site_url('supplier'));
        }

        $rules = [
            'nama_supplier' => 'required|max_length[50]',
            'alamat'        => 'required|max_length[100]',
            'kota'          => 'required|max_length[25]',
            'no_telepon'    => 'required|max_length[20]',
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

        $dataUpdate = [
            'id_supplier'   => $id,
            'nama_supplier' => (string) $this->request->getPost('nama_supplier'),
            'alamat'        => (string) $this->request->getPost('alamat'),
            'kota'          => (string) $this->request->getPost('kota'),
            'no_telepon'    => (string) $this->request->getPost('no_telepon'),
            'aktif'         => (int) ($this->request->getPost('aktif') ?? 1),
        ];

        $this->supplierModel->update($id, $dataUpdate);

        if ($this->request->isAJAX()) {
            return $this->response->setJSON([
                'status'  => 'success',
                'message' => 'Data supplier berhasil diperbarui.',
                'data'    => $dataUpdate,
            ]);
        }

        return redirect()->to(site_url('supplier'))->with('success', 'Data supplier berhasil diperbarui.');
    }

    public function delete($id = null)
    {
        if ($id) {
            try {
                $this->supplierModel->delete($id);
                if ($this->request->isAJAX()) {
                    return $this->response->setJSON(['status' => 'success', 'message' => 'Supplier berhasil dihapus.']);
                }
                return redirect()->to(site_url('supplier'))->with('success', 'Supplier berhasil dihapus.');
            } catch (\Exception $e) {
                if ($this->request->isAJAX()) {
                    return $this->response->setStatusCode(400)->setJSON(['status' => 'error', 'message' => 'Supplier gagal dihapus karena masih terkait dengan data transaksi.']);
                }
                return redirect()->to(site_url('supplier'))->with('error', 'Supplier gagal dihapus karena masih terkait dengan data transaksi.');
            }
        }

        return redirect()->to(site_url('supplier'));
    }
}
