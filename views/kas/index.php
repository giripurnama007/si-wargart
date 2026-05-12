<?php
/**
 * Kas Index View
 */
?>

<div class="row">
    <div class="col-lg-4 col-6">
        <div class="small-box bg-success">
            <div class="inner">
                <h3><?= formatCurrency($summary['total_masuk']) ?></h3>
                <p>Kas Masuk</p>
            </div>
            <div class="icon"><i class="fas fa-arrow-down"></i></div>
        </div>
    </div>
    <div class="col-lg-4 col-6">
        <div class="small-box bg-danger">
            <div class="inner">
                <h3><?= formatCurrency($summary['total_keluar']) ?></h3>
                <p>Kas Keluar</p>
            </div>
            <div class="icon"><i class="fas fa-arrow-up"></i></div>
        </div>
    </div>
    <div class="col-lg-4 col-6">
        <div class="small-box bg-primary">
            <div class="inner">
                <h3><?= formatCurrency($summary['saldo']) ?></h3>
                <p>Saldo</p>
            </div>
            <div class="icon"><i class="fas fa-wallet"></i></div>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <h3 class="card-title">Kas RT - <?= getBulanIndonesia(substr($bulan, 5, 2)) . ' ' . substr($bulan, 0, 4) ?></h3>
        <div class="card-tools">
            <form method="GET" action="<?= BASE_URL ?>index.php" class="d-inline mr-2">
                <input type="hidden" name="route" value="kas">
                <select name="bulan" class="form-control form-control-sm" onchange="this.form.submit()">
                    <?php for ($i = 0; $i < 12; $i++): $m = date('Y-m', strtotime("-$i months")); ?>
                        <option value="<?= $m ?>" <?= $bulan === $m ? 'selected' : '' ?>><?= getBulanIndonesia(substr($m, 5, 2)) . ' ' . substr($m, 0, 4) ?></option>
                    <?php endfor; ?>
                </select>
            </form>
            <a href="<?= nginx_url('kas/masuk') ?>" class="btn btn-success btn-sm">
                <i class="fas fa-plus mr-1"></i> Kas Masuk
            </a>
            <a href="<?= nginx_url('kas/keluar') ?>" class="btn btn-danger btn-sm">
                <i class="fas fa-minus mr-1"></i> Kas Keluar
            </a>
        </div>
    </div>

    <div class="card-body table-responsive">
        <table id="dataTable" class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Tanggal</th>
                    <th>Jenis</th>
                    <th>Kategori</th>
                    <th>Keterangan</th>
                    <th>Jumlah</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($transactions)): ?>
                    <?php $no = 1; foreach ($transactions as $t): ?>
                        <tr>
                            <td><?= $no++ ?></td>
                            <td><?= formatTanggal($t['tanggal']) ?></td>
                            <td><span class="badge badge-<?= $t['jenis'] === 'Masuk' ? 'success' : 'danger' ?>"><?= $t['jenis'] ?></span></td>
                            <td><?= $t['kategori'] ?></td>
                            <td><?= $t['keterangan'] ?></td>
                            <td><?= formatCurrency($t['jumlah']) ?></td>
                            <td>
                                <a href="#" onclick="confirmDelete('<?= route_url('kas/hapus', ['id' => $t['id']]) ?>', 'Hapus transaksi ini?')" class="btn btn-danger btn-sm">
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

<div class="card mt-4">
    <div class="card-header">
        <h3 class="card-title">Rekap Tahunan</h3>
    </div>
    <div class="card-body table-responsive">
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Tahun</th>
                    <th>Kas Masuk</th>
                    <th>Kas Keluar</th>
                    <th>Saldo</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($annualSummary as $s): ?>
                    <tr>
                        <td><?= $s['tahun'] ?></td>
                        <td><?= formatCurrency($s['total_masuk']) ?></td>
                        <td><?= formatCurrency($s['total_keluar']) ?></td>
                        <td><?= formatCurrency($s['total_masuk'] - $s['total_keluar']) ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>