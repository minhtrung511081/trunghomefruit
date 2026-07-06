<?php
session_start();
include("../../config/database.php");

if (!isset($_SESSION['user'])) {
    header("Location: ../../login.php");
    exit;
}

$user = $_SESSION['user'];

$full_name = htmlspecialchars($user['full_name'] ?? '');
$phone = htmlspecialchars($user['phone'] ?? '');

include("../../includes/header.php");
include("../../includes/navbar.php");
?>

<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

<div class="max-w-5xl mx-auto mt-8">

    <div class="bg-white rounded-xl shadow p-8">

        <h2 class="text-3xl font-bold mb-6">
            Thêm địa chỉ mới
        </h2>

        <form action="store.php" method="POST">

            <div class="grid grid-cols-2 gap-6">

                <div>

                    <label class="block mb-2 font-semibold">
                        Họ và tên *
                    </label>

                    <input
                        type="text"
                        name="full_name"
                        value="<?= $full_name ?>"
                        class="w-full border rounded-lg p-3"
                        required>

                </div>

                <div>

                    <label class="block mb-2 font-semibold">
                        Số điện thoại *
                    </label>

                    <input
                        type="text"
                        name="phone"
                        value="<?= $phone ?>"
                        class="w-full border rounded-lg p-3"
                        required>

                </div>

            </div>

            <div class="mt-6">

                <button
                    type="button"
                    id="btnLocation"
                    class="bg-blue-600 hover:bg-blue-700 text-white px-5 py-3 rounded">

                    <i class="fa fa-location-crosshairs"></i>

                    Lấy vị trí hiện tại

                </button>

            </div>

            <div
                id="map"
                class="mt-6 rounded-xl"
                style="height:450px;">
            </div>

            <input
                type="hidden"
                id="latitude"
                name="latitude">

            <input
                type="hidden"
                id="longitude"
                name="longitude">

            <div class="mt-6">

                <label class="block mb-2 font-semibold">
                    Địa chỉ *
                </label>

                <textarea
                    id="address"
                    name="address"
                    rows="3"
                    class="w-full border rounded-lg p-3"
                    readonly
                    required></textarea>

            </div>

            <div class="mt-6">

                <label class="block mb-2 font-semibold">
                    Chi tiết địa chỉ (không bắt buộc)
                </label>

                <input
                    type="text"
                    name="address_detail"
                    class="w-full border rounded-lg p-3"
                    placeholder="Số nhà, tầng, căn hộ...">

            </div>

            <div class="mt-6">

                <label class="flex items-center">

                    <input
                        type="checkbox"
                        name="is_default"
                        value="1">

                    <span class="ml-2">

                        Đặt làm địa chỉ mặc định

                    </span>

                </label>

            </div>

            <div class="mt-8 flex gap-4">

                <button
                    type="submit"
                    class="bg-green-600 hover:bg-green-700 text-white px-6 py-3 rounded-lg">

                    Lưu địa chỉ

                </button>

                <a
                    href="index.php"
                    class="bg-gray-500 hover:bg-gray-600 text-white px-6 py-3 rounded-lg">

                    Quay lại

                </a>

            </div>



            <script>
                let marker;

                const map = L.map('map').setView([10.8231, 106.6297], 13);

                L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                    maxZoom: 19,
                    attribution: '&copy; OpenStreetMap'
                }).addTo(map);

                // Hàm lấy địa chỉ từ tọa độ
                async function getAddress(lat, lng) {

                    try {

                        const response = await fetch(
                            `https://nominatim.openstreetmap.org/reverse?format=jsonv2&lat=${lat}&lon=${lng}`
                        );

                        const data = await response.json();

                        if (data.display_name) {

                            document.getElementById("address").value = data.display_name;

                        } else {

                            document.getElementById("address").value = "";

                            alert("Không tìm thấy địa chỉ.");

                        }

                    } catch (e) {

                        console.log(e);

                        alert("Không lấy được địa chỉ.");

                    }

                }

                // Click trên bản đồ
                map.on('click', function(e) {

                    const lat = e.latlng.lat;
                    const lng = e.latlng.lng;

                    if (marker) {
                        map.removeLayer(marker);
                    }

                    marker = L.marker([lat, lng]).addTo(map);

                    document.getElementById("latitude").value = lat;
                    document.getElementById("longitude").value = lng;

                    getAddress(lat, lng);

                });

                // Nút lấy vị trí hiện tại
                document.getElementById("btnLocation").addEventListener("click", function() {

                    if (!navigator.geolocation) {

                        alert("Trình duyệt không hỗ trợ GPS.");

                        return;

                    }

                    navigator.geolocation.getCurrentPosition(

                        function(position) {

                            const lat = position.coords.latitude;
                            const lng = position.coords.longitude;

                            if (marker) {

                                map.removeLayer(marker);

                            }

                            marker = L.marker([lat, lng]).addTo(map);

                            map.setView([lat, lng], 18);

                            document.getElementById("latitude").value = lat;
                            document.getElementById("longitude").value = lng;

                            getAddress(lat, lng);

                        },

                        function(error) {

                            switch (error.code) {

                                case error.PERMISSION_DENIED:

                                    alert("Bạn đã từ chối quyền truy cập vị trí.");

                                    break;

                                case error.POSITION_UNAVAILABLE:

                                    alert("Không lấy được vị trí.");

                                    break;

                                case error.TIMEOUT:

                                    alert("Quá thời gian lấy vị trí.");

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

        </form>

    </div>

</div>

<?php
include("../../includes/footer.php");
?>