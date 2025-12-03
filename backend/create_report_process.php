<?php
session_start();

// 1. Hubungkan ke Database
// Pastikan path ini benar (mundur satu folder dari backend, lalu masuk config)
require '../config/config.php';
require '../config/db_connect.php';

// 2. Cek Keamanan: Apakah User Login? Apakah lewat tombol Submit?
if (!isset($_SESSION['user_id'])) {
    // Kalau belum login, tendang ke halaman login
    header("Location: ../public/login.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    // Kalau buka file ini langsung tanpa lewat form, tendang ke dashboard
    header("Location: ../public/dashboard.php");
    exit();
}

// 3. Ambil Data dari Formulir (Pastikan name="" di HTML sama dengan $_POST disini)
$user_id     = $_SESSION['user_id'];
$type        = $_POST['type'] ?? 'Hilang'; // Default Hilang
$title       = trim($_POST['title']);
$description = trim($_POST['description']);
$location    = trim($_POST['location']);
$date        = $_POST['date']; // Format dari HTML biasanya YYYY-MM-DD

// 4. Validasi Sederhana (Cek kolom wajib)
if (empty($title) || empty($location) || empty($date)) {
    $pesan = urlencode("Judul, Lokasi, dan Tanggal wajib diisi!");
    header("Location: ../public/report_create.php?status=failed&msg=$pesan");
    exit();
}

// 5. PROSES UPLOAD FOTO (Bagian Paling Rawan Error)
$photo_name = null; // Default null jika tidak ada foto

// Cek apakah user mengupload file?
if (isset($_FILES['photo']) && $_FILES['photo']['error'] === 0) {
    
    // a. Aturan File
    $allowed_ext = ['jpg', 'jpeg', 'png', 'webp'];
    $max_size    = 2 * 1024 * 1024; // 2MB dalam byte

    // b. Ambil Info File
    $file_name = $_FILES['photo']['name'];
    $file_size = $_FILES['photo']['size'];
    $file_tmp  = $_FILES['photo']['tmp_name'];
    
    // c. Ambil Ekstensi File (contoh: jpg)
    $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));

    // d. Validasi Ekstensi & Ukuran
    if (!in_array($file_ext, $allowed_ext)) {
        header("Location: ../public/report_create.php?status=failed&msg=Format foto harus JPG, PNG, atau WEBP.");
        exit();
    }

    if ($file_size > $max_size) {
        header("Location: ../public/report_create.php?status=failed&msg=Ukuran foto terlalu besar (Maks 2MB).");
        exit();
    }

    // e. Buat Nama Baru yang Unik (Biar file gak saling tindih)
    // Contoh hasil: 654a3b2c1d.jpg
    $new_file_name = uniqid() . '.' . $file_ext;

    // f. Tentukan Lokasi Simpan
    // Simpan di folder public/uploads/
    $target_dir = '../public/uploads/';
    $target_file = $target_dir . $new_file_name;

    // g. Pindahkan File dari folder sementara ke folder tujuan
    if (move_uploaded_file($file_tmp, $target_file)) {
        $photo_name = $new_file_name; // Simpan nama ini ke database nanti
    } else {
        header("Location: ../public/report_create.php?status=failed&msg=Gagal mengupload foto ke server.");
        exit();
    }
}

// 6. SIMPAN DATA KE DATABASE
// Query Insert
$query = "INSERT INTO reports (user_id, status, title, description, location, incident_date, photo) 
          VALUES (?, ?, ?, ?, ?, ?, ?)";

$stmt = $conn->prepare($query);

if ($stmt) {
    // Bind Parameter: i = integer, s = string
    // Urutan: user_id(i), status(s), title(s), description(s), location(s), date(s), photo(s)
    $stmt->bind_param("issssss", $user_id, $type, $title, $description, $location, $date, $photo_name);

    if ($stmt->execute()) {
        // --- BERHASIL ---
        // Redirect ke halaman Daftar Laporan
        header("Location: ../public/report_list.php?status=success");
        exit();
    } else {
        // Gagal Eksekusi Query
        $error = urlencode("Database Error: " . $stmt->error);
        header("Location: ../public/report_create.php?status=failed&msg=$error");
        exit();
    }
} else {
    // Gagal Prepare Statement
    header("Location: ../public/report_create.php?status=failed&msg=Sistem Error (Prepare Failed).");
    exit();
}
?>