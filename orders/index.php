<?php
session_start();

require_once __DIR__ . "/../config/database.php";

if (!isset($_SESSION['user'])) {
    header("Location: /fruit_shop/login.php");
    exit;
}

$user_id = $_SESSION['user']['id'];

require_once __DIR__ . "/../includes/header.php";
require_once __DIR__ . "/../includes/navbar.php";

$sql = "
SELECT *
FROM orders
WHERE user_id = ?
ORDER BY id DESC
";

$stmt = mysqli_prepare($conn, $sql);

mysqli_stmt_bind_param($stmt, "i", $user_id);

mysqli_stmt_execute($stmt);

$result = mysqli_stmt_get_result($stmt);
?>

<div class="max-w-7xl mx-auto mt-8">

    <div class="bg-white rounded-lg shadow p-6">

        <h2 class="text-3xl font-bold mb-6">

            <i class="fa-solid fa-box"></i>

            Đơn hàng của tôi

        </h2>

        <?php if (mysqli_num_rows($result) == 0) { ?>

            <div class="text-center py-16">

                <i class="fa-solid fa-cart-shopping text-6xl text-gray-400"></i>

                <h3 class="text-2xl mt-6 font-bold">

                    Bạn chưa có đơn hàng nào

                </h3>

                <a
                    href="/fruit_shop/index.php"
                    class="inline-block mt-8 bg-green-600 hover:bg-green-700 text-white px-6 py-3 rounded">

                    Tiếp tục mua sắm

                </a>

            </div>

        <?php } else { ?>

            <div class="overflow-x-auto">

                <table class="w-full border">

                    <thead class="bg-green-600 text-white">

                        <tr>

                            <th class="p-3">Mã ĐH</th>

                            <th>Ngày đặt</th>

                            <th>Người nhận</th>

                            <th>SĐT</th>

                            <th>Tổng tiền</th>

                            <th>Thanh toán</th>

                            <th>Trạng thái</th>

                            <th>Thao tác</th>

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

                                <td class="text-center">

                                    <?= date("d/m/Y H:i", strtotime($order['created_at'])); ?>

                                </td>

                                <td>

                                    <?= htmlspecialchars($order['full_name']); ?>

                                </td>

                                <td>

                                    <?= htmlspecialchars($order['phone']); ?>

                                </td>

                                <td class="text-red-600 font-bold text-right pr-4">

                                    <?= number_format($order['total']); ?> đ

                                </td>

                                <td class="text-center">

                                    <?php

                                    if ($order['payment_method'] == "COD") {

                                        echo '<span class="bg-yellow-100 text-yellow-700 px-3 py-1 rounded">COD</span>';
                                    } elseif ($order['payment_method'] == "BANK") {

                                        echo '<span class="bg-blue-100 text-blue-700 px-3 py-1 rounded">Ngân hàng</span>';
                                    } elseif ($order['payment_method'] == "ZALOPAY") {

                                        echo '<span class="bg-purple-100 text-purple-700 px-3 py-1 rounded">ZaloPay</span>';
                                    } else {

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

                                        case 'Đang giao':
                                            echo '<span class="bg-blue-100 text-blue-700 px-3 py-1 rounded">Đang giao</span>';
                                            break;

                                        case 'Đã giao':
                                            echo '<span class="bg-green-100 text-green-700 px-3 py-1 rounded">Đã giao</span>';
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

                                    <a
                                        href="/fruit_shop/orders/detail.php?id=<?= $order['id']; ?>"
                                        class="bg-blue-600 hover:bg-blue-700 text-white px-3 py-2 rounded inline-block">

                                        <i class="fa-solid fa-eye"></i>

                                        Chi tiết

                                    </a>

                                    <?php if (($order['status'] ?? 'Đang xử lý') == 'Đang xử lý') { ?>

                                        <a
                                            href="/fruit_shop/orders/cancel.php?id=<?= $order['id']; ?>"
                                            onclick="return confirm('Bạn có chắc muốn hủy đơn hàng này?')"
                                            class="bg-red-600 hover:bg-red-700 text-white px-3 py-2 rounded inline-block ml-2">

                                            <i class="fa-solid fa-xmark"></i>

                                            Hủy

                                        </a>

                                    <?php } ?>

                                </td>

                            </tr>

                        <?php

                        }

                        ?>
                    </tbody>

                </table>

            </div>

        <?php } ?>

    </div>

</div>

<?php
require_once __DIR__ . "/../includes/footer.php";
?>