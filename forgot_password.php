<?php
session_start();
include("config.php");
include("smtp_config.php");

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'PHPMailer/src/Exception.php';
require 'PHPMailer/src/PHPMailer.php';
require 'PHPMailer/src/SMTP.php';

$message = "";
$success = false;

if(isset($_POST['submit'])){

    $email = trim($_POST['email']);

    $stmt = $conn->prepare("SELECT id FROM users WHERE email=?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if($result->num_rows == 1){

        $token = bin2hex(random_bytes(32));
        $expiry = date("Y-m-d H:i:s", strtotime("+30 minutes"));

        $update = $conn->prepare("UPDATE users SET reset_token=?, reset_token_expiry=? WHERE email=?");
        $update->bind_param("sss", $token, $expiry, $email);
        $update->execute();

        $reset_link = "http://localhost/campusconnect/reset_password.php?token=".$token;

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
            $mail->Subject = 'Reset Your Password - CampusConnect';
            $mail->Body = "
                <h3>Password Reset Request</h3>
                <p>Click below to reset your password:</p>
                <a href='$reset_link'>Reset Password</a>
                <p>This link expires in 30 minutes.</p>
            ";

            $mail->send();

            $message = "Reset link sent to your email.";
            $success = true;

        } catch (Exception $e) {
            $message = "Failed to send reset email.";
        }

    } else {
        $message = "Email not found.";
    }

    $stmt->close();
}
?>
<!DOCTYPE html>
<html>
<head>
<title>Forgot Password</title>
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
.message{
    text-align:center;
    margin-bottom:15px;
    color:<?php echo $success ? 'green' : 'red'; ?>;
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
<h2>Forgot Password</h2>

<?php if($message != "") echo "<div class='message'>$message</div>"; ?>

<form method="POST">
<input type="email" name="email" placeholder="Enter your email" required>
<button type="submit" name="submit">SEND RESET LINK</button>
</form>

<div class="bottom">
Back to <a href="login.php">Login</a>
</div>

</div>

</body>
</html>