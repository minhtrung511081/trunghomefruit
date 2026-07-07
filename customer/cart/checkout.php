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
        WHERE user_id=?
        ORDER BY is_default DESC,id DESC";

$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "i", $user['id']);
mysqli_stmt_execute($stmt);

$addressResult = mysqli_stmt_get_result($stmt);

?>

<div class="max-w-5xl mx-auto mt-8">

    <div class="bg-white rounded-lg shadow p-6">

        <h2 class="text-3xl font-bold mb-6 text-center">

            Xác nhận đơn hàng

        </h2>

        <form action="place_order.php" method="POST">

            <!-- ĐỊA CHỈ -->

            <div class="border rounded-lg p-5 mb-6">

                <div class="flex justify-between items-center mb-4">

                    <h3 class="text-xl font-bold">

                        <i class="fa-solid fa-location-dot text-red-500"></i>

                        Địa chỉ giao hàng

                    </h3>

                    <a
                        href="/fruit_shop/customer/addresses/index.php"
                        class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded">

                        <i class="fa-solid fa-address-book"></i>

                        Quản lý địa chỉ

                    </a>

                </div>

                <?php

                if (mysqli_num_rows($addressResult) > 0) {

                    while ($address = mysqli_fetch_assoc($addressResult)) {

                ?>

                        <label
                            class="border rounded-lg p-4 mb-3 block cursor-pointer hover:bg-gray-50">

                            <div class="flex">

                                <input
                                    type="radio"
                                    name="address_id"
                                    value="<?= $address['id']; ?>"
                                    <?= $address['is_default'] ? 'checked' : ''; ?>
                                    required>

                                <div class="ml-4 flex-1">

                                    <div class="font-bold text-lg">

                                        <?= htmlspecialchars($address['full_name']); ?>

                                    </div>

                                    <div class="mt-2">

                                        <i class="fa-solid fa-phone"></i>

                                        <?= htmlspecialchars($address['phone']); ?>

                                    </div>

                                    <div class="mt-2">

                                        <i class="fa-solid fa-location-dot"></i>

                                        <?= htmlspecialchars($address['address']); ?>

                                    </div>

                                    <?php if (!empty($address['address_detail'])) { ?>

                                        <div class="text-gray-500 mt-2">

                                            <?= htmlspecialchars($address['address_detail']); ?>

                                        </div>

                                    <?php } ?>

                                </div>

                            </div>

                        </label>

                    <?php

                    }
                } else {

                    ?>

                    <div class="bg-yellow-100 border rounded-lg p-5">

                        <p class="mb-4">

                            Bạn chưa có địa chỉ giao hàng.

                        </p>

                        <a
                            href="/fruit_shop/customer/addresses/index.php"
                            class="bg-green-600 text-white px-4 py-2 rounded">

                            <i class="fa-solid fa-plus"></i>

                            Thêm địa chỉ

                        </a>

                    </div>

                <?php } ?>

            </div>

            <!-- DANH SÁCH SẢN PHẨM -->

            <div class="border rounded-lg p-5 mb-6">

                <h3 class="text-xl font-bold text-green-600 mb-5">

                    <i class="fa-solid fa-cart-shopping"></i>

                    Sản phẩm đặt mua

                </h3>

                <?php

                $tongTien = 0;

                foreach ($_SESSION['cart'] as $product_id => $quantity) {

                    $product_id = (int)$product_id;
                    $quantity   = (float)$quantity;

                    $sqlProduct = "SELECT * FROM products WHERE id=? LIMIT 1";

                    $stmtProduct = mysqli_prepare($conn, $sqlProduct);

                    mysqli_stmt_bind_param($stmtProduct, "i", $product_id);

                    mysqli_stmt_execute($stmtProduct);

                    $resultProduct = mysqli_stmt_get_result($stmtProduct);

                    if (!$product = mysqli_fetch_assoc($resultProduct)) {
                        continue;
                    }

                    $price = (float)$product['price'];

                    $subTotal = $price * $quantity;

                    $tongTien += $subTotal;

                ?>

                    <div class="flex items-center border-b py-4">

                        <img
                            src="/fruit_shop/assets/images/products/<?= htmlspecialchars($product['image']) ?>"
                            class="w-24 h-24 object-cover rounded border">

                        <div class="ml-4 flex-1">

                            <div class="text-lg font-bold">

                                <?= htmlspecialchars($product['product_name']) ?>

                            </div>

                            <div class="mt-2 text-red-600 font-semibold">

                                <?= number_format($price) ?> đ

                            </div>

                            <div class="mt-2">

                                Số lượng:

                                <strong>

                                    <?= $quantity ?>

                                </strong>

                                <?php
                                if (!empty($product['unit'])) {
                                    echo htmlspecialchars($product['unit']);
                                }
                                ?>

                            </div>

                        </div>

                        <div class="text-right">

                            <div class="text-red-600 text-xl font-bold">

                                <?= number_format($subTotal) ?> đ

                            </div>

                        </div>

                    </div>

                <?php

                }

                ?>

                <div class="text-right mt-5">

                    <span class="text-xl">

                        Tổng tiền:

                    </span>

                    <span class="text-2xl font-bold text-red-600">

                        <?= number_format($tongTien) ?> đ

                    </span>

                </div>

            </div>

            <!-- GHI CHÚ -->

            <div class="mb-6">

                <label class="font-semibold block mb-2">

                    Ghi chú

                </label>

                <textarea
                    name="note"
                    rows="4"
                    class="w-full border rounded p-3"
                    placeholder="Nhập ghi chú nếu có..."></textarea>

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

                        Thanh toán chuyển khoản

                    </span>

                </label>

                <div
                    id="onlinePayment"
                    class="border rounded-lg p-4 mt-5"
                    style="display:none;">

                    <div class="mb-3 font-semibold">

                        Chọn hình thức thanh toán

                    </div>

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

                            Thanh toán bằng ZaloPay

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

                    <i class="fa-solid fa-circle-check"></i>

                    Xác nhận đặt hàng

                </button>

            </div>

        </form>

    </div>

    <script>
        function changePayment() {

            let online = document.querySelector(
                'input[value="ONLINE"]'
            ).checked;

            document.getElementById("onlinePayment").style.display =
                online ? "block" : "none";

        }

        changePayment();
    </script>

    <script>
        document.addEventListener("DOMContentLoaded", function() {

            let address = document.querySelector("input[name='address_id']");
            let btn = document.querySelector("button[type='submit']");

            if (!address) {

                btn.disabled = true;

                btn.classList.remove(
                    "bg-green-600",
                    "hover:bg-green-700"
                );

                btn.classList.add(
                    "bg-gray-400",
                    "cursor-not-allowed"
                );

                btn.innerHTML =
                    '<i class="fa-solid fa-triangle-exclamation"></i> Vui lòng thêm địa chỉ giao hàng';

            }

        });
    </script>

</div>

<?php require_once __DIR__ . "/../../includes/footer.php"; ?>