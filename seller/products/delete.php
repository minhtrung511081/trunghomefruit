<?php

session_start();

include("../../config/database.php");

if (!isset($_SESSION['user'])) {

    header("Location: ../../auth/login.php");
    exit();
}

$id = (int)$_GET['id'];

$sql = "SELECT image FROM products WHERE id=?";

$stmt = $conn->prepare($sql);

$stmt->bind_param("i", $id);

$stmt->execute();

$result = $stmt->get_result();

if ($row = $result->fetch_assoc()) {

    if (!empty($row['image'])) {

        $file = "../../assets/images/products/" . $row['image'];

        if (file_exists($file)) {

            unlink($file);
        }
    }
}

$seller_id = $_SESSION['user']['id'];

$sql = "DELETE
FROM products
WHERE id = ?
AND seller_id = ?";

$stmt = $conn->prepare($sql);

$stmt->bind_param("ii", $id, $seller_id);

$stmt->execute();

?>
<script>
    $("#content").load(
        "/fruit_shop/seller/products/list.php"
    );
</script>
<?php
exit;
