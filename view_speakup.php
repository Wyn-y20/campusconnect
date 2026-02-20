<?php
session_start();
include("config.php");

if(!isset($_SESSION['role']) || $_SESSION['role'] != "admin") {
    header("Location: login.php");
    exit();
}

$sql = "SELECT speakup.*, users.name 
        FROM speakup 
        JOIN users ON speakup.student_id = users.id
        ORDER BY speakup.id DESC";

$result = mysqli_query($conn, $sql);

include("header.php");
?>

<div class="card p-4 shadow-sm">

    <h2 class="mb-4">All SpeakUp Messages</h2>

    <?php if(mysqli_num_rows($result) > 0) { ?>

        <div class="table-responsive">
            <table class="table table-bordered table-striped table-hover">
                
                <thead class="table-dark">
                    <tr>
                        <th>Message</th>
                        <th>Submitted By</th>
                        <th>Date</th>
                    </tr>
                </thead>

                <tbody>
                    <?php while($row = mysqli_fetch_assoc($result)) { ?>
                        <tr>
                            <td><?php echo $row['message']; ?></td>

                            <td>
                                <?php 
                                if($row['is_anonymous'] == 1) {
                                    echo "Anonymous <br><small class='text-muted'>(Admin View: " . $row['name'] . ")</small>";
                                } else {
                                    echo $row['name'];
                                }
                                ?>
                            </td>

                            <td><?php echo $row['created_at']; ?></td>
                        </tr>
                    <?php } ?>
                </tbody>

            </table>
        </div>

    <?php } else { ?>

        <div class="alert alert-warning">
            No SpeakUp messages found.
        </div>

    <?php } ?>

    <hr>

    <a href="admin_dashboard.php" class="btn btn-secondary w-100">
        Back to Admin Dashboard
    </a>

</div>

<?php include("footer.php"); ?>
