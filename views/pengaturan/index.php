<?php
/**
 * Pengaturan View
 */
?>

<div class="row">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Pengaturan Aplikasi</h3>
            </div>
            <form action="<?= nginx_url('pengaturan/update') ?>" method="POST" enctype="multipart/form-data">
                <input type="hidden" name="csrf_token" value="<?= generateCSRFToken() ?>">
                <input type="hidden" name="logo_lama" value="<?= $pengaturan['logo'] ?>">

                <div class="card-body">
                    <div class="form-group">
                        <label for="nama_aplikasi">Nama Aplikasi</label>
                        <input type="text" class="form-control" name="nama_aplikasi" value="<?= $pengaturan['nama_aplikasi'] ?>">
                    </div>
                    <div class="form-group">
                        <label for="nama_rt">Nama RT</label>
                        <input type="text" class="form-control" name="nama_rt" value="<?= $pengaturan['nama_rt'] ?>">
                    </div>
                    <div class="form-group">
                        <label for="nama_rw">Nama RW</label>
                        <input type="text" class="form-control" name="nama_rw" value="<?= $pengaturan['nama_rw'] ?>">
                    </div>
                    <div class="form-group">
                        <label for="nama_ketua_rt">Nama Ketua RT</label>
                        <input type="text" class="form-control" name="nama_ketua_rt" value="<?= $pengaturan['nama_ketua_rt'] ?>">
                    </div>
                    <div class="form-group">
                        <label for="alamat">Alamat</label>
                        <textarea class="form-control" name="alamat" rows="2"><?= $pengaturan['alamat'] ?></textarea>
                    </div>
                    <div class="form-group">
                        <label for="no_telepon">No. Telepon</label>
                        <input type="text" class="form-control" name="no_telepon" value="<?= $pengaturan['no_telepon'] ?>">
                    </div>
                    <div class="form-group">
                        <label for="email">Email</label>
                        <input type="email" class="form-control" name="email" value="<?= $pengaturan['email'] ?>">
                    </div>
                    <div class="form-group">
                        <label for="logo">Logo</label>
                        <div class="input-group">
                            <div class="custom-file">
                                <input type="file" class="custom-file-input" name="logo" accept="image/*">
                                <label class="custom-file-label">Pilih file...</label>
                            </div>
                        </div>
                        <img src="<?= BASE_URL ?>assets/img/<?= $pengaturan['logo'] ?>" class="img-thumbnail mt-2" style="max-width: 100px;">
                    </div>
                </div>
                <div class="card-footer">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save mr-1"></i> Simpan
                    </button>
                </div>
            </form>
        </div>
    </div>

    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Manajemen User</h3>
            </div>
            <div class="card-body table-responsive">
                <table class="table table-bordered table-sm">
                    <thead>
                        <tr>
                            <th>User</th>
                            <th>Role</th>
                            <th>Status Akun</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($users as $u): ?>
                            <tr>
                                <td>
                                    <?= $u['nama'] ?><br>
                                    <small class="text-muted"><i class="fas fa-user text-xs"></i> <?= $u['username'] ?></small>
                                    <?php if ($u['role'] === 'warga'): ?><br><small class="text-info"><i class="fas fa-id-card text-xs"></i> NIK: <?= $u['nik'] ?? 'Belum ditautkan' ?></small><?php endif; ?>
                                </td>
                                <td><span class="badge badge-<?= $u['role'] === 'admin' ? 'danger' : ($u['role'] === 'ketua_rt' ? 'warning' : 'info') ?>"><?= ucfirst(str_replace('_', ' ', $u['role'])) ?></span></td>
                                <td><?= getStatusBadge($u['is_active'] ? 'Aktif' : 'Non-Aktif') ?></td>
                                <td>
                                    <a href="#" class="btn btn-warning btn-sm" onclick="editUser(<?= $u['id'] ?>, '<?= $u['nama'] ?>', '<?= $u['email'] ?>', '<?= $u['no_hp'] ?>', '<?= $u['role'] ?>', '<?= $u['nik'] ?? '' ?>')">
                                        <i class="fas fa-edit"></i> Edit
                                    </a>
                                    <?php if ($u['id'] != $_SESSION['user_id']): ?>
                                        <?php if ($u['is_active']): ?>
                                            <a href="<?= route_url('pengaturan/toggle_user', ['id' => $u['id']]) ?>" class="btn btn-secondary btn-sm" title="Non-Aktifkan">
                                                <i class="fas fa-ban"></i>
                                            </a>
                                        <?php else: ?>
                                            <a href="<?= route_url('pengaturan/toggle_user', ['id' => $u['id']]) ?>" class="btn btn-success btn-sm" title="Aktifkan">
                                                <i class="fas fa-check"></i>
                                            </a>
                                        <?php endif; ?>
                                        <a href="#" onclick="confirmDelete('<?= route_url('pengaturan/hapus_user', ['id' => $u['id']]) ?>', 'Yakin hapus user ini?')">
                                            <i class="fas fa-trash"></i>
                                        </a>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Tambah User</h3>
            </div>
            <form action="<?= nginx_url('pengaturan/tambah_user') ?>" method="POST">
                <input type="hidden" name="csrf_token" value="<?= generateCSRFToken() ?>">
                <div class="card-body">
                    <div class="form-group" id="nik_group">
                        <select class="form-control select2" name="nik">
                            <option value="">-- Pilih Warga (Wajib untuk role Warga) --</option>
                            <?php foreach ($listWarga as $w): ?>
                                <option value="<?= $w['nik'] ?>"><?= $w['nama_lengkap'] ?> - <?= $w['nik'] ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <input type="text" class="form-control" name="username" placeholder="Username" required>
                    </div>
                    <div class="form-group">
                        <input type="text" class="form-control" name="nama" placeholder="Nama Lengkap" required>
                    </div>
                    <div class="form-group">
                        <input type="email" class="form-control" name="email" placeholder="Email">
                    </div>
                    <div class="form-group">
                        <input type="text" class="form-control" name="no_hp" placeholder="No. HP">
                    </div>
                    <div class="form-group">
                        <input type="password" class="form-control" name="password" placeholder="Password" required>
                    </div>
                    <div class="form-group">
                        <select class="form-control" name="role" id="role_tambah">
                            <option value="warga">Warga</option>
                            <option value="ketua_rt">Ketua RT</option>
                            <option value="admin">Admin</option>
                        </select>
                    </div>
                </div>
                <div class="card-footer">
                    <button type="submit" class="btn btn-primary btn-sm">
                        <i class="fas fa-plus mr-1"></i> Tambah
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="card mt-4">
    <div class="card-header">
        <h3 class="card-title">Pengaturan Iuran</h3>
    </div>
    <div class="card-body table-responsive">
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Nama Iuran</th>
                    <th>Jenis</th>
                    <th>Jumlah</th>
                    <th>Status</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php $no = 1; foreach ($jenisIuran as $ji): ?>
                    <tr>
                        <td><?= $no++ ?></td>
                        <td><?= $ji['nama_iuran'] ?></td>
                        <td><span class="badge badge-info"><?= $ji['jenis'] ?></span></td>
                        <td><?= formatCurrency($ji['jumlah']) ?></td>
                        <td><span class="badge badge-<?= $ji['is_active'] ? 'success' : 'secondary' ?>"><?= $ji['is_active'] ? 'Aktif' : 'Non-Aktif' ?></span></td>
                        <td>
                            <a href="#" class="btn btn-warning btn-sm" onclick="editIuran(<?= $ji['id'] ?>, '<?= $ji['nama_iuran'] ?>', '<?= $ji['jenis'] ?>', <?= $ji['jumlah'] ?>, '<?= $ji['deskripsi'] ?>', <?= $ji['is_active'] ?>)">
                                <i class="fas fa-edit"></i> Edit
                            </a>
                            <a href="#" onclick="confirmDelete('<?= route_url('pengaturan/hapus_iuran', ['id' => $ji['id']]) ?>', 'Yakin hapus iuran ini?')">
                                <i class="fas fa-trash"></i>
                            </a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Modal Edit User -->
