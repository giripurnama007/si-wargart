<?php
/**
 * Pengaduan Controller
 * SI-WargaRT - Sistem Informasi Warga RT
 */

class PengaduanController {

    private $db;

    public function __construct() {
        requireLogin();
        $this->db = getDb();
    }

    /**
     * Index - List Pengaduan
     */
    public function index() {
        $user = getCurrentUser();
        $role = $user['role'];

        $pageTitle = 'Pengaduan';
        $breadcrumb = '<ol class="breadcrumb float-sm-right">
            <li class="breadcrumb-item"><a href="' . nginx_url('dashboard') . '">Home</a></li>
            <li class="breadcrumb-item active">Pengaduan</li>
        </ol>';

        $where = "";
        if ($role === 'warga') {
            $where = "WHERE p.id_warga = " . ($user['id_warga'] ?? 0);
        }

        $stmt = $this->db->query("
            SELECT p.*, w.nama_lengkap, w.no_hp, u.nama as respon_by_name
            FROM pengaduan p
            INNER JOIN warga w ON p.id_warga = w.id
            LEFT JOIN users u ON p.respon_by = u.id
            $where
            ORDER BY p.tanggal_pengaduan DESC, p.created_at DESC
        ");
        $pengaduan = $stmt->fetchAll();

        ob_start();
        include VIEWSPATH . 'pengaduan/index.php';
        $content = ob_get_clean();

        include LAYOUTS . 'main_layout.php';
    }

    /**
     * Tambah Pengaduan
     */
    public function tambah() {
        $user = getCurrentUser();

        $pageTitle = 'Ajukan Pengaduan';
        $breadcrumb = '<ol class="breadcrumb float-sm-right">
            <li class="breadcrumb-item"><a href="' . nginx_url('dashboard') . '">Home</a></li>
            <li class="breadcrumb-item"><a href="' . nginx_url('pengaduan') . '">Pengaduan</a></li>
            <li class="breadcrumb-item active">Ajukan</li>
        </ol>';

        ob_start();
        include VIEWSPATH . 'pengaduan/tambah.php';
        $content = ob_get_clean();

        include LAYOUTS . 'main_layout.php';
    }

    /**
     * Process Tambah Pengaduan
     */
    public function proses_tambah() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $user = getCurrentUser();

            $data = [
                'judul' => sanitize($_POST['judul'] ?? ''),
                'isi' => sanitize($_POST['isi'] ?? ''),
                'kategori' => sanitize($_POST['kategori'] ?? 'Lainnya'),
                'tanggal_pengaduan' => date('Y-m-d')
            ];

            if (!$data['judul'] || !$data['isi']) {
                setFlash('error', 'Judul dan isi pengaduan wajib diisi!');
                redirect('/pengaduan/tambah');
            }

            // Get warga id
            $id_warga = null;
            if ($user['role'] === 'warga' && isset($user['id_warga'])) {
                $id_warga = $user['id_warga'];
            } else {
                $id_warga = (int)($_POST['id_warga'] ?? 0);
            }

            if (!$id_warga) {
                setFlash('error', 'Warga tidak ditemukan!');
                redirect('/pengaduan/tambah');
            }

            // Handle lampiran
            $lampiranName = null;
            if (!empty($_FILES['lampiran']['name'])) {
                $upload = uploadFile($_FILES['lampiran'], UPLOAD_PENGADUAN, ['jpg', 'jpeg', 'png', 'pdf']);
                if ($upload['success']) {
                    $lampiranName = $upload['filename'];
                }
            }

            $stmt = $this->db->prepare("
                INSERT INTO pengaduan (id_warga, judul, isi, kategori, lampiran, tanggal_pengaduan)
                VALUES (?, ?, ?, ?, ?, ?)
            ");
            $stmt->execute([$id_warga, $data['judul'], $data['isi'], $data['kategori'], $lampiranName, $data['tanggal_pengaduan']]);

            // Create notification for admin
            $stmt = $this->db->query("SELECT id FROM users WHERE role IN ('admin', 'ketua_rt')");
            $admins = $stmt->fetchAll(PDO::FETCH_COLUMN);

            foreach ($admins as $adminId) {
                $stmt = $this->db->prepare("
                    INSERT INTO notifikasi (id_user, judul, isi, jenis, link)
                    VALUES (?, 'Pengaduan Baru', CONCAT('Pengaduan baru: ', ?), 'Pengaduan', '/pengaduan')
                ");
                $stmt->execute([$adminId, $data['judul']]);
            }

            logActivity('Ajukan pengaduan: ' . $data['judul'], 'Pengaduan');
            setFlash('success', 'Pengaduan berhasil diajukan!');
            redirect('/pengaduan');
        }

        redirect('/pengaduan/tambah');
    }

    /**
     * Respon Pengaduan (Admin)
     */
    public function respon() {
        requireRole(['admin', 'ketua_rt']);

        $id = (int)($_GET['id'] ?? 0);

        $stmt = $this->db->prepare("
            SELECT p.*, w.nama_lengkap, w.no_hp, w.email
            FROM pengaduan p
            INNER JOIN warga w ON p.id_warga = w.id
            WHERE p.id = ?
        ");
        $stmt->execute([$id]);
        $pengaduan = $stmt->fetch();

        if (!$pengaduan) {
            setFlash('error', 'Pengaduan tidak ditemukan!');
            redirect('/pengaduan');
        }

        $pageTitle = 'Respon Pengaduan';
        $breadcrumb = '<ol class="breadcrumb float-sm-right">
            <li class="breadcrumb-item"><a href="' . nginx_url('dashboard') . '">Home</a></li>
            <li class="breadcrumb-item"><a href="' . nginx_url('pengaduan') . '">Pengaduan</a></li>
            <li class="breadcrumb-item active">Respon</li>
        </ol>';

        ob_start();
        include VIEWSPATH . 'pengaduan/respon.php';
        $content = ob_get_clean();

        include LAYOUTS . 'main_layout.php';
    }

    /**
     * Process Respon
     */
    public function proses_respon() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            requireRole(['admin', 'ketua_rt']);

            $id = (int)($_POST['id'] ?? 0);
            $respon = sanitize($_POST['respon'] ?? '');
            $status = sanitize($_POST['status'] ?? 'Diproses');

            if (!$respon) {
                setFlash('error', 'Respon wajib diisi!');
                redirect('/pengaduan/respon?id=' . $id);
            }

            $tanggal_selesai = ($status === 'Selesai') ? date('Y-m-d') : null;

            $stmt = $this->db->prepare("
                UPDATE pengaduan SET respon = ?, respon_by = ?, respon_at = NOW(), status = ?, tanggal_selesai = ?
                WHERE id = ?
            ");
            $stmt->execute([$respon, $_SESSION['user_id'], $status, $tanggal_selesai, $id]);

            // Create notification
            $stmt = $this->db->prepare("
                SELECT id_warga FROM pengaduan WHERE id = ?
            ");
            $stmt->execute([$id]);
            $id_warga = $stmt->fetch()['id_warga'];

            $statusText = ($status === 'Selesai') ? 'selesai' : 'sedang diproses';
            $stmt = $this->db->prepare("
                INSERT INTO notifikasi (id_user, judul, isi, jenis, link)
                SELECT id, 'Respon Pengaduan', CONCAT('Pengaduan Anda ', ?), 'Pengaduan', '/pengaduan'
                FROM users WHERE role = 'warga' AND id_warga = ?
            ");
            $stmt->execute([$statusText, $id_warga]);

            logActivity('Respon pengaduan #' . $id, 'Pengaduan');
            setFlash('success', 'Respon berhasil disimpan!');
            redirect('/pengaduan');
        }

        redirect('/pengaduan');
    }

    /**
     * Hapus Pengaduan
     */
    public function hapus() {
        requireRole(['admin']);

        $id = (int)($_GET['id'] ?? 0);

        $stmt = $this->db->prepare("SELECT * FROM pengaduan WHERE id = ?");
        $stmt->execute([$id]);
        $pengaduan = $stmt->fetch();

        if ($pengaduan) {
            if ($pengaduan['lampiran']) {
                deleteFile(UPLOAD_PENGADUAN . $pengaduan['lampiran']);
            }

            $stmt = $this->db->prepare("DELETE FROM pengaduan WHERE id = ?");
            $stmt->execute([$id]);

            logActivity('Hapus pengaduan #' . $id, 'Pengaduan');
            setFlash('success', 'Pengaduan berhasil dihapus!');
        }

        redirect('/pengaduan');
    }
}