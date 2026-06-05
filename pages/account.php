<?php
// pages/account.php
if (!isset($pdo)) die('Truy cập bị từ chối!');

// 1. XỬ LÝ ĐĂNG XUẤT
if (isset($_GET['action']) && $_GET['action'] === 'logout') {
    unset($_SESSION['user']);
    header("Location: index.php?page=account");
    exit;
}

// 2. XỬ LÝ ĐĂNG NHẬP
$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['login'])) {
    $email = trim($_POST['email']);
    $password = md5(trim($_POST['password'])); // Mã hóa MD5 để khớp với database mẫu

    try {
        $stmt = $pdo->prepare("SELECT * FROM users WHERE email = :email AND password = :password");
        $stmt->execute(['email' => $email, 'password' => $password]);
        $user = $stmt->fetch();

        if ($user) {
            // Lưu thông tin vào Session
            $_SESSION['user'] = [
                'id' => $user['id'],
                'fullname' => $user['fullname'],
                'role' => $user['role'],
                'email' => $user['email']
            ];
            // Tải lại trang để cập nhật giao diện
            header("Location: index.php?page=account");
            exit;
        } else {
            $error = 'Email hoặc mật khẩu không chính xác!';
        }
    } catch (\PDOException $e) {
        $error = 'Lỗi hệ thống: ' . $e->getMessage();
    }
}
?>

<div class="account-container" style="max-width: 500px; margin: 40px auto; background: #fff; padding: 30px; border-radius: 8px; box-shadow: 0 4px 10px rgba(0,0,0,0.08);">
    
    <?php if (!isset($_SESSION['user'])): ?>
        <h2 style="text-align: center; color: #ff0000; margin-bottom: 25px; text-transform: uppercase;">Đăng Nhập Hệ Thống</h2>
        
        <?php if ($error !== ''): ?>
            <div style="background: #f8d7da; color: #721c24; padding: 10px; border-radius: 4px; margin-bottom: 15px; font-size: 14px; border: 1px solid #f5c6cb;">
                <?= $error ?>
            </div>
        <?php endif; ?>

        <form action="index.php?page=account" method="POST">
            <input type="hidden" name="login" value="1">
            
            <div style="margin-bottom: 15px;">
                <label style="display: block; margin-bottom: 5px; font-weight: bold;">Email đăng nhập:</label>
                <input type="email" name="email" required placeholder="Ví dụ: admin@mykingdom.club" style="width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 4px; outline: none;">
            </div>

            <div style="margin-bottom: 20px;">
                <label style="display: block; margin-bottom: 5px; font-weight: bold;">Mật khẩu:</label>
                <input type="password" name="password" required placeholder="Nhập mật khẩu" style="width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 4px; outline: none;">
            </div>

            <button type="submit" style="width: 100%; padding: 12px; background: #ff0000; color: white; border: none; border-radius: 4px; font-weight: bold; font-size: 16px; cursor: pointer;">ĐĂNG NHẬP</button>
        </form>

    <?php else: ?>
        <div style="text-align: center;">
            <div style="font-size: 50px; color: #ffc107; margin-bottom: 15px;">
                <i class="fa-solid fa-circle-user"></i>
            </div>
            <h2>Xin chào, <?= htmlspecialchars($_SESSION['user']['fullname']) ?>!</h2>
            <p style="color: #666; margin-bottom: 20px;">Tài khoản: <?= htmlspecialchars($_SESSION['user']['email']) ?></p>
            
            <div style="border-top: 1px solid #eee; padding-top: 20px; margin-top: 20px;">
                <?php if ($_SESSION['user']['role'] === 'admin'): ?>
                    <div style="background: #fff3cd; border: 1px solid #ffeeba; padding: 15px; border-radius: 6px; margin-bottom: 20px;">
                        <p style="color: #856404; font-weight: bold; margin-bottom: 10px;">Bạn đang đăng nhập với tư cách Quản trị viên</p>
                        <a href="admin/" style="display: block; background: #ff0000; color: white; padding: 12px; text-decoration: none; border-radius: 4px; font-weight: bold; text-transform: uppercase;">
                            <i class="fa-solid fa-gear"></i> Đi tới Hệ thống Quản trị
                        </a>
                    </div>
                <?php else: ?>
                    <p style="color: green; font-weight: bold; margin-bottom: 20px;">Bạn đang đăng nhập với tư cách Khách hàng</p>
                    <a href="index.php" style="display: inline-block; background: #333; color: white; padding: 10px 20px; text-decoration: none; border-radius: 4px;">Tiếp tục mua sắm</a>
                <?php endif; ?>

                <a href="index.php?page=account&action=logout" style="display: inline-block; margin-top: 15px; color: #ff0000; text-decoration: underline; font-size: 14px;">
                    <i class="fa-solid fa-right-from-bracket"></i> Đăng xuất tài khoản
                </a>
            </div>
        </div>
    <?php endif; ?>
</div>