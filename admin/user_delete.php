<?php
session_start();

// 1. Aktifkan Error Reporting (Agar ketahuan jika ada error, bukan layar putih)
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

try {
    // 2. Koneksi Database (Gunakan __DIR__ agar path lebih aman)
    // Asumsi: file ini ada di folder 'admin', dan db_connect ada di folder 'config' (naik satu level)
    require_once __DIR__ . '/../config/db_connect.php';

    // 3. Cek Login
    if (!isset($_SESSION['user_id'])) {
        header("Location: ../login.php");
        exit();
    }

    // 4. Proses Hapus
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['user_id'])) {
        $id_to_delete = $_POST['user_id'];

        // KEAMANAN: Jangan biarkan admin menghapus dirinya sendiri
        if ($id_to_delete == $_SESSION['user_id']) {
            header("Location: users.php?error=Anda tidak bisa menghapus akun yang sedang anda gunakan!");
            exit();
        }

        // Eksekusi Delete
        $stmt = $conn->prepare("DELETE FROM users WHERE user_id = ?");
        $stmt->bind_param("i", $id_to_delete);

        if ($stmt->execute()) {
            // SUKSES: Redirect dengan pesan success
            header("Location: users.php?success=1"); 
            exit();
        } else {
            throw new Exception("Gagal mengeksekusi query delete.");
        }
    } else {
        // Jika file dibuka langsung tanpa tombol hapus
        header("Location: users.php");
        exit();
    }

} catch (Exception $e) {
    // Jika terjadi error (misal koneksi database salah), kembali dengan pesan error
    $error_msg = urlencode($e->getMessage());
    header("Location: users.php?error=" . $error_msg);
    exit();
}
?>