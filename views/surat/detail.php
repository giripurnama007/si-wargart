<?php
/**
 * Detail Surat View
 */
?>

<div class="card">
    <div class="card-header">
        <h3 class="card-title">Detail Surat</h3>
        <div class="card-tools">
            <a href="<?= nginx_url('surat') ?>" class="btn btn-secondary btn-sm">
                <i class="fas fa-arrow-left mr-1"></i> Kembali
            </a>
        </div>
    </div>

    <div class="card-body">
        <div class="row">
            <div class="col-md-6">
                <table class="table table-striped">
                    <tr>
                        <th width="35%">No. Surat</th>
                        <td><?= $surat['no_surat'] ?: '-' ?></td>
                    </tr>
                    <tr>
                        <th>Jenis Surat</th>
                        <td><?= $surat['jenis_surat'] ?></td>
                    </tr>
                    <tr>
                        <th>Nama Pemohon</th>
                        <td><?= $surat['nama_lengkap'] ?></td>
                    </tr>
                    <tr>
                        <th>NIK</th>
                        <td><?= $surat['nik'] ?></td>
                    </tr>
                    <tr>
                        <th>Alamat</th>
                        <td><?= $surat['alamat'] ?></td>
                    </tr>
                    <tr>
                        <th>RT/RW</th>
                        <td><?= $surat['rt'] ?>/<?= $surat['rw'] ?></td>
                    </tr>
                </table>
            </div>
            <div class="col-md-6">
                <table class="table table-striped">
                    <tr>
                        <th width="35%">Tanggal Pengajuan</th>
                        <td><?= formatTanggal($surat['tanggal_pengajuan']) ?></td>
                    </tr>
                    <tr>
                        <th>Status</th>
                        <td><?= getStatusBadge($surat['status']) ?></td>
                    </tr>
                    <tr>
                        <th>Disetujui Ketua RT</th>
                        <td>
                            <?php if ($surat['approved_at_ketua']): ?>
                                <i class="fas fa-check text-success"></i> <?= $surat['approved_ketua_name'] ?> (<?= formatTanggal($surat['approved_at_ketua']) ?>)
                            <?php else: ?>
                                <i class="fas fa-clock text-warning"></i> Menunggu
                            <?php endif; ?>
                        </td>
                    </tr>
                    <tr>
                        <th>Disetujui Admin</th>
                        <td>
                            <?php if ($surat['approved_at_admin']): ?>
                                <i class="fas fa-check text-success"></i> <?= $surat['approved_admin_name'] ?> (<?= formatTanggal($surat['approved_at_admin']) ?>)
                            <?php else: ?>
                                <i class="fas fa-clock text-warning"></i> Menunggu
                            <?php endif; ?>
                        </td>
                    </tr>
                    <tr>
                        <th>Keterangan</th>
                        <td><?= $surat['keterangan'] ?: '-' ?></td>
                    </tr>
                </table>
            </div>
        </div>

        <div class="row mt-3">
            <div class="col-md-12">
                <div class="form-group">
                    <label><strong>Keperluan:</strong></label>
                    <p><?= nl2br($surat['keperluan']) ?></p>
                </div>
            </div>
        </div>

        <?php if (hasRole(['admin', 'ketua_rt'])): ?>
            <hr>
            <h5> Aksi</h5>

            <?php if ($surat['status'] === 'Pending' && hasRole(['ketua_rt', 'admin'])): ?>
                <form action="<?= nginx_url('surat/approve') ?>" method="POST" class="d-inline">
                    <input type="hidden" name="csrf_token" value="<?= generateCSRFToken() ?>">
                    <input type="hidden" name="id" value="<?= $surat['id'] ?>">
                    <input type="hidden" name="action" value="approve_ketua">
                    <button type="submit" class="btn btn-success">
                        <i class="fas fa-check mr-1"></i> Setujui (Ketua RT)
                    </button>
                </form>
            <?php endif; ?>

            <?php if ($surat['status'] === 'Approved_Ketua' && hasRole(['admin'])): ?>
                <form action="<?= nginx_url('surat/approve') ?>" method="POST" class="d-inline">
                    <input type="hidden" name="csrf_token" value="<?= generateCSRFToken() ?>">
                    <input type="hidden" name="id" value="<?= $surat['id'] ?>">
                    <input type="hidden" name="action" value="approve_admin">
                    <button type="submit" class="btn btn-success">
                        <i class="fas fa-check-double mr-1"></i> Finalisasi & Generate Nomor Surat
                    </button>
                </form>
            <?php endif; ?>

            <?php if (in_array($surat['status'], ['Pending', 'Approved_Ketua'])): ?>
                <button type="button" class="btn btn-danger" onclick="rejectSurat()">
                    <i class="fas fa-times mr-1"></i> Tolak
                </button>
            <?php endif; ?>

            <?php if ($surat['status'] === 'Selesai'): ?>
                <a href="<?= route_url('surat/cetak', ['id' => $surat['id']]) ?>" class="btn btn-primary" target="_blank">
                    <i class="fas fa-print mr-1"></i> Cetak Surat
                </a>
            <?php endif; ?>
        <?php endif; ?>
    </div>
</div>

<?php if (hasRole(['admin', 'ketua_rt'])): ?>
    <div class="modal fade" id="rejectModal" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <form action="<?= nginx_url('surat/approve') ?>" method="POST">
                <input type="hidden" name="csrf_token" value="<?= generateCSRFToken() ?>">
                <input type="hidden" name="id" value="<?= $surat['id'] ?>">
                <input type="hidden" name="action" value="reject">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Tolak Surat</h5>
                        <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="keterangan">Alasan Penolakan</label>
                            <textarea class="form-control" name="keterangan" rows="3" required></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-danger">Tolak</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <?php $extraScripts = '
    <script>
    function rejectSurat() {
        $("#rejectModal").modal("show");
    }
    </script>
    '; ?>
<?php endif; ?>