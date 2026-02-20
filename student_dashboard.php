<?php
session_start();
if(!isset($_SESSION['role']) || $_SESSION['role'] != "student") {
    header("Location: login.php");
    exit();
}
include("header.php");
?>

<div class="card shadow p-4">
    <h2 class="text-center mb-4">Student Dashboard</h2>

    <a href="speakup.php" class="btn btn-primary w-100 mb-3">SpeakUp Portal</a>

    <a href="lostfound.php" class="btn btn-success w-100 mb-3">
        Lost & Found (Upload Item)
    </a>

    <a href="view_lostfound.php" class="btn btn-warning w-100 mb-3">
        View Lost Items
    </a>

    <a href="vote.php" class="btn btn-info w-100 mb-3">
        Vote in Poll
    </a>

    <hr>

    <a href="logout.php" class="btn btn-danger w-100">
        Logout
    </a>
</div>

<?php include("footer.php"); ?>
