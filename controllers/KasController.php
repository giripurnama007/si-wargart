<?php
/**
 * Kas Controller
 * SI-WargaRT - Sistem Informasi Warga RT
 */

class KasController {

    private $db;

    public function __construct() {
        requireRole(['admin', 'ketua_rt']);
        $this->db = getDb();
    }

    /**
     * Index - Kas Overview
     */
    public function index() {
        $bulan = sanitize($_GET['bulan'] ?? date('Y-m'));

        // Get summary
        $stmt = $this->db->query("
            SELECT
                COALESCE(SUM(CASE WHEN jenis = 'Masuk' THEN jumlah ELSE 0 END), 0) as total_masuk,
                COALESCE(SUM(CASE WHEN jenis = 'Keluar' THEN jumlah ELSE 0 END), 0) as total_keluar,
                (COALESCE(SUM(CASE WHEN jenis = 'Masuk' THEN jumlah ELSE 0 END), 0) - COALESCE(SUM(CASE WHEN jenis = 'Keluar' THEN jumlah ELSE 0 END), 0)) as saldo
            FROM kas_rt
            WHERE DATE_FORMAT(tanggal, '%Y-%m') = '$bulan'
        ");
        $summary = $stmt->fetch();

        // Get transactions
        $stmt = $this->db->query("
            SELECT k.*, u.nama as created_by_name
            FROM kas_rt k
            LEFT JOIN users u ON k.created_by = u.id
            WHERE DATE_FORMAT(k.tanggal, '%Y-%m') = '$bulan'
            ORDER BY k.tanggal DESC, k.created_at DESC
        ");
        $transactions = $stmt->fetchAll();

        // Get annual summary
        $stmt = $this->db->query("
            SELECT
                DATE_FORMAT(tanggal, '%Y') as tahun,
                COALESCE(SUM(CASE WHEN jenis = 'Masuk' THEN jumlah ELSE 0 END), 0) as total_masuk,
                COALESCE(SUM(CASE WHEN jenis = 'Keluar' THEN jumlah ELSE 0 END), 0) as total_keluar
            FROM kas_rt
            GROUP BY DATE_FORMAT(tanggal, '%Y')
            ORDER BY tahun DESC
        ");
        $annualSummary = $stmt->fetchAll();

        $pageTitle = 'Kas RT';
        $breadcrumb = '<ol class="breadcrumb float-sm-right">
            <li class="breadcrumb-item"><a href="' . nginx_url('dashboard') . '">Home</a></li>
            <li class="breadcrumb-item active">Kas RT</li>
        </ol>';

        ob_start();
        include VIEWSPATH . 'kas/index.php';
        $content = ob_get_clean();

        include LAYOUTS . 'main_layout.php';
    }

    /**
     * Tambah Kas Masuk
     */
    public function masuk() {
        $pageTitle = 'Kas Masuk';
        $breadcrumb = '<ol class="breadcrumb float-sm-right">
            <li class="breadcrumb-item"><a href="' . nginx_url('dashboard') . '">Home</a></li>
            <li class="breadcrumb-item"><a href="' . nginx_url('kas') . '">Kas RT</a></li>
            <li class="breadcrumb-item active">Kas Masuk</li>
        </ol>';

        ob_start();
        include VIEWSPATH . 'kas/masuk.php';
        $content = ob_get_clean();

        include LAYOUTS . 'main_layout.php';
    }

    /**
     * Process Kas Masuk
     */
    public function proses_masuk() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = [
                'jenis' => 'Masuk',
                'kategori' => sanitize($_POST['kategori'] ?? ''),
                'jumlah' => (float)($_POST['jumlah'] ?? 0),
                'keterangan' => sanitize($_POST['keterangan'] ?? ''),
                'tanggal' => sanitize($_POST['tanggal'] ?? date('Y-m-d'))
            ];

            if (!$data['kategori'] || !$data['jumlah']) {
                setFlash('error', 'Data tidak lengkap!');
                redirect('/kas/masuk');
            }

            // Handle bukti
            $buktiName = null;
            if (!empty($_FILES['bukti']['name'])) {
                $upload = uploadFile($_FILES['bukti'], UPLOAD_PEMBAYARAN, ['jpg', 'jpeg', 'png', 'pdf']);
                if ($upload['success']) {
                    $buktiName = $upload['filename'];
                }
            }

            $stmt = $this->db->prepare("
                INSERT INTO kas_rt (jenis, kategori, jumlah, keterangan, tanggal, bukti, created_by)
                VALUES (?, ?, ?, ?, ?, ?, ?)
            ");
            $stmt->execute([$data['jenis'], $data['kategori'], $data['jumlah'], $data['keterangan'], $data['tanggal'], $buktiName, $_SESSION['user_id']]);

            logActivity('Tambah kas masuk: ' . formatCurrency($data['jumlah']), 'Kas');
            setFlash('success', 'Kas masuk berhasil dicatat!');
            redirect('/kas');
        }

        redirect('/kas/masuk');
    }

    /**
     * Tambah Kas Keluar
     */
    public function keluar() {
        $pageTitle = 'Kas Keluar';
        $breadcrumb = '<ol class="breadcrumb float-sm-right">
            <li class="breadcrumb-item"><a href="' . nginx_url('dashboard') . '">Home</a></li>
            <li class="breadcrumb-item"><a href="' . nginx_url('kas') . '">Kas RT</a></li>
            <li class="breadcrumb-item active">Kas Keluar</li>
        </ol>';

        ob_start();
        include VIEWSPATH . 'kas/keluar.php';
        $content = ob_get_clean();

        include LAYOUTS . 'main_layout.php';
    }

    /**
     * Process Kas Keluar
     */
    public function proses_keluar() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = [
                'jenis' => 'Keluar',
                'kategori' => sanitize($_POST['kategori'] ?? ''),
                'jumlah' => (float)($_POST['jumlah'] ?? 0),
                'keterangan' => sanitize($_POST['keterangan'] ?? ''),
                'tanggal' => sanitize($_POST['tanggal'] ?? date('Y-m-d'))
            ];

            if (!$data['kategori'] || !$data['jumlah']) {
                setFlash('error', 'Data tidak lengkap!');
                redirect('/kas/keluar');
            }

            // Handle bukti
            $buktiName = null;
            if (!empty($_FILES['bukti']['name'])) {
                $upload = uploadFile($_FILES['bukti'], UPLOAD_PEMBAYARAN, ['jpg', 'jpeg', 'png', 'pdf']);
                if ($upload['success']) {
                    $buktiName = $upload['filename'];
                }
            }

            $stmt = $this->db->prepare("
                INSERT INTO kas_rt (jenis, kategori, jumlah, keterangan, tanggal, bukti, created_by)
                VALUES (?, ?, ?, ?, ?, ?, ?)
            ");
            $stmt->execute([$data['jenis'], $data['kategori'], $data['jumlah'], $data['keterangan'], $data['tanggal'], $buktiName, $_SESSION['user_id']]);

            logActivity('Tambah kas keluar: ' . formatCurrency($data['jumlah']), 'Kas');
            setFlash('success', 'Kas keluar berhasil dicatat!');
            redirect('/kas');
        }

        redirect('/kas/keluar');
    }

    /**
     * Hapus Transaksi
     */
    public function hapus() {
        $id = (int)($_GET['id'] ?? 0);

        $stmt = $this->db->prepare("SELECT * FROM kas_rt WHERE id = ?");
        $stmt->execute([$id]);
        $kas = $stmt->fetch();

        if ($kas) {
            if ($kas['bukti']) {
                deleteFile(UPLOAD_PEMBAYARAN . $kas['bukti']);
            }

            $stmt = $this->db->prepare("DELETE FROM kas_rt WHERE id = ?");
            $stmt->execute([$id]);

            logActivity('Hapus transaksi kas #' . $id, 'Kas');
            setFlash('success', 'Transaksi berhasil dihapus!');
        }

        redirect('/kas');
    }
}