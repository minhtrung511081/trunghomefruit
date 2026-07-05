<?php
session_start();
include("../config/database.php");

if (!isset($_SESSION['user'])) {
    header("Location: /fruit_shop/login.php");
    exit;
}

if (!isset($_GET['id'])) {
    header("Location: /fruit_shop/index.php");
    exit;
}

$product_id = (int)$_GET['id'];

$sql = "SELECT * FROM products WHERE id=$product_id";
$result = mysqli_query($conn, $sql);

if (mysqli_num_rows($result) == 0) {
    header("Location: /fruit_shop/index.php");
    exit;
}

if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

if (isset($_SESSION['cart'][$product_id])) {
    $_SESSION['cart'][$product_id]++;
} else {
    $_SESSION['cart'][$product_id] = 1;
}

header("Location: /fruit_shop/cart/index.php");
exit;
