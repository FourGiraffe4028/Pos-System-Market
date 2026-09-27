<?php $this->extend('layout/main'); ?>
<?php $this->section('content'); ?>

<div class="d-flex justify-content-between align-items-center mb-3">
    <div>
        <h4 class="fw-bold mb-0"><i class="fa-solid fa-database me-2 text-warning"></i>Backup & Restore Database</h4>
        <small class="text-muted">Kelola snapshot database untuk pemulihan data.</small>
    </div>
    <a href="<?= site_url('backup/create') ?>" class="btn btn-warning btn-sm fw-bold"
       onclick="return confirm('Buat backup dari kondisi database saat ini?')">
        <i class="fa-solid fa-floppy-disk me-1"></i>Buat Backup Sekarang
    </a>
</div>

<!-- Info Card -->
<div class="row g-3 mb-4">
    <div class="col-md-4">
        <div class="card border-0 bg-warning bg-opacity-10 h-100">
            <div class="card-body d-flex align-items-start gap-3">
                <div class="rounded-circle bg-warning d-flex align-items-center justify-content-center" style="width:44px;height:44px;flex-shrink:0;">
                    <i class="fa-solid fa-floppy-disk text-dark"></i>
                </div>
                <div>
                    <div class="fw-bold">Buat Backup</div>
                    <small class="text-muted">Simpan snapshot database sekarang ke file .sql. Cocok dilakukan sebelum perubahan besar.</small>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card border-0 bg-info bg-opacity-10 h-100">
            <div class="card-body d-flex align-items-start gap-3">
                <div class="rounded-circle bg-info d-flex align-items-center justify-content-center" style="width:44px;height:44px;flex-shrink:0;">
                    <i class="fa-solid fa-rotate-left text-white"></i>
                </div>
                <div>
                    <div class="fw-bold">Restore Data</div>
                    <small class="text-muted">Kembalikan database ke kondisi saat backup dibuat. Safety backup otomatis dibuat sebelum restore.</small>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card border-0 bg-success bg-opacity-10 h-100">
            <div class="card-body d-flex align-items-start gap-3">
                <div class="rounded-circle bg-success d-flex align-items-center justify-content-center" style="width:44px;height:44px;flex-shrink:0;">
                    <i class="fa-solid fa-shield-halved text-white"></i>
                </div>
                <div>
                    <div class="fw-bold">Aman & Terproteksi</div>
                    <small class="text-muted">File backup tidak bisa diakses langsung via browser. Hanya admin yang bisa mengunduhnya.</small>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Backup List Card -->
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0"><i class="fa-solid fa-list me-2"></i>Daftar Backup Tersedia</h5>
        <span class="badge bg-secondary"><?= count($backups) ?> File</span>
    </div>
    <div class="card-body p-0">

        <?php if (session()->getFlashdata('success')) : ?>
            <div class="alert alert-success alert-dismissible fade show m-3" role="alert">
                <i class="fa-solid fa-circle-check me-2"></i>
                <?= session()->getFlashdata('success') ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <?php if (session()->getFlashdata('error')) : ?>
            <div class="alert alert-danger alert-dismissible fade show m-3" role="alert">
                <i class="fa-solid fa-triangle-exclamation me-2"></i>
                <?= esc(session()->getFlashdata('error')) ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th style="width:5%">#</th>
                        <th>Nama File</th>
                        <th class="text-center">Ukuran</th>
                        <th class="text-center">Dibuat Pada</th>
                        <th class="text-center" style="width:220px">Aksi</th>
                    </tr>
                </thead>
                <tbody id="backupTableBody">
                    <?php if (empty($backups)) : ?>
                        <tr>
                            <td colspan="5" class="text-center text-muted py-5">
                                <i class="fa-solid fa-inbox fa-2x mb-3 d-block text-secondary"></i>
                                Belum ada file backup.<br>
                                <small>Klik "Buat Backup Sekarang" untuk membuat snapshot pertama.</small>
                            </td>
                        </tr>
                    <?php else : ?>
                        <?php foreach ($backups as $i => $b) : ?>
                            <tr id="row_<?= esc($b['filename']) ?>">
                                <td><?= $i + 1 ?></td>
                                <td>
                                    <code class="text-dark fw-bold"><?= esc($b['filename']) ?></code>
                                    <?php if ($i === 0) : ?>
                                        <span class="badge bg-success ms-1">Terbaru</span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-center">
                                    <span class="badge bg-light text-dark border">
                                        <i class="fa-solid fa-file-code me-1 text-muted"></i>
                                        <?= esc($b['size']) ?>
                                    </span>
                                </td>
                                <td class="text-center">
                                    <small><?= esc($b['created']) ?></small>
                                </td>
                                <td class="text-center">
                                    <a href="<?= site_url('backup/download/' . urlencode($b['filename'])) ?>"
                                       class="btn btn-outline-primary btn-sm" title="Download backup ini">
                                        <i class="fa-solid fa-download"></i>
                                    </a>

                                    <button type="button"
                                            class="btn btn-outline-info btn-sm"
                                            title="Restore dari backup ini"
                                            onclick="confirmRestore('<?= esc($b['filename']) ?>')">
                                        <i class="fa-solid fa-rotate-left"></i>
                                    </button>

                                    <button type="button"
                                            class="btn btn-outline-danger btn-sm"
                                            title="Hapus backup ini"
                                            onclick="confirmDelete('<?= esc($b['filename']) ?>')">
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

