<?php
session_start();

require_once __DIR__ . "/../../config/database.php";

if (!isset($_SESSION['user'])) {
    header("Location: /fruit_shop/login.php");
    exit;
}

if ($_SESSION['user']['role_name'] != "Seller") {
    die("Bạn không có quyền truy cập.");
}

if (!isset($_GET['id'])) {
    header("Location: index.php");
    exit;
}

$order_id = (int)$_GET['id'];

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

mysqli_stmt_bind_param($stmt, "i", $order_id);
mysqli_stmt_execute($stmt);

$result = mysqli_stmt_get_result($stmt);

if (mysqli_num_rows($result) == 0) {

    die("Không tìm thấy đơn hàng.");
}

$order = mysqli_fetch_assoc($result);

/*
|--------------------------------------------------------------------------
| Chỉ hủy khi chưa giao
|--------------------------------------------------------------------------
*/

if (
    $order['status'] == "Đã giao" ||
    $order['status'] == "Hoàn thành" ||
    $order['status'] == "Đã hủy"
) {

    echo "<script>

        alert('Đơn hàng này không thể hủy.');

        location='detail.php?id={$order_id}';

    </script>";

    exit;
}

/*
|--------------------------------------------------------------------------
| Cập nhật trạng thái
|--------------------------------------------------------------------------
*/

$stmt = mysqli_prepare(
    $conn,
    "UPDATE orders
     SET status='Đã hủy'
     WHERE id=?"
);

mysqli_stmt_bind_param(
    $stmt,
    "i",
    $order_id
);

if (mysqli_stmt_execute($stmt)) {

    echo "<script>

        alert('Đã hủy đơn hàng.');

        location='index.php';

    </script>";
} else {

    echo "<script>

        alert('Có lỗi xảy ra.');

        history.back();

    </script>";
}
