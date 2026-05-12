<?php
/**
 * Generate Tagihan View
 */
?>

<div class="card">
    <div class="card-header">
        <h3 class="card-title">Generate Tagihan Bulanan</h3>
    </div>

    <form action="<?= nginx_url('iuran/proses_generate') ?>" method="POST">
        <input type="hidden" name="csrf_token" value="<?= generateCSRFToken() ?>">

        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="bulan">Bulan <span class="text-danger">*</span></label>
                        <input type="month" class="form-control" id="bulan" name="bulan" value="<?= date('Y-m') ?>" required>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="jenis_iuran_id">Jenis Iuran <span class="text-danger">*</span></label>
                        <select class="form-control" id="jenis_iuran_id" name="jenis_iuran_id" required onchange="updateJumlah()">
                            <option value="">Pilih Jenis Iuran</option>
                            <?php foreach ($jenisIuran as $ji): ?>
                                <option value="<?= $ji['id'] ?>" data-jumlah="<?= $ji['jumlah'] ?>">
                                    <?= $ji['nama_iuran'] ?> - <?= formatCurrency($ji['jumlah']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
            </div>

            <div class="form-group">
                <label for="jumlah">Jumlah <span class="text-danger">*</span></label>
                <input type="number" class="form-control" id="jumlah" name="jumlah" step="0.01" required>
            </div>

            <div class="alert alert-info">
                <i class="fas fa-info"></i>
                Tagihan akan digenerate untuk <strong>seluruh <?= count($warga) ?> warga aktif</strong>.
                Jika tagihan sudah ada, akan dilewati.
            </div>
        </div>

        <div class="card-footer">
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-bolt mr-1"></i> Generate Tagihan
            </button>
            <a href="<?= nginx_url('iuran') ?>" class="btn btn-secondary">
                <i class="fas fa-arrow-left mr-1"></i> Kembali
            </a>
        </div>
    </form>
</div>

<?php $extraScripts = '
<script>
function updateJumlah() {
    var select = document.getElementById("jenis_iuran_id");
    var option = select.options[select.selectedIndex];
    var jumlah = option.getAttribute("data-jumlah");
    if (jumlah) {
        document.getElementById("jumlah").value = jumlah;
    }
}
</script>
'; ?>