
<?php
include 'config/db.php';

if(isset($_POST['register'])){
    $name = $_POST['name'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $role = $_POST['role'];

    $stmt = $conn->prepare("INSERT INTO users(name,email,password,role,approved) VALUES (?,?,?,?,0)");
    $stmt->bind_param("ssss",$name,$email,$password,$role);

    if($stmt->execute()){
        $success = "✅ Registration Successful! Waiting for Admin approval.";
    } else {
        $error = "❌ Email already exists!";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
<title>Register</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

<style>
body{
     background: url('images/b12.webp') no-repeat center center fixed;
    background-size: 1550px 800px;
    height:100vh;
    display:flex;
    justify-content:center;
    align-items:center;
}
.card{
    border-radius:20px;
    box-shadow:0 10px 30px rgba(0,0,0,0.3);
}
</style>
</head>
<body>

<div class="card p-4" style="width:450px;">
    <h3 class="text-center mb-4">🏠 Create Account</h3>

    <?php 
    if(isset($error)) echo "<div class='alert alert-danger'>$error</div>";
    if(isset($success)) echo "<div class='alert alert-success'>$success</div>";
    ?>

    <form method="POST">
        <p><i>Enter your name</i></p>
        <input type="text" name="name" class="form-control mb-3" placeholder="Full Name" required>
        <p><i>Enter your email</i></p>
        <input type="email" name="email" class="form-control mb-3" placeholder="Email Address" required>
        <p><i>Enter your password</i></p>
        <input type="password" name="password" class="form-control mb-3" placeholder="Password" required>

        <select name="role" class="form-select mb-3" required>
            <option value="">-- Select Role --</option>
            <option value="owner">Owner</option>
            <option value="tenant">Tenant</option>
        </select>

        <button name="register" class="btn btn-dark w-100">Register</button>
    </form>

    <div class="text-center mt-3">
        Already have account? <a href="login_admin.php">Login</a>
    </div>
</div>

</body>
</html>