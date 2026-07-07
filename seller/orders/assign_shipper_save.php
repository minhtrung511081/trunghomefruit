<?php
session_start();

require_once __DIR__ . "/../../config/database.php";

if (!isset($_SESSION['user'])) {
    header("Location:/fruit_shop/login.php");
    exit;
}

if ($_SESSION['user']['role_id'] != 2) {
    die("Bạn không có quyền.");
}

if ($_SERVER['REQUEST_METHOD'] != "POST") {
    header("Location:index.php");
    exit;
}

$order_id = (int)$_POST['order_id'];
$shipper_id = (int)$_POST['shipper_id'];

/*
|--------------------------------------------------------------------------
| Kiểm tra shipper
|--------------------------------------------------------------------------
*/

$stmt = mysqli_prepare(
    $conn,
    "SELECT id
     FROM users
     WHERE id=?
     AND role_id=4"
);

mysqli_stmt_bind_param(
    $stmt,
    "i",
    $shipper_id
);

mysqli_stmt_execute($stmt);

$rs = mysqli_stmt_get_result($stmt);

if (mysqli_num_rows($rs) == 0) {

    die("Shipper không tồn tại.");
}

/*
|--------------------------------------------------------------------------
| Kiểm tra đơn hàng
|--------------------------------------------------------------------------
*/

$stmt = mysqli_prepare(
    $conn,
    "SELECT id,status
     FROM orders
     WHERE id=?"
);

mysqli_stmt_bind_param(
    $stmt,
    "i",
    $order_id
);

mysqli_stmt_execute($stmt);

$rs = mysqli_stmt_get_result($stmt);

if (mysqli_num_rows($rs) == 0) {

    die("Không tìm thấy đơn hàng.");
}

$order = mysqli_fetch_assoc($rs);

if ($order['status'] != "Đã xác nhận") {

    echo "<script>

    alert('Đơn hàng chưa được xác nhận hoặc không thể giao.');

    location='detail.php?id={$order_id}';

    </script>";

    exit;
}

/*
|--------------------------------------------------------------------------
| Gán shipper và cập nhật trạng thái
|--------------------------------------------------------------------------
*/

$stmt = mysqli_prepare(
    $conn,
    "UPDATE orders
     SET shipper_id=?,
         status='Đang giao'
     WHERE id=?"
);

mysqli_stmt_bind_param(
    $stmt,
    "ii",
    $shipper_id,
    $order_id
);


if (mysqli_stmt_execute($stmt)) {
    echo "success";
    exit;
} else {
    echo "error";
    exit;
}


mysqli_close($conn);
