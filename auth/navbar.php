<?php if(isset($_SESSION['user'])){ ?>

<div class="flex gap-5">

<span>

Xin chào,

<b><?= $_SESSION['user']['fullname'] ?></b>

</span>

<a href="auth/logout.php">

<i class="fa fa-right-from-bracket"></i>

Đăng xuất

</a>

</div>

<?php } else { ?>

<a href="auth/login.php">

<i class="fa fa-user"></i>

Đăng nhập

</a>

<?php } ?>