<?php
// Pastikan hanya POST request yang diizinkan
if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    // Jika diakses langsung, arahkan ke halaman login
    header("Location: ../public/login.php"); 
    exit();
}

// 1. Panggil file konfigurasi dan koneksi database
// PERBAIKAN PATH: Mundur satu level (..)
require '../config/config.php'; 

// 2. Ambil data dari FORM POST
$full_name = trim($_POST['nama'] ?? ''); 
$username = trim($_POST['username'] ?? '');
$email = trim($_POST['email'] ?? '');
$phone = trim($_POST['phone'] ?? ''); 
$password = $_POST['password'] ?? '';
$confirm_password = $_POST['password_confirm'] ?? '';


// --- VALIDASI DATA INPUT ---

if (empty($username) || empty($email) || empty($password) || empty($full_name)) {
    $error_msg = "Semua field wajib diisi.";
    // Redirect Gagal dihilangkan untuk debugging
    die("FAILURE (VALIDASI): " . $error_msg); 
}

if ($password !== $confirm_password) {
    $error_msg = "Password dan Konfirmasi Password tidak cocok.";
    // Redirect Gagal dihilangkan untuk debugging
    die("FAILURE (VALIDASI): " . $error_msg);
}

// --- PROSES DAN KEAMANAN DATA ---

$hashed_pass = password_hash($password, PASSWORD_DEFAULT);
$role = "user"; 
// Kolom aktivasi dihilangkan agar sesuai dengan struktur database Anda


// 3. PREPARED STATEMENT UNTUK INSERT
// Query disesuaikan agar cocok dengan database tanpa kolom token
$query = "INSERT INTO users (username, email, password_hash, full_name, phone, role)
          VALUES (?, ?, ?, ?, ?, ?)";

$stmt = $conn->prepare($query);

if (!$stmt) {
    // Fatal error saat prepare query
    error_log("MySQL Prepare Error: " . $conn->error);
    $error_msg = "Sistem error (Prep).";
    die("FAILURE (SISTEM): " . $error_msg);
}

$stmt->bind_param("ssssss", 
    $username, 
    $email, 
    $hashed_pass, 
    $full_name, 
    $phone, 
    $role
);

// 4. EKSEKUSI AMAN ($stmt->execute())
if ($stmt->execute()) {
    
    // Pendaftaran Berhasil
    // REDIRECT DIHENTIKAN, GANTI DENGAN PESAN MANUAL
    echo "<h1>✅ Pendaftaran BERHASIL!</h1>";
    echo "<p>Akun <b>" . htmlspecialchars($username) . "</b> berhasil dibuat di database.</p>";
    echo "<p>Silakan klik link di bawah ini untuk menuju halaman Login:</p>";
    echo "<p><a href='" . ROOT_URL . "public/login.php?status=register_success'>Masuk ke Aplikasi</a></p>";
    exit();

} else {
    // Pendaftaran Gagal (Database Error)
    
    if ($conn->errno == 1062) {
        $error_msg = "Username atau Email sudah terdaftar.";
    } else {
        $error_msg = "Terjadi kesalahan database: " . $stmt->error;
        error_log("MySQL Execute Error: " . $stmt->error);
    }
    
    die("<h1>❌ Pendaftaran GAGAL</h1><p>Error: " . $error_msg . "</p>");
}