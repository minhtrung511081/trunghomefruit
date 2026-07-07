<?php
session_start();

require_once __DIR__ . "/../config/database.php";

if (!isset($_SESSION['user'])) {
    header("Location: /fruit_shop/login.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] != 'POST') {
    header("Location: index.php");
    exit;
}

$user_id = $_SESSION['user']['id'];

$order_id = (int)$_POST['order_id'];

$rating = (int)$_POST['rating'];

$comment = trim($_POST['comment']);

if ($rating < 1 || $rating > 5) {

    die("Đánh giá không hợp lệ.");
}

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

if ($order['status'] != "Hoàn thành") {

    die("Chỉ đánh giá khi đơn hàng đã hoàn thành.");
}

/*
|--------------------------------------------------------------------------
| Lưu đánh giá cho từng sản phẩm
|--------------------------------------------------------------------------
*/

$sql = "
SELECT *
FROM order_details
WHERE order_id=?
";

$stmt = mysqli_prepare($conn, $sql);

mysqli_stmt_bind_param(
    $stmt,
    "i",
    $order_id
);

mysqli_stmt_execute($stmt);

$result = mysqli_stmt_get_result($stmt);

while ($detail = mysqli_fetch_assoc($result)) {

    $product_id = $detail['product_id'];

    /*
    |----------------------------------------------------------
    | Kiểm tra đã đánh giá chưa
    |----------------------------------------------------------
    */

    $check = mysqli_prepare(
        $conn,
        "SELECT id
        FROM reviews
        WHERE order_id=?
        AND product_id=?
        AND user_id=?"
    );

    mysqli_stmt_bind_param(
        $check,
        "iii",
        $order_id,
        $product_id,
        $user_id
    );

    mysqli_stmt_execute($check);

    $rs = mysqli_stmt_get_result($check);

    if (mysqli_num_rows($rs) > 0) {

        continue;
    }

    /*
    |----------------------------------------------------------
    | Thêm đánh giá
    |----------------------------------------------------------
    */

    $insert = mysqli_prepare(
        $conn,
        "INSERT INTO reviews(
            order_id,
            product_id,
            user_id,
            rating,
            comment
        ) VALUES(
            ?,
            ?,
            ?,
            ?,
            ?
        )"
    );

    mysqli_stmt_bind_param(
        $insert,
        "iiiis",
        $order_id,
        $product_id,
        $user_id,
        $rating,
        $comment
    );

    mysqli_stmt_execute($insert);
}

echo "<script>

alert('Đánh giá thành công.');

location='detail.php?id=" . $order_id . "';

</script>";

exit;
