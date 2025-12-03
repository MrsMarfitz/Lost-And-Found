<?php
require_once '../includes/crud_barang.php';
require __DIR__ . '/../config/config.php';
require __DIR__ . '/../config/db_connect.php';

if (isset($_GET['id'])) {
    $id = $_GET['id'];
    
    if (hapusBarang($id)) {
        echo "<script>alert('Laporan berhasil dihapus.'); window.location='report_list.php';</script>";
    } else {
        echo "<script>alert('Gagal menghapus data.'); window.location='report_list.php';</script>";
    }
} else {
    header("Location: report_list.php");
}

?>
