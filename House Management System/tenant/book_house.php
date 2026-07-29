<?php
session_start();
include '../config/db.php';

if(!isset($_SESSION['id']) || $_SESSION['role'] != "tenant"){
    header("Location: ../login_admin.php");
    exit;
}

if(isset($_GET['house_id'])){
    $house_id  = intval($_GET['house_id']);
    $tenant_id = $_SESSION['id'];

    
    $check = $conn->prepare("SELECT status FROM houses WHERE id=?");
    $check->bind_param("i", $house_id);
    $check->execute();
    $house = $check->get_result()->fetch_assoc();

    if($house['status'] != 'available'){
        echo "<script>
            alert('This house is not available.');
            window.location.href='dashboard.php';
        </script>";
        exit;
    }

    // 2️⃣ Booking insert
    $booking_date = date("Y-m-d H:i:s");
    $return_date  = date('Y-m-d', strtotime('+30 days'));
    $status = 'pending';

    $stmt = $conn->prepare(
        "INSERT INTO bookings (tenant_id, house_id, booking_date, return_date, status)
         VALUES (?, ?, ?, ?, ?)"
    );
    $stmt->bind_param("iisss", $tenant_id, $house_id, $booking_date, $return_date, $status);

    if($stmt->execute()){
        // 3️⃣ House status → pending
        $update = $conn->prepare("UPDATE houses SET status='pending' WHERE id=?");
        $update->bind_param("i", $house_id);
        $update->execute();

        echo "<script>
            alert('Booking created! Waiting for owner approval.');
            window.location.href='dashboard.php';
        </script>";
    } else {
        echo "<script>
            alert('Booking failed!');
            window.location.href='dashboard.php';
        </script>";
    }
    exit;
}