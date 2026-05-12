<?php
/**
 * Forum Controller
 * SI-WargaRT - Sistem Informasi Warga RT
 */

class ForumController {

    private $db;

    public function __construct() {
        requireLogin();
        $this->db = getDb();
    }

    /**
     * Index - Daftar Topik Forum
     */
    public function index() {
        $pageTitle = 'Forum Warga';
        $breadcrumb = '<ol class="breadcrumb float-sm-right">
            <li class="breadcrumb-item"><a href="' . nginx_url('dashboard') . '">Home</a></li>
            <li class="breadcrumb-item active">Forum Warga</li>
        </ol>';

        // Ambil semua topik beserta jumlah komentar dan nama pembuatnya
        $stmt = $this->db->query("
            SELECT t.*, u.nama as pembuat, u.foto,
                   (SELECT COUNT(k.id) FROM forum_komentar k WHERE k.id_topik = t.id) as total_komentar
            FROM forum_topik t
            INNER JOIN users u ON t.id_user = u.id
            ORDER BY t.created_at DESC
        ");
        $topik = $stmt->fetchAll();

        ob_start();
        include VIEWSPATH . 'forum/index.php';
        $content = ob_get_clean();

        include LAYOUTS . 'main_layout.php';
    }

    /**
     * Form Tambah Topik
     */
    public function tambah() {
        $pageTitle = 'Buat Topik Baru';
        $breadcrumb = '<ol class="breadcrumb float-sm-right">
            <li class="breadcrumb-item"><a href="' . nginx_url('dashboard') . '">Home</a></li>
            <li class="breadcrumb-item"><a href="' . nginx_url('forum') . '">Forum Warga</a></li>
            <li class="breadcrumb-item active">Buat Topik</li>
        </ol>';

        ob_start();
        include VIEWSPATH . 'forum/tambah.php';
        $content = ob_get_clean();

        include LAYOUTS . 'main_layout.php';
    }

    /**
     * Proses Tambah Topik
     */
    public function proses_tambah() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $judul = sanitize($_POST['judul'] ?? '');
            $isi = sanitize($_POST['isi'] ?? '');
            $id_user = $_SESSION['user_id'];

            if (empty($judul) || empty($isi)) {
                setFlash('error', 'Judul dan isi topik tidak boleh kosong!');
                redirect('/forum/tambah');
            }

            $stmt = $this->db->prepare("INSERT INTO forum_topik (id_user, judul, isi, status) VALUES (?, ?, ?, 'Open')");
            $stmt->execute([$id_user, $judul, $isi]);

            logActivity('Membuat topik forum baru: ' . $judul, 'Forum');
            setFlash('success', 'Topik berhasil dibuat!');
            redirect('/forum');
        }
        redirect('/forum/tambah');
    }

    /**
     * Detail Topik & Daftar Komentar
     */
    public function detail() {
        $id = (int)($_GET['id'] ?? 0);

        // Ambil data topik
        $stmt = $this->db->prepare("SELECT t.*, u.nama, u.foto FROM forum_topik t INNER JOIN users u ON t.id_user = u.id WHERE t.id = ?");
        $stmt->execute([$id]);
        $topik = $stmt->fetch();

        if (!$topik) {
            setFlash('error', 'Topik tidak ditemukan!');
            redirect('/forum');
        }

        // Update views counter
        $this->db->prepare("UPDATE forum_topik SET views = views + 1 WHERE id = ?")->execute([$id]);

        // Ambil komentar
        $stmt = $this->db->prepare("
            SELECT k.*, u.nama, u.foto, u.role
            FROM forum_komentar k
            INNER JOIN users u ON k.id_user = u.id
            WHERE k.id_topik = ? 
            ORDER BY k.created_at ASC
        ");
        $stmt->execute([$id]);
        $komentar = $stmt->fetchAll();

        $pageTitle = 'Diskusi Warga';
        $breadcrumb = '<ol class="breadcrumb float-sm-right">
            <li class="breadcrumb-item"><a href="' . nginx_url('dashboard') . '">Home</a></li>
            <li class="breadcrumb-item"><a href="' . nginx_url('forum') . '">Forum</a></li>
            <li class="breadcrumb-item active">Diskusi</li>
        </ol>';

        ob_start();
        include VIEWSPATH . 'forum/detail.php';
        $content = ob_get_clean();

        include LAYOUTS . 'main_layout.php';
    }

    /**
     * Proses Tambah Komentar
     */
    public function proses_komentar() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id_topik = (int)($_POST['id_topik'] ?? 0);
            $isi = sanitize($_POST['isi'] ?? '');
            $id_user = $_SESSION['user_id'];

            if (!empty($isi)) {
                $stmt = $this->db->prepare("INSERT INTO forum_komentar (id_topik, id_user, isi) VALUES (?, ?, ?)");
                $stmt->execute([$id_topik, $id_user, $isi]);

                setFlash('success', 'Komentar berhasil dikirim!');
            }
            redirect('/forum/detail?id=' . $id_topik);
        }
        redirect('/forum');
    }

    /**
     * Hapus Topik (Admin Only)
     */
    public function hapus() {
        requireRole(['admin', 'ketua_rt']);
        $id = (int)($_GET['id'] ?? 0);

        $stmt = $this->db->prepare("DELETE FROM forum_topik WHERE id = ?");
        $stmt->execute([$id]);

        setFlash('success', 'Topik diskusi berhasil dihapus!');
        redirect('/forum');
    }
}