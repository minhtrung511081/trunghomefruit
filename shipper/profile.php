<?php

session_start();

include("../config/database.php");


// kiểm tra đăng nhập

if (!isset($_SESSION['user'])) {

    header("Location: ../login.php");
    exit;
}


$user_id = $_SESSION['user']['id'];



// lấy thông tin user

$sql = "
    SELECT *
    FROM users
    WHERE id = ?
";


$stmt = mysqli_prepare($conn, $sql);


mysqli_stmt_bind_param(
    $stmt,
    "i",
    $user_id
);


mysqli_stmt_execute($stmt);


$result = mysqli_stmt_get_result($stmt);


$user = mysqli_fetch_assoc($result);


mysqli_stmt_close($stmt);



if (!$user) {

    header("Location: ../login.php");
    exit;
}




// cập nhật thông tin

if ($_SERVER['REQUEST_METHOD'] == "POST") {


    $fullname = $_POST['fullname'];

    $phone = $_POST['phone'];


    // giữ avatar cũ

    $avatar = $user['avatar'];



    // upload avatar mới

    if (
        isset($_FILES['avatar'])
        && $_FILES['avatar']['error'] == 0
    ) {


        $folder = "../uploads/avatar/";


        if (!is_dir($folder)) {

            mkdir($folder, 0777, true);
        }



        $filename = time()
            . "_"
            . basename($_FILES['avatar']['name']);



        $path = $folder . $filename;



        move_uploaded_file(

            $_FILES['avatar']['tmp_name'],

            $path

        );



        $avatar = $filename;
    }





    // update users

    $sql = "

        UPDATE users SET

            fullname = ?,

            phone = ?,

            avatar = ?

        WHERE id = ?

    ";



    $stmt = mysqli_prepare($conn, $sql);



    mysqli_stmt_bind_param(

        $stmt,

        "sssi",

        $fullname,

        $phone,

        $avatar,

        $user_id

    );



    mysqli_stmt_execute($stmt);



    mysqli_stmt_close($stmt);



    header("Location: profile.php?updated=1");

    exit;
}



include("../includes/header.php");

include("../includes/navbar.php");

?>



<div class="max-w-4xl mx-auto mt-8">


    <div class="bg-white shadow rounded-xl p-8">


        <h2 class="text-3xl font-bold mb-6">

            Thông tin cá nhân Shipper

        </h2>




        <?php if (isset($_GET['updated'])) { ?>


            <div class="bg-green-100 text-green-700 p-3 rounded mb-5">

                Cập nhật thông tin thành công

            </div>


        <?php } ?>





        <form method="POST" enctype="multipart/form-data">



            <div class="text-center mb-6">


                <?php if (!empty($user['avatar'])) { ?>


                    <img

                        src="../uploads/avatar/<?= htmlspecialchars($user['avatar']) ?>"

                        class="w-32 h-32 rounded-full mx-auto object-cover">


                <?php } else { ?>


                    <div class="w-32 h-32 bg-gray-200 rounded-full mx-auto flex items-center justify-center">

                        No Avatar

                    </div>


                <?php } ?>


            </div>


            <div class="mb-5">


                <label class="font-semibold">

                    Ảnh đại diện

                </label>


                <input

                    type="file"

                    name="avatar"

                    class="w-full border rounded-lg p-3">


            </div>





            <div class="mb-5">


                <label class="font-semibold">

                    Họ và tên

                </label>


                <input

                    type="text"

                    name="fullname"

                    value="<?= htmlspecialchars($user['fullname']) ?>"

                    class="w-full border rounded-lg p-3"

                    required>


            </div>





            <div class="mb-5">


                <label class="font-semibold">

                    Tên đăng nhập

                </label>


                <input

                    type="text"

                    value="<?= htmlspecialchars($user['username']) ?>"

                    class="w-full border rounded-lg p-3 bg-gray-100"

                    readonly>


            </div>





            <div class="mb-5">


                <label class="font-semibold">

                    Số điện thoại

                </label>


                <input

                    type="text"

                    name="phone"

                    value="<?= htmlspecialchars($user['phone']) ?>"

                    class="w-full border rounded-lg p-3">


            </div>





            <div class="mb-5">


                <label class="font-semibold">

                    Ngày tạo tài khoản

                </label>


                <input

                    type="text"

                    value="<?= $user['created_at'] ?>"

                    class="w-full border rounded-lg p-3 bg-gray-100"

                    readonly>


            </div>





            <button

                type="submit"

                class="bg-green-600 hover:bg-green-700 text-white px-6 py-3 rounded-lg">

                Lưu thay đổi

            </button>



        </form>


    </div>

</div>



<?php

include("../includes/footer.php");

?>