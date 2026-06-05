<?php
// admin/products.php
if (!isset($pdo)) die('Truy cập bị từ chối!');

$action = isset($_GET['action']) ? $_GET['action'] : 'list';

// XÓA SẢN PHẨM
if ($action === 'delete' && isset($_GET['id'])) {
    $id = (int)$_GET['id'];
    try {
        $pdo->prepare("DELETE FROM products WHERE id = ?")->execute([$id]);
        header("Location: index.php?page=products&msg=deleted"); exit;
    } catch (\PDOException $e) {
        header("Location: index.php?page=products&msg=error_fk"); exit;
    }
}

// CẬP NHẬT HOẶC THÊM MỚI
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name']);
    $category_id = (int)$_POST['category_id'];
    $supplier_id = !empty($_POST['supplier_id']) ? (int)$_POST['supplier_id'] : null;
    $price = (float)$_POST['price'];
    $discount_price = (float)$_POST['discount_price'];
    $stock = (int)$_POST['stock'];
    $image = trim($_POST['image']);

    if (isset($_POST['create_product'])) {
        $sql = "INSERT INTO products (name, category_id, supplier_id, price, discount_price, stock, image) VALUES (?, ?, ?, ?, ?, ?, ?)";
        $pdo->prepare($sql)->execute([$name, $category_id, $supplier_id, $price, $discount_price, $stock, $image]);
        header("Location: index.php?page=products&msg=created"); exit;
    }
    
    if (isset($_POST['update_product'])) {
        $id = (int)$_POST['product_id'];
        $sql = "UPDATE products SET name=?, category_id=?, supplier_id=?, price=?, discount_price=?, stock=?, image=? WHERE id=?";
        $pdo->prepare($sql)->execute([$name, $category_id, $supplier_id, $price, $discount_price, $stock, $image, $id]);
        header("Location: index.php?page=products&msg=updated"); exit;
    }
}

// Lấy dữ liệu dropdowns
$categories = $pdo->query("SELECT * FROM categories")->fetchAll();
$suppliers = $pdo->query("SELECT id, name FROM suppliers")->fetchAll();
?>

