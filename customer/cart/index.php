<?php
session_start();
require_once __DIR__ . "/../../config/database.php";

if (!isset($_SESSION['user'])) {
    header("Location: /fruit_shop/login.php");
    exit;
}

require_once __DIR__ . "/../../includes/header.php";
require_once __DIR__ . "/../../includes/navbar.php";
?>

<div class="max-w-6xl mx-auto mt-8">

    <h2 class="text-4xl font-bold mb-6">
        Giỏ hàng
    </h2>

    <table class="w-full border">

        <tr class="bg-gray-200">

            <th>Ảnh</th>
            <th>Tên</th>
            <th>Giá</th>
            <th>SL</th>
            <th>Thành tiền</th>
            <th></th>

        </tr>

        <?php

        $total = 0;

        if (isset($_SESSION['cart'])) {

            foreach ($_SESSION['cart'] as $id => $qty) {

                $sql = "SELECT * FROM products WHERE id=$id";
                $row = mysqli_fetch_assoc(mysqli_query($conn, $sql));

                $subtotal = $row['price'] * $qty;

                $total += $subtotal;

        ?>

                <tr>

                    <td width="120">
                        <img src="/fruit_shop/assets/images/products/<?=
                                                                        $row['image']
                                                                        ?>" width="100">
                    </td>

                    <td><?= $row['product_name'] ?></td>

                    <td><?= number_format($row['price']) ?>đ</td>

                    <td>

                        <a href="update.php?id=<?= $id ?>&action=minus"
                            class="px-3 bg-gray-300">-</a>

                        <?= $qty ?>

                        <a href="update.php?id=<?= $id ?>&action=plus"
                            class="px-3 bg-gray-300">+</a>

                    </td>

                    <td><?= number_format($subtotal) ?>đ</td>

                    <td>

                        <a class="text-red-600"
                            href="remove.php?id=<?= $id ?>">
                            Xóa
                        </a>

                    </td>

                </tr>

        <?php

            }
        }

        ?>

        <tr>

            <td colspan="4" align="right">

                <b>Tổng tiền</b>

            </td>

            <td>

                <b><?= number_format($total) ?>đ</b>

            </td>

            <td></td>

        </tr>

    </table>

    <div class="mt-6 flex gap-3">
        <a href="/fruit_shop/index.php"
            class="bg-blue-500 hover:bg-blue-600 text-white px-5 py-2 rounded">
            <i class="fa fa-arrow-left"></i>
            Tiếp tục mua
        </a>

        <a href="checkout.php"
            class="bg-green-600 hover:bg-green-700 text-white px-5 py-2 rounded">
            <i class="fa fa-shopping-bag"></i>
            Đặt hàng
        </a>
    </div>

</div>

<?php require_once __DIR__ . "/../../includes/footer.php"; ?>