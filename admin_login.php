<?php
session_start();
include("config.php");

$error = "";

if(isset($_POST['login'])) {

    $email = trim($_POST['email']);
    $password = $_POST['password'];

    $stmt = $conn->prepare("SELECT * FROM users WHERE email=? AND role='admin'");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if($result->num_rows === 1){

        $row = $result->fetch_assoc();

        if(password_verify($password, $row['password'])){

            if($row['is_verified'] == 0){
                $error = "Please verify your email before logging in.";
            } else {

                $_SESSION['user_id'] = $row['id'];
                $_SESSION['name'] = $row['fullname'];
                $_SESSION['role'] = $row['role'];

                header("Location: admin_dashboard.php");
                exit();
            }

        } else {
            $error = "Invalid password!";
        }

    } else {
        $error = "Admin not found!";
    }

    $stmt->close();
}
?>

<!DOCTYPE html>
<html>
<head>
<title>Admin Login</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

<style>
body{
    margin:0;
    height:100vh;
    display:flex;
    justify-content:center;
    align-items:center;
    font-family:Arial;
    background:linear-gradient(135deg,#6ec6ff,#0d47a1);
}
.card{
    background:white;
    padding:40px;
    width:380px;
    border-radius:20px;
    box-shadow:0 15px 30px rgba(0,0,0,0.3);
}
h2{
    text-align:center;
    color:#0d47a1;
    margin-bottom:25px;
}
input{
    width:100%;
    padding:12px;
    margin-bottom:15px;
    border-radius:10px;
    border:1px solid #ccc;
}
.password-wrapper{
    position:relative;
}
.password-wrapper input{
    padding-right:45px;
}
.password-wrapper i{
    position:absolute;
    right:15px;
    top:50%;
    transform:translateY(-50%);
    cursor:pointer;
    color:#1565c0;
}
button{
    width:100%;
    padding:12px;
    background:#1565c0;
    color:white;
    border:none;
    border-radius:10px;
    cursor:pointer;
}
button:hover{
    background:#0d47a1;
}
.error{
    color:red;
    text-align:center;
    margin-bottom:10px;
}
.bottom{
    text-align:center;
    margin-top:10px;
}
.bottom a{
    color:#1565c0;
    font-weight:bold;
    text-decoration:none;
}

/* Forgot Password Styling */
.forgot{
    text-align:right;
    margin-bottom:15px;
}
.forgot a{
    font-size:14px;
    color:#1565c0;
    text-decoration:none;
}
.forgot a:hover{
    text-decoration:underline;
}
</style>
</head>

<body>

<div class="card">
<h2>Admin Login</h2>

<?php if(!empty($error)) echo "<div class='error'>$error</div>"; ?>

<form method="POST">

<input type="email" name="email" placeholder="E-mail" required>

<div class="password-wrapper">
    <input type="password" id="password" name="password" placeholder="Password" required>
    <i class="fa-solid fa-eye" onclick="togglePassword(this)"></i>
</div>

<div class="forgot">
    <a href="forgot_password.php">Forgot Password?</a>
</div>

<button type="submit" name="login">LOGIN</button>

</form>

<div class="bottom">
No account? <a href="admin_register.php">Register</a>
</div>

</div>

<script>
function togglePassword(icon) {
    var field = document.getElementById("password");

    if (field.type === "password") {
        field.type = "text";
        icon.classList.remove("fa-eye");
        icon.classList.add("fa-eye-slash");
    } else {
        field.type = "password";
        icon.classList.remove("fa-eye-slash");
        icon.classList.add("fa-eye");
    }
}
</script>

</body>
</html>