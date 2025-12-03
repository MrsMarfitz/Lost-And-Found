<?php
// Pastikan hanya POST request yang diizinkan
if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: ../public/login.php"); 
    exit();
}

// 1. Panggil file konfigurasi dan koneksi database
// PERBAIKAN PATH KRITIS: Hanya mundur satu level (..)
require '../config/config.php'; 

// 2. Ambil data dari FORM POST
$username_or_email = trim($_POST['username_email'] ?? ''); 
$password_input = $_POST['password'] ?? ''; 


// --- VALIDASI AWAL ---
if (empty($username_or_email) || empty($password_input)) {
    $error_msg = "Username/Email dan Password wajib diisi.";
    // Gunakan ROOT_URL untuk redirect yang aman
    header("Location: " . ROOT_URL . "public/login.php?status=login_failed&msg=" . urlencode($error_msg));
    exit();
}

// --- PREPARED STATEMENT UNTUK MENGAMBIL DATA PENGGUNA ---
// REVISI: Menghapus 'activation_token' dari query SELECT agar tidak error database
$query = "SELECT user_id, username, password_hash, role FROM users WHERE username = ? OR email = ?";

$stmt = $conn->prepare($query);

if (!$stmt) {
    error_log("MySQL Prepare Error: " . $conn->error);
    $error_msg = "Sistem error (Prep).";
    $conn->close();
    header("Location: " . ROOT_URL . "public/login.php?status=login_failed&msg=" . urlencode($error_msg));
    exit();
}

// Bind parameter: ss (dua string)
$stmt->bind_param("ss", $username_or_email, $username_or_email);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 1) {
    $user = $result->fetch_assoc();
    
    // 3. Verifikasi Password
    if (password_verify($password_input, $user['password_hash'])) {
        
        // --- LOGIN BERHASIL: Buat Session ---
        $_SESSION['user_id'] = $user['user_id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['role'] = $user['role'];
        
        $stmt->close();
        $conn->close();
        
        // 4. Arahkan ke Dashboard (Pakai JS biar aman dari error port)
        echo "<script>
            alert('Login Berhasil! Selamat Datang, " . htmlspecialchars($user['username']) . "');
            window.location.href = '../public/index.php';
        </script>";
        exit();

    } else {
        // Password tidak cocok
        $error_msg = "Username/Email atau Password salah.";
        $stmt->close();
        $conn->close();
        echo "<script>
            alert('Username atau Password Salah!');
            window.history.back();
        </script>";
        exit();
    }
} else {
    // Pengguna tidak ditemukan
    $error_msg = "Username/Email atau Password salah.";
    $stmt->close();
    $conn->close();
    echo "<script>
        alert('Akun tidak ditemukan!');
        window.history.back();
    </script>";
    exit();
    
}