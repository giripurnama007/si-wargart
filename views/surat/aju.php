<?php
/**
 * Ajukan Surat View
 */
?>

<div class="card">
    <div class="card-header">
        <h3 class="card-title">Ajukan Surat Pengantar</h3>
    </div>

    <form action="<?= nginx_url('surat/proses_aju') ?>" method="POST">
        <input type="hidden" name="csrf_token" value="<?= generateCSRFToken() ?>">

        <div class="card-body">
            <?php if (!hasRole(['warga'])): ?>
                <div class="form-group">
                    <label for="id_warga">Pilih Warga <span class="text-danger">*</span></label>
                    <select class="form-control select2" id="id_warga" name="id_warga" required>
                        <option value="">Pilih Warga</option>
                        <?php foreach ($semuaWarga as $w): ?>
                            <option value="<?= $w['id'] ?>"><?= $w['nama_lengkap'] ?> - <?= $w['nik'] ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            <?php endif; ?>

            <div class="form-group">
                <label for="jenis_surat">Jenis Surat <span class="text-danger">*</span></label>
                <select class="form-control" id="jenis_surat" name="jenis_surat" required>
                    <option value="">Pilih Jenis Surat</option>
                    <option value="Surat Domisili">Surat Domisili</option>
                    <option value="Surat Keterangan">Surat Keterangan</option>
                    <option value="Surat Usaha">Surat Usaha</option>
                    <option value="Surat Pengantar Nikah">Surat Pengantar Nikah</option>
                </select>
            </div>

            <div class="form-group">
                <label for="keperluan">Keperluan <span class="text-danger">*</span></label>
                <textarea class="form-control" id="keperluan" name="keperluan" rows="4" required placeholder="Jelaskan keperluan surat ini..."></textarea>
            </div>
        </div>

        <div class="card-footer">
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-paper-plane mr-1"></i> Ajukan
            </button>
            <a href="<?= nginx_url('surat') ?>" class="btn btn-secondary">
                <i class="fas fa-arrow-left mr-1"></i> Kembali
            </a>
        </div>
    </form>
</div>