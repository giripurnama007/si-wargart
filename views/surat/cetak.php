<?php
/**
 * Cetak Surat View
 */
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title><?= $surat['jenis_surat'] ?> - <?= $surat['no_surat'] ?></title>
    <style>
        body { font-family: 'Times New Roman', serif; margin: 40px; font-size: 12pt; line-height: 1.5; }
        .header { text-align: center; border-bottom: 3px double #000; padding-bottom: 15px; margin-bottom: 20px; }
        .logo { width: 80px; height: 80px; object-fit: cover; border-radius: 50%; }
        .title { font-size: 16pt; font-weight: bold; margin: 5px 0; }
        .subtitle { font-size: 11pt; margin: 2px 0; }
        .content { margin-top: 30px; }
        .content p { margin: 10px 0; text-align: justify; }
        .info-table { width: 100%; border: none; }
        .info-table td { padding: 5px 0; vertical-align: top; }
        .info-table .label { width: 150px; }
        .footer { margin-top: 50px; }
        .signature { text-align: center; width: 250px; float: right; }
        .signature .name { margin-top: 60px; font-weight: bold; text-decoration: underline; }
        .qrcode { position: absolute; top: 0; right: 0; }
        @media print { body { margin: 20px; } }
    </style>
</head>
<body>
    <div class="header">
        <img src="<?= BASE_URL ?>assets/img/logo.png" class="logo" alt="Logo">
        <div class="title">PEMERINTAH KOTA/KABUPATEN</div>
        <div class="title">KELURAHAN SUKAMAJU</div>
        <div class="subtitle"><?= $surat['nama_rt'] ?> / <?= $surat['nama_rw'] ?></div>
        <div class="subtitle"><?= $surat['alamat_rt'] ?></div>
        <div class="subtitle">Telp: <?= $surat['no_telepon'] ?></div>
    </div>

    <div style="text-align: center; margin: 20px 0;">
        <strong style="font-size: 14pt; text-decoration: underline;"><?= strtoupper($surat['jenis_surat']) ?></strong>
        <br>
        <span>Nomor: <?= $surat['no_surat'] ?></span>
    </div>

    <div class="content">
        <p>Yang bertanda tangan di bawah ini, <?= $surat['nama_ketua_rt'] ?> selaku Ketua <?= $surat['nama_rt'] ?> <?= $surat['nama_rw'] ?> Kelurahan Sukamaju, dengan ini memberikan keterangan bahwa:</p>

        <table class="info-table">
            <tr>
                <td class="label">Nama Lengkap</td>
                <td>: <strong><?= $surat['nama_lengkap'] ?></strong></td>
            </tr>
            <tr>
                <td class="label">NIK</td>
                <td>: <?= $surat['nik'] ?></td>
            </tr>
            <tr>
                <td class="label">Tempat, Tgl Lahir</td>
                <td>: <?= $surat['tempat_lahir'] ?>, <?= formatTanggal($surat['tanggal_lahir']) ?></td>
            </tr>
            <tr>
                <td class="label">Jenis Kelamin</td>
                <td>: <?= $surat['jenis_kelamin'] === 'L' ? 'Laki-laki' : 'Perempuan' ?></td>
            </tr>
            <tr>
                <td class="label">Alamat</td>
                <td>: <?= $surat['alamat'] ?>, RT <?= $surat['rt'] ?>/RW <?= $surat['rw'] ?></td>
            </tr>
        </table>

        <p>Berdasarkan data yang ada di kami, benar bahwa orang tersebut di atas adalah warga kami yang beralamat di <?= $surat['alamat'] ?>, RT <?= $surat['rt'] ?>/RW <?= $surat['rw'] ?>, Kelurahan Sukamaju.</p>

        <p>Keperluan: <strong><?= nl2br($surat['keperluan']) ?></strong></p>

        <p>Demikian surat pengantar ini dibuat untuk digunakan sebagaimana mestinya.</p>
    </div>

    <div class="footer">
        <div class="signature">
            <p>Kota, <?= formatTanggal($surat['approved_at_admin'] ?? date('Y-m-d')) ?></p>
            <p>Ketua <?= $surat['nama_rt'] ?> <?= $surat['nama_rw'] ?></p>
            <p class="name"><?= $surat['nama_ketua_rt'] ?></p>
        </div>
    </div>

    <div style="clear: both;"></div>
    <div class="qrcode" style="text-align: center; margin-top: 30px;">
        <img src="<?= BASE_URL ?>uploads/pembayaran/<?= $surat['qrcode'] ?>" width="100">
        <p style="font-size: 8pt;">Scan untuk validasi</p>
    </div>

    <script>window.onload = function() { window.print(); }</script>
</body>
</html>