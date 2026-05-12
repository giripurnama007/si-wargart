<?php
/**
 * Pengumuman Index View
 */
?>

<div class="card">
    <div class="card-header">
        <h3 class="card-title">Pengumuman</h3>
        <div class="card-tools">
            <a href="<?= nginx_url('pengumuman/tambah') ?>" class="btn btn-primary btn-sm">
                <i class="fas fa-plus mr-1"></i> Tambah Pengumuman
            </a>
        </div>
    </div>

    <div class="card-body">
        <?php if (empty($pengumuman)): ?>
            <div class="alert alert-info">Belum ada pengumuman</div>
        <?php else: ?>
            <?php foreach ($pengumuman as $p): ?>
                <div class="post">
                    <div class="user-block">
                        <img class="img-circle img-bordered-sm" src="<?= getFotoWarga('default.png') ?>" alt="User Image" style="object-fit: cover;">
                        <span class="username">
                            <a href="#"><?= $p['judul'] ?></a>
                            <?= getStatusBadge($p['status']) ?>
                        </span>
                        <span class="description">
                            <i class="far fa-calendar mr-1"></i> <?= formatTanggal($p['tanggal']) ?> |
                            <i class="far fa-user mr-1"></i> <?= $p['created_by_name'] ?>
                        </span>
                    </div>
                    <p><?= nl2br($p['isi']) ?></p>
                    <p>
                        <?php if (hasRole(['admin', 'ketua_rt'])): ?>
                            <a href="<?= route_url('pengumuman/edit', ['id' => $p['id']]) ?>" class="btn btn-warning btn-sm">
                                <i class="fas fa-edit"></i> Edit
                            </a>
                            <a href="#" onclick="confirmDelete('<?= route_url('pengumuman/hapus', ['id' => $p['id']]) ?>')" class="btn btn-danger btn-sm">
                                <i class="fas fa-trash"></i> Hapus
                            </a>
                        <?php endif; ?>
                    </p>
                </div>
                <hr>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>