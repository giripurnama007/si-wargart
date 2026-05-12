<?php
/**
 * Respon Pengaduan View
 */
?>

<div class="card">
    <div class="card-header bg-warning">
        <h3 class="card-title">Respon Pengaduan</h3>
    </div>

    <div class="card-body">
        <div class="row">
            <div class="col-md-6">
                <table class="table table-striped">
                    <tr>
                        <th width="35%">Tanggal Pengaduan</th>
                        <td><?= formatTanggal($pengaduan['tanggal_pengaduan']) ?></td>
                    </tr>
                    <tr>
                        <th>Pelapor</th>
                        <td><?= $pengaduan['nama_lengkap'] ?><br><small class="text-muted"><?= $pengaduan['no_hp'] ?></small></td>
                    </tr>
                    <tr>
                        <th>Kategori</th>
                        <td><span class="badge badge-info"><?= $pengaduan['kategori'] ?></span></td>
                    </tr>
                    <tr>
                        <th>Status</th>
                        <td><?= getStatusBadge($pengaduan['status']) ?></td>
                    </tr>
                </table>
            </div>
        </div>

        <div class="row mt-3">
            <div class="col-md-12">
                <h5><strong>Judul:</strong> <?= $pengaduan['judul'] ?></h5>
                <div class="alert alert-secondary">
                    <strong>Isi Pengaduan:</strong><br>
                    <?= nl2br($pengaduan['isi']) ?>
                </div>
                <?php if ($pengaduan['lampiran']): ?>
                    <div class="mb-3">
                        <strong>Lampiran:</strong><br>
                        <a href="<?= BASE_URL ?>uploads/pengaduan/<?= $pengaduan['lampiran'] ?>" target="_blank" class="btn btn-sm btn-info">
                            <i class="fas fa-paperclip"></i> Lihat Lampiran
                        </a>
                    </div>
                <?php endif; ?>

                <?php if ($pengaduan['respon']): ?>
                    <div class="alert alert-success">
                        <strong>Respon:</strong><br>
                        <?= nl2br($pengaduan['respon']) ?>
                        <hr>
                        <small class="text-muted">
                            Oleh: <?= $pengaduan['respon_by_name'] ?><br>
                            Tanggal: <?= formatTanggal($pengaduan['respon_at']) ?>
                        </small>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <hr>
        <form action="<?= nginx_url('pengaduan/proses_respon') ?>" method="POST">
            <input type="hidden" name="csrf_token" value="<?= generateCSRFToken() ?>">
            <input type="hidden" name="id" value="<?= $pengaduan['id'] ?>">

            <div class="form-group">
                <label for="respon">Respon / Jawaban <span class="text-danger">*</span></label>
                <textarea class="form-control" id="respon" name="respon" rows="4" required placeholder="Berikan respon terhadap pengaduan ini..."><?= $pengaduan['respon'] ?></textarea>
            </div>

            <div class="form-group">
                <label for="status">Update Status</label>
                <select class="form-control" id="status" name="status">
                    <option value="Diproses" <?= $pengaduan['status'] === 'Diproses' ? 'selected' : '' ?>>Diproses</option>
                    <option value="Selesai" <?= $pengaduan['status'] === 'Selesai' ? 'selected' : '' ?>>Selesai</option>
                </select>
            </div>

            <button type="submit" class="btn btn-success">
                <i class="fas fa-save mr-1"></i> Simpan Respon
            </button>
            <a href="<?= nginx_url('pengaduan') ?>" class="btn btn-secondary ml-2">
                <i class="fas fa-arrow-left mr-1"></i> Kembali
            </a>
        </form>
    </div>
</div>