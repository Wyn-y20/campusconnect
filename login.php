<?php
session_start();
include("config.php");

if(isset($_POST['login'])) {

    $email = $_POST['email'];
    $password = $_POST['password'];

    $sql = "SELECT * FROM users WHERE email='$email' AND role='student'";
    $result = mysqli_query($conn, $sql);

    if(mysqli_num_rows($result) == 1) {
        $row = mysqli_fetch_assoc($result);

        if(password_verify($password, $row['password'])) {
            $_SESSION['user_id'] = $row['id'];
            $_SESSION['name'] = $row['name'];
            $_SESSION['role'] = "student";

            header("Location: student_dashboard.php");
            exit();
        } else {
            $error = "Invalid password!";
        }
    } else {
        $error = "Student not found!";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
<title>Student Login</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<style>
body {
    margin:0;
    height:100vh;
    display:flex;
    justify-content:center;
    align-items:center;
    font-family:Arial;
    background:linear-gradient(135deg,#6ec6ff,#0d47a1);
}
.card {
    background:white;
    padding:40px;
    width:380px;
    border-radius:20px;
    box-shadow:0 15px 30px rgba(0,0,0,0.3);
}
h2{text-align:center;color:#0d47a1;margin-bottom:25px;}
input{
    width:100%;
    padding:12px;
    margin-bottom:15px;
    border-radius:10px;
    border:1px solid #ccc;
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
button:hover{background:#0d47a1;}
.error{color:red;text-align:center;margin-bottom:10px;}
.bottom{text-align:center;margin-top:10px;}
.bottom a{color:#1565c0;font-weight:bold;text-decoration:none;}
</style>
</head>
<body>

<div class="card">
<h2>Student Login</h2>

<?php if(isset($error)) echo "<div class='error'>$error</div>"; ?>

<form method="POST">
<input type="email" name="email" placeholder="E-mail" required>
<input type="password" name="password" placeholder="Password" required>
<button type="submit" name="login">LOGIN</button>
</form>

<div class="bottom">
Don't have an account? <a href="register.php">Register</a>
</div>

</div>
</body>
</html>
