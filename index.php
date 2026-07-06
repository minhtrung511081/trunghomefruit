<?php
session_start();
include 'config/database.php';
include 'includes/header.php';
include 'includes/navbar.php';
?>

<div class="max-w-7xl mx-auto mt-6">

    <div class="bg-orange-300 rounded-xl p-12 text-center">
        <h1 class="text-5xl font-bold">🎉 Khuyến mãi giờ vàng 🎉</h1>
        <p class="text-2xl mt-5">Giảm giá các sản phẩm gia dụng</p>
    </div>

    <h2 class="text-4xl mt-10 mb-8 font-bold">Danh sách sản phẩm</h2>

    <div class="grid grid-cols-4 gap-8">

        <?php
        $sql = "SELECT * FROM products";
        $result = $conn->query($sql);

        if ($result && $result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
        ?>

                <div class="card">
                    <img
                        src="/fruit_shop/assets/images/products/<?= htmlspecialchars($row['image']) ?>"
                        style="height:220px;width:100%;object-fit:cover;">

                    <h3 class="text-2xl mt-3">
                        <?= htmlspecialchars($row['product_name']) ?>
                    </h3>

                    <p class="price">
                        <?= number_format($row['price']) ?> đ / <?= htmlspecialchars($row['unit']) ?>
                    </p>

                    <!-- THÊM VÀO GIỎ -->
                    <?php if (isset($_SESSION['user'])) { ?>

                        <a href="customer/cart/add.php?id=<?= $row['id'] ?>"
                            class="bg-green-600 text-white w-full mt-4 p-3 rounded block text-center">
                            <i class="fa fa-cart-plus"></i>
                            Thêm vào giỏ
                        </a>

                    <?php } else { ?>

                        <a href="auth/login.php"
                            class="bg-green-600 text-white w-full mt-4 p-3 rounded block text-center">
                            <i class="fa fa-cart-plus"></i>
                            Thêm vào giỏ
                        </a>

                    <?php } ?>
                </div>

        <?php
            }
        }
        ?>

    </div>
</div>

<?php include 'includes/footer.php'; ?>