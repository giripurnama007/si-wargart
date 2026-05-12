<?php
/**
 * Detail Warga View
 * SI-WargaRT - Sistem Informasi Warga RT
 */
?>

<div class="row">
    <div class="col-md-4">
        <div class="card card-primary">
            <div class="card-body box-profile">
                <div class="text-center">
                    <img class="profile-user-img img-fluid img-circle" 
                         src="<?= getFotoWarga($warga['foto']) ?>" 
                         alt="Foto <?= $warga['nama_lengkap'] ?>" style="width: 100px; height: 100px; object-fit: cover;">
                </div>
                <h3 class="profile-username text-center"><?= $warga['nama_lengkap'] ?></h3>
                <p class="text-muted text-center"><?= $warga['nik'] ?></p>
                <p class="text-center"><?= getStatusBadge($warga['status_warga']) ?></p>

                <div id="qrcode" class="text-center mt-3"></div>
                <p class="text-muted text-center"><small>QRCode Profil</small></p>

                <div class="mt-3">
                    <a href="<?= route_url('warga/export_pdf', ['id' => $warga['id']]) ?>" class="btn btn-success btn-block" target="_blank">
                        <i class="fas fa-id-card mr-1"></i> Cetak Kartu Identitas
                    </a>
                    <?php if (hasRole(['admin', 'ketua_rt'])): ?>
                        <a href="<?= route_url('warga/edit', ['id' => $warga['id']]) ?>" class="btn btn-warning btn-block">
                            <i class="fas fa-edit mr-1"></i> Edit Data
                        </a>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <div class="card card-primary">
            <div class="card-header">
                <h3 class="card-title">Informasi Lainnya</h3>
            </div>
            <div class="card-body">
                <strong><i class="fas fa-home mr-1"></i> Alamat</strong>
                <p class="text-muted"><?= $warga['alamat'] ?></p>
                <hr>
                <strong><i class="fas fa-map-marker-alt mr-1"></i> RT/RW</strong>
                <p class="text-muted"><?= $warga['rt'] ?>/<?= $warga['rw'] ?></p>
                <hr>
                <strong><i class="fas fa-phone mr-1"></i> No. HP</strong>
                <p class="text-muted"><?= $warga['no_hp'] ?: '-' ?></p>
                <hr>
                <strong><i class="fas fa-envelope mr-1"></i> Email</strong>
                <p class="text-muted"><?= $warga['email'] ?: '-' ?></p>
            </div>
        </div>
    </div>

    <div class="col-md-8">
        <div class="card">
            <div class="card-header p-2">
                <h3 class="card-title">Informasi Personal</h3>
            </div>
            <div class="card-body">
                <table class="table table-sm">
                    <tr>
                        <th width="35%">NIK</th>
                        <td><?= $warga['nik'] ?></td>
                    </tr>
                    <tr>
                        <th>No. KK</th>
                        <td><?= $warga['no_kk'] ?></td>
                    </tr>
                    <tr>
                        <th>Nama Lengkap</th>
                        <td><?= $warga['nama_lengkap'] ?></td>
                    </tr>
                    <tr>
                        <th>Tempat, Tanggal Lahir</th>
                        <td><?= $warga['tempat_lahir'] ?>, <?= formatTanggal($warga['tanggal_lahir']) ?></td>
                    </tr>
                    <tr>
                        <th>Jenis Kelamin</th>
                        <td><?= $warga['jenis_kelamin'] === 'L' ? 'Laki-laki' : 'Perempuan' ?></td>
                    </tr>
                    <tr>
                        <th>Agama</th>
                        <td><?= $warga['agama'] ?: '-' ?></td>
                    </tr>
                    <tr>
                        <th>Status Perkawinan</th>
                        <td><?= $warga['status_perkawinan'] ?></td>
                    </tr>
                    <tr>
                        <th>Pekerjaan</th>
                        <td><?= $warga['pekerjaan'] ?: '-' ?></td>
                    </tr>
                    <tr>
                        <th>Status Warga</th>
                        <td><?= getStatusBadge($warga['status_warga']) ?></td>
                    </tr>
                </table>
            </div>
        </div>

        <div class="card">
            <div class="card-header p-2">
                <h3 class="card-title">Riwayat Iuran</h3>
            </div>
            <div class="card-body table-responsive p-0">
                <table class="table table-sm">
                    <thead>
                        <tr>
                            <th>Bulan</th>
                            <th>Warga</th>
                            <th>Jenis Iuran</th>
                            <th>Jumlah</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($tagihan_kk)): ?>
                            <tr><td colspan="5" class="text-muted text-center">Belum ada riwayat iuran untuk KK ini</td></tr>
                        <?php else: ?>
                            <?php foreach ($tagihan_kk as $t): ?>
                                <tr>
                                    <td><?= getBulanIndonesia(substr($t['bulan'], 5, 2)) . ' ' . substr($t['bulan'], 0, 4) ?></td>
                                    <td><?= $t['nama_lengkap'] ?> <small class="text-muted">(<?= $t['nik'] ?>)</small></td>
                                    <td><?= $t['nama_iuran'] ?></td>
                                    <td><?= formatCurrency($t['jumlah']) ?></td>
                                    <td><?= getStatusBadge($t['status']) ?></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="card">
            <div class="card-header p-2">
                <h3 class="card-title">Riwayat Pengaduan</h3>
            </div>
            <div class="card-body table-responsive p-0">
                <table class="table table-sm">
                    <thead>
                        <tr>
                            <th>Tanggal</th>
                            <th>Judul</th>
                            <th>Kategori</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($pengaduan)): ?>
                            <tr><td colspan="4" class="text-muted text-center">Belum ada pengaduan</td></tr>
                        <?php else: ?>
                            <?php foreach ($pengaduan as $p): ?>
                                <tr>
                                    <td><?= formatTanggal($p['tanggal_pengaduan']) ?></td>
                                    <td><?= $p['judul'] ?></td>
                                    <td><?= $p['kategori'] ?></td>
                                    <td><?= getStatusBadge($p['status']) ?></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php $extraScripts = '
<script src="' . BASE_URL . 'assets/vendor/qrcodejs/qrcode.min.js"></script>
<script>
$(function() {
    new QRCode(document.getElementById("qrcode"), {
        text: "SI-WargaRT|NIK:' . $warga['nik'] . '|Nama:' . $warga['nama_lengkap'] . '",
        width: 100,
        height: 100
    });
});
</script>
'; ?>