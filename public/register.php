<<<<<<< Updated upstream
=======
<?php

require 'C:\\Users\\raymon\\OneDrive\\Desktop\\JAVASCRIPT PMD\\Lost-And-Found\\config\\config.php';

$data = json_decode(file_get_contents("php://input"), TRUE);

$username = $data['username'];
$email = $data['email'];
$password = $data['password'];
$confirm_password = $data['confirm_password']; // Menerima Konfirmasi Password
$full_name = $data['full_name'];
$phone = $data['phone']; // Pastikan formulir Anda mengirimkan data 'phone' atau hapus ini jika tidak ada

// --- VALIDASI TAMBAHAN ---
if ($password !== $confirm_password) {
    echo json_encode(["status" => "error", "message" => "Password dan Konfirmasi Password tidak cocok."]);
    exit();
}
// --- AKHIR VALIDASI ---

$hashed_pass = password_hash($password, PASSWORD_BCRYPT);
$role = "user";
$token = bin2hex(random_bytes(32));
$expiry = date("Y-m-d H:i:s", strtotime("+24 hours")); // token berlaku 24 jam

$query = "INSERT INTO users (username, email, password_hash, full_name, phone, role, activation_token, activation_expiry)
          VALUES ('$username', '$email', '$hashed_pass', '$full_name', '$phone', '$role', '$token', '$expiry')";

if(mysqli_query($conn, $query)){
    $verify_link = "http://localhost/api/verify.php?token=$token";

    mail($email,
         "Aktivasi Akun Lost & Found",
         "Silahkan aktivasi akun dengan klik: $verify_link",
         "Content-type:text/html;charset=UTF-8"
    );

    echo json_encode(["status"=>"ok", "message"=>"Registrasi berhasil! Silahkan cek email aktivasi."]);
} else {
    echo json_encode(["status"=>"error", "message"=>"Username atau email sudah terdaftar"]);
}
>>>>>>> Stashed changes
