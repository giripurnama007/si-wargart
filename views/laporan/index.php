<?php
/**
 * Laporan Index View
 */
?>

<div class="row">
    <div class="col-md-6">
        <div class="card">
            <div class="card-body text-center">
                <i class="fas fa-chart-line fa-4x text-info mb-3"></i>
                <h4>Laporan Kas</h4>
                <p>Laporan kas masuk dan keluar berdasarkan periode</p>
                <a href="<?= nginx_url('laporan/kas') ?>" class="btn btn-info">
                    <i class="fas fa-eye mr-1"></i> Lihat Laporan
                </a>
            </div>
        </div>
    </div>

    <div class="col-md-6">
        <div class="card">
            <div class="card-body text-center">
                <i class="fas fa-file-invoice-dollar fa-4x text-success mb-3"></i>
                <h4>Laporan Iuran</h4>
                <p>Rekapitulasi iuran bulanan dan pembayaran warga</p>
                <a href="<?= nginx_url('laporan/iuran') ?>" class="btn btn-success">
                    <i class="fas fa-eye mr-1"></i> Lihat Laporan
                </a>
            </div>
        </div>
    </div>

    <div class="col-md-6">
        <div class="card">
            <div class="card-body text-center">
                <i class="fas fa-exclamation-triangle fa-4x text-danger mb-3"></i>
                <h4>Laporan Tunggakan</h4>
                <p>Daftar warga yang menunggak iuran</p>
                <a href="<?= nginx_url('laporan/tunggakan') ?>" class="btn btn-danger">
                    <i class="fas fa-eye mr-1"></i> Lihat Laporan
                </a>
            </div>
        </div>
    </div>

    <div class="col-md-6">
        <div class="card">
            <div class="card-body text-center">
                <i class="fas fa-print fa-4x text-secondary mb-3"></i>
                <h4>Cetak Laporan</h4>
                <p>Export rekapitulasi Kas, Iuran, dan Tunggakan ke PDF</p>
                <form method="GET" action="<?= BASE_URL ?>index.php" target="_blank">
                    <input type="hidden" name="route" value="laporan/export_pdf">
                    <div class="input-group justify-content-center mb-3" style="max-width: 250px; margin: 0 auto;">
                        <input type="month" name="bulan" class="form-control" value="<?= date('Y-m') ?>" required>
                        <div class="input-group-append">
                            <button type="submit" class="btn btn-secondary"><i class="fas fa-file-pdf mr-1"></i> Export</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>