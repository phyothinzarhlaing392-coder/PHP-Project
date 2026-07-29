<?php
include '../config/db.php';

$id=$_GET['id'];

$conn->query("UPDATE bookings SET status='approved' WHERE id=$id");

$conn->query("
UPDATE houses
SET status='booked'
WHERE id=(SELECT house_id FROM bookings WHERE id=$id)
");

echo "Booking Approved!";
?>