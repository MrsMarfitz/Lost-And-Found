<?php
// Nyalakan error reporting sebentar untuk memastikan tidak ada syntax error
error_reporting(E_ALL);
ini_set('display_errors', 1);

require '../config/config.php'; 
require '../config/db_connect.php';

// Pastikan hanya POST request
if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: ../public/login.php"); 
    exit();
}

// 1. Ambil Data
$full_name = trim($_POST['nama'] ?? '');        
$username  = trim($_POST['username'] ?? '');    
$email     = trim($_POST['email'] ?? '');       
$phone     = trim($_POST['phone'] ?? '');       
$password  = $_POST['password'] ?? '';          
$confirm_password = $_POST['password_confirm'] ?? ''; 

// 2. Validasi Input Kosong
if (empty($username) || empty($email) || empty($password) || empty($full_name)) {
    $pesan = urlencode("Semua kolom wajib diisi.");
    header("Location: ../public/register.php?status=failed&msg=$pesan");
    exit();
}

// 3. Validasi Password
if ($password !== $confirm_password) {
    $pesan = urlencode("Password dan Konfirmasi Password tidak sama.");
    header("Location: ../public/register.php?status=failed&msg=$pesan");
    exit();
}

// --- BAGIAN PENTING: CEK APAKAH SUDAH ADA? ---
// Kita cek dulu database sebelum Insert, biar tidak crash/layar putih

$cek_query = "SELECT user_id FROM users WHERE username = ? OR email = ?";
$stmt_cek = $conn->prepare($cek_query);
$stmt_cek->bind_param("ss", $username, $email);
$stmt_cek->execute();
$stmt_cek->store_result(); // Simpan hasil pencarian

if ($stmt_cek->num_rows > 0) {
    // 🛑 DATA SUDAH ADA!
    // Langsung lempar balik ke register dengan pesan
    $pesan = urlencode("Username atau Email sudah terdaftar! Gunakan yang lain.");
    header("Location: ../public/register.php?status=failed&msg=$pesan");
    exit(); // Stop proses di sini
}

// 4. Jika Aman (Belum ada), Lanjut Simpan
$hashed_pass = password_hash($password, PASSWORD_DEFAULT);
$role = "user"; 

$insert_query = "INSERT INTO users (username, email, password_hash, full_name, phone, role) VALUES (?, ?, ?, ?, ?, ?)";
$stmt = $conn->prepare($insert_query);

if ($stmt) {
    $stmt->bind_param("ssssss", $username, $email, $hashed_pass, $full_name, $phone, $role);
    
    if ($stmt->execute()) {
        // ✅ SUKSES REGISTER
        header("Location: ../public/login.php?status=success");
        exit();
    } else {
        // Gagal saat execute (masalah teknis lain)
        $pesan = urlencode("Gagal menyimpan data: " . $stmt->error);
        header("Location: ../public/register.php?status=failed&msg=$pesan");
        exit();
    }
} else {
    // Gagal Prepare statement
    $pesan = urlencode("Terjadi kesalahan sistem database.");
    header("Location: ../public/register.php?status=failed&msg=$pesan");
    exit();
}
?>