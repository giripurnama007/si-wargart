<?php
/**
 * Tambah Pengumuman View
 */
?>

<div class="card">
    <div class="card-header">
        <h3 class="card-title">Tambah Pengumuman</h3>
    </div>

    <form action="<?= nginx_url('pengumuman/proses_tambah') ?>" method="POST" enctype="multipart/form-data">
        <input type="hidden" name="csrf_token" value="<?= generateCSRFToken() ?>">

        <div class="card-body">
            <div class="form-group">
                <label for="judul">Judul <span class="text-danger">*</span></label>
                <input type="text" class="form-control" id="judul" name="judul" required>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="tanggal">Tanggal</label>
                        <input type="date" class="form-control" id="tanggal" name="tanggal" value="<?= date('Y-m-d') ?>">
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="status">Status</label>
                        <select class="form-control" id="status" name="status">
                            <option value="Published">Published</option>
                            <option value="Draft">Draft</option>
                        </select>
                    </div>
                </div>
            </div>

            <div class="form-group">
                <label for="isi">Isi Pengumuman <span class="text-danger">*</span></label>
                <textarea class="form-control" id="isi" name="isi" rows="5" required></textarea>
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
            <a href="<?= nginx_url('pengumuman') ?>" class="btn btn-secondary">
                <i class="fas fa-arrow-left mr-1"></i> Kembali
            </a>
        </div>
    </form>
</div>