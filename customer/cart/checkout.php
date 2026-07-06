<?php
session_start();
require_once __DIR__ . "/../../config/database.php";

if (!isset($_SESSION['user'])) {
    header("Location: /fruit_shop/login.php");
    exit;
}

if (!isset($_SESSION['cart']) || empty($_SESSION['cart'])) {
    header("Location: index.php");
    exit;
}

require_once __DIR__ . "/../../includes/header.php";
require_once __DIR__ . "/../../includes/navbar.php";

$user = $_SESSION['user'];

$sql = "SELECT *
        FROM user_addresses
        WHERE user_id = ?
        ORDER BY is_default DESC, id DESC";

$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "i", $user['id']);
mysqli_stmt_execute($stmt);

$addresses = mysqli_stmt_get_result($stmt);
?>

<div class="max-w-4xl mx-auto mt-8 bg-white shadow-lg rounded-lg p-6">

    <h2 class="text-3xl font-bold mb-6 text-center">
        Xác nhận đặt hàng
    </h2>

    <form action="place_order.php" method="POST">

        <!-- ĐỊA CHỈ GIAO HÀNG -->

        <div class="border rounded-lg p-5 mb-6">

            <div class="flex justify-between items-center mb-4">

                <h3 class="text-xl font-bold text-blue-600">
                    <i class="fa-solid fa-location-dot"></i>
                    Địa chỉ giao hàng
                </h3>

                <a
                    href="/fruit_shop/customer/addresses/index.php"
                    class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded">

                    <i class="fa-solid fa-address-book"></i>

                    Quản lý địa chỉ

                </a>

            </div>

            <?php if (mysqli_num_rows($addresses) > 0) { ?>

                <?php while ($row = mysqli_fetch_assoc($addresses)) { ?>

                    <label
                        class="block border rounded-lg p-4 mb-3 cursor-pointer hover:bg-gray-50">

                        <div class="flex">

                            <input
                                type="radio"
                                name="address_id"
                                value="<?= $row['id'] ?>"
                                <?= $row['is_default'] ? 'checked' : '' ?>
                                required>

                            <div class="ml-4 w-full">

                                <div class="flex justify-between">

                                    <strong>

                                        <?= htmlspecialchars($row['full_name']) ?>

                                    </strong>

                                    <?php if ($row['is_default']) { ?>

                                        <span
                                            class="bg-green-100 text-green-700 px-3 py-1 rounded text-sm">

                                            Mặc định

                                        </span>

                                    <?php } ?>

                                </div>

                                <div class="text-gray-700 mt-2">

                                    <i class="fa-solid fa-phone"></i>

                                    <?= htmlspecialchars($row['phone']) ?>

                                </div>

                                <div class="text-gray-700 mt-2">

                                    <i class="fa-solid fa-location-dot"></i>

                                    <?= htmlspecialchars($row['address']) ?>

                                </div>

                                <?php if (!empty($row['address_detail'])) { ?>

                                    <div class="text-gray-500 mt-2">

                                        <?= htmlspecialchars($row['detail']) ?>

                                    </div>

                                <?php } ?>

                            </div>

                        </div>

                    </label>

                <?php } ?>

            <?php } else { ?>

                <div
                    class="bg-yellow-100 border border-yellow-300 rounded-lg p-5">

                    <p class="mb-4 text-yellow-800">

                        Bạn chưa có địa chỉ giao hàng.

                    </p>

                    <a
                        href="/fruit_shop/customer/addresses/index.php"
                        class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded">

                        <i class="fa-solid fa-plus"></i>

                        Thêm địa chỉ

                    </a>

                </div>

            <?php } ?>

        </div>

                <!-- DANH SÁCH SẢN PHẨM -->

        <div class="border rounded-lg p-5 mb-6">

            <h3 class="text-xl font-bold text-green-600 mb-4">
                <i class="fa-solid fa-cart-shopping"></i>
                Sản phẩm đặt mua
            </h3>

            <?php

            $tongTien = 0;

            foreach ($_SESSION['cart'] as $item) {

                $thanhTien = $item['price'] * $item['quantity'];

                $tongTien += $thanhTien;

            ?>

                <div class="flex items-center border-b py-4">

                    <img
                        src="/fruit_shop/uploads/products/<?= htmlspecialchars($item['image']) ?>"
                        class="w-20 h-20 rounded object-cover border">

                    <div class="ml-4 flex-1">

                        <h4 class="font-bold text-lg">

                            <?= htmlspecialchars($item['name']) ?>

                        </h4>

                        <div class="text-gray-600 mt-1">

                            Đơn giá:

                            <span class="text-red-600 font-semibold">

                                <?= number_format($item['price']) ?> đ

                            </span>

                        </div>

                        <div class="text-gray-600 mt-1">

                            Số lượng:

                            <strong>

                                <?= $item['quantity'] ?>

                                <?= $item['unit'] ?>

                            </strong>

                        </div>

                    </div>

                    <div class="text-right">

                        <div class="font-bold text-red-600 text-lg">

                            <?= number_format($thanhTien) ?> đ

                        </div>

                    </div>

                </div>

            <?php } ?>

            <div class="text-right mt-5">

                <span class="text-xl font-bold">

                    Tổng tiền:

                </span>

                <span class="text-2xl text-red-600 font-bold">

                    <?= number_format($tongTien) ?> đ

                </span>

            </div>

        </div>

        <!-- GHI CHÚ -->

        <div class="mb-5">

            <label class="block font-semibold mb-2">

                <i class="fa-solid fa-pen"></i>

                Ghi chú

            </label>

            <textarea
                name="note"
                rows="4"
                class="w-full border rounded p-3"
                placeholder="Ví dụ: Giao giờ hành chính, gọi trước khi giao..."></textarea>

        </div>

        <!-- PHƯƠNG THỨC THANH TOÁN -->

        <div class="border rounded-lg p-5 mb-6">

            <h3 class="text-xl font-bold mb-4">

                <i class="fa-solid fa-wallet"></i>

                Phương thức thanh toán

            </h3>

            <label class="flex items-center mb-4 cursor-pointer">

                <input
                    type="radio"
                    name="payment_method"
                    value="COD"
                    checked
                    onclick="changePayment()">

                <span class="ml-3">

                    Thanh toán khi nhận hàng (COD)

                </span>

            </label>

            <label class="flex items-center cursor-pointer">

                <input
                    type="radio"
                    name="payment_method"
                    value="ONLINE"
                    onclick="changePayment()">

                <span class="ml-3">

                    Thanh toán trước

                </span>

            </label>

            <div
                id="onlinePayment"
                class="border rounded-lg p-4 mt-5"
                style="display:none;">

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

                        Thanh toán qua ZaloPay

                    </span>

                </label>

            </div>

        </div>

        <!-- NÚT ĐẶT HÀNG -->

        <div class="flex justify-between">

            <a
                href="/fruit_shop/customer/cart/index.php"
                class="bg-gray-500 hover:bg-gray-600 text-white px-6 py-3 rounded">

                <i class="fa-solid fa-arrow-left"></i>

                Quay lại giỏ hàng

            </a>

            <button
                type="submit"
                class="bg-green-600 hover:bg-green-700 text-white px-8 py-3 rounded">

                <i class="fa-solid fa-check"></i>

                Xác nhận đặt hàng

            </button>

        </div>

    </form>

