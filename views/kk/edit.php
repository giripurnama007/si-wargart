<?php
/**
 * Edit KK View
 */
?>

<div class="card">
    <div class="card-header">
        <h3 class="card-title">Edit Kartu Keluarga</h3>
    </div>

    <form action="<?= nginx_url('kk/proses_edit') ?>" method="POST" enctype="multipart/form-data">
        <input type="hidden" name="csrf_token" value="<?= generateCSRFToken() ?>">
        <input type="hidden" name="id" value="<?= $kk['id'] ?>">
        <input type="hidden" name="foto_kk_lama" value="<?= $kk['foto_kk'] ?>">

        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="no_kk">No. KK <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="no_kk" name="no_kk" value="<?= $kk['no_kk'] ?>" maxlength="20" required>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="nik_kepala">NIK Kepala Keluarga</label>
                        <input type="text" class="form-control" id="nik_kepala" name="nik_kepala" value="<?= $kk['nik_kepala'] ?>" maxlength="20">
                    </div>
                </div>
            </div>

            <div class="form-group">
                <label for="nama_kepala">Nama Kepala Keluarga <span class="text-danger">*</span></label>
                <input type="text" class="form-control" id="nama_kepala" name="nama_kepala" value="<?= $kk['nama_kepala'] ?>" required>
            </div>

            <div class="form-group">
                <label for="alamat">Alamat</label>
                <textarea class="form-control" id="alamat" name="alamat" rows="2"><?= $kk['alamat'] ?></textarea>
            </div>

            <div class="row">
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="rt">RT</label>
                        <input type="text" class="form-control" id="rt" name="rt" value="<?= $kk['rt'] ?>" maxlength="5">
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="rw">RW</label>
                        <input type="text" class="form-control" id="rw" name="rw" value="<?= $kk['rw'] ?>" maxlength="5">
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="kode_pos">Kode Pos</label>
                        <input type="text" class="form-control" id="kode_pos" name="kode_pos" value="<?= $kk['kode_pos'] ?>" maxlength="10">
                    </div>
                </div>
            </div>

            <div class="form-group">
                <label for="foto_kk">Foto KK</label>
                <div class="input-group">
                    <div class="custom-file">
                        <input type="file" class="custom-file-input" name="foto_kk" accept="image/*,.pdf">
                        <label class="custom-file-label">Pilih file...</label>
                    </div>
                </div>
                <?php if ($kk['foto_kk']): ?>
                    <img src="<?= BASE_URL ?>uploads/warga/<?= $kk['foto_kk'] ?>" class="img-thumbnail mt-2" style="max-width: 200px;">
                <?php endif; ?>
            </div>
        </div>

        <div class="card-footer">
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-save mr-1"></i> Update
            </button>
            <a href="<?= nginx_url('kk') ?>" class="btn btn-secondary">
                <i class="fas fa-arrow-left mr-1"></i> Kembali
            </a>
        </div>
    </form>
</div>

<div class="card mt-4">
    <div class="card-header">
        <h3 class="card-title">Anggota Keluarga</h3>
    </div>
    <div class="card-body table-responsive">
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>No</th>
                    <th>NIK</th>
                    <th>Nama</th>
                    <th>Jenis Kelamin</th>
                    <th>Tanggal Lahir</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($anggota)): ?>
                    <tr><td colspan="6" class="text-center text-muted">Tidak ada anggota</td></tr>
                <?php else: ?>
                    <?php $no = 1; foreach ($anggota as $a): ?>
                        <tr>
                            <td><?= $no++ ?></td>
                            <td><?= $a['nik'] ?></td>
                            <td><?= $a['nama_lengkap'] ?></td>
                            <td><?= $a['jenis_kelamin'] ?></td>
                            <td><?= formatTanggal($a['tanggal_lahir']) ?></td>
                            <td><?= getStatusBadge($a['status_warga']) ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>