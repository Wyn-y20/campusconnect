<?php
session_start();
include("config.php");

if(!isset($_SESSION['role']) || $_SESSION['role'] != "student") {
    header("Location: login.php");
    exit();
}

$success = "";
$error = "";

/* Show success message */
if(isset($_GET['success'])) {
    $success = "Item submitted successfully!";
}

/* Handle form submission */
if(isset($_POST['submit'])) {

    $description = mysqli_real_escape_string($conn, $_POST['description']);
    $item_date = $_POST['item_date'];
    $category = mysqli_real_escape_string($conn, $_POST['category']);
    $user_id = $_SESSION['user_id'];

    /* ==============================
       🔐 SECURE IMAGE UPLOAD
    ============================== */

    if(isset($_FILES['image']) && $_FILES['image']['error'] === 0) {

        $image_name = $_FILES['image']['name'];
        $image_tmp = $_FILES['image']['tmp_name'];
        $image_size = $_FILES['image']['size'];

        /* 1️⃣ Validate extension */
        $allowed_extensions = ['jpg', 'jpeg', 'png', 'webp'];
        $file_extension = strtolower(pathinfo($image_name, PATHINFO_EXTENSION));

        if(!in_array($file_extension, $allowed_extensions)) {
            $error = "Only JPG, JPEG, PNG, WEBP files are allowed.";
        }

        /* 2️⃣ Validate MIME type */
        $allowed_mime = ['image/jpeg','image/png','image/webp'];
        $file_mime = mime_content_type($image_tmp);

        if(empty($error) && !in_array($file_mime, $allowed_mime)) {
            $error = "Invalid image file.";
        }

        /* 3️⃣ Limit file size (2MB) */
        if(empty($error) && $image_size > 2 * 1024 * 1024) {
            $error = "File size must be less than 2MB.";
        }

        /* 4️⃣ Rename file securely */
        if(empty($error)) {

            $new_filename = time() . "_" . bin2hex(random_bytes(5)) . "." . $file_extension;
            $upload_path = "uploads/" . $new_filename;

            if(!move_uploaded_file($image_tmp, $upload_path)) {
                $error = "Failed to upload image.";
            }
        }

    } else {
        $error = "Please upload an image.";
    }

    /* Insert into database if no error */
    if(empty($error)) {

        $stmt = $conn->prepare("
            INSERT INTO lost_and_found 
            (user_id, description, item_date, category, image) 
            VALUES (?, ?, ?, ?, ?)
        ");

        $stmt->bind_param("issss", 
            $user_id, 
            $description, 
            $item_date, 
            $category, 
            $new_filename
        );

        if($stmt->execute()) {
            header("Location: lostfound.php?success=1");
            exit();
        } else {
            $error = "Database error. Please try again.";
        }

        $stmt->close();
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

    <?php if($error != "") { ?>
        <div class="alert alert-danger">
            <?php echo $error; ?>
        </div>
    <?php } ?>

    <form method="POST" enctype="multipart/form-data">

        <div class="mb-3">
            <label class="form-label">Upload Item Image</label>
            <input type="file" name="image" class="form-control" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Item Description</label>
            <textarea name="description" class="form-control" required></textarea>
        </div>

        <div class="mb-3">
            <label class="form-label">Select Date</label>
            <input type="date" name="item_date" class="form-control" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Category</label>
            <select name="category" class="form-select" required>
                <option value="">Select Category</option>
                <option value="Electronics">Electronics</option>
                <option value="Personal Items">Personal Items</option>
                <option value="Books & Stationery">Books & Stationery</option>
            </select>
        </div>

        <button type="submit" name="submit" class="btn btn-primary w-100">
            Submit
        </button>

    </form>

    <hr>

    <a href="student_dashboard.php" class="btn btn-secondary w-100">
        Back to Dashboard
    </a>

</div>

<?php include("footer.php"); ?>