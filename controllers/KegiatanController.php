<?php
/**
 * Kegiatan Controller
 * SI-WargaRT - Sistem Informasi Warga RT
 */

class KegiatanController {

    private $db;

    public function __construct() {
        requireLogin();
        $this->db = getDb();
    }

    /**
     * Index - List Kegiatan
     */
    public function index() {
        $pageTitle = 'Kegiatan';
        $breadcrumb = '<ol class="breadcrumb float-sm-right">
            <li class="breadcrumb-item"><a href="' . nginx_url('dashboard') . '">Home</a></li>
            <li class="breadcrumb-item active">Kegiatan</li>
        </ol>';

        $stmt = $this->db->query("
            SELECT k.*, u.nama as created_by_name
            FROM kegiatan k
            LEFT JOIN users u ON k.created_by = u.id
            ORDER BY k.tanggal DESC, k.created_at DESC
        ");
        $kegiatan = $stmt->fetchAll();

        ob_start();
        include VIEWSPATH . 'kegiatan/index.php';
        $content = ob_get_clean();

        include LAYOUTS . 'main_layout.php';
    }

    /**
     * Tambah Kegiatan
     */
    public function tambah() {
        requireRole(['admin', 'ketua_rt']);

        $pageTitle = 'Tambah Kegiatan';
        $breadcrumb = '<ol class="breadcrumb float-sm-right">
            <li class="breadcrumb-item"><a href="' . nginx_url('dashboard') . '">Home</a></li>
            <li class="breadcrumb-item"><a href="' . nginx_url('kegiatan') . '">Kegiatan</a></li>
            <li class="breadcrumb-item active">Tambah</li>
        </ol>';

        ob_start();
        include VIEWSPATH . 'kegiatan/tambah.php';
        $content = ob_get_clean();

        include LAYOUTS . 'main_layout.php';
    }

    /**
     * Process Tambah Kegiatan
     */
    public function proses_tambah() {
        requireRole(['admin', 'ketua_rt']);

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = [
                'judul' => sanitize($_POST['judul'] ?? ''),
                'isi' => sanitize($_POST['isi'] ?? ''),
                'tanggal' => sanitize($_POST['tanggal'] ?? date('Y-m-d')),
                'waktu' => sanitize($_POST['waktu'] ?? ''),
                'lokasi' => sanitize($_POST['lokasi'] ?? ''),
                'status' => sanitize($_POST['status'] ?? 'Akan Datang')
            ];

            if (!$data['judul'] || !$data['tanggal']) {
                setFlash('error', 'Judul dan tanggal kegiatan wajib diisi!');
                redirect('/kegiatan/tambah');
            }

            // Handle banner
            $bannerName = null;
            if (!empty($_FILES['banner']['name'])) {
                $upload = uploadFile($_FILES['banner'], UPLOADPATH . 'kegiatan/', ['jpg', 'jpeg', 'png']);
                if ($upload['success']) {
                    $bannerName = $upload['filename'];
                }
            }

            $stmt = $this->db->prepare("
                INSERT INTO kegiatan (judul, isi, tanggal, waktu, lokasi, banner, status, created_by)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?)
            ");
            $stmt->execute([$data['judul'], $data['isi'], $data['tanggal'], $data['waktu'], $data['lokasi'], $bannerName, $data['status'], $_SESSION['user_id']]);

            logActivity('Tambah kegiatan: ' . $data['judul'], 'Kegiatan');
            setFlash('success', 'Kegiatan berhasil ditambahkan!');
            redirect('/kegiatan');
        }

        redirect('/kegiatan/tambah');
    }

    /**
     * Edit Kegiatan
     */
    public function edit() {
        requireRole(['admin', 'ketua_rt']);

        $id = (int)($_GET['id'] ?? 0);

        $stmt = $this->db->prepare("SELECT * FROM kegiatan WHERE id = ?");
        $stmt->execute([$id]);
        $kegiatan = $stmt->fetch();

        if (!$kegiatan) {
            setFlash('error', 'Kegiatan tidak ditemukan!');
            redirect('/kegiatan');
        }

        $pageTitle = 'Edit Kegiatan';
        $breadcrumb = '<ol class="breadcrumb float-sm-right">
            <li class="breadcrumb-item"><a href="' . nginx_url('dashboard') . '">Home</a></li>
            <li class="breadcrumb-item"><a href="' . nginx_url('kegiatan') . '">Kegiatan</a></li>
            <li class="breadcrumb-item active">Edit</li>
        </ol>';

        ob_start();
        include VIEWSPATH . 'kegiatan/edit.php';
        $content = ob_get_clean();

        include LAYOUTS . 'main_layout.php';
    }

