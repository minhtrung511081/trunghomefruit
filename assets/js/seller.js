$(function () {

    // Chỉ load trang chủ nếu content đang trống
    if ($("#content").html().trim() == "") {
        $("#content").load("pages/home.php");
    }

    $(".menu-item").click(function (e) {

        e.preventDefault();

        let page = $(this).data("page");

        $("#content").load(page);

    });

    $("#toggleSidebar").click(function () {

        $("#sidebar").toggleClass("w-64");
        $("#sidebar").toggleClass("w-20");
        $(".logo").toggle();
        $("#sidebar span").not(".logo").toggle();

    });

});