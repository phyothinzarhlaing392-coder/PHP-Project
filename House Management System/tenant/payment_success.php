<?php
session_start();
if(!isset($_SESSION['id']) || $_SESSION['role'] != "tenant"){
    header("Location: ../booking_history.php");
    exit;
}
$booking_id = isset($_GET['booking_id']) ? intval($_GET['booking_id']) : 0;
?>

<!DOCTYPE html>
<html>
<head>
    <title>Payment Successful</title>
    <style>
        body{font-family:Arial,sans-serif; padding:20px; background:#f0f0f0;}
        .card{background:#fff; padding:20px; max-width:400px; margin:auto; border-radius:10px; box-shadow:0 5px 15px rgba(0,0,0,0.3); text-align:center;}
        a{display:inline-block; margin-top:15px; padding:10px 15px; background:#007BFF; color:#fff; text-decoration:none; border-radius:5px;}
        a:hover{background:#0056b3;}
    </style>
</head>
<body>
<div class="card">
    <h2>Payment Successful!</h2>
    <p>Booking ID: <?php echo $booking_id; ?></p>
    <a href="dashboard.php">Go to Dashboard</a>
</div>
</body>
</html>