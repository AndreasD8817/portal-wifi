<?php
// ==========================================
// 1. KONFIGURASI SESI & AUTO LOGOUT
// ==========================================

// Atur agar Cookie Sesi mati saat browser ditutup (Parameter 0)
// Wajib diletakkan SEBELUM session_start()
session_set_cookie_params(0);

// Mulai Sesi
session_start();

// --- LOGIKA TIMEOUT (Tidak Aktif) ---
// Atur batas waktu diam (contoh: 30 menit = 1800 detik)
$timeout_duration = 1800; 

// Cek apakah ada riwayat waktu aktivitas terakhir
if (isset($_SESSION['last_activity'])) {
    // Hitung berapa lama user diam (Waktu Sekarang - Waktu Terakhir Aktif)
    $secondsInactive = time() - $_SESSION['last_activity'];
    
    // Jika diamnya lebih lama dari batas waktu
    if ($secondsInactive > $timeout_duration) {
        // Hapus semua data sesi
        session_unset();
        session_destroy();
        
        // Mulai sesi baru sebentar khusus untuk kirim pesan notifikasi
        session_start();
        $_SESSION['flash'] = [
            'type' => 'warning', 
            'title' => 'Sesi Berakhir', 
            'text' => 'Anda telah logout otomatis karena tidak aktif selama 30 menit.'
        ];
        
        // Lempar kembali ke halaman login
        header("Location: /");
        exit;
    }
}

// Update waktu aktivitas terakhir ke DETIK INI setiap kali halaman dimuat
$_SESSION['last_activity'] = time();


// ==========================================
// 2. KONEKSI & FUNGSI BANTUAN
// ==========================================
require_once 'config/db.php';

// Ambil data user berdasarkan NIK
function getUser($pdo, $nik) {
    $stmt = $pdo->prepare("SELECT * FROM users WHERE nik = ?");
    $stmt->execute([$nik]);
    return $stmt->fetch();
}

// Set pesan notifikasi (Flash Message) untuk SweetAlert
function setFlash($type, $title, $text) {
    $_SESSION['flash'] = ['type' => $type, 'title' => $title, 'text' => $text];
}

// --- INISIALISASI VARIABEL URL ---
$page = $_GET['page'] ?? 'home';
$act  = $_GET['act'] ?? '';

// ==========================================
// 3. LOGIKA LOGIN & PENGECEKAN PERANGKAT
// ==========================================
if ($page === 'login' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $nik = trim($_POST['nik']); // Hapus spasi
    $user = getUser($pdo, $nik);
    
    if ($user) {
        // Login Berhasil -> Simpan Sesi
        $_SESSION['user_nik'] = $user['nik'];
        $_SESSION['last_activity'] = time(); // Set waktu awal login
        
        // --- CEK KELENGKAPAN PERANGKAT ---
        // Ambil nama perangkat milik user ini
        $stmt = $pdo->prepare("SELECT device_name FROM devices WHERE user_nik = ?");
        $stmt->execute([$user['nik']]);
        $devices = $stmt->fetchAll(PDO::FETCH_COLUMN); // Ambil kolom nama saja

        $dataBelumLengkap = false;

        // Cek 1: Belum punya perangkat sama sekali?
        if (count($devices) == 0) {
            $dataBelumLengkap = true;
        } else {
            // Cek 2: Punya perangkat, tapi namanya kosong/spasi doang?
            foreach ($devices as $devName) {
                if (trim($devName) == '') {
                    $dataBelumLengkap = true;
                    break;
                }
            }
        }

        if ($dataBelumLengkap) {
            // Jika kosong -> Peringatan Merah
            setFlash('warning', 'PERHATIAN PENTING!', 'Nama Perangkat belum diisi.<br><br><b>Harap segera lengkapi data perangkat</b> agar bisa terdaftar dan terkoneksi ke jaringan WiFi DPRD.');
        } else {
            // Jika lengkap -> Sukses Biru
            setFlash('success', 'Berhasil Masuk!', 'Selamat Datang di Portal Wifi DPRD Surabaya.');
        }
        
        header("Location: /");
    } else {
        // Login Gagal
        setFlash('error', 'Gagal', 'NIK Tidak Ditemukan');
        header("Location: /");
    }
    exit;
}

// ==========================================
// 4. LOGIKA LOGOUT
// ==========================================
if ($page === 'logout') {
    session_destroy();
    header("Location: /");
    exit;
}

// ==========================================
// 5. MIDDLEWARE (CEK STATUS LOGIN)
// ==========================================
// Jika belum login, stop disini dan tampilkan halaman login
if (!isset($_SESSION['user_nik'])) {
    require 'views/layout/header.php';
    require 'views/login.php'; 
    require 'views/layout/footer.php';
    
    // Tampilkan notifikasi login jika ada
    if (isset($_SESSION['flash'])) {
        $f = $_SESSION['flash'];
        echo "<script>setTimeout(() => { Swal.fire({ title: '{$f['title']}', html: '{$f['text']}', icon: '{$f['type']}', confirmButtonColor: '#38bdf8', background: '#1e293b', color: '#fff', allowOutsideClick: false }); }, 100);</script>";
        unset($_SESSION['flash']);
    }
    exit;
}

// ==========================================
// 6. PERSIAPAN DATA DASHBOARD
// ==========================================
$currentUser = getUser($pdo, $_SESSION['user_nik']);

