<?php
session_start();

// 1. PANGGIL KONEKSI DATABASE
require_once __DIR__ . '/../config/db_connect.php';

// 2. Cek Admin
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit();
}

// 3. Ambil data dari URL (id dan status)
if (isset($_GET['id']) && isset($_GET['status'])) {
    $id = $_GET['id'];
    $status = $_GET['status']; // Nilainya bisa 'Approve', 'Reject', 'Approved', 'Rejected'

    // Update Query
    $query = "UPDATE reports SET status = ? WHERE report_id = ?";
    
    if ($stmt = $conn->prepare($query)) {
        $stmt->bind_param("si", $status, $id);
        
        if ($stmt->execute()) {
            // Berhasil, kembali ke halaman reports
            header("Location: reports.php?success=Status berhasil diubah menjadi $status");
            exit();
        } else {
            echo "Gagal update: " . $conn->error;
        }
    } else {
        echo "Query Error: " . $conn->error;
    }
} else {
    // Jika parameter kurang, kembalikan ke list
    header("Location: reports.php");
    exit();
}
?>