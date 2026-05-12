<?php
/**
 * Tambah KK View
 */
?>

<div class="card">
    <div class="card-header">
        <h3 class="card-title">Tambah Kartu Keluarga</h3>
    </div>

    <form action="<?= nginx_url('kk/proses_tambah') ?>" method="POST" enctype="multipart/form-data">
        <input type="hidden" name="csrf_token" value="<?= generateCSRFToken() ?>">

        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="no_kk">No. KK <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="no_kk" name="no_kk" maxlength="20" required>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="nik_kepala">NIK Kepala Keluarga</label>
                        <input type="text" class="form-control" id="nik_kepala" name="nik_kepala" maxlength="20">
                    </div>
                </div>
            </div>

            <div class="form-group">
                <label for="nama_kepala">Nama Kepala Keluarga <span class="text-danger">*</span></label>
                <input type="text" class="form-control" id="nama_kepala" name="nama_kepala" required>
            </div>

            <div class="form-group">
                <label for="alamat">Alamat</label>
                <textarea class="form-control" id="alamat" name="alamat" rows="2"></textarea>
            </div>

            <div class="row">
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="rt">RT</label>
                        <input type="text" class="form-control" id="rt" name="rt" value="01" maxlength="5">
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="rw">RW</label>
                        <input type="text" class="form-control" id="rw" name="rw" value="01" maxlength="5">
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="kode_pos">Kode Pos</label>
                        <input type="text" class="form-control" id="kode_pos" name="kode_pos" maxlength="10">
                    </div>
                </div>
            </div>

            <div class="form-group">
                <label for="foto_kk">Foto KK (opsional)</label>
                <div class="input-group">
                    <div class="custom-file">
                        <input type="file" class="custom-file-input" name="foto_kk" accept="image/*,.pdf">
                        <label class="custom-file-label">Pilih file...</label>
                    </div>
                </div>
            </div>
        </div>

        <div class="card-footer">
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-save mr-1"></i> Simpan
            </button>
            <a href="<?= nginx_url('kk') ?>" class="btn btn-secondary">
                <i class="fas fa-arrow-left mr-1"></i> Kembali
            </a>
        </div>
    </form>
</div>