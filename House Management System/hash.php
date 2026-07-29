<?php
$password = "admin123"; // plain text password
$hashed_password = password_hash($password, PASSWORD_DEFAULT); // အမှန်တဲ့ algorithm parameter
echo $hashed_password;
?>