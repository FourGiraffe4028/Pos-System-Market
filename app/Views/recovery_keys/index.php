<?php $this->extend('layout/main'); ?>
<?php $this->section('content'); ?>

<div class="d-flex justify-content-between align-items-center mb-3">
    <div>
        <h4 class="fw-bold mb-0"><i class="fa-solid fa-key me-2 text-warning"></i>Kelola Recovery Key</h4>
        <small class="text-muted">Manajemen Kunci Pemulihan Darurat Sistem (Khusus Owner).</small>
    </div>
    <button type="button" class="btn btn-warning btn-sm fw-bold" onclick="showGenerateModal()">
        <i class="fa-solid fa-plus me-1"></i>Generate Key Baru
    </button>
</div>

<!-- Warning Info Card -->
<div class="card border-0 bg-warning bg-opacity-10 mb-4">
    <div class="card-body d-flex align-items-start gap-3">
        <div class="rounded-circle bg-warning d-flex align-items-center justify-content-center" style="width:44px;height:44px;flex-shrink:0;">
            <i class="fa-solid fa-shield-halved text-dark fs-5"></i>
        </div>
        <div>
            <div class="fw-bold text-dark">Informasi Penting Recovery Key</div>
            <small class="text-muted">
                Recovery Key digunakan untuk membuka akses darurat ke sistem jika akun Admin tidak dapat diakses. 
                Key yang dibuat bersifat <strong>One-Time Use</strong> (hanya bisa dipakai 1 kali) dan 
                <strong>Kode Rahasia Plain</strong> hanya akan ditampilkan <strong>sekali saat pertama kali di-generate</strong>.
            </small>
        </div>
    </div>
</div>

<!-- Table Card -->
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0"><i class="fa-solid fa-list me-2"></i>Daftar Recovery Keys</h5>
        <span class="badge bg-secondary"><?= count($keys) ?> Keys</span>
    </div>
    <div class="card-body p-0">

        <?php if (session()->getFlashdata('success')) : ?>
            <div class="alert alert-success alert-dismissible fade show m-3" role="alert">
                <i class="fa-solid fa-circle-check me-2"></i> <?= session()->getFlashdata('success') ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th style="width:5%">#</th>
                        <th>Label / Catatan</th>
                        <th class="text-center">Status</th>
                        <th class="text-center">Dibuat Pada</th>
                        <th class="text-center">Berlaku Sampai</th>
                        <th class="text-center">Digunakan Pada</th>
                        <th class="text-center" style="width:100px">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($keys)) : ?>
                        <tr>
                            <td colspan="7" class="text-center text-muted py-5">
                                <i class="fa-solid fa-key fa-2x mb-3 d-block text-secondary"></i>
                                Belum ada Recovery Key.<br>
                                <small>Klik "Generate Key Baru" untuk membuat kunci pemulihan pertama.</small>
                            </td>
                        </tr>
                    <?php else : ?>
                        <?php foreach ($keys as $i => $k) : ?>
                            <?php
                                $isExpired = strtotime($k['expires_at']) < time();
                                $isUsed    = (int) $k['is_used'] === 1;
                            ?>
                            <tr id="row_<?= esc($k['id']) ?>">
                                <td><?= $i + 1 ?></td>
                                <td>
                                    <strong class="text-dark"><?= esc($k['label']) ?></strong>
                                </td>
                                <td class="text-center">
                                    <?php if ($isUsed) : ?>
                                        <span class="badge bg-secondary"><i class="fa-solid fa-check me-1"></i>Sudah Digunakan</span>
                                    <?php elseif ($isExpired) : ?>
                                        <span class="badge bg-danger"><i class="fa-solid fa-clock me-1"></i>Kedaluwarsa</span>
                                    <?php else : ?>
                                        <span class="badge bg-success"><i class="fa-solid fa-shield-check me-1"></i>Aktif</span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-center">
                                    <small><?= date('d M Y H:i', strtotime($k['created_at'])) ?></small>
                                </td>
                                <td class="text-center">
                                    <small class="<?= $isExpired && ! $isUsed ? 'text-danger fw-bold' : '' ?>">
                                        <?= date('d M Y H:i', strtotime($k['expires_at'])) ?>
                                    </small>
                                </td>
                                <td class="text-center">
                                    <small class="text-muted">
                                        <?= $k['used_at'] ? date('d M Y H:i', strtotime($k['used_at'])) : '-' ?>
                                    </small>
                                </td>
                                <td class="text-center">
                                    <button type="button" class="btn btn-outline-danger btn-sm" onclick="confirmDelete(<?= $k['id'] ?>, '<?= esc($k['label']) ?>')" title="Hapus Key">
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

<!-- Modal Form Generate Key -->
<div class="modal fade" id="modalGenerate" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="formGenerateKey">
                <?= csrf_field() ?>
                <div class="modal-header">
                    <h5 class="modal-title fw-bold"><i class="fa-solid fa-key me-2 text-warning"></i>Generate Recovery Key Baru</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Label / Catatan Kunci <span class="text-danger">*</span></label>
                        <input type="text" name="label" id="keyLabel" class="form-control" placeholder="Contoh: Kunci Cadangan Pak Reihan" required>
                        <small class="text-muted">Berikan nama agar mudah mengidentifikasi pemilik/peruntukan kunci.</small>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Masa Berlaku (Hari) <span class="text-danger">*</span></label>
                        <input type="number" name="days" id="keyDays" class="form-control" value="30" min="1" max="365" required>
                        <small class="text-muted">Berapa hari kunci ini akan valid (Default: 30 hari).</small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-warning fw-bold" id="btnSubmitGenerate">Generate Kunci</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Show Plain Key Result -->
