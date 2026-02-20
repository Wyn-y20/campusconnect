<?php
session_start();
include("config.php");

if(!isset($_SESSION['role']) || $_SESSION['role'] != "student") {
    header("Location: login.php");
    exit();
}

$success = "";

if(isset($_POST['submit'])) {

    $message = mysqli_real_escape_string($conn, $_POST['message']);
    $anonymous = isset($_POST['anonymous']) ? 1 : 0;
    $student_id = $_SESSION['user_id'];

    $sql = "INSERT INTO speakup (message, is_anonymous, student_id)
            VALUES ('$message', '$anonymous', '$student_id')";

    if(mysqli_query($conn, $sql)) {
        $success = "Message Submitted Successfully!";
    }
}

include("header.php");
?>

<div class="card p-4">

    <h2 class="mb-4">SpeakUp Portal</h2>

    <?php if($success != "") { ?>
        <div class="alert alert-success">
            <?php echo $success; ?>
        </div>
    <?php } ?>

    <form method="POST">

        <div class="mb-3">
    <label class="form-label">Your Message</label>
    <textarea name="message" class="form-control" required></textarea>
</div>

<div class="mb-3">
    <label class="form-label">Anonymous?</label>
    <select name="anonymous" class="form-select">
        <option value="yes">Yes</option>
        <option value="no">No</option>
    </select>
</div>

<button type="submit" class="btn btn-primary w-100">Submit</button>

            Submit Message
        </button>

    </form>

    <hr>

    <a href="student_dashboard.php" class="btn btn-secondary w-100">
        Back to Dashboard
    </a>

</div>

<?php include("footer.php"); ?>
