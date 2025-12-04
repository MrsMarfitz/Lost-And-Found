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

    // Ambil data text
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

    // 3. Cek Duplikat Username/Email
    $cek = $conn->prepare("SELECT user_id FROM users WHERE (username = ? OR email = ?) AND user_id != ?");
    $cek->bind_param("ssi", $username, $email, $user_id);
    $cek->execute();
    $cek->store_result();

    if ($cek->num_rows > 0) {
        echo "<script>alert('Username atau Email sudah digunakan orang lain!'); window.history.back();</script>";
        exit();
    }
    $cek->close();

    // ==========================================
    // LOGIKA UPLOAD FOTO (BAGIAN BARU)
    // ==========================================
    $nama_foto_baru = null; // Default null jika tidak ada upload

    if (isset($_FILES['foto_profil']) && $_FILES['foto_profil']['error'] === UPLOAD_ERR_OK) {
        $file_tmp   = $_FILES['foto_profil']['tmp_name'];
        $file_name  = $_FILES['foto_profil']['name'];
        $file_size  = $_FILES['foto_profil']['size'];
        $file_ext   = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));

        // Validasi Ekstensi
        $allowed_ext = ['jpg', 'jpeg', 'png', 'gif'];
        if (!in_array($file_ext, $allowed_ext)) {
            echo "<script>alert('Format file harus JPG, JPEG, PNG, atau GIF!'); window.history.back();</script>";
            exit();
        }

        // Validasi Ukuran (Maks 2MB)
        if ($file_size > 2000000) { 
            echo "<script>alert('Ukuran file terlalu besar (Max 2MB)!'); window.history.back();</script>";
            exit();
        }

        // Buat nama unik dan pindahkan file
        $nama_foto_baru = time() . '_' . uniqid() . '.' . $file_ext;
        $target_dir = "../public/uploads/"; // Pastikan folder ini ada!
        
        // Cek folder, buat jika belum ada
        if (!is_dir($target_dir)) { mkdir($target_dir, 0777, true); }

        if (!move_uploaded_file($file_tmp, $target_dir . $nama_foto_baru)) {
            echo "<script>alert('Gagal mengupload gambar!'); window.history.back();</script>";
            exit();
        }
    }

    // ==========================================
    // LOGIKA UPDATE DATABASE (KOMBINASI)
    // ==========================================
    
    // Skenario 1: Ganti Password & Ganti Foto
    if (!empty($pass_lama) && !empty($pass_baru) && $nama_foto_baru != null) {
        // ... (Verifikasi pass lama seperti sebelumnya) ...
        $stmt = $conn->prepare("SELECT password_hash FROM users WHERE user_id = ?");
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $res = $stmt->get_result();
        $row = $res->fetch_assoc();

        if (password_verify($pass_lama, $row['password_hash'])) {
            $new_hash = password_hash($pass_baru, PASSWORD_DEFAULT);
            $update = $conn->prepare("UPDATE users SET full_name=?, username=?, email=?, password_hash=?, profile_image=? WHERE user_id=?");
            $update->bind_param("sssssi", $nama, $username, $email, $new_hash, $nama_foto_baru, $user_id);
        } else {
            echo "<script>alert('Password lama salah!'); window.history.back();</script>";
            exit();
        }
    }
    // Skenario 2: Ganti Password SAJA (Tanpa Foto)
    else if (!empty($pass_lama) && !empty($pass_baru) && $nama_foto_baru == null) {
        $stmt = $conn->prepare("SELECT password_hash FROM users WHERE user_id = ?");
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $res = $stmt->get_result();
        $row = $res->fetch_assoc();

        if (password_verify($pass_lama, $row['password_hash'])) {
            $new_hash = password_hash($pass_baru, PASSWORD_DEFAULT);
            $update = $conn->prepare("UPDATE users SET full_name=?, username=?, email=?, password_hash=? WHERE user_id=?");
            $update->bind_param("ssssi", $nama, $username, $email, $new_hash, $user_id);
        } else {
            echo "<script>alert('Password lama salah!'); window.history.back();</script>";
            exit();
        }
    }
    // Skenario 3: Ganti Foto SAJA (Tanpa Password)
    else if ((empty($pass_lama) || empty($pass_baru)) && $nama_foto_baru != null) {
        $update = $conn->prepare("UPDATE users SET full_name=?, username=?, email=?, profile_image=? WHERE user_id=?");
        $update->bind_param("ssssi", $nama, $username, $email, $nama_foto_baru, $user_id);
    }
    // Skenario 4: Update Profil Biasa (Tanpa Password, Tanpa Foto)
    else {
        $update = $conn->prepare("UPDATE users SET full_name=?, username=?, email=? WHERE user_id=?");
        $update->bind_param("sssi", $nama, $username, $email, $user_id);
    }

    // 5. Eksekusi Update
    if ($update->execute()) {
        $_SESSION['username'] = $username;
        // Opsional: Update session foto juga jika perlu
        if($nama_foto_baru) { $_SESSION['profile_image'] = $nama_foto_baru; }

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
?>