<?php
session_start();
include("config.php");

if(!isset($_SESSION['role']) || $_SESSION['role'] != "admin") {
    header("Location: login.php");
    exit();
}

/* ===========================
   UPDATE STATUS (FIXED CLEAN)
=========================== */
if(isset($_POST['update_status'])) {

    $id = intval($_POST['id']);
    $new_status = strtolower(trim($_POST['status'])); // store lowercase

    if($new_status == "resolved") {

        mysqli_query($conn, "
            UPDATE speakup 
            SET status='resolved', resolved_at=NOW() 
            WHERE id='$id'
        ");

    } else {

        mysqli_query($conn, "
            UPDATE speakup 
            SET status='$new_status', resolved_at=NULL 
            WHERE id='$id'
        ");
    }
}

/* ===========================
   FILTERING
=========================== */
$where = "WHERE 1";

if(!empty($_GET['category'])) {
    $category = mysqli_real_escape_string($conn, $_GET['category']);
    $where .= " AND speakup.category = '$category'";
}

if(!empty($_GET['student'])) {
    $student = mysqli_real_escape_string($conn, $_GET['student']);
    $where .= " AND users.fullname LIKE '%$student%'";
}

/* ===========================
   QUERY
=========================== */
$sql = "SELECT speakup.*, users.fullname 
        FROM speakup
        JOIN users ON speakup.user_id = users.id
        $where
        ORDER BY speakup.id DESC";

$result = mysqli_query($conn, $sql);

include("header.php");
?>

<div class="card shadow p-4">

<h2 class="mb-4">All SpeakUp Messages</h2>

<form method="GET" class="row mb-4">

    <div class="col-md-4">
        <select name="category" class="form-select">
            <option value="">All Categories</option>
            <option value="Academic Issues">Academic Issues</option>
            <option value="Administrative/Facility Issues">Administrative/Facility Issues</option>
            <option value="Behavioral Misconduct">Behavioral Misconduct</option>
        </select>
    </div>

    <div class="col-md-4">
        <input type="text" name="student" class="form-control" 
               placeholder="Search by Student Name">
    </div>

    <div class="col-md-4">
        <button type="submit" class="btn btn-primary w-100">
            Filter / Search
        </button>
    </div>

</form>

<table class="table table-bordered">
<tr>
    <th>Category</th>
    <th>Message</th>
    <th>Submitted By</th>
    <th>Status</th>
    <th>Date</th>
    <th>Action</th>
</tr>

<?php while($row = mysqli_fetch_assoc($result)) { 

    $status = strtolower($row['status']);

    /* Badge Color Mapping */
    if($status == "pending") {
        $badge = "warning text-dark";
    }
    elseif($status == "in progress") {
        $badge = "primary";
    }
    elseif($status == "on hold") {
        $badge = "info text-dark";
    }
    elseif($status == "resolved") {
        $badge = "success";
    }
    elseif($status == "rejected") {
        $badge = "danger";
    }
    else {
        $badge = "secondary";
    }

?>

<tr>
    <td><?php echo $row['category']; ?></td>
    <td><?php echo $row['message']; ?></td>
    <td><?php echo $row['fullname']; ?></td>
    <td>
        <span class="badge bg-<?php echo $badge; ?>">
            <?php echo ucwords($status); ?>
        </span>
    </td>
    <td><?php echo $row['message_date']; ?></td>

    <td>
        <form method="POST" class="d-flex">
            <input type="hidden" name="id" value="<?php echo $row['id']; ?>">

            <select name="status" class="form-select form-select-sm me-2">
                <option value="pending" <?php if($status=="pending") echo "selected"; ?>>Pending</option>
                <option value="in progress" <?php if($status=="in progress") echo "selected"; ?>>In Progress</option>
                <option value="on hold" <?php if($status=="on hold") echo "selected"; ?>>On Hold</option>
                <option value="resolved" <?php if($status=="resolved") echo "selected"; ?>>Resolved</option>
                <option value="rejected" <?php if($status=="rejected") echo "selected"; ?>>Rejected</option>
            </select>

            <button type="submit" name="update_status" 
                    class="btn btn-success btn-sm">
                Update
            </button>
        </form>
    </td>
</tr>

<?php } ?>

</table>

<a href="admin_dashboard.php" class="btn btn-secondary w-100">
    Back to Admin Dashboard
</a>

</div>

<?php include("footer.php"); ?>