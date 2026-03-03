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

if(!$poll_result){
    die("Poll Query Failed: " . mysqli_error($conn));
}

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

/* Count votes properly (selected_option stores 1,2,3) */
$count_query = "SELECT selected_option, COUNT(*) as total 
                FROM votes 
                WHERE poll_id = '$poll_id' 
                GROUP BY selected_option";

$count_result = mysqli_query($conn, $count_query);

$count1 = $count2 = $count3 = 0;

if($count_result){
    while($row = mysqli_fetch_assoc($count_result)){
        if($row['selected_option'] == 1) $count1 = $row['total'];
        if($row['selected_option'] == 2) $count2 = $row['total'];
        if($row['selected_option'] == 3) $count3 = $row['total'];
    }
}

$total_votes = $count1 + $count2 + $count3;

/* Percentages */
$percent1 = $total_votes > 0 ? round(($count1 / $total_votes) * 100) : 0;
$percent2 = $total_votes > 0 ? round(($count2 / $total_votes) * 100) : 0;
$percent3 = $total_votes > 0 ? round(($count3 / $total_votes) * 100) : 0;

/* Get voters list */
$voters_query = "SELECT users.fullname, votes.selected_option 
                 FROM votes 
                 JOIN users ON votes.user_id = users.id
                 WHERE votes.poll_id = '$poll_id'";

$voters_result = mysqli_query($conn, $voters_query);

if(!$voters_result){
    die("Voters Query Failed: " . mysqli_error($conn));
}

include("header.php");
?>

<div class="card shadow-lg p-4">

    <h2 class="text-center mb-4 fw-bold">Poll Results</h2>

    <div class="mb-4 text-center">
        <h4 class="fw-semibold"><?php echo htmlspecialchars($poll['question']); ?></h4>
        <p class="text-muted">Total Votes: <strong><?php echo $total_votes; ?></strong></p>
    </div>

    <!-- OPTION 1 -->
    <div class="mb-3">
        <div class="d-flex justify-content-between">
            <strong><?php echo htmlspecialchars($poll['option1']); ?></strong>
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
            <strong><?php echo htmlspecialchars($poll['option2']); ?></strong>
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
            <strong><?php echo htmlspecialchars($poll['option3']); ?></strong>
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
                            <td><?php echo htmlspecialchars($row['fullname']); ?></td>
                            <td>
                                <?php 
                                if($row['selected_option'] == 1){
                                    echo htmlspecialchars($poll['option1']);
                                }
                                elseif($row['selected_option'] == 2){
                                    echo htmlspecialchars($poll['option2']);
                                }
                                elseif($row['selected_option'] == 3){
                                    echo htmlspecialchars($poll['option3']);
                                }
                                else{
                                    echo "Unknown";
                                }
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