<?php
/**
 * Kas Keluar View
 */
?>

<div class="card card-danger">
    <div class="card-header">
        <h3 class="card-title">Tambah Kas Keluar</h3>
    </div>

    <form action="<?= nginx_url('kas/proses_keluar') ?>" method="POST" enctype="multipart/form-data">
        <input type="hidden" name="csrf_token" value="<?= generateCSRFToken() ?>">

        <div class="card-body">
            <div class="form-group">
                <label for="kategori">Kategori <span class="text-danger">*</span></label>
                <select class="form-control" name="kategori" required>
                    <option value="">Pilih</option>
                    <option value="Kebersihan">Kebersihan</option>
                    <option value="Keamanan">Keamanan</option>
                    <option value="Perbaikan">Perbaikan</option>
                    <option value="Honorarium">Honorarium</option>
                    <option value="Kegiatan">Kegiatan</option>
                    <option value=" Lainnya">Lainnya</option>
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
            <button type="submit" class="btn btn-danger">
                <i class="fas fa-save mr-1"></i> Simpan
            </button>
            <a href="<?= nginx_url('kas') ?>" class="btn btn-secondary">
                <i class="fas fa-arrow-left mr-1"></i> Batal
            </a>
        </div>
    </form>
</div>