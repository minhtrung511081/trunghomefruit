<?php
session_start();
include("../../config/database.php");

if (!isset($_SESSION['user'])) {
    header("Location: ../../login.php");
    exit;
}

$user_id = $_SESSION['user']['id'];

// Lấy dữ liệu từ form
$full_name      = trim($_POST['full_name'] ?? '');
$phone          = trim($_POST['phone'] ?? '');
$address        = trim($_POST['address'] ?? '');
$address_detail = trim($_POST['address_detail'] ?? '');
$latitude       = trim($_POST['latitude'] ?? '');
$longitude      = trim($_POST['longitude'] ?? '');
$is_default     = isset($_POST['is_default']) ? 1 : 0;

// Kiểm tra dữ liệu bắt buộc
if (
    $full_name == "" ||
    $phone == "" ||
    $address == ""
) {
    header("Location: create.php?error=1");
    exit;
}

// Nếu chọn mặc định thì bỏ mặc định các địa chỉ khác
if ($is_default == 1) {

    $stmt = mysqli_prepare(
        $conn,
        "UPDATE user_addresses
         SET is_default = 0
         WHERE user_id = ?"
    );

    mysqli_stmt_bind_param($stmt, "i", $user_id);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);
} else {

    // Nếu đây là địa chỉ đầu tiên thì tự động mặc định
    $stmt = mysqli_prepare(
        $conn,
        "SELECT COUNT(*) FROM user_addresses WHERE user_id=?"
    );

    mysqli_stmt_bind_param($stmt, "i", $user_id);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_bind_result($stmt, $count);
    mysqli_stmt_fetch($stmt);
    mysqli_stmt_close($stmt);

    if ($count == 0) {
        $is_default = 1;
    }
}

// Thêm địa chỉ
$stmt = mysqli_prepare(
    $conn,
    "INSERT INTO user_addresses
    (
        user_id,
        full_name,
        phone,
        address,
        address_detail,
        latitude,
        longitude,
        is_default
    )
    VALUES
    (
        ?,?,?,?,?,?,?,?
    )"
);

mysqli_stmt_bind_param(
    $stmt,
    "issssssi",
    $user_id,
    $full_name,
    $phone,
    $address,
    $address_detail,
    $latitude,
    $longitude,
    $is_default
);

if (mysqli_stmt_execute($stmt)) {

    mysqli_stmt_close($stmt);

    header("Location: index.php?success=1");
    exit;
} else {

    mysqli_stmt_close($stmt);

    die("Lỗi: " . mysqli_error($conn));
}
