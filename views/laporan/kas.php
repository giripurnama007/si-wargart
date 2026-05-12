<?php
/**
 * Laporan Kas View
 */
?>

<div class="row">
    <div class="col-lg-4 col-6">
        <div class="small-box bg-success">
            <div class="inner">
                <h3><?= formatCurrency($summary['total_masuk']) ?></h3>
                <p>Total Kas Masuk</p>
            </div>
            <div class="icon"><i class="fas fa-arrow-down"></i></div>
        </div>
    </div>
    <div class="col-lg-4 col-6">
        <div class="small-box bg-danger">
            <div class="inner">
                <h3><?= formatCurrency($summary['total_keluar']) ?></h3>
                <p>Total Kas Keluar</p>
            </div>
            <div class="icon"><i class="fas fa-arrow-up"></i></div>
        </div>
    </div>
    <div class="col-lg-4 col-6">
        <div class="small-box bg-primary">
            <div class="inner">
                <h3><?= formatCurrency($saldo) ?></h3>
                <p>Saldo</p>
            </div>
            <div class="icon"><i class="fas fa-wallet"></i></div>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <h3 class="card-title">Laporan Kas RT</h3>
        <div class="card-tools">
            <form method="GET" action="<?= BASE_URL ?>index.php" class="d-inline mr-2">
                <input type="hidden" name="route" value="laporan/kas">
                <div class="input-group">
                    <input type="date" name="tanggal_awal" class="form-control" value="<?= $tanggal_awal ?>">
                    <span class="input-group-text">s/d</span>
                    <input type="date" name="tanggal_akhir" class="form-control" value="<?= $tanggal_akhir ?>">
                    <button type="submit" class="btn btn-primary">Filter</button>
                </div>
            </form>
            <a href="<?= route_url('laporan/export_kas_pdf', ['tanggal_awal' => $tanggal_awal, 'tanggal_akhir' => $tanggal_akhir]) ?>" class="btn btn-success btn-sm" target="_blank">
                <i class="fas fa-print mr-1"></i> Cetak PDF
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
                </tr>
            </thead>
            <tbody>
                <?php $no = 1; foreach ($transaksi as $t): ?>
                    <tr>
                        <td><?= $no++ ?></td>
                        <td><?= formatTanggal($t['tanggal']) ?></td>
                        <td><span class="badge badge-<?= $t['jenis'] === 'Masuk' ? 'success' : 'danger' ?>"><?= $t['jenis'] ?></span></td>
                        <td><?= $t['kategori'] ?></td>
                        <td><?= $t['keterangan'] ?></td>
                        <td style="text-align: right;"><?= formatCurrency($t['jumlah']) ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
            <tfoot>
                <tr>
                    <th colspan="5" class="text-right">Total Kas Masuk:</th>
                    <th class="text-right text-success"><?= formatCurrency($summary['total_masuk']) ?></th>
                </tr>
                <tr>
                    <th colspan="5" class="text-right">Total Kas Keluar:</th>
                    <th class="text-right text-danger"><?= formatCurrency($summary['total_keluar']) ?></th>
                </tr>
                <tr>
                    <th colspan="5" class="text-right">Saldo:</th>
                    <th class="text-right text-primary"><?= formatCurrency($saldo) ?></th>
                </tr>
            </tfoot>
        </table>
    </div>
</div>