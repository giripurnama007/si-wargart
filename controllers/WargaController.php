<?php
/**
 * Warga Controller
 * SI-WargaRT - Sistem Informasi Warga RT
 */

class WargaController {

    private $db;

    public function __construct() {
        requireRole(['admin', 'ketua_rt']);
        $this->db = getDb();
    }

    /**
     * Index - List Warga
     */
    public function index() {
        $search = sanitize($_GET['search'] ?? '');
        $status = sanitize($_GET['status'] ?? '');

        $page = (int)($_GET['page'] ?? 1);
        $perPage = PER_PAGE;
        $offset = ($page - 1) * $perPage;

        $where = "WHERE 1=1";
        $params = [];

        if ($search) {
            $where .= " AND (nama_lengkap LIKE ? OR nik LIKE ? OR no_kk LIKE ?)";
            $params[] = "%$search%";
            $params[] = "%$search%";
            $params[] = "%$search%";
        }

        if ($status) {
            $where .= " AND status_warga = ?";
            $params[] = $status;
        }

        // Count total
        $stmt = $this->db->prepare("SELECT COUNT(*) as total FROM warga $where");
        $stmt->execute($params);
        $total = $stmt->fetch()['total'];

        // Get warga
        $stmt = $this->db->prepare("SELECT * FROM warga $where ORDER BY created_at DESC LIMIT $perPage OFFSET $offset");
        $stmt->execute($params);
        $warga = $stmt->fetchAll();

        $totalPages = ceil($total / $perPage);

        $pageTitle = 'Data Warga';
        $breadcrumb = '<ol class="breadcrumb float-sm-right">
            <li class="breadcrumb-item"><a href="' . nginx_url('dashboard') . '">Home</a></li>
            <li class="breadcrumb-item active">Data Warga</li>
        </ol>';

        ob_start();
        include VIEWSPATH . 'warga/index.php';
        $content = ob_get_clean();

        include LAYOUTS . 'main_layout.php';
    }

    /**
     * Tambah Warga
     */
    public function tambah() {
        $pageTitle = 'Tambah Warga';
        $breadcrumb = '<ol class="breadcrumb float-sm-right">
            <li class="breadcrumb-item"><a href="' . nginx_url('dashboard') . '">Home</a></li>
            <li class="breadcrumb-item"><a href="' . nginx_url('warga') . '">Data Warga</a></li>
            <li class="breadcrumb-item active">Tambah</li>
        </ol>';

        ob_start();
        include VIEWSPATH . 'warga/tambah.php';
        $content = ob_get_clean();

        include LAYOUTS . 'main_layout.php';
    }

    /**
     * Process Tambah Warga
     */
    public function proses_tambah() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = [
                'nik' => sanitize($_POST['nik'] ?? ''),
                'no_kk' => sanitize($_POST['no_kk'] ?? ''),
                'nama_lengkap' => sanitize($_POST['nama_lengkap'] ?? ''),
                'tempat_lahir' => sanitize($_POST['tempat_lahir'] ?? ''),
                'tanggal_lahir' => sanitize($_POST['tanggal_lahir'] ?? ''),
                'jenis_kelamin' => sanitize($_POST['jenis_kelamin'] ?? ''),
                'agama' => sanitize($_POST['agama'] ?? ''),
                'status_perkawinan' => sanitize($_POST['status_perkawinan'] ?? 'Belum Kawin'),
                'pekerjaan' => sanitize($_POST['pekerjaan'] ?? ''),
                'no_hp' => sanitize($_POST['no_hp'] ?? ''),
                'email' => sanitize($_POST['email'] ?? ''),
                'alamat' => sanitize($_POST['alamat'] ?? ''),
                'rt' => sanitize($_POST['rt'] ?? '01'),
                'rw' => sanitize($_POST['rw'] ?? '01'),
                'status_warga' => sanitize($_POST['status_warga'] ?? 'Aktif')
            ];

