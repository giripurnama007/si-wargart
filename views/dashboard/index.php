<?php
/**
 * Dashboard View
 * SI-WargaRT - Sistem Informasi Warga RT
 */

$user = getCurrentUser();
?>

<div class="row">
    <!-- Stats Cards -->
    <div class="col-lg-3 col-6">
        <div class="small-box bg-success">
            <div class="inner">
                <h3><?= $stats['total_warga'] ?></h3>
                <p>Total Warga Aktif</p>
            </div>
            <div class="icon">
                <i class="fas fa-users"></i>
            </div>
            <a href="<?= BASE_URL ?>warga" class="small-box-footer">Selengkapnya <i class="fas fa-arrow-circle-right"></i></a>
        </div>
    </div>

    <div class="col-lg-3 col-6">
        <div class="small-box bg-info">
            <div class="inner">
                <h3><?= $stats['total_kk'] ?></h3>
                <p>Total Kartu Keluarga</p>
            </div>
            <div class="icon">
                <i class="fas fa-id-card"></i>
            </div>
            <a href="<?= BASE_URL ?>kk" class="small-box-footer">Selengkapnya <i class="fas fa-arrow-circle-right"></i></a>
        </div>
    </div>

    <div class="col-lg-3 col-6">
        <div class="small-box bg-warning">
            <div class="inner">
                <h3><?= formatCurrency($stats['iuran_bulan_ini']) ?></h3>
                <p>Iuran Bulan Ini</p>
            </div>
            <div class="icon">
                <i class="fas fa-money-bill-wave"></i>
            </div>
            <a href="<?= BASE_URL ?>iuran" class="small-box-footer">Selengkapnya <i class="fas fa-arrow-circle-right"></i></a>
        </div>
    </div>

    <div class="col-lg-3 col-6">
        <div class="small-box bg-danger">
            <div class="inner">
                <h3><?= formatCurrency($stats['total_tunggakan']) ?></h3>
                <p>Total Tunggakan</p>
            </div>
            <div class="icon">
                <i class="fas fa-exclamation-triangle"></i>
            </div>
            <a href="<?= BASE_URL ?>laporan/tunggakan" class="small-box-footer">Selengkapnya <i class="fas fa-arrow-circle-right"></i></a>
        </div>
    </div>
</div>

