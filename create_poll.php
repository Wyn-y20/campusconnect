<?php
session_start();
include("config.php");

if(!isset($_SESSION['role']) || $_SESSION['role'] != "admin") {
    header("Location: login.php");
    exit();
}

$success = "";

if(isset($_POST['create_poll'])) {

    $question = mysqli_real_escape_string($conn, $_POST['question']);
    $option1 = mysqli_real_escape_string($conn, $_POST['option1']);
    $option2 = mysqli_real_escape_string($conn, $_POST['option2']);
    $option3 = mysqli_real_escape_string($conn, $_POST['option3']);

    $sql = "INSERT INTO polls (question, option1, option2, option3)
            VALUES ('$question', '$option1', '$option2', '$option3')";

    if(mysqli_query($conn, $sql)) {
        $success = "Poll created successfully!";
    } else {
        die("Error: " . mysqli_error($conn));
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
            <label class="form-label">Poll Question</label>
            <textarea name="question" class="form-control" required></textarea>
        </div>

        <div class="mb-3">
            <label class="form-label">Option 1</label>
            <input type="text" name="option1" class="form-control" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Option 2</label>
            <input type="text" name="option2" class="form-control" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Option 3</label>
            <input type="text" name="option3" class="form-control" required>
        </div>

        <button type="submit" name="create_poll" class="btn btn-primary w-100">
            Create Poll
        </button>

    </form>

    <hr>

    <a href="admin_dashboard.php" class="btn btn-secondary w-100">
        Back to Dashboard
    </a>

</div>

<?php include("footer.php"); ?>