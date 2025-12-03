<?php
// File: backend/update_profile.php

session_start();
require '../config/db_connect.php';

// 1. Cek Login
if (!isset($_SESSION['user_id'])) {
    header("Location: ../public/login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// 2. Pastikan Request Method POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // Ambil data dari form (sesuai name di public/profile.php)
    $nama     = trim($_POST['nama']);
    $username = trim($_POST['username']);
    $email    = trim($_POST['email']);
    $pass_lama = $_POST['password_lama'];
    $pass_baru = $_POST['password_baru'];

    // Validasi Input Kosong
    if (empty($nama) || empty($username) || empty($email)) {
        echo "<script>alert('Nama, Username, dan Email tidak boleh kosong!'); window.history.back();</script>";
        exit();
    }

    // 3. Cek apakah Username/Email sudah dipakai orang lain (kecuali diri sendiri)
    $cek = $conn->prepare("SELECT user_id FROM USERS WHERE (username = ? OR email = ?) AND user_id != ?");
    $cek->bind_param("ssi", $username, $email, $user_id);
    $cek->execute();
    $cek->store_result();

    if ($cek->num_rows > 0) {
        echo "<script>alert('Username atau Email sudah digunakan pengguna lain!'); window.history.back();</script>";
        exit();
    }
    $cek->close();

    // 4. Logika Update Password
    if (!empty($pass_lama) && !empty($pass_baru)) {
        // Ambil password hash saat ini dari DB
        $stmt = $conn->prepare("SELECT password_hash FROM USERS WHERE user_id = ?");
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $res = $stmt->get_result();
        $row = $res->fetch_assoc();

        // Verifikasi password lama
        if (password_verify($pass_lama, $row['password_hash'])) {
            // Hash password baru
            $new_hash = password_hash($pass_baru, PASSWORD_DEFAULT);

            // Query Update dengan Password
            $update = $conn->prepare("UPDATE USERS SET full_name=?, username=?, email=?, password_hash=? WHERE user_id=?");
            $update->bind_param("ssssi", $nama, $username, $email, $new_hash, $user_id);
        } else {
            echo "<script>alert('Password lama salah!'); window.history.back();</script>";
            exit();
        }
    } else {
        // Query Update TANPA Password
        $update = $conn->prepare("UPDATE USERS SET full_name=?, username=?, email=? WHERE user_id=?");
        $update->bind_param("sssi", $nama, $username, $email, $user_id);
    }

    // 5. Eksekusi Update
    if ($update->execute()) {
        // Update Session Username jika berubah
        $_SESSION['username'] = $username;

        echo "<script>alert('Profil berhasil diperbarui!'); window.location='../public/profile.php';</script>";
    } else {
        echo "<script>alert('Gagal memperbarui profil: " . $conn->error . "'); window.history.back();</script>";
    }

    $update->close();
    $conn->close();
} else {
    header("Location: ../public/profile.php");
    exit();
}
