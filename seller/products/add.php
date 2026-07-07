<?php
session_start();

if (!isset($_SESSION['user'])) {
    header("Location: ../../auth/login.php");
    exit();
}
?>

<!DOCTYPE html>

<html>

<head>

    <meta charset="UTF-8">

    <title>Thêm sản phẩm</title>

    <script src="https://cdn.tailwindcss.com"></script>

</head>

<body class="bg-gray-100">

    <div class="max-w-3xl mx-auto mt-10">

        <div class="bg-white p-8 rounded-xl shadow">

            <h2 class="text-3xl font-bold mb-8">

                Thêm sản phẩm

            </h2>

            <form
                id="addForm"

                method="POST"

                enctype="multipart/form-data">

                <label>

                    Tên sản phẩm

                </label>

                <input

                    type="text"

                    name="product_name"

                    class="border w-full p-3 rounded mb-5"

                    required>

                <label>

                    Loại

                </label>

                <select

                    name="category"

                    class="border w-full p-3 rounded mb-5">

                    <option>Cau</option>

                    <option>Hạnh</option>

                    <option>Ớt</option>

                    <option>Gia dụng</option>

                </select>

                <label>

                    Đơn vị

                </label>

                <select

                    name="unit"

                    class="border w-full p-3 rounded mb-5">

                    <option>kg</option>

                    <option>cái</option>

                </select>

                <label>

                    Giá

                </label>

                <input

                    type="number"

                    name="price"

                    class="border w-full p-3 rounded mb-5"

                    required>

                <label>

                    Hình ảnh

                </label>

                <input

                    type="file"

                    name="image"

                    class="border w-full p-3 rounded mb-5">

                <label>

                    Mô tả

                </label>

                <textarea

                    name="description"

                    class="border w-full p-3 rounded mb-5">

</textarea>

                <button

                    class="bg-green-600 text-white px-8 py-3 rounded">

                    Lưu

                </button>

            </form>

        </div>

    </div>

    <script>
        $("#addForm").on("submit", function(e) {


            e.preventDefault();


            let formData = new FormData(this);



            $.ajax({

                url: "/fruit_shop/seller/products/save.php",

                type: "POST",

                data: formData,

                processData: false,

                contentType: false,


                success: function(res) {


                    console.log(res);


                    if (res.trim() == "success") {


                        alert("Thêm sản phẩm thành công");


                        $("#content").load(
                            "/fruit_shop/seller/products/list.php"
                        );


                    } else {


                        alert(res);


                    }


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