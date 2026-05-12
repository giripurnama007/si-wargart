<?php
/**
 * Surat Index View
 */
?>

<div class="card">
    <div class="card-header">
        <h3 class="card-title">Surat Pengantar</h3>
        <div class="card-tools">
            <a href="<?= nginx_url('surat/aju') ?>" class="btn btn-primary btn-sm">
                <i class="fas fa-plus mr-1"></i> Ajukan Surat
            </a>
        </div>
    </div>

    <div class="card-body table-responsive">
        <table id="dataTable" class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th>No</th>
                    <th>No Surat</th>
                    <th>Warga</th>
                    <th>Jenis Surat</th>
                    <th>Tanggal Pengajuan</th>
                    <th>Status</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($surat)): ?>
                    <?php $no = 1; foreach ($surat as $s): ?>
                        <tr>
                            <td><?= $no++ ?></td>
                            <td><?= $s['no_surat'] ?: '-' ?></td>
                            <td><?= $s['nama_lengkap'] ?><br><small class="text-muted"><?= $s['nik'] ?></small></td>
                            <td><?= $s['jenis_surat'] ?></td>
                            <td><?= formatTanggal($s['tanggal_pengajuan']) ?></td>
                            <td><?= getStatusBadge($s['status']) ?></td>
                            <td>
                                <a href="<?= route_url('surat/detail', ['id' => $s['id']]) ?>" class="btn btn-info btn-sm">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <?php if ($s['status'] === 'Selesai'): ?>
                                    <a href="<?= route_url('surat/cetak', ['id' => $s['id']]) ?>" class="btn btn-success btn-sm" target="_blank">
                                        <i class="fas fa-print"></i>
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