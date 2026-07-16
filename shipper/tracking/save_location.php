<?php

session_start();

include("../../config/database.php");

header("Content-Type: application/json");

if (!isset($_SESSION['user'])) {
    echo json_encode([
        "success" => false,
        "message" => "Bạn chưa đăng nhập."
    ]);
    exit;
}

$shipper_id = $_SESSION['user']['id'];

$order_id = isset($_POST['order_id']) ? intval($_POST['order_id']) : 0;
$latitude = isset($_POST['latitude']) ? floatval($_POST['latitude']) : 0;
$longitude = isset($_POST['longitude']) ? floatval($_POST['longitude']) : 0;
$note = trim($_POST['note'] ?? "");

/* Kiểm tra đơn hàng thuộc shipper */

$sql = "
SELECT id
FROM orders
WHERE id = ?
AND shipper_id = ?
";

$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "ii", $order_id, $shipper_id);
mysqli_stmt_execute($stmt);

$result = mysqli_stmt_get_result($stmt);

if (mysqli_num_rows($result) == 0) {

    echo json_encode([
        "success" => false,
        "message" => "Đơn hàng không hợp lệ."
    ]);

    exit;
}

mysqli_stmt_close($stmt);

/* Lưu vị trí */

$sql = "
INSERT INTO order_tracking
(
    order_id,
    shipper_id,
    latitude,
    longitude,
    note
)
VALUES
(
    ?,
    ?,
    ?,
    ?,
    ?
)
";

$stmt = mysqli_prepare($conn, $sql);

mysqli_stmt_bind_param(
    $stmt,
    "iidds",
    $order_id,
    $shipper_id,
    $latitude,
    $longitude,
    $note
);

if (mysqli_stmt_execute($stmt)) {

    echo json_encode([
        "success" => true,
        "message" => "Đã cập nhật vị trí."
    ]);
} else {

    echo json_encode([
        "success" => false,
        "message" => mysqli_error($conn)
    ]);
}

mysqli_stmt_close($stmt);
