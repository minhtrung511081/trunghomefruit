<?php
session_start();

require_once __DIR__ . "/../../includes/header.php";
require_once __DIR__ . "/../../includes/navbar.php";
?>

<div class="max-w-2xl mx-auto mt-16">

    <div class="bg-white rounded-lg shadow-lg p-10 text-center">

        <div class="text-7xl text-green-600 mb-5">
            <i class="fa-solid fa-circle-check"></i>
        </div>

        <h2 class="text-3xl font-bold text-green-600 mb-4">
            Đặt hàng thành công
        </h2>

        <p class="text-gray-600 mb-8">
            Cảm ơn bạn đã mua hàng tại
            <strong>Cửa Hàng Gia Dụng và Trái Cây</strong>.
        </p>

        <div class="flex justify-center gap-4">

            <a href="/fruit_shop/index.php"
                class="bg-green-600 hover:bg-green-700 text-white px-6 py-3 rounded">

                <i class="fa-solid fa-house"></i>

                Tiếp tục mua sắm

            </a>

            <a href="/fruit_shop/orders/index.php"
                class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded">

                <i class="fa-solid fa-box"></i>

                Đơn hàng của tôi

            </a>

        </div>

    </div>

</div>

<?php
require_once __DIR__ . "/../../includes/footer.php";
?>