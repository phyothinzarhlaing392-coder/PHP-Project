<?php
session_start();
if(!isset($_SESSION['id']) || $_SESSION['role'] != "owner"){
    header("Location: ../login_admin.php");
    exit;
}

include '../config/db.php';
$owner_id = $_SESSION['id'];

// Approve or reject action
if(isset($_GET['booking_id']) && isset($_GET['action'])){
    $booking_id = intval($_GET['booking_id']);
    $action = $_GET['action'];

    if($action === 'approve'){
        $new_status = 'approved'; // Owner approved
    } elseif($action === 'reject'){
        $new_status = 'rejected'; // Owner rejected
    }

    // Update booking status only if owner owns the house
    $stmt = $conn->prepare("
        UPDATE bookings b 
        JOIN houses h ON b.house_id = h.id 
        SET b.status = ? 
        WHERE b.id=? AND h.owner_id=?
    ");
    $stmt->bind_param("sii", $new_status, $booking_id, $owner_id);
    $stmt->execute();

    // Redirect tenant to payment page if approved
    if($action === 'approve'){
        header("Location: ../tenant/payment.php?booking_id=$booking_id");
        exit;
    }

    header("Location: owner_approve.php"); // refresh page for reject
    exit;
}

// Fetch pending bookings
$stmt = $conn->prepare("
    SELECT b.id AS booking_id, b.tenant_id, b.booking_date, b.return_date, b.status, h.title 
    FROM bookings b 
    JOIN houses h ON b.house_id = h.id 
    WHERE h.owner_id = ? AND b.status='pending'
");
$stmt->bind_param("i",$owner_id);
$stmt->execute();
$result = $stmt->get_result();
?>
<!DOCTYPE html>
<html>
<head>
    <title>Owner Approve Bookings</title>
    <style>
        body{font-family:Arial,sans-serif; background:#f4f4f4; padding:20px;}
        table{width:100%; border-collapse:collapse;}
        th,td{border:1px solid #ccc; padding:10px; text-align:center;}
        th{background:#007BFF; color:#fff;}
        a.approve{background:#28a745; color:#fff; padding:5px 10px; border-radius:5px; text-decoration:none;}
        a.reject{background:#dc3545; color:#fff; padding:5px 10px; border-radius:5px; text-decoration:none;}
        a.approve:hover{background:#218838;}
        a.reject:hover{background:#c82333;}
    </style>
</head>
<body>
<h1>Pending Bookings</h1>
<table>
    <tr>
        <th>Booking ID</th>
        <th>House</th>
        <th>Tenant ID</th>
        <th>Booking Date</th>
        <th>Return Date</th>
        <th>Action</th>
    </tr>
    <?php if($result->num_rows>0): while($row=$result->fetch_assoc()): ?>
        <tr>
            <td><?php echo $row['booking_id']; ?></td>
            <td><?php echo htmlspecialchars($row['title']); ?></td>
            <td><?php echo $row['tenant_id']; ?></td>
            <td><?php echo $row['booking_date']; ?></td>
            <td><?php echo $row['return_date']; ?></td>
            <td>
                <a class="approve" href="?booking_id=<?php echo $row['booking_id']; ?>&action=approve">Approve</a>
                <a class="reject" href="?booking_id=<?php echo $row['booking_id']; ?>&action=reject">Reject</a>
            </td>
        </tr>
    <?php endwhile; else: ?>
        <tr><td colspan="6">No pending bookings</td></tr>
    <?php endif; ?>
     <div style="text-align:center; margin-top:15px;">
        <a href="../owner/dashboard.php">Back to dashboard</a> | <a href="../logout.php">Logout</a>
    </div>
</table>
</body>
</html>