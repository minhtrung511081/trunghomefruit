<?php

session_start();

include("../../config/database.php");

if (!isset($_SESSION['user'])) {

    header("Location:../../login.php");

    exit;
}

$shipperId = $_SESSION['user']['id'];

$orderId = intval($_GET['id']);

$sql = "
SELECT
    o.*,
    u.warehouse_address,
    u.warehouse_latitude,
    u.warehouse_longitude
FROM orders o
JOIN users u
ON u.id = o.seller_id
WHERE o.id = ?
";

$stmt = mysqli_prepare($conn, $sql);

mysqli_stmt_bind_param($stmt, "i", $orderId);

mysqli_stmt_execute($stmt);

$result = mysqli_stmt_get_result($stmt);

$order = mysqli_fetch_assoc($result);

if (!$order) {

    die("Không tìm thấy đơn.");
}

include("../../includes/header.php");

include("../../includes/navbar.php");

?>

<link
    rel="stylesheet"
    href="https://unpkg.com/leaflet/dist/leaflet.css" />

<link
    rel="stylesheet"
    href="https://unpkg.com/leaflet-routing-machine/dist/leaflet-routing-machine.css" />

<div class="max-w-7xl mx-auto mt-6">

    <h2 class="text-3xl font-bold mb-5">

        Theo dõi giao hàng

    </h2>

    <div class="grid grid-cols-3 gap-4">

        <div class="bg-white rounded shadow p-5">

          
            <div class="bg-white rounded shadow p-5">
                <h3 class="font-bold">🏬 Kho hàng</h3>
                <p>
                    <?= !empty($order["warehouse_address"]) ? htmlspecialchars($order["warehouse_address"]) : "Chưa cập nhật địa chỉ kho" ?>
                </p>
            </div>



        </div>

        <div class="bg-white rounded shadow p-5">

            <h3 class="font-bold">

                🏠 Khách hàng

            </h3>

            <p>

                <?= htmlspecialchars($order["address"]) ?>

            </p>

        </div>

        <div class="bg-white rounded shadow p-5">

            <h3 class="font-bold">

                🚚 Trạng thái

            </h3>

            <p id="distance">

                Đang tính...

            </p>

            <p id="duration">

            </p>

        </div>

    </div>

    <div

        id="map"

        style="height:650px"

        class="rounded shadow mt-6">

    </div>

    <script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>

    <script src="https://unpkg.com/leaflet-routing-machine/dist/leaflet-routing-machine.js"></script>

    <script>
        // Nếu không có tọa độ trong DB, tự động lấy tọa độ mặc định (Ví dụ dưới đây là TP.HCM)
        let warehouseLat = <?= floatval($order["warehouse_latitude"] ?? 10.762622) ?>;
        let warehouseLng = <?= floatval($order["warehouse_longitude"] ?? 106.660172) ?>;

        let customerLat = <?= floatval($order["delivery_latitude"] ?? 10.776889) ?>;
        let customerLng = <?= floatval($order["delivery_longitude"] ?? 106.700806) ?>;

        // Kiểm tra nhanh trên Console của trình duyệt xem tọa độ đã nhận đúng chưa
        console.log("Kho:", warehouseLat, warehouseLng);
        console.log("Khách:", customerLat, customerLng);

        let map = L.map("map").setView(

            [warehouseLat, warehouseLng],

            13

        );

        L.tileLayer(

            'https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png',

            {

                maxZoom: 19

            }

        ).addTo(map);

        let warehouseIcon = L.icon({

            iconUrl: "/fruit_shop/assets/images/warehouse.png",

            iconSize: [40, 40]

        });

        let customerIcon = L.icon({

            iconUrl: "/fruit_shop/assets/images/home.png",

            iconSize: [40, 40]

        });

        // Thêm marker Kho hàng (Dùng icon mặc định)
        L.marker([warehouseLat, warehouseLng])
            .addTo(map)
            .bindPopup("Kho hàng");

        // Thêm marker Khách hàng (Dùng icon mặc định)
        L.marker([customerLat, customerLng])
            .addTo(map)
            .bindPopup("Khách hàng");

        let routing = L.Routing.control({

            waypoints: [

                L.latLng(

                    warehouseLat,

                    warehouseLng

                ),

                L.latLng(

                    customerLat,

                    customerLng

                )

            ],

            routeWhileDragging: false,

            addWaypoints: false,

            draggableWaypoints: false,

            fitSelectedRoutes: true,

            show: false

        }).addTo(map);

        routing.on("routesfound", function(e) {

            let route = e.routes[0];

            document.getElementById("distance").innerHTML =

                "Quãng đường: "

                +
                (route.summary.totalDistance / 1000).toFixed(2)

                +
                " km";

            document.getElementById("duration").innerHTML =

                "Thời gian: "

                +
                Math.round(route.summary.totalTime / 60)

                +
                " phút";

        });
    </script>

    <?php

    include("../../includes/footer.php");

    ?>