<?php
require_once "auth.php";
require_once "../config/config.php";

$table="reports"; 
$idCol="id";
$statusCol="status";

$id = $_GET["id"] ?? null;
$status = $_GET["status"] ?? null;

$allowed = ["pending","approved","rejected"];

if($id && $status && in_array($status,$allowed)){
  $stmt = $conn->prepare("UPDATE $table SET $statusCol=? WHERE $idCol=?");
  $stmt->bind_param("si",$status,$id);
  $stmt->execute();
}

header("Location: reports.php");
exit;