            // Check NIK exists
            $stmt = $this->db->prepare("SELECT id FROM warga WHERE nik = ?");
            $stmt->execute([$data['nik']]);
            if ($stmt->fetch()) {
                setFlash('error', 'NIK sudah terdaftar!');
                redirect('/warga/tambah');
            }

            // Handle foto upload
            $fotoName = 'default.png';
            if (!empty($_FILES['foto']['name'])) {
                $upload = uploadFile($_FILES['foto'], UPLOAD_WARGA, ['jpg', 'jpeg', 'png']);
                if ($upload['success']) {
                    $fotoName = $upload['filename'];
                }
            }

            $data['foto'] = $fotoName;

            // Insert
            $stmt = $this->db->prepare("
                INSERT INTO warga (nik, no_kk, nama_lengkap, tempat_lahir, tanggal_lahir, jenis_kelamin, agama, status_perkawinan, pekerjaan, no_hp, email, alamat, rt, rw, foto, status_warga)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
            ");
            $stmt->execute([
                $data['nik'], $data['no_kk'], $data['nama_lengkap'], $data['tempat_lahir'], $data['tanggal_lahir'],
                $data['jenis_kelamin'], $data['agama'], $data['status_perkawinan'], $data['pekerjaan'],
                $data['no_hp'], $data['email'], $data['alamat'], $data['rt'], $data['rw'], $data['foto'], $data['status_warga']
            ]);

            logActivity('Menambah data warga: ' . $data['nama_lengkap'], 'Warga');
            setFlash('success', 'Data warga berhasil ditambahkan!');
            redirect('/warga');
        }

