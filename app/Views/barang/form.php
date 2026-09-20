<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>

<?php $isEdit = isset($barang); ?>

<div class="d-flex align-items-center justify-content-between mb-4">
    <div>
        <h4 class="fw-bold mb-1" style="color: var(--secondary);"><?= $isEdit ? 'Edit Data Barang' : 'Tambah Barang Baru' ?></h4>
        <p class="text-muted small mb-0"><?= $isEdit ? 'Perbarui rincian data barang pada sistem POS.' : 'Lengkapi formulir di bawah ini untuk menambahkan barang baru.' ?></p>
    </div>
    <a href="<?= site_url('barang') ?>" class="btn btn-outline-secondary font-weight-bold">
        <i class="fa-solid fa-arrow-left me-1"></i> Kembali ke Daftar
    </a>
</div>

<!-- Errors Alert -->
<?php if (session()->getFlashdata('errors')) : ?>
    <div class="alert alert-danger alert-dismissible fade show mb-4" role="alert">
        <h6 class="fw-bold mb-1"><i class="fa-solid fa-triangle-exclamation me-1"></i> Terjadi Kesalahan Validasi:</h6>
        <ul class="mb-0 ps-3">
            <?php foreach (session()->getFlashdata('errors') as $err) : ?>
                <li><?= esc($err) ?></li>
            <?php endforeach; ?>
        </ul>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
<?php endif; ?>

<div class="row">
    <div class="col-lg-8">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-white py-3">
                <h5 class="fw-bold mb-0" style="color: #333;">
                    <i class="fa-solid <?= $isEdit ? 'fa-pen-to-square' : 'fa-box' ?> text-warning me-2"></i> Form Master Barang
                </h5>
            </div>
            <div class="card-body">
                <form action="<?= site_url($isEdit ? 'barang/update/' . $barang['id_barang'] : 'barang/store') ?>" method="post">
                    <?= csrf_field() ?>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">ID Barang (Otomatis)</label>
                            <input type="text" 
                                   name="id_barang" 
                                   class="form-control bg-light" 
                                   value="<?= esc($isEdit ? $barang['id_barang'] : old('id_barang', $nextId)) ?>" 
                                   readonly>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Barcode Barang (Opsional)</label>
                            <input type="text" 
                                   name="barcode" 
                                   class="form-control" 
                                   placeholder="Contoh: 8991234567890" 
                                   value="<?= esc($isEdit ? $barang['barcode'] : old('barcode')) ?>">
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Nama Barang <span class="text-danger">*</span></label>
                        <input type="text" 
                               name="nama_barang" 
                               class="form-control" 
                               placeholder="Contoh: Buku Tulis Sinar Dunia 38 Lbr" 
                               value="<?= esc($isEdit ? $barang['nama_barang'] : old('nama_barang')) ?>" 
                               required 
                               maxlength="100" 
                               autofocus>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Kategori Barang <span class="text-danger">*</span></label>
                            <select name="id_kategori" class="form-select" required>
                                <option value="">-- Pilih Kategori --</option>
                                <?php foreach ($kategori as $k) : ?>
                                    <option value="<?= esc($k['id_kategori']) ?>" 
                                        <?= ($isEdit && $barang['id_kategori'] == $k['id_kategori']) || old('id_kategori') == $k['id_kategori'] ? 'selected' : '' ?>>
                                        <?= esc($k['nama_kategori']) ?> (<?= esc($k['id_kategori']) ?>)
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Satuan Barang <span class="text-danger">*</span></label>
                            <select name="id_satuan" class="form-select" required>
                                <option value="">-- Pilih Satuan --</option>
                                <?php foreach ($satuan as $s) : ?>
                                    <option value="<?= esc($s['id_satuan']) ?>" 
                                        <?= ($isEdit && $barang['id_satuan'] == $s['id_satuan']) || old('id_satuan') == $s['id_satuan'] ? 'selected' : '' ?>>
                                        <?= esc($s['nama_satuan']) ?> (<?= esc($s['id_satuan']) ?>)
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Harga Jual (Rp) <span class="text-danger">*</span></label>
                            <input type="number" 
                                   step="500" 
                                   name="harga_jual" 
                                   class="form-control" 
                                   placeholder="Contoh: 5000" 
                                   value="<?= esc($isEdit ? $barang['harga_jual'] : old('harga_jual', '0')) ?>" 
                                   required min="0">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Stok Minimum Alert <span class="text-danger">*</span></label>
                            <input type="number" 
                                   name="stok_minimum" 
                                   class="form-control" 
                                   placeholder="Contoh: 10" 
                                   value="<?= esc($isEdit ? $barang['stok_minimum'] : old('stok_minimum', '10')) ?>" 
                                   required min="0">
                        </div>
                    </div>

                    <?php if ($isEdit) : ?>
                        <div class="mb-3">
                            <label class="form-label">Status Aktif</label>
                            <select name="aktif" class="form-select">
                                <option value="1" <?= $barang['aktif'] == 1 ? 'selected' : '' ?>>Aktif (Bisa Dijual)</option>
                                <option value="0" <?= $barang['aktif'] == 0 ? 'selected' : '' ?>>Non-Aktif (Diarsipkan)</option>
                            </select>
                        </div>
                    <?php endif; ?>

                    <div class="d-flex align-items-center justify-content-end gap-2 mt-4">
                        <a href="<?= site_url('barang') ?>" class="btn btn-secondary">Batal</a>
                        <button type="submit" class="btn btn-primary font-weight-bold px-4">
                            <i class="fa-solid fa-floppy-disk me-1"></i> <?= $isEdit ? 'Simpan Perubahan' : 'Tambah Barang' ?>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="col-lg-4 mt-3 mt-lg-0">
        <div class="card shadow-sm border-0 mb-3" style="background: #fafafa;">
            <div class="card-body">
                <h6 class="fw-bold mb-2 text-warning"><i class="fa-solid fa-circle-info me-1"></i> Ketentuan Stok Barang</h6>
                <p class="small text-muted mb-2">
                    Sesuai alur operasional POS:
                </p>
                <ul class="small text-muted ps-3 mb-0">
                    <li class="mb-1">Stok awal barang baru secara otomatis diset ke <strong>0</strong>.</li>
                    <li class="mb-1">Pengisian stok barang wajib dilakukan melalui <strong>Faktur Pembelian / Stok Masuk</strong>.</li>
                    <li class="mb-1">Pencatatan pembuat barang akan terikat pada akun session login: <strong><?= esc(session()->get('nama_user')) ?></strong>.</li>
                </ul>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>
