<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width,initial-scale=1"/>
  <title>Register - Lost & Found Campus</title>
  <link rel="stylesheet" href="assets/css/style.css">
</head>
<body class="bg-gradient">

  <main class="auth-wrap">
    <div class="auth-card">
      <div class="auth-left">
        <img src="assets/img/logo.png" alt="logo" class="brand">
        <h1>Create Account</h1>
        <p class="muted">Buat akun agar bisa melaporkan barang hilang/temuan</p>

        <form action="../backend/register_process.php" method="POST" class="form">
          <input name="nama" type="text" placeholder="Nama Lengkap" required>
          <input name="username" type="text" placeholder="Username" required>
          <input name="email" type="email" placeholder="Email" required>
          <input name="password" type="password" placeholder="Password" required>
          <input name="password_confirm" type="password" placeholder="Konfirmasi Password" required>

          <button class="btn-primary" type="submit">Create Account</button>
        </form>

        <p class="muted center">Sudah punya akun? <a href="login.php">Login</a></p>
      </div>

      <aside class="auth-right">
        <div class="auth-hero">
          <h2>Welcome!</h2>
          <p>Let’s help the campus community — report lost & found items quickly.</p>
          <a href="login.php" class="btn-outline">Sign In</a>
        </div>
      </aside>
    </div>
  </main>

<script src="assets/js/app.js"></script>
</body>
</html>
=======
<?php

require 'C:\\Users\\raymon\\OneDrive\\Desktop\\JAVASCRIPT PMD\\Lost-And-Found\\config\\config.php';

$data = json_decode(file_get_contents("php://input"), TRUE);

$username = $data['username'];
$email = $data['email'];
$password = $data['password'];
$confirm_password = $data['confirm_password'];
$full_name = $data['full_name'];
$phone = $data['phone']; 

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


>>>>>>> 0cdd4e13549f85fc66c5d7a3185ce608f62f738b
