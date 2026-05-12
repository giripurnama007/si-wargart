<?php
/**
 * Edit Warga View
 * SI-WargaRT - Sistem Informasi Warga RT
 */
?>

<div class="card">
    <div class="card-header">
        <h3 class="card-title">Edit Data Warga</h3>
    </div>

    <form action="<?= nginx_url('warga/proses_edit') ?>" method="POST" enctype="multipart/form-data">
        <input type="hidden" name="csrf_token" value="<?= generateCSRFToken() ?>">
        <input type="hidden" name="id" value="<?= $warga['id'] ?>">
        <input type="hidden" name="foto_lama" value="<?= $warga['foto'] ?>">

        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="nik">NIK <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="nik" name="nik" value="<?= $warga['nik'] ?>" maxlength="20" required>
                    </div>
                    <div class="form-group">
                        <label for="no_kk">No. KK <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="no_kk" name="no_kk" value="<?= $warga['no_kk'] ?>" maxlength="20" required>
                    </div>
                    <div class="form-group">
                        <label for="nama_lengkap">Nama Lengkap <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="nama_lengkap" name="nama_lengkap" value="<?= $warga['nama_lengkap'] ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="tempat_lahir">Tempat Lahir</label>
                        <input type="text" class="form-control" id="tempat_lahir" name="tempat_lahir" value="<?= $warga['tempat_lahir'] ?>">
                    </div>
                    <div class="form-group">
                        <label for="tanggal_lahir">Tanggal Lahir</label>
                        <input type="date" class="form-control" id="tanggal_lahir" name="tanggal_lahir" value="<?= $warga['tanggal_lahir'] ?>">
                    </div>
                    <div class="form-group">
                        <label for="jenis_kelamin">Jenis Kelamin <span class="text-danger">*</span></label>
                        <select class="form-control" id="jenis_kelamin" name="jenis_kelamin" required>
                            <option value="L" <?= $warga['jenis_kelamin'] === 'L' ? 'selected' : '' ?>>Laki-laki</option>
                            <option value="P" <?= $warga['jenis_kelamin'] === 'P' ? 'selected' : '' ?>>Perempuan</option>
                        </select>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="form-group">
                        <label for="agama">Agama</label>
                        <select class="form-control" id="agama" name="agama">
                            <option value="Islam" <?= $warga['agama'] === 'Islam' ? 'selected' : '' ?>>Islam</option>
                            <option value="Kristen" <?= $warga['agama'] === 'Kristen' ? 'selected' : '' ?>>Kristen</option>
                            <option value="Katolik" <?= $warga['agama'] === 'Katolik' ? 'selected' : '' ?>>Katolik</option>
                            <option value="Hindu" <?= $warga['agama'] === 'Hindu' ? 'selected' : '' ?>>Hindu</option>
                            <option value="Buddha" <?= $warga['agama'] === 'Buddha' ? 'selected' : '' ?>>Buddha</option>
                            <option value="Konghucu" <?= $warga['agama'] === 'Konghucu' ? 'selected' : '' ?>>Konghucu</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="status_perkawinan">Status Perkawinan</label>
                        <select class="form-control" id="status_perkawinan" name="status_perkawinan">
                            <option value="Belum Kawin" <?= $warga['status_perkawinan'] === 'Belum Kawin' ? 'selected' : '' ?>>Belum Kawin</option>
                            <option value="Kawin" <?= $warga['status_perkawinan'] === 'Kawin' ? 'selected' : '' ?>>Kawin</option>
                            <option value="Cerai Hidup" <?= $warga['status_perkawinan'] === 'Cerai Hidup' ? 'selected' : '' ?>>Cerai Hidup</option>
                            <option value="Cerai Mati" <?= $warga['status_perkawinan'] === 'Cerai Mati' ? 'selected' : '' ?>>Cerai Mati</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="pekerjaan">Pekerjaan</label>
                        <input type="text" class="form-control" id="pekerjaan" name="pekerjaan" value="<?= $warga['pekerjaan'] ?>">
                    </div>
                    <div class="form-group">
                        <label for="no_hp">No. HP</label>
                        <input type="text" class="form-control" id="no_hp" name="no_hp" value="<?= $warga['no_hp'] ?>" maxlength="20">
                    </div>
                    <div class="form-group">
                        <label for="email">Email</label>
                        <input type="email" class="form-control" id="email" name="email" value="<?= $warga['email'] ?>">
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-12">
                    <div class="form-group">
                        <label for="alamat">Alamat</label>
                        <textarea class="form-control" id="alamat" name="alamat" rows="2"><?= $warga['alamat'] ?></textarea>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="rt">RT</label>
                        <input type="text" class="form-control" id="rt" name="rt" value="<?= $warga['rt'] ?>" maxlength="5">
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="rw">RW</label>
                        <input type="text" class="form-control" id="rw" name="rw" value="<?= $warga['rw'] ?>" maxlength="5">
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="status_warga">Status Warga</label>
                        <select class="form-control" id="status_warga" name="status_warga">
                            <option value="Aktif" <?= $warga['status_warga'] === 'Aktif' ? 'selected' : '' ?>>Aktif</option>
                            <option value="Non-Aktif" <?= $warga['status_warga'] === 'Non-Aktif' ? 'selected' : '' ?>>Non-Aktif</option>
                            <option value="Mutasi" <?= $warga['status_warga'] === 'Mutasi' ? 'selected' : '' ?>>Mutasi</option>
                            <option value="Meninggal" <?= $warga['status_warga'] === 'Meninggal' ? 'selected' : '' ?>>Meninggal</option>
                        </select>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="foto">Foto</label>
                        <div class="input-group">
                            <div class="custom-file">
                                <input type="file" class="custom-file-input" id="foto" name="foto" accept="image/*">
                                <label class="custom-file-label" for="foto">Pilih file...</label>
                            </div>
                        </div>
                        <small class="text-muted">Format: JPG, PNG. Maks 2MB</small>
                        <br>
                        <img src="<?= getFotoWarga($warga['foto']) ?>" class="img-thumbnail mt-2" style="max-width: 150px; max-height: 150px; object-fit: cover;">
                    </div>
                </div>
            </div>
        </div>

        <div class="card-footer">
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-save mr-1"></i> Update
            </button>
            <a href="<?= nginx_url('warga') ?>" class="btn btn-secondary">
                <i class="fas fa-times mr-1"></i> Batal
            </a>
        </div>
    </form>
</div>