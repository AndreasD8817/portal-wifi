<?php
// Konfigurasi Database (Sesuaikan dengan Laragon Anda)
$host = 'localhost';      // Host database (biasanya localhost)
$db   = 'portal_dprd';    // Nama database yang Anda buat di HeidiSQL
$user = 'root';           // Username default Laragon
$pass = '';               // Password default Laragon (kosong)

try {
    // Membuat koneksi menggunakan PDO (PHP Data Objects) agar lebih aman
    $pdo = new PDO("mysql:host=$host;dbname=$db;charset=utf8", $user, $pass);
    
    // Mengaktifkan mode error agar jika ada kesalahan SQL, pesan error muncul
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Mengatur agar data yang diambil berbentuk Array Asosiatif (nama kolom)
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    
} catch (PDOException $e) {
    // Jika koneksi gagal, hentikan program dan tampilkan pesan error
    die("Koneksi Database Gagal: " . $e->getMessage());
}
?>