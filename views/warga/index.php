<?php
/**
 * Warga Index View
 * SI-WargaRT - Sistem Informasi Warga RT
 */
?>

<div class="card">
    <div class="card-header">
        <h3 class="card-title">Data Warga</h3>
        <div class="card-tools">
            <?php if (hasRole(['admin', 'ketua_rt'])): ?>
                <a href="<?= nginx_url('warga/tambah') ?>" class="btn btn-primary btn-sm">
                    <i class="fas fa-plus mr-1"></i> Tambah Warga
                </a>
                <a href="<?= nginx_url('warga/import') ?>" class="btn btn-success btn-sm">
                    <i class="fas fa-file-import mr-1"></i> Import
                </a>
                <a href="<?= nginx_url('warga/export') ?>" class="btn btn-info btn-sm">
                    <i class="fas fa-file-export mr-1"></i> Export
                </a>
            <?php endif; ?>
        </div>
    </div>

    <div class="card-body">
        <form method="GET" action="<?= BASE_URL ?>index.php" class="mb-3">
            <input type="hidden" name="route" value="warga">
            <div class="row">
                <div class="col-md-4">
                    <input type="text" name="search" class="form-control" placeholder="Cari NIK, Nama, No KK..." value="<?= $search ?? '' ?>">
                </div>
                <div class="col-md-3">
                    <select name="status" class="form-control">
                        <option value="">Semua Status</option>
                        <option value="Aktif" <?= ($status ?? '') === 'Aktif' ? 'selected' : '' ?>>Aktif</option>
                        <option value="Non-Aktif" <?= ($status ?? '') === 'Non-Aktif' ? 'selected' : '' ?>>Non-Aktif</option>
                        <option value="Mutasi" <?= ($status ?? '') === 'Mutasi' ? 'selected' : '' ?>>Mutasi</option>
                        <option value="Meninggal" <?= ($status ?? '') === 'Meninggal' ? 'selected' : '' ?>>Meninggal</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-default"><i class="fas fa-search"></i> Cari</button>
                </div>
            </div>
        </form>

        <table id="dataTable" class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Foto</th>
                    <th>NIK</th>
                    <th>Nama Lengkap</th>
                    <th>JK</th>
                    <th>TTL</th>
                    <th>Pekerjaan</th>
                    <th>Status</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($warga)): ?>
                    <?php $no = $offset + 1; foreach ($warga as $w): ?>
                        <tr>
                            <td><?= $no++ ?></td>
                            <td>
                                <img src="<?= getFotoWarga($w['foto']) ?>" alt="Foto" class="img-circle" style="width: 40px; height: 40px; object-fit: cover;">
                            </td>
                            <td><?= $w['nik'] ?></td>
                            <td><?= $w['nama_lengkap'] ?></td>
                            <td><?= $w['jenis_kelamin'] ?></td>
                            <td><?= $w['tempat_lahir'] ?>, <?= formatTanggal($w['tanggal_lahir'], 'd/m/Y') ?></td>
                            <td><?= $w['pekerjaan'] ?></td>
                            <td><?= getStatusBadge($w['status_warga']) ?></td>
                            <td>
                                <a href="<?= route_url('warga/detail', ['id' => $w['id']]) ?>" class="btn btn-info btn-sm" title="Detail">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <?php if (hasRole(['admin', 'ketua_rt'])): ?>
                                    <a href="<?= route_url('warga/edit', ['id' => $w['id']]) ?>" class="btn btn-warning btn-sm" title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <a href="#" onclick="confirmDelete('<?= route_url('warga/hapus', ['id' => $w['id']]) ?>', 'Yakin ingin menghapus warga ini?')" class="btn btn-danger btn-sm" title="Hapus">
                                        <i class="fas fa-trash"></i>
                                    </a>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>

        <div class="mt-3">
            <?php
                $queryStr = 'warga';
                if (!empty($search)) $queryStr .= '&search=' . urlencode($search);
                if (!empty($status)) $queryStr .= '&status=' . urlencode($status);
                
                echo pagination($total, $perPage, $page, $queryStr);
            ?>
        </div>
    </div>
</div>