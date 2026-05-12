<?php
/**
 * Laporan Iuran View
 */
?>

<div class="card">
    <div class="card-header">
        <h3 class="card-title">Laporan Iuran - <?= getBulanIndonesia(substr($bulan, 5, 2)) . ' ' . substr($bulan, 0, 4) ?></h3>
        <div class="card-tools">
            <form method="GET" action="<?= BASE_URL ?>index.php" class="d-inline mr-2">
                <input type="hidden" name="route" value="laporan/iuran">
                <select name="bulan" class="form-control form-control-sm" onchange="this.form.submit()">
                    <?php for ($i = 0; $i < 12; $i++): $m = date('Y-m', strtotime("-$i months")); ?>
                        <option value="<?= $m ?>" <?= $bulan === $m ? 'selected' : '' ?>><?= getBulanIndonesia(substr($m, 5, 2)) . ' ' . substr($m, 0, 4) ?></option>
                    <?php endfor; ?>
                </select>
            </form>
            <a href="<?= route_url('laporan/export_iuran_pdf', ['bulan' => $bulan]) ?>" class="btn btn-success btn-sm" target="_blank">
                <i class="fas fa-print mr-1"></i> Cetak PDF
            </a>
        </div>
    </div>

    <div class="card-body table-responsive">
        <table id="dataTable" class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Jenis Iuran</th>
                    <th>Kategori</th>
                    <th>Total Tagihan</th>
                    <th>Lunas</th>
                    <th>Menunggak</th>
                    <th>Total Tertagih</th>
                </tr>
            </thead>
            <tbody>
                <?php $no = 1; foreach ($summary as $s): ?>
                    <tr>
                        <td><?= $no++ ?></td>
                        <td><?= $s['nama_iuran'] ?></td>
                        <td><?= $s['jenis'] ?></td>
                        <td><?= $s['total_tagihan'] ?></td>
                        <td><span class="badge badge-success"><?= $s['total_lunas'] ?></span></td>
                        <td><span class="badge badge-danger"><?= $s['total_tunggakan'] ?></span></td>
                        <td><?= formatCurrency($s['total_tertagih']) ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>