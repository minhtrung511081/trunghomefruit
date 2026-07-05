<?php

session_start();

include("../config/database.php");

$username = trim($_POST['username']);
$password = md5($_POST['password']);

$sql = "SELECT u.*, r.role_name
        FROM users u
        INNER JOIN roles r ON u.role_id = r.id
        WHERE username=? AND password=?";

$stmt = $conn->prepare($sql);

$stmt->bind_param("ss", $username, $password);

$stmt->execute();

$result = $stmt->get_result();

if ($result->num_rows == 1) {

    $user = $result->fetch_assoc();

    $_SESSION['user'] = $user;

    switch ($user['role_name']) {

        case 'Admin':
            header("Location: ../admin/dashboard.php");
            exit();

        case 'Seller':
            header("Location: ../seller/dashboard.php");
            exit();

        case 'Customer':
            // Người mua về trang chủ
            header("Location: ../index.php");
            exit();

        case 'Shipper':
            header("Location: ../shipper/dashboard.php");
            exit();

        default:
            header("Location: ../index.php");
            exit();
    }
} else {

    header("Location: login.php?error=1");
}
