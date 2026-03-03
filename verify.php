<?php
include("config.php");

$message = "";
$success = false;
$login_page = "login.php"; // default

if(isset($_GET['token'])) {

    $token = $_GET['token'];

    $stmt = $conn->prepare("SELECT id, role FROM users WHERE verification_token = ?");
    $stmt->bind_param("s", $token);
    $stmt->execute();
    $result = $stmt->get_result();

    if($result->num_rows > 0){

        $row = $result->fetch_assoc();
        $role = $row['role'];

        // Set correct login page
        if($role == "admin"){
            $login_page = "admin_login.php";
        } else {
            $login_page = "login.php";
        }

        $update = $conn->prepare("UPDATE users SET is_verified = 1, verification_token = NULL WHERE verification_token = ?");
        $update->bind_param("s", $token);
        $update->execute();

        $message = "Email verified successfully!";
        $success = true;

    } else {
        $message = "Invalid or expired verification link.";
    }

    $stmt->close();
}
?>

<!DOCTYPE html>
<html>
<head>
<title>Email Verification</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<?php if($success): ?>
<meta http-equiv="refresh" content="4;url=<?php echo $login_page; ?>">
<?php endif; ?>

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
    width:400px;
    border-radius:20px;
    box-shadow:0 15px 30px rgba(0,0,0,0.3);
    text-align:center;
}

h2{
    margin-bottom:20px;
}

.success{
    color:green;
    margin-bottom:20px;
}

.error{
    color:red;
    margin-bottom:20px;
}

button{
    padding:12px 20px;
    background:#1565c0;
    color:white;
    border:none;
    border-radius:10px;
    cursor:pointer;
}

button:hover{
    background:#0d47a1;
}
</style>
</head>

<body>

<div class="card">

<h2>Email Verification</h2>

<?php if($success): ?>
    <div class="success"><?php echo $message; ?></div>
    <p>You will be redirected shortly...</p>
    <a href="<?php echo $login_page; ?>">
        <button>Go to Login</button>
    </a>
<?php else: ?>
    <div class="error"><?php echo $message; ?></div>
<?php endif; ?>

</div>

</body>
</html>