<?php

session_start();

include("../../config/database.php");


// kiểm tra đăng nhập

if (!isset($_SESSION['user'])) {

    header("Location: ../../login.php");
    exit;
}


$shipper_id = $_SESSION['user']['id'];



// kiểm tra id đơn

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {

    header("Location:index.php");
    exit;
}


$order_id = intval($_GET['id']);




// lấy đơn hàng

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




// xử lý cập nhật

if ($_SERVER['REQUEST_METHOD'] == "POST") {


    $status = $_POST['status'];



    $allow = [

        "Chờ xác nhận",

        "Đã xác nhận",

        "Đang giao",

        "Hoàn thành"

    ];



    if (!in_array($status, $allow)) {


        die("Trạng thái không hợp lệ");
    }




    $sql = "

    UPDATE orders

    SET status = ?

    WHERE id = ?

    AND shipper_id = ?

    ";




    $stmt = mysqli_prepare($conn, $sql);



    mysqli_stmt_bind_param(

        $stmt,

        "sii",

        $status,

        $order_id,

        $shipper_id

    );



    mysqli_stmt_execute($stmt);



    mysqli_stmt_close($stmt);



    header("Location:detail.php?id=" . $order_id . "&updated=1");

    exit;
}



include("../../includes/header.php");

include("../../includes/navbar.php");

?>



<div class="max-w-xl mx-auto mt-10">


    <div class="bg-white shadow rounded-xl p-8">



        <h2 class="text-2xl font-bold mb-6">

            Cập nhật trạng thái đơn #<?= $order['id'] ?>

        </h2>




        <form method="POST">



            <label class="block mb-2 font-semibold">

                Trạng thái hiện tại:

            </label>



            <select

                name="status"

                class="w-full border rounded-lg p-3">



                <option value="Chờ xác nhận"

                    <?= $order['status'] == "Chờ xác nhận" ? "selected" : "" ?>>

                    Chờ xác nhận

                </option>



                <option value="Đã xác nhận"

                    <?= $order['status'] == "Đã xác nhận" ? "selected" : "" ?>>

                    Đã xác nhận

                </option>



                <option value="Đang giao"

                    <?= $order['status'] == "Đang giao" ? "selected" : "" ?>>

                    Đang giao

                </option>



                <option value="Hoàn thành"

                    <?= $order['status'] == "Hoàn thành" ? "selected" : "" ?>>

                    Hoàn thành

                </option>



            </select>




            <button

                type="submit"

                class="mt-6 bg-green-600 text-white px-6 py-3 rounded-lg">

                Lưu trạng thái

            </button>



            <a

                href="detail.php?id=<?= $order['id'] ?>"

                class="ml-3 bg-gray-500 text-white px-6 py-3 rounded-lg">

                Quay lại

            </a>



        </form>



    </div>

</div>



<?php

include("../../includes/footer.php");

?>