<?php
/**
 * Detail Kegiatan View
 */
?>

<div class="card">
    <div class="card-header">
        <h3 class="card-title">Detail Kegiatan</h3>
        <div class="card-tools">
            <a href="<?= nginx_url('kegiatan') ?>" class="btn btn-secondary btn-sm">
                <i class="fas fa-arrow-left mr-1"></i> Kembali
            </a>
        </div>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-8">
                <h4 class="text-primary"><?= htmlspecialchars($kegiatan['judul']) ?></h4>
                <div class="text-muted mb-3">
                    <i class="far fa-calendar-alt mr-1"></i> <?= formatTanggal($kegiatan['tanggal']) ?> 
                    <?php if ($kegiatan['waktu']): ?>
                        <i class="far fa-clock ml-2 mr-1"></i> <?= date('H:i', strtotime($kegiatan['waktu'])) ?> WIB
                    <?php endif; ?>
                    <i class="fas fa-map-marker-alt ml-2 mr-1"></i> <?= htmlspecialchars($kegiatan['lokasi']) ?: 'Tidak ditentukan' ?>
                </div>
                
                <h5>Status: <?= getStatusBadge($kegiatan['status']) ?></h5>
                
                <hr>
                <h5>Deskripsi Kegiatan</h5>
                <p style="white-space: pre-wrap;"><?= nl2br(htmlspecialchars($kegiatan['isi'])) ?></p>
            </div>
            
            <div class="col-md-4 text-center">
                <?php if ($kegiatan['banner']): ?>
                    <img src="<?= BASE_URL ?>uploads/kegiatan/<?= $kegiatan['banner'] ?>" class="img-fluid img-thumbnail" alt="Banner Kegiatan">
                <?php else: ?>
                    <div class="bg-light p-5 text-center text-muted border">
                        <i class="fas fa-image fa-4x mb-3"></i>
                        <p>Tidak ada banner</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>