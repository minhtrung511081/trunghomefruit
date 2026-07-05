<?php
session_start();

if(isset($_SESSION['user'])){
    header("Location: ../index.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="vi">

<head>

<meta charset="UTF-8">

<title>Đăng nhập</title>

<script src="https://cdn.tailwindcss.com"></script>

<link rel="stylesheet"
href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">

</head>

<body class="bg-green-100">

<div class="flex justify-center items-center h-screen">

<div class="bg-white w-[420px] rounded-xl shadow-xl p-8">

<h2 class="text-3xl font-bold text-center text-green-700 mb-8">

<i class="fa-solid fa-user-lock"></i>

Đăng nhập

</h2>

<form action="login_process.php" method="POST">

<div class="mb-5">

<label>Tên đăng nhập</label>

<input

type="text"

name="username"

class="w-full border rounded p-3"

required>

</div>

<div class="mb-5">

<label>Mật khẩu</label>

<input

type="password"

name="password"

class="w-full border rounded p-3"

required>

</div>

<button

class="w-full bg-green-700 text-white p-3 rounded hover:bg-green-800">

<i class="fa-solid fa-right-to-bracket"></i>

Đăng nhập

</button>

</form>

<?php

if(isset($_GET['error'])){

echo "<p class='text-red-600 mt-5 text-center'>Sai tài khoản hoặc mật khẩu.</p>";

}

?>

</div>

</div>

</body>

</html>