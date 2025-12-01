<?php
// Pastikan hanya POST request yang diizinkan
if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    // Jika diakses langsung, tampilkan pesan status
    die("Akses langsung tidak diizinkan."); 
}

// 1. Panggil file konfigurasi dan koneksi database
// ROOT_URL sudah didefinisikan di config.php
require '../../config/config.php'; 

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
    // Redirection dihilangkan sementara untuk debugging
    die("FAILURE (VALIDASI): " . $error_msg); 
}

if ($password !== $confirm_password) {
    $error_msg = "Password dan Konfirmasi Password tidak cocok.";
    // Redirection dihilangkan sementara untuk debugging
    die("FAILURE (VALIDASI): " . $error_msg);
}

// --- PROSES DAN KEAMANAN DATA ---

$hashed_pass = password_hash($password, PASSWORD_DEFAULT);
$role = "user"; 
$token = bin2hex(random_bytes(32)); 
$expiry = date("Y-m-d H:i:s", strtotime("+24 hours"));


// 3. PREPARED STATEMENT UNTUK INSERT
$query = "INSERT INTO users (username, email, password_hash, full_name, phone, role, activation_token, activation_expiry)
          VALUES (?, ?, ?, ?, ?, ?, ?, ?)";

$stmt = $conn->prepare($query);

if (!$stmt) {
    // Fatal error saat prepare query
    error_log("MySQL Prepare Error: " . $conn->error);
    die("FAILURE (SISTEM): Kesalahan preparasi query database.");
}

$stmt->bind_param("ssssssss", 
    $username, 
    $email, 
    $hashed_pass, 
    $full_name, 
    $phone, 
    $role, 
    $token, 
    $expiry
);

// 4. EKSEKUSI AMAN ($stmt->execute())
if ($stmt->execute()) {
    
    // Pendaftaran Berhasil
    // Output success secara plaintext
    echo "SUCCESS: Akun berhasil terdaftar di database!";
    
} else {
    // Pendaftaran Gagal (Database Error)
    
    if ($conn->errno == 1062) {
        $error_msg = "Username atau Email sudah terdaftar.";
    } else {
        $error_msg = "Terjadi kesalahan database.";
        error_log("MySQL Execute Error: " . $stmt->error);
    }
    
    // Output failure secara plaintext
    die("FAILURE (DATABASE): " . $error_msg);
}

$stmt->close();
$conn->close();
?>