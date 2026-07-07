<?php
session_start();

require_once __DIR__ . "/../../config/database.php";

if (!isset($_SESSION['user'])) {
    header("Location:/fruit_shop/login.php");
    exit;
}

if ($_SESSION['user']['role_id'] != 2) {
    die("Bạn không có quyền.");
}

if (!isset($_GET['id'])) {
    header("Location:index.php");
    exit;
}

$order_id = (int)$_GET['id'];

/*
|--------------------------------------------------------------------------
| Lấy thông tin đơn hàng
|--------------------------------------------------------------------------
*/

$stmt = mysqli_prepare(
    $conn,
    "SELECT *
     FROM orders
     WHERE id=?"
);

mysqli_stmt_bind_param($stmt, "i", $order_id);

mysqli_stmt_execute($stmt);

$rs = mysqli_stmt_get_result($stmt);

if (mysqli_num_rows($rs) == 0) {

    die("Không tìm thấy đơn hàng.");
}

$order = mysqli_fetch_assoc($rs);

/*
|--------------------------------------------------------------------------
| Lấy danh sách shipper
|--------------------------------------------------------------------------
*/

$shippers = mysqli_query($conn, "
SELECT
id,
fullname,
phone,
avatar
FROM users
WHERE role_id=4
ORDER BY fullname ASC
");

require_once __DIR__ . "/../../includes/header.php";
require_once __DIR__ . "/../../includes/navbar.php";
?>

<div class="flex">

    <?php include("../sidebar.php"); ?>

    <div class="flex-1 p-6">

        <div class="bg-white rounded-lg shadow p-6">

            <h2 class="text-3xl font-bold mb-6">

                <i class="fa-solid fa-truck"></i>

                Chọn Shipper giao đơn #<?= $order_id ?>

            </h2>

            <form
                method="post"
                <form id="frmAssign">
                <!-- action="assign_shipper_save.php" -->
                <input
                    type="hidden"
                    name="order_id"
                    value="<?= $order_id ?>">

                <div class="grid md:grid-cols-2 gap-5">

                    <?php

                    while ($shipper = mysqli_fetch_assoc($shippers)) {

                        $avatar = !empty($shipper['avatar'])
                            ? "/fruit_shop/assets/images/avatars/" . $shipper['avatar']
                            : "/fruit_shop/assets/images/avatars/default-avatar.png";

                    ?>

                        <label
                            class="border rounded-lg p-5 cursor-pointer hover:border-green-500 hover:bg-green-50 transition">

                            <input
                                type="radio"
                                name="shipper_id"
                                value="<?= $shipper['id']; ?>"
                                class="mb-4"
                                required>

                            <div class="flex items-center">

                                <img
                                    src="<?= $avatar; ?>"
                                    class="w-20 h-20 rounded-full object-cover border">

                                <div class="ml-4">

                                    <h3 class="text-xl font-bold">

                                        <?= htmlspecialchars($shipper['fullname']); ?>

                                    </h3>

                                    <p class="text-gray-600 mt-2">

                                        <i class="fa-solid fa-phone"></i>

                                        <?= htmlspecialchars($shipper['phone']); ?>

                                    </p>

                                    <p class="text-green-600 mt-2">

                                        <i class="fa-solid fa-truck"></i>

                                        Shipper

                                    </p>

                                </div>

                            </div>

                        </label>

                    <?php

                    }

                    ?>

                </div>

                <div class="mt-8 flex justify-between">

                    <a
                        href="/fruit_shop/seller/dashboard.php?page=orders&id=<?= $order_id ?>"
                        class="btn-back-detail bg-gray-600 hover:bg-gray-700 text-white px-6 py-3 rounded">

                        <i class="fa-solid fa-arrow-left"></i>
                        Quay lại
                    </a>

                    <button
                        type="submit"
                        class="bg-green-600 hover:bg-green-700 text-white px-8 py-3 rounded">

                        <i class="fa-solid fa-check"></i>

                        Giao cho Shipper

                    </button>

                </div>

            </form>

        </div>

    </div>

    <script>
        $(document).on("click", ".btn-back-detail", function(e) {

            e.preventDefault();

            let id = <?= $order_id ?>;

            window.location =
                "/fruit_shop/seller/dashboard.php?load=detail&id=" + id;

        });

        $("#frmAssign").submit(function(e) {

            e.preventDefault();

            $.ajax({

                url: "/fruit_shop/seller/orders/assign_shipper_save.php",

                type: "POST",

                data: $(this).serialize(),

                success: function(res) {

                    res = $.trim(res);

                    if (res == "success") {

                        window.location.href = "/fruit_shop/seller/orders/detail.php?id=<?= $order_id ?>";

                    } else {

                        alert(res);

                    }

                }

            });

        });
    </script>
</div>

<?php
require_once __DIR__ . "/../../includes/footer.php";
?>