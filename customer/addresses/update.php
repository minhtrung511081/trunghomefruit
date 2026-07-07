<?php
session_start();

include("../../config/database.php");


if (!isset($_SESSION['user'])) {

    header("Location: ../../login.php");
    exit;
}


$user_id = $_SESSION['user']['id'];


// lấy id từ form POST
if (!isset($_POST['id'])) {

    header("Location:index.php");
    exit;
}


$id = intval($_POST['id']);



$full_name = $_POST['full_name'];

$phone = $_POST['phone'];

$address = $_POST['address'];

$address_detail = $_POST['address_detail'];

$latitude = $_POST['latitude'];

$longitude = $_POST['longitude'];



$is_default = isset($_POST['is_default']) ? 1 : 0;



// nếu chọn mặc định
if ($is_default == 1) {


    $stmt = mysqli_prepare(
        $conn,
        "UPDATE user_addresses 
         SET is_default = 0
         WHERE user_id = ?"
    );


    mysqli_stmt_bind_param(
        $stmt,
        "i",
        $user_id
    );


    mysqli_stmt_execute($stmt);


    mysqli_stmt_close($stmt);
}



// update địa chỉ

$stmt = mysqli_prepare(

    $conn,

    "UPDATE user_addresses SET

        full_name = ?,
        phone = ?,
        address = ?,
        address_detail = ?,
        latitude = ?,
        longitude = ?,
        is_default = ?

     WHERE id = ?
     AND user_id = ?"

);



mysqli_stmt_bind_param(

    $stmt,

    "ssssddiii",

    $full_name,

    $phone,

    $address,

    $address_detail,

    $latitude,

    $longitude,

    $is_default,

    $id,

    $user_id

);



$result = mysqli_stmt_execute($stmt);



mysqli_stmt_close($stmt);



if ($result) {


    header("Location:index.php?updated=1");
} else {


    echo "Lỗi cập nhật: " . mysqli_error($conn);
}


exit;
