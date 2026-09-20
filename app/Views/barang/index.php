<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>

<div class="d-flex align-items-center justify-content-between mb-4">
    <div>
        <h4 class="fw-bold mb-1" style="color: var(--secondary);">Data Master Barang</h4>
        <p class="text-muted small mb-0">Kelola katalog barang, barcode, kategori, satuan, harga jual, dan stok minimum.</p>
    </div>
    <button type="button" class="btn btn-primary font-weight-bold" data-bs-toggle="modal" data-bs-target="#modalTambahBarang" onclick="prepareTambahModal()">
        <i class="fa-solid fa-plus me-1"></i> Tambah Barang Baru
    </button>
</div>

<div class="card shadow-sm border-0">
    <div class="card-header bg-white py-3 d-flex align-items-center justify-content-between">
        <h5 class="fw-bold mb-0" style="color: #333;">Daftar Barang</h5>
        <span class="badge bg-light text-dark border" id="totalBarangBadge">Total: <?= count($barang) ?> Item</span>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0" id="tableBarang">
                <thead>
                    <tr>
                        <th style="width: 60px;" class="ps-4">No</th>
                        <th style="width: 140px;">ID / Barcode</th>
                        <th>Nama Barang</th>
                        <th>Kategori</th>
                        <th>Satuan</th>
                        <th>Harga Jual</th>
                        <th class="text-center">Stok / Min</th>
                        <th>Pembuat</th>
                        <th style="width: 110px;" class="text-end pe-4">Aksi</th>
                    </tr>
                </thead>
                <tbody id="tbodyBarang">
                    <?php if (empty($barang)) : ?>
                        <tr id="rowEmpty">
                            <td colspan="9" class="text-center py-4 text-muted">Belum ada data barang. Silakan tambah barang baru.</td>
                        </tr>
                    <?php else : ?>
                        <?php $no = 1; foreach ($barang as $b) : ?>
                            <tr id="row-<?= esc($b['id_barang']) ?>">
                                <td class="ps-4 fw-bold text-muted row-no"><?= $no++ ?></td>
                                <td>
                                    <code><?= esc($b['id_barang']) ?></code>
                                    <div class="small text-muted barcode-text"><?= ! empty($b['barcode']) ? '<i class="fa-solid fa-barcode"></i> ' . esc($b['barcode']) : '' ?></div>
                                </td>
                                <td class="fw-bold text-dark nama-barang-text"><?= esc($b['nama_barang']) ?></td>
                                <td><span class="badge bg-light text-dark border kategori-text"><?= esc($b['nama_kategori']) ?></span></td>
                                <td class="satuan-text"><?= esc($b['nama_satuan']) ?></td>
                                <td class="fw-bold text-success harga-jual-text">Rp <?= number_format($b['harga_jual'], 2, ',', '.') ?></td>
                                <td class="text-center stok-text">
                                    <span class="badge <?= $b['stok'] <= $b['stok_minimum'] ? ($b['stok'] == 0 ? 'bg-danger' : 'bg-warning text-dark') : 'bg-success' ?> px-2 py-1">
                                        <?= esc($b['stok']) ?> / Min <?= esc($b['stok_minimum']) ?>
                                    </span>
                                </td>
                                <td>
                                    <small class="text-muted"><i class="fa-solid fa-user-pen me-1"></i> <span class="pembuat-text"><?= esc($b['pembuat'] ?? $b['created_by'] ?? 'System') ?></span></small>
                                </td>
                                <td class="text-end pe-4">
                                    <button type="button" class="btn btn-sm btn-outline-warning me-1" onclick="editBarang('<?= esc($b['id_barang']) ?>')">
                                        <i class="fa-solid fa-pen-to-square"></i>
                                    </button>
                                    <button type="button" class="btn btn-sm btn-outline-danger" onclick="deleteBarang('<?= esc($b['id_barang']) ?>')">
                                        <i class="fa-solid fa-trash"></i>
                                    </button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal Tambah Barang (Bootstrap) -->
