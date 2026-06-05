<?php
session_start();
ob_start();

require_once '../config/database.php';

// 1. XỬ LÝ ĐĂNG XUẤT
if (isset($_GET['action']) && $_GET['action'] === 'logout') {
    unset($_SESSION['admin_user']);
    header("Location: index.php");
    exit;
}

// 2. XỬ LÝ ĐĂNG NHẬP
$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['login'])) {
    $email = trim($_POST['email']);
    $password = md5(trim($_POST['password']));
    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = :email AND password = :password AND role = 'admin'");
    $stmt->execute(['email' => $email, 'password' => $password]);
    $user = $stmt->fetch();
    if ($user) {
        $_SESSION['admin_user'] = $user['fullname'];
        header("Location: index.php");
        exit;
    } else {
        $error = 'Sai tài khoản hoặc bạn không có quyền Quản trị!';
    }
}

// FORM ĐĂNG NHẬP
if (!isset($_SESSION['admin_user'])):
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Đăng Nhập Admin</title>
    <style>
        body { background: #2c3e50; display: flex; justify-content: center; align-items: center; height: 100vh; margin: 0; font-family: sans-serif; }
        .login-box { background: #fff; padding: 40px; border-radius: 8px; width: 350px; box-shadow: 0 4px 15px rgba(0,0,0,0.2); }
        .login-box h2 { text-align: center; color: #ee4d2d; margin-bottom: 20px; }
        .input-group { margin-bottom: 15px; }
        .input-group input { width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 4px; box-sizing: border-box; outline: none; }
        button { width: 100%; padding: 12px; background: #ee4d2d; color: #fff; border: none; border-radius: 4px; font-weight: bold; cursor: pointer; }
    </style>
</head>
<body>
    <div class="login-box">
        <h2>MYKINGDOM ADMIN</h2>
        <?php if($error) echo "<p style='color:red; font-size:14px; text-align:center;'>$error</p>"; ?>
        <form action="index.php" method="POST">
            <input type="hidden" name="login" value="1">
            <div class="input-group"><input type="email" name="email" placeholder="Email (admin@mykingdom.club)" required></div>
            <div class="input-group"><input type="password" name="password" placeholder="Mật khẩu (123456)" required></div>
            <button type="submit">ĐĂNG NHẬP HỆ THỐNG</button>
        </form>
    </div>
</body>
</html>
<?php exit; endif; ?>

<?php
// BẢNG ĐIỀU KHIỂN CHÍNH
$page = isset($_GET['page']) ? $_GET['page'] : 'dashboard';
// Đã dọn dẹp mảng allowed không bị lặp chữ categories
$allowed = ['orders', 'products', 'dashboard', 'categories', 'users', 'suppliers', 'reviews'];
if (!in_array($page, $allowed)) { $page = 'dashboard'; }
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Hệ Thống Quản Trị</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; }
        body { display: flex; background: #f6f6f6; min-height: 100vh; overflow-x: hidden; }
        
        .sidebar { width: 250px; background: #2c3e50; color: #fff; display: flex; flex-direction: column; flex-shrink: 0; }
        .sidebar h3 { padding: 20px; text-align: center; background: #1a252f; margin: 0; color: #ffc107; font-size: 18px; }
        
        .sidebar a { display: block; padding: 12px 20px; color: #ecf0f1; text-decoration: none; border-left: 3px solid transparent; font-size: 14px; }
        .sidebar a:hover, .sidebar a.active { background: #34495e; color: #ffc107; border-left: 3px solid #ffc107; }
        
        .sidebar-heading { background: #1a252f; padding: 10px 20px; color: #7f8c8d; font-size: 11px; text-transform: uppercase; margin-top: 5px; font-weight: bold; letter-spacing: 1px; }
        
        .main { flex: 1; display: flex; flex-direction: column; width: calc(100% - 250px); }
        .topbar { background: #fff; padding: 15px 30px; display: flex; justify-content: space-between; box-shadow: 0 2px 5px rgba(0,0,0,0.05); align-items: center; }
        .content { padding: 30px; overflow-y: auto; flex: 1; }
    </style>
</head>
<body>
    <div class="sidebar">
        <h3><i class="fa-solid fa-shield-halved"></i> MYKINGDOM</h3>
        
        <a href="index.php?page=dashboard" class="<?= $page == 'dashboard' ? 'active' : '' ?>"><i class="fa-solid fa-chart-pie" style="width:25px;"></i> Thống kê</a>
        
        <div class="sidebar-heading">Quản lý bán hàng</div>
        <a href="index.php?page=orders" class="<?= $page == 'orders' ? 'active' : '' ?>"><i class="fa-solid fa-file-invoice" style="width:25px;"></i> Đơn hàng</a>
        
        <div class="sidebar-heading">Quản lý kho hàng</div>
        <a href="index.php?page=products" class="<?= $page == 'products' ? 'active' : '' ?>"><i class="fa-solid fa-box" style="width:25px;"></i> Sản phẩm</a>
        <a href="index.php?page=suppliers" class="<?= $page == 'suppliers' ? 'active' : '' ?>"><i class="fa-solid fa-truck-fast" style="width:25px;"></i> Nhà cung cấp</a>
        
        <div class="sidebar-heading">Tài khoản & Phản hồi</div>
        <a href="index.php?page=users" class="<?= $page == 'users' ? 'active' : '' ?>"><i class="fa-solid fa-users" style="width:25px;"></i> Người dùng</a>
        <a href="index.php?page=reviews" class="<?= $page == 'reviews' ? 'active' : '' ?>"><i class="fa-solid fa-star" style="width:25px;"></i> Đánh giá</a>
    </div>

    <div class="main">
        <div class="topbar">
            <span style="color:#7f8c8d; font-weight:bold;">Hệ thống quản lý nội dung</span>
            <span>Chào, <b><?= htmlspecialchars($_SESSION['admin_user']) ?></b> | <a href="index.php?action=logout" style="color: red; text-decoration: none;"><i class="fa-solid fa-power-off"></i> Đăng xuất</a></span>
        </div>
        <div class="content">
            <?php
            if (file_exists("{$page}.php")) { include "{$page}.php"; } 
            else { echo "<h3>Đang phát triển tính năng này...</h3>"; }
            ?>
        </div>
    </div>
</body>
</html>