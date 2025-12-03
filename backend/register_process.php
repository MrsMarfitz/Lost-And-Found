<?php
// ... (Bagian koneksi database tetap sama) ...

// --- VALIDASI INPUT ---
if (empty($username) || empty($email) || empty($password) || empty($full_name)) {
    $pesan = urlencode("Semua kolom wajib diisi.");
    header("Location: ../public/register.php?status=failed&msg={$pesan}");
    exit();
}

// --- VALIDASI PASSWORD ---
if ($password !== $confirm_password) {
    $pesan = urlencode("Password dan Konfirmasi Password tidak cocok.");
    header("Location: ../public/register.php?status=failed&msg={$pesan}");
    exit();
}

// ... (Proses Hash Password dll tetap sama) ...

// --- EKSEKUSI QUERY DENGAN PENANGANAN DUPLIKAT ---
try {
    if ($stmt->execute()) {
        // SUKSES
        header("Location: ../public/login.php?status=success");
        exit();
    } else {
        throw new Exception($stmt->error);
    }
} catch (mysqli_sql_exception $e) {
    // Cek Error Code 1062 (Duplicate Entry)
    if ($conn->errno == 1062) {
        $pesan = urlencode("Username atau Email sudah terdaftar. Gunakan yang lain.");
    } else {
        $pesan = urlencode("Terjadi kesalahan sistem. Coba lagi nanti.");
    }
    
    // Kembalikan user ke halaman register dengan pesan error
    header("Location: ../public/register.php?status=failed&msg={$pesan}");
    exit();
} catch (Exception $e) {
    // Error umum lainnya
    $pesan = urlencode("Gagal mendaftar: " . $e->getMessage());
    header("Location: ../public/register.php?status=failed&msg={$pesan}");
    exit();
}
?>