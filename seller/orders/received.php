<?php
session_start();

require_once __DIR__ . "/../config/database.php";

if (!isset($_SESSION['user'])) {
    header("Location: /fruit_shop/login.php");
    exit;
}

if (!isset($_GET['id'])) {
    header("Location: index.php");
    exit;
}

$order_id = (int)$_GET['id'];
$user_id  = $_SESSION['user']['id'];

/*
|--------------------------------------------------------------------------
| Kiểm tra đơn hàng
|--------------------------------------------------------------------------
*/

$sql = "
SELECT *
FROM orders
WHERE id=?
AND user_id=?
LIMIT 1
";

$stmt = mysqli_prepare($conn, $sql);

mysqli_stmt_bind_param(
    $stmt,
    "ii",
    $order_id,
    $user_id
);

mysqli_stmt_execute($stmt);

$result = mysqli_stmt_get_result($stmt);

if (mysqli_num_rows($result) == 0) {

    die("Không tìm thấy đơn hàng.");
}

$order = mysqli_fetch_assoc($result);

/*
|--------------------------------------------------------------------------
| Chỉ xác nhận khi trạng thái là Đã giao
|--------------------------------------------------------------------------
*/

if ($order['status'] != "Đã giao") {

    echo "<script>

    alert('Đơn hàng chưa được giao.');

    location='detail.php?id=" . $order_id . "';

    </script>";

    exit;
}

/*
|--------------------------------------------------------------------------
| Cập nhật trạng thái nhận hàng
|--------------------------------------------------------------------------
*/

$sql = "UPDATE orders
SET status='Hoàn thành'
WHERE id=?";

$stmt = mysqli_prepare($conn, $sql);

mysqli_stmt_bind_param(
    $stmt,
    "i",
    $order_id
);

if (mysqli_stmt_execute($stmt)) {

    echo "<script>

    alert('Cảm ơn bạn đã xác nhận.');

    location='detail.php?id=" . $order_id . "';

    </script>";
} else {

    echo "<script>

    alert('Có lỗi xảy ra.');

    history.back();

    </script>";
}
