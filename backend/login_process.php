<?php
if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: ../public/login.php"); 
    exit();
}

require '../../config/config.php'; 

$username_or_email = trim($_POST['username_email'] ?? ''); 
$password_input = $_POST['password'] ?? ''; 

// --- VALIDASI AWAL ---
if (empty($username_or_email) || empty($password_input)) {
    $error_msg = "Username/Email dan Password wajib diisi.";
    header("Location: ../public/login.php?status=login_failed&msg=" . urlencode($error_msg));
    exit();
}

// --- PREPARED STATEMENT UNTUK MENGAMBIL DATA PENGGUNA ---
$query = "SELECT user_id, username, password_hash, role, activation_token FROM users WHERE username = ? OR email = ?";

$stmt = $conn->prepare($query);

if (!$stmt) {
    error_log("MySQL Prepare Error: " . $conn->error);
    $error_msg = "Sistem error (Prep).";
    // Close koneksi sebelum exit
    $conn->close();
    header("Location: ../public/login.php?status=login_failed&msg=" . urlencode($error_msg));
    exit();
}

// Bind parameter: ss (dua string, karena mencari di username ATAU email)
$stmt->bind_param("ss", $username_or_email, $username_or_email);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 1) {
    $user = $result->fetch_assoc();
    
    // Verifikasi Password 
    if (password_verify($password_input, $user['password_hash'])) {
        
        // Pengecekan Kritis: Akun sudah diaktivasi?
        if (!empty($user['activation_token'])) {
            $error_msg = "Akun Anda belum diaktifkan. Silakan cek email Anda untuk tautan aktivasi.";
            $stmt->close();
            $conn->close();
            header("Location: ../public/login.php?status=login_failed&msg=" . urlencode($error_msg));
            exit();
        }
        
        // --- LOGIN BERHASIL: Buat Session ---
        $_SESSION['user_id'] = $user['user_id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['role'] = $user['role'];
        
        // Tutup statement dan koneksi sebelum redirect sukses
        $stmt->close();
        $conn->close();
        
        // Arahkan ke Halaman Dashboard/Index
        header("Location: ../public/index.php"); 
        exit();

    } else {

        $error_msg = "Username/Email atau Password salah.";

        $stmt->close();
        $conn->close();
        header("Location: ../public/login.php?status=login_failed&msg=" . urlencode($error_msg));
        exit();
    }
} else {

    $error_msg = "Username/Email atau Password salah.";

    $stmt->close();
    $conn->close();
    header("Location: ../public/login.php?status=login_failed&msg=" . urlencode($error_msg));
    exit();
}
