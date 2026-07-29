<?php
session_start();
if(!isset($_SESSION['id']) || $_SESSION['role']!="admin"){
    header("Location: ../login-admin.php");
    exit;
}
include '../config/db.php';

// Pagination setup for users
$limit = 10; // items per page
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$start = ($page - 1) * $limit;

// Search setup
$search = isset($_GET['search']) ? $_GET['search'] : '';

// Approve user action
if(isset($_GET['approve'])){
    $id = intval($_GET['approve']);
    $conn->query("UPDATE users SET approved=1 WHERE id=$id");
    header("Location: dashboard.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Admin Dashboard</title>
<style>
body {
    font-family: Arial, sans-serif;
    background: url('https://images.unsplash.com/photo-1505843513577-22bb7d21e455?fm=jpg&q=60&w=3000&auto=format&fit=crop&ixlib=rb-4.1.0&ixid=M3wxMjA3fDB8MHxzZWFyY2h8Mnx8YmlnJTIwaG91c2V8ZW58MHx8MHx8fDA%3D') no-repeat center center fixed;
    background-size: cover;
    margin:0;
}
.container {
    background: rgba(255,255,255,0.95);
    margin:30px auto;
    padding:20px;
    border-radius:10px;
    width:95%;
    max-width:1300px;
}
h1, h2 { text-align:center; color:#333; }
table {
    width:100%;
    border-collapse: collapse;
    margin-top:20px;
    background:white;
    box-shadow:0 5px 15px rgba(0,0,0,0.1);
}
th, td {
    border:1px solid #ccc;
    padding:10px;
    text-align:center;
}
th { background-color:#007BFF; color:white; }
tr:nth-child(even) { background-color:#f2f2f2; }
a { text-decoration:none; color:#007BFF; }
a:hover { text-decoration:underline; }
a.button{
    padding:6px 12px;
    background:green;
    color:white;
    border-radius:5px;
}
a.button:hover{ background:darkgreen; }
.logout{
    display:inline-block;
    margin-top:15px;
    padding:8px 15px;
    background:green;
    color:white;
    border-radius:5px;
}
input[type="text"]{ padding:8px; width:200px; margin-bottom:10px; border-radius:5px; border:1px solid #ccc; }
button{ padding:8px 12px; border:none; border-radius:5px; background-color:#007BFF; color:white; cursor:pointer; }
button:hover{ background-color:#0056b3; }
.pagination a{ margin:0 5px; text-decoration:none; color:#007BFF; }
.pagination a.active{ font-weight:bold; color:#000; }
</style>
</head>
<body>
<div class="container">
<h1>🤵-----Welcome Admin-----🤵</h1>

<!-- Users List with search & pagination -->
<h2>Users List</h2>
<form method="GET">
    <input type="text" name="search" placeholder="Search by name or email..." value="<?php echo htmlspecialchars($search); ?>">
    <button type="submit">Search</button>
</form>
<table>
<tr>
<th>ID</th><th>Name</th><th>Email</th><th>Role</th><th>Registered At</th><th>Status</th><th>Action</th>
</tr>
<?php
$sql = "SELECT * FROM users WHERE (name LIKE ? OR email LIKE ?) ORDER BY id DESC LIMIT $start, $limit";
$stmt = $conn->prepare($sql);
$search_param = "%$search%";
$stmt->bind_param("ss", $search_param, $search_param);
$stmt->execute();
$result = $stmt->get_result();
if($result->num_rows > 0){
    while($row = $result->fetch_assoc()){
        $approved = $row['approved'] ? "Approved" : "Pending";
        echo "<tr>";
        echo "<td>".$row['id']."</td>";
        echo "<td>".$row['name']."</td>";
        echo "<td>".$row['email']."</td>";
        echo "<td>".$row['role']."</td>";
        echo "<td>".$row['registration_date']."</td>";
        echo "<td>$approved</td>";
        echo "<td>";
        if(!$row['approved']){
            echo "<a href='?approve=".$row['id']."' class='button'>Approve</a>";
        }
        echo "</td>";
        echo "</tr>";
    }
}else{
    echo "<tr><td colspan='7'>No users found.</td></tr>";
}
?>
</table>

<?php
// Pagination links
$total_result = $conn->query("SELECT COUNT(*) AS total FROM users WHERE name LIKE '%$search%' OR email LIKE '%$search%'");
$total_row = $total_result->fetch_assoc();
$total_pages = ceil($total_row['total'] / $limit);
if($total_pages > 1){
    echo '<div class="pagination">';
    for($i=1;$i<=$total_pages;$i++){
        $active = ($i == $page) ? 'active' : '';
        echo "<a class='$active' href='?page=$i&search=".urlencode($search)."'>$i</a>";
    }
    echo '</div>';
}
?>
<!-- Houses List -->
<h2>Houses List</h2>
<table>
<tr><th>ID</th><th>Title</th><th>Location</th><th>Owner ID</th><th>Price</th><th>Status</th></tr>
<?php
$sql2 = "SELECT * FROM houses ORDER BY id DESC";
$result2 = $conn->query($sql2);
if($result2->num_rows > 0){
    while($row = $result2->fetch_assoc()){
        echo "<tr>";
        echo "<td>".$row['id']."</td>";
        echo "<td>".$row['title']."</td>";
        echo "<td>".$row['location']."</td>";
        echo "<td>".$row['owner_id']."</td>";
        echo "<td>".$row['price']."</td>";
        echo "<td>".$row['status']."</td>";
        echo "</tr>";
    }
}else{
    echo "<tr><td colspan='6'>No houses found.</td></tr>";
}
?>
</table>

<!-- Bookings List -->
<h2>Bookings List</h2>
<table>
<tr><th>Booking ID</th><th>House ID</th><th>Tenant ID</th><th>Status</th><th>Booking Date</th><th>Return Date</th></tr>
<?php
$sql3 = "SELECT * FROM bookings ORDER BY booking_date DESC";
$result3 = $conn->query($sql3);
if($result3->num_rows > 0){
    while($row = $result3->fetch_assoc()){
        echo "<tr>";
        echo "<td>".$row['id']."</td>";
        echo "<td>".$row['house_id']."</td>";
        echo "<td>".$row['tenant_id']."</td>";
        echo "<td>".$row['status']."</td>";
        echo "<td>".$row['booking_date']."</td>";
        echo "<td>".$row['return_date']."</td>";
        echo "</tr>";
    }
}else{
    echo "<tr><td colspan='6'>No bookings found.</td></tr>";
}
?>
</table>

<a href="../logout.php" class="logout">Logout</a>
</div>
</body>
</html>