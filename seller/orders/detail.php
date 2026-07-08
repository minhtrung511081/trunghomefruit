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

$sql = "
SELECT
o.*,
u.warehouse_address,
u.warehouse_latitude,
u.warehouse_longitude
FROM orders o
JOIN order_details od ON od.order_id=o.id
JOIN products p ON p.id=od.product_id
JOIN users u ON u.id=p.seller_id
WHERE o.id=?
GROUP BY o.id
";

$stmt = mysqli_prepare($conn, $sql);

mysqli_stmt_bind_param(
    $stmt,
    "i",
    $order_id
);

mysqli_stmt_execute($stmt);

$result = mysqli_stmt_get_result($stmt);

if (mysqli_num_rows($result) == 0) {

    die("Không tìm thấy đơn hàng.");
}

$order = mysqli_fetch_assoc($result);

?>


<div class="p-6">

    <div class="bg-white rounded-lg shadow p-6">

        <div class="flex justify-between items-center mb-6">

            <h2 class="text-3xl font-bold">

                <i class="fa-solid fa-box"></i>

                Chi tiết đơn hàng #<?= $order['id']; ?>

            </h2>

            <button
                type="button"
                id="btnBackOrder"
                class="bg-gray-600 hover:bg-gray-700 text-white px-6 py-3 rounded">

                <i class="fa-solid fa-arrow-left"></i>

                Quay lại

            </button>

        </div>

        <div class="grid md:grid-cols-2 gap-6">

            <div class="border rounded-lg p-5">

                <h3 class="text-xl font-bold mb-4">

                    Thông tin khách hàng

                </h3>

                <p>

                    <strong>Họ tên:</strong>

                    <?= htmlspecialchars($order['full_name']); ?>

                </p>

                <p class="mt-3">

                    <strong>Số điện thoại:</strong>

                    <?= htmlspecialchars($order['phone']); ?>

                </p>

                <p class="mt-3">

                    <strong>Địa chỉ:</strong>

                    <?= nl2br(htmlspecialchars($order['address'])); ?>

                </p>

                <p class="mt-3">

                    <strong>Ghi chú:</strong>

                    <?= htmlspecialchars($order['note']); ?>

                </p>

            </div>

            <div class="border rounded-lg p-5">

                <h3 class="text-xl font-bold mb-4">

                    Thông tin đơn hàng

                </h3>

                <p>

                    <strong>Ngày đặt:</strong>

                    <?= date("d/m/Y H:i", strtotime($order['created_at'])); ?>

                </p>

                <p class="mt-3">

                    <strong>Thanh toán:</strong>

                    <?= htmlspecialchars($order['payment_method']); ?>

                </p>

                <p class="mt-3">

                    <strong>Trạng thái thanh toán:</strong>

                    <?= htmlspecialchars($order['payment_status']); ?>

                </p>

                <p class="mt-3">

                    <strong>Trạng thái:</strong>

                    <?= htmlspecialchars($order['status']); ?>

                </p>

            </div>

        </div>

        <div class="mt-8">

            <h3 class="text-2xl font-bold mb-5">

                Danh sách sản phẩm

            </h3>

            <div class="overflow-x-auto">

                <table class="w-full border">

                    <thead class="bg-green-600 text-white">

                        <tr>

                            <th class="p-3">Ảnh</th>

                            <th>Tên sản phẩm</th>

                            <th>Đơn giá</th>

                            <th>SL</th>

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
    ON od.product_id = p.id
