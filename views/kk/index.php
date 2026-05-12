<?php
/**
 * KK Index View
 */
?>

<div class="card">
    <div class="card-header">
        <h3 class="card-title">Kartu Keluarga</h3>
        <div class="card-tools">
            <a href="<?= nginx_url('kk/tambah') ?>" class="btn btn-primary btn-sm">
                <i class="fas fa-plus mr-1"></i> Tambah KK
            </a>
        </div>
    </div>

    <div class="card-body table-responsive">
        <table id="dataTable" class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th>No</th>
                    <th>No. KK</th>
                    <th>Kepala Keluarga</th>
                    <th>NIK Kepala</th>
                    <th>Jumlah Anggota</th>
                    <th>Alamat</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($kk)): ?>
                    <?php $no = 1; foreach ($kk as $k): ?>
                        <tr>
                            <td><?= $no++ ?></td>
                            <td><strong><?= $k['no_kk'] ?></strong></td>
                            <td><?= $k['nama_kepala'] ?></td>
                            <td><?= $k['nik_kepala'] ?: '-' ?></td>
                            <td><span class="badge badge-info"><?= $k['jumlah_anggota'] ?> orang</span></td>
                            <td><?= $k['alamat'] ?></td>
                            <td>
                                <a href="<?= route_url('kk/detail', ['id' => $k['id']]) ?>" class="btn btn-info btn-sm" title="Detail KK">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="<?= route_url('kk/edit', ['id' => $k['id']]) ?>" class="btn btn-warning btn-sm">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <a href="#" onclick="confirmDelete('<?= route_url('kk/hapus', ['id' => $k['id']]) ?>')" class="btn btn-danger btn-sm">
                                    <i class="fas fa-trash"></i>
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>