<?php
session_start();
include("../../config/database.php");

if (!isset($_SESSION['user'])) {
    header("Location: ../../login.php");
    exit;
}

$user_id = $_SESSION['user']['id'];

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: index.php");
    exit;
}

$id = (int)$_GET['id'];

$stmt = mysqli_prepare(
    $conn,
    "SELECT * FROM user_addresses
     WHERE id = ? AND user_id = ?"
);

mysqli_stmt_bind_param($stmt, "ii", $id, $user_id);
mysqli_stmt_execute($stmt);

$result = mysqli_stmt_get_result($stmt);

if (mysqli_num_rows($result) == 0) {

    mysqli_stmt_close($stmt);

    header("Location: index.php");
    exit;
}

$address = mysqli_fetch_assoc($result);

mysqli_stmt_close($stmt);

include("../../includes/header.php");
include("../../includes/navbar.php");
?>

<link
    rel="stylesheet"
    href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />

<script
    src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

<div class="max-w-5xl mx-auto mt-8">

    <div class="bg-white rounded-xl shadow p-8">

        <h2 class="text-3xl font-bold mb-6">

            Chỉnh sửa địa chỉ

        </h2>

        <form action="update.php" method="POST">

            <input
                type="hidden"
                name="id"
                value="<?= $address['id'] ?>">

            <div class="grid grid-cols-2 gap-6">

                <div>

                    <label class="block mb-2 font-semibold">

                        Họ và tên *

                    </label>

                    <input
                        type="text"
                        name="full_name"
                        class="w-full border rounded-lg p-3"
                        value="<?= htmlspecialchars($address['full_name']) ?>"
                        required>

                </div>

                <div>

                    <label class="block mb-2 font-semibold">

                        Số điện thoại *

                    </label>

                    <input
                        type="text"
                        name="phone"
                        class="w-full border rounded-lg p-3"
                        value="<?= htmlspecialchars($address['phone']) ?>"
                        required>

                </div>

            </div>

            <div class="mt-6">

                <button
                    type="button"
                    id="btnLocation"
                    class="bg-blue-600 text-white px-5 py-3 rounded">

                    📍 Lấy vị trí hiện tại

                </button>

            </div>

            <div
                id="map"
                class="mt-6 rounded-xl"
                style="height:450px;"></div>

            <input
                type="hidden"
                id="latitude"
                name="latitude"
                value="<?= $address['latitude'] ?>">

            <input
                type="hidden"
                id="longitude"
                name="longitude"
                value="<?= $address['longitude'] ?>">

            <div class="mt-6">

                <label class="block mb-2 font-semibold">

                    Địa chỉ

                </label>

                <textarea
                    id="address"
                    name="address"
                    rows="3"
                    class="w-full border rounded-lg p-3"
                    readonly
                    required><?= htmlspecialchars($address['address']) ?></textarea>

            </div>

            <div class="mt-6">

                <label class="block mb-2 font-semibold">

                    Chi tiết địa chỉ

                </label>

                <input
                    type="text"
                    name="address_detail"
                    class="w-full border rounded-lg p-3"
                    value="<?= htmlspecialchars($address['address_detail']) ?>">

            </div>

            <div class="mt-6">

                <label class="flex items-center">

                    <input
                        type="checkbox"
                        name="is_default"
                        value="1"

                        <?= $address['is_default'] ? "checked" : "" ?>>

                    <span class="ml-2">

                        Đặt làm địa chỉ mặc định

                    </span>

                </label>

            </div>

            <script>
                let lat = parseFloat(document.getElementById("latitude").value) || 10.8231;
                let lng = parseFloat(document.getElementById("longitude").value) || 106.6297;

                const map = L.map('map').setView([lat, lng], 16);

                L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                    maxZoom: 19,
                    attribution: '&copy; OpenStreetMap'
                }).addTo(map);

                let marker = L.marker([lat, lng]).addTo(map);

                // Lấy địa chỉ từ tọa độ
                async function getAddress(lat, lng) {

                    try {

                        const response = await fetch(
                            `https://nominatim.openstreetmap.org/reverse?format=jsonv2&lat=${lat}&lon=${lng}`
                        );

                        const data = await response.json();

                        if (data.display_name) {

                            document.getElementById("address").value = data.display_name;

                        }

                    } catch (e) {

                        console.log(e);

                    }

                }

                // Click để đổi vị trí
                map.on('click', function(e) {

                    let lat = e.latlng.lat;
                    let lng = e.latlng.lng;

                    marker.setLatLng([lat, lng]);

                    document.getElementById("latitude").value = lat;
                    document.getElementById("longitude").value = lng;

                    getAddress(lat, lng);

                });

                // Lấy vị trí hiện tại
                document.getElementById("btnLocation").addEventListener("click", function() {

                    if (!navigator.geolocation) {

                        alert("Trình duyệt không hỗ trợ định vị.");

                        return;

                    }

                    navigator.geolocation.getCurrentPosition(

                        function(position) {

                            let lat = position.coords.latitude;
                            let lng = position.coords.longitude;

                            marker.setLatLng([lat, lng]);

                            map.setView([lat, lng], 18);

                            document.getElementById("latitude").value = lat;
                            document.getElementById("longitude").value = lng;

                            getAddress(lat, lng);

                        },

                        function(error) {

                            switch (error.code) {

                                case error.PERMISSION_DENIED:

                                    alert("Bạn chưa cấp quyền truy cập vị trí.");

                                    break;

                                case error.POSITION_UNAVAILABLE:

                                    alert("Không lấy được vị trí.");

                                    break;

                                case error.TIMEOUT:

                                    alert("Hết thời gian chờ.");

                                    break;

                                default:

                                    alert("Có lỗi xảy ra.");

                            }

                        },

                        {

                            enableHighAccuracy: true,
                            timeout: 10000,
                            maximumAge: 0

                        }

                    );

                });
            </script>

            <div class="mt-8 flex gap-4">

                <button
                    type="submit"
                    class="bg-green-600 hover:bg-green-700 text-white px-6 py-3 rounded-lg">

                    <i class="fa fa-floppy-disk"></i>

                    Cập nhật địa chỉ

                </button>

                <a
                    href="index.php"
                    class="bg-gray-500 hover:bg-gray-600 text-white px-6 py-3 rounded-lg">

                    <i class="fa fa-arrow-left"></i>

                    Quay lại

                </a>

            </div>

        </form>

    </div>

</div>

<?php
include("../../includes/footer.php");
?>