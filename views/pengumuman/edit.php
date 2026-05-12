<?php
/**
 * Edit Pengumuman View
 */
?>

<div class="card">
    <div class="card-header">
        <h3 class="card-title">Edit Pengumuman</h3>
    </div>

    <form action="<?= nginx_url('pengumuman/proses_edit') ?>" method="POST" enctype="multipart/form-data">
        <input type="hidden" name="csrf_token" value="<?= generateCSRFToken() ?>">
        <input type="hidden" name="id" value="<?= $pengumuman['id'] ?>">
        <input type="hidden" name="banner_lama" value="<?= $pengumuman['banner'] ?>">

        <div class="card-body">
            <div class="form-group">
                <label for="judul">Judul <span class="text-danger">*</span></label>
                <input type="text" class="form-control" id="judul" name="judul" value="<?= $pengumuman['judul'] ?>" required>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="tanggal">Tanggal</label>
                        <input type="date" class="form-control" id="tanggal" name="tanggal" value="<?= $pengumuman['tanggal'] ?>">
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="status">Status</label>
                        <select class="form-control" id="status" name="status">
                            <option value="Published" <?= $pengumuman['status'] === 'Published' ? 'selected' : '' ?>>Published</option>
                            <option value="Draft" <?= $pengumuman['status'] === 'Draft' ? 'selected' : '' ?>>Draft</option>
                            <option value="Archived" <?= $pengumuman['status'] === 'Archived' ? 'selected' : '' ?>>Archived</option>
                        </select>
                    </div>
                </div>
            </div>

            <div class="form-group">
                <label for="isi">Isi Pengumuman <span class="text-danger">*</span></label>
                <textarea class="form-control" id="isi" name="isi" rows="5" required><?= $pengumuman['isi'] ?></textarea>
            </div>

            <div class="form-group">
                <label for="banner">Banner</label>
                <div class="input-group">
                    <div class="custom-file">
                        <input type="file" class="custom-file-input" name="banner" accept="image/*">
                        <label class="custom-file-label">Pilih file...</label>
                    </div>
                </div>
                <?php if ($pengumuman['banner']): ?>
                    <img src="<?= BASE_URL ?>uploads/pengumuman/<?= $pengumuman['banner'] ?>" class="img-thumbnail mt-2" style="max-width: 200px;">
                <?php endif; ?>
            </div>
        </div>

        <div class="card-footer">
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-save mr-1"></i> Update
            </button>
            <a href="<?= nginx_url('pengumuman') ?>" class="btn btn-secondary">
                <i class="fas fa-arrow-left mr-1"></i> Kembali
            </a>
        </div>
    </form>
</div>