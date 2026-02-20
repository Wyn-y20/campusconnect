<?php
session_start();
include("config.php");

if(!isset($_SESSION['role']) || $_SESSION['role'] != "student") {
    header("Location: login.php");
    exit();
}

$success = "";

if(isset($_POST['submit'])) {

    $title = mysqli_real_escape_string($conn, $_POST['title']);
    $description = mysqli_real_escape_string($conn, $_POST['description']);
    $uploaded_by = $_SESSION['user_id'];

    $image_name = $_FILES['image']['name'];
    $temp_name = $_FILES['image']['tmp_name'];

    // Create unique file name to avoid duplicate names
    $image_name = time() . "_" . $image_name;
    $folder = "uploads/" . $image_name;

    if(move_uploaded_file($temp_name, $folder)) {

        $sql = "INSERT INTO lost_found (title, description, image, uploaded_by)
                VALUES ('$title', '$description', '$image_name', '$uploaded_by')";

        if(mysqli_query($conn, $sql)) {
            $success = "Item Submitted Successfully!";
        }
    }
}

include("header.php");
?>

<div class="card p-4">

    <h2 class="mb-4">Lost & Found Portal</h2>

    <?php if($success != "") { ?>
        <div class="alert alert-success">
            <?php echo $success; ?>
        </div>
    <?php } ?>

    <form method="POST" enctype="multipart/form-data">

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
            Submit Item
        </button>

    </form>

    <hr>

    <a href="student_dashboard.php" class="btn btn-secondary w-100">
        Back to Dashboard
    </a>

</div>

<?php include("footer.php"); ?>
