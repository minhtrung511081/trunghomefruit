<?php
session_start();

include("../../config/database.php");


if (!isset($_SESSION['user'])) {

    header("Location: ../../login.php");
    exit;
}


$shipper_id = $_SESSION['user']['id'];



if (!isset($_GET['id'])) {

    header("Location:index.php");
    exit;
}


$order_id = intval($_GET['id']);



// lấy thông tin đơn hàng

$sql = "

SELECT *

FROM orders

WHERE id = ?

AND shipper_id = ?

";


$stmt = mysqli_prepare($conn, $sql);


mysqli_stmt_bind_param(

    $stmt,

    "ii",

    $order_id,

    $shipper_id

);


mysqli_stmt_execute($stmt);


$result = mysqli_stmt_get_result($stmt);


if (mysqli_num_rows($result) == 0) {

    header("Location:index.php");

    exit;
}


$order = mysqli_fetch_assoc($result);



mysqli_stmt_close($stmt);




// lấy sản phẩm trong đơn

$sql = "

SELECT

order_details.*,

products.product_name,

products.image,

products.unit

FROM order_details

INNER JOIN products
    ON order_details.product_id = products.id

WHERE order_details.order_id = ?

";



$stmt = mysqli_prepare($conn, $sql);


mysqli_stmt_bind_param(

    $stmt,

    "i",

    $order_id

);


mysqli_stmt_execute($stmt);


$items = mysqli_stmt_get_result($stmt);



include("../../includes/header.php");

include("../../includes/navbar.php");

?>



<div class="max-w-5xl mx-auto mt-8">


    <div class="bg-white shadow rounded-xl p-8">


        <h2 class="text-3xl font-bold mb-6">

            Chi tiết đơn hàng #<?= $order['id'] ?>

        </h2>


        <div class="grid grid-cols-2 gap-5">


            <div>

                <b>Khách hàng:</b>

                <?= htmlspecialchars($order['full_name']) ?>

            </div>


            <div>

                <b>SĐT:</b>

                <?= htmlspecialchars($order['phone']) ?>

            </div>


            <div class="col-span-2">

                <b>Địa chỉ:</b>

                <?= htmlspecialchars($order['address']) ?>

            </div>


            <div>

                <b>Tổng tiền:</b>

                <?= number_format($order['total']) ?> đ

            </div>


            <div>

                <b>Trạng thái:</b>

                <?= $order['status'] ?>

            </div>


            <div>

                <b>Thanh toán:</b>

                <?= $order['payment_method'] ?>

            </div>


            <div>

                <b>Tình trạng:</b>

                <?= $order['payment_status'] ?>

            </div>



        </div>




        <hr class="my-6">



        <h3 class="text-xl font-bold mb-4">

            Sản phẩm

        </h3>



        <table class="w-full border">


            <tr class="bg-gray-100">

                <th class="p-3 border">

                    Tên

                </th>


                <th class="p-3 border">

                    Số lượng

                </th>


                <th class="p-3 border">

                    Giá

                </th>

            </tr>



            <?php while ($item = mysqli_fetch_assoc($items)) { ?>

                <tr>


                    <td class="p-3 border">

                        <?= htmlspecialchars($item['product_name']) ?>

                    </td>


                    <td class="p-3 border text-center">

                        <?= $item['quantity'] ?>

                    </td>


                    <td class="p-3 border">

                        <?= number_format($item['price']) ?> đ

                    </td>


                </tr>


            <?php } ?>


        </table>



        <div class="mt-6 flex gap-3">

            <a href="/fruit_shop/shipper/dashboard.php"
                class="bg-gray-600 hover:bg-gray-700 text-white px-5 py-3 rounded">
                <i class="fa-solid fa-arrow-left"></i>
                Quay lại Dashboard
            </a>


            <a href="update_status.php?id=<?= $order['id'] ?>"
                class="bg-green-600 hover:bg-green-700 text-white px-5 py-3 rounded">
                <i class="fa-solid fa-truck"></i>
                Cập nhật trạng thái
            </a>

            <a href="../tracking/tracking.php?id=<?= $order['id'] ?>"
                class="bg-green-600 text-white px-4 py-2 rounded">
                <i class="fa-solid fa-location-dot"></i>
                Cập nhật vị trí
            </a>


        </div>



    </div>


</div>

<script src="https://unpkg.com/leaflet/dist/leaflet.js">
</script>

<script>
    let map = L.map("map").setView([10.0452, 105.7469], 13);

    L.tileLayer(

        'https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png',

        {

            maxZoom: 19

        }

    ).addTo(map);

    let marker = null;

    function loadTracking() {

        fetch(

                "get_tracking.php?order_id=<?= $order['id'] ?>"

            )

            .then(res => res.json())

            .then(data => {

                if (!data.success) {

                    return;

                }

                let lat = parseFloat(data.data.latitude);

                let lng = parseFloat(data.data.longitude);

                let note = data.data.note;

                let time = data.data.created_at;

                document.getElementById("note").innerHTML = note;

                document.getElementById("time").innerHTML = time;

                if (marker) {

                    marker.setLatLng([lat, lng]);

                } else {

                    marker = L.marker([lat, lng]).addTo(map);

                }

                map.panTo([lat, lng]);

            });

    }

    loadTracking();

    setInterval(loadTracking, 5000);
</script>

<?php

include("../../includes/footer.php");

?>