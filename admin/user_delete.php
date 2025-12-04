<?php
session_start();
// Aktifkan error reporting agar kalau ada error ketahuan, bukan blank
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

try {
    // Panggil koneksi database
    require_once __DIR__ . '/../config/db_connect.php';

    // Cek Login Admin
    if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
        header("Location: ../login.php");
        exit();
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['user_id'])) {
        $id_to_delete = $_POST['user_id'];

        // Cek jangan hapus diri sendiri
        if ($id_to_delete == $_SESSION['user_id']) {
            header("Location: users.php?error=Tidak bisa menghapus akun sendiri!");
            exit();
        }

        // Jalankan Delete
        $stmt = $conn->prepare("DELETE FROM users WHERE user_id = ?");
        $stmt->bind_param("i", $id_to_delete);
        $stmt->execute();

        header("Location: users.php?success=Pengguna berhasil dihapus");
        exit();
    } else {
        header("Location: users.php");
        exit();
    }

} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
?>