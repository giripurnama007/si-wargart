<?php
/**
 * Import Warga View
 */
?>

<div class="card">
    <div class="card-header">
        <h3 class="card-title">Import Data Warga</h3>
    </div>

    <div class="card-body">
        <div class="alert alert-info">
            <h5><i class="icon fas fa-info"></i> Petunjuk Import</h5>
            <ul class="mb-0">
                <li>Format file必须是 CSV (Comma Separated Values)</li>
                <li>Kolom harus berurutan: NIK, No KK, Nama, Tempat Lahir, Tanggal Lahir, Jenis Kelamin, Agama, Status Kawin, Pekerjaan, No HP, Email, Alamat, RT, RW</li>
                <li>Baris pertama akan diabaikan (header)</li>
                <li>NIK harus unik, data duplikat akan dilewati</li>
            </ul>
        </div>

        <form action="<?= nginx_url('warga/proses_import') ?>" method="POST" enctype="multipart/form-data">
            <input type="hidden" name="csrf_token" value="<?= generateCSRFToken() ?>">

            <div class="form-group">
                <label for="file">File CSV</label>
                <div class="input-group">
                    <div class="custom-file">
                        <input type="file" class="custom-file-input" id="file" name="file" accept=".csv" required>
                        <label class="custom-file-label" for="file">Pilih file CSV...</label>
                    </div>
                </div>
            </div>

            <div class="mt-3">
                <a href="<?= BASE_URL ?>assets/template_import_warga.csv" class="btn btn-secondary" download>
                    <i class="fas fa-download mr-1"></i> Download Template
                </a>
            </div>
    </div>

    <div class="card-footer">
        <button type="submit" class="btn btn-primary">
            <i class="fas fa-upload mr-1"></i> Import
        </button>
        <a href="<?= nginx_url('warga') ?>" class="btn btn-secondary">
            <i class="fas fa-arrow-left mr-1"></i> Kembali
        </a>
    </div>
    </form>
</div>