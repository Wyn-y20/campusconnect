<?php
session_start();
include("config.php");

if(!isset($_SESSION['role']) || $_SESSION['role'] != "admin") {
    header("Location: login.php");
    exit();
}

$success = "";

if(isset($_POST['create'])) {

    $question = mysqli_real_escape_string($conn, $_POST['question']);
    $option1  = mysqli_real_escape_string($conn, $_POST['option1']);
    $option2  = mysqli_real_escape_string($conn, $_POST['option2']);
    $option3  = mysqli_real_escape_string($conn, $_POST['option3']);

    $sql = "INSERT INTO polls (question, option1, option2, option3)
            VALUES ('$question', '$option1', '$option2', '$option3')";

    if(mysqli_query($conn, $sql)) {
        $success = "Poll Created Successfully!";
    }
}

include("header.php");
?>

<div class="card p-4">

    <h2 class="mb-4">Create Poll</h2>

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
            Create Poll
        </button>

    </form>

    <hr>

    <a href="admin_dashboard.php" class="btn btn-secondary w-100">
        Back to Dashboard
    </a>

</div>

<?php include("footer.php"); ?>
