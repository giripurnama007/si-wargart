<?php
/**
 * Notifikasi Index View
 */
?>

<div class="card">
    <div class="card-header">
        <h3 class="card-title">Notifikasi</h3>
        <div class="card-tools">
            <a href="<?= BASE_URL ?>notifikasi/clear" class="btn btn-danger btn-sm" onclick="return confirm('Hapus semua notifikasi?')">
                <i class="fas fa-trash mr-1"></i> Hapus Semua
            </a>
        </div>
    </div>

    <div class="card-body">
        <?php if (empty($notifikasi)): ?>
            <div class="alert alert-info text-center">
                <i class="fas fa-bell-slash fa-2x mb-2"></i>
                <p>Tidak ada notifikasi</p>
            </div>
        <?php else: ?>
            <div class="timeline">
                <?php foreach ($notifikasi as $n): ?>
                    <div class="time-label">
                        <span class="bg-<?= $n['is_read'] ? 'secondary' : 'primary' ?>">
                            <?= formatTanggal($n['created_at'], 'd M Y') ?>
                        </span>
                    </div>
                    <div>
                        <i class="fas fa-<?= $n['jenis'] === 'Tagihan' ? 'money-bill' : ($n['jenis'] === 'Pengumuman' ? 'bullhorn' : ($n['jenis'] === 'Surat' ? 'file-alt' : 'bell')) ?> bg-<?= $n['is_read'] ? 'secondary' : 'primary' ?>"></i>
                        <div class="timeline-item">
                            <h3 class="timeline-header">
                                <strong><?= $n['judul'] ?></strong>
                            </h3>
                            <div class="timeline-body">
                                <?= $n['isi'] ?>
                            </div>
                            <div class="timeline-footer">
                                <small class="text-muted">
                                    <i class="far fa-clock"></i> <?= formatTanggal($n['created_at'], 'H:i') ?>
                                </small>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</div>