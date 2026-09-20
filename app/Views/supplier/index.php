<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>

<div class="d-flex align-items-center justify-content-between mb-4">
    <div>
        <h4 class="fw-bold mb-1" style="color: var(--secondary);">Data Master Supplier</h4>
        <p class="text-muted small mb-0">Kelola daftar distributor dan pemasok barang.</p>
    </div>
    <button type="button" class="btn btn-primary font-weight-bold" data-bs-toggle="modal" data-bs-target="#modalTambah">
        <i class="fa-solid fa-plus me-1"></i> Tambah Supplier
    </button>
</div>

<div class="card shadow-sm border-0">
    <div class="card-header bg-white py-3 d-flex align-items-center justify-content-between">
        <h5 class="fw-bold mb-0" style="color: #333;">Data Supplier</h5>
        <span class="badge bg-light text-dark border">Total: <?= count($supplier) ?> Supplier</span>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead>
                    <tr>
                        <th style="width: 70px;" class="ps-4">No</th>
                        <th style="width: 120px;">ID</th>
                        <th>Nama Supplier</th>
                        <th>Alamat</th>
                        <th>Kota</th>
                        <th>No Telepon</th>
                        <th>Status</th>
                        <th style="width: 120px;" class="text-end pe-4">Aksi</th>
                    </tr>
                </thead>
                <tbody id="tbodySupplier">
                    <?php if (empty($supplier)) : ?>
                        <tr id="rowEmpty">
                            <td colspan="8" class="text-center py-4 text-muted">Belum ada data supplier.</td>
                        </tr>
                    <?php else : ?>
                        <?php $no = 1; foreach ($supplier as $sup) : ?>
                            <tr id="row-<?= esc($sup['id_supplier']) ?>">
                                <td class="ps-4 fw-bold text-muted row-no"><?= $no++ ?></td>
                                <td><code><?= esc($sup['id_supplier']) ?></code></td>
                                <td class="fw-bold text-dark cell-nama"><?= esc($sup['nama_supplier']) ?></td>
                                <td class="cell-alamat"><?= esc($sup['alamat']) ?></td>
                                <td class="cell-kota"><?= esc($sup['kota']) ?></td>
                                <td class="cell-telp"><?= esc($sup['no_telepon']) ?></td>
                                <td class="cell-status">
                                    <?php if ($sup['aktif'] == 1) : ?>
                                        <span class="badge bg-success">Aktif</span>
                                    <?php else : ?>
                                        <span class="badge bg-secondary">Non-Aktif</span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-end pe-4">
                                    <button type="button" class="btn btn-sm btn-outline-warning me-1" data-bs-toggle="modal" data-bs-target="#modalEdit<?= esc($sup['id_supplier']) ?>">
                                        <i class="fa-solid fa-pen-to-square"></i>
                                    </button>
                                    <button type="button" class="btn btn-sm btn-outline-danger" onclick="deleteSupplier('<?= esc($sup['id_supplier']) ?>')">
                                        <i class="fa-solid fa-trash"></i>
                                    </button>
                                </td>
                            </tr>

                            <!-- Modal Edit Supplier -->
                            <div class="modal fade modal-edit" id="modalEdit<?= esc($sup['id_supplier']) ?>" tabindex="-1" aria-hidden="true">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <form onsubmit="submitEditSupplier(event, '<?= esc($sup['id_supplier']) ?>')">
                                            <?= csrf_field() ?>
                                            <div class="modal-header">
                                                <h5 class="modal-title fw-bold">Edit Supplier</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body text-start">
                                                <div class="alert alert-danger d-none modal-alert mb-3"></div>
                                                <div class="mb-3">
                                                    <label class="form-label">ID Supplier</label>
                                                    <input type="text" class="form-control bg-light" value="<?= esc($sup['id_supplier']) ?>" readonly>
                                                </div>
                                                <div class="mb-3">
                                                    <label class="form-label">Nama Supplier</label>
                                                    <input type="text" name="nama_supplier" class="form-control" value="<?= esc($sup['nama_supplier']) ?>" required max="50">
                                                </div>
                                                <div class="mb-3">
                                                    <label class="form-label">Alamat</label>
                                                    <input type="text" name="alamat" class="form-control" value="<?= esc($sup['alamat']) ?>" required max="100">
                                                </div>
                                                <div class="row">
                                                    <div class="col-md-6 mb-3">
                                                        <label class="form-label">Kota</label>
                                                        <input type="text" name="kota" class="form-control" value="<?= esc($sup['kota']) ?>" required max="25">
                                                    </div>
                                                    <div class="col-md-6 mb-3">
                                                        <label class="form-label">No Telepon</label>
                                                        <input type="text" name="no_telepon" class="form-control" value="<?= esc($sup['no_telepon']) ?>" required max="20">
                                                    </div>
                                                </div>
                                                <div class="mb-3">
                                                    <label class="form-label">Status Aktif</label>
                                                    <select name="aktif" class="form-select">
                                                        <option value="1" <?= $sup['aktif'] == 1 ? 'selected' : '' ?>>Aktif</option>
                                                        <option value="0" <?= $sup['aktif'] == 0 ? 'selected' : '' ?>>Non-Aktif</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                                                <button type="submit" class="btn btn-primary font-weight-bold">Simpan Perubahan</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal Tambah Supplier -->
