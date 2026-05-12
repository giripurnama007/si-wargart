<?php
/**
 * KK Controller
 * SI-WargaRT - Sistem Informasi Warga RT
 */

class KKController {

    private $db;

    public function __construct() {
        requireRole(['admin', 'ketua_rt']);
        $this->db = getDb();
    }

    /**
     * Index - List KK
     */
    public function index() {
        $pageTitle = 'Kartu Keluarga';
        $breadcrumb = '<ol class="breadcrumb float-sm-right">
            <li class="breadcrumb-item"><a href="' . nginx_url('dashboard') . '">Home</a></li>
            <li class="breadcrumb-item active">Kartu Keluarga</li>
        </ol>';

        $stmt = $this->db->query("
            SELECT kk.*,
                   (SELECT COUNT(*) FROM warga WHERE no_kk = kk.no_kk) as jumlah_anggota
            FROM kartu_keluarga kk
            ORDER BY kk.no_kk
        ");
        $kk = $stmt->fetchAll();

        ob_start();
        include VIEWSPATH . 'kk/index.php';
        $content = ob_get_clean();

        include LAYOUTS . 'main_layout.php';
    }

    /**
     * Tambah KK
     */
    public function tambah() {
        $pageTitle = 'Tambah Kartu Keluarga';
        $breadcrumb = '<ol class="breadcrumb float-sm-right">
            <li class="breadcrumb-item"><a href="' . nginx_url('dashboard') . '">Home</a></li>
            <li class="breadcrumb-item"><a href="' . nginx_url('kk') . '">Kartu Keluarga</a></li>
            <li class="breadcrumb-item active">Tambah</li>
        </ol>';

        ob_start();
        include VIEWSPATH . 'kk/tambah.php';
        $content = ob_get_clean();

        include LAYOUTS . 'main_layout.php';
    }

    /**
     * Process Tambah KK
     */
    public function proses_tambah() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = [
                'no_kk' => sanitize($_POST['no_kk'] ?? ''),
                'nik_kepala' => sanitize($_POST['nik_kepala'] ?? ''),
                'nama_kepala' => sanitize($_POST['nama_kepala'] ?? ''),
                'alamat' => sanitize($_POST['alamat'] ?? ''),
                'rt' => sanitize($_POST['rt'] ?? '01'),
                'rw' => sanitize($_POST['rw'] ?? '01'),
                'kode_pos' => sanitize($_POST['kode_pos'] ?? '')
            ];

            if (!$data['no_kk'] || !$data['nama_kepala']) {
                setFlash('error', 'No. KK dan Nama Kepala Keluarga wajib diisi!');
                redirect('/kk/tambah');
            }

            // Check no_kk exists
            $stmt = $this->db->prepare("SELECT id FROM kartu_keluarga WHERE no_kk = ?");
            $stmt->execute([$data['no_kk']]);
            if ($stmt->fetch()) {
                setFlash('error', 'No. KK sudah terdaftar!');
                redirect('/kk/tambah');
            }

            // Handle foto
            $fotoName = null;
            if (!empty($_FILES['foto_kk']['name'])) {
                $upload = uploadFile($_FILES['foto_kk'], UPLOAD_WARGA, ['jpg', 'jpeg', 'png', 'pdf']);
                if ($upload['success']) {
                    $fotoName = $upload['filename'];
                }
            }

            $stmt = $this->db->prepare("
                INSERT INTO kartu_keluarga (no_kk, nik_kepala, nama_kepala, alamat, rt, rw, kode_pos, foto_kk)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?)
            ");
            $stmt->execute([$data['no_kk'], $data['nik_kepala'], $data['nama_kepala'], $data['alamat'],
                           $data['rt'], $data['rw'], $data['kode_pos'], $fotoName]);

            logActivity('Tambah KK: ' . $data['no_kk'], 'KK');
            setFlash('success', 'Kartu Keluarga berhasil ditambahkan!');
            redirect('/kk');
        }

