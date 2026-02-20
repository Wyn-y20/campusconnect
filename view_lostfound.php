<?php
session_start();
include("config.php");

if(!isset($_SESSION['role'])) {
    header("Location: login.php");
    exit();
}

$sql = "SELECT lost_found.*, users.name 
        FROM lost_found
        JOIN users ON lost_found.uploaded_by = users.id
        ORDER BY lost_found.id DESC";

$result = mysqli_query($conn, $sql);

include("header.php");
?>

<div class="card shadow p-4">

    <h2 class="text-center mb-4">All Lost & Found Items</h2>

    <?php if(mysqli_num_rows($result) > 0) { ?>

        <div class="row">

            <?php while($row = mysqli_fetch_assoc($result)) { ?>

                <div class="col-md-4 mb-4">
                    <div class="card h-100 shadow-sm">

                        <img src="uploads/<?php echo $row['image']; ?>" 
                             class="card-img-top"
                             style="height:220px; object-fit:cover;">

                        <div class="card-body d-flex flex-column">

                            <h5 class="card-title">
                                <?php echo $row['title']; ?>
                            </h5>

                            <p class="card-text">
                                <?php echo $row['description']; ?>
                            </p>

                            <p class="text-muted mt-auto">
                                Uploaded By: <?php echo $row['name']; ?>
                            </p>

                        </div>

                    </div>
                </div>

            <?php } ?>

        </div>

    <?php } else { ?>

        <div class="alert alert-warning text-center">
            No items found.
        </div>

    <?php } ?>

    <div class="mt-4">
        <?php if($_SESSION['role'] == "admin") { ?>
            <a href="admin_dashboard.php" class="btn btn-secondary w-100">
                Back to Admin Dashboard
            </a>
        <?php } else { ?>
            <a href="student_dashboard.php" class="btn btn-secondary w-100">
                Back to Student Dashboard
            </a>
        <?php } ?>
    </div>

</div>

<?php include("footer.php"); ?>
