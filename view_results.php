<?php
session_start();
include("config.php");

if(!isset($_SESSION['role']) || $_SESSION['role'] != "admin") {
    header("Location: login.php");
    exit();
}

/* Get latest poll */
$poll_query = "SELECT * FROM polls ORDER BY id DESC LIMIT 1";
$poll_result = mysqli_query($conn, $poll_query);

if(mysqli_num_rows($poll_result) == 0) {
    include("header.php");
    echo "<div class='card shadow p-4'>";
    echo "<div class='alert alert-warning text-center'>No poll available.</div>";
    echo "<a href='admin_dashboard.php' class='btn btn-secondary w-100'>Back</a>";
    echo "</div>";
    include("footer.php");
    exit();
}

$poll = mysqli_fetch_assoc($poll_result);
$poll_id = $poll['id'];

/* Count votes */
$count1 = mysqli_num_rows(mysqli_query($conn, "SELECT * FROM votes WHERE poll_id='$poll_id' AND selected_option='option1'"));
$count2 = mysqli_num_rows(mysqli_query($conn, "SELECT * FROM votes WHERE poll_id='$poll_id' AND selected_option='option2'"));
$count3 = mysqli_num_rows(mysqli_query($conn, "SELECT * FROM votes WHERE poll_id='$poll_id' AND selected_option='option3'"));

$total_votes = $count1 + $count2 + $count3;

/* Percentages */
$percent1 = $total_votes > 0 ? round(($count1 / $total_votes) * 100) : 0;
$percent2 = $total_votes > 0 ? round(($count2 / $total_votes) * 100) : 0;
$percent3 = $total_votes > 0 ? round(($count3 / $total_votes) * 100) : 0;

/* Get voters */
$voters_query = "SELECT users.name, votes.selected_option 
                 FROM votes 
                 JOIN users ON votes.user_id = users.id
                 WHERE votes.poll_id='$poll_id'";

$voters_result = mysqli_query($conn, $voters_query);

include("header.php");
?>

<div class="card shadow-lg p-4">

    <h2 class="text-center mb-4 fw-bold">Poll Results</h2>

    <div class="mb-4 text-center">
        <h4 class="fw-semibold"><?php echo $poll['question']; ?></h4>
        <p class="text-muted">Total Votes: <strong><?php echo $total_votes; ?></strong></p>
    </div>

    <!-- OPTION 1 -->
    <div class="mb-3">
        <div class="d-flex justify-content-between">
            <strong><?php echo $poll['option1']; ?></strong>
            <span><?php echo $count1; ?> votes (<?php echo $percent1; ?>%)</span>
        </div>
        <div class="progress">
            <div class="progress-bar bg-primary progress-bar-striped progress-bar-animated"
                role="progressbar"
                style="width: <?php echo $percent1; ?>%;">
                <?php echo $percent1; ?>%
            </div>
        </div>
    </div>

    <!-- OPTION 2 -->
    <div class="mb-3">
        <div class="d-flex justify-content-between">
            <strong><?php echo $poll['option2']; ?></strong>
            <span><?php echo $count2; ?> votes (<?php echo $percent2; ?>%)</span>
        </div>
        <div class="progress">
            <div class="progress-bar bg-success progress-bar-striped progress-bar-animated"
                role="progressbar"
                style="width: <?php echo $percent2; ?>%;">
                <?php echo $percent2; ?>%
            </div>
        </div>
    </div>

    <!-- OPTION 3 -->
    <?php if(!empty($poll['option3'])) { ?>
    <div class="mb-4">
        <div class="d-flex justify-content-between">
            <strong><?php echo $poll['option3']; ?></strong>
            <span><?php echo $count3; ?> votes (<?php echo $percent3; ?>%)</span>
        </div>
        <div class="progress">
            <div class="progress-bar bg-info progress-bar-striped progress-bar-animated"
                role="progressbar"
                style="width: <?php echo $percent3; ?>%;">
                <?php echo $percent3; ?>%
            </div>
        </div>
    </div>
    <?php } ?>

    <hr class="my-4">

    <!-- VOTER LIST -->
    <h5 class="mb-3 fw-bold">Voter List</h5>

    <?php if(mysqli_num_rows($voters_result) > 0) { ?>
        <div class="table-responsive">
            <table class="table table-bordered table-striped table-hover">
                <thead class="table-dark text-center">
                    <tr>
                        <th>Student Name</th>
                        <th>Selected Option</th>
                    </tr>
                </thead>
                <tbody class="text-center">
                    <?php while($row = mysqli_fetch_assoc($voters_result)) { ?>
                        <tr>
                            <td><?php echo $row['name']; ?></td>
                            <td>
                                <?php 
                                $option_text = $poll[$row['selected_option']];
                                echo $option_text; 
                                ?>
                            </td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
    <?php } else { ?>
        <div class="alert alert-info text-center">
            No votes yet.
        </div>
    <?php } ?>

    <a href="admin_dashboard.php" class="btn btn-secondary w-100 mt-3">
        Back to Dashboard
    </a>

</div>

<?php include("footer.php"); ?>
