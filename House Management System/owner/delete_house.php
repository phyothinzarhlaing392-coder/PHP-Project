<?php
session_start();

// Check login & role
if(!isset($_SESSION['id']) || $_SESSION['role'] != "owner"){
    header("Location: ../login.php");
    exit;
}

include '../config/db.php';

$owner_id = $_SESSION['id'];
$house_id = $_GET['id'] ?? 0;

// Check if house exists and belongs to this owner
$stmt = $conn->prepare("SELECT * FROM houses WHERE id=? AND owner_id=?");
$stmt->bind_param("ii", $house_id, $owner_id);
$stmt->execute();
$result = $stmt->get_result();

if($result->num_rows == 0){
    echo "House not found or you don't have permission to delete.";
    exit;
}

// Delete house
$stmt_delete = $conn->prepare("DELETE FROM houses WHERE id=? AND owner_id=?");
$stmt_delete->bind_param("ii", $house_id, $owner_id);
$stmt_delete->execute();

// Redirect back to dashboard
header("Location: dashboard.php");
exit;
?>