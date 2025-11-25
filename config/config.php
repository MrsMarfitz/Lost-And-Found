<?php

$host = "localhost";
$db   = "lostfound_db";
$user = "root";
$pass = "";

$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) {
    die("Database connection failed");
}
session_start();
