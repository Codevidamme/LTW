<?php
// pages/home.php
// Tránh việc truy cập trực tiếp file này không thông qua index.php
if (!isset($pdo)) {
    die('Truy cập bị từ chối!');
}

// Lấy danh sách sản phẩm từ database (Lấy 10 sản phẩm mới nhất)
try {
    $sql_products = "SELECT p.*, b.name AS brand_name 
                     FROM products p 
                     LEFT JOIN brands b ON p.brand_id = b.id 
                     ORDER BY p.id DESC LIMIT 10";
    $stmt = $pdo->query($sql_products);
    $products = $stmt->fetchAll();
} catch (\PDOException $e) {
    echo "Lỗi truy xuất sản phẩm: " . $e->getMessage();
    $products = [];
}
?>

<div class="home-banner" style="margin-bottom: 30px;">
    <div style="width: 100%; height: 300px; background: linear-gradient(to right, #ff4e50, #f9d423); border-radius: 10px; display: flex; align-items: center; justify-content: center; color: white;">
        <h1>SĂN SALE ĐỒ CHƠI HÈ - GIẢM ĐẾN 50%</h1>
    </div>
</div>

<section class="home-products">
    <div class="section-title" style="display: flex; justify-content: space-between; align-items: center; border-bottom: 2px solid #ff0000; margin-bottom: 20px; padding-bottom: 10px;">
        <h2 style="margin: 0; color: #333; text-transform: uppercase;">Sản phẩm nổi bật</h2>
        <a href="index.php?page=category&id=all" style="color: #ff0000; text-decoration: none; font-weight: bold;">Xem tất cả ></a>
    </div>

    <div class="product-grid" style="display: grid; grid-template-columns: repeat(5, 1fr); gap: 20px;">
        <?php if (count($products) > 0): ?>
            <?php foreach ($products as $row): ?>
                <div class="product-card" style="border: 1px solid #eee; border-radius: 8px; padding: 15px; text-align: center; position: relative; transition: transform 0.3s, box-shadow 0.3s; background: #fff;">
                    
                    <?php if ($row['discount_price'] > 0): ?>
                        <?php 
                            $percent = round((($row['price'] - $row['discount_price']) / $row['price']) * 100); 
                        ?>
                        <div class="badge-sale" style="position: absolute; top: 10px; left: 10px; background: #ff0000; color: white; padding: 3px 8px; border-radius: 4px; font-size: 12px; font-weight: bold;">
                            -<?= $percent ?>%
                        </div>
                    <?php endif; ?>

                    <a href="index.php?page=product_detail&id=<?= $row['id'] ?>">
                        <img src="assets/images/<?= htmlspecialchars($row['image']) ?>" alt="<?= htmlspecialchars($row['name']) ?>" style="width: 100%; height: 180px; object-fit: contain; margin-bottom: 15px; background: #f9f9f9; border-radius: 4px;">
                    </a>

                    <div style="color: #888; font-size: 12px; margin-bottom: 5px; text-transform: uppercase;"><?= htmlspecialchars($row['brand_name']) ?></div>

                    <h3 style="font-size: 14px; margin: 0 0 10px 0; height: 40px; overflow: hidden; text-overflow: ellipsis; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical;">
                        <a href="index.php?page=product_detail&id=<?= $row['id'] ?>" style="text-decoration: none; color: #333;">
                            <?= htmlspecialchars($row['name']) ?>
                        </a>
                    </h3>

                    <div class="product-price">
                        <?php if ($row['discount_price'] > 0): ?>
                            <span style="color: #ff0000; font-weight: bold; font-size: 16px;"><?= number_format($row['discount_price'], 0, ',', '.') ?>đ</span>
                            <span style="color: #999; text-decoration: line-through; font-size: 13px; margin-left: 5px;"><?= number_format($row['price'], 0, ',', '.') ?>đ</span>
                        <?php else: ?>
                            <span style="color: #ff0000; font-weight: bold; font-size: 16px;"><?= number_format($row['price'], 0, ',', '.') ?>đ</span>
                        <?php endif; ?>
                    </div>

                    <form action="index.php?page=cart" method="POST" style="margin-top: 15px;">
                        <input type="hidden" name="action" value="add">
                        <input type="hidden" name="product_id" value="<?= $row['id'] ?>">
                        <button type="submit" style="width: 100%; padding: 8px 0; background: #ff0000; color: white; border: none; border-radius: 4px; cursor: pointer; font-weight: bold; transition: background 0.3s;">
                            <i class="fa-solid fa-cart-plus"></i> CHỌN MUA
                        </button>
                    </form>

                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p>Chưa có sản phẩm nào trong hệ thống.</p>
        <?php endif; ?>
    </div>
</section>

<style>
.product-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 5px 15px rgba(0,0,0,0.1);
}
.product-card button:hover {
    background: #cc0000 !important;
}
</style>