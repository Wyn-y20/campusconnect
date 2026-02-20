<?php
include("config.php");

if(isset($_POST['register'])) {

    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $phone = trim($_POST['phone']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    $role = "student";

    // Check password match
    if($password !== $confirm_password) {
        echo "<script>alert('Passwords do not match');</script>";
    } else {

        // Check if email already exists
        $check_stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
        $check_stmt->bind_param("s", $email);
        $check_stmt->execute();
        $check_stmt->store_result();

        if($check_stmt->num_rows > 0) {

            echo "<script>alert('Email already exists');</script>";

        } else {

            // Hash password
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);

            // Insert user using prepared statement
            $stmt = $conn->prepare("INSERT INTO users (name, email, phone, password, role) VALUES (?, ?, ?, ?, ?)");
            $stmt->bind_param("sssss", $name, $email, $phone, $hashed_password, $role);

            if($stmt->execute()) {
                header("Location: login.php");
                exit();
            } else {
                echo "<script>alert('Registration failed');</script>";
            }

            $stmt->close();
        }

        $check_stmt->close();
    }
}
?>
<?php include("auth_header.php"); ?>

<div class="auth-card">

    <h2>Student Registration</h2>

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
        <a href="login.php">Log in</a>
    </div>

</div>

<?php include("auth_footer.php"); ?>
