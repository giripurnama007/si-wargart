<?php
/**
 * Pengumuman Controller
 * SI-WargaRT - Sistem Informasi Warga RT
 */

class PengumumanController {

    private $db;

    public function __construct() {
        requireRole(['admin', 'ketua_rt']);
        $this->db = getDb();
    }

    /**
     * Index - List Pengumuman
     */
    public function index() {
        $pageTitle = 'Pengumuman';
        $breadcrumb = '<ol class="breadcrumb float-sm-right">
            <li class="breadcrumb-item"><a href="' . nginx_url('dashboard') . '">Home</a></li>
            <li class="breadcrumb-item active">Pengumuman</li>
        </ol>';

        $stmt = $this->db->query("
            SELECT p.*, u.nama as created_by_name
            FROM pengumuman p
            LEFT JOIN users u ON p.created_by = u.id
            ORDER BY p.tanggal DESC, p.created_at DESC
        ");
        $pengumuman = $stmt->fetchAll();

        ob_start();
        include VIEWSPATH . 'pengumuman/index.php';
        $content = ob_get_clean();

        include LAYOUTS . 'main_layout.php';
    }

    /**
     * Tambah Pengumuman
     */
    public function tambah() {
        $pageTitle = 'Tambah Pengumuman';
        $breadcrumb = '<ol class="breadcrumb float-sm-right">
            <li class="breadcrumb-item"><a href="' . nginx_url('dashboard') . '">Home</a></li>
            <li class="breadcrumb-item"><a href="' . nginx_url('pengumuman') . '">Pengumuman</a></li>
            <li class="breadcrumb-item active">Tambah</li>
        </ol>';

        ob_start();
        include VIEWSPATH . 'pengumuman/tambah.php';
        $content = ob_get_clean();

        include LAYOUTS . 'main_layout.php';
    }

    /**
     * Process Tambah Pengumuman
     */
    public function proses_tambah() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = [
                'judul' => sanitize($_POST['judul'] ?? ''),
                'isi' => sanitize($_POST['isi'] ?? ''),
                'tanggal' => sanitize($_POST['tanggal'] ?? date('Y-m-d')),
                'status' => sanitize($_POST['status'] ?? 'Published')
            ];

            if (!$data['judul'] || !$data['isi']) {
                setFlash('error', 'Judul dan isi pengumuman wajib diisi!');
                redirect('/pengumuman/tambah');
            }

            // Handle banner
            $bannerName = null;
            if (!empty($_FILES['banner']['name'])) {
                $upload = uploadFile($_FILES['banner'], UPLOADPATH . 'pengumuman/', ['jpg', 'jpeg', 'png']);
                if ($upload['success']) {
                    $bannerName = $upload['filename'];
                }
            }

            $stmt = $this->db->prepare("
                INSERT INTO pengumuman (judul, isi, tanggal, banner, status, created_by)
                VALUES (?, ?, ?, ?, ?, ?)
            ");
            $stmt->execute([$data['judul'], $data['isi'], $data['tanggal'], $bannerName, $data['status'], $_SESSION['user_id']]);

            // Send notification to all warga users
            $this->sendNotification('Pengumuman Baru', $data['judul'], '/pengumuman');

            logActivity('Tambah pengumuman: ' . $data['judul'], 'Pengumuman');
            setFlash('success', 'Pengumuman berhasil dipublikasikan!');
            redirect('/pengumuman');
        }

        redirect('/pengumuman/tambah');
    }

    /**
     * Edit Pengumuman
     */
    public function edit() {
        $id = (int)($_GET['id'] ?? 0);

        $stmt = $this->db->prepare("SELECT * FROM pengumuman WHERE id = ?");
        $stmt->execute([$id]);
        $pengumuman = $stmt->fetch();

        if (!$pengumuman) {
            setFlash('error', 'Pengumuman tidak ditemukan!');
            redirect('/pengumuman');
        }

        $pageTitle = 'Edit Pengumuman';
        $breadcrumb = '<ol class="breadcrumb float-sm-right">
            <li class="breadcrumb-item"><a href="' . nginx_url('dashboard') . '">Home</a></li>
            <li class="breadcrumb-item"><a href="' . nginx_url('pengumuman') . '">Pengumuman</a></li>
            <li class="breadcrumb-item active">Edit</li>
        </ol>';

        ob_start();
        include VIEWSPATH . 'pengumuman/edit.php';
        $content = ob_get_clean();

        include LAYOUTS . 'main_layout.php';
    }

    /**
     * Process Edit Pengumuman
     */
    public function proses_edit() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = (int)($_POST['id'] ?? 0);

            $data = [
                'judul' => sanitize($_POST['judul'] ?? ''),
                'isi' => sanitize($_POST['isi'] ?? ''),
                'tanggal' => sanitize($_POST['tanggal'] ?? date('Y-m-d')),
                'status' => sanitize($_POST['status'] ?? 'Published')
            ];

            if (!$data['judul'] || !$data['isi']) {
                setFlash('error', 'Judul dan isi pengumuman wajib diisi!');
                redirect('/pengumuman/edit?id=' . $id);
            }

            // Handle banner
            $bannerName = $_POST['banner_lama'] ?? null;
            if (!empty($_FILES['banner']['name'])) {
                $upload = uploadFile($_FILES['banner'], UPLOADPATH . 'pengumuman/', ['jpg', 'jpeg', 'png']);
                if ($upload['success']) {
                    if ($bannerName) {
                        deleteFile(UPLOADPATH . 'pengumuman/' . $bannerName);
                    }
                    $bannerName = $upload['filename'];
                }
            }

            $stmt = $this->db->prepare("
                UPDATE pengumuman SET judul = ?, isi = ?, tanggal = ?, banner = ?, status = ?
                WHERE id = ?
            ");
            $stmt->execute([$data['judul'], $data['isi'], $data['tanggal'], $bannerName, $data['status'], $id]);

            logActivity('Edit pengumuman: ' . $data['judul'], 'Pengumuman');
            setFlash('success', 'Pengumuman berhasil diupdate!');
            redirect('/pengumuman');
        }

        redirect('/pengumuman');
    }

    /**
     * Hapus Pengumuman
     */
    public function hapus() {
        $id = (int)($_GET['id'] ?? 0);

        $stmt = $this->db->prepare("SELECT * FROM pengumuman WHERE id = ?");
        $stmt->execute([$id]);
        $pengumuman = $stmt->fetch();

        if ($pengumuman) {
            if ($pengumuman['banner']) {
                deleteFile(UPLOADPATH . 'pengumuman/' . $pengumuman['banner']);
            }

            $stmt = $this->db->prepare("DELETE FROM pengumuman WHERE id = ?");
            $stmt->execute([$id]);

            logActivity('Hapus pengumuman: ' . $pengumuman['judul'], 'Pengumuman');
            setFlash('success', 'Pengumuman berhasil dihapus!');
        }

        redirect('/pengumuman');
    }

    /**
     * Send Notification to All Users
     */
    private function sendNotification($judul, $isi, $link) {
        $stmt = $this->db->query("SELECT id FROM users");
        $users = $stmt->fetchAll(PDO::FETCH_COLUMN);

        foreach ($users as $userId) {
            $stmt = $this->db->prepare("INSERT INTO notifikasi (id_user, judul, isi, jenis, link) VALUES (?, ?, ?, 'Pengumuman', ?)");
            $stmt->execute([$userId, $judul, $isi, $link]);
        }
    }
}