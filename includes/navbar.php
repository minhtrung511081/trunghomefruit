<nav class="bg-green-700 text-white shadow-lg">

    <div class="max-w-7xl mx-auto flex justify-between items-center p-4">

        <a href="/fruit_shop/index.php" class="text-2xl font-bold">
            🍉 Cửa Hàng Gia Dụng và Trái Cây
        </a>

        <div class="flex items-center gap-5">

            <?php

            $count = 0;

            if (isset($_SESSION['cart'])) {

                $count = isset($_SESSION['cart']) ? count($_SESSION['cart']) : 0;
            }

            ?>

            <a href="/fruit_shop/customer/cart/index.php">

                <i class="fa fa-shopping-cart"></i>

                Giỏ hàng

                (<?= $count ?>)

            </a>

            <a href="/fruit_shop/orders/index.php">
                <i class="fa-solid fa-box"></i>
                Đơn hàng
            </a>

            <?php if (isset($_SESSION['user'])) { ?>

                <span>
                    Xin chào,
                    <strong><?= $_SESSION['user']['fullname']; ?></strong>
                </span>

                <a href="/fruit_shop/auth/logout.php">
                    <i class="fa-solid fa-right-from-bracket"></i>
                    Đăng xuất
                </a>

            <?php } else { ?>

                <a href="auth/login.php">
                    <i class="fa-solid fa-user"></i>
                    Đăng nhập
                </a>

            <?php } ?>

        </div>

    </div>

</nav>