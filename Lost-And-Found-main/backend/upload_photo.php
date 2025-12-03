<?php
// File: backend/upload_photo.php

session_start();
require '../config/db_connect.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: ../public/login.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_FILES["photo"])) {

    $user_id = $_SESSION['user_id'];
    $target_dir = "../public/uploads/";

    // Pastikan folder uploads ada
    if (!is_dir($target_dir)) {
        mkdir($target_dir, 0777, true);
    }

    $file_name = basename($_FILES["photo"]["name"]);
    $imageFileType = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));

    // Validasi apakah file adalah gambar
    $check = getimagesize($_FILES["photo"]["tmp_name"]);
    if ($check === false) {
        echo "<script>alert('File bukan gambar!'); window.history.back();</script>";
        exit();
    }

    // Validasi Ukuran (Max 2MB)
    if ($_FILES["photo"]["size"] > 2000000) {
        echo "<script>alert('Ukuran file terlalu besar (Max 2MB).'); window.history.back();</script>";
        exit();
    }

    // Validasi Format
    if ($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg") {
        echo "<script>alert('Hanya format JPG, JPEG, & PNG yang diperbolehkan.'); window.history.back();</script>";
        exit();
    }

    // Generate nama file unik agar tidak bentrok
    $new_file_name = "profile_" . $user_id . "_" . time() . "." . $imageFileType;
    $target_file = $target_dir . $new_file_name;

    // Ambil foto lama untuk dihapus (Opsional, agar server tidak penuh)
    $q_old = $conn->query("SELECT photo_profile FROM USERS WHERE user_id = $user_id");
    $d_old = $q_old->fetch_assoc();
    $old_photo = $d_old['photo_profile'];

    // Upload File Baru
    if (move_uploaded_file($_FILES["photo"]["tmp_name"], $target_file)) {

        // Update Database
        $stmt = $conn->prepare("UPDATE USERS SET photo_profile = ? WHERE user_id = ?");
        $stmt->bind_param("si", $new_file_name, $user_id);

        if ($stmt->execute()) {
            // Hapus file lama jika ada dan bukan default
            if ($old_photo && file_exists($target_dir . $old_photo) && $old_photo != 'user.jpg') {
                unlink($target_dir . $old_photo);
            }
            echo "<script>alert('Foto profil berhasil diubah!'); window.location='../public/profile.php';</script>";
        } else {
            echo "<script>alert('Gagal update database.'); window.history.back();</script>";
        }
    } else {
        echo "<script>alert('Terjadi kesalahan saat mengunggah file.'); window.history.back();</script>";
    }
} else {
    header("Location: ../public/profile.php");
}
