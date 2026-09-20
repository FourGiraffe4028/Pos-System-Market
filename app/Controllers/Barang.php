<?php

namespace App\Controllers;

use App\Models\BarangModel;
use App\Models\KategoriModel;
use App\Models\SatuanModel;

class Barang extends BaseController
{
    protected BarangModel $barangModel;
    protected KategoriModel $kategoriModel;
    protected SatuanModel $satuanModel;

    public function __construct()
    {
        $this->barangModel   = new BarangModel();
        $this->kategoriModel = new KategoriModel();
        $this->satuanModel   = new SatuanModel();
    }

    public function index()
    {
        $data = [
            'title'    => 'Master Data Barang - POS System',
            'barang'   => $this->barangModel->getBarangWithRelations(),
            'kategori' => $this->kategoriModel->orderBy('nama_kategori', 'ASC')->findAll(),
            'satuan'   => $this->satuanModel->orderBy('nama_satuan', 'ASC')->findAll(),
            'nextId'   => $this->barangModel->generateId(),
        ];

        return view('barang/index', $data);
    }

    /**
     * Get JSON data for a single barang (for AJAX Edit modal).
     */
    public function getJson($id = null)
    {
        if (! $id) {
            return $this->response->setStatusCode(404)->setJSON(['status' => 'error', 'message' => 'ID Barang tidak valid.']);
        }

        $barang = $this->barangModel->find($id);

        if (! $barang) {
            return $this->response->setStatusCode(404)->setJSON(['status' => 'error', 'message' => 'Data barang tidak ditemukan.']);
        }

        return $this->response->setJSON(['status' => 'success', 'data' => $barang]);
    }

    public function store()
    {
        $rules = [
            'nama_barang'  => [
                'rules'  => 'required|max_length[100]',
                'errors' => ['required' => 'Nama barang wajib diisi.'],
            ],
            'id_kategori'  => [
                'rules'  => 'required',
                'errors' => ['required' => 'Kategori barang wajib dipilih.'],
            ],
            'id_satuan'    => [
                'rules'  => 'required',
                'errors' => ['required' => 'Satuan barang wajib dipilih.'],
            ],
            'harga_jual'   => [
                'rules'  => 'required|numeric|greater_than_equal_to[0]',
                'errors' => [
                    'required'               => 'Harga jual wajib diisi.',
                    'greater_than_equal_to' => 'Harga jual tidak boleh bernilai negatif.',
                ],
            ],
            'stok_minimum' => [
                'rules'  => 'required|integer|greater_than_equal_to[0]',
                'errors' => [
                    'required'               => 'Stok minimum wajib diisi.',
                    'greater_than_equal_to' => 'Stok minimum tidak boleh negatif.',
                ],
            ],
        ];

        $barcode = trim((string) $this->request->getPost('barcode'));
        if (! empty($barcode)) {
            $rules['barcode'] = [
                'rules'  => 'is_unique[barang.barcode]',
                'errors' => ['is_unique' => 'Barcode ini sudah terdaftar pada barang lain.'],
            ];
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

        $id = $this->request->getPost('id_barang') ?: $this->barangModel->generateId();
        $createdBy = session()->get('user_id');

        $dataInsert = [
            'id_barang'           => $id,
            'barcode'             => ! empty($barcode) ? $barcode : null,
            'nama_barang'         => (string) $this->request->getPost('nama_barang'),
            'id_kategori'         => (string) $this->request->getPost('id_kategori'),
            'id_satuan'           => (string) $this->request->getPost('id_satuan'),
            'harga_jual'          => (float) $this->request->getPost('harga_jual'),
            'stok_minimum'        => (int) $this->request->getPost('stok_minimum'),
            'stok'                => 0, // Mandatory: Stok awal 0, pengisian wajib lewat faktur pembelian
            'harga_beli_terakhir' => 0.00,
            'aktif'               => 1,
            'created_by'          => $createdBy,
        ];

        $this->barangModel->insert($dataInsert);

        $insertedRow = $this->barangModel->getBarangWithRelations($id);

        if ($this->request->isAJAX()) {
            return $this->response->setJSON([
                'status'  => 'success',
                'message' => 'Barang baru berhasil ditambahkan! (Stok awal = 0)',
                'data'    => $insertedRow,
                'nextId'  => $this->barangModel->generateId(),
            ]);
        }

        return redirect()->to(site_url('barang'))->with('success', 'Barang baru berhasil ditambahkan.');
    }

    public function update($id = null)
    {
        if (! $id) {
            if ($this->request->isAJAX()) {
                return $this->response->setStatusCode(400)->setJSON(['status' => 'error', 'message' => 'ID Barang tidak valid.']);
            }
            return redirect()->to(site_url('barang'));
        }

        $rules = [
            'nama_barang'  => 'required|max_length[100]',
            'id_kategori'  => 'required',
            'id_satuan'    => 'required',
            'harga_jual'   => 'required|numeric|greater_than_equal_to[0]',
            'stok_minimum' => 'required|integer|greater_than_equal_to[0]',
        ];

        $barcode = trim((string) $this->request->getPost('barcode'));
        if (! empty($barcode)) {
            $rules['barcode'] = "is_unique[barang.barcode,id_barang,{$id}]";
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

        $dataUpdate = [
            'barcode'      => ! empty($barcode) ? $barcode : null,
            'nama_barang'  => (string) $this->request->getPost('nama_barang'),
            'id_kategori'  => (string) $this->request->getPost('id_kategori'),
            'id_satuan'    => (string) $this->request->getPost('id_satuan'),
            'harga_jual'   => (float) $this->request->getPost('harga_jual'),
            'stok_minimum' => (int) $this->request->getPost('stok_minimum'),
            'aktif'        => (int) ($this->request->getPost('aktif') ?? 1),
        ];

        $this->barangModel->update($id, $dataUpdate);

        $updatedRow = $this->barangModel->getBarangWithRelations($id);

        if ($this->request->isAJAX()) {
            return $this->response->setJSON([
                'status'  => 'success',
                'message' => 'Data barang berhasil diperbarui.',
                'data'    => $updatedRow,
            ]);
        }

        return redirect()->to(site_url('barang'))->with('success', 'Data barang berhasil diperbarui.');
    }

    public function delete($id = null)
    {
        if ($id) {
            try {
                $this->barangModel->delete($id);
                if ($this->request->isAJAX()) {
                    return $this->response->setJSON(['status' => 'success', 'message' => 'Barang berhasil dihapus.']);
                }
                return redirect()->to(site_url('barang'))->with('success', 'Barang berhasil dihapus.');
            } catch (\Exception $e) {
                if ($this->request->isAJAX()) {
                    return $this->response->setStatusCode(400)->setJSON(['status' => 'error', 'message' => 'Barang gagal dihapus karena sudah tercatat dalam transaksi.']);
                }
                return redirect()->to(site_url('barang'))->with('error', 'Barang gagal dihapus karena sudah tercatat dalam transaksi.');
            }
        }

        return redirect()->to(site_url('barang'));
    }
}
