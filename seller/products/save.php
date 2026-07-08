<?php
session_start();

include("../../config/database.php");

if (!isset($_SESSION['user'])) {
    header("Location: ../../auth/login.php");
    exit();
}

$product_name = trim($_POST['product_name']);
$category = trim($_POST['category']);
$unit = trim($_POST['unit']);
$price = (float)$_POST['price'];
$description = trim($_POST['description']);

$imageName = "";

if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {

    $allow = ['jpg', 'jpeg', 'png', 'gif', 'webp'];

    $ext = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));

    if (in_array($ext, $allow)) {

        $imageName = time() . "_" . preg_replace('/[^a-zA-Z0-9._-]/', '', $_FILES['image']['name']);

        move_uploaded_file(
            $_FILES['image']['tmp_name'],
            "../../assets/images/products/" . $imageName
        );
    }
}

$seller_id = $_SESSION['user']['id'];

$sql = "INSERT INTO products
(
    seller_id,
    product_name,
    category,
    unit,
    price,
    image,
    description
)
VALUES
(
    ?,?,?,?,?,?,?
)";

$stmt = $conn->prepare($sql);

$stmt->bind_param(
    "isssdss",
    $seller_id,
    $product_name,
    $category,
    $unit,
    $price,
    $imageName,
    $description,
);

if ($stmt->execute()) {

    echo "success";
} else {

    echo "Lỗi: " . $conn->error;
}
