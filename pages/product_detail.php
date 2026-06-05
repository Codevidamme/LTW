<?php
// pages/product_detail.php
if (!isset($pdo)) die('Truy cập bị từ chối!');

// Lấy ID sản phẩm từ URL, ép kiểu về số nguyên để bảo mật
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Truy vấn lấy thông tin sản phẩm và tên thương hiệu
$sql = "SELECT p.*, b.name AS brand_name 
        FROM products p 
        LEFT JOIN brands b ON p.brand_id = b.id 
        WHERE p.id = :id";
$stmt = $pdo->prepare($sql);
$stmt->execute(['id' => $id]);
$product = $stmt->fetch();

// Nếu không tìm thấy sản phẩm
if (!$product) {
    echo "<div style='text-align: center; padding: 50px;'><h2>Sản phẩm không tồn tại hoặc đã bị xóa.</h2></div>";
    return; // Dừng thực thi đoạn code bên dưới
}
?>

<div class="product-detail-container" style="display: flex; gap: 40px; background: #fff; padding: 20px; border-radius: 8px;">
    <div class="product-image" style="flex: 1;">
        <img src="assets/images/<?= htmlspecialchars($product['image']) ?>" alt="<?= htmlspecialchars($product['name']) ?>" style="width: 100%; border: 1px solid #eee; border-radius: 8px;">
    </div>

    <div class="product-info" style="flex: 1.5;">
        <h1 style="margin-top: 0; font-size: 24px; color: #333;"><?= htmlspecialchars($product['name']) ?></h1>
        <p style="color: #666; font-size: 14px;">Thương hiệu: <b style="color: #000;"><?= htmlspecialchars($product['brand_name']) ?></b></p>
        
        <div class="price-box" style="background: #fafafa; padding: 15px; border-radius: 8px; margin: 20px 0;">
            <?php if ($product['discount_price'] > 0): ?>
                <span style="color: #ff0000; font-size: 28px; font-weight: bold;"><?= number_format($product['discount_price'], 0, ',', '.') ?>đ</span>
                <span style="color: #999; text-decoration: line-through; font-size: 16px; margin-left: 10px;"><?= number_format($product['price'], 0, ',', '.') ?>đ</span>
            <?php else: ?>
                <span style="color: #ff0000; font-size: 28px; font-weight: bold;"><?= number_format($product['price'], 0, ',', '.') ?>đ</span>
            <?php endif; ?>
        </div>

        <p><b>Tình trạng:</b> <?= $product['stock'] > 0 ? "<span style='color: green;'>Còn hàng ({$product['stock']})</span>" : "<span style='color: red;'>Hết hàng</span>" ?></p>

        <form action="index.php?page=cart" method="POST" style="margin-top: 30px; display: flex; gap: 15px;">
            <input type="hidden" name="action" value="add">
            <input type="hidden" name="product_id" value="<?= $product['id'] ?>">
            
            <div style="display: flex; align-items: center;">
                <label style="margin-right: 10px; font-weight: bold;">Số lượng:</label>
                <input type="number" name="quantity" value="1" min="1" max="<?= $product['stock'] ?>" style="width: 60px; padding: 8px; text-align: center; border: 1px solid #ccc; border-radius: 4px;">
            </div>

            <button type="submit" <?= $product['stock'] == 0 ? 'disabled' : '' ?> style="padding: 10px 30px; background: #ff0000; color: white; border: none; border-radius: 4px; font-size: 16px; font-weight: bold; cursor: pointer;">
                <i class="fa-solid fa-cart-plus"></i> THÊM VÀO GIỎ
            </button>
        </form>

        <div class="description" style="margin-top: 40px; border-top: 1px solid #eee; padding-top: 20px;">
            <h3>Mô tả sản phẩm</h3>
            <p style="line-height: 1.6; color: #444;"><?= nl2br(htmlspecialchars($product['description'])) ?></p>
        </div>
    </div>
</div>