<div class="row">
    <!-- Chart Kas -->
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title"><i class="fas fa-chart-line mr-2"></i> Grafik Kas RT</h3>
            </div>
            <div class="card-body">
                <canvas id="kasChart" height="100"></canvas>
            </div>
        </div>
    </div>

    <!-- Quick Stats -->
    <div class="col-lg-4">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title"><i class="fas fa-info-circle mr-2"></i> Statistik</h3>
            </div>
            <div class="card-body">
                <div class="info-box">
                    <span class="info-box-icon bg-warning"><i class="fas fa-clock"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Tagihan Pending</span>
                        <span class="info-box-number"><?= $stats['tagihan_pending'] ?></span>
                    </div>
                </div>
                <div class="info-box">
                    <span class="info-box-icon bg-danger"><i class="fas fa-exclamation-circle"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Pengaduan Aktif</span>
                        <span class="info-box-number"><?= $stats['pengaduan_aktif'] ?></span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <!-- Pengumuman Terbaru -->
    <div class="col-lg-6">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title"><i class="fas fa-bullhorn mr-2"></i> Pengumuman Terbaru</h3>
                <div class="card-tools">
                    <a href="<?= BASE_URL ?>pengumuman" class="btn btn-tool"><i class="fas fa-eye"></i></a>
                </div>
            </div>
            <div class="card-body p-0">
                <?php if (empty($pengumumanTerbaru)): ?>
                    <div class="p-3 text-muted">Belum ada pengumuman</div>
                <?php else: ?>
                    <ul class="todo-list">
                        <?php foreach ($pengumumanTerbaru as $p): ?>
                            <li>
                                <span class="text"><?= $p['judul'] ?></span>
                                <small class="badge badge-info"><i class="far fa-calendar"></i> <?= formatTanggal($p['tanggal'], 'd M') ?></small>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Kegiatan Akan Datang -->
    <div class="col-lg-6">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title"><i class="fas fa-calendar-alt mr-2"></i> Kegiatan Akan Datang</h3>
                <div class="card-tools">
                    <a href="<?= BASE_URL ?>kegiatan" class="btn btn-tool"><i class="fas fa-eye"></i></a>
                </div>
            </div>
            <div class="card-body p-0">
                <?php if (empty($kegiatanAkanDatang)): ?>
                    <div class="p-3 text-muted">Belum ada kegiatan terjadwal</div>
                <?php else: ?>
                    <ul class="products-list product-list-in-card pl-2 pr-2">
                        <?php foreach ($kegiatanAkanDatang as $k): ?>
                            <li class="item">
                                <div class="product-info">
                                    <a href="<?= route_url('kegiatan/detail', ['id' => $k['id']]) ?>" class="product-title"><?= $k['judul'] ?></a>
                                    <span class="product-description">
                                        <i class="far fa-calendar"></i> <?= formatTanggal($k['tanggal']) ?>
                                        <?php if ($k['waktu']): ?>
                                            | <i class="far fa-clock"></i> <?= date('H:i', strtotime($k['waktu'])) ?>
                                        <?php endif; ?>
                                        <?php if ($k['lokasi']): ?>
                                            | <i class="fas fa-map-marker-alt"></i> <?= $k['lokasi'] ?>
                                        <?php endif; ?>
                                    </span>
                                </div>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <!-- Recent Pembayaran -->
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title"><i class="fas fa-receipt mr-2"></i> Pembayaran Terbaru</h3>
            </div>
            <div class="card-body table-responsive p-0">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Warga</th>
                            <th>Iuran</th>
                            <th>Jumlah</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($recentPayments)): ?>
                            <tr><td colspan="3" class="text-muted">Belum ada pembayaran</td></tr>
                        <?php else: ?>
                            <?php foreach ($recentPayments as $p): ?>
                                <tr>
                                    <td><?= $p['nama_lengkap'] ?></td>
                                    <td><?= $p['nama_iuran'] ?></td>
                                    <td><?= formatCurrency($p['jumlah_bayar']) ?></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Recent Pengaduan -->
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title"><i class="fas fa-exclamation-circle mr-2"></i> Pengaduan Terbaru</h3>
            </div>
            <div class="card-body table-responsive p-0">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Judul</th>
                            <th>Pelapor</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($recentPengaduan)): ?>
                            <tr><td colspan="3" class="text-muted">Belum ada pengaduan</td></tr>
                        <?php else: ?>
                            <?php foreach ($recentPengaduan as $p): ?>
                                <tr>
                                    <td><?= substr($p['judul'], 0, 30) ?>...</td>
                                    <td><?= $p['nama_lengkap'] ?></td>
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
<script>
$(function () {
    // Kas Chart
    var ctx = document.getElementById("kasChart").getContext("2d");
    var kasChart = new Chart(ctx, {
        type: "bar",
        data: {
            labels: ' . json_encode($chartData['bulanLabels']) . ',
            datasets: [
                {
                    label: "Kas Masuk",
                    backgroundColor: "#28a745",
                    data: ' . json_encode($chartData['kasMasuk']) . '
                },
                {
                    label: "Kas Keluar",
                    backgroundColor: "#dc3545",
                    data: ' . json_encode($chartData['kasKeluar']) . '
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                yAxes: [{
                    ticks: {
                        callback: function(value) {
                            return "Rp " + value.toLocaleString("id-ID");
                        }
                    }
                }]
            },
            tooltips: {
                callbacks: {
                    label: function(context) {
                        return context.dataset.label + ": Rp " + context.raw.toLocaleString("id-ID");
                    }
                }
            }
        }
    });
});
</script>
'; ?>