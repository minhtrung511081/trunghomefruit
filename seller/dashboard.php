<?php
session_start();

if (!isset($_SESSION['user']) || $_SESSION['user']['role_name'] != "Seller") {
    header("Location: ../auth/login.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="vi">

<head>

    <meta charset="UTF-8">
    <title>Seller Dashboard</title>

    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

    <script src="https://cdn.tailwindcss.com"></script>

    <link rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">
    <link rel="stylesheet"
        href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css">

    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <link rel="stylesheet" href="../assets/css/seller.css">
</head>

<body class="bg-gray-100">

    <div class="flex">

        <?php include "sidebar.php"; ?>

        <div class="flex-1">

            <?php include "header.php"; ?>

            <div id="content" class="p-6">

            </div>

        </div>

    </div>

    <script>
        $(function() {

            <?php if (isset($_GET['load']) && $_GET['load'] == "orders") { ?>

                $("#content").load("/fruit_shop/seller/orders/index.php");

            <?php } ?>

        });
    </script>

    <script src="../assets/js/seller.js"></script>

</body>

</html>