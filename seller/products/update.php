<?php

session_start();

include("../../config/database.php");

if (!isset($_SESSION['user'])) {
    header("Location: ../../auth/login.php");
    exit();
}

$id = (int)$_POST['id'];

$product_name = trim($_POST['product_name']);
$category = trim($_POST['category']);
$unit = trim($_POST['unit']);
$price = (float)$_POST['price'];
$description = trim($_POST['description']);

$stmt = $conn->prepare("SELECT image FROM products WHERE id=?");
$stmt->bind_param("i", $id);
$stmt->execute();

$old = $stmt->get_result()->fetch_assoc();

$image = $old['image'];

if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {

    if (!empty($image)) {

        $oldFile = "../../assets/images/products/" . $image;

        if (file_exists($oldFile)) {
            unlink($oldFile);
        }
    }

    $ext = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));

    $image = time() . "_" . basename($_FILES['image']['name']);

    move_uploaded_file(
        $_FILES['image']['tmp_name'],
        "../../assets/images/products/" . $image
    );
}

$seller_id = $_SESSION['user']['id'];

$sql = "UPDATE products SET
    product_name=?,
    category=?,
    unit=?,
    price=?,
    image=?,
    description=?
WHERE id=?
AND seller_id=?";

$stmt = $conn->prepare($sql);

$stmt->bind_param(
    "sssdssii",
    $product_name,
    $category,
    $unit,
    $price,
    $image,
    $description,
    $id,
    $seller_id
);

if ($stmt->execute()) {

    echo "success";
} else {

    echo "Lỗi: " . $conn->error;
}
