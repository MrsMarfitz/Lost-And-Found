<?php
session_start();
require_once __DIR__ . '/../config/db_connect.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit();
}

if (isset($_GET['id']) && isset($_GET['status'])) {
    $id = $_GET['id'];
    $status = $_GET['status'];

    $query = "UPDATE reports SET status = ? WHERE report_id = ?";
    
    if ($stmt = $conn->prepare($query)) {
        $stmt->bind_param("si", $status, $id);
        
        if ($stmt->execute()) {

            header("Location: reports.php?success=Status berhasil diubah");
            exit();
        } else {
            echo "Error executing query: " . $stmt->error;
        }
        $stmt->close();
    } else {
        echo "Error preparing statement: " . $conn->error;
    }
} else {

    header("Location: reports.php");
    exit();
}
?>