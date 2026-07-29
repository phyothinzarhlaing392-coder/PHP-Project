
<?php
session_start();
if(!isset($_SESSION['id']) || $_SESSION['role'] != "tenant"){
    header("Location: ../login_admin.php");
    exit;
}
include '../config/db.php';

$tenant_id = $_SESSION['id'];

// Fetch unpaid bookings
$stmt = $conn->prepare("
    SELECT b.id AS booking_id, b.booking_date, b.return_date, h.title, h.price AS monthly_rent
    FROM bookings b
    JOIN houses h ON b.house_id = h.id
    WHERE b.tenant_id=? AND b.payment_status='unpaid'
");
$stmt->bind_param("i", $tenant_id);
$stmt->execute();
$result = $stmt->get_result();

// Handle payment
if(isset($_POST['pay'])){
    $booking_id = intval($_POST['booking_id']);
    $amount = floatval($_POST['total_amount']);
    $payment_method = $_POST['payment_method']; // cash or bank
    $bank_name = ($payment_method=='bank' && isset($_POST['bank_name'])) ? $_POST['bank_name'] : NULL;
    $account_number = ($payment_method=='bank' && isset($_POST['account_number'])) ? $_POST['account_number'] : NULL;

    // Insert payment record
    $stmt_pay = $conn->prepare("INSERT INTO payments (booking_id, amount, method, bank, account_number, status, payment_date) VALUES (?,?,?,?,?, 'paid', NOW())");
    $stmt_pay->bind_param("idsss", $booking_id, $amount, $payment_method, $bank_name, $account_number);
    $stmt_pay->execute();

    // Update booking payment_status
    $stmt_update = $conn->prepare("UPDATE bookings SET payment_status='paid' WHERE id=?");
    $stmt_update->bind_param("i", $booking_id);
    $stmt_update->execute();

    $success = "✅ Payment successful via ".htmlspecialchars($payment_method).($bank_name ? " ($bank_name)" : "")."!";
    header("Refresh:1");
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Tenant Payment</title>
    <style>
        body{font-family:Arial,sans-serif; padding:20px; background:#f4f4f4;background: url('https://i.pinimg.com/736x/fb/fc/8e/fbfc8ecd5f42a4a8f64451d7c5eab611.jpg') no-repeat center center fixed; background-size: 1550px 800px;}
        .container{max-width:600px; margin:auto; background:#fff; padding:20px; border-radius:10px;}
        h1{text-align:center; color:#007BFF;}
        table{width:100%; border-collapse:collapse; margin-top:20px;}
        th,td{border:1px solid #ccc; padding:10px; text-align:center;}
        th{background:#007BFF; color:#fff;}
        input[type="submit"]{padding:10px 20px; background:#28a745; color:#fff; border:none; border-radius:5px; cursor:pointer;}
        input[type="submit"]:hover{background:#218838;}
        .success{color:green; text-align:center; margin-bottom:15px;}
        .bank-fields{margin-top:10px;}
    </style>
    <script>
        function calculateTotal(id){
            var start = new Date(document.getElementById('start_'+id).value);
            var end = new Date(document.getElementById('end_'+id).value);
            var months = (end.getFullYear()-start.getFullYear())*12 + (end.getMonth()-start.getMonth()) + 1;
            if(months<1) months=1;
            var rent = parseFloat(document.getElementById('rent_'+id).value);
            var total = months * rent;
            document.getElementById('months_'+id).innerText = months;
            document.getElementById('total_'+id).innerText = '$'+total.toFixed(2);
            document.getElementById('total_amount_'+id).value = total;
        }

        function toggleBank(booking_id){
            var method = document.getElementById('payment_method_'+booking_id).value;
            var bankDiv = document.getElementById('bank_info_'+booking_id);
            bankDiv.style.display = (method==='bank') ? 'block' : 'none';
        }
    </script>
</head>
<body>
<div class="container">
    <h1>Payment for Your Booking</h1>
    <?php if(isset($success)) echo "<div class='success'>$success</div>"; ?>

    <?php while($row = $result->fetch_assoc()):
        $booking_id = $row['booking_id'];
        $monthly_rent = $row['monthly_rent'];
        $start = new DateTime($row['booking_date']);
        $end = new DateTime($row['return_date']);


$months = max(1, ($end->format('Y')-$start->format('Y'))*12 + ($end->format('m')-$start->format('m')));
        $total_amount = $monthly_rent * $months;
    ?>
    <table>
        <tr><th>House</th><td><?php echo htmlspecialchars($row['title']); ?></td></tr>
        <tr><th>Booking Start</th><td><input type="date" id="start_<?php echo $booking_id;?>" value="<?php echo date('Y-m-d', strtotime($row['booking_date']));?>" onchange="calculateTotal(<?php echo $booking_id;?>)"></td></tr>
        <tr><th>Booking End</th><td><input type="date" id="end_<?php echo $booking_id;?>" value="<?php echo date('Y-m-d', strtotime($row['return_date']));?>" onchange="calculateTotal(<?php echo $booking_id;?>)"></td></tr>
        <tr><th>Monthly Rent</th><td>$<span id="rent_display_<?php echo $booking_id;?>"><?php echo $monthly_rent;?></span></td></tr>
        <tr><th>Months</th><td id="months_<?php echo $booking_id;?>"><?php echo $months;?></td></tr>
        <tr><th>Total Amount</th><td id="total_<?php echo $booking_id;?>">$<?php echo $total_amount;?></td></tr>
    </table>

    <form method="POST" style="margin-top:10px; text-align:center;">
        <input type="hidden" name="booking_id" value="<?php echo $booking_id;?>">
        <input type="hidden" id="total_amount_<?php echo $booking_id;?>" name="total_amount" value="<?php echo $total_amount;?>">
        <input type="hidden" id="rent_<?php echo $booking_id;?>" value="<?php echo $monthly_rent;?>">

        <label>Payment Method:</label>
        <select name="payment_method" id="payment_method_<?php echo $booking_id;?>" onchange="toggleBank(<?php echo $booking_id;?>)" required>
            <option value="">Select</option>
            <option value="cash">Cash</option>
            <option value="bank">Bank Transfer</option>
        </select>

        <div id="bank_info_<?php echo $booking_id;?>" class="bank-fields" style="display:none;">
            <label>Bank:</label>
            <select name="bank_name">
                <option value="">Select Bank</option>
                <option value="CB">CB Bank</option>
                <option value="KBZ">KBZ Bank</option>
                <option value="AYA">AYA Bank</option>
            </select>
            <br>
            <label>Account Number:</label>
            <input type="text" name="account_number" placeholder="Enter your bank account number">
            <p>Use your Booking ID as transfer reference.</p>
        </div>

        <br><br>
        <input type="submit" name="pay" value="Pay Now">
    </form>
    <br><hr>
    <?php endwhile; ?>

    <div style="text-align:center; margin-top:15px;">
        <a href="../tenant/dashboard.php">Back to dashboard</a> | <a href="../logout.php">Logout</a>
    </div>
</div>
</body>
</html>