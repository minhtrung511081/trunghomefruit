<?php
session_start();
include("../config/database.php");

if (!isset($_SESSION['user'])) {
    header("Location: /fruit_shop/login.php");
    exit;
}

if (!isset($_SESSION['cart']) || empty($_SESSION['cart'])) {
    header("Location: index.php");
    exit;
}

include("../includes/header.php");
include("../includes/navbar.php");

$user = $_SESSION['user'];

$full_name = htmlspecialchars($user['full_name'] ?? '');
$phone     = htmlspecialchars($user['phone'] ?? '');
$address   = htmlspecialchars($user['address'] ?? '');
?>

<div class="max-w-3xl mx-auto mt-8 bg-white shadow p-6 rounded">

    <h2 class="text-3xl font-bold mb-6">
        Đặt hàng
    </h2>

    <form action="place_order.php" method="POST">

        <div class="mb-4">
            <label class="block mb-2 font-semibold">
                Họ và tên
            </label>

            <input
                type="text"
                name="full_name"
                class="w-full border p-2 rounded"
                value="<?= $full_name ?>"
                required>
        </div>

        <div class="mb-4">
            <label class="block mb-2 font-semibold">
                Số điện thoại
            </label>

            <input
                type="text"
                name="phone"
                class="w-full border p-2 rounded"
                value="<?= $phone ?>"
                required>
        </div>

        <div class="mb-4">

            <label class="font-semibold block mb-2">
                Địa chỉ giao hàng
            </label>

            <textarea
                id="address"
                name="address"
                class="w-full border rounded p-2"
                rows="3"
                required><?= $address ?></textarea>

        </div>

        <input type="hidden" id="latitude" name="latitude">
        <input type="hidden" id="longitude" name="longitude">

        <div class="mb-3">

            <button
                type="button"
                id="btnLocation"
                class="bg-blue-600 text-white px-4 py-2 rounded">

                📍 Lấy vị trí hiện tại

            </button>

        </div>

        <div id="map"
            style="height:400px;border-radius:10px;"></div>

        <div class="mb-4">
            <label class="block mb-2 font-semibold">
                Ghi chú
            </label>

            <textarea
                name="note"
                class="w-full border p-2 rounded"
                rows="3"></textarea>
        </div>

        <div class="mb-6">

            <label class="font-semibold block mb-3">
                Phương thức thanh toán
            </label>

            <div class="space-y-3">

                <label class="flex items-center">
                    <input
                        type="radio"
                        name="payment_method"
                        value="COD"
                        checked
                        onclick="changePayment()">

                    <span class="ml-2">
                        Thanh toán khi nhận hàng (COD)
                    </span>
                </label>

                <label class="flex items-center">
                    <input
                        type="radio"
                        name="payment_method"
                        value="ONLINE"
                        onclick="changePayment()">

                    <span class="ml-2">
                        Thanh toán trước
                    </span>
                </label>

            </div>

        </div>

        <div
            id="onlinePayment"
            style="display:none"
            class="border rounded p-4">

            <label class="flex items-center mb-3">

                <input
                    type="radio"
                    name="online_type"
                    value="BANK"
                    checked>

                <span class="ml-2">
                    Chuyển khoản ngân hàng
                </span>

            </label>

            <label class="flex items-center">

                <input
                    type="radio"
                    name="online_type"
                    value="ZALOPAY">

                <span class="ml-2">
                    ZaloPay
                </span>

            </label>

        </div>

        <button
            type="submit"
            class="bg-green-600 hover:bg-green-700 text-white px-6 py-3 rounded">
            Xác nhận đặt hàng
        </button>

    </form>

</div>

<script>
    var map = L.map('map').setView([10.8231, 106.6297], 13);

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        maxZoom: 19
    }).addTo(map);

    var marker;

    map.on('click', function(e) {

        if (marker) {
            map.removeLayer(marker);
        }

        marker = L.marker(e.latlng).addTo(map);

        document.getElementById("latitude").value = e.latlng.lat;

        document.getElementById("longitude").value = e.latlng.lng;

    });

    document.getElementById("btnLocation").onclick = function() {

        if (navigator.geolocation) {

            navigator.geolocation.getCurrentPosition(function(position) {

                var lat = position.coords.latitude;

                var lng = position.coords.longitude;

                map.setView([lat, lng], 17);

                if (marker) {
                    map.removeLayer(marker);
                }

                marker = L.marker([lat, lng]).addTo(map);

                document.getElementById("latitude").value = lat;

                document.getElementById("longitude").value = lng;

            });

        } else {

            alert("Trình duyệt không hỗ trợ GPS.");

        }

    }

    function changePayment() {

        let online = document.querySelector('input[value="ONLINE"]').checked;

        document.getElementById("onlinePayment").style.display =
            online ? "block" : "none";

    }
</script>

<?php include("../includes/footer.php"); ?>