<?php
$conn = mysqli_connect("localhost", "root", "", "campusconnect");

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}
?>
