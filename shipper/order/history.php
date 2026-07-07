<?php

session_start();

include("../../config/database.php");


// kiểm tra đăng nhập

if (!isset($_SESSION['user'])) {

    header("Location: ../../login.php");
    exit;
}


$shipper_id = $_SESSION['user']['id'];




// lấy danh sách đơn đã hoàn thành

$sql = "

SELECT *

FROM orders

WHERE shipper_id = ?

AND status = 'Hoàn thành'

ORDER BY created_at DESC

";



$stmt = mysqli_prepare($conn, $sql);



mysqli_stmt_bind_param(

    $stmt,

    "i",

    $shipper_id

);



mysqli_stmt_execute($stmt);



$result = mysqli_stmt_get_result($stmt);



include("../../includes/header.php");

include("../../includes/navbar.php");

?>



<div class="max-w-6xl mx-auto mt-8">



    <div class="bg-white shadow rounded-xl p-8">



        <h2 class="text-3xl font-bold mb-6">

            Lịch sử giao hàng

        </h2>




        <?php if (mysqli_num_rows($result) == 0) { ?>



            <div class="text-center text-gray-500 py-10">


                Chưa có đơn hàng đã hoàn thành


            </div>



        <?php } else { ?>



            <table class="w-full border-collapse">



                <thead>


                    <tr class="bg-gray-100">


                        <th class="border p-3">

                            Mã đơn

                        </th>


                        <th class="border p-3">

                            Khách hàng

                        </th>


                        <th class="border p-3">

                            Số điện thoại

                        </th>


                        <th class="border p-3">

                            Tổng tiền

                        </th>


                        <th class="border p-3">

                            Ngày đặt

                        </th>


                        <th class="border p-3">

                            Thao tác

                        </th>


                    </tr>


                </thead>



                <tbody>



                    <?php while ($order = mysqli_fetch_assoc($result)) { ?>



                        <tr>



                            <td class="border p-3 text-center">

                                #<?= $order['id'] ?>

                            </td>



                            <td class="border p-3">

                                <?= htmlspecialchars($order['full_name']) ?>

                            </td>



                            <td class="border p-3">

                                <?= htmlspecialchars($order['phone']) ?>

                            </td>



                            <td class="border p-3">

                                <?= number_format($order['total']) ?> đ

                            </td>



                            <td class="border p-3">

                                <?= date(
                                    "d/m/Y H:i",
                                    strtotime($order['created_at'])
                                ) ?>

                            </td>



                            <td class="border p-3 text-center">



                                <a

                                    href="detail.php?id=<?= $order['id'] ?>"

                                    class="bg-blue-600 text-white px-4 py-2 rounded">


                                    Xem


                                </a>



                            </td>



                        </tr>



                    <?php } ?>



                </tbody>



            </table>



        <?php } ?>



    </div>



</div>



<?php

mysqli_stmt_close($stmt);

include("../../includes/footer.php");

?>