<div class="modal fade" id="editUserModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <form action="<?= nginx_url('pengaturan/edit_user') ?>" method="POST">
            <input type="hidden" name="csrf_token" value="<?= generateCSRFToken() ?>">
            <input type="hidden" name="id" id="edit_user_id">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Edit User</h5>
                    <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
                </div>
                <div class="modal-body">
                    <div class="form-group" id="edit_nik_group" style="display: none;">
                        <label for="edit_nik">Warga (NIK)</label>
                        <select class="form-control select2" id="edit_nik" name="nik">
                            <option value="">-- Pilih Warga --</option>
                            <?php foreach ($listWarga as $w): ?>
                                <option value="<?= $w['nik'] ?>"><?= $w['nama_lengkap'] ?> - <?= $w['nik'] ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="edit_nama">Nama Lengkap</label>
                        <input type="text" class="form-control" id="edit_nama" name="nama" required>
                    </div>
                    <div class="form-group">
                        <label for="edit_email">Email</label>
                        <input type="email" class="form-control" id="edit_email" name="email">
                    </div>
                    <div class="form-group">
                        <label for="edit_no_hp">No. HP</label>
                        <input type="text" class="form-control" id="edit_no_hp" name="no_hp">
                    </div>
                    <div class="form-group">
                        <label for="edit_role">Role</label>
                        <select class="form-control" id="edit_role" name="role">
                            <option value="warga">Warga</option>
                            <option value="ketua_rt">Ketua RT</option>
                            <option value="admin">Admin</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="edit_password">Password Baru (kosongkan jika tidak diubah)</label>
                        <input type="password" class="form-control" id="edit_password" name="password">
                        <small class="form-text text-muted">Isi hanya jika ingin mengganti password.</small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Modal Edit Iuran -->
