<?php
session_start();
if(!isset($_SESSION['id']) || $_SESSION['role'] != "owner"){
    header("Location: ../login_admin.php");
    exit;
}

include '../config/db.php';

$upload_dir = "../uploads/";
if(!is_dir($upload_dir)){
    mkdir($upload_dir, 0777, true);
}

if(isset($_POST['add'])){
    $title = $_POST['title'];
    $location = $_POST['location'];
    $price = $_POST['price'];
    $status = $_POST['status'];

    if(isset($_FILES['image']) && $_FILES['image']['error'] == 0){
        $image = $_FILES['image']['name'];
        $tmp_name = $_FILES['image']['tmp_name'];
        $target_file = $upload_dir . basename($image);

        if(move_uploaded_file($tmp_name, $target_file)){
            $owner_id = $_SESSION['id'];
            $stmt = $conn->prepare("INSERT INTO houses(owner_id,title,location,price,status,image) VALUES(?,?,?,?,?,?)");
            $stmt->bind_param("issdss", $owner_id, $title, $location, $price, $status, $image);
            $stmt->execute();

            // Redirect to dashboard after adding
            header("Location: dashboard.php");
            exit;
        } else {
            $error = "Failed to upload image!";
        }
    } else {
        $error = "Please select an image!";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Add House - Online House Management System</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background: url('https://png.pngtree.com/thumb_back/fh260/background/20241115/pngtree-a-lovely-house-with-contrasting-red-and-white-roofs-sitting-in-image_16585799.jpg') no-repeat center center fixed;
            background-size: 1550px 800px;
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
        button {
            width: 95%;
            padding: 10px;
            margin-top: 15px;
            border: none;
            border-radius: 5px;
            background-color: #28a745;
            color: #fff;
            cursor: pointer;
            font-size: 16px;
        }
        button:hover {
            background-color: #218838;
        }
        .error {
            color: red;
            margin-bottom: 10px;
        }
        a {
            display: block;
            margin-top: 15px;
            color: #007BFF;
            text-decoration: none;
        }
        a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="overlay">
        <div class="container">
            <h1><i>🏠Add New House🏠</i></h1>
            <?php if(isset($error)) { echo '<div class="error">'.$error.'</div>'; } ?>
            <form method="POST" enctype="multipart/form-data">
                <input type="text" name="title" placeholder="Title" required><br>
                <input type="text" name="location" placeholder="Location" required><br>
                <input type="number" step="0.01" name="price" placeholder="Price" required><br>
                <select name="status" required>
                    <option value="available">Available</option>
                    <option value="booked">Booked</option>
                </select><br>
                <input type="file" name="image" required><br>
                <button name="add">Add House</button>
            </form>
            <a href="dashboard.php">Back to Dashboard</a>
        </div>
    </div>
</body>
</html>