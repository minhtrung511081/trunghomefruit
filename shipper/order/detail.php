<?php
session_start();

include("../../config/database.php");


if (!isset($_SESSION['user'])) {

    header("Location: ../../login.php");
    exit;
}


$shipper_id = $_SESSION['user']['id'];



if (!isset($_GET['id'])) {

    header("Location:index.php");
    exit;
}


$order_id = intval($_GET['id']);



// lấy thông tin đơn hàng

$sql = "

SELECT *

FROM orders

WHERE id = ?

AND shipper_id = ?

";


$stmt = mysqli_prepare($conn, $sql);


mysqli_stmt_bind_param(

    $stmt,

    "ii",

    $order_id,

    $shipper_id

);


mysqli_stmt_execute($stmt);


$result = mysqli_stmt_get_result($stmt);


if (mysqli_num_rows($result) == 0) {

    header("Location:index.php");

    exit;
}


$order = mysqli_fetch_assoc($result);



mysqli_stmt_close($stmt);




// lấy sản phẩm trong đơn

$sql = "

SELECT 

order_details.*,

products.name,

products.image

FROM order_details

JOIN products

ON order_details.product_id = products.id

WHERE order_id = ?

";



$stmt = mysqli_prepare($conn, $sql);


mysqli_stmt_bind_param(

    $stmt,

    "i",

    $order_id

);


mysqli_stmt_execute($stmt);


$items = mysqli_stmt_get_result($stmt);



include("../../includes/header.php");

include("../../includes/navbar.php");

?>



<div class="max-w-5xl mx-auto mt-8">


    <div class="bg-white shadow rounded-xl p-8">


        <h2 class="text-3xl font-bold mb-6">

            Chi tiết đơn hàng #<?= $order['id'] ?>

        </h2>



        <div class="grid grid-cols-2 gap-5">


            <div>

                <b>Khách hàng:</b>

                <?= htmlspecialchars($order['full_name']) ?>

            </div>


            <div>

                <b>SĐT:</b>

                <?= htmlspecialchars($order['phone']) ?>

            </div>


            <div class="col-span-2">

                <b>Địa chỉ:</b>

                <?= htmlspecialchars($order['address']) ?>

            </div>


            <div>

                <b>Tổng tiền:</b>

                <?= number_format($order['total']) ?> đ

            </div>


            <div>

                <b>Trạng thái:</b>

                <?= $order['status'] ?>

            </div>


            <div>

                <b>Thanh toán:</b>

                <?= $order['payment_method'] ?>

            </div>


            <div>

                <b>Tình trạng:</b>

                <?= $order['payment_status'] ?>

            </div>



        </div>




        <hr class="my-6">



        <h3 class="text-xl font-bold mb-4">

            Sản phẩm

        </h3>



        <table class="w-full border">


            <tr class="bg-gray-100">

                <th class="p-3 border">

                    Tên

                </th>


                <th class="p-3 border">

                    Số lượng

                </th>


                <th class="p-3 border">

                    Giá

                </th>

            </tr>



            <?php while ($item = mysqli_fetch_assoc($items)) { ?>

                <tr>


                    <td class="p-3 border">

                        <?= htmlspecialchars($item['name']) ?>

                    </td>


                    <td class="p-3 border text-center">

                        <?= $item['quantity'] ?>

                    </td>


                    <td class="p-3 border">

                        <?= number_format($item['price']) ?> đ

                    </td>


                </tr>


            <?php } ?>


        </table>



        <div class="mt-6">


            <a href="update_status.php?id=<?= $order['id'] ?>"

                class="bg-green-600 text-white px-5 py-3 rounded">

                Cập nhật trạng thái

            </a>


        </div>



    </div>


</div>



<?php

include("../../includes/footer.php");

?>