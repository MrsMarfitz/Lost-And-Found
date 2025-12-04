<?php
session_start();

require '../config/config.php';
require '../config/db_connect.php';

// Cek Login & Method
if (!isset($_SESSION['user_id'])) {
    header("Location: ../public/login.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: ../public/dashboard.php");
    exit();
}

$user_id     = $_SESSION['user_id'];
$input_type  = $_POST['type'] ?? 'Hilang'; 
$title       = trim($_POST['title']);
$description = trim($_POST['description']);
$location    = trim($_POST['location']);
$date        = $_POST['date']; 

if ($input_type == 'Ditemukan') {
    $db_type = 'Found';      
    $db_status = 'Ditemukan'; 
} else {
    $db_type = 'Lost';       
    $db_status = 'Hilang';   
}

if (empty($title) || empty($location) || empty($date)) {
    $pesan = urlencode("Judul, Lokasi, dan Tanggal wajib diisi!");
    header("Location: ../public/report_create.php?status=failed&msg=$pesan");
    exit();
}

$photo_name = null; 

if (isset($_FILES['photo']) && $_FILES['photo']['error'] === 0) {
    $allowed_ext = ['jpg', 'jpeg', 'png', 'webp'];
    $max_size    = 2 * 1024 * 1024; // 2MB
    
    $file_name = $_FILES['photo']['name'];
    $file_tmp  = $_FILES['photo']['tmp_name'];
    $file_ext  = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));

    if (!in_array($file_ext, $allowed_ext)) {
        header("Location: ../public/report_create.php?status=failed&msg=Format foto salah.");
        exit();
    }

    if ($_FILES['photo']['size'] > $max_size) {
        header("Location: ../public/report_create.php?status=failed&msg=Foto terlalu besar (Max 2MB).");
        exit();
    }

    $new_file_name = uniqid() . '.' . $file_ext;
    
    $target_dir = '../public/uploads/';
    $target_file = $target_dir . $new_file_name;

    if (move_uploaded_file($file_tmp, $target_file)) {
        $photo_name = $new_file_name;
    } else {
        header("Location: ../public/report_create.php?status=failed&msg=Gagal upload foto.");
        exit();
    }
}

$query = "INSERT INTO reports (user_id, type, status, title, description, location, incident_date, photo) 
          VALUES (?, ?, ?, ?, ?, ?, ?, ?)";

$stmt = $conn->prepare($query);

if ($stmt) {
    // Bind Param: 8 variabel (i = integer, s = string)
    // Urutan: user_id, type, status, title, description, location, incident_date, photo
    $stmt->bind_param("isssssss", $user_id, $db_type, $db_status, $title, $description, $location, $date, $photo_name);

    if ($stmt->execute()) {
        header("Location: ../public/report_list.php?status=success");
        exit();
    } else {
        $error = urlencode("DB Error: " . $stmt->error);
        header("Location: ../public/report_create.php?status=failed&msg=$error");
        exit();
    }
} else {
    $error = urlencode("Prepare Failed: " . $conn->error);
    header("Location: ../public/report_create.php?status=failed&msg=$error");
    exit();
}
?>