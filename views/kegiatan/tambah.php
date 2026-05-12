<?php
/**
 * Tambah Kegiatan View
 */
?>

<div class="card">
    <div class="card-header">
        <h3 class="card-title">Tambah Kegiatan</h3>
    </div>

    <form action="<?= nginx_url('kegiatan/proses_tambah') ?>" method="POST" enctype="multipart/form-data">
        <input type="hidden" name="csrf_token" value="<?= generateCSRFToken() ?>">

        <div class="card-body">
            <div class="form-group">
                <label for="judul">Judul <span class="text-danger">*</span></label>
                <input type="text" class="form-control" id="judul" name="judul" required>
            </div>

            <div class="row">
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="tanggal">Tanggal <span class="text-danger">*</span></label>
                        <input type="date" class="form-control" id="tanggal" name="tanggal" value="<?= date('Y-m-d') ?>" required>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="waktu">Waktu</label>
                        <input type="time" class="form-control" id="waktu" name="waktu">
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="status">Status</label>
                        <select class="form-control" id="status" name="status">
                            <option value="Akan Datang">Akan Datang</option>
                            <option value="Sedang Berlangsung">Sedang Berlangsung</option>
                            <option value="Selesai">Selesai</option>
                            <option value="Dibatalkan">Dibatalkan</option>
                        </select>
                    </div>
                </div>
            </div>

            <div class="form-group">
                <label for="lokasi">Lokasi</label>
                <input type="text" class="form-control" id="lokasi" name="lokasi">
            </div>

            <div class="form-group">
                <label for="isi">Deskripsi</label>
                <textarea class="form-control" id="isi" name="isi" rows="3"></textarea>
            </div>

            <div class="form-group">
                <label for="banner">Banner (opsional)</label>
                <div class="input-group">
                    <div class="custom-file">
                        <input type="file" class="custom-file-input" name="banner" accept="image/*">
                        <label class="custom-file-label">Pilih file...</label>
                    </div>
                </div>
            </div>
        </div>

        <div class="card-footer">
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-save mr-1"></i> Simpan
            </button>
            <a href="<?= nginx_url('kegiatan') ?>" class="btn btn-secondary">
                <i class="fas fa-arrow-left mr-1"></i> Kembali
            </a>
        </div>
    </form>
</div>