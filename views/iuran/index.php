<?php
/**
 * Iuran Index View
 */
?>

<div class="row">
    <div class="col-lg-3 col-6">
        <div class="small-box bg-success">
            <div class="inner">
                <h3><?= formatCurrency($summary['total_lunas']) ?></h3>
                <p>Lunas</p>
            </div>
            <div class="icon"><i class="fas fa-check-circle"></i></div>
        </div>
    </div>
    <div class="col-lg-3 col-6">
        <div class="small-box bg-warning">
            <div class="inner">
                <h3><?= formatCurrency($summary['total_pending']) ?></h3>
                <p>Pending</p>
            </div>
            <div class="icon"><i class="fas fa-clock"></i></div>
        </div>
    </div>
    <div class="col-lg-3 col-6">
        <div class="small-box bg-danger">
            <div class="inner">
                <h3><?= formatCurrency($summary['total_tunggakan']) ?></h3>
                <p>Menunggak</p>
            </div>
            <div class="icon"><i class="fas fa-exclamation-triangle"></i></div>
        </div>
    </div>
    <div class="col-lg-3 col-6">
        <div class="small-box bg-info">
            <div class="inner">
                <h3><?= $summary['total_tagihan'] ?></h3>
                <p>Total Tagihan</p>
            </div>
            <div class="icon"><i class="fas fa-file-invoice"></i></div>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <h3 class="card-title">Iuran RT - <?= getBulanIndonesia(substr($bulan, 5, 2)) . ' ' . substr($bulan, 0, 4) ?></h3>
        <div class="card-tools">
            <form method="GET" action="<?= BASE_URL ?>index.php" class="d-inline mr-2">
                <input type="hidden" name="route" value="iuran">
                <select name="bulan" class="form-control form-control-sm" onchange="this.form.submit()">
                    <?php for ($i = 0; $i < 12; $i++): $m = date('Y-m', strtotime("-$i months")); ?>
                        <option value="<?= $m ?>" <?= $bulan === $m ? 'selected' : '' ?>><?= getBulanIndonesia(substr($m, 5, 2)) . ' ' . substr($m, 0, 4) ?></option>
                    <?php endfor; ?>
                </select>
            </form>
            <?php if (hasRole(['admin', 'ketua_rt'])): ?>
                <a href="<?= nginx_url('iuran/generate') ?>" class="btn btn-primary btn-sm">
                    <i class="fas fa-plus mr-1"></i> Generate Tagihan
                </a>
            <?php endif; ?>
            <a href="<?= nginx_url('iuran/pembayaran') ?>" class="btn btn-success btn-sm">
                <i class="fas fa-money-bill mr-1"></i> <?= hasRole(['warga']) ? 'Bayar Iuran' : 'Input Pembayaran' ?>
            </a>
            <?php if (hasRole(['admin', 'ketua_rt'])): ?>
                <a href="<?= nginx_url('iuran/verifikasi') ?>" class="btn btn-info btn-sm">
                    <i class="fas fa-check mr-1"></i> Verifikasi
                </a>
            <?php endif; ?>
        </div>
    </div>

    <div class="card-body table-responsive">
        <table id="dataTable" class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Warga</th>
                    <th>NIK</th>
                    <th>Jenis Iuran</th>
                    <th>Jumlah</th>
                    <th>Status</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($tagihan)): ?>
                    <?php $no = 1; foreach ($tagihan as $t): ?>
                        <tr>
                            <td><?= $no++ ?></td>
                            <td><?= $t['nama_lengkap'] ?></td>
                            <td><?= $t['nik'] ?></td>
                            <td><?= $t['nama_iuran'] ?> (<?= $t['jenis'] ?>)</td>
                            <td><?= formatCurrency($t['jumlah']) ?></td>
                            <td><?= getStatusBadge($t['status']) ?></td>
                            <td>
                                <?php if (hasRole(['admin', 'ketua_rt'])): ?>
                                    <a href="#" onclick="confirmDelete('<?= route_url('iuran/hapus_tagihan', ['id' => $t['id']]) ?>', 'Hapus tagihan ini?')" class="btn btn-danger btn-sm">
                                        <i class="fas fa-trash"></i>
                                    </a>
                                <?php elseif ($t['status'] !== 'Lunas'): ?>
                                    <a href="<?= route_url('iuran/pembayaran', ['bulan' => $t['bulan']]) ?>" class="btn btn-success btn-sm">
                                        <i class="fas fa-wallet mr-1"></i> Bayar
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