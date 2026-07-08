<?php
session_start();

require_once __DIR__ . "/../../config/database.php";

if (!isset($_SESSION['user'])) {
    header("Location:/fruit_shop/login.php");
    exit;
}

if ($_SESSION['user']['role_id'] != 4) {
    die("Bạn không có quyền truy cập.");
}

$shipper_id = $_SESSION['user']['id'];

$sql = "
SELECT
    o.*
FROM orders o
WHERE o.shipper_id = ?
ORDER BY o.id DESC
";

$stmt = mysqli_prepare($conn, $sql);

mysqli_stmt_bind_param(
    $stmt,
    "i",
    $shipper_id
);

mysqli_stmt_execute($stmt);

$result = mysqli_stmt_get_result($stmt);

require_once __DIR__ . "/../../includes/header.php";
require_once __DIR__ . "/../../includes/navbar.php";
?>

<div class="flex">

    <?php include("../sidebar.php"); ?>

    <div class="flex-1 p-6">

        <div class="bg-white rounded-lg shadow">

            <div class="p-5 border-b">

                <h2 class="text-3xl font-bold">

                    <i class="fa-solid fa-truck-fast text-green-600"></i>

                    Đơn hàng được giao

                </h2>

            </div>

            <div class="overflow-x-auto">

                <table class="w-full">

                    <thead class="bg-green-600 text-white">

                        <tr>

                            <th class="p-3">Mã</th>

                            <th>Khách hàng</th>

                            <th>Điện thoại</th>

                            <th>Tổng tiền</th>

                            <th>Thanh toán</th>

                            <th>Trạng thái</th>

                            <th>Ngày đặt</th>

                            <th>Thao tác</th>

                        </tr>

                    </thead>

                    <tbody>

                        <?php

                        while ($order = mysqli_fetch_assoc($result)) {

                        ?>

                            <tr class="border-b hover:bg-gray-50">

                                <td class="text-center p-3">

                                    #<?= $order['id']; ?>

                                </td>

                                <td>

                                    <strong>

                                        <?= htmlspecialchars($order['full_name']); ?>

                                    </strong>

                                </td>

                                <td>

                                    <?= htmlspecialchars($order['phone']); ?>

                                </td>

                                <td class="text-red-600 font-bold">

                                    <?= number_format($order['total']); ?> đ

                                </td>

                                <td class="text-center">

                                    <?php

                                    switch ($order['payment_method']) {

                                        case "COD":

                                            echo '<span class="bg-yellow-100 text-yellow-700 px-3 py-1 rounded">
        COD
        </span>';

                                            break;

                                        case "BANK":

                                            echo '<span class="bg-blue-100 text-blue-700 px-3 py-1 rounded">
        Ngân hàng
        </span>';

                                            break;

                                        case "ZALOPAY":

                                            echo '<span class="bg-purple-100 text-purple-700 px-3 py-1 rounded">
        ZaloPay
        </span>';

                                            break;

                                        default:

                                            echo htmlspecialchars($order['payment_method']);
                                    }

                                    ?>

                                </td>

                                <td class="text-center">

                                    <?php

                                    $status = $order['status'];

                                    switch ($status) {

                                        case "Đang giao":

                                            echo '<span class="bg-indigo-100 text-indigo-700 px-3 py-1 rounded">
        Đang giao
        </span>';

                                            break;

                                        case "Đã giao":

                                            echo '<span class="bg-green-100 text-green-700 px-3 py-1 rounded">
        Đã giao
        </span>';

                                            break;

                                        case "Hoàn thành":

                                            echo '<span class="bg-green-600 text-white px-3 py-1 rounded">
        Hoàn thành
        </span>';

                                            break;

                                        case "Giao thất bại":

                                            echo '<span class="bg-red-100 text-red-700 px-3 py-1 rounded">
        Giao thất bại
        </span>';

                                            break;

                                        default:

                                            echo htmlspecialchars($status);
                                    }

                                    ?>

                                </td>

                                <td class="text-center">

                                    <?= date("d/m/Y H:i", strtotime($order['created_at'])); ?>

                                </td>

                                <td class="text-center">

                                    <div class="flex justify-center gap-2 flex-wrap">

                                        <a
                                            href="detail.php?id=<?= $order['id']; ?>"
                                            class="bg-blue-600 hover:bg-blue-700 text-white px-3 py-2 rounded">

                                            <i class="fa-solid fa-eye"></i>

                                        </a>

                                        <?php if ($status == "Đang giao") { ?>

                                            <a
                                                href="update_location.php?id=<?= $order['id']; ?>"
                                                class="bg-orange-500 hover:bg-orange-600 text-white px-3 py-2 rounded">

                                                <i class="fa-solid fa-location-dot"></i>

                                            </a>

                                            <a
                                                href="delivered.php?id=<?= $order['id']; ?>"
                                                onclick="return confirm('Xác nhận đã giao thành công?')"
                                                class="bg-green-600 hover:bg-green-700 text-white px-3 py-2 rounded">

                                                <i class="fa-solid fa-check"></i>

                                            </a>

                                            <a
                                                href="failed.php?id=<?= $order['id']; ?>"
                                                onclick="return confirm('Đánh dấu giao thất bại?')"
                                                class="bg-red-600 hover:bg-red-700 text-white px-3 py-2 rounded">

                                                <i class="fa-solid fa-xmark"></i>

                                            </a>

                                        <?php } ?>

                                    </div>

                                </td>

                            </tr>

                        <?php

                        }

                        ?>

                    </tbody>

                </table>

            </div>

            <?php

            /*
|--------------------------------------------------------------------------
| Thống kê đơn hàng
|--------------------------------------------------------------------------
*/

            $sql_shipping = mysqli_query(
                $conn,
                "SELECT COUNT(*) total
     FROM orders
     WHERE shipper_id={$shipper_id}
     AND status='Đang giao'"
            );

            $shipping = mysqli_fetch_assoc($sql_shipping)['total'];

            $sql_delivered = mysqli_query(
                $conn,
                "SELECT COUNT(*) total
     FROM orders
     WHERE shipper_id={$shipper_id}
     AND status='Đã giao'"
            );

            $delivered = mysqli_fetch_assoc($sql_delivered)['total'];

            $sql_finish = mysqli_query(
                $conn,
                "SELECT COUNT(*) total
     FROM orders
     WHERE shipper_id={$shipper_id}
     AND status='Hoàn thành'"
            );

            $finish = mysqli_fetch_assoc($sql_finish)['total'];

            $sql_failed = mysqli_query(
                $conn,
                "SELECT COUNT(*) total
     FROM orders
     WHERE shipper_id={$shipper_id}
     AND status='Giao thất bại'"
            );

            $failed = mysqli_fetch_assoc($sql_failed)['total'];

            ?>

            <div class="grid grid-cols-2 md:grid-cols-4 gap-5 p-6">

                <div class="bg-blue-100 rounded-lg shadow p-5 text-center">

                    <i class="fa-solid fa-truck-fast text-5xl text-blue-600"></i>

                    <div class="text-3xl font-bold mt-3">

                        <?= $shipping ?>

                    </div>

                    <div class="mt-2">

                        Đang giao

                    </div>

                </div>

                <div class="bg-green-100 rounded-lg shadow p-5 text-center">

                    <i class="fa-solid fa-circle-check text-5xl text-green-600"></i>

                    <div class="text-3xl font-bold mt-3">

                        <?= $delivered ?>

                    </div>

                    <div class="mt-2">

                        Đã giao

                    </div>

                </div>

                <div class="bg-emerald-100 rounded-lg shadow p-5 text-center">

                    <i class="fa-solid fa-box-open text-5xl text-emerald-600"></i>

                    <div class="text-3xl font-bold mt-3">

                        <?= $finish ?>

                    </div>

                    <div class="mt-2">

                        Hoàn thành

                    </div>

                </div>

                <div class="bg-red-100 rounded-lg shadow p-5 text-center">

                    <i class="fa-solid fa-triangle-exclamation text-5xl text-red-600"></i>

                    <div class="text-3xl font-bold mt-3">

                        <?= $failed ?>

                    </div>

                    <div class="mt-2">

                        Giao thất bại

                    </div>

                </div>

            </div>

            <div class="p-6">

                <div class="bg-green-50 border border-green-200 rounded-lg p-5">

                    <h3 class="text-xl font-bold mb-3">

                        <i class="fa-solid fa-circle-info text-green-600"></i>

                        Thông tin

                    </h3>

                    <ul class="list-disc ml-6 space-y-2">

                        <li>
                            📍 Nhấn biểu tượng vị trí để cập nhật GPS hiện tại.
                        </li>

                        <li>
                            ✅ Sau khi giao xong nhấn "Đã giao".
                        </li>

                        <li>
                            ❌ Nếu khách không nhận hàng chọn "Giao thất bại".
                        </li>

                        <li>
                            🗺️ Khách hàng sẽ xem được vị trí giao hàng trên Google Map.
                        </li>

                    </ul>

                </div>

            </div>

        </div>

    </div>

</div>

<?php

/*
|--------------------------------------------------------------------------
| Giải phóng bộ nhớ
|--------------------------------------------------------------------------
*/

if (isset($stmt)) {
    mysqli_stmt_close($stmt);
}

mysqli_close($conn);

require_once __DIR__ . "/../../includes/footer.php";

?>