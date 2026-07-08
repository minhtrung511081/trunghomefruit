<?php
session_start();

include("../../config/database.php");

if (!isset($_SESSION['user'])) {
    header("Location: ../../auth/login.php");
    exit();
}

$seller_id = $_SESSION['user']['id'];

$sql = "
SELECT
warehouse_address,
warehouse_latitude,
warehouse_longitude
FROM users
WHERE id=?
";

$stmt = mysqli_prepare($conn, $sql);

mysqli_stmt_bind_param(
    $stmt,
    "i",
    $seller_id
);

mysqli_stmt_execute($stmt);

$result = mysqli_stmt_get_result($stmt);

$user = mysqli_fetch_assoc($result);

$lat = isset($user['warehouse_latitude'])
    ? (float)$user['warehouse_latitude']
    : 10.045162;

$lng = isset($user['warehouse_longitude'])
    ? (float)$user['warehouse_longitude']
    : 105.746857;

$address = $user['warehouse_address'];
?>

<div class="bg-white rounded-xl shadow p-6">

    <h2 class="text-3xl font-bold mb-6">

        <i class="fa-solid fa-warehouse"></i>

        Vị trí kho hàng

    </h2>

    <div class="mb-4">

        <label class="font-bold">

            Địa chỉ kho hàng

        </label>

        <input
            type="text"
            id="warehouse_address"
            class="border rounded-lg p-3 w-full mt-2"
            value="<?= htmlspecialchars($address) ?>">

    </div>

    <input
        type="hidden"
        id="latitude"
        value="<?= $lat ?>">

    <input
        type="hidden"
        id="longitude"
        value="<?= $lng ?>">

    <div
        id="map"
        style="height:550px;border-radius:12px;">
    </div>

    <div class="mt-6 flex gap-3">

        <button
            id="btnCurrent"
            class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded">

            <i class="fa-solid fa-location-crosshairs"></i>

            Vị trí hiện tại

        </button>

        <button
            id="btnSave"
            class="bg-green-600 hover:bg-green-700 text-white px-6 py-3 rounded">

            <i class="fa-solid fa-floppy-disk"></i>

            Lưu vị trí

        </button>

    </div>

</div>

<script>
    let lat = <?= (float)$lat ?>;
    let lng = <?= (float)$lng ?>;

    var map = L.map("map").setView([lat, lng], 16);

    L.tileLayer(
        "https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png", {
            attribution: "© OpenStreetMap"
        }
    ).addTo(map);

    // Marker ban đầu
    var marker = L.marker([lat, lng], {
        draggable: true
    }).addTo(map);

    // Nếu đã có địa chỉ thì hiển thị Popup
    if ($("#warehouse_address").val() != "") {

        marker.bindPopup($("#warehouse_address").val()).openPopup();

    }

    // ===============================
    // Click trên bản đồ
    // ===============================

    map.on("click", function(e) {

        lat = e.latlng.lat;
        lng = e.latlng.lng;

        marker.setLatLng([lat, lng]);

        $("#latitude").val(lat);
        $("#longitude").val(lng);

        getAddress(lat, lng);

    });

    // ===============================
    // Kéo marker
    // ===============================

    marker.on("dragend", function() {

        let p = marker.getLatLng();

        lat = p.lat;
        lng = p.lng;

        $("#latitude").val(lat);
        $("#longitude").val(lng);

        getAddress(lat, lng);

    });

    // ===============================
    // Lấy vị trí hiện tại
    // ===============================

    $("#btnCurrent").click(function() {

        if (!navigator.geolocation) {

            alert("Trình duyệt không hỗ trợ GPS.");

            return;

        }

        navigator.geolocation.getCurrentPosition(

            function(position) {

                lat = position.coords.latitude;
                lng = position.coords.longitude;

                map.setView([lat, lng], 17);

                marker.setLatLng([lat, lng]);

                $("#latitude").val(lat);
                $("#longitude").val(lng);

                getAddress(lat, lng);

            },

            function() {

                alert("Không lấy được vị trí hiện tại.");

            }

        );

    });

    // ===============================
    // Hàm lấy địa chỉ từ tọa độ
    // ===============================

    function getAddress(lat, lng) {

        fetch(
                "https://nominatim.openstreetmap.org/reverse?format=jsonv2&lat=" +
                lat +
                "&lon=" +
                lng
            )

            .then(response => response.json())

            .then(data => {

                if (data.display_name) {

                    $("#warehouse_address").val(data.display_name);

                    marker
                        .bindPopup(data.display_name)
                        .openPopup();

                }

            })

            .catch(function() {

                console.log("Không lấy được địa chỉ.");

            });

    }

    //========================================
    // Lưu vị trí
    //========================================

    $("#btnSave").click(function() {

        $.ajax({

            url: "/fruit_shop/seller/profile/save_location.php",

            type: "POST",

            data: {

                address: $("#warehouse_address").val(),

                latitude: $("#latitude").val(),

                longitude: $("#longitude").val()

            },

            success: function(res) {

                res = $.trim(res);

                if (res == "success") {

                    alert("Đã cập nhật vị trí kho hàng.");

                } else {

                    alert(res);

                }

            }

        });

    });
</script>