WHERE od.order_id = ?
ORDER BY od.id ASC
";

                        $stmtDetail = mysqli_prepare($conn, $sqlDetail);

                        mysqli_stmt_bind_param(
                            $stmtDetail,
                            "i",
                            $order_id
                        );

                        mysqli_stmt_execute($stmtDetail);

                        $resultDetail = mysqli_stmt_get_result($stmtDetail);

                        while ($item = mysqli_fetch_assoc($resultDetail)) {

                            $subTotal = $item['price'] * $item['quantity'];

                            $total += $subTotal;

                            $image = !empty($item['image'])
                                ? "/fruit_shop/assets/images/products/" . $item['image']
                                : "/fruit_shop/assets/images/products/no-image.png";
                        ?>

                            <tr class="border-b hover:bg-gray-50">

                                <td class="p-3 text-center">

                                    <img
                                        src="<?= $image; ?>"
                                        class="w-20 h-20 object-cover rounded border mx-auto">

                                </td>

                                <td>

                                    <div class="font-bold">

                                        <?= htmlspecialchars($item['product_name']); ?>

                                    </div>

                                    <div class="text-gray-500 text-sm mt-1">

                                        Đơn vị:

                                        <?= htmlspecialchars($item['unit']); ?>

                                    </div>

                                </td>

                                <td class="text-center text-red-600 font-semibold">

                                    <?= number_format($item['price']); ?> đ

                                </td>

                                <td class="text-center">

                                    <?= $item['quantity']; ?>

                                    <?= htmlspecialchars($item['unit']); ?>

                                </td>

                                <td class="text-right pr-4 font-bold text-red-600">

                                    <?= number_format($subTotal); ?> đ

                                </td>

                            </tr>

                        <?php

                        }

                        ?>

                    </tbody>

                    <tfoot>

                        <tr class="bg-gray-100">

                            <td colspan="4" class="text-right font-bold p-4">

                                Tổng thanh toán

                            </td>

                            <td class="text-right pr-4 text-2xl font-bold text-red-600">

                                <?= number_format($total); ?> đ

                            </td>

                        </tr>

                    </tfoot>

                </table>

            </div>

        </div>

        <div class="grid md:grid-cols-2 gap-6 mt-8">

            <!-- Thông tin giao hàng -->
            <div class="border rounded-lg p-5">

                <h3 class="text-xl font-bold mb-4">

                    <i class="fa-solid fa-truck"></i>

                    Thông tin giao hàng

                </h3>

                <table class="w-full">

                    <tr>

                        <td class="py-2 font-semibold" width="180">

                            Trạng thái đơn

                        </td>

                        <td>

                            <?php

                            $status = $order['status'];

                            switch ($status) {

                                case "Chờ xác nhận":

                                    echo '<span class="bg-yellow-100 text-yellow-700 px-3 py-1 rounded">
        Đang xử lý
        </span>';

                                    break;

                                case "Đã xác nhận":

                                    echo '<span class="bg-blue-100 text-blue-700 px-3 py-1 rounded">
        Đã xác nhận
        </span>';

                                    break;

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
                                case "Đã hủy":
                                    echo '<span class="bg-red-100 text-red-700 px-3 py-1 rounded">
            Đã hủy
          </span>';
                                    break;
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

                            Phương thức

                        </td>

                        <td>

                            <?= htmlspecialchars($order['payment_method']); ?>

                        </td>

                    </tr>

                    <tr>

                        <td class="py-2 font-semibold">

                            Thanh toán

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

                            <?= number_format($total); ?> đ

                        </td>

                    </tr>

                </table>

            </div>

            <!-- Bản đồ -->



            <div class="border rounded-lg p-5">

                <h3 class="text-xl font-bold mb-4">
                    <i class="fa-solid fa-location-dot text-red-500"></i>
                    Thông tin giao hàng
                </h3>

                <div class="mb-3">
                    <b>🏪 Địa chỉ kho hàng</b><br>

                    <span class="text-green-700">
                        <?= htmlspecialchars($order['warehouse_address']) ?>
                    </span>
                </div>

                <div class="mb-3">
                    <b>📍 Địa chỉ giao hàng</b><br>

                    <span class="text-blue-700">
                        <?= htmlspecialchars($order['address']) ?>
                    </span>
                </div>

                <div class="grid grid-cols-2 gap-4 mb-4">

                    <div>
                        <b>Khoảng cách:</b><br>
                        <span id="distance">Đang tính...</span>
                    </div>

                    <div>
                        <b>Thời gian:</b><br>
                        <span id="time">Đang tính...</span>
                    </div>

                </div>

                <div id="map" style="height:420px;border-radius:10px;"></div>

            </div>

        </div>

        <div class="mt-8 flex justify-between items-center">


            <div class="space-x-2">

                <?php

                $status = $order['status'];


                if ($status == "Chờ xác nhận") {

                ?>

                    <a
                        href="#"
                        class="btn-confirm-order bg-green-600 text-white px-4 py-2 rounded"
                        data-id="<?= $order['id'] ?>">
                        Xác nhận
                    </a>

                    <a
                        href="/fruit_shop/seller/orders/cancel.php?id=<?= $order['id']; ?>"
                        onclick="return confirm('Bạn chắc chắn muốn hủy đơn?')"
                        class="bg-red-600 hover:bg-red-700 text-white px-5 py-3 rounded">

                        <i class="fa-solid fa-xmark"></i>

                        Hủy đơn

                    </a>

                <?php

                } elseif ($status == "Đã xác nhận") {

                ?>

                    <a
                        href="/fruit_shop/seller/orders/assign_shipper.php?id=<?= $order['id']; ?>"
                        class="bg-blue-600 hover:bg-blue-700 text-white px-5 py-3 rounded">

                        <i class="fa-solid fa-truck"></i>

                        Giao cho Shipper

                    </a>

                <?php

                } elseif ($status == "Đang giao") {

                ?>

                    <span
                        class="bg-indigo-100 text-indigo-700 px-5 py-3 rounded inline-block">

                        <i class="fa-solid fa-truck-fast"></i>

                        Đơn hàng đang được giao

                    </span>

                <?php

                } elseif ($status == "Đã giao") {

                ?>

                    <span
                        class="bg-green-100 text-green-700 px-5 py-3 rounded inline-block">

                        <i class="fa-solid fa-circle-check"></i>

                        Đã giao thành công

                    </span>

                <?php

                } elseif ($status == "Hoàn thành") {

                ?>

                    <span
                        class="bg-green-600 text-white px-5 py-3 rounded inline-block">

                        <i class="fa-solid fa-award"></i>

                        Đơn hàng hoàn thành

                    </span>

                <?php

                } elseif ($status == "Đã hủy") {

                ?>

                    <span
                        class="bg-red-100 text-red-700 px-5 py-3 rounded inline-block">

                        <i class="fa-solid fa-ban"></i>

                        Đơn hàng đã hủy

                    </span>

                <?php

                }

                ?>

            </div>

        </div>

        <hr class="my-8">

        <div class="grid grid-cols-2 md:grid-cols-4 gap-5">

            <div class="bg-yellow-50 rounded-lg p-5 text-center border">

                <i class="fa-solid fa-box text-4xl text-yellow-600"></i>

                <div class="mt-3 text-lg font-bold">

                    <?= mysqli_num_rows($resultDetail); ?>

                </div>

                <div>

                    Sản phẩm

                </div>

            </div>

            <div class="bg-green-50 rounded-lg p-5 text-center border">

                <i class="fa-solid fa-money-bill-wave text-4xl text-green-600"></i>

                <div class="mt-3 text-lg font-bold">

                    <?= number_format($total); ?> đ

                </div>

                <div>

                    Tổng tiền

                </div>

            </div>

            <div class="bg-blue-50 rounded-lg p-5 text-center border">

                <i class="fa-solid fa-credit-card text-4xl text-blue-600"></i>

                <div class="mt-3 text-lg font-bold">

                    <?= htmlspecialchars($order['payment_method']); ?>

                </div>

                <div>

                    Thanh toán

                </div>

            </div>

            <div class="bg-purple-50 rounded-lg p-5 text-center border">

                <i class="fa-solid fa-calendar-days text-4xl text-purple-600"></i>

                <div class="mt-3 text-lg font-bold">

                    <?= date("d/m/Y", strtotime($order['created_at'])); ?>

                </div>

                <div>

                    Ngày đặt

                </div>

            </div>

        </div>

    </div>

    <script>
        let map = null;

        $(document).on("click", "#btnBackOrder", function() {

            $("#content").load("/fruit_shop/seller/orders/index.php");

        });

        let sellerLat = Number("<?= $order['warehouse_latitude'] ?>");
        let sellerLng = Number("<?= $order['warehouse_longitude'] ?>");

        let customerLat = Number("<?= $order['latitude'] ?>");
        let customerLng = Number("<?= $order['longitude'] ?>");

        setTimeout(function() {

            if ($("#map").length == 0) return;

            if (
                sellerLat == 0 ||
                sellerLng == 0 ||
                customerLat == 0 ||
                customerLng == 0
            ) {

                alert("Thiếu tọa độ kho hàng hoặc khách hàng.");
                return;

            }

            if (map != null) {

                map.remove();

            }

            map = L.map("map").setView(
                [sellerLat, sellerLng],
                13
            );

            L.tileLayer(
                "https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png", {
                    attribution: "&copy; OpenStreetMap"
                }
            ).addTo(map);

            //--------------------------------------------------
            // Icon
            //--------------------------------------------------

            let warehouseIcon = L.icon({

                iconUrl: "https://cdn-icons-png.flaticon.com/512/684/684908.png",

                iconSize: [38, 38]

            });

            let customerIcon = L.icon({

                iconUrl: "https://cdn-icons-png.flaticon.com/512/535/535239.png",

                iconSize: [38, 38]

            });

            //--------------------------------------------------
            // Marker
            //--------------------------------------------------

            L.marker(
                    [sellerLat, sellerLng], {
                        icon: warehouseIcon
                    }
                )
                .addTo(map)
                .bindPopup("🏪 Kho hàng");

            L.marker(
                    [customerLat, customerLng], {
                        icon: customerIcon
                    }
                )
                .addTo(map)
                .bindPopup("📍 Người nhận");

            //--------------------------------------------------
            // Đường đi xe
            //--------------------------------------------------

            L.Routing.control({

                    waypoints: [

                        L.latLng(sellerLat, sellerLng),

                        L.latLng(customerLat, customerLng)

                    ],

                    lineOptions: {

                        styles: [{

                            color: "#2563eb",

                            opacity: 0.9,

                            weight: 6

                        }]

                    },

                    createMarker: function() {

                        return null;

                    },

                    addWaypoints: false,

                    draggableWaypoints: false,

                    fitSelectedRoutes: true,

                    show: false,

                    routeWhileDragging: false

                }).addTo(map)

                .on("routesfound", function(e) {

                    let route = e.routes[0];

                    $("#distance").html(

                        (route.summary.totalDistance / 1000).toFixed(2)

                        +
                        " km"

                    );

                    $("#time").html(

                        Math.round(route.summary.totalTime / 60)

                        +
                        " phút"

                    );

                });

            //--------------------------------------------------
            // Địa chỉ kho
            //--------------------------------------------------

            fetch(

                    "https://nominatim.openstreetmap.org/reverse?format=jsonv2"

                    +
                    "&lat=" + sellerLat

                    +
                    "&lon=" + sellerLng

                )

                .then(r => r.json())

                .then(data => {

                    if (data.display_name) {

                        $("#warehouseAddress").html(data.display_name);

                    }

                });

            //--------------------------------------------------
            // Địa chỉ khách
            //--------------------------------------------------

            fetch(

                    "https://nominatim.openstreetmap.org/reverse?format=jsonv2"

                    +
                    "&lat=" + customerLat

                    +
                    "&lon=" + customerLng

                )

                .then(r => r.json())

                .then(data => {

                    if (data.display_name) {

                        $("#customerAddress").html(data.display_name);

                    }

                });

        }, 300);

        $(document).off("click", ".btn-confirm-order");

        $(document).on("click", ".btn-confirm-order", function(e) {

            e.preventDefault();

            let id = $(this).data("id");

            if (!confirm("Xác nhận đơn?")) return;

            $.get(

                "/fruit_shop/seller/orders/confirm.php",

                {
                    id: id
                },

                function(res) {

                    if ($.trim(res) == "success") {

                        $("#content").load("/fruit_shop/seller/orders/detail.php?id=<?= $order_id ?>");

                    } else {

                        alert(res);

                    }

                }

            );

        });
    </script>