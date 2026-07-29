<?php
session_start();

// Redirect logged-in users
if(isset($_SESSION['id'])){
    switch($_SESSION['role']){
        case 'owner':
            header("Location: owner/dashboard.php"); exit;
        case 'tenant':
            header("Location: tenant/dashboard.php"); exit;
        case 'admin':
            header("Location: admin/dashboard.php"); exit;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Welcome - Online House Management System</title>
    <style>
        /* General Body */
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background: url('images/b14.jpeg') no-repeat center center fixed;
            background-size: 1550px 800px;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        /* Overlay */
        .overlay {
            background: rgba(0, 0, 0, 0.5);
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        /* Main container */
        .container {
            background: rgba(241, 235, 237, 0.94);
            padding: 0px 30px;
            width: 400px;
            border-radius: 15px;
            text-align: center;
            box-shadow: 0px 0px 25px #00000080;
        }

        /* Headings */
        h1 {
            color: #120101;
            margin-bottom: 10px;
        }
        p {
            color: #0c0101;
            margin-bottom: 30px;
        }

        /* Buttons */
        a {
            display: block;
            margin: 15px 0;
            padding: 12px;
            text-decoration: none;
            color: #060101;
            background-color: #007BFF;
            border-radius: 5px;
            transition: background-color 0.3s, transform 0.2s;
        }
        a:hover {
            background-color: #0056b3;
            transform: translateY(-2px);
        }

        /* Contact Section */
        .contact {
            margin-top: 30px;
            background: rgba(255,255,255,0.1);
            padding: 15px;
            border-radius: 10px;
            color: #0c0101;
            font-size: 14px;
        }

        .contact h3 {
            margin: 0 0 5px;
            font-size: 16px;
            text-decoration: none;
        }

        /* Footer */
        footer {
            text-align: center;
            padding: 10px 0;
            background: #0b3422;
            color: #fff;
            font-size: 13px;
            margin-top:auto;
        }

        /* Responsive */
        @media(max-width: 500px){
            .container { width: 90%; padding: 30px 20px; }
        }
    </style>
</head>
<body>
    <div class="overlay">
        <div class="container">
            <h1>🏠 Welcome to Online House Rental Management System</h1>
            <p>Please login or register to continue</p>
            <a href="login_admin.php">Login</a>
            <a href="register.php">Register</a>
<div class="contact" >
                <h3>☎️Contact Us</h3>
                <p>✉️ phyothinzarhlaiing392@gmail.com </p>
                <p>📞 +95 9 760 671 311</p>
                <p>Address: TaOhn, Sintgaing, Mandalay</p>
        </div>
    </div>
</div>

    <footer>
        &copy; <?php echo date('Y'); ?> Online House Management System. All Rights Reserved.
    </footer>

</body>
</html>