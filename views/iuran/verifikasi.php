<?php
/**
 * Verifikasi Pembayaran View
 */
?>

<div class="card">
    <div class="card-header">
        <h3 class="card-title">Verifikasi Pembayaran Pending</h3>
    </div>
    <div class="card-body table-responsive">
        <?php if (empty($pending)): ?>
            <div class="alert alert-success">
                <i class="fas fa-check"></i> Tidak ada pembayaran yang menunggu verifikasi
            </div>
        <?php else: ?>
            <table id="dataTable" class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Tanggal Bayar</th>
                        <th>Warga</th>
                        <th>Iuran</th>
                        <th>Bulan</th>
                        <th>Jumlah</th>
                        <th>Bukti</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $no = 1; foreach ($pending as $p): ?>
                        <tr>
                            <td><?= $no++ ?></td>
                            <td><?= formatTanggal($p['tanggal_bayar']) ?></td>
                            <td><?= $p['nama_lengkap'] ?><br><small class="text-muted"><?= $p['nik'] ?></small></td>
                            <td><?= $p['nama_iuran'] ?></td>
                            <td><?= getBulanIndonesia(substr($p['bulan'], 5, 2)) . ' ' . substr($p['bulan'], 0, 4) ?></td>
                            <td><?= formatCurrency($p['jumlah_bayar']) ?></td>
                            <td>
                                <?php if ($p['bukti_transfer']): ?>
                                    <a href="<?= BASE_URL ?>uploads/pembayaran/<?= $p['bukti_transfer'] ?>" target="_blank" class="btn btn-sm btn-info">
                                        <i class="fas fa-eye"></i> Lihat
                                    </a>
                                <?php else: ?>
                                    -
                                <?php endif; ?>
                            </td>
                            <td>
                                <form action="<?= nginx_url('iuran/proses_verifikasi') ?>" method="POST" class="d-inline">
                                    <input type="hidden" name="csrf_token" value="<?= generateCSRFToken() ?>">
                                    <input type="hidden" name="id" value="<?= $p['id'] ?>">
                                    <input type="hidden" name="action" value="verify">
                                    <button type="submit" class="btn btn-success btn-sm">
                                        <i class="fas fa-check"></i> Verifikasi
                                    </button>
                                </form>
                                <button type="button" class="btn btn-danger btn-sm" onclick="rejectPayment(<?= $p['id'] ?>)">
                                    <i class="fas fa-times"></i> Tolak
                                </button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>
</div>

<div class="card mt-4">
    <div class="card-header">
        <h3 class="card-title">Riwayat Pembayaran Terverifikasi</h3>
    </div>
    <div class="card-body table-responsive">
        <table id="dataTable2" class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Tanggal Bayar</th>
                    <th>Warga</th>
                    <th>Iuran</th>
                    <th>Bulan</th>
                    <th>Jumlah</th>
                    <th>Diverifikasi Oleh</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <?php $no = 1; foreach ($verified as $v): ?>
                    <tr>
                        <td><?= $no++ ?></td>
                        <td><?= formatTanggal($v['tanggal_bayar']) ?></td>
                        <td><?= $v['nama_lengkap'] ?></td>
                        <td><?= $v['nama_iuran'] ?></td>
                        <td><?= getBulanIndonesia(substr($v['bulan'], 5, 2)) . ' ' . substr($v['bulan'], 0, 4) ?></td>
                        <td><?= formatCurrency($v['jumlah_bayar']) ?></td>
                        <td><?= $v['verified_by_name'] ?></td>
                        <td><?= getStatusBadge($v['status']) ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<div class="modal fade" id="rejectModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <form action="<?= nginx_url('iuran/proses_verifikasi') ?>" method="POST">
            <input type="hidden" name="csrf_token" value="<?= generateCSRFToken() ?>">
            <input type="hidden" name="id" id="reject_id">
            <input type="hidden" name="action" value="reject">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Tolak Pembayaran</h5>
                    <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label for="keterangan">Alasan Penolakan</label>
                        <textarea class="form-control" id="keterangan" name="keterangan" rows="3" required></textarea>
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
function rejectPayment(id) {
    $("#reject_id").val(id);
    $("#rejectModal").modal("show");
}
</script>
'; ?>