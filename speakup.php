<?php
session_start();
include("config.php");

if(!isset($_SESSION['role']) || $_SESSION['role'] != "student") {
    header("Location: login.php");
    exit();
}

$success = "";

if(isset($_GET['success']) && $_GET['success'] == 1) {
    $success = "Message submitted successfully!";
}

/* Handle form submission */
if(isset($_POST['submit'])) {

    $message = mysqli_real_escape_string($conn, $_POST['message']);
    $category = mysqli_real_escape_string($conn, $_POST['category']);
    $message_date = $_POST['message_date'];
    $user_id = $_SESSION['user_id'];

    $sql = "INSERT INTO speakup 
            (user_id, category, message_date, message, status, created_at)
            VALUES 
            ('$user_id', '$category', '$message_date', '$message', 'Pending', NOW())";

    if(mysqli_query($conn, $sql)) {
        header("Location: speakup.php?success=1");
        exit();
    } else {
        die("Error: " . mysqli_error($conn));
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
        <label class="form-label">Category</label>
        <select name="category" class="form-select" required>
            <option value="">Select Category</option>
            <option value="Academic Issues">Academic Issues</option>
            <option value="Administrative/Facility Issues">Administrative/Facility Issues</option>
            <option value="Behavioral Misconduct">Behavioral Misconduct</option>
        </select>
    </div>

    <div class="mb-3">
        <label class="form-label">Select Date</label>
        <input type="date" name="message_date" class="form-control" required>
    </div>

    <button type="submit" name="submit" class="btn btn-primary w-100">
        Submit
    </button>

</form>

<hr>

<h4>Your Submitted Messages</h4>

<?php
$student_id = $_SESSION['user_id'];

/* ACTIVE MESSAGES */
$active_query = "
    SELECT * FROM speakup 
    WHERE user_id = '$student_id'
    AND (
        status != 'Resolved'
        OR resolved_at >= NOW() - INTERVAL 15 DAY
        OR resolved_at IS NULL
    )
    ORDER BY id DESC
";

$active_result = mysqli_query($conn, $active_query);
?>

<table class="table table-bordered mt-3">
<tr>
    <th>Category</th>
    <th>Message</th>
    <th>Status</th>
    <th>Date</th>
</tr>

<?php while($row = mysqli_fetch_assoc($active_result)) { ?>
<tr>
    <td><?php echo $row['category']; ?></td>
    <td><?php echo $row['message']; ?></td>
    <td>
        <?php
        $status = $row['status'];
        $badge = "secondary";

        if($status == "Pending") $badge = "secondary";
        elseif($status == "In Progress") $badge = "primary";
        elseif($status == "On Hold") $badge = "warning";
        elseif($status == "Resolved") $badge = "success";
        elseif($status == "Rejected") $badge = "danger";
        ?>

        <span class="badge bg-<?php echo $badge; ?>">
            <?php echo $status; ?>
        </span>
    </td>
    <td><?php echo $row['message_date']; ?></td>
</tr>
<?php } ?>
</table>

<!-- Archive Toggle Button -->
<button onclick="toggleArchive()" class="btn btn-secondary mb-3">
    View Archived Messages
</button>

<?php
/* ARCHIVED MESSAGES */
$archive_query = "
    SELECT * FROM speakup 
    WHERE user_id = '$student_id'
    AND status = 'Resolved'
    AND resolved_at < NOW() - INTERVAL 15 DAY
    ORDER BY resolved_at DESC
";

$archive_result = mysqli_query($conn, $archive_query);
?>

<div id="archiveSection" style="display:none;">

<h4>Archived Messages</h4>

<table class="table table-bordered mt-3">
<tr>
    <th>Category</th>
    <th>Message</th>
    <th>Status</th>
    <th>Resolved On</th>
</tr>

<?php while($row = mysqli_fetch_assoc($archive_result)) { ?>
<tr>
    <td><?php echo $row['category']; ?></td>
    <td><?php echo $row['message']; ?></td>
    <td><span class="badge bg-success">Resolved</span></td>
    <td><?php echo date("d M Y", strtotime($row['resolved_at'])); ?></td>
</tr>
<?php } ?>

</table>
</div>

<a href="student_dashboard.php" class="btn btn-secondary w-100">
    Back to Dashboard
</a>

</div>

<script>
function toggleArchive(){
    var section = document.getElementById("archiveSection");
    if(section.style.display === "none"){
        section.style.display = "block";
    } else {
        section.style.display = "none";
    }
}
</script>

<?php include("footer.php"); ?>