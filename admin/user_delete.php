<?php
require_once "auth.php";
require_once "../config/db_connect.php";

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['user_id'])) {
    $user_id = intval($_POST['user_id']);

    // Jangan hapus Super Admin (asumsi user_id = 1)
    if ($user_id === 1) {
        header("Location: users.php?error=tidak_bisa_hapus_superadmin");
        exit;
    }

    // Query hapus user (gunakan user_id yang benar)
    $query = "DELETE FROM users WHERE user_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $user_id);

    if ($stmt->execute()) {
        header("Location: users.php?success=1");
    } else {
        header("Location: users.php?error=gagal_hapus_user");
    }
    exit;
} else {
    header("Location: users.php");
    exit;
}
?>
