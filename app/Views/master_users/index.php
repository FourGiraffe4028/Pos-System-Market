<?php $this->extend('layout/main'); ?>
<?php $this->section('content'); ?>

<div class="d-flex justify-content-between align-items-center mb-3">
    <h4 class="fw-bold mb-0">Master Data Users</h4>
    <button type="button" class="btn btn-warning btn-sm fw-bold" onclick="showAddModal()">
        <i class="fa-solid fa-user-plus me-1"></i>Tambah User Baru
    </button>
</div>

<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">Daftar Pengguna Aplikasi</h5>
        <span class="badge bg-secondary"><?= count($users) ?> Users</span>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0" id="tabelUsers">
                <thead class="table-light">
                    <tr>
                        <th style="width: 5%;">#</th>
                        <th>User ID</th>
                        <th>Username</th>
                        <th>Nama Pengguna</th>
                        <th class="text-center">Role</th>
                        <th>Mitra Supplier</th>
                        <th class="text-center">Status</th>
                        <th class="text-center" style="width: 15%;">Aksi</th>
                    </tr>
                </thead>
                <tbody id="usersTableBody">
                    <?php if (empty($users)) : ?>
                        <tr id="emptyRow">
                            <td colspan="8" class="text-center text-muted py-4">Belum ada data user.</td>
                        </tr>
                    <?php else : ?>
                        <?php foreach ($users as $i => $u) : ?>
                            <tr id="row_<?= esc($u['user_id']) ?>">
                                <td><?= $i + 1 ?></td>
                                <td><code><?= esc($u['user_id']) ?></code></td>
                                <td><strong><?= esc($u['username']) ?></strong></td>
                                <td><?= esc($u['nama_user']) ?></td>
                                <td class="text-center">
                                    <?php
                                        $badgeColor = match ($u['role']) {
                                            'admin'      => 'bg-danger',
                                            'supervisor' => 'bg-warning text-dark',
                                            'owner'      => 'bg-purple text-white',
                                            'inventory'  => 'bg-info text-dark',
                                            default      => 'bg-secondary',
                                        };
                                    ?>
                                    <span class="badge <?= $badgeColor ?> text-uppercase"><?= esc($u['role']) ?></span>
                                </td>
                                <td>
                                    <?php if ($u['role'] === 'inventory' && ! empty($u['nama_supplier'])) : ?>
                                        <small class="fw-bold text-dark"><i class="fa-solid fa-truck-field me-1 text-warning"></i><?= esc($u['nama_supplier']) ?></small>
                                    <?php else : ?>
                                        <span class="text-muted small">-</span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-center">
                                    <?php if ($u['aktif'] == 1) : ?>
                                        <span class="badge bg-success">Aktif</span>
                                    <?php else : ?>
                                        <span class="badge bg-secondary">Nonaktif</span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-center">
                                    <button type="button" class="btn btn-outline-warning btn-sm" onclick="showEditModal('<?= esc($u['user_id']) ?>')" title="Edit User">
                                        <i class="fa-solid fa-pen-to-square"></i>
                                    </button>
                                    <?php if ($u['user_id'] !== session()->get('user_id')) : ?>
                                        <button type="button" class="btn btn-outline-danger btn-sm" onclick="confirmDelete('<?= esc($u['user_id']) ?>', '<?= esc($u['nama_user']) ?>')" title="Hapus User">
                                            <i class="fa-solid fa-trash"></i>
                                        </button>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal Add / Edit User -->
