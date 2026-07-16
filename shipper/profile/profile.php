<?php

session_start();

include("../../config/database.php");

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


        $folder = __DIR__ . "/../../assets/images/avatars/";

        if (!is_dir($folder)) {
            mkdir($folder, 0777, true);
        }

        $filename = time() . "_" . preg_replace('/[^a-zA-Z0-9._-]/', '_', $_FILES['avatar']['name']);

        $path = $folder . $filename;

        if (move_uploaded_file($_FILES['avatar']['tmp_name'], $path)) {
            $avatar = $filename;
        } else {
            die("Upload thất bại");
        }

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



include("../../includes/header.php");
include("../../includes/navbar.php");

?>



<div class="max-w-4xl mx-auto mt-8">


    <div class="bg-white shadow rounded-xl p-8">


        <h2 class="text-3xl font-bold mb-6">

            Thông tin cá nhân Shipper

        </h2>

        <div class="mb-6">

            <a
                href="/fruit_shop/shipper/dashboard.php"
                class="bg-gray-600 hover:bg-gray-700 text-white px-5 py-3 rounded-lg inline-block">

                <i class="fa fa-arrow-left"></i>

                Quay lại Dashboard

            </a>

        </div>


        <?php if (isset($_GET['updated'])) { ?>


            <div class="bg-green-100 text-green-700 p-3 rounded mb-5">

                Cập nhật thông tin thành công

            </div>


        <?php } ?>





        <form method="POST" enctype="multipart/form-data">



            <div class="text-center mb-6">

                <?php
                $avatar = "/fruit_shop/assets/images/avatars/default-avatar.jpg";

                if (!empty($user['avatar'])) {

                    $avatarFile = __DIR__ . "/../../assets/images/avatars/" . $user['avatar'];

                    if (file_exists($avatarFile)) {
                        $avatar = "/fruit_shop/assets/images/avatars/" . htmlspecialchars($user['avatar']);
                    }
                }
                ?>

                <img
                    id="avatarPreview"
                    src="<?= $avatar ?>"
                    class="w-32 h-32 rounded-full mx-auto object-cover"
                    alt="Avatar">

            </div>


            <div class="mb-5">


                <label class="font-semibold">

                    Ảnh đại diện

                </label>


                <input
                    type="file"
                    name="avatar"
                    id="avatar"
                    accept="image/*"
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

<script>
    document.getElementById("avatar").addEventListener("change", function(e) {

        if (e.target.files.length > 0) {

            const reader = new FileReader();

            reader.onload = function(event) {
                document.getElementById("avatarPreview").src = event.target.result;
            };

            reader.readAsDataURL(e.target.files[0]);
        }

    });
</script>


<?php

include("../../includes/footer.php");
?>