<?php
session_start();

// 1. PANGGIL KONEKSI DATABASE (Ini yang sebelumnya kurang)
require_once __DIR__ . '/../config/db_connect.php';

// 2. Cek apakah user adalah Admin
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit();
}

// 3. Proses Delete
if (isset($_GET['id'])) {
    $report_id = $_GET['id'];

    // (Opsional) Hapus foto dulu dari folder uploads jika mau bersih
    /*
    $query_foto = "SELECT photo FROM reports WHERE report_id = ?";
    if ($stmt = $conn->prepare($query_foto)) {
        $stmt->bind_param("i", $report_id);
        $stmt->execute();
        $res = $stmt->get_result();
        if ($row = $res->fetch_assoc()) {
            $file_path = __DIR__ . '/../public/uploads/' . $row['photo'];
            if (file_exists($file_path)) unlink($file_path);
        }
    }
    */

    // Hapus Data dari Database
    $query = "DELETE FROM reports WHERE report_id = ?";
    
    if ($stmt = $conn->prepare($query)) {
        $stmt->bind_param("i", $report_id);
        
        if ($stmt->execute()) {
            // Berhasil
            header("Location: reports.php?success=Laporan berhasil dihapus");
            exit();
        } else {
            echo "Error deleting: " . $conn->error;
        }
    } else {
        echo "Error preparing statement: " . $conn->error;
    }
} else {
    header("Location: reports.php");
    exit();
}
?>