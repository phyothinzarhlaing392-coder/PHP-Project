<?php
$conn = new mysqli("localhost","root","root","house_db");

if($conn->connect_error){
die("Database Failed");
}
?>