// Ambil daftar perangkat user untuk ditampilkan di tabel
$stmtDev = $pdo->prepare("SELECT * FROM devices WHERE user_nik = ?");
$stmtDev->execute([$currentUser['nik']]);
$devices = $stmtDev->fetchAll();

// ==========================================
// 7. PROSES SIMPAN DATA (TAMBAH/EDIT)
// ==========================================
if ($page === 'save_device' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'] ?? '';
    $username = str_replace(' ', '', $_POST['username']); // Username tanpa spasi
    $password = $_POST['password'];
    $deviceName = $_POST['device_name'];
    
    // Validasi Password (Hanya jika user punya izin edit)
    if (!preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d).+$/', $password) && $currentUser['can_edit']) {
        setFlash('warning', 'Password Lemah', 'Wajib Huruf Besar, Kecil & Angka');
        header("Location: /"); exit;
    }

    if ($id) { 
        // --- MODE EDIT ---
        if ($currentUser['can_edit']) {
            // Boleh Edit: Update Password & Nama Perangkat
            $stmt = $pdo->prepare("UPDATE devices SET password=?, device_name=? WHERE id=? AND user_nik=?");
            $stmt->execute([$password, $deviceName, $id, $currentUser['nik']]);
        } else {
            // Dikunci Admin: HANYA update Nama Perangkat
            $stmt = $pdo->prepare("UPDATE devices SET device_name=? WHERE id=? AND user_nik=?");
            $stmt->execute([$deviceName, $id, $currentUser['nik']]);
        }
        setFlash('success', 'Berhasil', 'Data berhasil diperbarui');
    } else { 
        // --- MODE TAMBAH BARU ---
        // Cek Kuota dulu
        if (count($devices) < $currentUser['quota']) {
            $stmt = $pdo->prepare("INSERT INTO devices (user_nik, username, password, device_name) VALUES (?, ?, ?, ?)");
            $stmt->execute([$currentUser['nik'], $username, $password, $deviceName]);
            setFlash('success', 'Berhasil', 'Perangkat ditambahkan');
        } else {
            setFlash('error', 'Gagal', 'Kuota Perangkat Penuh');
        }
    }
    header("Location: /"); exit;
}

// ==========================================
// 8. PROSES UPLOAD PENGAJUAN PDF
// ==========================================
if ($page === 'upload_request' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_FILES['pdf_file']) && $_FILES['pdf_file']['error'] == 0) {
        // Nama file unik
        $fileName = $currentUser['nik'] . '_Pengajuan_' . date('Ymd_His') . '.pdf';
        $uploadDir = 'public/uploads/';
        
        // Buat folder jika belum ada
        if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);
        
        if(move_uploaded_file($_FILES['pdf_file']['tmp_name'], $uploadDir . $fileName)) {
            // Catat di database
            $stmt = $pdo->prepare("INSERT INTO requests (user_nik, file_path) VALUES (?, ?)");
            $stmt->execute([$currentUser['nik'], $fileName]);
            setFlash('success', 'Terkirim', 'Pengajuan akan diproses 2x24 Jam.');
        } else {
            setFlash('error', 'Gagal', 'Gagal mengupload file.');
        }
        header("Location: /"); exit;
    }
}

// ==========================================
// 9. LOGIKA KHUSUS ADMIN
// ==========================================
if ($currentUser['role'] === 'ADMIN') {
    // Kunci/Buka User Tertentu
    if ($act === 'toggle_lock') {
        $stmt = $pdo->prepare("UPDATE users SET can_edit = ? WHERE nik = ?");
        $stmt->execute([$_GET['status'], $_GET['nik']]);
        header("Location: /admin"); exit;
    }
    // Kunci/Buka SEMUA User (Global)
    if ($act === 'global_lock') {
        $stmt = $pdo->prepare("UPDATE users SET can_edit = ?");
        $stmt->execute([$_GET['status']]);
        setFlash('success', 'Sukses', 'Status Edit Semua User Diupdate');
        header("Location: /admin"); exit;
    }
    // Update Kuota User
    if ($act === 'update_quota') {
        $stmt = $pdo->prepare("UPDATE users SET quota = ? WHERE nik = ?");
        $stmt->execute([$_POST['quota'], $_POST['nik']]);
        setFlash('success', 'Sukses', 'Kuota Diupdate');
        header("Location: /admin"); exit;
    }
    // Selesaikan Pengajuan
    if ($act === 'request_done') {
        $stmt = $pdo->prepare("UPDATE requests SET status = 'Selesai' WHERE id = ?");
        $stmt->execute([$_GET['id']]);
        header("Location: /admin"); exit;
    }
}

// ==========================================
// 10. TAMPILKAN HALAMAN UTAMA
// ==========================================
require 'views/layout/header.php';

// Tampilkan Notifikasi (Popup) jika ada session flash
if (isset($_SESSION['flash'])) {
    $f = $_SESSION['flash'];
    // Gunakan setTimeout agar popup muncul mulus setelah halaman loading
    echo "<script>setTimeout(() => { Swal.fire({ title: '{$f['title']}', html: '{$f['text']}', icon: '{$f['type']}', confirmButtonColor: '#38bdf8', background: '#1e293b', color: '#fff', allowOutsideClick: false }); }, 300);</script>";
    unset($_SESSION['flash']);
}

// Pilih Tampilan: Admin atau Dashboard User Biasa
if ($page === 'admin' && $currentUser['role'] === 'ADMIN') {
    require 'views/admin.php';
} else {
    require 'views/dashboard.php';
}

require 'views/layout/footer.php';
?>