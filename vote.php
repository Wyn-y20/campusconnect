<?php
session_start();
include("config.php");

if(!isset($_SESSION['role']) || $_SESSION['role'] != "student") {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$message = "";

/* Get latest poll */
$poll_query = "SELECT * FROM polls ORDER BY id DESC LIMIT 1";
$poll_result = mysqli_query($conn, $poll_query);

if(mysqli_num_rows($poll_result) == 0) {
    include("header.php");
    echo "<div class='card p-4'>";
    echo "<div class='alert alert-warning'>No poll available right now.</div>";
    echo "<a href='student_dashboard.php' class='btn btn-secondary w-100'>Back</a>";
    echo "</div>";
    include("footer.php");
    exit();
}

$poll = mysqli_fetch_assoc($poll_result);
$poll_id = $poll['id'];

/* Check if student already voted */
$check_vote = "SELECT * FROM votes WHERE poll_id='$poll_id' AND user_id='$user_id'";
$vote_result = mysqli_query($conn, $check_vote);

if(mysqli_num_rows($vote_result) > 0) {
    include("header.php");
    echo "<div class='card p-4'>";
    echo "<div class='alert alert-info'>You have already voted in this poll.</div>";
    echo "<a href='student_dashboard.php' class='btn btn-secondary w-100'>Back</a>";
    echo "</div>";
    include("footer.php");
    exit();
}

/* If vote submitted */
if(isset($_POST['vote'])) {

    $selected_option = mysqli_real_escape_string($conn, $_POST['option']);

    $insert_vote = "INSERT INTO votes (poll_id, user_id, selected_option)
                    VALUES ('$poll_id', '$user_id', '$selected_option')";

    if(mysqli_query($conn, $insert_vote)) {
        include("header.php");
        echo "<div class='card p-4'>";
        echo "<div class='alert alert-success'>Vote Submitted Successfully!</div>";
        echo "<a href='student_dashboard.php' class='btn btn-secondary w-100'>Back</a>";
        echo "</div>";
        include("footer.php");
        exit();
    }
}

include("header.php");
?>

<div class="card p-4">

    <h3 class="mb-4"><?php echo $poll['question']; ?></h3>

    <form method="POST">

        <div class="form-check mb-3">
            <input class="form-check-input" type="radio" name="option" value="option1" required>
            <label class="form-check-label">
                <?php echo $poll['option1']; ?>
            </label>
        </div>

        <div class="form-check mb-3">
            <input class="form-check-input" type="radio" name="option" value="option2">
            <label class="form-check-label">
                <?php echo $poll['option2']; ?>
            </label>
        </div>

        <?php if(!empty($poll['option3'])) { ?>
        <div class="form-check mb-3">
            <input class="form-check-input" type="radio" name="option" value="option3">
            <label class="form-check-label">
                <?php echo $poll['option3']; ?>
            </label>
        </div>
        <?php } ?>

        <button type="submit" name="vote" class="btn btn-primary w-100">
            Submit Vote
        </button>

    </form>

    <hr>

    <a href="student_dashboard.php" class="btn btn-secondary w-100">
        Back to Dashboard
    </a>

</div>

<?php include("footer.php"); ?>