<div class="modal fade" id="modalResultKey" tabindex="-1" data-bs-backdrop="static" data-bs-keyboard="false" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-warning">
            <div class="modal-header bg-warning text-dark">
                <h5 class="modal-title fw-bold"><i class="fa-solid fa-triangle-exclamation me-2"></i>SIMPAN KODE DARURAT INI!</h5>
            </div>
            <div class="modal-body text-center py-4">
                <p class="text-muted mb-2">Kode Recovery Key Plain hanya ditampilkan <strong>SATU KALI SAJA</strong>. Salin dan simpan di tempat yang aman:</p>
                
                <div class="bg-light p-3 rounded border border-warning my-3 position-relative">
                    <code class="fs-4 fw-bold text-danger d-block text-break" id="resultPlainKey">--------------------------------</code>
                </div>

                <div class="mb-3 text-start small text-muted">
                    <div><strong>Label:</strong> <span id="resultLabel">-</span></div>
                    <div><strong>Berlaku Sampai:</strong> <span id="resultExpires">-</span></div>
                </div>

                <div class="alert alert-danger mb-0 small text-start">
                    <i class="fa-solid fa-circle-exclamation me-1"></i>
                    <strong>Peringatan:</strong> Setelah modal ini ditutup, kode di atas tidak dapat dilihat lagi!
                </div>
            </div>
            <div class="modal-footer justify-content-between">
                <button type="button" class="btn btn-outline-secondary" onclick="copyKeyToClipboard()">
                    <i class="fa-solid fa-copy me-1"></i>Salin Kode
                </button>
                <button type="button" class="btn btn-warning fw-bold" data-bs-dismiss="modal" onclick="location.reload()">
                    Saya Sudah Menyimpannya
                </button>
            </div>
        </div>
    </div>
</div>

<?php $this->endSection(); ?>

<?php $this->section('scripts'); ?>
<script>
const modalGenerate = new bootstrap.Modal(document.getElementById('modalGenerate'));
const modalResultKey = new bootstrap.Modal(document.getElementById('modalResultKey'));

function showGenerateModal() {
    document.getElementById('formGenerateKey').reset();
    document.getElementById('keyDays').value = 30;
    modalGenerate.show();
}

document.getElementById('formGenerateKey').addEventListener('submit', function(e) {
    e.preventDefault();

    const btn = document.getElementById('btnSubmitGenerate');
    btn.disabled = true;
    btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin me-1"></i>Memproses...';

    const formData = new FormData(this);

    fetch('<?= site_url('recovery-keys/generate') ?>', {
        method: 'POST',
        body: formData,
        headers: { 'X-Requested-With': 'XMLHttpRequest' }
    })
    .then(r => r.json())
    .then(res => {
        btn.disabled = false;
        btn.innerHTML = 'Generate Kunci';

        if (res.status === 'success') {
            modalGenerate.hide();
            
            document.getElementById('resultPlainKey').innerText = res.plain_key;
            document.getElementById('resultLabel').innerText = res.label;
            document.getElementById('resultExpires').innerText = res.expires_at;

            modalResultKey.show();
        } else {
            Swal.fire({ icon: 'error', title: 'Gagal', text: res.message || 'Gagal generate key.' });
        }
    })
    .catch(() => {
        btn.disabled = false;
        btn.innerHTML = 'Generate Kunci';
        Swal.fire({ icon: 'error', title: 'Error', text: 'Terjadi kesalahan sistem.' });
    });
});

function copyKeyToClipboard() {
    const keyText = document.getElementById('resultPlainKey').innerText;
    navigator.clipboard.writeText(keyText).then(() => {
        Swal.fire({ icon: 'success', title: 'Tersalin!', text: 'Kode Recovery Key telah disalin ke clipboard.', timer: 1500, showConfirmButton: false });
    });
}

function confirmDelete(id, label) {
    Swal.fire({
        icon: 'warning',
        title: 'Hapus Recovery Key?',
        html: `Apakah Anda yakin ingin menghapus key <strong>${escHtml(label)}</strong>?`,
        showCancelButton: true,
        confirmButtonText: 'Ya, Hapus!',
        cancelButtonText: 'Batal',
        confirmButtonColor: '#dc3545',
    }).then(r => {
        if (r.isConfirmed) {
            fetch(`<?= site_url('recovery-keys/delete') ?>/${id}`, {
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            })
            .then(r => r.json())
            .then(res => {
                if (res.status === 'success') {
                    Swal.fire({ icon: 'success', title: 'Dihapus!', text: res.message, timer: 1500, showConfirmButton: false })
                        .then(() => location.reload());
                } else {
                    Swal.fire({ icon: 'error', title: 'Gagal', text: res.message });
                }
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