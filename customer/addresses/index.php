<?php
session_start();
include("../../config/database.php");

if (!isset($_SESSION['user'])) {
    header("Location: ../../login.php");
    exit;
}

$user_id = $_SESSION['user']['id'];

$sql = "SELECT * FROM user_addresses
        WHERE user_id='$user_id'
        ORDER BY is_default DESC,id DESC";

$result = mysqli_query($conn, $sql);

include("../../includes/header.php");
include("../../includes/navbar.php");
?>

<div class="max-w-6xl mx-auto mt-8">

    <div class="flex justify-between items-center mb-6">

        <div>

            <h2 class="text-3xl font-bold">
                Địa chỉ của tôi
            </h2>

            <p class="text-gray-500 mt-2">
                Quản lý địa chỉ giao hàng
            </p>

        </div>

        <a
            href="create.php"
            class="bg-green-600 hover:bg-green-700 text-white px-5 py-3 rounded-lg">

            <i class="fa fa-plus"></i>

            Thêm địa chỉ

        </a>

    </div>

    <?php

    if (mysqli_num_rows($result) == 0) {

    ?>

        <div class="bg-white rounded-xl shadow p-12 text-center">

            <i
                class="fa fa-location-dot text-6xl text-gray-300 mb-5">
            </i>

            <h3 class="text-2xl font-bold">

                Bạn chưa có địa chỉ nào

            </h3>

            <p class="text-gray-500 mt-3">

                Hãy thêm địa chỉ giao hàng đầu tiên.

            </p>

            <a
                href="create.php"
                class="inline-block mt-6 bg-green-600 text-white px-6 py-3 rounded">

                Thêm địa chỉ

            </a>

        </div>

        <?php

    } else {

        while ($row = mysqli_fetch_assoc($result)) {

        ?>

            <div class="bg-white rounded-xl shadow-md p-6 mb-6 border">

                <div class="flex justify-between">

                    <div class="w-full">

                        <div class="flex items-center gap-3">

                            <h3 class="text-xl font-bold">

                                <?= htmlspecialchars($row['full_name']) ?>

                            </h3>

                            <?php if ($row['is_default'] == 1) { ?>

                                <span
                                    class="bg-green-100 text-green-700 px-3 py-1 rounded-full text-sm">

                                    Mặc định

                                </span>

                            <?php } ?>

                        </div>

                        <p class="mt-2 text-gray-700">

                            <i class="fa fa-phone text-green-600"></i>

                            <?= htmlspecialchars($row['phone']) ?>

                        </p>

                        <p class="mt-2 text-gray-700">

                            <i class="fa fa-location-dot text-red-500"></i>

                            <?= nl2br(htmlspecialchars($row['address'])) ?>

                        </p>

                        <?php if (!empty($row['address_detail'])) { ?>

                            <p class="mt-2 text-gray-500">

                                <strong>Chi tiết:</strong>

                                <?= htmlspecialchars($row['address_detail']) ?>

                            </p>

                        <?php } ?>

                        <?php if (!empty($row['latitude']) && !empty($row['longitude'])) { ?>

                            <p class="mt-2 text-sm text-gray-500">

                                GPS:
                                <?= $row['latitude'] ?> ,
                                <?= $row['longitude'] ?>

                            </p>

                        <?php } ?>

                    </div>

                </div>

                <div class="flex flex-wrap gap-3 mt-6">

                    <a
                        href="edit.php?id=<?= $row['id'] ?>"
                        class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded">

                        <i class="fa fa-pen"></i>

                        Sửa

                    </a>

                    <a
                        href="delete.php?id=<?= $row['id'] ?>"
                        class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded"
                        onclick="return confirm('Bạn có chắc muốn xóa địa chỉ này?')">

                        <i class="fa fa-trash"></i>

                        Xóa

                    </a>

                    <?php if ($row['is_default'] == 0) { ?>

                        <a
                            href="set_default.php?id=<?= $row['id'] ?>"
                            class="bg-orange-500 hover:bg-orange-600 text-white px-4 py-2 rounded">

                            <i class="fa fa-star"></i>

                            Đặt mặc định

                        </a>

                    <?php } ?>

                    <?php if (!empty($row['latitude']) && !empty($row['longitude'])) { ?>

                        <a
                            target="_blank"
                            href="https://www.google.com/maps?q=<?= $row['latitude'] ?>,<?= $row['longitude'] ?>"
                            class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded">

                            <i class="fa fa-map-location-dot"></i>

                            Xem bản đồ

                        </a>

                    <?php } ?>

                </div>

            </div>

    <?php

        }
    }

    ?>

</div>

<?php

if (isset($_GET['success'])) {

?>

    <script>
        alert("Thêm địa chỉ thành công.");
    </script>

<?php

}

?>

<?php

if (isset($_GET['updated'])) {

?>

    <script>
        alert("Cập nhật địa chỉ thành công.");
    </script>

<?php

}

?>

<?php

if (isset($_GET['deleted'])) {

?>

    <script>
        alert("Xóa địa chỉ thành công.");
    </script>

<?php

}

?>

<?php

if (isset($_GET['default'])) {

?>

    <script>
        alert("Đã đặt làm địa chỉ mặc định.");
    </script>

<?php

}

include("../../includes/footer.php");

?>