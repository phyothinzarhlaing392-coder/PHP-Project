<?php
include '../config/db.php';
session_start();

if(isset($_GET['approve'])){
    $id = $_GET['approve'];
    $stmt = $conn->prepare("UPDATE users SET approved=1 WHERE id=?");
    $stmt->bind_param("i",$id);
    $stmt->execute();
    header("Location:dashboard.php");
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Approve Users</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;500;600&display=swap" rel="stylesheet">
    <style>
        body{
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(135deg,#667eea,#764ba2);
            margin:0;
            padding:0;
        }
        .container{
            width:85%;
            margin:50px auto;
            background:white;
            padding:30px;
            border-radius:15px;
            box-shadow:0 15px 35px rgba(0,0,0,0.2);
        }
        h2{
            text-align:center;
            margin-bottom:30px;
            color:#333;
        }
        table{
            width:100%;
            border-collapse:collapse;
        }
        th{
            background:#667eea;
            color:white;
            padding:12px;
        }
        td{
            padding:12px;
            text-align:center;
            border-bottom:1px solid #ddd;
        }
        tr:hover{
            background:#f2f2f2;
        }
        .approve-btn{
            padding:8px 15px;
            background:#28a745;
            color:white;
            border:none;
            border-radius:20px;
            text-decoration:none;
            font-size:14px;
        }
        .approve-btn:hover{
            background:#218838;
        }
        .no-data{
            text-align:center;
            padding:20px;
            color:gray;
        }
    </style>
</head>
<body>

<div class="container">
    <h2>Pending User Approvals</h2>

    <table>
        <tr>
            <th>Name</th>
            <th>Email</th>
            <th>Role</th>
            <th>Action</th>
        </tr>

        <?php
        $result = $conn->query("SELECT * FROM users WHERE approved=0");

        if($result->num_rows > 0){
            while($row = $result->fetch_assoc()){
        ?>
        <tr>
            <td><?php echo $row['name']; ?></td>
            <td><?php echo $row['email']; ?></td>
            <td><?php echo ucfirst($row['role']); ?></td>
            <td>
                <a class="approve-btn" href="?approve=<?php echo $row['id']; ?>">Approve</a>
            </td>
        </tr>
        <?php } } else { ?>
        <tr>
            <td colspan="4" class="no-data">No Pending Users 🎉</td>
        </tr>
        <?php } ?>
    </table>
</div>

</body>
</html>