<?php
/**
 * Kegiatan Index View
 */
?>

<div class="card">
    <div class="card-header">
        <h3 class="card-title">Kegiatan RT</h3>
        <div class="card-tools">
            <?php if (hasRole(['admin', 'ketua_rt'])): ?>
                <a href="<?= nginx_url('kegiatan/tambah') ?>" class="btn btn-primary btn-sm">
                    <i class="fas fa-plus mr-1"></i> Tambah Kegiatan
                </a>
            <?php endif; ?>
        </div>
    </div>

    <div class="card-body table-responsive">
        <table id="dataTable" class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Judul</th>
                    <th>Tanggal</th>
                    <th>Waktu</th>
                    <th>Lokasi</th>
                    <th>Status</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($kegiatan)): ?>
                    <?php $no = 1; foreach ($kegiatan as $k): ?>
                        <tr>
                            <td><?= $no++ ?></td>
                            <td><?= $k['judul'] ?></td>
                            <td><?= formatTanggal($k['tanggal']) ?></td>
                            <td><?= $k['waktu'] ? date('H:i', strtotime($k['waktu'])) : '-' ?></td>
                            <td><?= $k['lokasi'] ?: '-' ?></td>
                            <td><?= getStatusBadge($k['status']) ?></td>
                            <td>
                                <a href="<?= route_url('kegiatan/detail', ['id' => $k['id']]) ?>" class="btn btn-info btn-sm" title="Detail">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <?php if (hasRole(['admin', 'ketua_rt'])): ?>
                                    <a href="<?= route_url('kegiatan/edit', ['id' => $k['id']]) ?>" class="btn btn-warning btn-sm" title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <a href="#" onclick="confirmDelete('<?= route_url('kegiatan/hapus', ['id' => $k['id']]) ?>')" class="btn btn-danger btn-sm" title="Hapus">
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