<style>
    .breadcrumb { font-size: 13px; color: #777; margin-bottom: 20px; }
    .breadcrumb a { color: #ee4d2d; text-decoration: none; }
    .box { background: #fff; border-radius: 4px; box-shadow: 0 1px 4px rgba(0,0,0,0.05); padding: 20px; margin-bottom: 20px; }
    
    .sp-table { width: 100%; border-collapse: collapse; margin-top: 15px; }
    .sp-table th { background: #f8f9fa; color: #555; font-weight: 500; padding: 12px 15px; text-align: left; font-size: 14px; border-bottom: 2px solid #eee; }
    .sp-table td { padding: 12px 15px; border-bottom: 1px solid #eee; font-size: 14px; color: #333; vertical-align: middle; }
    .sp-table tr:hover { background: #fafafa; }
    
    .btn-create { background: #ee4d2d; color: #fff; padding: 8px 15px; text-decoration: none; border-radius: 4px; font-size: 14px; font-weight: 500; float: right; }
    .btn-create:hover { background: #d73211; }
    .btn-action { display: inline-flex; align-items: center; justify-content: center; width: 32px; height: 32px; color: #fff; border-radius: 4px; text-decoration: none; margin-right: 5px; font-size: 13px; }
    .btn-edit { background: #007bff; }
    .btn-delete { background: #dc3545; }
    
    .sp-form-group { margin-bottom: 15px; }
    .sp-form-group label { display: block; margin-bottom: 8px; font-size: 14px; font-weight: 500; }
    .sp-form-control { width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px; outline: none; box-sizing: border-box; }
    .sp-alert { padding: 12px 20px; background: #eaffea; border: 1px solid #4caf50; color: #2e7d32; border-radius: 4px; margin-bottom: 20px; font-size: 14px; }
</style>

<div class="breadcrumb">
    <i class="fa-solid fa-house"></i> <a href="index.php">Trang chủ</a> / Kho hàng / Sản phẩm
</div>

<?php if (isset($_GET['msg'])): ?>
    <?php 
        if ($_GET['msg'] == 'deleted') echo '<div class="sp-alert"><i class="fa-solid fa-check"></i> Đã xóa sản phẩm!</div>';
        if ($_GET['msg'] == 'updated') echo '<div class="sp-alert"><i class="fa-solid fa-check"></i> Đã cập nhật sản phẩm!</div>';
        if ($_GET['msg'] == 'created') echo '<div class="sp-alert"><i class="fa-solid fa-check"></i> Đã thêm sản phẩm!</div>';
    ?>
<?php endif; ?>

<?php
// =========================================================================
// FORM THÊM / SỬA SẢN PHẨM
// =========================================================================
if ($action === 'add' || $action === 'edit'):
    $p = ['id'=>'', 'name'=>'', 'category_id'=>'', 'supplier_id'=>'', 'price'=>'0', 'discount_price'=>'0', 'stock'=>'10', 'image'=>''];
    if ($action === 'edit') {
        $stmt = $pdo->prepare("SELECT * FROM products WHERE id = ?");
        $stmt->execute([(int)$_GET['id']]);
        $p = $stmt->fetch();
    }
?>
    <div class="box" style="max-width: 900px;">
        <h3 style="margin-top: 0; margin-bottom: 20px; border-bottom: 1px solid #eee; padding-bottom: 15px;">
            <?= $action=='add' ? 'Thêm Sản Phẩm Mới' : 'Cập Nhật Sản Phẩm' ?>
        </h3>
        <form action="index.php?page=products" method="POST">
            <?php if($action=='add'): ?><input type="hidden" name="create_product" value="1"><?php else: ?><input type="hidden" name="update_product" value="1"><input type="hidden" name="product_id" value="<?= $p['id'] ?>"><?php endif; ?>
            <div class="sp-form-group">
                <label>Tên sản phẩm đồ chơi *</label>
                <input type="text" name="name" class="sp-form-control" value="<?= htmlspecialchars($p['name']) ?>" required>
            </div>
            <div style="display: flex; gap: 20px;">
                <div class="sp-form-group" style="flex: 1;">
                    <label>Danh mục (Phân loại) *</label>
                    <select name="category_id" class="sp-form-control" required>
                        <option value="">-- Chọn ngành hàng --</option>
                        <?php foreach($categories as $c): ?>
                            <option value="<?= $c['id'] ?>" <?= $p['category_id'] == $c['id'] ? 'selected' : '' ?>><?= $c['name'] ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="sp-form-group" style="flex: 1;">
                    <label>Nhà cung cấp (Nguồn hàng)</label>
                    <select name="supplier_id" class="sp-form-control">
                        <option value="">-- Thuộc nội bộ / Không rõ --</option>
                        <?php foreach($suppliers as $s): ?>
                            <option value="<?= $s['id'] ?>" <?= $p['supplier_id'] == $s['id'] ? 'selected' : '' ?>><?= htmlspecialchars($s['name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="sp-form-group" style="flex: 1;">
                    <label>Tên file hình ảnh *</label>
                    <input type="text" name="image" class="sp-form-control" value="<?= htmlspecialchars($p['image']) ?>" required>
                </div>
            </div>
            <div style="display: flex; gap: 20px;">
                <div class="sp-form-group" style="flex: 1;">
                    <label>Giá bán gốc (VNĐ) *</label>
                    <input type="number" name="price" class="sp-form-control" value="<?= $p['price'] ?>" required>
                </div>
                <div class="sp-form-group" style="flex: 1;">
                    <label>Giá khuyến mãi</label>
                    <input type="number" name="discount_price" class="sp-form-control" value="<?= $p['discount_price'] ?>">
                </div>
                <div class="sp-form-group" style="flex: 1;">
                    <label>Số lượng kho *</label>
                    <input type="number" name="stock" class="sp-form-control" value="<?= $p['stock'] ?>" required>
                </div>
            </div>
            <div style="text-align: right; margin-top: 20px;">
                <a href="index.php?page=products" style="padding: 8px 15px; color: #555; text-decoration: none; margin-right: 10px; border: 1px solid #ccc; border-radius: 4px;">Hủy bỏ</a>
                <button type="submit" style="background: #ee4d2d; color: #fff; border: none; padding: 9px 20px; border-radius: 4px; font-weight: 500; cursor: pointer;">LƯU THÔNG TIN</button>
            </div>
        </form>
    </div>

<?php
else:

    // Lấy dữ liệu lọc
    $filter_cat = isset($_GET['cat_id']) ? (int)$_GET['cat_id'] : 0;
    $keyword = isset($_GET['keyword']) ? trim($_GET['keyword']) : '';

    // Tạo điều kiện SQL động
    $where = "1=1";
    $params = [];

    // Lọc theo danh mục
    if ($filter_cat > 0) {
        $where .= " AND p.category_id = ?";
        $params[] = $filter_cat;
    }

    // Tìm kiếm theo tên sản phẩm
    if (!empty($keyword)) {
        $where .= " AND p.name LIKE ?";
        $params[] = "%$keyword%";
    }

    $sql = "SELECT p.*, c.name as category_name, s.name as supplier_name 
            FROM products p 
            LEFT JOIN categories c ON p.category_id = c.id 
            LEFT JOIN suppliers s ON p.supplier_id = s.id 
            WHERE $where 
            ORDER BY p.id DESC";

    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $products = $stmt->fetchAll();
?>

<div class="box">

    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 15px; gap:20px; flex-wrap:wrap;">

        <!-- FORM TÌM KIẾM + LỌC -->
        <form action="index.php" method="GET"
              style="display: flex; gap: 10px; align-items: center; flex-wrap: wrap;">

            <input type="hidden" name="page" value="products">

            <strong style="color: #555;">
                <i class="fa-solid fa-filter"></i> Bộ lọc:
            </strong>

            <!-- Ô tìm kiếm -->
            <input
                type="text"
                name="keyword"
                placeholder="Tìm tên sản phẩm..."
                value="<?= htmlspecialchars($keyword) ?>"
                style="padding:8px;border:1px solid #ccc;border-radius:4px;outline:none;min-width:220px;"
            >

            <!-- Danh mục -->
            <select
                name="cat_id"
                style="padding:8px;border:1px solid #ccc;border-radius:4px;outline:none;min-width:200px;"
            >
                <option value="0">--- Hiển thị Tất cả ---</option>

                <?php foreach($categories as $c): ?>
                    <option value="<?= $c['id'] ?>" <?= $filter_cat == $c['id'] ? 'selected' : '' ?>>
                        <?= htmlspecialchars($c['name']) ?>
                    </option>
                <?php endforeach; ?>
            </select>

            <!-- Nút tìm -->
            <button type="submit"
                    style="background:#ee4d2d;color:white;border:none;padding:8px 15px;border-radius:4px;cursor:pointer;">
                <i class="fa-solid fa-magnifying-glass"></i> Tìm kiếm
            </button>

        </form>

        <!-- Nút thêm -->
        <a href="index.php?page=products&action=add" class="btn-create">
            <i class="fa-solid fa-plus"></i> Tạo mới sản phẩm
        </a>

    </div>

    <table class="sp-table">
        <thead>
            <tr>
                <th style="width: 50px; text-align: center;">STT</th>
                <th style="width: 60px;">Hình</th>
                <th style="width: 30%;">Tên sản phẩm</th>
                <th>Phân loại & Nguồn gốc</th>
                <th>Giá bán</th>
                <th>Trạng thái</th>
                <th style="text-align: center; width: 100px;">Hành động</th>
            </tr>
        </thead>

        <tbody>

        <?php
        $stt = 1;

        if(count($products) > 0):

            foreach ($products as $row):
        ?>

            <tr>

                <td style="text-align:center;font-weight:bold;color:#777;">
                    <?= $stt++ ?>
                </td>

                <td>
                    <img
                        src="../assets/images/<?= htmlspecialchars($row['image']) ?>"
                        style="width:45px;height:45px;object-fit:contain;border:1px solid #eee;border-radius:4px;"
                    >
                </td>

                <td style="font-weight:500;color:#2c3e50;">
                    <?= htmlspecialchars($row['name']) ?>
                </td>

                <td>

                    <div style="color:#555;font-size:13px;font-weight:500;">
                        <i class="fa-solid fa-tags" style="color:#007bff;"></i>
                        <?= htmlspecialchars($row['category_name']) ?>
                    </div>

                    <div style="color:#888;font-size:12px;margin-top:4px;">
                        <i class="fa-solid fa-truck" style="color:#17a2b8;"></i>
                        <?= htmlspecialchars($row['supplier_name'] ?? 'Nội bộ') ?>
                    </div>

                </td>

                <td style="color:#ee4d2d;font-weight:bold;">

                    <?= number_format(
                        $row['discount_price'] > 0
                        ? $row['discount_price']
                        : $row['price']
                    ) ?>đ

                </td>

                <td>

                    <?php if($row['stock'] > 0): ?>

                        <span style="background:#eaffea;color:#28a745;padding:4px 8px;border-radius:4px;font-size:12px;font-weight:bold;">
                            Còn: <?= $row['stock'] ?>
                        </span>

                    <?php else: ?>

                        <span style="background:#ffebeb;color:#dc3545;padding:4px 8px;border-radius:4px;font-size:12px;font-weight:bold;">
                            Hết hàng
                        </span>

                    <?php endif; ?>

                </td>

                <td style="text-align:center;">

                    <a href="index.php?page=products&action=edit&id=<?= $row['id'] ?>"
                       class="btn-action btn-edit"
                       title="Chỉnh sửa">
                        <i class="fa-solid fa-pencil"></i>
                    </a>

                    <a href="index.php?page=products&action=delete&id=<?= $row['id'] ?>"
                       class="btn-action btn-delete"
                       onclick="return confirm('Bạn có chắc chắn muốn xóa sản phẩm này?');"
                       title="Xóa">
                        <i class="fa-solid fa-trash"></i>
                    </a>

                </td>

            </tr>

        <?php
            endforeach;

        else:
        ?>

            <tr>
                <td colspan="7"
                    style="text-align:center;padding:30px;color:#777;">
                    Không tìm thấy sản phẩm nào.
                </td>
            </tr>

        <?php endif; ?>

        </tbody>

    </table>

</div>

<?php endif; ?>