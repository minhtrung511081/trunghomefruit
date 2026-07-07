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

$user_id = $_SESSION['user']['id'];

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

    die("Đơn hàng không tồn tại.");
}

$order = mysqli_fetch_assoc($result);

require_once __DIR__ . "/../includes/header.php";
require_once __DIR__ . "/../includes/navbar.php";
?>

<div class="max-w-7xl mx-auto mt-8">

    <div class="bg-white rounded-lg shadow p-6">

        <div class="flex justify-between items-center mb-6">

            <h2 class="text-3xl font-bold">

                <i class="fa-solid fa-box"></i>

                Chi tiết đơn hàng #<?= $order['id']; ?>

            </h2>

            <a
                href="/fruit_shop/orders/index.php"
                class="bg-gray-600 hover:bg-gray-700 text-white px-5 py-2 rounded">

                <i class="fa-solid fa-arrow-left"></i>

                Quay lại

            </a>

        </div>

        <div class="grid md:grid-cols-2 gap-6">

            <div class="border rounded-lg p-5">

                <h3 class="font-bold text-xl mb-4">

                    Thông tin người nhận

                </h3>

                <div class="mb-3">

                    <strong>Họ tên:</strong>

                    <?= htmlspecialchars($order['full_name']); ?>

                </div>

                <div class="mb-3">

                    <strong>Số điện thoại:</strong>

                    <?= htmlspecialchars($order['phone']); ?>

                </div>

                <div class="mb-3">

                    <strong>Địa chỉ:</strong>

                    <?= nl2br(htmlspecialchars($order['address'])); ?>

                </div>

                <div class="mb-3">

                    <strong>Ghi chú:</strong>

                    <?= htmlspecialchars($order['note']); ?>

                </div>

            </div>

            <div class="border rounded-lg p-5">

                <h3 class="font-bold text-xl mb-4">

                    Thông tin đơn hàng

                </h3>

                <div class="mb-3">

                    <strong>Ngày đặt:</strong>

                    <?= date("d/m/Y H:i", strtotime($order['created_at'])); ?>

                </div>

                <div class="mb-3">

                    <strong>Thanh toán:</strong>

                    <?= htmlspecialchars($order['payment_method']); ?>

                </div>

                <div class="mb-3">

                    <strong>Trạng thái thanh toán:</strong>

                    <?= htmlspecialchars($order['payment_status']); ?>

                </div>

                <div class="mb-3">

                    <strong>Trạng thái đơn:</strong>

                    <?= htmlspecialchars($order['status']); ?>

                </div>

            </div>

        </div>

        <div class="mt-8">

            <h3 class="text-2xl font-bold mb-4">

                Danh sách sản phẩm

            </h3>

            <div class="overflow-x-auto">

                <table class="w-full border">

                    <thead class="bg-green-600 text-white">

                        <tr>

                            <th class="p-3">Ảnh</th>

                            <th>Tên sản phẩm</th>

                            <th>Đơn giá</th>

                            <th>Số lượng</th>

                            <th>Thành tiền</th>

                        </tr>

                    </thead>

                    <tbody>

                        <?php

                        $total = 0;

                        $sqlDetail = "
SELECT
    od.*,
    p.product_name,
    p.image,
    p.unit