</div>

        <!-- DANH SÁCH SẢN PHẨM -->

        <div class="border rounded-lg p-5 mb-6">

            <h3 class="text-xl font-bold text-green-600 mb-4">
                <i class="fa-solid fa-cart-shopping"></i>
                Sản phẩm đặt mua
            </h3>

            <?php

            $tongTien = 0;

            foreach ($_SESSION['cart'] as $item) {

                $thanhTien = $item['price'] * $item['quantity'];

                $tongTien += $thanhTien;

            ?>

                <div class="flex items-center border-b py-4">

                    <img
                        src="/fruit_shop/uploads/products/<?= htmlspecialchars($item['image']) ?>"
                        class="w-20 h-20 rounded object-cover border">

                    <div class="ml-4 flex-1">

                        <h4 class="font-bold text-lg">

                            <?= htmlspecialchars($item['name']) ?>

                        </h4>

                        <div class="text-gray-600 mt-1">

                            Đơn giá:

                            <span class="text-red-600 font-semibold">

                                <?= number_format($item['price']) ?> đ

                            </span>

                        </div>

                        <div class="text-gray-600 mt-1">

                            Số lượng:

                            <strong>

                                <?= $item['quantity'] ?>

                                <?= $item['unit'] ?>

                            </strong>

                        </div>

                    </div>

                    <div class="text-right">

                        <div class="font-bold text-red-600 text-lg">

                            <?= number_format($thanhTien) ?> đ

                        </div>

                    </div>

                </div>

            <?php } ?>

            <div class="text-right mt-5">

                <span class="text-xl font-bold">

                    Tổng tiền:

                </span>

                <span class="text-2xl text-red-600 font-bold">

                    <?= number_format($tongTien) ?> đ

                </span>

            </div>

        </div>

        <!-- GHI CHÚ -->

        <div class="mb-5">

            <label class="block font-semibold mb-2">

                <i class="fa-solid fa-pen"></i>

                Ghi chú

            </label>

            <textarea
                name="note"
                rows="4"
                class="w-full border rounded p-3"
                placeholder="Ví dụ: Giao giờ hành chính, gọi trước khi giao..."></textarea>

        </div>

        <!-- PHƯƠNG THỨC THANH TOÁN -->

        <div class="border rounded-lg p-5 mb-6">

            <h3 class="text-xl font-bold mb-4">

                <i class="fa-solid fa-wallet"></i>

                Phương thức thanh toán

            </h3>

            <label class="flex items-center mb-4 cursor-pointer">

                <input
                    type="radio"
                    name="payment_method"
                    value="COD"
                    checked
                    onclick="changePayment()">

                <span class="ml-3">

                    Thanh toán khi nhận hàng (COD)

                </span>

            </label>

            <label class="flex items-center cursor-pointer">

                <input
                    type="radio"
                    name="payment_method"
                    value="ONLINE"
                    onclick="changePayment()">

                <span class="ml-3">

                    Thanh toán trước

                </span>

            </label>

            <div
                id="onlinePayment"
                class="border rounded-lg p-4 mt-5"
                style="display:none;">

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

                        Thanh toán qua ZaloPay

                    </span>

                </label>

            </div>

        </div>

        <!-- NÚT ĐẶT HÀNG -->

        <div class="flex justify-between">

            <a
                href="/fruit_shop/customer/cart/index.php"
                class="bg-gray-500 hover:bg-gray-600 text-white px-6 py-3 rounded">

                <i class="fa-solid fa-arrow-left"></i>

                Quay lại giỏ hàng

            </a>

            <button
                type="submit"
                class="bg-green-600 hover:bg-green-700 text-white px-8 py-3 rounded">

                <i class="fa-solid fa-check"></i>

                Xác nhận đặt hàng

            </button>

        </div>

    </form>

</div>