<?php
/**
 * Laporan Tunggakan View
 */
?>

<div class="card">
    <div class="card-header">
        <h3 class="card-title">Daftar Tunggakan Iuran</h3>
        <div class="card-tools">
            <a href="<?= nginx_url('laporan/export_tunggakan_pdf') ?>" class="btn btn-success btn-sm" target="_blank">
                <i class="fas fa-print mr-1"></i> Cetak PDF
            </a>
        </div>
    </div>

    <div class="card-body table-responsive">
        <?php if (empty($grouped)): ?>
            <div class="alert alert-success">
                <i class="fas fa-check"></i> Tidak ada warga yang menunggak!
            </div>
        <?php else: ?>
            <?php foreach ($grouped as $item): ?>
                <div class="card card-danger mb-3">
                    <div class="card-header">
                        <h3 class="card-title"><?= $item['warga']['nama_lengkap'] ?></h3>
                        <span class="float-right">Total Tunggakan: <strong><?= formatCurrency($item['total_tunggakan']) ?></strong></span>
                    </div>
                    <div class="card-body p-0">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Bulan</th>
                                    <th>Jenis Iuran</th>
                                    <th>Jumlah</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($item['detail'] as $d): ?>
                                    <tr>
                                        <td><?= getBulanIndonesia(substr($d['bulan'], 5, 2)) . ' ' . substr($d['bulan'], 0, 4) ?></td>
                                        <td><?= $d['nama_iuran'] ?></td>
                                        <td><?= formatCurrency($d['jumlah']) ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                        <div class="p-3">
                            <strong>Kontak:</strong> <?= $item['warga']['no_hp'] ?: '-' ?><br>
                            <strong>Alamat:</strong> <?= $item['warga']['alamat'] ?: '-' ?>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>