FROM order_details od
INNER JOIN products p
ON od.product_id=p.id
WHERE od.order_id=?";

                        $stmtDetail = mysqli_prepare($conn, $sqlDetail);

                        mysqli_stmt_bind_param(
                            $stmtDetail,
                            "i",
                            $order_id
                        );

                        mysqli_stmt_execute($stmtDetail);

                        $resultDetail = mysqli_stmt_get_result($stmtDetail);

                        while ($item = mysqli_fetch_assoc($resultDetail)) {

                            $thanhTien = $item['price'] * $item['quantity'];

                            $total += $thanhTien;

                        ?>

                            <tr class="border-b hover:bg-gray-50">

                                <td class="p-3 text-center">

                                    <?php

                                    $image = !empty($item['image'])
                                        ? "/fruit_shop/assets/images/products/" . $item['image']
                                        : "/fruit_shop/assets/images/products/no-image.png";

                                    ?>

                                    <img
                                        src="<?= $image ?>"
                                        class="w-20 h-20 object-cover rounded border mx-auto">

                                </td>

                                <td>

                                    <div class="font-bold">

                                        <?= htmlspecialchars($item['product_name']); ?>

                                    </div>

                                </td>

                                <td class="text-center text-red-600">

                                    <?= number_format($item['price']); ?> đ

                                </td>

                                <td class="text-center">

                                    <?= $item['quantity']; ?>

                                </td>

                                <td class="text-right pr-4 font-bold text-red-600">

                                    <?= number_format($thanhTien); ?> đ

                                </td>

                            </tr>

                        <?php

                        }

                        ?>

                    </tbody>

                </table>

            </div>

            <div class="mt-6 flex justify-end">

                <div class="text-2xl font-bold">

                    Tổng thanh toán :

                    <span class="text-red-600">

                        <?= number_format($total); ?> đ

                    </span>

                </div>

            </div>

            <div class="mt-8">

                <div class="grid md:grid-cols-2 gap-6">

                    <div class="border rounded-lg p-5">

                        <h3 class="text-xl font-bold mb-4">

                            <i class="fa-solid fa-credit-card"></i>

                            Thông tin thanh toán

                        </h3>

                        <table class="w-full">

                            <tr>

                                <td class="py-2 font-semibold">

                                    Phương thức

                                </td>

                                <td>

                                    <?=
                                    htmlspecialchars($order['payment_method']);
                                    ?>

                                </td>

                            </tr>

                            <tr>

                                <td class="py-2 font-semibold">

                                    Trạng thái thanh toán

                                </td>

                                <td>

                                    <?php

                                    if ($order['payment_status'] == "Đã thanh toán") {

                                    ?>

                                        <span class="bg-green-100 text-green-700 px-3 py-1 rounded">

                                            Đã thanh toán

                                        </span>

                                    <?php

                                    } else {

                                    ?>

                                        <span class="bg-yellow-100 text-yellow-700 px-3 py-1 rounded">

                                            Chưa thanh toán

                                        </span>

                                    <?php

                                    }

                                    ?>

                                </td>

                            </tr>

                            <tr>

                                <td class="py-2 font-semibold">

                                    Tổng tiền

                                </td>

                                <td class="text-red-600 font-bold text-xl">

                                    <?= number_format($total) ?>

                                    đ

                                </td>

                            </tr>

                        </table>

                    </div>

                    <div class="border rounded-lg p-5">

                        <h3 class="text-xl font-bold mb-4">

                            <i class="fa-solid fa-truck"></i>

                            Thông tin giao hàng

                        </h3>

                        <table class="w-full">

                            <tr>

                                <td class="py-2 font-semibold">

                                    Trạng thái

                                </td>

                                <td>

                                    <?php

                                    $status = $order['status'];

                                    switch ($status) {

                                        case "Đang xử lý":

                                            echo '<span class="bg-gray-100 text-gray-700 px-3 py-1 rounded">

Đang xử lý

</span>';

                                            break;

                                        case "Đang giao":

                                            echo '<span class="bg-blue-100 text-blue-700 px-3 py-1 rounded">

Đang giao

</span>';

                                            break;

                                        case "Đã giao":

                                            echo '<span class="bg-green-100 text-green-700 px-3 py-1 rounded">

Đã giao

</span>';

                                            break;

                                        case "Đã hủy":

                                            echo '<span class="bg-red-100 text-red-700 px-3 py-1 rounded">

Đã hủy

</span>';

                                            break;

                                        default:

                                            echo htmlspecialchars($status);
                                    }

                                    ?>

                                </td>

                            </tr>

                            <tr>

                                <td class="py-2 font-semibold">

                                    Ngày đặt

                                </td>

                                <td>

                                    <?=

                                    date(
                                        "d/m/Y H:i",
                                        strtotime($order['created_at'])
                                    );

                                    ?>

                                </td>

                            </tr>

                            <?php

                            if (!empty($order['delivery_date'])) {

                            ?>

                                <tr>

                                    <td class="py-2 font-semibold">

                                        Ngày giao

                                    </td>

                                    <td>

                                        <?=

                                        date(
                                            "d/m/Y",
                                            strtotime($order['delivery_date'])
                                        );

                                        ?>

                                    </td>

                                </tr>

                            <?php

                            }

                            ?>

                        </table>

                    </div>

                </div>

            </div>

            <div class="mt-8">

                <?php

                if (
                    !empty($order['latitude']) &&
                    !empty($order['longitude'])
                ) {

                ?>

                    <div class="border rounded-lg p-5">

                        <h3 class="text-xl font-bold mb-4">

                            <i class="fa-solid fa-location-dot text-red-500"></i>

                            Vị trí giao hàng

                        </h3>

                        <div
                            id="map"
                            style="height:420px;border-radius:10px;">
                        </div>

                    </div>

                    <script>
                        document.addEventListener("DOMContentLoaded", function() {

                            var lat = <?= (float)$order['latitude']; ?>;

                            var lng = <?= (float)$order['longitude']; ?>;

                            var map = L.map("map").setView([lat, lng], 16);

                            L.tileLayer(

                                'https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png',

                                {

                                    maxZoom: 19

                                }

                            ).addTo(map);

                            L.marker([lat, lng])

                                .addTo(map)

                                .bindPopup("Địa chỉ giao hàng")

                                .openPopup();

                        });
                    </script>

                <?php

                }

                ?>

            </div>

            <div class="mt-10 flex justify-between">

                <a
                    href="/fruit_shop/orders/index.php"
                    class="bg-gray-600 hover:bg-gray-700 text-white px-6 py-3 rounded">

                    <i class="fa-solid fa-arrow-left"></i>

                    Quay lại

                </a>

                <div>

                    <?php

                    $status = $order['status'];

                    if ($status == "Đang xử lý") {

                    ?>

                        <a
                            href="/fruit_shop/orders/cancel.php?id=<?= $order['id']; ?>"
                            onclick="return confirm('Bạn có chắc muốn hủy đơn hàng?');"
                            class="bg-red-600 hover:bg-red-700 text-white px-6 py-3 rounded">

                            <i class="fa-solid fa-xmark"></i>

                            Hủy đơn

                        </a>

                    <?php

                    }

                    ?>

                    <?php

                    if ($status == "Đã giao") {

                    ?>

                        <a
                            href="/fruit_shop/orders/received.php?id=<?= $order['id']; ?>"
                            onclick="return confirm('Xác nhận đã nhận hàng?');"
                            class="bg-green-600 hover:bg-green-700 text-white px-6 py-3 rounded">

                            <i class="fa-solid fa-circle-check"></i>

                            Đã nhận hàng

                        </a>

                    <?php

                    }

                    ?>

                </div>

            </div>

        </div>

        <?php

        /*
|--------------------------------------------------------------------------
| Đánh giá sản phẩm sau khi giao thành công
|--------------------------------------------------------------------------
*/

        if (($order['status'] ?? '') == "Đã giao") {

        ?>

            <div class="bg-white rounded-lg shadow mt-8 p-6">

                <h3 class="text-2xl font-bold mb-5">

                    <i class="fa-solid fa-star text-yellow-500"></i>

                    Đánh giá đơn hàng

                </h3>

                <form
                    action="/fruit_shop/orders/review.php"
                    method="POST">

                    <input
                        type="hidden"
                        name="order_id"
                        value="<?= $order['id']; ?>">

                    <div class="mb-4">

                        <label class="font-semibold block mb-2">

                            Đánh giá sao

                        </label>

                        <select
                            name="rating"
                            class="border rounded p-2 w-40"
                            required>

                            <option value="5">⭐⭐⭐⭐⭐ (5 sao)</option>

                            <option value="4">⭐⭐⭐⭐ (4 sao)</option>

                            <option value="3">⭐⭐⭐ (3 sao)</option>

                            <option value="2">⭐⭐ (2 sao)</option>

                            <option value="1">⭐ (1 sao)</option>

                        </select>

                    </div>

                    <div class="mb-4">

                        <label class="font-semibold block mb-2">

                            Nhận xét

                        </label>

                        <textarea
                            name="comment"
                            rows="5"
                            class="border rounded p-3 w-full"
                            placeholder="Nhập nhận xét của bạn..."></textarea>

                    </div>

                    <button
                        type="submit"
                        class="bg-yellow-500 hover:bg-yellow-600 text-white px-6 py-3 rounded">

                        <i class="fa-solid fa-paper-plane"></i>

                        Gửi đánh giá

                    </button>

                </form>

            </div>

        <?php

        }

        ?>

    </div>

    <?php

    require_once __DIR__ . "/../includes/footer.php";

    ?>