<div class="modal fade" id="editIuranModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <form action="<?= nginx_url('pengaturan/edit_iuran') ?>" method="POST">
            <input type="hidden" name="csrf_token" value="<?= generateCSRFToken() ?>">
            <input type="hidden" name="id" id="edit_iuran_id">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Edit Jenis Iuran</h5>
                    <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label for="edit_nama_iuran">Nama Iuran</label>
                        <input type="text" class="form-control" id="edit_nama_iuran" name="nama_iuran" required>
                    </div>
                    <div class="form-group">
                        <label for="edit_jenis">Jenis</label>
                        <select class="form-control" id="edit_jenis" name="jenis">
                            <option value="Kebersihan">Kebersihan</option>
                            <option value="Keamanan">Keamanan</option>
                            <option value="Kas RT">Kas RT</option>
                            <option value="Dana Sosial">Dana Sosial</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="edit_jumlah">Jumlah</label>
                        <input type="number" class="form-control" id="edit_jumlah" name="jumlah" step="0.01">
                    </div>
                    <div class="form-group">
                        <label for="edit_deskripsi">Deskripsi</label>
                        <textarea class="form-control" id="edit_deskripsi" name="deskripsi" rows="3"></textarea>
                    </div>
                    <div class="form-group">
                        <div class="custom-control custom-switch">
                            <input type="checkbox" class="custom-control-input" id="edit_is_active" name="is_active" value="1">
                            <label class="custom-control-label" for="edit_is_active">Aktif</label>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                </div>
            </div>
        </form>
    </div>
</div>

<?php $extraScripts = '
<script>
function editUser(id, nama, email, no_hp, role, nik) {
    $("#edit_user_id").val(id);
    $("#edit_nama").val(nama);
    $("#edit_email").val(email);
    $("#edit_no_hp").val(no_hp);
    $("#edit_role").val(role);
    $("#edit_nik").val(nik);
    $("#edit_password").val(""); // Clear password field for security
    
    if (role === "warga") {
        $("#edit_nik_group").show();
    } else {
        $("#edit_nik_group").hide();
    }
    
    $("#editUserModal").modal("show");
}

function editIuran(id, nama_iuran, jenis, jumlah, deskripsi, is_active) {
    $("#edit_iuran_id").val(id);
    $("#edit_nama_iuran").val(nama_iuran);
    $("#edit_jenis").val(jenis);
    $("#edit_jumlah").val(jumlah);
    $("#edit_deskripsi").val(deskripsi);
    $("#edit_is_active").prop("checked", is_active == 1);
    $("#editIuranModal").modal("show");
}

$(function() {
    // Toggle form NIK saat role diubah (Form Tambah)
    $("#role_tambah").on("change", function() {
        if ($(this).val() === "warga") {
            $("#nik_group").slideDown();
        } else {
            $("#nik_group").slideUp();
        }
    });
    
    // Toggle form NIK saat role diubah (Form Edit)
    $("#edit_role").on("change", function() {
        if ($(this).val() === "warga") {
            $("#edit_nik_group").slideDown();
        } else {
            $("#edit_nik_group").slideUp();
        }
    });
});
</script>
'; ?>