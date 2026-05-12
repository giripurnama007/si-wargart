<?php
/**
 * Detail KK View
 */
?>

<div class="card">
    <div class="card-header">
        <h3 class="card-title">Detail Kartu Keluarga</h3>
        <div class="card-tools">
            <a href="<?= route_url('kk/edit', ['id' => $kk['id']]) ?>" class="btn btn-warning btn-sm">
                <i class="fas fa-edit mr-1"></i> Edit Data
            </a>
            <a href="<?= nginx_url('kk') ?>" class="btn btn-secondary btn-sm">
                <i class="fas fa-arrow-left mr-1"></i> Kembali
            </a>
        </div>
    </div>
    <div class="card-body">
        <div class="row mb-4">
            <div class="col-md-8">
                <table class="table table-borderless table-sm">
                    <tr>
                        <th width="200">No. Kartu Keluarga</th>
                        <td>: <strong><?= $kk['no_kk'] ?></strong></td>
                    </tr>
                    <tr>
                        <th>Nama Kepala Keluarga</th>
                        <td>: <?= $kk['nama_kepala'] ?></td>
                    </tr>
                    <tr>
                        <th>NIK Kepala Keluarga</th>
                        <td>: <?= $kk['nik_kepala'] ?: '-' ?></td>
                    </tr>
                    <tr>
                        <th>Alamat Lengkap</th>
                        <td>: <?= $kk['alamat'] ?></td>
                    </tr>
                    <tr>
                        <th>RT / RW</th>
                        <td>: <?= $kk['rt'] ?> / <?= $kk['rw'] ?></td>
                    </tr>
                    <tr>
                        <th>Kode Pos</th>
                        <td>: <?= $kk['kode_pos'] ?: '-' ?></td>
                    </tr>
                </table>
            </div>
            <div class="col-md-4 text-center">
                <?php if ($kk['foto_kk']): ?>
                    <img src="<?= BASE_URL ?>uploads/warga/<?= $kk['foto_kk'] ?>" class="img-thumbnail" style="max-height: 150px; object-fit: contain;" alt="Foto KK">
                    <br>
                    <a href="<?= BASE_URL ?>uploads/warga/<?= $kk['foto_kk'] ?>" target="_blank" class="btn btn-sm btn-info mt-2"><i class="fas fa-search-plus"></i> Lihat Scan KK</a>
                <?php else: ?>
                    <div class="border p-4 text-muted bg-light">
                        <i class="fas fa-image fa-3x mb-2"></i><br>
                        <small>Belum ada foto/scan KK</small>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<div class="card mt-4">
    <div class="card-header bg-lightblue">
        <h3 class="card-title">Daftar Anggota Keluarga</h3>
    </div>
    <div class="card-body table-responsive">
        <table class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th>No</th>
                    <th>NIK</th>
                    <th>Nama Lengkap</th>
                    <th>JK</th>
                    <th>Tempat, Tgl Lahir</th>
                    <th>Status Warga</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($anggota)): ?>
                    <tr><td colspan="7" class="text-center text-muted">Tidak ada data anggota keluarga</td></tr>
                <?php else: ?>
                    <?php $no = 1; foreach ($anggota as $a): ?>
                        <tr>
                            <td><?= $no++ ?></td>
                            <td><?= $a['nik'] ?></td>
                            <td><?= $a['nama_lengkap'] ?></td>
                            <td><?= $a['jenis_kelamin'] ?></td>
                            <td><?= $a['tempat_lahir'] ?>, <?= formatTanggal($a['tanggal_lahir'], 'd/m/Y') ?></td>
                            <td><?= getStatusBadge($a['status_warga']) ?></td>
                            <td>
                                <a href="<?= route_url('warga/detail', ['id' => $a['id']]) ?>" class="btn btn-info btn-sm" title="Lihat Profil Warga"><i class="fas fa-eye"></i> Profil</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>