    /**
     * Process Edit Kegiatan
     */
    public function proses_edit() {
        requireRole(['admin', 'ketua_rt']);

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = (int)($_POST['id'] ?? 0);

            $data = [
                'judul' => sanitize($_POST['judul'] ?? ''),
                'isi' => sanitize($_POST['isi'] ?? ''),
                'tanggal' => sanitize($_POST['tanggal'] ?? date('Y-m-d')),
                'waktu' => sanitize($_POST['waktu'] ?? ''),
                'lokasi' => sanitize($_POST['lokasi'] ?? ''),
                'status' => sanitize($_POST['status'] ?? 'Akan Datang')
            ];

            if (!$data['judul'] || !$data['tanggal']) {
                setFlash('error', 'Judul dan tanggal kegiatan wajib diisi!');
                redirect('/kegiatan/edit?id=' . $id);
            }

            // Handle banner
            $bannerName = $_POST['banner_lama'] ?? null;
            if (!empty($_FILES['banner']['name'])) {
                $upload = uploadFile($_FILES['banner'], UPLOADPATH . 'kegiatan/', ['jpg', 'jpeg', 'png']);
                if ($upload['success']) {
                    if ($bannerName) {
                        deleteFile(UPLOADPATH . 'kegiatan/' . $bannerName);
                    }
                    $bannerName = $upload['filename'];
                }
            }

            $stmt = $this->db->prepare("
                UPDATE kegiatan SET judul = ?, isi = ?, tanggal = ?, waktu = ?, lokasi = ?, banner = ?, status = ?
                WHERE id = ?
            ");
            $stmt->execute([$data['judul'], $data['isi'], $data['tanggal'], $data['waktu'], $data['lokasi'], $bannerName, $data['status'], $id]);

            logActivity('Edit kegiatan: ' . $data['judul'], 'Kegiatan');
            setFlash('success', 'Kegiatan berhasil diupdate!');
            redirect('/kegiatan');
        }

        redirect('/kegiatan');
    }

    /**
     * Hapus Kegiatan
     */
    public function hapus() {
        requireRole(['admin', 'ketua_rt']);

        $id = (int)($_GET['id'] ?? 0);

        $stmt = $this->db->prepare("SELECT * FROM kegiatan WHERE id = ?");
        $stmt->execute([$id]);
        $kegiatan = $stmt->fetch();

        if ($kegiatan) {
            if ($kegiatan['banner']) {
                deleteFile(UPLOADPATH . 'kegiatan/' . $kegiatan['banner']);
            }

            $stmt = $this->db->prepare("DELETE FROM kegiatan WHERE id = ?");
            $stmt->execute([$id]);

            logActivity('Hapus kegiatan: ' . $kegiatan['judul'], 'Kegiatan');
            setFlash('success', 'Kegiatan berhasil dihapus!');
        }

        redirect('/kegiatan');
    }

    /**
     * Detail Kegiatan
     */
    public function detail() {
        $id = (int)($_GET['id'] ?? 0);

        $stmt = $this->db->prepare("
            SELECT k.*, u.nama as created_by_name
            FROM kegiatan k
            LEFT JOIN users u ON k.created_by = u.id
            WHERE k.id = ?
        ");
        $stmt->execute([$id]);
        $kegiatan = $stmt->fetch();

        if (!$kegiatan) {
            setFlash('error', 'Kegiatan tidak ditemukan!');
            redirect('/kegiatan');
        }

        $pageTitle = 'Detail Kegiatan';
        $breadcrumb = '<ol class="breadcrumb float-sm-right">
            <li class="breadcrumb-item"><a href="' . nginx_url('dashboard') . '">Home</a></li>
            <li class="breadcrumb-item"><a href="' . nginx_url('kegiatan') . '">Kegiatan</a></li>
            <li class="breadcrumb-item active">Detail</li>
        </ol>';

        ob_start();
        include VIEWSPATH . 'kegiatan/detail.php';
        $content = ob_get_clean();

        include LAYOUTS . 'main_layout.php';
    }
}