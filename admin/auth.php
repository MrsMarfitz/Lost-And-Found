<?php
session_start();

// Cek apakah sudah login
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Cek apakah role adalah admin
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    // Kalau bukan admin, lempar ke dashboard user biasa
    header("Location: ../public/dashboard.php");
    exit();
}