        redirect('/warga/tambah');
    }

    /**
     * Edit Warga
     */
    public function edit() {
        $id = (int)($_GET['id'] ?? 0);

        $stmt = $this->db->prepare("SELECT * FROM warga WHERE id = ?");
        $stmt->execute([$id]);
        $warga = $stmt->fetch();

        if (!$warga) {
            setFlash('error', 'Data warga tidak ditemukan!');
            redirect('/warga');
        }

        $pageTitle = 'Edit Warga';
        $breadcrumb = '<ol class="breadcrumb float-sm-right">
            <li class="breadcrumb-item"><a href="' . nginx_url('dashboard') . '">Home</a></li>
            <li class="breadcrumb-item"><a href="' . nginx_url('warga') . '">Data Warga</a></li>
            <li class="breadcrumb-item active">Edit</li>
        </ol>';

        ob_start();
        include VIEWSPATH . 'warga/edit.php';
        $content = ob_get_clean();

        include LAYOUTS . 'main_layout.php';
    }

    /**
     * Process Edit Warga
     */
    public function proses_edit() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = (int)($_POST['id'] ?? 0);

            $data = [
                'nik' => sanitize($_POST['nik'] ?? ''),
                'no_kk' => sanitize($_POST['no_kk'] ?? ''),
                'nama_lengkap' => sanitize($_POST['nama_lengkap'] ?? ''),
                'tempat_lahir' => sanitize($_POST['tempat_lahir'] ?? ''),
                'tanggal_lahir' => sanitize($_POST['tanggal_lahir'] ?? ''),
                'jenis_kelamin' => sanitize($_POST['jenis_kelamin'] ?? ''),
                'agama' => sanitize($_POST['agama'] ?? ''),
                'status_perkawinan' => sanitize($_POST['status_perkawinan'] ?? ''),
                'pekerjaan' => sanitize($_POST['pekerjaan'] ?? ''),
                'no_hp' => sanitize($_POST['no_hp'] ?? ''),
                'email' => sanitize($_POST['email'] ?? ''),
                'alamat' => sanitize($_POST['alamat'] ?? ''),
                'rt' => sanitize($_POST['rt'] ?? '01'),
                'rw' => sanitize($_POST['rw'] ?? '01'),
                'status_warga' => sanitize($_POST['status_warga'] ?? 'Aktif')
            ];

            // Check NIK exists (exclude current)
            $stmt = $this->db->prepare("SELECT id FROM warga WHERE nik = ? AND id != ?");
            $stmt->execute([$data['nik'], $id]);
            if ($stmt->fetch()) {
                setFlash('error', 'NIK sudah terdaftar!');
                redirect('/warga/edit?id=' . $id);
            }

            // Handle foto upload
            $fotoName = $_POST['foto_lama'] ?? 'default.png';
            if (!empty($_FILES['foto']['name'])) {
                $upload = uploadFile($_FILES['foto'], UPLOAD_WARGA, ['jpg', 'jpeg', 'png']);
                if ($upload['success']) {
                    // Delete old foto
                    if ($fotoName !== 'default.png') {
                        deleteFile(UPLOAD_WARGA . $fotoName);
                    }
                    $fotoName = $upload['filename'];
                }
            }

            $data['foto'] = $fotoName;
            $data['id'] = $id;

            // Update
            $stmt = $this->db->prepare("
                UPDATE warga SET
                    nik = ?, no_kk = ?, nama_lengkap = ?, tempat_lahir = ?, tanggal_lahir = ?,
                    jenis_kelamin = ?, agama = ?, status_perkawinan = ?, pekerjaan = ?,
                    no_hp = ?, email = ?, alamat = ?, rt = ?, rw = ?, foto = ?, status_warga = ?
                WHERE id = ?
            ");
            $stmt->execute([
                $data['nik'], $data['no_kk'], $data['nama_lengkap'], $data['tempat_lahir'], $data['tanggal_lahir'],
                $data['jenis_kelamin'], $data['agama'], $data['status_perkawinan'], $data['pekerjaan'],
                $data['no_hp'], $data['email'], $data['alamat'], $data['rt'], $data['rw'], $data['foto'], $data['status_warga'],
                $data['id']
            ]);

            logActivity('Mengubah data warga: ' . $data['nama_lengkap'], 'Warga');
            setFlash('success', 'Data warga berhasil diupdate!');
            redirect('/warga');
        }

        redirect('/warga');
    }

    /**
     * Hapus Warga
     */
    public function hapus() {
        $id = (int)($_GET['id'] ?? 0);

        $stmt = $this->db->prepare("SELECT * FROM warga WHERE id = ?");
        $stmt->execute([$id]);
        $warga = $stmt->fetch();

        if ($warga) {
            // Delete foto
            if ($warga['foto'] !== 'default.png') {
                deleteFile(UPLOAD_WARGA . $warga['foto']);
            }

            $stmt = $this->db->prepare("DELETE FROM warga WHERE id = ?");
            $stmt->execute([$id]);

            logActivity('Menghapus data warga: ' . $warga['nama_lengkap'], 'Warga');
            setFlash('success', 'Data warga berhasil dihapus!');
        } else {
            setFlash('error', 'Data warga tidak ditemukan!');
        }

        redirect('/warga');
    }

    /**
     * Detail Warga
     */
    public function detail() {
        $id = (int)($_GET['id'] ?? 0);

        $stmt = $this->db->prepare("SELECT * FROM warga WHERE id = ?");
        $stmt->execute([$id]);
        $warga = $stmt->fetch();

        if (!$warga) {
            setFlash('error', 'Data warga tidak ditemukan!');
            redirect('/warga');
        }

        // Get tagihan
        $stmt = $this->db->prepare("
            SELECT t.*, j.nama_iuran, j.jenis
            FROM tagihan_iuran t
            INNER JOIN jenis_iuran j ON t.id_jenis_iuran = j.id
            WHERE t.id_warga = ?
            ORDER BY t.bulan DESC
            LIMIT 12
        ");
        $stmt->execute([$id]);
        $tagihan = $stmt->fetchAll();

        // Get pengaduan
        $stmt = $this->db->prepare("SELECT * FROM pengaduan WHERE id_warga = ? ORDER BY created_at DESC LIMIT 5");
        $stmt->execute([$id]);
        $pengaduan = $stmt->fetchAll();

        $pageTitle = 'Detail Warga';
        $breadcrumb = '<ol class="breadcrumb float-sm-right">
            <li class="breadcrumb-item"><a href="' . nginx_url('dashboard') . '">Home</a></li>
            <li class="breadcrumb-item"><a href="' . nginx_url('warga') . '">Data Warga</a></li>
            <li class="breadcrumb-item active">Detail</li>
        </ol>';

        ob_start();
        include VIEWSPATH . 'warga/detail.php';
        $content = ob_get_clean();

        include LAYOUTS . 'main_layout.php';
    }

    /**
     * Export PDF
     */
    public function export_pdf() {
        requireRole(['admin', 'ketua_rt']);

        $id = (int)($_GET['id'] ?? 0);

        $stmt = $this->db->prepare("SELECT * FROM warga WHERE id = ?");
        $stmt->execute([$id]);
        $warga = $stmt->fetch();

        if (!$warga) {
            setFlash('error', 'Data warga tidak ditemukan!');
            redirect('/warga');
        }

        // Generate QR Code
        $qrData = 'SI-WargaRT|NIK:' . $warga['nik'] . '|Nama:' . $warga['nama_lengkap'];

        ob_start();
        ?>
        <html>
        <head>
            <title>Kartu Identitas Warga</title>
            <style>
                body { font-family: Arial, sans-serif; }
                .card { width: 350px; border: 2px solid #333; padding: 20px; margin: 20px auto; }
                .header { text-align: center; border-bottom: 2px solid #333; padding-bottom: 10px; margin-bottom: 20px; }
                .photo { width: 120px; height: 150px; border: 1px solid #ccc; float: right; margin-left: 15px; background: #f0f0f0; }
                .field { margin-bottom: 8px; }
                .label { font-weight: bold; font-size: 12px; }
                .value { font-size: 14px; }
            </style>
        </head>
        <body>
            <div class="card">
                <div class="header">
                    <h3>SI-WargaRT</h3>
                    <p>Kartu Identitas Warga</p>
                </div>
                <img src="<?= BASE_URL ?>uploads/warga/<?= $warga['foto'] ?>" class="photo" alt="Foto">
                <div class="field">
                    <div class="label">NIK</div>
                    <div class="value"><?= $warga['nik'] ?></div>
                </div>
                <div class="field">
                    <div class="label">Nama Lengkap</div>
                    <div class="value"><?= $warga['nama_lengkap'] ?></div>
                </div>
                <div class="field">
                    <div class="label">Tempat/Tgl Lahir</div>
                    <div class="value"><?= $warga['tempat_lahir'] ?>, <?= formatTanggal($warga['tanggal_lahir']) ?></div>
                </div>
                <div class="field">
                    <div class="label">Jenis Kelamin</div>
                    <div class="value"><?= $warga['jenis_kelamin'] == 'L' ? 'Laki-laki' : 'Perempuan' ?></div>
                </div>
                <div class="field">
                    <div class="label">Alamat</div>
                    <div class="value"><?= $warga['alamat'] ?></div>
                </div>
                <div class="field">
                    <div class="label">RT/RW</div>
                    <div class="value"><?= $warga['rt'] ?>/<?= $warga['rw'] ?></div>
                </div>
                <div style="clear: both;"></div>
                <div id="qrcode" style="text-align: center; margin-top: 20px;"></div>
            </div>
            <script src="<?= BASE_URL ?>assets/vendor/qrcodejs/qrcode.min.js"></script>
            <script>
                new QRCode(document.getElementById("qrcode"), "<?= $qrData ?>");
            </script>
        </body>
        </html>
        <?php
        $html = ob_get_clean();
        echo $html;
    }

    /**
     * Import Excel
     */
    public function import() {
        $pageTitle = 'Import Data Warga';
        $breadcrumb = '<ol class="breadcrumb float-sm-right">
            <li class="breadcrumb-item"><a href="' . nginx_url('dashboard') . '">Home</a></li>
            <li class="breadcrumb-item"><a href="' . nginx_url('warga') . '">Data Warga</a></li>
            <li class="breadcrumb-item active">Import</li>
        </ol>';

        ob_start();
        include VIEWSPATH . 'warga/import.php';
        $content = ob_get_clean();

        include LAYOUTS . 'main_layout.php';
    }

    /**
     * Process Import
     */
    public function proses_import() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_FILES['file']['name'])) {
            $file = $_FILES['file'];
            $ext = pathinfo($file['name'], PATHINFO_EXTENSION);

            if ($ext !== 'csv') {
                setFlash('error', 'Format file harus CSV!');
                redirect('/warga/import');
            }

            $handle = fopen($file['tmp_name'], 'r');
            $row = 0;
            $success = 0;
            $failed = 0;

            while (($data = fgetcsv($handle, 1000, ",")) !== false) {
                $row++;
                if ($row == 1) continue; // Skip header

                if (count($data) < 12) continue;

                // Check NIK exists
                $stmt = $this->db->prepare("SELECT id FROM warga WHERE nik = ?");
                $stmt->execute([sanitize($data[0])]);
                if ($stmt->fetch()) {
                    $failed++;
                    continue;
                }

                $stmt = $this->db->prepare("
                    INSERT INTO warga (nik, no_kk, nama_lengkap, tempat_lahir, tanggal_lahir, jenis_kelamin, agama, status_perkawinan, pekerjaan, no_hp, email, alamat, rt, rw)
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
                ");
                try {
                    $stmt->execute([
                        sanitize($data[0]), sanitize($data[1]), sanitize($data[2]),
                        sanitize($data[3]), sanitize($data[4]), sanitize($data[5]),
                        sanitize($data[6]), sanitize($data[7]), sanitize($data[8]),
                        sanitize($data[9]), sanitize($data[10]), sanitize($data[11]),
                        sanitize($data[12] ?? '01'), sanitize($data[13] ?? '01')
                    ]);
                    $success++;
                } catch (Exception $e) {
                    $failed++;
                }
            }

            fclose($handle);

            logActivity('Import data warga: ' . $success . ' berhasil, ' . $failed . ' gagal', 'Warga');
            setFlash('success', "Import selesai! $success berhasil, $failed gagal.");
            redirect('/warga');
        }

        redirect('/warga/import');
    }

    /**
     * Export Excel
     */
    public function export() {
        requireRole(['admin', 'ketua_rt']);

        $stmt = $this->db->query("SELECT * FROM warga ORDER BY nama_lengkap");
        $warga = $stmt->fetchAll();

        // Set headers for CSV
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="data_warga_' . date('Ymd') . '.csv"');

        $output = fopen('php://output', 'w');

        // Header
        fputcsv($output, ['NIK', 'No KK', 'Nama Lengkap', 'Tempat Lahir', 'Tanggal Lahir', 'Jenis Kelamin', 'Agama', 'Status Kawin', 'Pekerjaan', 'No HP', 'Email', 'Alamat', 'RT', 'RW', 'Status']);

        // Data
        foreach ($warga as $w) {
            fputcsv($output, [
                $w['nik'], $w['no_kk'], $w['nama_lengkap'], $w['tempat_lahir'], $w['tanggal_lahir'],
                $w['jenis_kelamin'], $w['agama'], $w['status_perkawinan'], $w['pekerjaan'],
                $w['no_hp'], $w['email'], $w['alamat'], $w['rt'], $w['rw'], $w['status_warga']
            ]);
        }

        fclose($output);
        exit;
    }
}