<div class="mt-3 p-3 bg-light rounded border">
    <small class="text-muted">
        <i class="fa-solid fa-circle-info me-1 text-info"></i>
        <strong>Catatan Restore:</strong> Restore akan <strong>menghapus dan menulis ulang semua data</strong> di database
        sesuai kondisi saat backup dibuat. Sebelum restore dijalankan, sistem akan otomatis membuat
        <em>safety backup</em> dari kondisi database terkini untuk berjaga-jaga.
    </small>
</div>

<?php $this->endSection(); ?>

<?php $this->section('scripts'); ?>
<script>
function confirmRestore(filename) {
    Swal.fire({
        icon: 'warning',
        title: 'Restore Database?',
        html: `
            <p>Anda akan me-restore database dari file:</p>
            <code class="d-block bg-light p-2 rounded mb-3">${escHtml(filename)}</code>
            <div class="alert alert-danger text-start small mb-0">
                <i class="fa-solid fa-triangle-exclamation me-1"></i>
                <strong>PERHATIAN:</strong> Semua data saat ini akan <strong>dihapus dan digantikan</strong>
                dengan data dari backup.<br><br>
                Safety backup otomatis akan dibuat sebelum restore dijalankan.
            </div>
        `,
        showCancelButton: true,
        confirmButtonText: '<i class="fa-solid fa-rotate-left me-1"></i> Ya, Restore Sekarang!',
        cancelButtonText: 'Batal',
        confirmButtonColor: '#17a2b8',
        cancelButtonColor: '#6c757d',
        focusCancel: true,
    }).then((result) => {
        if (result.isConfirmed) {
            Swal.fire({
                title: 'Konfirmasi Akhir',
                html: `<p class="mb-2">Ketik <strong>RESTORE</strong> untuk melanjutkan:</p>
                       <input id="confirmInput" type="text" class="form-control text-center fw-bold" placeholder="ketik RESTORE" autocomplete="off">`,
                showCancelButton: true,
                confirmButtonText: 'Lanjutkan Restore',
                cancelButtonText: 'Batal',
                confirmButtonColor: '#dc3545',
                preConfirm: () => {
                    const val = document.getElementById('confirmInput').value.trim();
                    if (val !== 'RESTORE') {
                        Swal.showValidationMessage('Ketik RESTORE (huruf kapital) untuk konfirmasi.');
                        return false;
                    }
                    return true;
                }
            }).then((r2) => {
                if (r2.isConfirmed) {
                    window.location.href = `<?= site_url('backup/restore') ?>/${encodeURIComponent(filename)}`;
                }
            });
        }
    });
}

function confirmDelete(filename) {
    Swal.fire({
        icon: 'warning',
        title: 'Hapus Backup?',
        html: `Anda akan menghapus file:<br><code>${escHtml(filename)}</code><br><small class="text-muted">File ini tidak bisa dipulihkan setelah dihapus.</small>`,
        showCancelButton: true,
        confirmButtonText: 'Ya, Hapus!',
        cancelButtonText: 'Batal',
        confirmButtonColor: '#dc3545',
    }).then((result) => {
        if (result.isConfirmed) {
            fetch(`<?= site_url('backup/delete') ?>/${encodeURIComponent(filename)}`, {
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            })
            .then(r => r.json())
            .then(res => {
                if (res.status === 'success') {
                    Swal.fire({
                        icon: 'success', title: 'Dihapus!', text: res.message,
                        timer: 1500, showConfirmButton: false
                    }).then(() => location.reload());
                } else {
                    Swal.fire({ icon: 'error', title: 'Gagal', text: res.message });
                }
            })
            .catch(() => {
                Swal.fire({ icon: 'error', title: 'Error', text: 'Terjadi kesalahan jaringan.' });
            });
        }
    });
}

function escHtml(str) {
    const d = document.createElement('div');
    d.textContent = str;
    return d.innerHTML;
}
</script>
<?php $this->endSection(); ?>