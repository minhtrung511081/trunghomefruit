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

if ($order['status'] == "Hoàn thành") {
    echo "
    <script>
        alert('Đơn hàng đã hoàn thành, không thể cập nhật nữa.');
        window.location.href='detail.php?id={$order_id}';
    </script>";
    exit;
}

mysqli_stmt_close($stmt);


// xử lý cập nhật

if ($_SERVER['REQUEST_METHOD'] == "POST") {


    $status = $_POST['status'];

    $current = $order['status'];

    if ($current == "Đã xác nhận" && $status != "Đang giao") {

        echo "
    <script>
        alert('Chỉ được chuyển sang trạng thái Đang giao.');
        history.back();
    </script>";
        exit;
    }

    if (
        $current == "Đang giao" &&
        !in_array($status, ["Hoàn thành", "Đã hủy"])
    ) {
        echo "
    <script>
        alert('Chỉ được chuyển sang Hoàn thành hoặc Đã hủy.');
        history.back();
    </script>";
        exit;
    }

    if ($current == "Hoàn thành") {

        echo "
    <script>
        alert('Đơn hàng đã hoàn thành.');
        window.location.href='detail.php?id={$order_id}';
    </script>";
        exit;
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

            <select name="status" class="w-full border rounded-lg p-3">

                <?php if ($order['status'] == "Đã xác nhận") { ?>

                    <option value="Đang giao">
                        Đang giao
                    </option>

                <?php } elseif ($order['status'] == "Đang giao") { ?>

                    <option value="Hoàn thành">
                        Hoàn thành
                    </option>

                    <option value="Đã hủy">
                        Không nhận hàng (Hủy đơn)
                    </option>

                <?php } elseif ($order['status'] == "Hoàn thành") { ?>

                    <option disabled selected>
                        Đơn hàng đã hoàn thành
                    </option>

                <?php } elseif ($order['status'] == "Đã hủy") { ?>

                    <option disabled selected>
                        Đơn hàng đã hủy
                    </option>

                <?php } else { ?>

                    <option disabled selected>
                        Không thể cập nhật
                    </option>

                <?php } ?>

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

<script>
    document.querySelector("form").addEventListener("submit", function(e) {

        let status = document.querySelector("select[name='status']").value;

        if (status === "Đã hủy") {

            if (!confirm("Xác nhận khách không nhận hàng và hủy đơn?")) {
                e.preventDefault();
            }

        }

    });
</script>

<?php

include("../../includes/footer.php");

?>