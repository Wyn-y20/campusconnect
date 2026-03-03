<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();
include("config.php");
include("smtp_config.php");

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'PHPMailer/src/Exception.php';
require 'PHPMailer/src/PHPMailer.php';
require 'PHPMailer/src/SMTP.php';

$error = "";
$success = "";

if(isset($_POST['register'])) {

    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    $role = "student";

    // Backend validation (IMPORTANT)
    if($password !== $confirm_password ||
       !preg_match('/[A-Z]/', $password) ||
       !preg_match('/[a-z]/', $password) ||
       !preg_match('/[0-9]/', $password) ||
       !preg_match('/[\W]/', $password) ||
       strlen($password) < 8
    ){
        $error = "Password must meet required criteria.";
    }

    if(empty($error)) {
        $check_stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
        $check_stmt->bind_param("s", $email);
        $check_stmt->execute();
        $check_stmt->store_result();

        if($check_stmt->num_rows > 0) {
            $error = "Email already exists.";
        }

        $check_stmt->close();
    }

    if(empty($error)) {

        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        $verification_token = bin2hex(random_bytes(32));

        $stmt = $conn->prepare("
            INSERT INTO users 
            (fullname, email, password, role, is_verified, verification_token) 
            VALUES (?, ?, ?, ?, 0, ?)
        ");

        $stmt->bind_param("sssss", $name, $email, $hashed_password, $role, $verification_token);

        if($stmt->execute()) {

            $verification_link = "http://localhost/campusconnect/verify.php?token=" . $verification_token;

            $mail = new PHPMailer(true);

            try {
                $mail->isSMTP();
                $mail->Host = 'smtp.gmail.com';
                $mail->SMTPAuth = true;
                $mail->Username = SMTP_EMAIL;
                $mail->Password = SMTP_PASS;
                $mail->SMTPSecure = 'tls';
                $mail->Port = 587;

                $mail->setFrom(SMTP_EMAIL, 'CampusConnect');
                $mail->addAddress($email);

                $mail->isHTML(true);
                $mail->Subject = 'Verify Your Email - CampusConnect';
                $mail->Body = "
                    <h3>Welcome to CampusConnect!</h3>
                    <p>Please click below to verify your account:</p>
                    <a href='$verification_link'>Verify Account</a>
                ";

                $mail->send();

                $success = "Registration successful! Please check your email to verify your account.";

            } catch (Exception $e) {
                $error = "Email could not be sent.";
            }

        } else {
            $error = "Registration failed.";
        }

        $stmt->close();
    }
}
?>

<!DOCTYPE html>
<html>
<head>
<title>Student Registration</title>
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
button{
    width:100%;
    padding:12px;
    background:#1565c0;
    color:white;
    border:none;
    border-radius:10px;
    cursor:pointer;
}
button:disabled{
    background:#999 !important;
    cursor:not-allowed;
}
button:hover{
    background:#0d47a1;
}
.password-wrapper{
    position:relative;
}
.password-wrapper i{
    position:absolute;
    right:15px;
    top:50%;
    transform:translateY(-50%);
    cursor:pointer;
    color:#1565c0;
}
.password-rules{
    display:none;
    background:#fff3f3;
    border:1px solid #ffcccc;
    padding:10px;
    border-radius:8px;
    margin-bottom:15px;
    font-size:14px;
}
.password-rules p{
    margin:5px 0;
    color:#c62828;
}
.password-rules p.valid{
    color:green;
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
<h2>Student Registration</h2>

<?php if(!empty($error)) echo "<div class='error'>$error</div>"; ?>
<?php if(!empty($success)) echo "<div class='success'>$success</div>"; ?>

<form method="POST">

<input type="text" name="name" placeholder="Full Name *" required>
<input type="email" name="email" placeholder="E-mail *" required>

<div class="password-wrapper">
    <input type="password" id="password" name="password" placeholder="Password *" required>
    <i class="fa-solid fa-eye" onclick="togglePassword('password', this)"></i>
</div>

<div id="password-rules" class="password-rules">
    <p id="rule-length">• At least 8 characters</p>
    <p id="rule-upper">• One uppercase letter</p>
    <p id="rule-lower">• One lowercase letter</p>
    <p id="rule-number">• One number</p>
    <p id="rule-special">• One special character</p>
</div>

<div class="password-wrapper">
    <input type="password" id="confirm_password" name="confirm_password" placeholder="Confirm Password *" required>
    <i class="fa-solid fa-eye" onclick="togglePassword('confirm_password', this)"></i>
</div>

<button type="submit" name="register" id="registerBtn" disabled>REGISTER</button>

</form>

<div class="bottom">
Already have an account? <a href="login.php">Log in</a>
</div>

</div>

<script>
function togglePassword(fieldId, icon) {
    var field = document.getElementById(fieldId);
    if (field.type === "password") {
        field.type = "text";
        icon.classList.replace("fa-eye","fa-eye-slash");
    } else {
        field.type = "password";
        icon.classList.replace("fa-eye-slash","fa-eye");
    }
}

const passwordInput = document.getElementById("password");
const confirmInput = document.getElementById("confirm_password");
const registerBtn = document.getElementById("registerBtn");

const ruleLength = document.getElementById("rule-length");
const ruleUpper = document.getElementById("rule-upper");
const ruleLower = document.getElementById("rule-lower");
const ruleNumber = document.getElementById("rule-number");
const ruleSpecial = document.getElementById("rule-special");
const rulesBox = document.getElementById("password-rules");

passwordInput.addEventListener("focus", () => rulesBox.style.display = "block");
passwordInput.addEventListener("blur", () => rulesBox.style.display = "none");

function validatePassword(){
    const password = passwordInput.value;
    const confirm = confirmInput.value;
    let valid = true;

    if(password.length >= 8){ ruleLength.classList.add("valid"); }
    else { ruleLength.classList.remove("valid"); valid = false; }

    if(/[A-Z]/.test(password)){ ruleUpper.classList.add("valid"); }
    else { ruleUpper.classList.remove("valid"); valid = false; }

    if(/[a-z]/.test(password)){ ruleLower.classList.add("valid"); }
    else { ruleLower.classList.remove("valid"); valid = false; }

    if(/[0-9]/.test(password)){ ruleNumber.classList.add("valid"); }
    else { ruleNumber.classList.remove("valid"); valid = false; }

    if(/[\W]/.test(password)){ ruleSpecial.classList.add("valid"); }
    else { ruleSpecial.classList.remove("valid"); valid = false; }

    if(password !== confirm){ valid = false; }

    registerBtn.disabled = !valid;
}

passwordInput.addEventListener("input", validatePassword);
confirmInput.addEventListener("input", validatePassword);
</script>

</body>
</html>