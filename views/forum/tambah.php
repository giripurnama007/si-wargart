<?php
/**
 * Forum Tambah View
 */
?>

<div class="card card-primary card-outline">
    <div class="card-header">
        <h3 class="card-title">Mulai Topik Diskusi Baru</h3>
    </div>
    <form action="<?= nginx_url('forum/proses_tambah') ?>" method="POST">
        <input type="hidden" name="csrf_token" value="<?= generateCSRFToken() ?>">
        <div class="card-body">
            <div class="form-group">
                <label for="judul">Judul Diskusi <span class="text-danger">*</span></label>
                <input type="text" class="form-control" id="judul" name="judul" required placeholder="Tuliskan judul topik yang singkat dan jelas...">
            </div>
            <div class="form-group">
                <label for="isi">Isi Pesan <span class="text-danger">*</span></label>
                <textarea class="form-control" id="isi" name="isi" rows="6" required placeholder="Jelaskan lebih detail apa yang ingin Anda diskusikan dengan warga lain..."></textarea>
            </div>
        </div>
        <div class="card-footer">
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-paper-plane mr-1"></i> Posting Topik
            </button>
            <a href="<?= nginx_url('forum') ?>" class="btn btn-secondary">
                <i class="fas fa-arrow-left mr-1"></i> Batal
            </a>
        </div>
    </form>
</div>