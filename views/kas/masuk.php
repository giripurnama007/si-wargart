<?php
/**
 * Kas Masuk View
 */
?>

<div class="card card-success">
    <div class="card-header">
        <h3 class="card-title">Tambah Kas Masuk</h3>
    </div>

    <form action="<?= nginx_url('kas/proses_masuk') ?>" method="POST" enctype="multipart/form-data">
        <input type="hidden" name="csrf_token" value="<?= generateCSRFToken() ?>">

        <div class="card-body">
            <div class="form-group">
                <label for="kategori">Kategori <span class="text-danger">*</span></label>
                <select class="form-control" name="kategori" required>
                    <option value="">Pilih</option>
                    <option value="Iuran Kebersihan">Iuran Kebersihan</option>
                    <option value="Iuran Keamanan">Iuran Keamanan</option>
                    <option value="Kas RT">Kas RT</option>
                    <option value="Dana Sosial">Dana Sosial</option>
                    <option value=" Sumbangan">Sumbangan</option>
                    <option value="Lainnya">Lainnya</option>
                </select>
            </div>

            <div class="form-group">
                <label for="jumlah">Jumlah <span class="text-danger">*</span></label>
                <input type="number" class="form-control" name="jumlah" step="0.01" required>
            </div>

            <div class="form-group">
                <label for="tanggal">Tanggal <span class="text-danger">*</span></label>
                <input type="date" class="form-control" name="tanggal" value="<?= date('Y-m-d') ?>" required>
            </div>

            <div class="form-group">
                <label for="keterangan">Keterangan</label>
                <textarea class="form-control" name="keterangan" rows="3"></textarea>
            </div>

            <div class="form-group">
                <label for="bukti">Bukti (opsional)</label>
                <div class="input-group">
                    <div class="custom-file">
                        <input type="file" class="custom-file-input" name="bukti" accept="image/*,.pdf">
                        <label class="custom-file-label">Pilih file...</label>
                    </div>
                </div>
            </div>
        </div>

        <div class="card-footer">
            <button type="submit" class="btn btn-success">
                <i class="fas fa-save mr-1"></i> Simpan
            </button>
            <a href="<?= nginx_url('kas') ?>" class="btn btn-secondary">
                <i class="fas fa-arrow-left mr-1"></i> Kembali
            </a>
        </div>
    </form>
</div>