<?php

require '../config/config.php'; 
require '../config/db_connect.php';

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: ../public/login.php"); 
    exit();
}

$full_name = trim($_POST['nama'] ?? '');       
$username  = trim($_POST['username'] ?? '');    
$email     = trim($_POST['email'] ?? '');       
$phone     = trim($_POST['phone'] ?? '');       
$password  = $_POST['password'] ?? '';          
$confirm_password = $_POST['password_confirm'] ?? ''; 

// VALIDASI INPUT KOSONG
if (empty($username) || empty($email) || empty($password) || empty($full_name) || empty($phone)) {
    $pesan = urlencode("Semua kolom wajib diisi.");
    header("Location: ../public/register.php?status=failed&msg=$pesan");
    exit();
}

// VALIDASI PASSWORD
if ($password !== $confirm_password) {
    $pesan = urlencode("Password dan Konfirmasi Password tidak sama.");
    header("Location: ../public/register.php?status=failed&msg=$pesan");
    exit();
}

// CEK DUPLIKAT (Username/Email sudah ada belum?)
$cek_query = "SELECT user_id FROM users WHERE username = ? OR email = ?";
$stmt_cek = $conn->prepare($cek_query);
$stmt_cek->bind_param("ss", $username, $email);
$stmt_cek->execute();
$stmt_cek->store_result(); 

if ($stmt_cek->num_rows > 0) {
    $pesan = urlencode("Username atau Email sudah terdaftar! Gunakan yang lain.");
    header("Location: ../public/register.php?status=failed&msg=$pesan");
    exit(); 
}

// SIMPAN KE DATABASE
$hashed_pass = password_hash($password, PASSWORD_DEFAULT);
$role = "user"; 

$insert_query = "INSERT INTO users (username, email, password_hash, full_name, phone, role) VALUES (?, ?, ?, ?, ?, ?)";
$stmt = $conn->prepare($insert_query);

if ($stmt) {
    $stmt->bind_param("ssssss", $username, $email, $hashed_pass, $full_name, $phone, $role);
    
    if ($stmt->execute()) {
        header("Location: ../public/login.php?status=success");
        exit();
    } else {
        $pesan = urlencode("Gagal database: " . $stmt->error);
        header("Location: ../public/register.php?status=failed&msg=$pesan");
        exit();
    }
} else {
    $pesan = urlencode("Terjadi kesalahan sistem database.");
    header("Location: ../public/register.php?status=failed&msg=$pesan");
    exit();
}
?>