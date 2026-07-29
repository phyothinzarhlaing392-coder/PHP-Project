<?php
session_start();
if(!isset($_SESSION['id']) || $_SESSION['role'] != "tenant"){
    header("Location: ../dashboard.php");
    exit;
}

include '../config/db.php';
$tenant_id = $_SESSION['id'];

// Pending bookings (status = approved and not yet paid)
$stmt_pending = $conn->prepare("
    SELECT b.id, h.title, h.price, b.status, b.payment_status
    FROM bookings b
    JOIN houses h ON b.house_id = h.id
    WHERE b.tenant_id=? AND b.payment_status='unpaid' AND b.status='approved'
    ORDER BY b.booking_date DESC
");
$stmt_pending->bind_param("i", $tenant_id);
$stmt_pending->execute();
$pending = $stmt_pending->get_result();

// Paid bookings with payment method info
$stmt_paid = $conn->prepare("
    SELECT b.id, h.title, h.price, b.status, b.payment_status, p.method, p.bank
    FROM bookings b
    JOIN houses h ON b.house_id = h.id
    JOIN payments p ON p.booking_id = b.id
    WHERE b.tenant_id=? AND b.payment_status='paid'
    ORDER BY b.booking_date DESC
");
$stmt_paid->bind_param("i", $tenant_id);
$stmt_paid->execute();
$paid = $stmt_paid->get_result();
?>
<!DOCTYPE html>
<html>
<head>
    <title>Tenant Dashboard</title>
    <style>
        body{font-family:Arial,sans-serif; padding:20px; background:#f4f4f4;}
        h2{text-align:center; color:#007BFF;}
        table{width:100%; border-collapse:collapse; margin-bottom:30px; background:#fff;}
        th,td{border:1px solid #ccc; padding:10px; text-align:center;}
        th{background:#007BFF; color:#fff;}
        a.pay{background:#28a745; color:#fff; padding:5px 10px; border-radius:5px; text-decoration:none;}
        a.pay:hover{background:#218838;}
        .paid{color:#28a745; font-weight:bold;}
    </style>
</head>
<body>

<h2>Pending Payments (Pay Now)</h2>
<?php if($pending->num_rows>0): ?>
<table>
    <tr>
        <th>Booking ID</th>
        <th>House</th>
        <th>Price</th>
        <th>Status</th>
        <th>Action</th>
    </tr>
    <?php while($row=$pending->fetch_assoc()): ?>
    <tr>
        <td><?php echo $row['id']; ?></td>
        <td><?php echo htmlspecialchars($row['title']); ?></td>
        <td>$<?php echo number_format($row['price'],2); ?></td>
        <td><?php echo $row['status']; ?></td>
        <td><a class="pay" href="payment.php?booking_id=<?php echo $row['id']; ?>">Pay Now</a></td>
    </tr>
    <?php endwhile; ?>
</table>
<?php else: ?>
<p style="text-align:center;">No pending payments.</p>
<?php endif; ?>

<h2>Paid Bookings</h2>
<?php if($paid->num_rows>0): ?>
<table>
    <tr>
        <th>Booking ID</th>
        <th>House</th>
        <th>Price</th>
        <th>Status</th>
        <th>Payment Method</th>
        <th>Bank (if any)</th>
    </tr>
    <?php while($row=$paid->fetch_assoc()): ?>
    <tr>
        <td><?php echo $row['id']; ?></td>
        <td><?php echo htmlspecialchars($row['title']); ?></td>
        <td>$<?php echo number_format($row['price'],2); ?></td>
        <td><?php echo $row['status']; ?></td>
        <td class="paid"><?php echo ucfirst($row['method']); ?></td>
        <td><?php echo $row['bank'] ? htmlspecialchars($row['bank']) : '-'; ?></td>
    </tr>
    <?php endwhile; ?>
</table>
<?php else: ?>
<p style="text-align:center;">No paid bookings yet.</p>
<?php endif; ?>
 <div style="text-align:center; margin-top:15px;">
        <a href="../tenant/dashboard.php">Back to dashboard</a> | <a href="../logout.php">Logout</a>
    </div>
</body>
</html>