<?php
/**
 * Ajukan Pengaduan View
 */
?>

<div class="card">
    <div class="card-header">
        <h3 class="card-title">Ajukan Pengaduan</h3>
    </div>

    <form action="<?= nginx_url('pengaduan/proses_tambah') ?>" method="POST" enctype="multipart/form-data">
        <input type="hidden" name="csrf_token" value="<?= generateCSRFToken() ?>">

        <div class="card-body">
            <div class="form-group">
                <label for="judul">Judul <span class="text-danger">*</span></label>
                <input type="text" class="form-control" id="judul" name="judul" required placeholder="Judul pengaduan...">
            </div>

            <div class="form-group">
                <label for="kategori">Kategori</label>
                <select class="form-control" id="kategori" name="kategori">
                    <option value="Kebersihan">Kebersihan</option>
                    <option value="Keamanan">Keamanan</option>
                    <option value="Fasilitas">Fasilitas</option>
                    <option value="Lingkungan">Lingkungan</option>
                    <option value="Lainnya">Lainnya</option>
                </select>
            </div>

            <div class="form-group">
                <label for="isi">Isi Pengaduan <span class="text-danger">*</span></label>
                <textarea class="form-control" id="isi" name="isi" rows="5" required placeholder="Jelaskan pengaduan Anda secara detail..."></textarea>
            </div>

            <div class="form-group">
                <label for="lampiran">Lampiran (foto/opsional)</label>
                <div class="input-group">
                    <div class="custom-file">
                        <input type="file" class="custom-file-input" name="lampiran" accept="image/*,.pdf">
                        <label class="custom-file-label">Pilih file...</label>
                    </div>
                </div>
            </div>
        </div>

        <div class="card-footer">
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-paper-plane mr-1"></i> Kirim
            </button>
            <a href="<?= nginx_url('pengaduan') ?>" class="btn btn-secondary">
                <i class="fas fa-arrow-left mr-1"></i> Kembali
            </a>
        </div>
    </form>
</div>