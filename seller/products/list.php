<?php

session_start();

include("../../config/database.php");

if (!isset($_SESSION['user'])) {
    header("Location: ../../auth/login.php");
    exit();
}

?>

<!DOCTYPE html>

<html lang="vi">

<head>

    <meta charset="UTF-8">

    <title>Danh sách sản phẩm</title>

    <script src="https://cdn.tailwindcss.com"></script>

    <link rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">

</head>

<body class="bg-gray-100">

    <div class="max-w-7xl mx-auto mt-10">

        <div class="bg-white p-6 rounded-xl shadow">

            <div class="flex justify-between mb-5">

                <h2 class="text-3xl font-bold">

                    Danh sách sản phẩm

                </h2>

                <a
                    href="#"
                    id="btn-add"
                    class="bg-green-600 text-white px-5 py-3 rounded">

                    <i class="fa fa-plus"></i>

                    Thêm sản phẩm

                </a>

            </div>

            <form method="GET">

                <input

                    type="text"

                    name="keyword"

                    placeholder="Tìm theo tên"

                    class="border rounded p-3 w-80">

                <button

                    class="bg-blue-600 text-white px-5 py-3 rounded">

                    Tìm

                </button>

            </form>

            <table class="w-full mt-6 border">

                <tr class="bg-green-700 text-white">

                    <th>ID</th>

                    <th>Hình</th>

                    <th>Tên sản phẩm</th>

                    <th>Loại</th>

                    <th>Giá</th>

                    <th>Đơn vị</th>

                    <th>Thao tác</th>

                </tr>

                <?php


                $seller_id = $_SESSION['user']['id'];

                if (!empty($_GET['keyword'])) {

                    $key = mysqli_real_escape_string($conn, $_GET['keyword']);

                    $sql = "
        SELECT *
        FROM products
        WHERE seller_id = $seller_id
        AND product_name LIKE '%$key%'
        ORDER BY id DESC
    ";
                } else {

                    $sql = "
        SELECT *
        FROM products
        WHERE seller_id = $seller_id
        ORDER BY id DESC
    ";
                }

                $result = $conn->query($sql);

                while ($row = $result->fetch_assoc()) {

                ?>

                    <tr class="border">

                        <td class="p-3">

                            <?= $row['id']; ?>

                        </td>

                        <td>

                            <img
                                src="/fruit_shop/assets/images/products/<?= htmlspecialchars($row['image']) ?>"
                                class="w-20 h-20 object-cover rounded">

                        </td>

                        <td>

                            <?= $row['product_name']; ?>

                        </td>

                        <td>

                            <?= $row['category']; ?>

                        </td>

                        <td>

                            <?= number_format($row['price']); ?> đ

                        </td>

                        <td>

                            <?= $row['unit']; ?>

                        </td>

                        <td>

                            <a
                                href="#"
                                class="btn-edit bg-blue-600 text-white px-3 py-2 rounded"
                                data-id="<?= $row['id'] ?>">
                                Sửa
                            </a>

                            <a
                                href="#"
                                class="btn-delete bg-red-600 text-white px-3 py-2 rounded"
                                data-id="<?= $row['id'] ?>">

                                Xóa

                            </a>

                        </td>

                    </tr>

                <?php

                }

                ?>

            </table>

        </div>

    </div>

    <script>
        $(document).on("click", ".btn-delete", function(e) {

            e.preventDefault();


            if (!confirm("Xóa sản phẩm?"))
                return;



            let id = $(this).data("id");



            $("#content").load(
                "/fruit_shop/seller/products/delete.php?id=" + id
            );


        });

        $(document).on("click", "#btn-add", function(e) {

            e.preventDefault();


            $("#content").load(
                "/fruit_shop/seller/products/add.php",
                function(response, status, xhr) {


                    if (status == "error") {

                        console.log("Lỗi:", xhr.status);
                        console.log(xhr.responseText);

                        alert("Không tải được add.php");

                    }


                }
            );


        });

        $(document).off("click", ".btn-edit");

        $(document).on("click", ".btn-edit", function(e) {

            e.preventDefault();

            let id = $(this).data("id");

            console.log("Edit ID:", id);

            $("#content").load(
                "/fruit_shop/seller/products/edit.php?id=" + id,
                function(response, status, xhr) {

                    if (status == "error") {

                        console.log(xhr.responseText);

                        alert("Không tải được edit.php");

                    }

                }
            );

        });

        $(document).on("submit", "#form-add", function(e) {

            e.preventDefault();


            let formData = new FormData(this);


            $.ajax({

                url: "/fruit_shop/seller/products/save.php",

                type: "POST",

                data: formData,

                processData: false,

                contentType: false,


                success: function(response) {


                    console.log(response);


                    $("#content").load(
                        "/fruit_shop/seller/products/list.php"
                    );


                },


                error: function(xhr) {

                    console.log(xhr.responseText);

                    alert("Lỗi lưu sản phẩm");

                }


            });


        });
    </script>

</body>



</html>