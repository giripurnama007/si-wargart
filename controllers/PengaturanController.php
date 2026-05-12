<?php
/**
 * Pengaturan Controller
 * SI-WargaRT - Sistem Informasi Warga RT
 */

class PengaturanController {

    private $db;

    public function __construct() {
        requireRole(['admin']);
        $this->db = getDb();
    }

    /**
     * Index - Pengaturan
     */
    public function index() {
        $pageTitle = 'Pengaturan';
        $breadcrumb = '<ol class="breadcrumb float-sm-right">
            <li class="breadcrumb-item"><a href="' . nginx_url('dashboard') . '">Home</a></li>
            <li class="breadcrumb-item active">Pengaturan</li>
        </ol>';

        // Get pengaturan
        $stmt = $this->db->query("SELECT * FROM pengaturan LIMIT 1");
        $pengaturan = $stmt->fetch();

        // Get user list
        $stmt = $this->db->query("
            SELECT u.*, w.nik 
            FROM users u 
            LEFT JOIN warga w ON u.id_warga = w.id 
            ORDER BY u.role, u.nama
        ");
        $users = $stmt->fetchAll();

        // Get jenis iuran
        $stmt = $this->db->query("SELECT * FROM jenis_iuran ORDER BY jenis, nama_iuran");
        $jenisIuran = $stmt->fetchAll();

        // Get list of warga for NIK dropdown
        $stmt = $this->db->query("SELECT nik, nama_lengkap FROM warga ORDER BY nama_lengkap");
        $listWarga = $stmt->fetchAll();

        ob_start();
        include VIEWSPATH . 'pengaturan/index.php';
        $content = ob_get_clean();

        include LAYOUTS . 'main_layout.php';
    }

    /**
     * Update Pengaturan
     */
    public function update() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = [
                'nama_aplikasi' => sanitize($_POST['nama_aplikasi'] ?? 'SI-WargaRT'),
                'alamat' => sanitize($_POST['alamat'] ?? ''),
                'no_telepon' => sanitize($_POST['no_telepon'] ?? ''),
                'email' => sanitize($_POST['email'] ?? ''),
                'nama_rt' => sanitize($_POST['nama_rt'] ?? 'RT 01'),
                'nama_rw' => sanitize($_POST['nama_rw'] ?? 'RW 01'),
                'nama_ketua_rt' => sanitize($_POST['nama_ketua_rt'] ?? '')
            ];

            // Handle logo upload
            $logoName = $_POST['logo_lama'] ?? 'logo.png';
            if (!empty($_FILES['logo']['name'])) {
                $upload = uploadFile($_FILES['logo'], BASEPATH . 'assets/img/', ['png', 'jpg', 'jpeg']);
                if ($upload['success']) {
                    if ($logoName !== 'logo.png') {
                        deleteFile(BASEPATH . 'assets/img/' . $logoName);
                    }
                    $logoName = $upload['filename'];
                }
            }

            $stmt = $this->db->prepare("
                UPDATE pengaturan SET
                    nama_aplikasi = ?, alamat = ?, no_telepon = ?, email = ?,
                    nama_rt = ?, nama_rw = ?, nama_ketua_rt = ?, logo = ?
                WHERE id = 1
            ");
            $stmt->execute([$data['nama_aplikasi'], $data['alamat'], $data['no_telepon'], $data['email'],
                          $data['nama_rt'], $data['nama_rw'], $data['nama_ketua_rt'], $logoName]);

            logActivity('Update pengaturan sistem', 'Pengaturan');
            setFlash('success', 'Pengaturan berhasil disimpan!');
            redirect('/pengaturan');
        }

        redirect('/pengaturan');
    }

    /**
     * Tambah User
     */
    public function tambah_user() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $nik = sanitize($_POST['nik'] ?? '');
            $username = sanitize($_POST['username'] ?? '');
            $nama = sanitize($_POST['nama'] ?? '');
            $email = sanitize($_POST['email'] ?? '');
            $no_hp = sanitize($_POST['no_hp'] ?? '');
            $password = sanitize($_POST['password'] ?? '');
            $role = sanitize($_POST['role'] ?? 'warga');

            if (!$username || !$nama || !$password) {
                setFlash('error', 'Data tidak lengkap!');
                redirect('/pengaturan');
            }

