<?php
/**
 * Pembayaran Iuran View
 */
?>

<div class="card">
    <div class="card-header">
        <h3 class="card-title">Input Pembayaran</h3>
    </div>

    <form action="<?= nginx_url('iuran/proses_pembayaran') ?>" method="POST" enctype="multipart/form-data">
        <input type="hidden" name="csrf_token" value="<?= generateCSRFToken() ?>">

        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="id_warga">Pilih Warga <span class="text-danger">*</span></label>
                        <select class="form-control select2" id="id_warga" name="id_warga" required onchange="loadTagihan()" <?= hasRole(['warga']) ? 'disabled' : '' ?>>
                            <?php if (!hasRole(['warga'])): ?>
                                <option value="">Pilih Warga</option>
                            <?php endif; ?>
                            <?php foreach ($warga as $w): ?>
                                <option value="<?= $w['id'] ?>" <?= (hasRole(['warga']) && $w['id'] == ($_SESSION['id_warga'] ?? 0)) ? 'selected' : '' ?>><?= $w['nama_lengkap'] ?> - <?= $w['nik'] ?></option>
                            <?php endforeach; ?>
                        </select>
                        <?php if (hasRole(['warga'])): ?>
                            <input type="hidden" name="id_warga" value="<?= $warga[0]['id'] ?? '' ?>">
                        <?php endif; ?>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="bulan">Bulan <span class="text-danger">*</span></label>
                        <input type="month" class="form-control" id="bulan" name="bulan" value="<?= $selectedBulan ?? date('Y-m') ?>" onchange="loadTagihan()">
                    </div>
                </div>
            </div>

            <div class="form-group">
                <label for="id_tagihan">Tagihan <span class="text-danger">*</span></label>
                <select class="form-control" id="id_tagihan" name="id_tagihan" required>
                    <option value="">Pilih Tagihan</option>
                </select>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="jumlah_bayar">Jumlah Bayar <span class="text-danger">*</span></label>
                        <input type="number" class="form-control" id="jumlah_bayar" name="jumlah_bayar" step="0.01" required>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="tanggal_bayar">Tanggal Bayar <span class="text-danger">*</span></label>
                        <input type="date" class="form-control" id="tanggal_bayar" name="tanggal_bayar" value="<?= date('Y-m-d') ?>" required>
                    </div>
                </div>
            </div>

            <div class="form-group">
                <label for="metode_bayar">Metode Bayar</label>
                <select class="form-control" id="metode_bayar" name="metode_bayar">
                    <option value="Transfer">Transfer</option>
                    <option value="Cash">Cash</option>
                    <option value="E-Wallet">E-Wallet</option>
                </select>
            </div>

            <div class="form-group">
                <label for="bukti_transfer">Bukti Transfer</label>
                <div class="input-group">
                    <div class="custom-file">
                        <input type="file" class="custom-file-input" id="bukti_transfer" name="bukti_transfer" accept="image/*,.pdf">
                        <label class="custom-file-label" for="bukti_transfer">Pilih file...</label>
                    </div>
                </div>
            </div>
        </div>

        <div class="card-footer">
            <button type="submit" class="btn btn-success">
                <i class="fas fa-save mr-1"></i> Simpan Pembayaran
            </button>
        </div>
    </form>
</div>

<div class="card mt-4">
    <div class="card-header">
        <h3 class="card-title">Riwayat Pembayaran</h3>
    </div>
    <div class="card-body table-responsive">
        <table id="dataTable" class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Tanggal</th>
                    <th>Warga</th>
                    <th>Iuran</th>
                    <th>Bulan</th>
                    <th>Jumlah</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <?php $no = 1; foreach ($pembayaran as $p): ?>
                    <tr>
                        <td><?= $no++ ?></td>
                        <td><?= formatTanggal($p['tanggal_bayar']) ?></td>
                        <td><?= $p['nama_lengkap'] ?></td>
                        <td><?= $p['nama_iuran'] ?></td>
                        <td><?= getBulanIndonesia(substr($p['bulan'], 5, 2)) . ' ' . substr($p['bulan'], 0, 4) ?></td>
                        <td><?= formatCurrency($p['jumlah_bayar']) ?></td>
                        <td><?= getStatusBadge($p['status']) ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<?php $extraScripts = '
<script>
$(document).ready(function() {
    if ($("#id_warga").val() && $("#bulan").val()) {
        loadTagihan();
    }
});

function loadTagihan() {
    var id_warga = $("#id_warga").val();
    var bulan = $("#bulan").val();
    if (id_warga && bulan) {
        $.ajax({
            url: "' . route_url('iuran/get_tagihan') . '&id_warga=" + id_warga + "&bulan=" + bulan,
            success: function(response) {
                try {
                    var res = typeof response === "string" ? JSON.parse(response) : response;
                    var options = "<option value=\"\">Pilih Tagihan</option>";
                    if (res.data && res.data.tagihan) {
                        res.data.tagihan.forEach(function(t) {
                            options += "<option value=\"" + t.id + "\" data-jumlah=\"" + t.jumlah + "\">" + t.nama_iuran + " - Rp " + parseInt(t.jumlah).toLocaleString() + "</option>";
                        });
                    }
                    $("#id_tagihan").html(options);
                } catch (e) {}
            }
        });
    }
}

$(document).on("change", "#id_tagihan", function() {
    var option = $(this).find("option:selected");
    var jumlah = option.data("jumlah");
    if (jumlah) {
        $("#jumlah_bayar").val(jumlah);
    }
});
</script>
'; ?>