        redirect('/kk/tambah');
    }

    /**
     * Edit KK
     */
    public function edit() {
        $id = (int)($_GET['id'] ?? 0);

        $stmt = $this->db->prepare("SELECT * FROM kartu_keluarga WHERE id = ?");
        $stmt->execute([$id]);
        $kk = $stmt->fetch();

        if (!$kk) {
            setFlash('error', 'Kartu Keluarga tidak ditemukan!');
            redirect('/kk');
        }

        // Get anggota
        $stmt = $this->db->prepare("SELECT * FROM warga WHERE no_kk = ? ORDER BY status_perkawinan DESC, tanggal_lahir ASC");
        $stmt->execute([$kk['no_kk']]);
        $anggota = $stmt->fetchAll();

        $pageTitle = 'Edit Kartu Keluarga';
        $breadcrumb = '<ol class="breadcrumb float-sm-right">
            <li class="breadcrumb-item"><a href="' . nginx_url('dashboard') . '">Home</a></li>
            <li class="breadcrumb-item"><a href="' . nginx_url('kk') . '">Kartu Keluarga</a></li>
            <li class="breadcrumb-item active">Edit</li>
        </ol>';

        ob_start();
        include VIEWSPATH . 'kk/edit.php';
        $content = ob_get_clean();

        include LAYOUTS . 'main_layout.php';
    }

    /**
     * Process Edit KK
     */
    public function proses_edit() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = (int)($_POST['id'] ?? 0);

            $data = [
                'no_kk' => sanitize($_POST['no_kk'] ?? ''),
                'nik_kepala' => sanitize($_POST['nik_kepala'] ?? ''),
                'nama_kepala' => sanitize($_POST['nama_kepala'] ?? ''),
                'alamat' => sanitize($_POST['alamat'] ?? ''),
                'rt' => sanitize($_POST['rt'] ?? '01'),
                'rw' => sanitize($_POST['rw'] ?? '01'),
                'kode_pos' => sanitize($_POST['kode_pos'] ?? '')
            ];

            if (!$data['no_kk'] || !$data['nama_kepala']) {
                setFlash('error', 'No. KK dan Nama Kepala Keluarga wajib diisi!');
                redirect('/kk/edit?id=' . $id);
            }

            // Handle foto
            $fotoName = $_POST['foto_kk_lama'] ?? null;
            if (!empty($_FILES['foto_kk']['name'])) {
                $upload = uploadFile($_FILES['foto_kk'], UPLOAD_WARGA, ['jpg', 'jpeg', 'png', 'pdf']);
                if ($upload['success']) {
                    if ($fotoName) {
                        deleteFile(UPLOAD_WARGA . $fotoName);
                    }
                    $fotoName = $upload['filename'];
                }
            }

            $stmt = $this->db->prepare("
                UPDATE kartu_keluarga SET no_kk = ?, nik_kepala = ?, nama_kepala = ?, alamat = ?, rt = ?, rw = ?, kode_pos = ?, foto_kk = ?
                WHERE id = ?
            ");
            $stmt->execute([$data['no_kk'], $data['nik_kepala'], $data['nama_kepala'], $data['alamat'],
                           $data['rt'], $data['rw'], $data['kode_pos'], $fotoName, $id]);

            logActivity('Edit KK: ' . $data['no_kk'], 'KK');
            setFlash('success', 'Kartu Keluarga berhasil diupdate!');
            redirect('/kk');
        }

        redirect('/kk');
    }

    /**
     * Hapus KK
     */
    public function hapus() {
        $id = (int)($_GET['id'] ?? 0);

        $stmt = $this->db->prepare("SELECT * FROM kartu_keluarga WHERE id = ?");
        $stmt->execute([$id]);
        $kk = $stmt->fetch();

        if ($kk) {
            // Check if warga exists with this KK
            $stmt = $this->db->prepare("SELECT COUNT(*) as total FROM warga WHERE no_kk = ?");
            $stmt->execute([$kk['no_kk']]);
            $count = $stmt->fetch()['total'];

            if ($count > 0) {
                setFlash('error', 'Tidak dapat menghapus KK! Terdapat ' . $count . ' warga yang masih terhubung.');
                redirect('/kk');
            }

            if ($kk['foto_kk']) {
                deleteFile(UPLOAD_WARGA . $kk['foto_kk']);
            }

            $stmt = $this->db->prepare("DELETE FROM kartu_keluarga WHERE id = ?");
            $stmt->execute([$id]);

            logActivity('Hapus KK: ' . $kk['no_kk'], 'KK');
            setFlash('success', 'Kartu Keluarga berhasil dihapus!');
        }

        redirect('/kk');
    }

    /**
     * Detail KK
     */
    public function detail() {
        $id = (int)($_GET['id'] ?? 0);

        $stmt = $this->db->prepare("SELECT * FROM kartu_keluarga WHERE id = ?");
        $stmt->execute([$id]);
        $kk = $stmt->fetch();

        if (!$kk) {
            setFlash('error', 'Kartu Keluarga tidak ditemukan!');
            redirect('/kk');
        }

        // Get anggota keluarga berdasarkan no_kk
        $stmt = $this->db->prepare("SELECT * FROM warga WHERE no_kk = ? ORDER BY status_perkawinan DESC, tanggal_lahir ASC");
        $stmt->execute([$kk['no_kk']]);
        $anggota = $stmt->fetchAll();

        $pageTitle = 'Detail Kartu Keluarga';
        $breadcrumb = '<ol class="breadcrumb float-sm-right">
            <li class="breadcrumb-item"><a href="' . nginx_url('dashboard') . '">Home</a></li>
            <li class="breadcrumb-item"><a href="' . nginx_url('kk') . '">Kartu Keluarga</a></li>
            <li class="breadcrumb-item active">Detail</li>
        </ol>';

        ob_start();
        include VIEWSPATH . 'kk/detail.php';
        $content = ob_get_clean();

        include LAYOUTS . 'main_layout.php';
    }
}