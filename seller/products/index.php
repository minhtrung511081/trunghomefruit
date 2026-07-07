<?php

session_start();

include("../../config/database.php");

if (!isset($_SESSION['user'])) {
    exit("Không có quyền");
}

?>


<div class="bg-white rounded-xl shadow p-6">


    <h2 class="text-3xl font-bold mb-6">

        Quản lý đơn hàng

    </h2>


    <table class="w-full border">


        <tr class="bg-green-700 text-white">

            <th>ID</th>
            <th>Khách hàng</th>
            <th>SĐT</th>
            <th>Tổng tiền</th>
            <th>Trạng thái</th>
            <th>Xử lý</th>

        </tr>


        <?php

        $sql="
        SELECT *
        FROM orders
        ORDER BY id DESC
        ";


        $result=$conn->query($sql);


        while($row=$result->fetch_assoc()){


        ?>


        <tr class="border">


            <td class="p-3">
                <?= $row['id'] ?>
            </td>


            <td>
                <?= $row['full_name'] ?>
            </td>


            <td>
                <?= $row['phone'] ?>
            </td>


            <td>
                <?= number_format($row['total']) ?> đ
            </td>


            <td>
                <?= $row['status'] ?>
            </td>


            <td>

                <button
                class="bg-blue-600 text-white px-3 py-2 rounded">

                Xem

                </button>

            </td>


        </tr>


        <?php } ?>


    </table>


</div>