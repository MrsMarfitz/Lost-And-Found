<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width,initial-scale=1"/>
  <title>Login - Lost & Found Campus</title>
  <link rel="stylesheet" href="assets/css/style.css">
</head>
<body class="bg-gradient">

  <main class="auth-wrap">
    <div class="auth-card">
      <div class="auth-left">
        <img src="assets/img/logo.png" alt="logo" class="brand">
        <h1>Sign In</h1>
        <p class="muted">Masuk menggunakan akun kamu</p>

        <form action="../backend/login_process.php" method="POST" class="form">
          <input name="user" type="text" placeholder="Username atau Email" required>
          <input name="password" type="password" placeholder="Password" required>

          <div class="row-between">
            <label class="checkbox"><input type="checkbox"> Remember me</label>
            <a class="link-small" href="#">Lupa password?</a>
          </div>

          <button class="btn-primary" type="submit">Sign In</button>
        </form>

        <p class="muted center">Belum punya akun? <a href="register.php">Daftar</a></p>
      </div>

      <aside class="auth-right">
        <div class="auth-hero">
          <h2>Hello, Friend!</h2>
          <p>Register with your personal details to use all features of Lost & Found Campus.</p>
          <a href="register.php" class="btn-outline">Sign Up</a>
        </div>
      </aside>
    </div>
  </main>

<script src="assets/js/app.js"></script>
</body>
</html>
