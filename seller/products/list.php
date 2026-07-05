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
                    href="add.php"
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

                $where = "";

                if (isset($_GET['keyword'])) {

                    $key = $_GET['keyword'];

                    $where = " WHERE product_name LIKE '%$key%' ";
                }

                $sql = "SELECT * FROM products $where ORDER BY id DESC";

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

                                onclick="return confirm('Xóa sản phẩm?')"

                                href="delete.php?id=<?= $row['id']; ?>"

                                class="bg-red-600 text-white px-3 py-2 rounded">

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
        $(document).off("click", ".btn-edit");

        $(document).on("click", ".btn-edit", function(e) {

            e.preventDefault();

            let id = $(this).data("id");

            console.log("Edit ID:", id);

            $("#content").load("products/edit.php?id=" + id, function(response, status, xhr) {

                if (status == "error") {
                    console.log(xhr.status);
                    console.log(xhr.responseText);
                    alert("Không tải được edit.php");
                }

            });

        });
    </script>

</body>



</html>