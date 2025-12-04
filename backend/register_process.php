<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// 1. Load Library PHPMailer (Sesuai nama folder kamu 'PHPMailer-master')
require 'PHPMailer-master/src/Exception.php';
require 'PHPMailer-master/src/PHPMailer.php';
require 'PHPMailer-master/src/SMTP.php';

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


if (empty($username) || empty($email) || empty($password) || empty($full_name) || empty($phone)) {
    $pesan = urlencode("Semua kolom wajib diisi.");
    header("Location: ../public/register.php?status=failed&msg=$pesan");
    exit();
}

if (!preg_match("/^[0-9]+$/", $phone)) {
    $pesan = urlencode("Nomor telepon hanya boleh berisi angka.");
    header("Location: ../public/register.php?status=failed&msg=$pesan");
    exit();
}

if (strlen($phone) < 10 || strlen($phone) > 13) {
    $pesan = urlencode("Nomor telepon tidak valid (harus 10-13 digit).");
    header("Location: ../public/register.php?status=failed&msg=$pesan");
    exit();
}

if ($password !== $confirm_password) {
    $pesan = urlencode("Password dan Konfirmasi Password tidak sama.");
    header("Location: ../public/register.php?status=failed&msg=$pesan");
    exit();
}

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

$hashed_pass = password_hash($password, PASSWORD_DEFAULT);
$role = "user"; 
$token = bin2hex(random_bytes(32)); 
$is_verified = 0; 

$insert_query = "INSERT INTO users (username, email, password_hash, full_name, phone, role, verification_token, is_verified) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
$stmt = $conn->prepare($insert_query);

if ($stmt) {
    $stmt->bind_param("sssssssi", $username, $email, $hashed_pass, $full_name, $phone, $role, $token, $is_verified);
    
    if ($stmt->execute()) {
        
        $mail = new PHPMailer(true);

        try {
            $mail->isSMTP();
            $mail->Host       = 'smtp.gmail.com';
            $mail->SMTPAuth   = true;
            $mail->Username   = 'isi email disini google asli';
            $mail->Password   = 'isi token disini'; // akun google asli dan token asli!
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port       = 587;

            $mail->setFrom('no-reply@lostfound.com', 'Admin Lost & Found');
            $mail->addAddress($email, $full_name);

            $mail->isHTML(true);
            $mail->Subject = 'Verifikasi Akun Lost & Found';
            
            $base_url = "http://localhost:8081/Lost-And-Found/public/verify.php"; 
            $link = $base_url . "?email=" . urlencode($email) . "&token=" . $token;

            $mail->Body = "
                <h3>Halo, $full_name!</h3>
                <p>Terima kasih telah mendaftar. Akun Anda hampir siap.</p>
                <p>Silakan klik tombol di bawah ini untuk mengaktifkan akun Anda:</p>
                <a href='$link' style='background-color:#3b82f6; color:white; padding:10px 20px; text-decoration:none; border-radius:5px;'>Verifikasi Akun Saya</a>
                <br><br>
                <p>Atau copy link ini: $link</p>
            ";

            $mail->send();
            
            $pesan = urlencode("Registrasi berhasil! Cek email Anda untuk verifikasi akun.");
            header("Location: ../public/login.php?status=success&msg=$pesan");
            exit();

        } catch (Exception $e) {
            
            $pesan = urlencode("Akun dibuat, tapi gagal kirim email. Error: {$mail->ErrorInfo}");
            header("Location: ../public/register.php?status=failed&msg=$pesan");
            exit();
        }

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