<?php
session_start();

// kosongkan semua data session
$_SESSION = [];

// kalau pakai cookie session, hapus juga cookienya
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

// benar‑benar tutup session
session_destroy();

// kembali ke halaman login
header("Location: login.php");
exit();
