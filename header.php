<!DOCTYPE html>
<html>
<head>
    <title>CampusConnect</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <div class="container">
        <a class="navbar-brand fw-bold" href="#">
            CampusConnect
        </a>

        <div class="ms-auto">
            <?php if(isset($_SESSION['role'])) { ?>
                <a href="logout.php" class="btn btn-danger btn-sm">Logout</a>
            <?php } ?>
        </div>
    </div>
</nav>

<div class="container mt-4">
