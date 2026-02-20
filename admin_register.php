<?php
include("config.php");

if(isset($_POST['register'])) {

    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $phone = mysqli_real_escape_string($conn, $_POST['phone']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    $role = "admin";

    if($password != $confirm_password) {
        echo "<script>alert('Passwords do not match');</script>";
    } else {

        // Check duplicate email
        $check = "SELECT * FROM users WHERE email='$email'";
        $result = mysqli_query($conn, $check);

        if(mysqli_num_rows($result) > 0) {
            echo "<script>alert('Email already exists');</script>";
        } else {

            $hashed_password = password_hash($password, PASSWORD_DEFAULT);

            $insert = "INSERT INTO users (name, email, phone, password, role) 
                       VALUES ('$name', '$email', '$phone', '$hashed_password', '$role')";

            if(mysqli_query($conn, $insert)) {
                echo "<script>alert('Admin Registered Successfully'); window.location='admin_login.php';</script>";
                exit();
            } else {
                echo "Error: " . mysqli_error($conn);
            }
        }
    }
}
?>

<?php include("auth_header.php"); ?>

<div class="auth-card">
    <h2>Admin Registration</h2>

    <form method="POST">

        <input type="text" name="name" placeholder="Full Name *" required>

        <input type="email" name="email" placeholder="E-mail *" required>

        <input type="text" name="phone" placeholder="Phone No. *" required>

        <input type="password" name="password" placeholder="Password *" required>

        <input type="password" name="confirm_password" placeholder="Confirm Password *" required>

        <button type="submit" name="register">REGISTER</button>

    </form>

    <div class="auth-bottom">
        Already have an account? 
        <a href="/campusconnect/admin_login.php">Log in</a>
    </div>
</div>

<?php include("auth_footer.php"); ?>
