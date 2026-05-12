<?php
/**
 * Forum Index View
 */
$user_id = $_SESSION['user_id'] ?? 0;
$role = $_SESSION['role'] ?? '';
?>

<div class="row mb-3">
    <div class="col-md-12 text-right">
        <a href="<?= nginx_url('forum/tambah') ?>" class="btn btn-primary">
            <i class="fas fa-edit mr-1"></i> Buat Topik Baru
        </a>
    </div>
</div>

<div class="row">
    <div class="col-md-12">
        <?php if (empty($topik)): ?>
            <div class="alert alert-info text-center">
                <i class="fas fa-info-circle mb-2 fa-2x"></i><br>
                Belum ada topik diskusi yang dibuat. Jadilah yang pertama memulai diskusi!
            </div>
        <?php else: ?>
            <?php foreach ($topik as $t): ?>
                <div class="card card-widget widget-user-2 shadow-sm">
                    <div class="widget-user-header bg-light">
                        <div class="widget-user-image">
                            <img class="img-circle elevation-2" src="<?= getFotoWarga($t['foto']) ?>" alt="User Avatar" style="width: 65px; height: 65px; object-fit: cover;">
                        </div>
                        <h3 class="widget-user-username font-weight-bold ml-5">
                            <a href="<?= route_url('forum/detail', ['id' => $t['id']]) ?>" class="text-dark"><?= $t['judul'] ?></a>
                        </h3>
                        <h5 class="widget-user-desc ml-5">
                            Oleh: <?= $t['pembuat'] ?> | <i class="far fa-clock"></i> <?= formatTanggal($t['created_at'], 'd M Y H:i') ?>
                        </h5>
                    </div>
                    <div class="card-footer p-2">
                        <div class="row align-items-center">
                            <div class="col-sm-4 border-right">
                                <div class="description-block">
                                    <h5 class="description-header text-info"><i class="fas fa-comments mr-1"></i> <?= $t['total_komentar'] ?></h5>
                                    <span class="description-text">BALASAN</span>
                                </div>
                            </div>
                            <div class="col-sm-4 border-right text-center">
                                <div class="description-block">
                                    <h5 class="description-header text-secondary"><i class="fas fa-eye mr-1"></i> <?= $t['views'] ?? 0 ?></h5>
                                    <span class="description-text">DILIHAT</span>
                                </div>
                            </div>
                            <div class="col-sm-4 text-right">
                                <?php
                                $canDelete = ($role === 'admin' || $role === 'ketua_rt') || ($t['id_user'] == $user_id && $t['total_komentar'] == 0);
                                if ($canDelete):
                                ?>
                                    <a href="<?= nginx_url('forum/hapus?id=' . $t['id']) ?>"
                                       class="btn btn-danger btn-sm"
                                       onclick="return confirm('Yakin ingin menghapus topik ini?');"
                                       title="Hapus Topik">
                                        <i class="fas fa-trash"></i> Hapus
                                    </a>
                                <?php endif; ?>
                                <a href="<?= route_url('forum/detail', ['id' => $t['id']]) ?>" class="btn btn-outline-primary btn-sm">
                                    Ikut Diskusi <i class="fas fa-arrow-right ml-1"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>