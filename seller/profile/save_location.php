<?php
session_start();

include("../../config/database.php");

if (!isset($_SESSION['user'])) {
    exit("login");
}

$seller_id = $_SESSION['user']['id'];

$address = trim($_POST['address']);
$latitude = (float)$_POST['latitude'];
$longitude = (float)$_POST['longitude'];

$sql = "
UPDATE users
SET
    warehouse_address=?,
    warehouse_latitude=?,
    warehouse_longitude=?
WHERE id=?
";

$stmt = mysqli_prepare($conn, $sql);

mysqli_stmt_bind_param(
    $stmt,
    "sddi",
    $address,
    $latitude,
    $longitude,
    $seller_id
);

if (mysqli_stmt_execute($stmt)) {

    echo "success";
} else {

    echo "error";
}

mysqli_stmt_close($stmt);
mysqli_close($conn);
