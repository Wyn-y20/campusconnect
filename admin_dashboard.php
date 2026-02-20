<?php
session_start();

if(!isset($_SESSION['role']) || $_SESSION['role'] != "admin") {
    header("Location: login.php");
    exit();
}

include("header.php");
?>

<div class="card shadow p-4">
    <h2 class="text-center mb-4">
        Welcome Admin <?php echo $_SESSION['name']; ?>
    </h2>

    <div class="d-grid gap-3">

        <a href="view_speakup.php" class="btn btn-primary btn-lg">
            View SpeakUp Messages
        </a>

        <a href="view_lostfound.php" class="btn btn-success btn-lg">
            View Lost & Found
        </a>

        <a href="create_poll.php" class="btn btn-warning btn-lg">
            Create Poll
        </a>

        <a href="view_results.php" class="btn btn-info btn-lg">
            View Poll Results
        </a>

        <a href="logout.php" class="btn btn-danger btn-lg mt-2">
            Logout
        </a>

    </div>
</div>

<?php include("footer.php"); ?>
