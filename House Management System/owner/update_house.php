<?php
session_start();
if(!isset($_SESSION['id']) || $_SESSION['role'] != "owner"){
    header("Location: ../login.php");
    exit;
}

include '../config/db.php';

$upload_dir = "../uploads/";
if(!is_dir($upload_dir)){
    mkdir($upload_dir, 0777, true);
}

// Get house ID
if(!isset($_GET['id'])){
    header("Location: dashboard.php");
    exit;
}

$house_id = $_GET['id'];
$owner_id = $_SESSION['id'];

// Fetch existing house data
$stmt = $conn->prepare("SELECT * FROM houses WHERE id=? AND owner_id=?");
$stmt->bind_param("ii", $house_id, $owner_id);
$stmt->execute();
$result = $stmt->get_result();

if($result->num_rows != 1){
    header("Location: dashboard.php");
    exit;
}

$house = $result->fetch_assoc();

if(isset($_POST['update'])){
    $title = $_POST['title'];
    $location = $_POST['location'];
    $price = $_POST['price'];
    $status = $_POST['status'];
    $image = $house['image']; // default existing image

    // Check if new image uploaded
    if(isset($_FILES['image']) && $_FILES['image']['error'] == 0){
        $image = $_FILES['image']['name'];
        $tmp_name = $_FILES['image']['tmp_name'];
        move_uploaded_file($tmp_name, $upload_dir . $image);
    }

    $stmt_update = $conn->prepare("UPDATE houses SET title=?, location=?, price=?, status=?, image=? WHERE id=? AND owner_id=?");
    $stmt_update->bind_param("ssdssii", $title, $location, $price, $status, $image, $house_id, $owner_id);
    $stmt_update->execute();

    header("Location: dashboard.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Update House - Online House Management System</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background: url('../images/background.jpg') no-repeat center center fixed;
            background-size: cover;
            min-height: 100vh;
        }
        .overlay {
            background: rgba(0,0,0,0.5);
            width: 100%;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .container {
            background: rgba(255,255,255,0.95);
            padding: 30px;
            border-radius: 10px;
            width: 400px;
            box-shadow: 0px 0px 20px #00000080;
            text-align: center;
        }
        h1 {
            margin-bottom: 20px;
            color: #333;
        }
        input[type="text"], input[type="number"], select, input[type="file"] {
            width: 90%;
            padding: 10px;
            margin: 10px 0;
            border-radius: 5px;
            border: 1px solid #ccc;
        }
        img.preview-img {
            width: 150px;
            height: 100px;
            object-fit: cover;
            margin-bottom: 10px;
            border-radius: 5px;
        }
        button {
            width: 95%;
            padding: 10px;
            margin-top: 15px;
            border: none;
            border-radius: 5px;
            background-color: #ffc107;
            color: #fff;
            cursor: pointer;
            font-size: 16px;
        }
        button:hover {
            background-color: #e0a800;
        }
        a {
            display: block;
            margin-top: 15px;
            color: #007BFF;
            text-decoration: none;
        }
        a:hover { text-decoration: underline; }
    </style>
</head>
<body>
    <div class="overlay">
        <div class="container">
            <h1>Update House</h1>
            <form method="POST" enctype="multipart/form-data">
                <input type="text" name="title" value="<?php echo $house['title']; ?>" required><br>
                <input type="text" name="location" value="<?php echo $house['location']; ?>" required><br>
                <input type="number" step="0.01" name="price" value="<?php echo $house['price']; ?>" required><br>
                <select name="status" required>
                    <option value="available" <?php if($house['status']=='available') echo 'selected'; ?>>Available</option>
                    <option value="booked" <?php if($house['status']=='booked') echo 'selected'; ?>>Booked</option>
                </select><br>
                <img class="preview-img" src="../uploads/<?php echo $house['image']; ?>" alt="Current Image"><br>
                <input type="file" name="image"><br>
                <button name="update">Update House</button>
            </form>
            <a href="dashboard.php">Back to Dashboard</a>
        </div>
    </div>
</body>
</html>