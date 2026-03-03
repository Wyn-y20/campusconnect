<?php
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
    $role = "admin";

    // Backend validation (IMPORTANT for security)
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
        $check = $conn->prepare("SELECT id FROM users WHERE email=?");
        $check->bind_param("s", $email);
        $check->execute();
        $check->store_result();

        if($check->num_rows > 0) {
            $error = "Email already exists.";
        }
        $check->close();
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
                $mail->Subject = 'Verify Your Admin Account - CampusConnect';
                $mail->Body = "
                    <h3>Welcome Admin!</h3>
                    <p>Please click below to verify your admin account:</p>
                    <a href='$verification_link'>Verify Admin Account</a>
                ";

                $mail->send();

                $success = "Registration successful! Please check your email to verify your admin account.";

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

<?php include("auth_header.php"); ?>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

<style>
.password-wrapper {
    position: relative;
    margin-top: 15px;
}
.password-wrapper input {
    width: 100%;
    padding-right: 45px;
}
.password-wrapper i {
    position: absolute;
    right: 15px;
    top: 50%;
    transform: translateY(-50%);
    cursor: pointer;
    color: #1565c0;
}

.password-rules {
    display: none;
    background: #fff3f3;
    border: 1px solid #ffcccc;
    padding: 10px;
    border-radius: 8px;
    margin-bottom: 15px;
    font-size: 14px;
}

.password-rules p {
    margin: 5px 0;
    color: #c62828;
}

.password-rules p.valid {
    color: green;
}

button:disabled {
    background: #999 !important;
    cursor: not-allowed;
}
</style>

<div class="auth-card">
    <h2>Admin Registration</h2>

    <?php if(!empty($error)) echo "<div style='color:red;text-align:center;margin-bottom:15px;'>$error</div>"; ?>
    <?php if(!empty($success)) echo "<div style='color:green;text-align:center;margin-bottom:15px;'>$success</div>"; ?>

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

        <button type="submit" name="register" id="registerBtn" disabled style="margin-top:20px;">
            REGISTER
        </button>

    </form>

    <div class="auth-bottom">
        Already have an account?
        <a href="admin_login.php">Log in</a>
    </div>
</div>

<script>
// Eye Toggle
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

// Live Validation
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

<?php include("auth_footer.php"); ?>