<div class="modal fade" id="modalUser" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="formUser">
                <?= csrf_field() ?>
                <div class="modal-header">
                    <h5 class="modal-title fw-bold" id="modalUserTitle"><i class="fa-solid fa-user-plus me-2"></i>Tambah User Baru</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">User ID</label>
                        <input type="text" name="user_id" id="userId" class="form-control" value="<?= esc($nextId) ?>" readonly style="background-color: #e9ecef;">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Username <span class="text-danger">*</span></label>
                        <input type="text" name="username" id="username" class="form-control" placeholder="Contoh: kasir02" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Nama Lengkap <span class="text-danger">*</span></label>
                        <input type="text" name="nama_user" id="namaUser" class="form-control" placeholder="Nama pengguna..." required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Password <span id="passHelpText" class="text-danger">*</span></label>
                        <input type="password" name="password" id="password" class="form-control" placeholder="Ketik password...">
                        <small class="text-muted id-edit-note" style="display:none; font-size: 11px;">Kosongkan password jika tidak ingin mengubahnya.</small>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Role Akses <span class="text-danger">*</span></label>
                        <select name="role" id="roleSelect" class="form-select" required onchange="toggleSupplierField()">
                            <option value="kasir">Kasir</option>
                            <option value="admin">Admin</option>
                            <option value="supervisor">Supervisor</option>
                            <option value="owner">Owner</option>
                            <option value="inventory">Inventory / Staff Warehouse</option>
                        </select>
                    </div>
                    <div class="mb-3" id="wrapperSupplier" style="display: none;">
                        <label class="form-label">Mitra Supplier (Khusus Staff Warehouse) <span class="text-danger">*</span></label>
                        <select name="id_supplier" id="idSupplier" class="form-select">
                            <option value="">-- Pilih Supplier --</option>
                            <?php foreach ($suppliers as $s) : ?>
                                <option value="<?= esc($s['id_supplier']) ?>"><?= esc($s['nama_supplier']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="mb-3" id="wrapperStatus" style="display: none;">
                        <label class="form-label">Status Akun</label>
                        <select name="aktif" id="aktifSelect" class="form-select">
                            <option value="1">Aktif</option>
                            <option value="0">Nonaktif</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" id="btnSubmitUser" class="btn btn-warning fw-bold">Simpan User</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php $this->endSection(); ?>

<?php $this->section('scripts'); ?>
<script>
let isEditMode = false;
let currentEditId = '';
const modalUser = new bootstrap.Modal(document.getElementById('modalUser'));

function toggleSupplierField() {
    const role = document.getElementById('roleSelect').value;
    const wrapper = document.getElementById('wrapperSupplier');
    wrapper.style.display = (role === 'inventory') ? '' : 'none';
}

function showAddModal() {
    isEditMode = false;
    currentEditId = '';
    document.getElementById('formUser').reset();
    document.getElementById('modalUserTitle').innerHTML = '<i class="fa-solid fa-user-plus me-2"></i>Tambah User Baru';
    document.getElementById('userId').value = '<?= esc($nextId) ?>';
    document.getElementById('password').required = true;
    document.getElementById('passHelpText').style.display = '';
    document.querySelector('.id-edit-note').style.display = 'none';
    document.getElementById('wrapperStatus').style.display = 'none';
    toggleSupplierField();
    modalUser.show();
}

function showEditModal(id) {
    isEditMode = true;
    currentEditId = id;
    document.getElementById('formUser').reset();
    document.getElementById('modalUserTitle').innerHTML = '<i class="fa-solid fa-user-pen me-2"></i>Edit Data User';
    document.getElementById('password').required = false;
    document.getElementById('passHelpText').style.display = 'none';
    document.querySelector('.id-edit-note').style.display = '';
    document.getElementById('wrapperStatus').style.display = '';

    fetch(`<?= site_url('master-users/get-json') ?>/${id}`)
        .then(r => r.json())
        .then(res => {
            if (res.status === 'success') {
                const data = res.data;
                document.getElementById('userId').value = data.user_id;
                document.getElementById('username').value = data.username;
                document.getElementById('namaUser').value = data.nama_user;
                document.getElementById('roleSelect').value = data.role;
                document.getElementById('aktifSelect').value = data.aktif;

                toggleSupplierField();
                if (data.role === 'inventory') {
                    document.getElementById('idSupplier').value = data.id_supplier || '';
                }

                modalUser.show();
            } else {
                Swal.fire({ icon: 'error', title: 'Error', text: res.message });
            }
        });
}

document.getElementById('formUser').addEventListener('submit', function(e) {
    e.preventDefault();

    const url = isEditMode
        ? `<?= site_url('master-users/update') ?>/${currentEditId}`
        : `<?= site_url('master-users/store') ?>`;

    const formData = new FormData(this);

    fetch(url, {
        method: 'POST',
        body: formData,
        headers: { 'X-Requested-With': 'XMLHttpRequest' }
    })
    .then(r => r.json())
    .then(res => {
        if (res.status === 'success') {
            modalUser.hide();
            Swal.fire({ icon: 'success', title: 'Berhasil', text: res.message, timer: 1500, showConfirmButton: false })
                .then(() => location.reload());
        } else {
            let errorMsg = res.message || 'Gagal menyimpan data user.';
            if (res.errors) {
                errorMsg = Object.values(res.errors).join('<br>');
            }
            Swal.fire({ icon: 'error', title: 'Validasi Gagal', html: errorMsg });
        }
    });
});

function confirmDelete(id, nama) {
    Swal.fire({
        title: 'Hapus User?',
        html: `Apakah Anda yakin ingin menghapus user <strong>${escHtml(nama)}</strong>?`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Ya, Hapus!',
        cancelButtonText: 'Batal',
        confirmButtonColor: '#e74a3b',
    }).then(r => {
        if (r.isConfirmed) {
            fetch(`<?= site_url('master-users/delete') ?>/${id}`, {
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            })
            .then(r => r.json())
            .then(res => {
                if (res.status === 'success') {
                    Swal.fire({ icon: 'success', title: 'Berhasil', text: res.message, timer: 1500, showConfirmButton: false })
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
<style>
    .bg-purple { background-color: #6f42c1; }
</style>
<?php $this->endSection(); ?>

