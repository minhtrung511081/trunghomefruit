<?php
session_start();

require_once __DIR__ . "/../../config/database.php";

if (!isset($_SESSION['user'])) {
    header("Location: /fruit_shop/login.php");
    exit;
}

if ($_SESSION['user']['role_name'] != "Seller") {
    die("Bạn không có quyền.");
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

$rs = mysqli_stmt_get_result($stmt);

if (mysqli_num_rows($rs) == 0) {

    die("Không tìm thấy đơn hàng.");
}

$order = mysqli_fetch_assoc($rs);

if ($order['status'] != "Đang xử lý") {

    echo "<script>

    alert('Đơn hàng này không thể xác nhận.');

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
     SET status='Đã xác nhận'
     WHERE id=?"
);

mysqli_stmt_bind_param(
    $stmt,
    "i",
    $order_id
);

if (mysqli_stmt_execute($stmt)) {

    echo "<script>

    alert('Đã xác nhận đơn hàng.');

    location='detail.php?id={$order_id}';

    </script>";
} else {

    echo "<script>

    alert('Có lỗi xảy ra.');

    history.back();

    </script>";
}
