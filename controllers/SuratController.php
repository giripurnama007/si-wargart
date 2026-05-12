<?php
/**
 * Surat Controller
 * SI-WargaRT - Sistem Informasi Warga RT
 */

class SuratController {

    private $db;

    public function __construct() {
        requireLogin();
        $this->db = getDb();
    }

    /**
     * Index - List Surat
     */
    public function index() {
        $user = getCurrentUser();

        $pageTitle = 'Surat Pengantar';
        $breadcrumb = '<ol class="breadcrumb float-sm-right">
            <li class="breadcrumb-item"><a href="' . nginx_url('dashboard') . '">Home</a></li>
            <li class="breadcrumb-item active">Surat Pengantar</li>
        </ol>';

        $status = sanitize($_GET['status'] ?? '');
        $where = "WHERE 1=1";
        if ($status) {
            $where .= " AND s.status = '$status'";
        }

        if ($user['role'] === 'warga') {
            $id_warga = $user['id_warga'] ?? 0;
            $where .= " AND s.id_warga = $id_warga";
        }

        $stmt = $this->db->query("
            SELECT s.*, w.nama_lengkap, w.nik, w.alamat,
                   k.nama as approved_ketua_name,
                   a.nama as approved_admin_name
            FROM surat_pengantar s
            INNER JOIN warga w ON s.id_warga = w.id
            LEFT JOIN users k ON s.approved_by_ketua = k.id
            LEFT JOIN users a ON s.approved_by_admin = a.id
            $where
            ORDER BY s.tanggal_pengajuan DESC, s.created_at DESC
        ");
        $surat = $stmt->fetchAll();

        ob_start();
        include VIEWSPATH . 'surat/index.php';
        $content = ob_get_clean();

        include LAYOUTS . 'main_layout.php';
    }

    /**
     * Ajukan Surat (Warga)
     */
    public function aju() {
        $user = getCurrentUser();

        // Get warga for this user
        $stmt = $this->db->prepare("SELECT * FROM warga WHERE id = ?");
        $warga = null;
        if ($user['role'] === 'warga' && isset($user['id_warga'])) {
            $stmt->execute([$user['id_warga']]);
            $warga = $stmt->fetch();
        }

        // Get semua warga untuk admin/ketua
        $semuaWarga = [];
        if ($user['role'] !== 'warga') {
            $semuaWarga = $this->db->query("SELECT id, nik, nama_lengkap FROM warga WHERE status_warga = 'Aktif' ORDER BY nama_lengkap")->fetchAll();
        }

        $pageTitle = 'Ajukan Surat';
        $breadcrumb = '<ol class="breadcrumb float-sm-right">
            <li class="breadcrumb-item"><a href="' . nginx_url('dashboard') . '">Home</a></li>
            <li class="breadcrumb-item"><a href="' . nginx_url('surat') . '">Surat Pengantar</a></li>
            <li class="breadcrumb-item active">Ajukan</li>
        </ol>';

        ob_start();
        include VIEWSPATH . 'surat/aju.php';
        $content = ob_get_clean();

        include LAYOUTS . 'main_layout.php';
    }

    /**
     * Process Ajukan Surat
     */
    public function proses_aju() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $user = getCurrentUser();

            $data = [
                'jenis_surat' => sanitize($_POST['jenis_surat'] ?? ''),
                'keperluan' => sanitize($_POST['keperluan'] ?? ''),
                'tanggal_pengajuan' => date('Y-m-d')
            ];

            if (!$data['jenis_surat'] || !$data['keperluan']) {
                setFlash('error', 'Semua field wajib diisi!');
                redirect('/surat/aju');
            }

            // Get warga id
            $id_warga = null;
            if ($user['role'] === 'warga' && isset($user['id_warga'])) {
                $id_warga = (int)$user['id_warga'];
            } else {
                $id_warga = (int)($_POST['id_warga'] ?? 0);
            }

            if (!$id_warga) {
                setFlash('error', 'Warga tidak ditemukan!');
                redirect('/surat/aju');
            }

            $stmt = $this->db->prepare("
                INSERT INTO surat_pengantar (id_warga, jenis_surat, keperluan, tanggal_pengajuan, status)
                VALUES (?, ?, ?, ?, 'Pending')
            ");
            $stmt->execute([$id_warga, $data['jenis_surat'], $data['keperluan'], $data['tanggal_pengajuan']]);

            $suratId = $this->db->lastInsertId();

            // Create notification for admin
            $stmt = $this->db->query("SELECT id FROM users WHERE role IN ('admin', 'ketua_rt')");
            $admins = $stmt->fetchAll(PDO::FETCH_COLUMN);

            foreach ($admins as $adminId) {
                $stmt = $this->db->prepare("
                    INSERT INTO notifikasi (id_user, judul, isi, jenis, link)
                    VALUES (?, 'Permohonan Surat Baru', 'Ada permohonan surat baru yang menunggu persetujuan', 'Surat', '/surat/detail?id=$suratId')
                ");
                $stmt->execute([$adminId]);
            }

            logActivity('Ajukan surat: ' . $data['jenis_surat'], 'Surat');
            setFlash('success', 'Surat berhasil diajukan!');
            redirect('/surat');
        }

        redirect('/surat/aju');
    }

    /**
     * Detail Surat
     */
    public function detail() {
        $user = getCurrentUser();
        $id = (int)($_GET['id'] ?? 0);

        $stmt = $this->db->prepare("
            SELECT s.*, w.nama_lengkap, w.nik, w.alamat, w.rt, w.rw, w.tempat_lahir, w.tanggal_lahir,
                   k.nama as approved_ketua_name,
                   a.nama as approved_admin_name
            FROM surat_pengantar s
            INNER JOIN warga w ON s.id_warga = w.id
            LEFT JOIN users k ON s.approved_by_ketua = k.id
            LEFT JOIN users a ON s.approved_by_admin = a.id
            WHERE s.id = ?
        ");
        $stmt->execute([$id]);
        $surat = $stmt->fetch();

        if (!$surat) {
            setFlash('error', 'Surat tidak ditemukan!');
            redirect('/surat');
        }

        if ($user['role'] === 'warga' && $surat['id_warga'] != ($user['id_warga'] ?? 0)) {
            setFlash('error', 'Anda tidak memiliki akses ke surat ini!');
            redirect('/surat');
        }

        $pageTitle = 'Detail Surat';
        $breadcrumb = '<ol class="breadcrumb float-sm-right">
            <li class="breadcrumb-item"><a href="' . nginx_url('dashboard') . '">Home</a></li>
            <li class="breadcrumb-item"><a href="' . nginx_url('surat') . '">Surat Pengantar</a></li>
            <li class="breadcrumb-item active">Detail</li>
        </ol>';

        ob_start();
        include VIEWSPATH . 'surat/detail.php';
        $content = ob_get_clean();

        include LAYOUTS . 'main_layout.php';
    }

    /**
     * Approve Surat
     */
    public function approve() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = (int)($_POST['id'] ?? 0);
            $action = sanitize($_POST['action'] ?? '');

            $stmt = $this->db->prepare("SELECT * FROM surat_pengantar WHERE id = ?");
            $stmt->execute([$id]);
            $surat = $stmt->fetch();

            if (!$surat) {
                setFlash('error', 'Surat tidak ditemukan!');
                redirect('/surat');
            }

            if ($action === 'approve_ketua') {
                requireRole(['ketua_rt', 'admin']);

                $stmt = $this->db->prepare("
                    UPDATE surat_pengantar SET status = 'Approved_Ketua', approved_by_ketua = ?, approved_at_ketua = NOW()
                    WHERE id = ?
                ");
                $stmt->execute([$_SESSION['user_id'], $id]);

                logActivity('Approve surat oleh Ketua RT #' . $id, 'Surat');
                setFlash('success', 'Surat berhasil disetujui!');
            } elseif ($action === 'approve_admin') {
                requireRole(['admin']);

                // Generate nomor surat
                $no_surat = '001/SK/' . date('Y');
                $stmt = $this->db->prepare("SELECT COUNT(*) as total FROM surat_pengantar WHERE YEAR(tanggal_pengajuan) = YEAR(NOW())");
                $stmt->execute();
                $count = $stmt->fetch()['total'] + 1;
                $no_surat = str_pad($count, 3, '0', STR_PAD_LEFT) . '/SK/' . date('Y');

                // Generate QR Code
                $qrData = 'SI-WargaRT|Surat:' . $no_surat . '|Nik:' . $surat['nik'];
                $qrFile = 'qr_' . time() . '.png';

                $stmt = $this->db->prepare("
                    UPDATE surat_pengantar SET status = 'Selesai', approved_by_admin = ?, approved_at_admin = NOW(), no_surat = ?, qrcode = ?
                    WHERE id = ?
                ");
                $stmt->execute([$_SESSION['user_id'], $no_surat, $qrFile, $id]);

                // Create notification for warga
                $stmt = $this->db->prepare("
                    INSERT INTO notifikasi (id_user, judul, isi, jenis, link)
                    SELECT id, 'Surat Selesai', 'Surat pengantar Anda sudah selesai dan siap diambil', 'Surat', '/surat/detail?id=$id'
                    FROM users WHERE role = 'warga' AND id_warga = ?
                ");
                $stmt->execute([$surat['id_warga']]);

                logActivity('Approve surat oleh Admin #' . $id . ' No: ' . $no_surat, 'Surat');
                setFlash('success', 'Surat berhasil disetujui dan selesai!');
            } elseif ($action === 'reject') {
                requireRole(['admin', 'ketua_rt']);

                $keterangan = sanitize($_POST['keterangan'] ?? '');
                $stmt = $this->db->prepare("UPDATE surat_pengantar SET status = 'Rejected', keterangan = ? WHERE id = ?");
                $stmt->execute([$keterangan, $id]);

                // Create notification
                $stmt = $this->db->prepare("
                    INSERT INTO notifikasi (id_user, judul, isi, jenis, link)
                    SELECT id, 'Surat Ditolak', 'Surat pengantar Anda ditolak', 'Surat', '/surat/detail?id=$id'
                    FROM users WHERE role = 'warga' AND id_warga = ?
                ");
                $stmt->execute([$surat['id_warga']]);

                logActivity('Tolak surat #' . $id, 'Surat');
                setFlash('warning', 'Surat ditolak!');
            }

            redirect('/surat');
        }

        redirect('/surat');
    }

    /**
     * Cetak Surat
     */
    public function cetak() {
        $user = getCurrentUser();
        $id = (int)($_GET['id'] ?? 0);

        $stmt = $this->db->prepare("
            SELECT s.*, w.nama_lengkap, w.nik, w.alamat, w.rt, w.rw, w.tempat_lahir, w.tanggal_lahir, w.jenis_kelamin,
                   p.nama_rt, p.nama_rw, p.nama_ketua_rt, p.alamat as alamat_rt, p.no_telepon
            FROM surat_pengantar s
            INNER JOIN warga w ON s.id_warga = w.id
            CROSS JOIN pengaturan p
            WHERE s.id = ?
        ");
        $stmt->execute([$id]);
        $surat = $stmt->fetch();

        if (!$surat || $surat['status'] !== 'Selesai') {
            setFlash('error', 'Surat belum selesai diproses!');
            redirect('/surat');
        }

        if ($user['role'] === 'warga' && $surat['id_warga'] != ($user['id_warga'] ?? 0)) {
            setFlash('error', 'Anda tidak memiliki akses ke surat ini!');
            redirect('/surat');
        }

        ob_start();
        include VIEWSPATH . 'surat/cetak.php';
        $content = ob_get_clean();

        // For PDF printing
        echo $content;
    }
}