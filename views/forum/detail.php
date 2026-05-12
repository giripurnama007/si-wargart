<?php
/**
 * Forum Detail & Komentar View
 */
?>

<div class="card card-widget">
    <div class="card-header">
        <div class="user-block">
            <img class="img-circle" src="<?= getFotoWarga($topik['foto']) ?>" alt="User Image" style="object-fit: cover;">
            <span class="username text-primary"><?= $topik['nama'] ?></span>
            <span class="description"><i class="far fa-clock"></i> Diposting pada <?= formatTanggal($topik['created_at'], 'd F Y - H:i') ?></span>
        </div>
        <div class="card-tools">
            <?php if (hasRole(['admin', 'ketua_rt'])): ?>
                <a href="#" onclick="confirmDelete('<?= route_url('forum/hapus', ['id' => $topik['id']]) ?>', 'Yakin menghapus topik diskusi ini?')" class="btn btn-tool text-danger" title="Hapus Topik">
                    <i class="fas fa-trash"></i>
                </a>
            <?php endif; ?>
            <a href="<?= nginx_url('forum') ?>" class="btn btn-tool" title="Kembali ke Daftar">
                <i class="fas fa-times"></i>
            </a>
        </div>
    </div>
    <div class="card-body">
        <h4 class="mb-3 font-weight-bold"><?= $topik['judul'] ?></h4>
        <p style="white-space: pre-wrap; font-size: 1.1em;"><?= htmlspecialchars($topik['isi']) ?></p>
        
        <span class="float-right text-muted"><?= count($komentar) ?> balasan</span>
    </div>
    
    <!-- Bagian Daftar Komentar -->
    <div class="card-footer card-comments">
        <?php if (empty($komentar)): ?>
            <div class="text-center text-muted py-3">Belum ada balasan, jadilah yang pertama memberikan pendapat!</div>
        <?php else: ?>
            <?php foreach ($komentar as $k): ?>
                <div class="card-comment">
                    <img class="img-circle img-sm" src="<?= getFotoWarga($k['foto']) ?>" alt="User Image" style="object-fit: cover;">
                    <div class="comment-text">
                        <span class="username">
                            <?= $k['nama'] ?> 
                            <?php if (in_array($k['role'], ['admin', 'ketua_rt'])): ?>
                                <span class="badge badge-warning ml-1 text-xs">Pengurus</span>
                            <?php endif; ?>
                            <span class="text-muted float-right"><?= formatTanggalPendek($k['created_at']) ?> <?= date('H:i', strtotime($k['created_at'])) ?></span>
                        </span>
                        <div style="white-space: pre-wrap; margin-top: 5px;"><?= htmlspecialchars($k['isi']) ?></div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
    
    <!-- Form Kirim Komentar -->
    <div class="card-footer">
        <?php if ($topik['status'] === 'Open'): ?>
            <form action="<?= nginx_url('forum/proses_komentar') ?>" method="post">
                <input type="hidden" name="csrf_token" value="<?= generateCSRFToken() ?>">
                <input type="hidden" name="id_topik" value="<?= $topik['id'] ?>">
                
                <img class="img-fluid img-circle img-sm" src="<?= getFotoWarga($_SESSION['foto'] ?? '') ?>" alt="Alt Text" style="object-fit: cover;">
                <div class="img-push">
                    <div class="input-group">
                        <input type="text" name="isi" class="form-control form-control-sm" placeholder="Tulis balasan Anda..." required autocomplete="off">
                        <div class="input-group-append">
                            <button type="submit" class="btn btn-primary btn-sm">
                                <i class="fas fa-paper-plane"></i> Kirim
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        <?php else: ?>
            <div class="text-center text-danger">
                <i class="fas fa-lock mr-1"></i> Topik diskusi ini telah ditutup.
            </div>
        <?php endif; ?>
    </div>
</div>