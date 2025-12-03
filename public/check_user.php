<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();

require '../config/config.php';
require '../config/db_connect.php';

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: login.php");
    exit();
}

// 3. Ambil Data dari Form Login
$username_input = trim($_POST['username'] ?? ''); // Bisa username atau email
$password_input = $_POST['password'] ?? '';

// Cek kosong
if (empty($username_input) || empty($password_input)) {
    header("Location: login.php?status=failed&msg=" . urlencode("Username dan Password wajib diisi."));
    exit();
}

// 4. Cek User di Database (Mencari berdasarkan username ATAU email)
$query = "SELECT user_id, username, password_hash, full_name, role FROM users WHERE username = ? OR email = ?";
$stmt = $conn->prepare($query);

if (!$stmt) {
    die("Error Database Prepare: " . $conn->error);
}

$stmt->bind_param("ss", $username_input, $username_input);
$stmt->execute();
$result = $stmt->get_result();

// 5. Verifikasi Data
if ($row = $result->fetch_assoc()) {
    // User ditemukan, sekarang cek passwordnya
    // password_verify(password_ketikan_user, password_acak_di_db)
    
    if (password_verify($password_input, $row['password_hash'])) {
        // --- LOGIN SUKSES! ---
        
        // Simpan data penting ke SESSION biar server ingat siapa yang login
        $_SESSION['user_id']   = $row['user_id'];
        $_SESSION['username']  = $row['username'];
        $_SESSION['full_name'] = $row['full_name'];
        $_SESSION['role']      = $row['role'];
        $_SESSION['is_login']  = true;

        // Redirect ke Halaman Utama (Dashboard/Index)
        // Ganti 'index.php' sesuai halaman tujuanmu
        header("Location: ../index.php"); 
        exit();

    } else {
        // Password Salah
        header("Location: login.php?status=failed&msg=" . urlencode("Password salah!"));
        exit();
    }
} else {
    // Username/Email tidak ditemukan
    header("Location: login.php?status=failed&msg=" . urlencode("Akun tidak ditemukan."));
    exit();
}
?>