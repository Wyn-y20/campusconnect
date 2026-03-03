<?php
include("config.php");

$error = "";
$success = "";
$valid = false;

if(isset($_GET['token'])){
    $token = $_GET['token'];

    $stmt = $conn->prepare("SELECT id FROM users WHERE reset_token=? AND reset_token_expiry > NOW()");
    $stmt->bind_param("s", $token);
    $stmt->execute();
    $result = $stmt->get_result();

    if($result->num_rows == 1){
        $valid = true;
    } else {
        $error = "Invalid or expired reset link.";
    }

    $stmt->close();
}

if(isset($_POST['reset'])){

    $token = $_POST['token'];
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    // Password validation
    if($password !== $confirm_password ||
       !preg_match('/[A-Z]/', $password) ||
       !preg_match('/[a-z]/', $password) ||
       !preg_match('/[0-9]/', $password) ||
       !preg_match('/[\W]/', $password) ||
       strlen($password) < 8
    ){
        $error = "Password must meet required criteria.";
        $valid = true;
    } else {

        $hashed = password_hash($password, PASSWORD_DEFAULT);

        $update = $conn->prepare("UPDATE users 
                                  SET password=?, reset_token=NULL, reset_token_expiry=NULL 
                                  WHERE reset_token=?");
        $update->bind_param("ss", $hashed, $token);
        $update->execute();

        $success = "Password updated successfully. You can now login.";
        $valid = false;
        $update->close();
    }
}
?>

<!DOCTYPE html>
<html>
<head>
<title>Reset Password</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">

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
    margin-bottom:15px;
}
.success{
    color:green;
    text-align:center;
    margin-bottom:15px;
}
.password-rules{
    display:none;
    background:#fff3f3;
    border:1px solid #ffcccc;
    padding:10px;
    border-radius:8px;
    margin-bottom:15px;
    font-size:14px;
    color:#c62828;
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
</style>
</head>
<body>

<div class="card">
<h2>Reset Password</h2>

<?php if(!empty($error)) echo "<div class='error'>$error</div>"; ?>
<?php if(!empty($success)) echo "<div class='success'>$success</div>"; ?>

<?php if($valid): ?>

<form method="POST">

<input type="hidden" name="token" value="<?php echo htmlspecialchars($_GET['token']); ?>">

<input type="password" id="password" name="password" placeholder="New Password" required>

<div id="password-rules" class="password-rules">
    • At least 8 characters<br>
    • One uppercase letter<br>
    • One lowercase letter<br>
    • One number<br>
    • One special character
</div>

<input type="password" name="confirm_password" placeholder="Confirm Password" required>

<button type="submit" name="reset">RESET PASSWORD</button>

</form>

<?php endif; ?>

<div class="bottom">
<a href="login.php">Back to Login</a>
</div>

</div>

<script>
const passwordInput = document.getElementById("password");
const rules = document.getElementById("password-rules");

if(passwordInput){
    passwordInput.addEventListener("focus", function() {
        rules.style.display = "block";
    });

    passwordInput.addEventListener("blur", function() {
        rules.style.display = "none";
    });
}
</script>

</body>
</html>