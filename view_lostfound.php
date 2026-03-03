<?php
session_start();
include("config.php");

if(!isset($_SESSION['role'])) {
    header("Location: login.php");
    exit();
}

/* Category Filter Logic */
$where = "";
$selected_category = "";

if(isset($_GET['category']) && $_GET['category'] != "") {
    $selected_category = mysqli_real_escape_string($conn, $_GET['category']);
    $where = "WHERE lost_and_found.category = '$selected_category'";
}

/* Final Query - Sort by newest upload */
$sql = "SELECT lost_and_found.*, users.fullname 
        FROM lost_and_found
        JOIN users ON lost_and_found.user_id = users.id
        $where
        ORDER BY lost_and_found.created_at DESC";

$result = mysqli_query($conn, $sql);

if(!$result){
    die("Query Failed: " . mysqli_error($conn));
}

include("header.php");
?>

<div class="card shadow p-4">

    <h2 class="text-center mb-4">All Lost & Found Items</h2>

    <!-- Category Filter -->
    <form method="GET" class="mb-4 text-center">
        <select name="category" class="form-select w-50 d-inline">
            <option value="">All Categories</option>
            <option value="Electronics" <?php if($selected_category=="Electronics") echo "selected"; ?>>Electronics</option>
            <option value="Personal Items" <?php if($selected_category=="Personal Items") echo "selected"; ?>>Personal Items</option>
            <option value="Books & Stationery" <?php if($selected_category=="Books & Stationery") echo "selected"; ?>>Books & Stationery</option>
        </select>

        <button type="submit" class="btn btn-primary mt-2">
            Filter
        </button>
    </form>

    <?php if(mysqli_num_rows($result) > 0) { ?>

        <div class="row">

            <?php while($row = mysqli_fetch_assoc($result)) { ?>

                <div class="col-md-4 mb-4">
                    <div class="card h-100 shadow-sm">

                        <?php
                        $image_path = "uploads/" . $row['image'];
                        if(file_exists($image_path)) {
                        ?>
                            <img src="<?php echo $image_path; ?>" 
                                 class="card-img-top"
                                 style="height:220px; object-fit:cover;">
                        <?php } else { ?>
                            <img src="https://via.placeholder.com/400x220?text=No+Image"
                                 class="card-img-top"
                                 style="height:220px; object-fit:cover;">
                        <?php } ?>

                        <div class="card-body d-flex flex-column">

                            <h5 class="card-title">
                                <?php echo htmlspecialchars($row['category']); ?>
                            </h5>

                            <p class="card-text">
                                <?php echo htmlspecialchars($row['description']); ?>
                            </p>

                            <p class="text-muted">
                                Date: <?php echo $row['item_date']; ?>
                            </p>

                            <p class="text-muted mt-auto">
                                Uploaded By: <?php echo htmlspecialchars($row['fullname']); ?>
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