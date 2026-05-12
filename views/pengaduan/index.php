<?php
/**
 * Pengaduan Index View
 */
?>

<div class="card">
    <div class="card-header">
        <h3 class="card-title">Pengaduan Warga</h3>
        <div class="card-tools">
            <a href="<?= nginx_url('pengaduan/tambah') ?>" class="btn btn-primary btn-sm">
                <i class="fas fa-plus mr-1"></i> Ajukan Pengaduan
            </a>
        </div>
    </div>

    <div class="card-body table-responsive">
        <table id="dataTable" class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Tanggal</th>
                    <th>Judul</th>
                    <th>Kategori</th>
                    <th>Pelapor</th>
                    <th>Status</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($pengaduan)): ?>
                    <?php $no = 1; foreach ($pengaduan as $p): ?>
                        <tr>
                            <td><?= $no++ ?></td>
                            <td><?= formatTanggal($p['tanggal_pengaduan']) ?></td>
                            <td><?= $p['judul'] ?></td>
                            <td><span class="badge badge-info"><?= $p['kategori'] ?></span></td>
                            <td><?= $p['nama_lengkap'] ?><br><small class="text-muted"><?= $p['no_hp'] ?></small></td>
                            <td><?= getStatusBadge($p['status']) ?></td>
                            <td>
                                <?php if (hasRole(['admin', 'ketua_rt']) && in_array($p['status'], ['Dikirim', 'Diproses'])): ?>
                                    <a href="<?= route_url('pengaduan/respon', ['id' => $p['id']]) ?>" class="btn btn-warning btn-sm">
                                        <i class="fas fa-reply"></i> Respon
                                    </a>
                                <?php endif; ?>
                                <a href="<?= route_url('pengaduan/respon', ['id' => $p['id']]) ?>" class="btn btn-info btn-sm">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <?php if (hasRole(['admin']) && $p['status'] === 'Selesai'): ?>
                                    <a href="#" onclick="confirmDelete('<?= route_url('pengaduan/hapus', ['id' => $p['id']]) ?>')" class="btn btn-danger btn-sm">
                                        <i class="fas fa-trash"></i>
                                    </a>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>