<?php
// includes/header.php
// Đảm bảo file config đã được nhúng ở index.php nên ở đây ta có thể dùng biến $pdo

try {
    // 1. Lấy các danh mục cha (parent_id IS NULL) - Ví dụ: Đồ chơi lắp ráp, Búp bê...
    $sql_parent = "SELECT * FROM categories WHERE parent_id IS NULL";
    $stmt_parent = $pdo->query($sql_parent);
    $parent_categories = $stmt_parent->fetchAll();
} catch (\PDOException $e) {
    $parent_categories = [];
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MyKingdom Clone - Vương Quốc Đồ Chơi</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>

<header>
    <div class="topbar">
        <div class="container">
            <span><i class="fa-solid fa-phone"></i> Hotline: 1900 1208</span>
            <span><i class="fa-solid fa-location-dot"></i> Hệ thống 100 cửa hàng toàn quốc</span>
        </div>
    </div>

    <div class="main-header">
        <div class="container header-flex">
            <div class="logo">
                <a href="index.php">
                    <img src="assets/images/logo_mykingdom.png" alt="MyKingdom Logo" style="height: 50px;">
                </a>
            </div>

            <div class="search-box">
                <form action="index.php" method="GET">
                    <input type="hidden" name="page" value="search">
                    <input type="text" name="keyword" placeholder="Tìm kiếm sản phẩm đồ chơi tương tự LEGO, Barbie...">
                    <button type="submit"><i class="fa-solid fa-magnifying-glass"></i></button>
                </form>
            </div>

            <div class="header-actions">
                <a href="index.php?page=account" class="action-item">
                    <i class="fa-regular fa-user"></i>
                    <span>Tài khoản</span>
                </a>
                <a href="index.php?page=cart" class="action-item cart-icon">
                    <i class="fa-solid fa-basket-shopping"></i>
                    <span>Giỏ hàng</span>
                    <span class="cart-count">0</span>
                </a>
            </div>
        </div>
    </div>

    <nav class="navbar">
        <div class="container">
            <ul class="main-menu">
                <li><a href="index.php" class="active">Trang Chủ</a></li>
                
                <?php foreach ($parent_categories as $parent): ?>
                    <li class="has-children">
                        <a href="index.php?page=category&id=<?= $parent['id'] ?>">
                            <?= htmlspecialchars($parent['name']) ?> <i class="fa-solid fa-chevron-down" style="font-size: 10px;"></i>
                        </a>
                        
                        <?php
                        // Tìm danh mục con ứng với danh mục cha hiện tại
                        $sql_child = "SELECT * FROM categories WHERE parent_id = :parent_id";
                        $stmt_child = $pdo->prepare($sql_child);
                        $stmt_child->execute(['parent_id' => $parent['id']]);
                        $child_categories = $stmt_child->fetchAll();
                        ?>
                        
                        <?php if (!empty($child_categories)): ?>
                            <ul class="dropdown-menu">
                                <?php foreach ($child_categories as $child): ?>
                                    <li>
                                        <a href="index.php?page=category&id=<?= $child['id'] ?>">
                                            <?= htmlspecialchars($child['name']) ?>
                                        </a>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                        <?php endif; ?>
                    </li>
                <?php endforeach; ?>
                
                <li><a href="index.php?page=promotions" style="color: #ff0000; font-weight: bold;">Khuyến Mãi Hot</a></li>
            </ul>
        </div>
    </nav>
</header>

<main class="main-content container">