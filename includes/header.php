<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include_once __DIR__ . '/../config/database.php';
?>

<!DOCTYPE html>
<html lang="vi">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Cửa Hàng Gia Dụng và Trái Cây</title>

    <link rel="stylesheet" href="assets/css/style.css">

    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

    <script src="https://cdn.tailwindcss.com"></script>

    <link rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">

    <link rel="stylesheet"
        href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />

    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

</head>

<body class="bg-gray-100">