<div class="modal fade" id="modalTambahBarang" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form id="formTambahBarang" onsubmit="submitTambahBarang(event)">
                <?= csrf_field() ?>
                <div class="modal-header">
                    <h5 class="modal-title fw-bold">Tambah Barang Baru</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div id="tambahAlert" class="alert alert-danger d-none mb-3"></div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">ID Barang (Otomatis)</label>
                            <input type="text" name="id_barang" id="tambah_id_barang" class="form-control bg-light" value="<?= esc($nextId) ?>" readonly>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Barcode Barang (Opsional)</label>
                            <input type="text" name="barcode" id="tambah_barcode" class="form-control" placeholder="Contoh: 8991234567890">
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Nama Barang <span class="text-danger">*</span></label>
                        <input type="text" name="nama_barang" id="tambah_nama_barang" class="form-control" placeholder="Contoh: Buku Tulis Sinar Dunia 38 Lbr" required maxlength="100">
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Kategori Barang <span class="text-danger">*</span></label>
                            <select name="id_kategori" id="tambah_id_kategori" class="form-select" required>
                                <option value="">-- Pilih Kategori --</option>
                                <?php foreach ($kategori as $k) : ?>
                                    <option value="<?= esc($k['id_kategori']) ?>"><?= esc($k['nama_kategori']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Satuan Barang <span class="text-danger">*</span></label>
                            <select name="id_satuan" id="tambah_id_satuan" class="form-select" required>
                                <option value="">-- Pilih Satuan --</option>
                                <?php foreach ($satuan as $s) : ?>
                                    <option value="<?= esc($s['id_satuan']) ?>"><?= esc($s['nama_satuan']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Harga Jual (Rp) <span class="text-danger">*</span></label>
                            <input type="number" step="500" name="harga_jual" id="tambah_harga_jual" class="form-control" placeholder="Contoh: 5000" value="0" required min="0">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Stok Minimum Alert <span class="text-danger">*</span></label>
                            <input type="number" name="stok_minimum" id="tambah_stok_minimum" class="form-control" placeholder="Contoh: 10" value="10" required min="0">
                        </div>
                    </div>

                    <div class="alert alert-warning py-2 small mb-0">
                        <i class="fa-solid fa-circle-info me-1"></i> Stok awal otomatis bernilai <strong>0</strong>. Pembuat barang terikat session: <strong><?= esc(session()->get('nama_user')) ?></strong>.
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary font-weight-bold" id="btnSimpanTambah">
                        <i class="fa-solid fa-floppy-disk me-1"></i> Simpan Barang
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Edit Barang (Bootstrap) -->
<div class="modal fade" id="modalEditBarang" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form id="formEditBarang" onsubmit="submitEditBarang(event)">
                <?= csrf_field() ?>
                <input type="hidden" name="id_barang" id="edit_id_barang_hidden">

                <div class="modal-header">
                    <h5 class="modal-title fw-bold">Edit Data Barang</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div id="editAlert" class="alert alert-danger d-none mb-3"></div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">ID Barang</label>
                            <input type="text" id="edit_id_barang_text" class="form-control bg-light" readonly>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Barcode Barang</label>
                            <input type="text" name="barcode" id="edit_barcode" class="form-control">
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Nama Barang <span class="text-danger">*</span></label>
                        <input type="text" name="nama_barang" id="edit_nama_barang" class="form-control" required maxlength="100">
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Kategori Barang <span class="text-danger">*</span></label>
                            <select name="id_kategori" id="edit_id_kategori" class="form-select" required>
                                <option value="">-- Pilih Kategori --</option>
                                <?php foreach ($kategori as $k) : ?>
                                    <option value="<?= esc($k['id_kategori']) ?>"><?= esc($k['nama_kategori']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Satuan Barang <span class="text-danger">*</span></label>
                            <select name="id_satuan" id="edit_id_satuan" class="form-select" required>
                                <option value="">-- Pilih Satuan --</option>
                                <?php foreach ($satuan as $s) : ?>
                                    <option value="<?= esc($s['id_satuan']) ?>"><?= esc($s['nama_satuan']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Harga Jual (Rp) <span class="text-danger">*</span></label>
                            <input type="number" step="500" name="harga_jual" id="edit_harga_jual" class="form-control" required min="0">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Stok Minimum Alert <span class="text-danger">*</span></label>
                            <input type="number" name="stok_minimum" id="edit_stok_minimum" class="form-control" required min="0">
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Status Aktif</label>
                        <select name="aktif" id="edit_aktif" class="form-select">
                            <option value="1">Aktif (Bisa Dijual)</option>
                            <option value="0">Non-Aktif (Diarsipkan)</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary font-weight-bold" id="btnSimpanEdit">
                        <i class="fa-solid fa-floppy-disk me-1"></i> Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
    const baseUrl = "<?= site_url('/') ?>";

    function prepareTambahModal() {
        document.getElementById('tambahAlert').classList.add('d-none');
    }

    // Submit Tambah Barang via AJAX
    function submitTambahBarang(e) {
        e.preventDefault();
        const form = document.getElementById('formTambahBarang');
        const formData = new FormData(form);
        const alertBox = document.getElementById('tambahAlert');
        const btnSubmit = document.getElementById('btnSimpanTambah');

        alertBox.classList.add('d-none');
        btnSubmit.disabled = true;

        fetch(baseUrl + 'barang/store', {
            method: 'POST',
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: formData
        })
        .then(res => res.json())
        .then(data => {
            btnSubmit.disabled = false;
            if (data.status === 'success') {
                // Close Modal
                const modalEl = document.getElementById('modalTambahBarang');
                const modal = bootstrap.Modal.getInstance(modalEl);
                if (modal) modal.hide();

                // Reset form
                form.reset();
                if (data.nextId) {
                    document.getElementById('tambah_id_barang').value = data.nextId;
                }

                // Append new row dynamically without page refresh
                appendRowToTable(data.data);

                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil!',
                    text: data.message,
                    timer: 2000,
                    showConfirmButton: false
                });
            } else {
                let errHtml = '<ul class="mb-0 ps-3">';
                if (data.errors) {
                    for (let key in data.errors) {
                        errHtml += `<li>${data.errors[key]}</li>`;
                    }
                } else {
                    errHtml += `<li>${data.message || 'Terjadi kesalahan.'}</li>`;
                }
                errHtml += '</ul>';
                alertBox.innerHTML = errHtml;
                alertBox.classList.remove('d-none');
            }
        })
        .catch(err => {
            btnSubmit.disabled = false;
            console.error(err);
        });
    }

    // Load data for Edit Modal via AJAX
    function editBarang(id) {
        const alertBox = document.getElementById('editAlert');
        alertBox.classList.add('d-none');

        fetch(baseUrl + 'barang/get-json/' + id, {
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        })
        .then(res => res.json())
        .then(res => {
            if (res.status === 'success') {
                const b = res.data;
                document.getElementById('edit_id_barang_hidden').value = b.id_barang;
                document.getElementById('edit_id_barang_text').value = b.id_barang;
                document.getElementById('edit_barcode').value = b.barcode || '';
                document.getElementById('edit_nama_barang').value = b.nama_barang;
                document.getElementById('edit_id_kategori').value = b.id_kategori;
                document.getElementById('edit_id_satuan').value = b.id_satuan;
                document.getElementById('edit_harga_jual').value = b.harga_jual;
                document.getElementById('edit_stok_minimum').value = b.stok_minimum;
                document.getElementById('edit_aktif').value = b.aktif;

                const modal = new bootstrap.Modal(document.getElementById('modalEditBarang'));
                modal.show();
            } else {
                Swal.fire('Error', res.message || 'Gagal memuat data barang.', 'error');
            }
        });
    }

    // Submit Edit Barang via AJAX
    function submitEditBarang(e) {
        e.preventDefault();
        const form = document.getElementById('formEditBarang');
        const id = document.getElementById('edit_id_barang_hidden').value;
        const formData = new FormData(form);
        const alertBox = document.getElementById('editAlert');
        const btnSubmit = document.getElementById('btnSimpanEdit');

        alertBox.classList.add('d-none');
        btnSubmit.disabled = true;

        fetch(baseUrl + 'barang/update/' + id, {
            method: 'POST',
            headers: { 'X-Requested-With': 'XMLHttpRequest' },
            body: formData
        })
        .then(res => res.json())
        .then(data => {
            btnSubmit.disabled = false;
            if (data.status === 'success') {
                const modalEl = document.getElementById('modalEditBarang');
                const modal = bootstrap.Modal.getInstance(modalEl);
                if (modal) modal.hide();

                // Update existing row in table dynamically without page refresh
                updateRowInTable(data.data);

                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil!',
                    text: data.message,
                    timer: 2000,
                    showConfirmButton: false
                });
            } else {
                let errHtml = '<ul class="mb-0 ps-3">';
                if (data.errors) {
                    for (let key in data.errors) {
                        errHtml += `<li>${data.errors[key]}</li>`;
                    }
                } else {
                    errHtml += `<li>${data.message || 'Terjadi kesalahan.'}</li>`;
                }
                errHtml += '</ul>';
                alertBox.innerHTML = errHtml;
                alertBox.classList.remove('d-none');
            }
        })
        .catch(err => {
            btnSubmit.disabled = false;
            console.error(err);
        });
    }

    // Delete Barang via AJAX
    function deleteBarang(id) {
        Swal.fire({
            title: 'Apakah Anda Yakin?',
            text: "Barang " + id + " akan dihapus permanen.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#dc2626',
            cancelButtonColor: '#6b7280',
            confirmButtonText: 'Ya, Hapus!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                fetch(baseUrl + 'barang/delete/' + id, {
                    headers: { 'X-Requested-With': 'XMLHttpRequest' }
                })
                .then(res => res.json())
                .then(data => {
                    if (data.status === 'success') {
                        const row = document.getElementById('row-' + id);
                        if (row) row.remove();
                        reindexTable();
                        Swal.fire('Terhapus!', data.message, 'success');
                    } else {
                        Swal.fire('Gagal!', data.message, 'error');
                    }
                });
            }
        });
    }

    // Helper: Dynamically append new row to table
    function appendRowToTable(b) {
        const tbody = document.getElementById('tbodyBarang');
        const emptyRow = document.getElementById('rowEmpty');
        if (emptyRow) emptyRow.remove();

        const tr = document.createElement('tr');
        tr.id = 'row-' + b.id_barang;

        const barcodeText = b.barcode ? `<i class="fa-solid fa-barcode"></i> ${b.barcode}` : '';
        const hargaFormatted = new Intl.NumberFormat('id-ID', { minimumFractionDigits: 2 }).format(b.harga_jual);
        const stokBadgeClass = b.stok <= b.stok_minimum ? (b.stok == 0 ? 'bg-danger' : 'bg-warning text-dark') : 'bg-success';
        const pembuatText = b.pembuat || b.created_by || 'System';

        tr.innerHTML = `
            <td class="ps-4 fw-bold text-muted row-no">#</td>
            <td>
                <code>${b.id_barang}</code>
                <div class="small text-muted barcode-text">${barcodeText}</div>
            </td>
            <td class="fw-bold text-dark nama-barang-text">${b.nama_barang}</td>
            <td><span class="badge bg-light text-dark border kategori-text">${b.nama_kategori}</span></td>
            <td class="satuan-text">${b.nama_satuan}</td>
            <td class="fw-bold text-success harga-jual-text">Rp ${hargaFormatted}</td>
            <td class="text-center stok-text">
                <span class="badge ${stokBadgeClass} px-2 py-1">
                    ${b.stok} / Min ${b.stok_minimum}
                </span>
            </td>
            <td>
                <small class="text-muted"><i class="fa-solid fa-user-pen me-1"></i> <span class="pembuat-text">${pembuatText}</span></small>
            </td>
            <td class="text-end pe-4">
                <button type="button" class="btn btn-sm btn-outline-warning me-1" onclick="editBarang('${b.id_barang}')">
                    <i class="fa-solid fa-pen-to-square"></i>
                </button>
                <button type="button" class="btn btn-sm btn-outline-danger" onclick="deleteBarang('${b.id_barang}')">
                    <i class="fa-solid fa-trash"></i>
                </button>
            </td>
        `;

        tbody.prepend(tr);
        reindexTable();
    }

    // Helper: Dynamically update row values in table
    function updateRowInTable(b) {
        const row = document.getElementById('row-' + b.id_barang);
        if (!row) return;

        const barcodeText = b.barcode ? `<i class="fa-solid fa-barcode"></i> ${b.barcode}` : '';
        const hargaFormatted = new Intl.NumberFormat('id-ID', { minimumFractionDigits: 2 }).format(b.harga_jual);
        const stokBadgeClass = b.stok <= b.stok_minimum ? (b.stok == 0 ? 'bg-danger' : 'bg-warning text-dark') : 'bg-success';
        const pembuatText = b.pembuat || b.created_by || 'System';

        row.querySelector('.barcode-text').innerHTML = barcodeText;
        row.querySelector('.nama-barang-text').textContent = b.nama_barang;
        row.querySelector('.kategori-text').textContent = b.nama_kategori;
        row.querySelector('.satuan-text').textContent = b.nama_satuan;
        row.querySelector('.harga-jual-text').textContent = 'Rp ' + hargaFormatted;
        row.querySelector('.stok-text').innerHTML = `<span class="badge ${stokBadgeClass} px-2 py-1">${b.stok} / Min ${b.stok_minimum}</span>`;
        row.querySelector('.pembuat-text').textContent = pembuatText;
    }

    function reindexTable() {
        const rows = document.querySelectorAll('#tbodyBarang tr:not(#rowEmpty)');
        rows.forEach((r, idx) => {
            const noEl = r.querySelector('.row-no');
            if (noEl) noEl.textContent = idx + 1;
        });
        const badge = document.getElementById('totalBarangBadge');
        if (badge) badge.textContent = `Total: ${rows.length} Item`;
    }
</script>
<?= $this->endSection() ?>
