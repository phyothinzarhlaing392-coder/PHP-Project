<?php
session_start();
if(!isset($_SESSION['id']) || $_SESSION['role'] != "tenant"){
    header("Location: ../login_admin.php");
    exit;
}

include '../config/db.php';

// ✅ Only fetch houses that are available
$query = "SELECT * FROM houses WHERE status!='paid' ORDER BY id";
$result = $conn->query($query);
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Tenant Dashboard</title>
<style>
body {
    font-family: 'Arial', sans-serif;
    background: url('https://t3.ftcdn.net/jpg/06/37/91/58/360_F_637915894_xioRfH3tMwJ7EopQqoGCe5dkzjPYMJLx.jpg') no-repeat center center fixed;
    background-size: cover;
    margin: 0;
    padding: 0;
}
.header{
    text-align:center;
    background: rgba(30, 117, 66, 0.5);
    color: #fff;
}
.overlay {
    background: rgba(0,0,0,0.5);
    width: 100%;
    min-height: 100vh;
    padding: 20px;
}
h1 {
    color: #fff;
    text-align: center;
    margin-bottom: 20px;
}
nav {
    text-align: center;
    margin-bottom: 30px;
}
nav a {
    display: inline-block;
    margin: 0 15px;
    color: #fff;
    font-weight: bold;
    text-decoration: none;
    padding: 8px 12px;
    border-radius: 5px;
    background: #007BFF;
}
nav a:hover { background: #0056b3; }

.house-grid {
    display: flex;
    flex-wrap: wrap;
    justify-content: center;
}

.house-card {
    background: #fff;
    width: 280px;
    margin: 15px;
    border-radius: 10px;
    overflow: hidden;
    box-shadow: 0 0 15px rgba(0,0,0,0.3);
    transition: transform 0.2s;
}
.house-card:hover { transform: scale(1.05); }
.house-card img { width: 100%; height: 180px; object-fit: cover; }

.house-card .info { padding: 15px; }
.house-card .info h3 { margin: 0 0 10px; color: #333; }
.house-card .info p { margin: 5px 0; color: #555; }
.house-card .info a {
    display: inline-block;
    margin-top: 10px;
    padding: 8px 12px;
    border-radius: 5px;
    text-decoration: none;
}
.available { background: #28a745; color: #fff; }
.available:hover { background: #218838; }
</style>
</head>
<body>
    <header class="header">
        “For more info, feel free to contact us via hotline  📞 +95 9 760 671 311”
    </header>
<div class="overlay">
<h1>💖💖Welcome Tenant💖💖<br><?php echo htmlspecialchars($_SESSION['name']); ?>!</h1>
<nav>
    <a href="booking_history.php">My Booking History</a>
    <a href="../logout.php">Logout</a>
</nav>

<div class="house-grid">
<?php while($house = $result->fetch_assoc()): ?>
    <div class="house-card">
        <img src="../uploads/<?php echo htmlspecialchars($house['image']); ?>" alt="House">
        <div class="info">
            <h3><?php echo htmlspecialchars($house['title']); ?></h3>
            <p>Location: <?php echo htmlspecialchars($house['location']); ?></p>
            <p>Price: $<?php echo number_format($house['price'],2); ?></p>
            <a href="book_house.php?house_id=<?php echo $house['id']; ?>" class="available">Book Now</a>
        </div>
    </div>
<?php endwhile; ?>
</div>
</div>
</body>
</html>