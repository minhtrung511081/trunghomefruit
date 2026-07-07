<?php
session_start();

require_once __DIR__ . "/../../config/database.php";

if (!isset($_SESSION['user'])) {

    header("Location:/fruit_shop/login.php");

    exit;
}

if ($_SESSION['user']['role_name'] != "Seller") {

    die("Bạn không có quyền truy cập.");
}

require_once __DIR__ . "/../../includes/header.php";
require_once __DIR__ . "/../../includes/navbar.php";

$sql = "
SELECT
    o.*
FROM orders o
ORDER BY o.id DESC
";

$result = mysqli_query($conn, $sql);
?>


<div class="p-6">
    <div class="bg-white rounded-lg shadow">

        <div class="p-5 border-b">

            <h2 class="text-3xl font-bold">

                <i class="fa-solid fa-box"></i>

                Quản lý đơn hàng

            </h2>

        </div>

        <div class="overflow-x-auto">

            <table class="w-full">

                <thead class="bg-green-600 text-white">

                    <tr>

                        <th class="p-3">

                            Mã

                        </th>

                        <th>

                            Khách hàng

                        </th>

                        <th>

                            Điện thoại

                        </th>

                        <th>

                            Tổng tiền

                        </th>

                        <th>

                            Thanh toán

                        </th>

                        <th>

                            Trạng thái

                        </th>

                        <th>

                            Ngày đặt

                        </th>

                        <th>

                            Thao tác

                        </th>

                    </tr>

                </thead>

                <tbody>

                    <?php

                    while ($order = mysqli_fetch_assoc($result)) {

                    ?>

                        <tr class="border-b hover:bg-gray-50">

                            <td class="p-3 text-center">

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

                                        echo '<span class="bg-yellow-100 text-yellow-700 px-3 py-1 rounded">COD</span>';

                                        break;

                                    case "BANK":

                                        echo '<span class="bg-blue-100 text-blue-700 px-3 py-1 rounded">Ngân hàng</span>';

                                        break;

                                    case "ZALOPAY":

                                        echo '<span class="bg-purple-100 text-purple-700 px-3 py-1 rounded">ZaloPay</span>';

                                        break;

                                    default:

                                        echo htmlspecialchars($order['payment_method']);
                                }

                                ?>

                            </td>

                            <td class="text-center">

                                <?php

                                $status = $order['status'] ?? 'Đang xử lý';

                                switch ($status) {

                                    case 'Đang xử lý':

                                        echo '<span class="bg-gray-100 text-gray-700 px-3 py-1 rounded">Đang xử lý</span>';

                                        break;

                                    case 'Đã xác nhận':

                                        echo '<span class="bg-blue-100 text-blue-700 px-3 py-1 rounded">Đã xác nhận</span>';

                                        break;

                                    case 'Đang giao':

                                        echo '<span class="bg-indigo-100 text-indigo-700 px-3 py-1 rounded">Đang giao</span>';

                                        break;

                                    case 'Đã giao':

                                        echo '<span class="bg-green-100 text-green-700 px-3 py-1 rounded">Đã giao</span>';

                                        break;

                                    case 'Hoàn thành':

                                        echo '<span class="bg-green-600 text-white px-3 py-1 rounded">Hoàn thành</span>';

                                        break;

                                    case 'Đã hủy':

                                        echo '<span class="bg-red-100 text-red-700 px-3 py-1 rounded">Đã hủy</span>';

                                        break;

                                    default:

                                        echo htmlspecialchars($status);
                                }

                                ?>

                            </td>

                            <td class="text-center">

                                <?= date("d/m/Y H:i", strtotime($order['created_at'])); ?>

                            </td>

                            <td class="text-center space-x-2">

                                <a
                                    href="/fruit_shop/seller/orders/detail.php?id=<?= $order['id']; ?>"
                                    class="bg-blue-600 hover:bg-blue-700 text-white px-3 py-2 rounded">

                                    <i class="fa-solid fa-eye"></i>

                                </a>

                                <?php if (($order['status'] ?? 'Đang xử lý') == 'Đang xử lý') { ?>

                                    <a
                                        href="/fruit_shop/seller/orders/confirm.php?id=<?= $order['id']; ?>"
                                        onclick="return confirm('Xác nhận đơn hàng này?');"
                                        class="bg-green-600 hover:bg-green-700 text-white px-3 py-2 rounded">

                                        <i class="fa-solid fa-check"></i>

                                    </a>

                                <?php } ?>

                                <?php if (($order['status'] ?? '') == 'Đã xác nhận') { ?>

                                    <a
                                        href="/fruit_shop/seller/orders/assign_shipper.php?id=<?= $order['id']; ?>"
                                        class="bg-orange-500 hover:bg-orange-600 text-white px-3 py-2 rounded">

                                        <i class="fa-solid fa-truck"></i>

                                    </a>

                                <?php } ?>

                                <?php if (($order['status'] ?? '') == 'Đã hủy') { ?>

                                    <span class="text-red-600 font-semibold">

                                        Đã hủy

                                    </span>

                                <?php } ?>

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

        $sql_processing = mysqli_query(
            $conn,
            "SELECT COUNT(*) total
     FROM orders
     WHERE status='Đang xử lý'"
        );

        $processing = mysqli_fetch_assoc($sql_processing)['total'];

        $sql_confirmed = mysqli_query(
            $conn,
            "SELECT COUNT(*) total
     FROM orders
     WHERE status='Đã xác nhận'"
        );

        $confirmed = mysqli_fetch_assoc($sql_confirmed)['total'];

        $sql_shipping = mysqli_query(
            $conn,
            "SELECT COUNT(*) total
     FROM orders
     WHERE status='Đang giao'"
        );

        $shipping = mysqli_fetch_assoc($sql_shipping)['total'];

        $sql_finish = mysqli_query(
            $conn,
            "SELECT COUNT(*) total
     FROM orders
     WHERE status='Hoàn thành'"
        );

        $finish = mysqli_fetch_assoc($sql_finish)['total'];

        ?>

        <div class="grid grid-cols-2 md:grid-cols-4 gap-5 p-6">

            <div class="bg-yellow-100 rounded-lg p-5 text-center">

                <i class="fa-solid fa-clock text-4xl text-yellow-600"></i>

                <div class="text-3xl font-bold mt-3">

                    <?= $processing ?>

                </div>

                <div>

                    Đang xử lý

                </div>

            </div>

            <div class="bg-blue-100 rounded-lg p-5 text-center">

                <i class="fa-solid fa-circle-check text-4xl text-blue-600"></i>

                <div class="text-3xl font-bold mt-3">

                    <?= $confirmed ?>

                </div>

                <div>

                    Đã xác nhận

                </div>

            </div>

            <div class="bg-indigo-100 rounded-lg p-5 text-center">

                <i class="fa-solid fa-truck text-4xl text-indigo-600"></i>

                <div class="text-3xl font-bold mt-3">

                    <?= $shipping ?>

                </div>

                <div>

                    Đang giao

                </div>

            </div>

            <div class="bg-green-100 rounded-lg p-5 text-center">

                <i class="fa-solid fa-box-open text-4xl text-green-600"></i>

                <div class="text-3xl font-bold mt-3">

                    <?= $finish ?>

                </div>

                <div>

                    Hoàn thành

                </div>

            </div>

        </div>

    </div>

</div>