            // Check username exists
            $stmt = $this->db->prepare("SELECT id FROM users WHERE username = ?");
            $stmt->execute([$username]);
            if ($stmt->fetch()) {
                setFlash('error', 'Username sudah digunakan!');
                redirect('/pengaturan');
            }

            $id_warga = null;
            if ($role === 'warga') {
                if (empty($nik)) {
                    setFlash('error', 'NIK wajib diisi untuk role Warga!');
                    redirect('/pengaturan');
                }
                
                $stmt = $this->db->prepare("SELECT id FROM warga WHERE nik = ?");
                $stmt->execute([$nik]);
                $warga = $stmt->fetch();

                if (!$warga) {
                    setFlash('error', 'NIK tidak ditemukan di data Warga!');
                    redirect('/pengaturan');
                }
                
                $id_warga = $warga['id'];

                $stmt = $this->db->prepare("SELECT id FROM users WHERE id_warga = ?");
                $stmt->execute([$id_warga]);
                if ($stmt->fetch()) {
                    setFlash('error', 'Warga dengan NIK tersebut sudah memiliki akun!');
                    redirect('/pengaturan');
                }
            }

            $stmt = $this->db->prepare("
                INSERT INTO users (id_warga, username, password, nama, email, no_hp, role)
                VALUES (?, ?, ?, ?, ?, ?, ?)
            ");
            $stmt->execute([$id_warga, $username, hashPassword($password), $nama, $email, $no_hp, $role]);

            logActivity('Tambah user: ' . $nama, 'Pengaturan');
            setFlash('success', 'User berhasil ditambahkan!');
            redirect('/pengaturan');
        }

