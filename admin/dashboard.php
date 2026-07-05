<?php

include("../includes/auth.php");

if($_SESSION['user']['role_name']!="Admin"){

die("Bạn không có quyền.");

}

?>

<!DOCTYPE html>

<html>

<head>

<title>Admin Dashboard</title>

<script src="https://cdn.tailwindcss.com"></script>

</head>

<body>

<div class="p-10">

<h1 class="text-4xl font-bold">

Xin chào Admin

</h1>

<p>

<?php

echo $_SESSION['user']['fullname'];

?>

</p>

<a

href="../auth/logout.php"

class="bg-red-600 text-white px-5 py-2 rounded">

Đăng xuất

</a>

</div>

</body>

</html>