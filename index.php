<?php
// Bật hiển thị mọi loại lỗi để debug
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();
ob_start();

// 1. Nhúng file cấu hình kết nối Database
require_once 'config/database.php';

// 2. Lấy giá trị biến 'page' trên thanh URL
$page = isset($_GET['page']) ? $_GET['page'] : 'home';

// ĐÃ XÓA admin_orders VÀ admin_products KHỎI DANH SÁCH NÀY
$allowed_pages = ['home', 'category', 'product_detail', 'cart', 'account', 'search', 'promotions'];

if (in_array($page, $allowed_pages)) {
    $file_path = "pages/{$page}.php";
    
    if (file_exists($file_path)) {
        // Luôn luôn hiển thị giao diện bán hàng (vì admin đã bị dời đi nơi khác)
        include 'includes/header.php';
        echo "<div class='container main-wrapper' style='min-height: 50vh; padding: 20px 0;'>";
        include $file_path;
        echo "</div>";
        include 'includes/footer.php';
    } else {
        include 'includes/header.php';
        echo "<h2 style='text-align: center; color: gray; margin-top: 50px;'>Trang đang được phát triển...</h2>";
        include 'includes/footer.php';
    }
} else {
    include 'includes/header.php';
    echo "<h2 style='text-align: center; color: red; margin-top: 50px;'>404 - Không tìm thấy trang!</h2>";
    include 'includes/footer.php';
}
?>