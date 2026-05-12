<?php
/**
 * Profil User View
 */
?>

<div class="card">
    <div class="card-header">
        <h3 class="card-title">Profil Saya</h3>
    </div>
    <form action="<?= nginx_url('auth/proses_profile') ?>" method="POST" enctype="multipart/form-data">
        <input type="hidden" name="csrf_token" value="<?= generateCSRFToken() ?>">
        <div class="card-body">
            <div class="row">
                <div class="col-md-12 text-center mb-4">
                    <img src="<?= getFotoWarga($user['foto']) ?>" id="preview_foto" class="profile-user-img img-fluid img-circle" style="width: 100px; height: 100px; object-fit: cover;" alt="User profile picture">
                    <div class="mt-2">
                        <label for="foto" class="btn btn-sm btn-outline-primary" style="cursor: pointer;">
                            <i class="fas fa-camera mr-1"></i> Ganti Foto
                        </label>
                        <input type="file" id="foto" name="foto" class="d-none" accept="image/*" onchange="document.getElementById('preview_foto').src = window.URL.createObjectURL(this.files[0])">
                    </div>
                    <small class="text-muted d-block">Format: JPG, PNG. Maks 2MB</small>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label>Username</label>
                        <input type="text" class="form-control" value="<?= htmlspecialchars($user['username']) ?>" disabled>
                    </div>
                    <?php if ($user['role'] === 'warga'): ?>
                        <div class="form-group">
                            <label>NIK</label>
                            <input type="text" class="form-control <?= empty($user['nik']) ? 'is-invalid' : '' ?>" value="<?= htmlspecialchars($user['nik'] ?? 'Belum terhubung dengan Data Warga') ?>" disabled>
                            <?php if (empty($user['nik'])): ?>
                                <span class="error invalid-feedback">Hubungi Admin/Ketua RT untuk menautkan akun dengan NIK Anda.</span>
                            <?php endif; ?>
                        </div>
                        <div class="form-group">
                            <label>No. KK</label>
                            <input type="text" class="form-control" value="<?= htmlspecialchars($user['no_kk'] ?? '-') ?>" disabled>
                        </div>
                    <?php endif; ?>
                    <div class="form-group">
                        <label for="nama">Nama Lengkap</label>
                        <input type="text" class="form-control" id="nama" name="nama" value="<?= htmlspecialchars($user['nama']) ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="email">Email</label>
                        <input type="email" class="form-control" id="email" name="email" value="<?= htmlspecialchars($user['email']) ?>">
                    </div>
                    <div class="form-group">
                        <label for="no_hp">No. HP</label>
                        <input type="text" class="form-control" id="no_hp" name="no_hp" value="<?= htmlspecialchars($user['no_hp']) ?>">
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="password_lama">Password Lama</label>
                        <input type="password" class="form-control" id="password_lama" name="password_lama" placeholder="Isi untuk ganti password">
                    </div>
                    <div class="form-group">
                        <label for="password_baru">Password Baru</label>
                        <input type="password" class="form-control" id="password_baru" name="password_baru" placeholder="Isi untuk ganti password">
                    </div>
                    <div class="form-group">
                        <label>Role</label>
                        <input type="text" class="form-control" value="<?= htmlspecialchars(ucfirst(str_replace('_', ' ', $user['role']))) ?>" disabled>
                    </div>
                    <div class="form-group">
                        <label>Status Akun</label>
                        <input type="text" class="form-control" value="<?= htmlspecialchars($user['is_active'] ? 'Aktif' : 'Non-Aktif') ?>" disabled>
                    </div>
                    <div class="form-group">
                        <label>Terakhir Login</label>
                        <input type="text" class="form-control" value="<?= $user['last_login'] ? formatTanggal($user['last_login'], 'd F Y H:i') : '-' ?>" disabled>
                    </div>
                </div>
            </div>
        </div>
        <div class="card-footer">
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-save mr-1"></i> Update Profil
            </button>
        </div>
    </form>
</div>

<?php $extraScripts = '
<script>
$(document).ready(function() {
    // Custom file input for photo upload
    bsCustomFileInput.init();

    // Example: If you want to show a success toast after update
    // You can check for flash messages in your main layout to handle this.
    // var flash = ' . json_encode(getFlash()) . '; // Assuming getFlash() can be called here or passed from controller
    // if (flash && flash.type === "success") {
    //     showToast("success", flash.message);
    // }

    // Password confirmation if needed in the future
    // $("#password_baru").on("keyup", function() {
    //    // Implement logic for password strength or confirmation
    // });
});
</script>
'; ?>