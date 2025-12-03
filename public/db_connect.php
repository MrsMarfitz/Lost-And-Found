<?php

$servername = "localhost";
<<<<<<< Updated upstream:public/db_connect.php
$username = "root";       
$password = "";           
$database = "lost_and_found_db"; 

=======
$username   = "root";
$password   = "";
$database   = "lost_and_found_schema";
>>>>>>> Stashed changes:config/db_connect.php

$conn = new mysqli($servername, $username, $password, $database);


if ($conn->connect_error) {

    die("Koneksi gagal: " . $conn->connect_error);
}


$conn->set_charset("utf8mb4");


?>