<div class="modal fade" id="modalTambah" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="formTambah" onsubmit="submitTambahSupplier(event)">
                <?= csrf_field() ?>
                <div class="modal-header">
                    <h5 class="modal-title fw-bold">Tambah Supplier Baru</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div id="tambahAlert" class="alert alert-danger d-none mb-3"></div>
                    <div class="mb-3">
                        <label class="form-label">ID Supplier (Otomatis)</label>
                        <input type="text" name="id_supplier" id="tambah_id" class="form-control bg-light" value="<?= esc($nextId) ?>" readonly>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Nama Supplier</label>
                        <input type="text" name="nama_supplier" class="form-control" placeholder="Contoh: PT. Indomarco Prismatama" required max="50" autofocus>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Alamat</label>
                        <input type="text" name="alamat" class="form-control" placeholder="Jalan / Kawasan Industri" required max="100">
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Kota</label>
                            <input type="text" name="kota" class="form-control" placeholder="Contoh: Jakarta" required max="25">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">No Telepon</label>
                            <input type="text" name="no_telepon" class="form-control" placeholder="Contoh: 0211234567" required max="20">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary font-weight-bold">Simpan Supplier</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
    const baseUrl = "<?= site_url('/') ?>";

    function submitTambahSupplier(e) {
        e.preventDefault();
        const form = document.getElementById('formTambah');
        const formData = new FormData(form);
        const alertBox = document.getElementById('tambahAlert');

        alertBox.classList.add('d-none');

        fetch(baseUrl + 'supplier/store', {
            method: 'POST',
            headers: { 'X-Requested-With': 'XMLHttpRequest' },
            body: formData
        })
        .then(res => res.json())
        .then(data => {
            if (data.status === 'success') {
                const modal = bootstrap.Modal.getInstance(document.getElementById('modalTambah'));
                if (modal) modal.hide();
                form.reset();
                if (data.nextId) document.getElementById('tambah_id').value = data.nextId;

                window.location.reload();
            } else {
                let errs = Object.values(data.errors || {}).join('<br>') || data.message;
                alertBox.innerHTML = errs;
                alertBox.classList.remove('d-none');
            }
        });
    }

    function submitEditSupplier(e, id) {
        e.preventDefault();
        const form = e.target;
        const formData = new FormData(form);
        const alertBox = form.querySelector('.modal-alert');

        if (alertBox) alertBox.classList.add('d-none');

        fetch(baseUrl + 'supplier/update/' + id, {
            method: 'POST',
            headers: { 'X-Requested-With': 'XMLHttpRequest' },
            body: formData
        })
        .then(res => res.json())
        .then(data => {
            if (data.status === 'success') {
                const modal = bootstrap.Modal.getInstance(document.getElementById('modalEdit' + id));
                if (modal) modal.hide();

                const row = document.getElementById('row-' + id);
                if (row) {
                    row.querySelector('.cell-nama').textContent = data.data.nama_supplier;
                    row.querySelector('.cell-alamat').textContent = data.data.alamat;
                    row.querySelector('.cell-kota').textContent = data.data.kota;
                    row.querySelector('.cell-telp').textContent = data.data.no_telepon;
                    row.querySelector('.cell-status').innerHTML = data.data.aktif == 1 ? '<span class="badge bg-success">Aktif</span>' : '<span class="badge bg-secondary">Non-Aktif</span>';
                }

                Swal.fire({ icon: 'success', title: 'Berhasil', text: data.message, timer: 1500, showConfirmButton: false });
            } else {
                let errs = Object.values(data.errors || {}).join('<br>') || data.message;
                if (alertBox) {
                    alertBox.innerHTML = errs;
                    alertBox.classList.remove('d-none');
                }
            }
        });
    }

    function deleteSupplier(id) {
        Swal.fire({
            title: 'Hapus Supplier?',
            text: "Supplier " + id + " akan dihapus.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#dc2626',
            confirmButtonText: 'Ya, Hapus'
        }).then(result => {
            if (result.isConfirmed) {
                fetch(baseUrl + 'supplier/delete/' + id, {
                    headers: { 'X-Requested-With': 'XMLHttpRequest' }
                })
                .then(res => res.json())
                .then(data => {
                    if (data.status === 'success') {
                        const row = document.getElementById('row-' + id);
                        if (row) row.remove();
                        Swal.fire('Terhapus!', data.message, 'success');
                    } else {
                        Swal.fire('Gagal!', data.message, 'error');
                    }
                });
            }
        });
    }
</script>
<?= $this->endSection() ?>