        redirect('/pengaturan');
    }

    /**
     * Edit User
     */
    public function edit_user() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = (int)($_POST['id'] ?? 0);
            $nama = sanitize($_POST['nama'] ?? '');
            $email = sanitize($_POST['email'] ?? '');
            $no_hp = sanitize($_POST['no_hp'] ?? '');
            $role = sanitize($_POST['role'] ?? 'warga');
            $password = $_POST['password'] ?? '';
            $nik = sanitize($_POST['nik'] ?? '');

            $id_warga = null;
            if ($role === 'warga') {
                if (empty($nik)) {
                    setFlash('error', 'NIK wajib diisi untuk role Warga!');
                    redirect('/pengaturan');
                }
                
                $stmt = $this->db->prepare("SELECT id FROM warga WHERE nik = ?");
                $stmt->execute([$nik]);
                $warga = $stmt->fetch();

                if (!$warga) {
                    setFlash('error', 'NIK tidak ditemukan di data Warga!');
                    redirect('/pengaturan');
                }
                
                $id_warga = $warga['id'];

                $stmt = $this->db->prepare("SELECT id FROM users WHERE id_warga = ? AND id != ?");
                $stmt->execute([$id_warga, $id]);
                if ($stmt->fetch()) {
                    setFlash('error', 'Warga dengan NIK tersebut sudah memiliki akun lain!');
                    redirect('/pengaturan');
                }
            }

            // Jika role bukan warga, set id_warga ke null
            if ($role !== 'warga') {
                $id_warga = null;
            }

            $sql = "UPDATE users SET nama = ?, email = ?, no_hp = ?, role = ?, id_warga = ?";
            $params = [$nama, $email, $no_hp, $role, $id_warga];

            if ($password) {
                $sql .= ", password = ?";
                $params[] = hashPassword($password);
            }

            $sql .= " WHERE id = ?";
            $params[] = $id;

            $stmt = $this->db->prepare($sql);
            $stmt->execute($params);

            logActivity('Edit user: ' . $nama, 'Pengaturan');
            setFlash('success', 'User berhasil diupdate!');
            redirect('/pengaturan');
        }

        redirect('/pengaturan');
    }

    /**
     * Hapus User
     */
    public function hapus_user() {
        $id = (int)($_GET['id'] ?? 0);

        if ($id == $_SESSION['user_id']) {
            setFlash('error', 'Tidak dapat menghapus user sendiri!');
            redirect('/pengaturan');
        }

        $stmt = $this->db->prepare("DELETE FROM users WHERE id = ?");
        $stmt->execute([$id]);

        logActivity('Hapus user #' . $id, 'Pengaturan');
        setFlash('success', 'User berhasil dihapus!');
        redirect('/pengaturan');
    }

    /**
     * Toggle Status User (Aktif / Non-Aktif)
     */
    public function toggle_user() {
        $id = (int)($_GET['id'] ?? 0);

        if ($id == $_SESSION['user_id']) {
            setFlash('error', 'Tidak dapat mengubah status akun sendiri!');
            redirect('/pengaturan');
        }

        $stmt = $this->db->prepare("SELECT id, is_active, nama, role, id_warga FROM users WHERE id = ?");
        $stmt->execute([$id]);
        $user = $stmt->fetch();

        if ($user) {
            $new_status = $user['is_active'] ? 0 : 1;
            $stmt = $this->db->prepare("UPDATE users SET is_active = ? WHERE id = ?");
            $stmt->execute([$new_status, $id]);

            // Jika diaktifkan, dan role warga, aktifkan juga status warga di tabel warga
            if ($new_status == 1 && $user['role'] === 'warga' && $user['id_warga']) {
                $stmtWarga = $this->db->prepare("UPDATE warga SET status_warga = 'Aktif' WHERE id = ? AND status_warga = 'Non-Aktif'");
                $stmtWarga->execute([$user['id_warga']]);
            }

            $status_text = $new_status ? 'diaktifkan' : 'dinonaktifkan';
            logActivity("Mengubah status user {$user['nama']} menjadi $status_text", 'Pengaturan');
            setFlash('success', "Akun {$user['nama']} berhasil $status_text!");
        }

        redirect('/pengaturan');
    }

    /**
     * Tambah Jenis Iuran
     */
    public function tambah_iuran() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $nama_iuran = sanitize($_POST['nama_iuran'] ?? '');
            $jenis = sanitize($_POST['jenis'] ?? '');
            $jumlah = (float)($_POST['jumlah'] ?? 0);
            $deskripsi = sanitize($_POST['deskripsi'] ?? '');

            if (!$nama_iuran || !$jenis) {
                setFlash('error', 'Data tidak lengkap!');
                redirect('/pengaturan');
            }

            $stmt = $this->db->prepare("
                INSERT INTO jenis_iuran (nama_iuran, jenis, jumlah, deskripsi)
                VALUES (?, ?, ?, ?)
            ");
            $stmt->execute([$nama_iuran, $jenis, $jumlah, $deskripsi]);

            logActivity('Tambah jenis iuran: ' . $nama_iuran, 'Pengaturan');
            setFlash('success', 'Jenis iuran berhasil ditambahkan!');
            redirect('/pengaturan');
        }

        redirect('/pengaturan');
    }

    /**
     * Edit Jenis Iuran
     */
    public function edit_iuran() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = (int)($_POST['id'] ?? 0);
            $nama_iuran = sanitize($_POST['nama_iuran'] ?? '');
            $jenis = sanitize($_POST['jenis'] ?? '');
            $jumlah = (float)($_POST['jumlah'] ?? 0);
            $deskripsi = sanitize($_POST['deskripsi'] ?? '');
            $is_active = isset($_POST['is_active']) ? 1 : 0;

            $stmt = $this->db->prepare("
                UPDATE jenis_iuran SET nama_iuran = ?, jenis = ?, jumlah = ?, deskripsi = ?, is_active = ?
                WHERE id = ?
            ");
            $stmt->execute([$nama_iuran, $jenis, $jumlah, $deskripsi, $is_active, $id]);

            logActivity('Edit jenis iuran: ' . $nama_iuran, 'Pengaturan');
            setFlash('success', 'Jenis iuran berhasil diupdate!');
            redirect('/pengaturan');
        }

        redirect('/pengaturan');
    }

    /**
     * Hapus Jenis Iuran
     */
    public function hapus_iuran() {
        $id = (int)($_GET['id'] ?? 0);

        $stmt = $this->db->prepare("DELETE FROM jenis_iuran WHERE id = ?");
        $stmt->execute([$id]);

        logActivity('Hapus jenis iuran #' . $id, 'Pengaturan');
        setFlash('success', 'Jenis iuran berhasil dihapus!');
        redirect('/pengaturan');
    }
}