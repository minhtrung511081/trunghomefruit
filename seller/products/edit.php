<?php
session_start();
include("../../config/database.php");

if (!isset($_SESSION['user']) || $_SESSION['user']['role_name'] != "Seller") {
    exit("Không có quyền truy cập.");
}

if (!isset($_GET['id'])) {
    exit("Thiếu ID sản phẩm.");
}

$id = (int)$_GET['id'];

            $seller_id = $_SESSION['user']['id'];

            $stmt = $conn->prepare("
    SELECT *
    FROM products
    WHERE id = ?
    AND seller_id = ?
");

            $stmt->bind_param("ii", $id, $seller_id);

            $stmt->execute();

$result = $stmt->get_result();

if ($result->num_rows == 0) {
    exit("Không tìm thấy sản phẩm.");
}

$product = $result->fetch_assoc();
?>

<div class="bg-white rounded-xl shadow p-6">

    <h2 class="text-3xl font-bold text-green-700 mb-6">
        Sửa sản phẩm
    </h2>

    <form id="editForm"

        method="POST"
        enctype="multipart/form-data">

        <input type="hidden"
            name="id"
            value="<?= $product['id']; ?>">

        <div class="mb-4">
            <label class="font-semibold">Tên sản phẩm</label>

            <input
                type="text"
                name="product_name"
                class="w-full border rounded-lg p-3 mt-2"
                value="<?= htmlspecialchars($product['product_name']); ?>"
                required>
        </div>

        <div class="grid grid-cols-2 gap-5">

            <div>

                <label class="font-semibold">Danh mục</label>

                <input
                    type="text"
                    name="category"
                    class="w-full border rounded-lg p-3 mt-2"
                    value="<?= htmlspecialchars($product['category']); ?>">

            </div>

            <div>

                <label class="font-semibold">Đơn vị</label>

                <input
                    type="text"
                    name="unit"
                    class="w-full border rounded-lg p-3 mt-2"
                    value="<?= htmlspecialchars($product['unit']); ?>">

            </div>

        </div>

        <div class="mt-4">

            <label class="font-semibold">Giá</label>

            <input
                type="number"
                name="price"
                class="w-full border rounded-lg p-3 mt-2"
                value="<?= $product['price']; ?>"
                required>

        </div>

        <div class="mt-4">

            <label class="font-semibold">Mô tả</label>

            <textarea
                name="description"
                rows="5"
                class="w-full border rounded-lg p-3 mt-2"><?= htmlspecialchars($product['description']); ?></textarea>

        </div>

        <div class="mt-5">

            <label class="font-semibold">Ảnh hiện tại</label>

            <br><br>

            <?php if (!empty($product['image'])) { ?>

                <img
                    id="preview"
                    src="/fruit_shop/assets/images/products/<?= $product['image']; ?>"
                    class="w-40 h-40 object-cover rounded border">

            <?php } ?>

        </div>

        <div class="mt-5">

            <label class="font-semibold">Ảnh mới</label>

            <input
                type="file"
                id="image"
                name="image"
                class="w-full border rounded-lg p-3 mt-2">

        </div>

        <div class="mt-8 flex gap-3">

            <button
                type="submit"
                class="bg-green-600 hover:bg-green-700 text-white px-8 py-3 rounded-lg">

                💾 Cập nhật

            </button>

            <button
                type="button"
                id="btnBack"
                class="bg-gray-600 hover:bg-gray-700 text-white px-8 py-3 rounded-lg">

                ← Quay lại

            </button>

        </div>

    </form>

</div>

<script>
    $("#editForm").on("submit", function(e) {

        e.preventDefault();

        let formData = new FormData(this);

        $.ajax({
            url: "/fruit_shop/seller/products/update.php",
            type: "POST",
            data: formData,
            processData: false,
            contentType: false,

            success: function(res) {

                if (res.trim() == "success") {

                    alert("Cập nhật thành công");

                    $("#content").load("products/list.php");

                } else {

                    alert(res);

                }

            }

        });

    });

    $("#image").change(function() {

        let file = this.files[0];

        if (file) {

            $("#preview").attr("src", URL.createObjectURL(file));

        }

    });

    $("#btnBack").click(function() {

        $("#content").load("products/list.php");

    });
</script>