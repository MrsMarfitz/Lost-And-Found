<?php
session_start(); // <--- WAJIB ADA DI BARIS PERTAMA!

require '../config/config.php'; 
require '../config/db_connect.php';

// 1. Pastikan user masuk lewat jalur POST
if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: ../public/login.php"); 
    exit();
}

// 2. Ambil data (Pastikan 'name' di HTML nanti adalah 'username_email')
$username_or_email = trim($_POST['username_email'] ?? ''); 
$password_input = $_POST['password'] ?? ''; 

// 3. Cari user
$query = "SELECT user_id, username, password_hash, role, full_name FROM users WHERE username = ? OR email = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("ss", $username_or_email, $username_or_email);
$stmt->execute();
$result = $stmt->get_result();

// 4. Proses Login
if ($result->num_rows === 1) {
    $user = $result->fetch_assoc();
    
    if (password_verify($password_input, $user['password_hash'])) {
        
        // -- SIMPAN SESI --
        $_SESSION['user_id'] = $user['user_id'];
        $_SESSION['role'] = $user['role'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['full_name'] = $user['full_name']; // Tambahan biar enak dipanggil
        $_SESSION['status'] = 'login';
        
        // -- REDIRECT --
        if ($user['role'] === 'admin') {
            header("Location: ../admin/index.php");
        } else {
            // Pastikan file dashboard.php ada di folder public
            header("Location: ../public/dashboard.php");
        }
        exit();

    } else {
        // Password Salah
        header("Location: ../public/login.php?status=login_failed&msg=Password%20Salah");
        exit();
    }
} else {
    // Akun tidak ada
    header("Location: ../public/login.php?status=login_failed&msg=Akun%20tidak%20ditemukan");
    exit();
}
?>