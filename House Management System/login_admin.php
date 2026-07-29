<?php
session_start();
include 'config/db.php';

$error = '';
$role_type = 'admin'; // default role

if(isset($_POST['login'])){
    $role_type = $_POST['role_type'];
    $email = $_POST['email'];
    $password = $_POST['password'];

    $stmt = $conn->prepare("SELECT * FROM users WHERE email=? AND role=?");
    $stmt->bind_param("ss", $email, $role_type);
    $stmt->execute();
    $result = $stmt->get_result();

    if($result->num_rows == 1){
        $user = $result->fetch_assoc();
        if($user['approved'] == 0){
            $error = "⏳ Your account is waiting for Admin approval!";
        } else if(password_verify($password, $user['password'])){
            $_SESSION['id'] = $user['id'];
            $_SESSION['role'] = $user['role'];
            $_SESSION['name'] = $user['name'];

            switch($user['role']){
                case 'admin': header("Location: admin/dashboard.php"); break;
                case 'owner': header("Location: owner/dashboard.php"); break;
                case 'tenant': header("Location: tenant/dashboard.php"); break;
            }
            exit;
        } else {
            $error = "❌ Incorrect password!";
        }
    } else {
        $error = "❌ Email not found!";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Login - House Management</title>
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
    width: 400px;
}
.nav-tabs .nav-link.active {
    background-color: #007BFF;
    color: white;
    border-radius: 20px 20px 0 0;
}
.alert{
    border-radius: 10px;
}
</style>
</head>
<body>
<div class="card p-4">
    <h3 class="text-center mb-4">🏠 House Management</h3>

    <ul class="nav nav-tabs mb-3" id="roleTabs" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link active" id="admin-tab" data-role="admin">Admin</button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="owner-tab" data-role="owner">Owner</button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="tenant-tab" data-role="tenant">Tenant</button>
        </li>
    </ul>

    <?php if($error) echo "<div class='alert alert-danger'>$error</div>"; ?>

    <form method="POST">
        <input type="hidden" name="role_type" id="role_type" value="admin">
        <div class="mb-3">
            <input type="email" name="email" class="form-control" placeholder="Admin Email" required>
        </div>
        <div class="mb-3">
            <input type="password" name="password" class="form-control" placeholder="Password" required>
        </div>
        <button type="submit" name="login" id="loginBtn" class="btn btn-primary w-100">Login as Admin</button>
   

    </form>
    <div>
        Already have account? <a href="register.php">Register</a>
</div>

</div>

<script>
const tabs = document.querySelectorAll('.nav-link');
const roleInput = document.getElementById('role_type');
const emailInput = document.querySelector('input[name="email"]');
const loginBtn = document.getElementById('loginBtn');

tabs.forEach(tab => {
    tab.addEventListener('click', () => {
        tabs.forEach(t => t.classList.remove('active'));
        tab.classList.add('active');
        let role = tab.getAttribute('data-role');
        roleInput.value = role;

        // update placeholders & button text
        switch(role){
            case 'admin':
                emailInput.placeholder = 'Admin Email';
                loginBtn.textContent = 'Login as Admin';
                loginBtn.className = 'btn btn-primary w-100';
                break;
            case 'owner':
                emailInput.placeholder = 'Owner Email';
                loginBtn.textContent = 'Login as Owner';
                loginBtn.className = 'btn btn-success w-100';
                break;
            case 'tenant':
                emailInput.placeholder = 'Tenant Email';
                loginBtn.textContent = 'Login as Tenant';
                loginBtn.className = 'btn btn-warning w-100';
                break;
        }
    });
});
</script>

</body>
</html>