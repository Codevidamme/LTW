<?php
// pages/cart.php
if (!isset($pdo)) die('Truy cập bị từ chối!');

// Khởi tạo giỏ hàng nếu chưa có
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

// Xử lý các action (Thêm, Xóa sản phẩm khỏi giỏ)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    
    if ($action === 'add') {
        $product_id = (int)$_POST['product_id'];
        $quantity = (int)($_POST['quantity'] ?? 1);
        
        // Nếu sản phẩm đã có trong giỏ, cộng dồn số lượng
        if (isset($_SESSION['cart'][$product_id])) {
            $_SESSION['cart'][$product_id] += $quantity;
        } else {
            $_SESSION['cart'][$product_id] = $quantity;
        }
        // Load lại trang để tránh submit form nhiều lần khi F5
        header("Location: index.php?page=cart");
        exit;
    }
    
    if ($action === 'remove') {
        $product_id = (int)$_POST['product_id'];
        unset($_SESSION['cart'][$product_id]);
        header("Location: index.php?page=cart");
        exit;
    }
}
?>

<h2>Giỏ hàng của bạn</h2>

<?php if (empty($_SESSION['cart'])): ?>
    <div style="text-align: center; padding: 50px; background: #fff; border-radius: 8px;">
        <i class="fa-solid fa-cart-shopping" style="font-size: 50px; color: #ccc; margin-bottom: 20px;"></i>
        <p>Giỏ hàng đang trống.</p>
        <a href="index.php" style="display: inline-block; margin-top: 10px; padding: 10px 20px; background: #ff0000; color: #fff; text-decoration: none; border-radius: 4px;">Tiếp tục mua sắm</a>
    </div>
<?php else: ?>
    <table style="width: 100%; background: #fff; border-collapse: collapse; text-align: left;">
        <thead>
            <tr style="border-bottom: 2px solid #eee;">
                <th style="padding: 15px;">Sản phẩm</th>
                <th style="padding: 15px;">Đơn giá</th>
                <th style="padding: 15px;">Số lượng</th>
                <th style="padding: 15px;">Thành tiền</th>
                <th style="padding: 15px;">Xóa</th>
            </tr>
        </thead>
        <tbody>
            <?php 
            $total_order = 0;
            // Dùng IN (...) để truy vấn một lần lấy ra tất cả sản phẩm trong giỏ
            $ids = implode(',', array_keys($_SESSION['cart']));
            $stmt = $pdo->query("SELECT * FROM products WHERE id IN ($ids)");
            $cart_products = $stmt->fetchAll();

            foreach ($cart_products as $item): 
                $qty = $_SESSION['cart'][$item['id']];
                $price = $item['discount_price'] > 0 ? $item['discount_price'] : $item['price'];
                $subtotal = $price * $qty;
                $total_order += $subtotal;
            ?>
                <tr style="border-bottom: 1px solid #eee;">
                    <td style="padding: 15px; display: flex; align-items: center; gap: 15px;">
                        <img src="assets/images/<?= htmlspecialchars($item['image']) ?>" style="width: 60px; height: 60px; object-fit: contain;">
                        <?= htmlspecialchars($item['name']) ?>
                    </td>
                    <td style="padding: 15px; font-weight: bold; color: #ff0000;"><?= number_format($price, 0, ',', '.') ?>đ</td>
                    <td style="padding: 15px;"><?= $qty ?></td>
                    <td style="padding: 15px; font-weight: bold;"><?= number_format($subtotal, 0, ',', '.') ?>đ</td>
                    <td style="padding: 15px;">
                        <form action="index.php?page=cart" method="POST">
                            <input type="hidden" name="action" value="remove">
                            <input type="hidden" name="product_id" value="<?= $item['id'] ?>">
                            <button type="submit" style="background: none; border: none; color: red; cursor: pointer;"><i class="fa-solid fa-trash"></i></button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <div style="text-align: right; background: #fff; padding: 20px; margin-top: 20px; border-radius: 8px;">
        <h3>Tổng tiền: <span style="color: #ff0000; font-size: 24px;"><?= number_format($total_order, 0, ',', '.') ?>đ</span></h3>
        <button style="padding: 12px 30px; background: #ff0000; color: white; border: none; border-radius: 4px; font-size: 16px; font-weight: bold; cursor: pointer; margin-top: 10px;">TIẾN HÀNH THANH TOÁN</button>
    </div>
<?php endif; ?>