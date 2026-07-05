<?php
session_start();
include("../config/database.php");

if (!isset($_SESSION['user'])) {
    exit;
}

$user_id = $_SESSION['user']['id'];

$full_name = mysqli_real_escape_string($conn, $_POST['full_name']);
$phone = mysqli_real_escape_string($conn, $_POST['phone']);
$address = mysqli_real_escape_string($conn, $_POST['address']);
$note = mysqli_real_escape_string($conn, $_POST['note']);

$total = 0;

foreach ($_SESSION['cart'] as $id => $qty) {

    $product = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM products WHERE id=$id"));

    $total += $product['price'] * $qty;
}

mysqli_query($conn, "INSERT INTO orders(user_id,full_name,phone,address,note,total)
VALUES('$user_id','$full_name','$phone','$address','$note','$total')");

$order_id = mysqli_insert_id($conn);

foreach ($_SESSION['cart'] as $id => $qty) {

    $product = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM products WHERE id=$id"));

    mysqli_query($conn, "INSERT INTO order_details(order_id,product_id,quantity,price)
VALUES('$order_id','$id','$qty','" . $product['price'] . "')");
}

unset($_SESSION['cart']);

header("Location: success.php");
