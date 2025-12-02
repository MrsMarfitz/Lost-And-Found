<?php
// 1. Panggil Mesin
require_once '../includes/crud_barang.php';

// 2. Ambil ID yang mau dihapus
if (isset($_GET['id'])) {
    $id = $_GET['id'];
    
    // 3. Suruh mesin menghapus
    if (hapusBarang($id)) {
        // Kalau berhasil, balik ke daftar
        echo "<script>alert('Laporan berhasil dihapus.'); window.location='report_list.php';</script>";
    } else {
        // Kalau gagal
        echo "<script>alert('Gagal menghapus data.'); window.location='report_list.php';</script>";
    }
} else {
    // Kalau orang iseng buka file ini tanpa ID, tendang balik
    header("Location: report_list.php");
}
?>