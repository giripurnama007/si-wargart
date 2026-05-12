<?php
/**
 * Iuran Controller
 * SI-WargaRT - Sistem Informasi Warga RT
 */

class IuranController {

    private $db;

    public function __construct() {
        requireLogin();
        $this->db = getDb();
    }

    /**
     * Index - List Iuran
     */
    public function index() {
        $user = getCurrentUser();
        $bulan = sanitize($_GET['bulan'] ?? date('Y-m'));
        $status = sanitize($_GET['status'] ?? '');
        $id_warga = $user['id_warga'] ?? 0;

        $pageTitle = 'Iuran RT';
        $breadcrumb = '<ol class="breadcrumb float-sm-right">
            <li class="breadcrumb-item"><a href="' . nginx_url('dashboard') . '">Home</a></li>
            <li class="breadcrumb-item active">Iuran</li>
        </ol>';

        $whereSummary = "WHERE bulan = '$bulan'";
        if ($user['role'] === 'warga') {
            $whereSummary .= " AND id_warga = $id_warga";
        }

        // Get summary
        $stmt = $this->db->query("
            SELECT
                COUNT(*) as total_tagihan,
                SUM(CASE WHEN status = 'Lunas' THEN jumlah ELSE 0 END) as total_lunas,
                SUM(CASE WHEN status = 'Pending' THEN jumlah ELSE 0 END) as total_pending,
                SUM(CASE WHEN status = 'Menunggak' THEN jumlah ELSE 0 END) as total_tunggakan
            FROM tagihan_iuran
            $whereSummary
        ");
        $summary = $stmt->fetch();

        // Get tagihan
        $where = "WHERE t.bulan = '$bulan'";
        if ($status) {
            $where .= " AND t.status = '$status'";
        }
        if ($user['role'] === 'warga') {
            $where .= " AND t.id_warga = $id_warga";
        }

        $stmt = $this->db->query("
            SELECT t.*, w.nama_lengkap, w.nik, j.nama_iuran, j.jenis, j.jumlah as jumlah_iuran
            FROM tagihan_iuran t
            INNER JOIN warga w ON t.id_warga = w.id
            INNER JOIN jenis_iuran j ON t.id_jenis_iuran = j.id
            $where
            ORDER BY t.status, w.nama_lengkap
        ");
        $tagihan = $stmt->fetchAll();

        // Get jenis iuran
        $stmt = $this->db->query("SELECT * FROM jenis_iuran WHERE is_active = 1");
        $jenisIuran = $stmt->fetchAll();

        ob_start();
        include VIEWSPATH . 'iuran/index.php';
        $content = ob_get_clean();

        include LAYOUTS . 'main_layout.php';
    }

    /**
     * Generate Tagihan
     */
    public function generate() {
        requireRole(['admin', 'ketua_rt']);

        $pageTitle = 'Generate Tagihan';
        $breadcrumb = '<ol class="breadcrumb float-sm-right">
            <li class="breadcrumb-item"><a href="' . nginx_url('dashboard') . '">Home</a></li>
            <li class="breadcrumb-item"><a href="' . nginx_url('iuran') . '">Iuran</a></li>
            <li class="breadcrumb-item active">Generate</li>
        </ol>';

        // Get warga aktif
        $stmt = $this->db->query("SELECT * FROM warga WHERE status_warga = 'Aktif' ORDER BY nama_lengkap");
        $warga = $stmt->fetchAll();

        // Get jenis iuran aktif
        $stmt = $this->db->query("SELECT * FROM jenis_iuran WHERE is_active = 1");
        $jenisIuran = $stmt->fetchAll();

        ob_start();
        include VIEWSPATH . 'iuran/generate.php';
        $content = ob_get_clean();

        include LAYOUTS . 'main_layout.php';
    }

    /**
     * Process Generate Tagihan
     */
    public function proses_generate() {
        requireRole(['admin', 'ketua_rt']);

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $bulan = sanitize($_POST['bulan'] ?? '');
            $jenis_iuran_id = (int)($_POST['jenis_iuran_id'] ?? 0);
            $jumlah = (float)($_POST['jumlah'] ?? 0);

            if (!$bulan || !$jenis_iuran_id) {
                setFlash('error', 'Data tidak lengkap!');
                redirect('/iuran/generate');
            }

            // Get warga aktif
            $stmt = $this->db->query("SELECT id FROM warga WHERE status_warga = 'Aktif'");
            $wargaIds = $stmt->fetchAll(PDO::FETCH_COLUMN);

            $success = 0;
            foreach ($wargaIds as $wargaId) {
                // Check if tagihan already exists
                $stmt = $this->db->prepare("
                    SELECT id FROM tagihan_iuran WHERE id_warga = ? AND id_jenis_iuran = ? AND bulan = ?
                ");
                $stmt->execute([$wargaId, $jenis_iuran_id, $bulan]);
                if ($stmt->fetch()) {
                    continue;
                }

                // Calculate due date (end of month)
                $jatuhTempo = date('Y-m-t', strtotime($bulan . '-01'));

                $stmt = $this->db->prepare("
                    INSERT INTO tagihan_iuran (id_warga, id_jenis_iuran, bulan, jumlah, status, tanggal_jatuh_tempo)
                    VALUES (?, ?, ?, ?, 'Pending', ?)
                ");
                $stmt->execute([$wargaId, $jenis_iuran_id, $bulan, $jumlah, $jatuhTempo]);
                $success++;
            }

            logActivity("Generate tagihan $bulan - $success warga", 'Iuran');
            setFlash('success', "Berhasil generate tagihan untuk $success warga!");
            redirect('/iuran?bulan=' . $bulan);
        }

        redirect('/iuran/generate');
    }

    /**
     * Pembayaran
     */
    public function pembayaran() {
        $user = getCurrentUser();

        $pageTitle = 'Pembayaran Iuran';
        $breadcrumb = '<ol class="breadcrumb float-sm-right">
            <li class="breadcrumb-item"><a href="' . nginx_url('dashboard') . '">Home</a></li>
            <li class="breadcrumb-item"><a href="' . nginx_url('iuran') . '">Iuran</a></li>
            <li class="breadcrumb-item active">Pembayaran</li>
        </ol>';
        $selectedBulan = sanitize($_GET['bulan'] ?? date('Y-m'));

        // Get warga for dropdown
        if ($user['role'] === 'warga') {
            $id_warga = $user['id_warga'] ?? 0;
            $stmt = $this->db->query("SELECT id, nik, nama_lengkap FROM warga WHERE id = $id_warga AND status_warga = 'Aktif'");
        } else {
            $stmt = $this->db->query("SELECT id, nik, nama_lengkap FROM warga WHERE status_warga = 'Aktif' ORDER BY nama_lengkap");
        }
        $warga = $stmt->fetchAll();

        // Get jenis iuran
        $stmt = $this->db->query("SELECT * FROM jenis_iuran WHERE is_active = 1");
        $jenisIuran = $stmt->fetchAll();

        $whereRiwayat = "";
        if ($user['role'] === 'warga') {
            $whereRiwayat = "WHERE p.id_warga = " . ($user['id_warga'] ?? 0);
        }

        // Get recent payments
        $stmt = $this->db->query("
            SELECT p.*, w.nama_lengkap, j.nama_iuran, t.bulan
            FROM pembayaran p
            INNER JOIN warga w ON p.id_warga = w.id
            INNER JOIN tagihan_iuran t ON p.id_tagihan = t.id
            INNER JOIN jenis_iuran j ON t.id_jenis_iuran = j.id
            $whereRiwayat
            ORDER BY p.created_at DESC
            LIMIT 50
        ");
        $pembayaran = $stmt->fetchAll();

        ob_start();
        include VIEWSPATH . 'iuran/pembayaran.php';
        $content = ob_get_clean();

        include LAYOUTS . 'main_layout.php';
    }

    /**
     * Process Pembayaran
     */
    public function proses_pembayaran() {
        $user = getCurrentUser();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id_tagihan = (int)($_POST['id_tagihan'] ?? 0);
            $jumlah_bayar = (float)($_POST['jumlah_bayar'] ?? 0);
            $tanggal_bayar = sanitize($_POST['tanggal_bayar'] ?? date('Y-m-d'));
            $metode_bayar = sanitize($_POST['metode_bayar'] ?? 'Transfer');

            // Get tagihan
            $stmt = $this->db->prepare("SELECT * FROM tagihan_iuran WHERE id = ?");
            $stmt->execute([$id_tagihan]);
            $tagihan = $stmt->fetch();

            if (!$tagihan) {
                setFlash('error', 'Tagihan tidak ditemukan!');
                redirect('/iuran/pembayaran');
            }

            // Cek otorisasi warga hanya bisa bayar tagihan miliknya sendiri
            if ($user['role'] === 'warga' && $tagihan['id_warga'] != ($user['id_warga'] ?? 0)) {
                setFlash('error', 'Tagihan tidak valid!');
                redirect('/iuran/pembayaran');
            }

            // Handle bukti transfer
            $buktiName = null;
            if (!empty($_FILES['bukti_transfer']['name'])) {
                $upload = uploadFile($_FILES['bukti_transfer'], UPLOAD_PEMBAYARAN, ['jpg', 'jpeg', 'png', 'pdf']);
                if ($upload['success']) {
                    $buktiName = $upload['filename'];
                }
            }

            // Insert pembayaran
            $stmt = $this->db->prepare("
                INSERT INTO pembayaran (id_tagihan, id_warga, jumlah_bayar, tanggal_bayar, metode_bayar, bukti_transfer, status)
                VALUES (?, ?, ?, ?, ?, ?, 'Pending')
            ");
            $stmt->execute([$id_tagihan, $tagihan['id_warga'], $jumlah_bayar, $tanggal_bayar, $metode_bayar, $buktiName]);

            logActivity('Input pembayaran untuk tagihan #' . $id_tagihan, 'Iuran');
            setFlash('success', 'Pembayaran berhasil dicatat!');
            redirect('/iuran/pembayaran');
        }

        redirect('/iuran/pembayaran');
    }

    /**
     * Verifikasi Pembayaran
     */
    public function verifikasi() {
        requireRole(['admin', 'ketua_rt']);

        $pageTitle = 'Verifikasi Pembayaran';
        $breadcrumb = '<ol class="breadcrumb float-sm-right">
            <li class="breadcrumb-item"><a href="' . nginx_url('dashboard') . '">Home</a></li>
            <li class="breadcrumb-item"><a href="' . nginx_url('iuran') . '">Iuran</a></li>
            <li class="breadcrumb-item active">Verifikasi</li>
        </ol>';

        // Get pending payments
        $stmt = $this->db->query("
            SELECT p.*, w.nama_lengkap, w.nik, j.nama_iuran, t.bulan, t.jumlah as jumlah_tagihan
            FROM pembayaran p
            INNER JOIN warga w ON p.id_warga = w.id
            INNER JOIN tagihan_iuran t ON p.id_tagihan = t.id
            INNER JOIN jenis_iuran j ON t.id_jenis_iuran = j.id
            WHERE p.status = 'Pending'
            ORDER BY p.created_at DESC
        ");
        $pending = $stmt->fetchAll();

        // Get verified payments
        $stmt = $this->db->query("
            SELECT p.*, w.nama_lengkap, j.nama_iuran, t.bulan, u.nama as verified_by_name
            FROM pembayaran p
            INNER JOIN warga w ON p.id_warga = w.id
            INNER JOIN tagihan_iuran t ON p.id_tagihan = t.id
            INNER JOIN jenis_iuran j ON t.id_jenis_iuran = j.id
            LEFT JOIN users u ON p.verified_by = u.id
            WHERE p.status = 'Verified'
            ORDER BY p.verified_at DESC
            LIMIT 50
        ");
        $verified = $stmt->fetchAll();

        ob_start();
        include VIEWSPATH . 'iuran/verifikasi.php';
        $content = ob_get_clean();

        include LAYOUTS . 'main_layout.php';
    }

    /**
     * Process Verifikasi
     */
    public function proses_verifikasi() {
        requireRole(['admin', 'ketua_rt']);

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = (int)($_POST['id'] ?? 0);
            $action = sanitize($_POST['action'] ?? '');

            $stmt = $this->db->prepare("SELECT * FROM pembayaran WHERE id = ?");
            $stmt->execute([$id]);
            $pembayaran = $stmt->fetch();

            if (!$pembayaran) {
                setFlash('error', 'Pembayaran tidak ditemukan!');
                redirect('/iuran/verifikasi');
            }

            if ($action === 'verify') {
                $stmt = $this->db->prepare("
                    UPDATE pembayaran SET status = 'Verified', verified_by = ?, verified_at = NOW() WHERE id = ?
                ");
                $stmt->execute([$_SESSION['user_id'], $id]);

                // Update tagihan status
                $stmt = $this->db->prepare("UPDATE tagihan_iuran SET status = 'Lunas' WHERE id = ?");
                $stmt->execute([$pembayaran['id_tagihan']]);

                // Create notification
                $stmt = $this->db->prepare("
                    INSERT INTO notifikasi (id_user, judul, isi, jenis, link)
                    SELECT id, 'Pembayaran Diverifikasi', CONCAT('Pembayaran iuran Anda telah diverifikasi'), 'Tagihan', '/iuran'
                    FROM users WHERE role = 'warga' AND id_warga = ?
                ");
                $stmt->execute([$pembayaran['id_warga']]);

                logActivity('Verifikasi pembayaran #' . $id, 'Iuran');
                setFlash('success', 'Pembayaran berhasil diverifikasi!');
            } elseif ($action === 'reject') {
                $keterangan = sanitize($_POST['keterangan'] ?? '');
                $stmt = $this->db->prepare("
                    UPDATE pembayaran SET status = 'Rejected', keterangan = ? WHERE id = ?
                ");
                $stmt->execute([$keterangan, $id]);

                logActivity('Tolak pembayaran #' . $id, 'Iuran');
                setFlash('warning', 'Pembayaran ditolak!');
            }

            redirect('/iuran/verifikasi');
        }

        redirect('/iuran/verifikasi');
    }

    /**
     * Hapus Tagihan
     */
    public function hapus_tagihan() {
        requireRole(['admin', 'ketua_rt']);

        $id = (int)($_GET['id'] ?? 0);

        $stmt = $this->db->prepare("DELETE FROM tagihan_iuran WHERE id = ?");
        $stmt->execute([$id]);

        setFlash('success', 'Tagihan berhasil dihapus!');
        redirect('/iuran');
    }

    /**
     * Get Tagihan by Warga (AJAX)
     */
    public function get_tagihan() {
        $user = getCurrentUser();
        $id_warga = (int)($_GET['id_warga'] ?? 0);
        $bulan = sanitize($_GET['bulan'] ?? date('Y-m'));

        if ($user['role'] === 'warga') {
            $id_warga = $user['id_warga'] ?? 0;
        }

        $stmt = $this->db->prepare("
            SELECT t.*, j.nama_iuran, j.jumlah as jumlah_default
            FROM tagihan_iuran t
            INNER JOIN jenis_iuran j ON t.id_jenis_iuran = j.id
            WHERE t.id_warga = ? AND t.bulan = ? AND t.status != 'Lunas'
        ");
        $stmt->execute([$id_warga, $bulan]);
        $tagihan = $stmt->fetchAll();

        jsonResponse(true, '', ['tagihan' => $tagihan]);
    }
}