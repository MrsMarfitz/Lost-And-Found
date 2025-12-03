<?php
require_once "auth.php";
require_once "../config/config.php";

$table="reports";
$idCol="id";

$id = $_GET["id"] ?? null;

if($id){
  $stmt = $conn->prepare("DELETE FROM $table WHERE $idCol=?");
  $stmt->bind_param("i",$id);
  $stmt->execute();
}

header("Location: reports.php");
exit;
