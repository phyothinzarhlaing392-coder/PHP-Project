<?php
include '../config/db.php';

$id=$_GET['id'];

$conn->query("
UPDATE bookings
SET status='completed',
return_date=CURDATE()
WHERE id=$id
");

$conn->query("
UPDATE houses
SET status='available'
WHERE id=(SELECT house_id FROM bookings WHERE id=$id)
");

echo "House Returned!";
?>