<?php
$host = "localhost";
$db   = "lostfound_db";
$user = "root";
$pass = "user123";

$conn = new mysqli($host, $user, $user123, $db);

if ($conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);
}

session_start();
