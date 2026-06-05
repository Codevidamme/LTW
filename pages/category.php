<?php
// pages/category.php
if (!isset($pdo)) die('Truy cập bị từ chối!');

$category_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Lấy tên danh mục hiện tại để làm tiêu đề
$stmt_cat = $pdo->prepare("SELECT name FROM categories WHERE id = :id");
$stmt_cat->execute(['id' => $category_id]);
$category = $stmt_cat->fetch();

$cat_name = $category ? $category['name'] : 'Tất cả sản phẩm';

// Lấy sản phẩm thuộc danh mục này (hoặc lấy tất cả nếu id = 0/all)
if ($category_id > 0) {
    // Truy vấn sản phẩm của danh mục hiện tại HOẶC các danh mục con của nó
    $sql_prod = "SELECT * FROM products WHERE category_id = :id OR category_id IN (SELECT id FROM categories WHERE parent_id = :id)";
    $stmt_prod = $pdo->prepare($sql_prod);
    $stmt_prod->execute(['id' => $category_id]);
} else {
    $stmt_prod = $pdo->query("SELECT * FROM products");
}
$products = $stmt_prod->fetchAll();
?>

<div class="category-page">
    <h2 style="border-bottom: 2px solid #ff0000; padding-bottom: 10px; margin-bottom: 20px;"><?= htmlspecialchars($cat_name) ?></h2>

    <div class="product-grid" style="display: grid; grid-template-columns: repeat(5, 1fr); gap: 20px;">
        <?php if (count($products) > 0): ?>
            <?php foreach ($products as $row): ?>
                <div class="product-card" style="border: 1px solid #eee; border-radius: 8px; padding: 15px; text-align: center; background: #fff;">
                    <a href="index.php?page=product_detail&id=<?= $row['id'] ?>">
                        <img src="assets/images/<?= htmlspecialchars($row['image']) ?>" style="width: 100%; height: 180px; object-fit: contain;">
                    </a>
                    <h3 style="font-size: 14px; margin: 10px 0; height: 40px; overflow: hidden;"><a href="index.php?page=product_detail&id=<?= $row['id'] ?>" style="text-decoration: none; color: #333;"><?= htmlspecialchars($row['name']) ?></a></h3>
                    
                    <div style="color: #ff0000; font-weight: bold;">
                        <?= number_format($row['discount_price'] > 0 ? $row['discount_price'] : $row['price'], 0, ',', '.') ?>đ
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p>Đang cập nhật sản phẩm cho danh mục này.</p>
        <?php endif; ?>
    </div>
</div>