<?php
session_start();

// kalau udah login, langsung ke dashboard
if (isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true) {
  header("Location: index.php");
  exit;
}

$error = "";

// akun admin sementara (buat tugas dulu)
// NANTI BISA DIGANTI DB kalau backend sudah siap
$ADMIN_USER = "admin";
$ADMIN_PASS = "admin123";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
  $u = $_POST["username"] ?? "";
  $p = $_POST["password"] ?? "";

  if ($u === $ADMIN_USER && $p === $ADMIN_PASS) {
    $_SESSION['admin_logged_in'] = true;
    $_SESSION['admin_username'] = $u;
    header("Location: index.php");
    exit;
  } else {
    $error = "Username atau password salah!";
  }
}
?>
<!DOCTYPE html>
<html>
<head>
  <meta charset="UTF-8">
  <title>Admin Login</title>
  <style>
    :root{
      --bg1:#f4f2ff; --bg2:#ece9ff; --card:#fff; --border:#e8e6ff;
      --text:#171a2b; --muted:#6b7280; --primary:#6c5ce7; --primary2:#8b7bff;
      --ring: rgba(108,92,231,0.25);
    }
    *{box-sizing:border-box;font-family:Inter,system-ui,Segoe UI,Arial,sans-serif;}
    body{
      margin:0; min-height:100vh; display:grid; place-items:center;
      background:
        radial-gradient(900px 700px at 10% -10%, #c7bfff55, transparent),
        radial-gradient(900px 700px at 90% 0%, #b7d2ff55, transparent),
        linear-gradient(180deg, var(--bg1), var(--bg2));
      padding:20px;
    }
    .card{
      width:min(420px,100%);
      background:var(--card);
      border:1px solid var(--border);
      border-radius:18px;
      padding:22px;
      box-shadow:0 10px 30px rgba(40,30,120,0.08);
    }
    h2{margin:0 0 6px;}
    p{margin:0 0 16px;color:var(--muted);font-size:14px;}
    .field{display:grid;gap:6px;margin-bottom:12px;}
    label{font-size:12px;color:var(--muted);font-weight:700;}
    input{
      padding:12px;border-radius:12px;border:1px solid var(--border);
      outline:none;font-size:14px;
    }
    input:focus{
      border-color:var(--primary);
      box-shadow:0 0 0 4px var(--ring);
    }
    .btn{
      width:100%;padding:12px;border:none;border-radius:12px;cursor:pointer;
      font-weight:800;color:white;font-size:14px;
      background:linear-gradient(135deg,var(--primary),var(--primary2));
      box-shadow:0 8px 18px var(--ring);
    }
    .err{
      background:#fee2e2;color:#991b1b;border:1px solid #fecaca;
      padding:10px;border-radius:10px;font-size:13px;margin-bottom:10px;
    }
  </style>
</head>
<body>
  <div class="card">
    <h2>Admin Login</h2>
    <p>Masuk sebagai admin untuk mengelola laporan.</p>

    <?php if($error): ?>
      <div class="err"><?= $error ?></div>
    <?php endif; ?>

    <form method="POST">
      <div class="field">
        <label>Username</label>
        <input type="text" name="username" placeholder="Username" required>
      </div>
      <div class="field">
        <label>Password</label>
        <input type="password" name="password" placeholder="Password" required>
      </div>
      <button class="btn" type="submit">Sign In</button>
    </form>
  </div>
</body>
</html>
