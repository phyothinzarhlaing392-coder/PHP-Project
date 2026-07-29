
<?php
session_start();
if(!isset($_SESSION['id']) || $_SESSION['role'] != "owner"){
    header("Location: ../login_admin.php");
    exit;
}

include '../config/db.php';
$owner_id = $_SESSION['id'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Owner Dashboard</title>
    <style>
        body {
            background: url('https://i.pinimg.com/736x/fb/fc/8e/fbfc8ecd5f42a4a8f64451d7c5eab611.jpg') no-repeat center center fixed;
            background-size: 1550px 800px;
            font-family: Arial, sans-serif;
            margin:0; padding:0;
            
        }
        .overlay {
            background: rgba(0,0,0,0.5);
            width: 100%; min-height: 100vh;
            padding: 20px;
        }
        .container {
            background: rgba(255,255,255,0.95);
            padding: 20px; border-radius:10px;
            max-width: 1200px; margin:auto;
            box-shadow: 0px 0px 20px #00000080;
        }
        h1 { text-align:center; color:#333; }
        a.add-house, a.logout { display:inline-block; margin:5px; padding:10px 20px; border-radius:5px; color:#fff; text-decoration:none; }
        a.add-house { background-color:#28a745; }
        a.add-house:hover { background-color:#218838; }
        a.logout { background-color:red; float:right; }
        a.logout:hover { background-color:#5a6268; }
        table { width:100%; border-collapse: collapse; margin-top:20px; }
        th, td { padding:12px; border:1px solid #ddd; text-align:center; }
        th { background-color:#007BFF; color:#fff; }
        img.house-img { width:100px; height:80px; object-fit:cover; border-radius:5px; }
        a.action-btn { padding:5px 10px; border-radius:5px; color:white; text-decoration:none; margin:2px; display:inline-block;}
        a.update { background-color:#ffc107; } a.update:hover { background-color:#e0a800; }
        a.delete { background-color:#dc3545; } a.delete:hover { background-color:#c82333; }
        a.manage { background-color:green; } a.manage:hover { background-color:#117a8b; }

        /* Status badges */
        .status-badge { padding: 5px 10px; border-radius: 5px; color: #fff; font-weight: bold; }
        .status-badge.available { background-color: #6c757d; } /* gray */
        .status-badge.pending   { background-color: #ffc107; } /* yellow */
        .status-badge.approved  { background-color: #28a745; } /* green */
        .status-badge.paid      { background-color: #007BFF; } /* blue */
    </style>
</head>
<body>
<div class="overlay">
<div class="container">
    <h1>💃Owner Dashboard💃<br> 🕺Welcome  <?php echo htmlspecialchars($_SESSION['name']); ?>🕺</h1>
    <a class="logout" href="../logout.php">Logout</a>
    <a class="add-house" href="add_house.php">Add New House</a>
    <a class='action-btn manage' href='owner_approve.php'>Manage Bookings</a>

    <h2>My Houses</h2>
    <table>
        <tr>
            <th>ID</th>
            <th>Title</th>
            <th>Location</th>
            <th>Price</th>
            <th>Status</th>
            <th>Image</th>
            <th>Actions</th>
        </tr>

        <?php
        // Fetch houses + booking status
        $stmt = $conn->prepare("
            SELECT h.*, 
                   b.status AS booking_status
            FROM houses h
            LEFT JOIN bookings b 
                ON h.id = b.house_id 
                AND b.status IN ('approved','paid') 
            WHERE h.owner_id=?
            ORDER BY h.id DESC
        ");
        $stmt->bind_param("i", $owner_id);
        $stmt->execute();
        $result = $stmt->get_result();

        if($result->num_rows > 0){
            while($row = $result->fetch_assoc()){
                echo "<tr>";
                echo "<td>".$row['id']."</td>";
                echo "<td>".htmlspecialchars($row['title'])."</td>";
                echo "<td>".htmlspecialchars($row['location'])."</td>";
                echo "<td>$".number_format($row['price'],2)."</td>";

                // Status logic with badges
                $status_text = $row['status']; // default house status
                $status_class = 'available';


if ($row['booking_status'] === 'approved') {
                    $status_text = 'approved';
                    $status_class = 'approved';
                } elseif ($row['booking_status'] === 'paid') {
                    $status_text = 'paid';
                    $status_class = 'paid';
                } elseif ($row['status'] === 'pending') {
                    $status_text = 'pending';
                    $status_class = 'pending';
                }

                echo "<td><span class='status-badge {$status_class}'>".$status_text."</span></td>";
                echo "<td><img class='house-img' src='../uploads/".$row['image']."' alt='House Image'></td>";
                echo "<td>
                        <a class='action-btn update' href='update_house.php?id=".$row['id']."'>Update</a>
                        <a class='action-btn delete' href='delete_house.php?id=".$row['id']."' onclick='return confirm(\"Are you sure?\")'>Delete</a>
                      </td>";
                echo "</tr>";
            }
        } else {
            echo "<tr><td colspan='7'>No houses found.</td></tr>";
        }
        ?>
    </table>
</div>
</div>
</body>
</html>