<?php

session_start();

include("../../config/database.php");

header("Content-Type: application/json");

$order_id = isset($_GET['order_id']) ? intval($_GET['order_id']) : 0;

$sql = "
SELECT
    latitude,
    longitude,
    note,
    created_at
FROM order_tracking
WHERE order_id = ?
ORDER BY created_at DESC
LIMIT 1
";

$stmt = mysqli_prepare($conn, $sql);

mysqli_stmt_bind_param($stmt, "i", $order_id);

mysqli_stmt_execute($stmt);

$result = mysqli_stmt_get_result($stmt);

if ($row = mysqli_fetch_assoc($result)) {

    echo json_encode([
        "success" => true,
        "data" => $row
    ]);
} else {

    echo json_encode([
        "success" => false
    ]);
}

mysqli_stmt_close($stmt);
