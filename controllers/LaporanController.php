<?php
/**
 * Laporan Controller
 * SI-WargaRT - Sistem Informasi Warga RT
 */

class LaporanController {

    private $db;

    public function __construct() {
        requireRole(['admin', 'ketua_rt']);
        $this->db = getDb();
    }

    /**
     * Index - Pilih Laporan
     */
    public function index() {
        $pageTitle = 'Laporan';
        $breadcrumb = '<ol class="breadcrumb float-sm-right">
            <li class="breadcrumb-item"><a href="' . nginx_url('dashboard') . '">Home</a></li>
            <li class="breadcrumb-item active">Laporan</li>
        </ol>';

        ob_start();
        include VIEWSPATH . 'laporan/index.php';
        $content = ob_get_clean();

        include LAYOUTS . 'main_layout.php';
    }

    /**
     * Laporan Kas
     */
    public function kas() {
        $tanggal_awal = sanitize($_GET['tanggal_awal'] ?? date('Y-01-01'));
        $tanggal_akhir = sanitize($_GET['tanggal_akhir'] ?? date('Y-m-d'));

        $pageTitle = 'Laporan Kas';
        $breadcrumb = '<ol class="breadcrumb float-sm-right">
            <li class="breadcrumb-item"><a href="' . nginx_url('dashboard') . '">Home</a></li>
            <li class="breadcrumb-item"><a href="' . nginx_url('laporan') . '">Laporan</a></li>
            <li class="breadcrumb-item active">Kas</li>
        </ol>';

        // Get summary
        $stmt = $this->db->prepare("
            SELECT
                COALESCE(SUM(CASE WHEN jenis = 'Masuk' THEN jumlah ELSE 0 END), 0) as total_masuk,
                COALESCE(SUM(CASE WHEN jenis = 'Keluar' THEN jumlah ELSE 0 END), 0) as total_keluar
            FROM kas_rt
            WHERE tanggal BETWEEN ? AND ?
        ");
        $stmt->execute([$tanggal_awal, $tanggal_akhir]);
        $summary = $stmt->fetch();

        // Get transactions
        $stmt = $this->db->prepare("
            SELECT * FROM kas_rt
            WHERE tanggal BETWEEN ? AND ?
            ORDER BY tanggal DESC, created_at DESC
        ");
        $stmt->execute([$tanggal_awal, $tanggal_akhir]);
        $transaksi = $stmt->fetchAll();

        $saldo = $summary['total_masuk'] - $summary['total_keluar'];

        ob_start();
        include VIEWSPATH . 'laporan/kas.php';
        $content = ob_get_clean();

        include LAYOUTS . 'main_layout.php';
    }

    /**
     * Laporan Iuran
     */
    public function iuran() {
        $bulan = sanitize($_GET['bulan'] ?? date('Y-m'));

        $pageTitle = 'Laporan Iuran';
        $breadcrumb = '<ol class="breadcrumb float-sm-right">
            <li class="breadcrumb-item"><a href="' . nginx_url('dashboard') . '">Home</a></li>
            <li class="breadcrumb-item"><a href="' . nginx_url('laporan') . '">Laporan</a></li>
            <li class="breadcrumb-item active">Iuran</li>
        </ol>';

        // Get summary by jenis
        $stmt = $this->db->query("
            SELECT j.id, j.nama_iuran, j.jenis, j.jumlah,
                   COUNT(t.id) as total_tagihan,
                   COUNT(CASE WHEN t.status = 'Lunas' THEN 1 END) as total_lunas,
                   COUNT(CASE WHEN t.status = 'Menunggak' THEN 1 END) as total_tunggakan,
                   COALESCE(SUM(CASE WHEN p.status = 'Verified' THEN p.jumlah_bayar ELSE 0 END), 0) as total_tertagih
            FROM jenis_iuran j
            LEFT JOIN tagihan_iuran t ON j.id = t.id_jenis_iuran AND t.bulan = '$bulan'
            LEFT JOIN pembayaran p ON t.id = p.id_tagihan AND p.status = 'Verified'
            WHERE j.is_active = 1
            GROUP BY j.id
        ");
        $summary = $stmt->fetchAll();

        // Get detail
        $stmt = $this->db->prepare("
            SELECT t.*, w.nama_lengkap, w.nik, j.nama_iuran, j.jenis,
                   p.jumlah_bayar as sudah_bayar, p.tanggal_bayar, p.status as status_bayar
            FROM tagihan_iuran t
            INNER JOIN warga w ON t.id_warga = w.id
            INNER JOIN jenis_iuran j ON t.id_jenis_iuran = j.id
            LEFT JOIN pembayaran p ON t.id = p.id_tagihan AND p.status = 'Verified'
            WHERE t.bulan = ?
            ORDER BY j.nama_iuran, w.nama_lengkap
        ");
        $stmt->execute([$bulan]);
        $detail = $stmt->fetchAll();

        ob_start();
        include VIEWSPATH . 'laporan/iuran.php';
        $content = ob_get_clean();

        include LAYOUTS . 'main_layout.php';
    }

    /**
     * Laporan Tunggakan
     */
    public function tunggakan() {
        $pageTitle = 'Laporan Tunggakan';
        $breadcrumb = '<ol class="breadcrumb float-sm-right">
            <li class="breadcrumb-item"><a href="' . nginx_url('dashboard') . '">Home</a></li>
            <li class="breadcrumb-item"><a href="' . nginx_url('laporan') . '">Laporan</a></li>
            <li class="breadcrumb-item active">Tunggakan</li>
        </ol>';

        // Get tunggakan
        $stmt = $this->db->query("
            SELECT t.*, w.nama_lengkap, w.nik, w.no_hp, w.alamat, j.nama_iuran, j.jenis, j.jumlah
            FROM tagihan_iuran t
            INNER JOIN warga w ON t.id_warga = w.id
            INNER JOIN jenis_iuran j ON t.id_jenis_iuran = j.id
            WHERE t.status = 'Menunggak'
            ORDER BY w.nama_lengkap, t.bulan DESC
        ");
        $tunggakan = $stmt->fetchAll();

        // Group by warga
        $grouped = [];
        foreach ($tunggakan as $t) {
            $key = $t['id_warga'];
            if (!isset($grouped[$key])) {
                $grouped[$key] = [
                    'warga' => [
                        'id' => $t['id_warga'],
                        'nama_lengkap' => $t['nama_lengkap'],
                        'nik' => $t['nik'],
                        'no_hp' => $t['no_hp'],
                        'alamat' => $t['alamat']
                    ],
                    'total_tunggakan' => 0,
                    'detail' => []
                ];
            }
            $grouped[$key]['total_tunggakan'] += $t['jumlah'];
            $grouped[$key]['detail'][] = $t;
        }

        ob_start();
        include VIEWSPATH . 'laporan/tunggakan.php';
        $content = ob_get_clean();

        include LAYOUTS . 'main_layout.php';
    }

    /**
     * Export PDF Kas
     */
    public function export_kas_pdf() {
        $tanggal_awal = sanitize($_GET['tanggal_awal'] ?? date('Y-01-01'));
        $tanggal_akhir = sanitize($_GET['tanggal_akhir'] ?? date('Y-m-d'));

        $stmt = $this->db->prepare("
            SELECT * FROM kas_rt
            WHERE tanggal BETWEEN ? AND ?
            ORDER BY tanggal DESC
        ");
        $stmt->execute([$tanggal_awal, $tanggal_akhir]);
        $transaksi = $stmt->fetchAll();

        $stmt = $this->db->prepare("
            SELECT
                COALESCE(SUM(CASE WHEN jenis = 'Masuk' THEN jumlah ELSE 0 END), 0) as total_masuk,
                COALESCE(SUM(CASE WHEN jenis = 'Keluar' THEN jumlah ELSE 0 END), 0) as total_keluar
            FROM kas_rt
            WHERE tanggal BETWEEN ? AND ?
        ");
        $stmt->execute([$tanggal_awal, $tanggal_akhir]);
        $summary = $stmt->fetch();

        $saldo = $summary['total_masuk'] - $summary['total_keluar'];

        ob_start();
        ?>
        <html>
        <head>
            <title>Laporan Kas RT</title>
            <style>
                body { font-family: Arial, sans-serif; font-size: 12px; }
                table { width: 100%; border-collapse: collapse; margin-top: 20px; }
                th, td { border: 1px solid #333; padding: 8px; }
                th { background: #f0f0f0; }
                .header { text-align: center; }
                .summary { margin-top: 20px; }
            </style>
        </head>
        <body>
            <div class="header">
                <h2>LAPORAN KAS RT</h2>
                <p>Periode: <?= formatTanggal($tanggal_awal) ?> - <?= formatTanggal($tanggal_akhir) ?></p>
            </div>
            <table>
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
                        <td><?= $t['jenis'] ?></td>
                        <td><?= $t['kategori'] ?></td>
                        <td><?= $t['keterangan'] ?></td>
                        <td style="text-align: right;"><?= formatCurrency($t['jumlah']) ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <div class="summary">
                <p><strong>Total Kas Masuk:</strong> <?= formatCurrency($summary['total_masuk']) ?></p>
                <p><strong>Total Kas Keluar:</strong> <?= formatCurrency($summary['total_keluar']) ?></p>
                <p><strong>Saldo:</strong> <?= formatCurrency($saldo) ?></p>
            </div>
        </body>
        </html>
        <?php
        $html = ob_get_clean();
        echo $html;
    }

    /**
     * Export PDF Laporan Iuran
     */
    public function export_iuran_pdf() {
        $bulan = sanitize($_GET['bulan'] ?? date('Y-m'));

        // Get summary by jenis
        $stmt = $this->db->query("
            SELECT j.id, j.nama_iuran, j.jenis, j.jumlah,
                   COUNT(t.id) as total_tagihan,
                   COUNT(CASE WHEN t.status = 'Lunas' THEN 1 END) as total_lunas,
                   COUNT(CASE WHEN t.status = 'Menunggak' THEN 1 END) as total_tunggakan,
                   COALESCE(SUM(CASE WHEN p.status = 'Verified' THEN p.jumlah_bayar ELSE 0 END), 0) as total_tertagih
            FROM jenis_iuran j
            LEFT JOIN tagihan_iuran t ON j.id = t.id_jenis_iuran AND t.bulan = '$bulan'
            LEFT JOIN pembayaran p ON t.id = p.id_tagihan AND p.status = 'Verified'
            WHERE j.is_active = 1
            GROUP BY j.id
        ");
        $summary = $stmt->fetchAll();

        ob_start();
        ?>
        <html>
        <head>
            <title>Laporan Iuran RT</title>
            <style>
                body { font-family: Arial, sans-serif; font-size: 12px; }
                table { width: 100%; border-collapse: collapse; margin-top: 20px; }
                th, td { border: 1px solid #333; padding: 8px; }
                th { background: #f0f0f0; }
                .header { text-align: center; }
            </style>
        </head>
        <body>
            <div class="header">
                <h2>LAPORAN REKAPITULASI IURAN RT</h2>
                <p>Bulan: <?= getBulanIndonesia(substr($bulan, 5, 2)) . ' ' . substr($bulan, 0, 4) ?></p>
            </div>
            <table>
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
                        <td><?= $s['total_lunas'] ?></td>
                        <td><?= $s['total_tunggakan'] ?></td>
                        <td style="text-align: right;"><?= formatCurrency($s['total_tertagih']) ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </body>
        </html>
        <?php
        $html = ob_get_clean();
        echo $html;
    }

    /**
     * Export PDF Laporan Tunggakan
     */
    public function export_tunggakan_pdf() {
        $stmt = $this->db->query("
            SELECT t.*, w.nama_lengkap, w.nik, w.no_hp, w.alamat, j.nama_iuran, j.jenis, j.jumlah
            FROM tagihan_iuran t
            INNER JOIN warga w ON t.id_warga = w.id
            INNER JOIN jenis_iuran j ON t.id_jenis_iuran = j.id
            WHERE t.status = 'Menunggak'
            ORDER BY w.nama_lengkap, t.bulan DESC
        ");
        $tunggakan = $stmt->fetchAll();

        $grouped = [];
        foreach ($tunggakan as $t) {
            $key = $t['id_warga'];
            if (!isset($grouped[$key])) {
                $grouped[$key] = [
                    'warga' => [
                        'nama_lengkap' => $t['nama_lengkap'],
                        'no_hp' => $t['no_hp'],
                        'alamat' => $t['alamat']
                    ],
                    'total_tunggakan' => 0,
                    'detail' => []
                ];
            }
            $grouped[$key]['total_tunggakan'] += $t['jumlah'];
            $grouped[$key]['detail'][] = $t;
        }

        ob_start();
        ?>
        <html>
        <head>
            <title>Laporan Tunggakan RT</title>
            <style>
                body { font-family: Arial, sans-serif; font-size: 12px; }
                table { width: 100%; border-collapse: collapse; margin-top: 5px; margin-bottom: 20px;}
                th, td { border: 1px solid #333; padding: 6px; }
                th { background: #f0f0f0; }
                .header { text-align: center; margin-bottom: 20px;}
                .warga-title { font-size: 14px; font-weight: bold; background: #eee; padding: 5px; border: 1px solid #333;}
                .warga-info { margin-bottom: 5px; }
            </style>
        </head>
        <body>
            <div class="header">
                <h2>LAPORAN TUNGGAKAN IURAN RT</h2>
                <p>Tanggal Cetak: <?= formatTanggal(date('Y-m-d')) ?></p>
            </div>
            
            <?php if (empty($grouped)): ?>
                <p style="text-align:center;">Tidak ada data tunggakan.</p>
            <?php else: ?>
                <?php foreach ($grouped as $item): ?>
                    <div class="warga-title">
                        <?= $item['warga']['nama_lengkap'] ?> - Total: <?= formatCurrency($item['total_tunggakan']) ?>
                    </div>
                    <div class="warga-info">
                        Kontak: <?= $item['warga']['no_hp'] ?: '-' ?> | Alamat: <?= $item['warga']['alamat'] ?: '-' ?>
                    </div>
                    <table>
                        <thead>
                            <tr>
                                <th width="30%">Bulan</th>
                                <th width="40%">Jenis Iuran</th>
                                <th width="30%">Jumlah</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($item['detail'] as $d): ?>
                            <tr>
                                <td><?= getBulanIndonesia(substr($d['bulan'], 5, 2)) . ' ' . substr($d['bulan'], 0, 4) ?></td>
                                <td><?= $d['nama_iuran'] ?></td>
                                <td style="text-align: right;"><?= formatCurrency($d['jumlah']) ?></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php endforeach; ?>
            <?php endif; ?>
        </body>
        </html>
        <?php
        $html = ob_get_clean();
        echo $html;
    }

    /**
     * Export PDF Semua Laporan Sekaligus (Konsolidasi)
     */
    public function export_all_pdf() {
        $bulan = sanitize($_GET['bulan'] ?? date('Y-m'));
        $tanggal_awal = date('Y-m-01', strtotime($bulan . '-01'));
        $tanggal_akhir = date('Y-m-t', strtotime($bulan . '-01'));

        // --- 1. DATA KAS ---
        $stmt = $this->db->prepare("
            SELECT
                COALESCE(SUM(CASE WHEN jenis = 'Masuk' THEN jumlah ELSE 0 END), 0) as total_masuk,
                COALESCE(SUM(CASE WHEN jenis = 'Keluar' THEN jumlah ELSE 0 END), 0) as total_keluar
            FROM kas_rt WHERE tanggal BETWEEN ? AND ?
        ");
        $stmt->execute([$tanggal_awal, $tanggal_akhir]);
        $summaryKas = $stmt->fetch();
        $saldoKas = $summaryKas['total_masuk'] - $summaryKas['total_keluar'];

        // --- 2. DATA IURAN ---
        $stmt = $this->db->query("
            SELECT j.id, j.nama_iuran, j.jenis, j.jumlah,
                   COUNT(t.id) as total_tagihan,
                   COUNT(CASE WHEN t.status = 'Lunas' THEN 1 END) as total_lunas,
                   COUNT(CASE WHEN t.status = 'Menunggak' THEN 1 END) as total_tunggakan,
                   COALESCE(SUM(CASE WHEN p.status = 'Verified' THEN p.jumlah_bayar ELSE 0 END), 0) as total_tertagih
            FROM jenis_iuran j
            LEFT JOIN tagihan_iuran t ON j.id = t.id_jenis_iuran AND t.bulan = '$bulan'
            LEFT JOIN pembayaran p ON t.id = p.id_tagihan AND p.status = 'Verified'
            WHERE j.is_active = 1
            GROUP BY j.id
        ");
        $summaryIuran = $stmt->fetchAll();

        // --- 3. DATA TUNGGAKAN ---
        $stmt = $this->db->query("
            SELECT t.*, w.nama_lengkap, j.nama_iuran, j.jumlah
            FROM tagihan_iuran t
            INNER JOIN warga w ON t.id_warga = w.id
            INNER JOIN jenis_iuran j ON t.id_jenis_iuran = j.id
            WHERE t.status = 'Menunggak'
            ORDER BY w.nama_lengkap, t.bulan DESC
        ");
        $tunggakan = $stmt->fetchAll();

        $groupedTunggakan = [];
        foreach ($tunggakan as $t) {
            $key = $t['id_warga'];
            if (!isset($groupedTunggakan[$key])) {
                $groupedTunggakan[$key] = [
                    'nama_lengkap' => $t['nama_lengkap'],
                    'total_tunggakan' => 0,
                    'detail' => []
                ];
            }
            $groupedTunggakan[$key]['total_tunggakan'] += $t['jumlah'];
            $groupedTunggakan[$key]['detail'][] = $t;
        }

        ob_start();
        ?>
        <html>
        <head>
            <title>Laporan Konsolidasi RT</title>
            <style>
                body { font-family: Arial, sans-serif; font-size: 12px; }
                table { width: 100%; border-collapse: collapse; margin-top: 10px; margin-bottom: 30px; }
                th, td { border: 1px solid #333; padding: 6px; }
                th { background: #f0f0f0; }
                .header { text-align: center; border-bottom: 2px solid #000; padding-bottom: 10px; margin-bottom: 20px;}
                .section-title { font-size: 14px; font-weight: bold; background: #333; color: white; padding: 5px; margin-top: 20px;}
                .warga-title { font-weight: bold; background: #eee; padding: 5px; border: 1px solid #333; border-bottom: none;}
            </style>
        </head>
        <body>
            <div class="header">
                <h2>LAPORAN KONSOLIDASI RT</h2>
                <p>Bulan: <?= getBulanIndonesia(substr($bulan, 5, 2)) . ' ' . substr($bulan, 0, 4) ?></p>
            </div>

            <!-- BAGIAN 1: KAS RT -->
            <div class="section-title">1. REKAPITULASI KAS RT</div>
            <table>
                <tr>
                    <th>Total Kas Masuk</th>
                    <td style="text-align: right;"><?= formatCurrency($summaryKas['total_masuk']) ?></td>
                </tr>
                <tr>
                    <th>Total Kas Keluar</th>
                    <td style="text-align: right;"><?= formatCurrency($summaryKas['total_keluar']) ?></td>
                </tr>
                <tr>
                    <th>Saldo Kas (Bulan Ini)</th>
                    <td style="text-align: right; font-weight: bold;"><?= formatCurrency($saldoKas) ?></td>
                </tr>
            </table>

            <!-- BAGIAN 2: IURAN RT -->
            <div class="section-title">2. REKAPITULASI IURAN RT</div>
            <table>
                <thead>
                    <tr>
                        <th>Jenis Iuran</th>
                        <th>Tagihan Terbit</th>
                        <th>Lunas</th>
                        <th>Menunggak</th>
                        <th>Total Tertagih</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($summaryIuran as $s): ?>
                    <tr>
                        <td><?= $s['nama_iuran'] ?></td>
                        <td style="text-align: center;"><?= $s['total_tagihan'] ?></td>
                        <td style="text-align: center;"><?= $s['total_lunas'] ?></td>
                        <td style="text-align: center;"><?= $s['total_tunggakan'] ?></td>
                        <td style="text-align: right;"><?= formatCurrency($s['total_tertagih']) ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>

            <!-- BAGIAN 3: TUNGGAKAN -->
            <div class="section-title" style="page-break-before: auto;">3. DAFTAR TUNGGAKAN WARGA</div>
            <?php if (empty($groupedTunggakan)): ?>
                <p style="text-align:center; margin-top: 15px;">Tidak ada tunggakan iuran.</p>
            <?php else: ?>
                <?php foreach ($groupedTunggakan as $item): ?>
                    <div class="warga-title">
                        <?= $item['nama_lengkap'] ?> (Total: <?= formatCurrency($item['total_tunggakan']) ?>)
                    </div>
                    <table style="margin-top: 0;">
                        <tr><td style="color: #555;">
                            Rincian: <?= implode(', ', array_map(function($d) { return $d['nama_iuran'] . ' (' . substr($d['bulan'], 5, 2) . '/' . substr($d['bulan'], 0, 4) . ')'; }, $item['detail'])) ?>
                        </td></tr>
                    </table>
                <?php endforeach; ?>
            <?php endif; ?>
        </body>
        </html>
        <?php
        $html = ob_get_clean();
        echo $html;
    }
}