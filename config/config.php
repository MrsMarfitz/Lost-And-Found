<?php
<<<<<<< Updated upstream
$host = "localhost";
$user = "root";
$pass = "";
$db = "lost_and_found_db";

$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) {
    die("Koneksi Database Gagal: " . $conn->connect_error);
}
$conn->set_charset("utf8mb4");

define('ROOT_URL', 'http://localhost:8081/Lost-And-Found/');

session_start();
=======
// Cek dulu apakah session sudah jalan atau belum agar tidak error
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

define('ROOT_URL', 'http://localhost/Lost-And-Found/');
?>
>>>>>>> Stashed changes
