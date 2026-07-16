<?php
session_start();

require_once __DIR__ . "/../../config/database.php";

if (!isset($_SESSION['user'])) {
    header("Location: /fruit_shop/login.php");
    exit;
}

if (empty($_SESSION['cart'])) {
    header("Location: index.php");
    exit;
}

$user_id = $_SESSION['user']['id'];

$address_id = (int)($_POST['address_id'] ?? 0);

$note = mysqli_real_escape_string(
    $conn,
    $_POST['note'] ?? ''
);

$payment_method = $_POST['payment_method'] ?? 'COD';

if ($payment_method == "ONLINE") {

    $payment_method =
        $_POST['online_type'] ?? 'BANK';
}

/*
|--------------------------------------------------------------------------
| Lấy địa chỉ giao hàng
|--------------------------------------------------------------------------
*/

$sqlAddress = "SELECT *
               FROM user_addresses
               WHERE id=?
               AND user_id=?";

$stmt = mysqli_prepare($conn, $sqlAddress);

mysqli_stmt_bind_param(
    $stmt,
    "ii",
    $address_id,
    $user_id
);

mysqli_stmt_execute($stmt);

$result = mysqli_stmt_get_result($stmt);

$address = mysqli_fetch_assoc($result);

if (!$address) {

    die("Địa chỉ giao hàng không tồn tại.");
}

$full_name = mysqli_real_escape_string(
    $conn,
    $address['full_name']
);

$phone = mysqli_real_escape_string(
    $conn,
    $address['phone']
);

$address_text = mysqli_real_escape_string(
    $conn,
    $address['address']
);

$latitude = $address['latitude'];

$longitude = $address['longitude'];

$total = 0;

/*
|--------------------------------------------------------------------------
| Tính tổng tiền
|--------------------------------------------------------------------------
*/

foreach ($_SESSION['cart'] as $product_id => $qty) {

    $product_id = (int)$product_id;

    $qty = (float)$qty;

    $sqlProduct = "SELECT *
                   FROM products
                   WHERE id=$product_id
                   LIMIT 1";

    $rs = mysqli_query($conn, $sqlProduct);

    if (!$product = mysqli_fetch_assoc($rs)) {

        continue;
    }

    $total += $product['price'] * $qty;
}
/*
|--------------------------------------------------------------------------
| Lưu đơn hàng
|--------------------------------------------------------------------------
*/

$sql = "
SELECT seller_id
FROM products
WHERE id = ?
";

$stmt = mysqli_prepare($conn, $sql);

mysqli_stmt_bind_param($stmt, "i", $product_id);

mysqli_stmt_execute($stmt);

$result = mysqli_stmt_get_result($stmt);

$product = mysqli_fetch_assoc($result);

$seller_id = $product['seller_id'];

$sqlOrder = "INSERT INTO orders (
    user_id,
    full_name,
    phone,
    address,
    note,
    total,
    latitude,
    longitude,
    payment_method,
    payment_status
) VALUES (
    ?,
    ?,
    ?,
    ?,
    ?,
    ?,
    ?,
    ?,
    ?,
    ?
)";

$stmtOrder = mysqli_prepare($conn, $sqlOrder);

$payment_status = ($payment_method == "COD")
    ? "Chưa thanh toán"
    : "Đã thanh toán";

mysqli_stmt_bind_param(
    $stmtOrder,
    "issssdssss",
    $user_id,
    $full_name,
    $phone,
    $address_text,
    $note,
    $total,
    $latitude,
    $longitude,
    $payment_method,
    $payment_status
);

if (!mysqli_stmt_execute($stmtOrder)) {
    die("Lỗi tạo đơn hàng: " . mysqli_error($conn));
}

$order_id = mysqli_insert_id($conn);

/*
|--------------------------------------------------------------------------
| Lưu chi tiết đơn hàng
|--------------------------------------------------------------------------
*/

foreach ($_SESSION['cart'] as $product_id => $qty) {

    $product_id = (int)$product_id;
    $qty = (float)$qty;

    $sqlProduct = "SELECT *
                   FROM products
                   WHERE id=$product_id
                   LIMIT 1";

    $rs = mysqli_query($conn, $sqlProduct);

    if (!$product = mysqli_fetch_assoc($rs)) {
        continue;
    }

    $price = $product['price'];

    $sqlDetail = "INSERT INTO order_details(
        order_id,
        product_id,
        quantity,
        price
    ) VALUES (
        ?,
        ?,
        ?,
        ?
    )";

    $stmtDetail = mysqli_prepare($conn, $sqlDetail);

    mysqli_stmt_bind_param(
        $stmtDetail,
        "iiid",
        $order_id,
        $product_id,
        $qty,
        $price
    );

    mysqli_stmt_execute($stmtDetail);

    /*
    |----------------------------------------------------------
    | Trừ tồn kho (nếu bảng products có cột stock hoặc quantity)
    |----------------------------------------------------------
    */

    // mysqli_query(
    //     $conn,
    //     "UPDATE products
    //      SET quantity = quantity - $qty
    //      WHERE id = $product_id"
    // );
}

/*
|--------------------------------------------------------------------------
| Xóa giỏ hàng
|--------------------------------------------------------------------------
*/

unset($_SESSION['cart']);

/*
|--------------------------------------------------------------------------
| Chuyển trang
|--------------------------------------------------------------------------
*/

header("Location: success.php");
exit;
