<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>

<div class="d-flex align-items-center justify-content-between mb-4">
    <div>
        <h4 class="fw-bold mb-1" style="color: var(--secondary);">Data Master Satuan Barang</h4>
        <p class="text-muted small mb-0">Kelola unit kemasan atau takaran produk.</p>
    </div>
    <button type="button" class="btn btn-primary font-weight-bold" data-bs-toggle="modal" data-bs-target="#modalTambah">
        <i class="fa-solid fa-plus me-1"></i> Tambah Satuan
    </button>
</div>

<div class="card shadow-sm border-0">
    <div class="card-header bg-white py-3 d-flex align-items-center justify-content-between">
        <h5 class="fw-bold mb-0" style="color: #333;">Data Satuan</h5>
        <span class="badge bg-light text-dark border">Total: <?= count($satuan) ?> Satuan</span>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead>
                    <tr>
                        <th style="width: 80px;" class="ps-4">No</th>
                        <th style="width: 150px;">ID Satuan</th>
                        <th>Nama Satuan</th>
                        <th style="width: 150px;" class="text-end pe-4">Aksi</th>
                    </tr>
                </thead>
                <tbody id="tbodySatuan">
                    <?php if (empty($satuan)) : ?>
                        <tr id="rowEmpty">
                            <td colspan="4" class="text-center py-4 text-muted">Belum ada data satuan.</td>
                        </tr>
                    <?php else : ?>
                        <?php $no = 1; foreach ($satuan as $s) : ?>
                            <tr id="row-<?= esc($s['id_satuan']) ?>">
                                <td class="ps-4 fw-bold text-muted row-no"><?= $no++ ?></td>
                                <td><code><?= esc($s['id_satuan']) ?></code></td>
                                <td class="fw-bold text-dark cell-nama"><?= esc($s['nama_satuan']) ?></td>
                                <td class="text-end pe-4">
                                    <button type="button" class="btn btn-sm btn-outline-warning me-1" data-bs-toggle="modal" data-bs-target="#modalEdit<?= esc($s['id_satuan']) ?>">
                                        <i class="fa-solid fa-pen-to-square"></i>
                                    </button>
                                    <button type="button" class="btn btn-sm btn-outline-danger" onclick="deleteSatuan('<?= esc($s['id_satuan']) ?>')">
                                        <i class="fa-solid fa-trash"></i>
                                    </button>
                                </td>
                            </tr>

                            <!-- Modal Edit Satuan -->
                            <div class="modal fade modal-edit" id="modalEdit<?= esc($s['id_satuan']) ?>" tabindex="-1" aria-hidden="true">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <form onsubmit="submitEditSatuan(event, '<?= esc($s['id_satuan']) ?>')">
                                            <?= csrf_field() ?>
                                            <div class="modal-header">
                                                <h5 class="modal-title fw-bold">Edit Satuan</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body text-start">
                                                <div class="alert alert-danger d-none modal-alert mb-3"></div>
                                                <div class="mb-3">
                                                    <label class="form-label">ID Satuan</label>
                                                    <input type="text" class="form-control bg-light" value="<?= esc($s['id_satuan']) ?>" readonly>
                                                </div>
                                                <div class="mb-3">
                                                    <label class="form-label">Nama Satuan</label>
                                                    <input type="text" name="nama_satuan" class="form-control input-nama" value="<?= esc($s['nama_satuan']) ?>" required max="20">
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

<!-- Modal Tambah Satuan -->
<div class="modal fade" id="modalTambah" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="formTambah" onsubmit="submitTambahSatuan(event)">
                <?= csrf_field() ?>
                <div class="modal-header">
                    <h5 class="modal-title fw-bold">Tambah Satuan Baru</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div id="tambahAlert" class="alert alert-danger d-none mb-3"></div>
                    <div class="mb-3">
                        <label class="form-label">ID Satuan (Otomatis)</label>
                        <input type="text" name="id_satuan" id="tambah_id" class="form-control bg-light" value="<?= esc($nextId) ?>" readonly>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Nama Satuan</label>
                        <input type="text" name="nama_satuan" id="tambah_nama" class="form-control" placeholder="Contoh: Pcs / Pack / Dus" required max="20" autofocus>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary font-weight-bold">Simpan Satuan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
    const baseUrl = "<?= site_url('/') ?>";

    function submitTambahSatuan(e) {
        e.preventDefault();
        const form = document.getElementById('formTambah');
        const formData = new FormData(form);
        const alertBox = document.getElementById('tambahAlert');

        alertBox.classList.add('d-none');

        fetch(baseUrl + 'satuan/store', {
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

    function submitEditSatuan(e, id) {
        e.preventDefault();
        const form = e.target;
        const formData = new FormData(form);
        const alertBox = form.querySelector('.modal-alert');

        if (alertBox) alertBox.classList.add('d-none');

        fetch(baseUrl + 'satuan/update/' + id, {
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
                if (row) row.querySelector('.cell-nama').textContent = data.data.nama_satuan;

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

    function deleteSatuan(id) {
        Swal.fire({
            title: 'Hapus Satuan?',
            text: "Satuan " + id + " akan dihapus.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#dc2626',
            confirmButtonText: 'Ya, Hapus'
        }).then(result => {
            if (result.isConfirmed) {
                fetch(baseUrl + 'satuan/delete/' + id, {
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
