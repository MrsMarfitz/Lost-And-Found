<?php
// --- Bagian 1: Koneksi Database (Wajib Ada) ---
$host = "localhost";
$user = "root";
$pass = "";
$db = "lost_and_found_db"; // PASTIKAN NAMA DB INI SAMA PERSIS DENGAN NAMA DI PHPMYADMIN!

$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) {
    die("Koneksi Database Gagal: " . $conn->connect_error);
}
$conn->set_charset("utf8mb4");

define('ROOT_URL', 'http://localhost:8081/Lost-And-Found/');

session_start();