<?php

session_start();

include("../config/database.php");


// kiểm tra đăng nhập

if (!isset($_SESSION['user'])) {

    header("Location: ../login.php");
    exit;
}


$shipper_id = $_SESSION['user']['id'];



// Tổng đơn

$sql = "

SELECT COUNT(*) AS total

FROM orders

WHERE shipper_id = ?

";


$stmt = mysqli_prepare($conn, $sql);

mysqli_stmt_bind_param(
    $stmt,
    "i",
    $shipper_id
);

mysqli_stmt_execute($stmt);

$result = mysqli_stmt_get_result($stmt);

$total_orders = mysqli_fetch_assoc($result)['total'];

mysqli_stmt_close($stmt);





// Đơn đang chờ

$sql = "

SELECT COUNT(*) AS total

FROM orders

WHERE shipper_id = ?

AND status IN ('Chờ xác nhận','Đã xác nhận')

";


$stmt = mysqli_prepare($conn, $sql);


mysqli_stmt_bind_param(
    $stmt,
    "i",
    $shipper_id
);


mysqli_stmt_execute($stmt);


$result = mysqli_stmt_get_result($stmt);


$pending_orders = mysqli_fetch_assoc($result)['total'];

mysqli_stmt_close($stmt);





// Đang giao

$sql = "

SELECT COUNT(*) AS total

FROM orders

WHERE shipper_id = ?

AND status='Đang giao'

";


$stmt = mysqli_prepare($conn, $sql);


mysqli_stmt_bind_param(
    $stmt,
    "i",
    $shipper_id
);


mysqli_stmt_execute($stmt);


$result = mysqli_stmt_get_result($stmt);


$shipping_orders = mysqli_fetch_assoc($result)['total'];

mysqli_stmt_close($stmt);





// Hoàn thành

$sql = "

SELECT COUNT(*) AS total

FROM orders

WHERE shipper_id = ?

AND status='Hoàn thành'

";


$stmt = mysqli_prepare($conn, $sql);


mysqli_stmt_bind_param(
    $stmt,
    "i",
    $shipper_id
);


mysqli_stmt_execute($stmt);


$result = mysqli_stmt_get_result($stmt);


$completed_orders = mysqli_fetch_assoc($result)['total'];

mysqli_stmt_close($stmt);






// đơn mới nhất

$sql = "

SELECT *

FROM orders

WHERE shipper_id = ?

ORDER BY created_at DESC

LIMIT 5

";


$stmt = mysqli_prepare($conn, $sql);


mysqli_stmt_bind_param(
    $stmt,
    "i",
    $shipper_id
);


mysqli_stmt_execute($stmt);


$orders = mysqli_stmt_get_result($stmt);



include("../includes/header.php");

include("../includes/navbar.php");

?>



<div class="max-w-6xl mx-auto mt-8">



    <h1 class="text-3xl font-bold mb-8">

        Dashboard Shipper

    </h1>




    <div class="grid grid-cols-4 gap-6">



        <div class="bg-white shadow rounded-xl p-6">

            <h3 class="text-gray-500">

                Tổng đơn

            </h3>

            <p class="text-3xl font-bold mt-3">

                <?= $total_orders ?>

            </p>

        </div>




        <div class="bg-white shadow rounded-xl p-6">

            <h3 class="text-gray-500">

                Chờ xử lý

            </h3>

            <p class="text-3xl font-bold mt-3">

                <?= $pending_orders ?>

            </p>

        </div>




        <div class="bg-white shadow rounded-xl p-6">

            <h3 class="text-gray-500">

                Đang giao

            </h3>

            <p class="text-3xl font-bold mt-3">

                <?= $shipping_orders ?>

            </p>

        </div>




        <div class="bg-white shadow rounded-xl p-6">

            <h3 class="text-gray-500">

                Hoàn thành

            </h3>

            <p class="text-3xl font-bold mt-3">

                <?= $completed_orders ?>

            </p>

        </div>



    </div>






    <div class="bg-white shadow rounded-xl mt-8 p-6">



        <h2 class="text-2xl font-bold mb-5">

            Đơn hàng gần đây

        </h2>




        <table class="w-full border">



            <tr class="bg-gray-100">


                <th class="border p-3">

                    Mã đơn

                </th>


                <th class="border p-3">

                    Khách hàng

                </th>


                <th class="border p-3">

                    Tổng tiền

                </th>


                <th class="border p-3">

                    Trạng thái

                </th>


                <th class="border p-3">

                    Xem

                </th>


            </tr>




            <?php while ($order = mysqli_fetch_assoc($orders)) { ?>

                <tr>


                    <td class="border p-3 text-center">

                        #<?= $order['id'] ?>

                    </td>


                    <td class="border p-3">

                        <?= htmlspecialchars($order['full_name']) ?>

                    </td>


                    <td class="border p-3">

                        <?= number_format($order['total']) ?> đ

                    </td>


                    <td class="border p-3">

                        <?= $order['status'] ?>

                    </td>


                    <td class="border p-3 text-center">


                        <a

                            href="order/detail.php?id=<?= $order['id'] ?>"

                            class="bg-blue-600 text-white px-4 py-2 rounded">


                            Xem


                        </a>


                    </td>


                </tr>


            <?php } ?>



        </table>


    </div>



</div>



<?php

mysqli_stmt_close($stmt);

include("../includes/footer.php");

?>