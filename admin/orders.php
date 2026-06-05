<?php
// admin/orders.php
if (!isset($pdo)) die('Truy cập bị từ chối!');

$action = isset($_GET['action']) ? $_GET['action'] : 'list';

// =========================================================================
// 1. XỬ LÝ DATABASE (XÓA, CẬP NHẬT, THÊM MỚI)
// =========================================================================

if ($action === 'delete' && isset($_GET['id'])) {
    $id = (int)$_GET['id'];
    try {
        $pdo->prepare("DELETE FROM order_details WHERE order_id = ?")->execute([$id]);
        $pdo->prepare("DELETE FROM orders WHERE id = ?")->execute([$id]);
        header("Location: index.php?page=orders&msg=deleted");
        exit;
    } catch (\PDOException $e) {
        die("Lỗi xóa đơn hàng: " . $e->getMessage());
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_order'])) {
    $id = (int)$_POST['order_id'];
    $status = $_POST['status'];
    $shipping_address = trim($_POST['shipping_address']);

    $pdo->prepare("UPDATE orders SET status = ?, shipping_address = ? WHERE id = ?")
        ->execute([$status, $shipping_address, $id]);

    header("Location: index.php?page=orders&msg=updated");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['create_order'])) {
    // Lấy tên khách hàng từ input text
    $customer_name = trim($_POST['customer_name']);
    $total_price = (float)$_POST['total_price'];
    $shipping_address = trim($_POST['shipping_address']);
    $status = $_POST['status'];

    // Lưu đơn hàng thủ công vào cột customer_name
    // Lưu ý: bảng orders cần có cột customer_name
    $pdo->prepare("INSERT INTO orders (customer_name, total_price, shipping_address, status) VALUES (?, ?, ?, ?)")
        ->execute([$customer_name, $total_price, $shipping_address, $status]);

    header("Location: index.php?page=orders&msg=created");
    exit;
}
?>
<style>
    :root {
        --shopee-red: #ee4d2d;
        --shopee-bg: #f6f6f6;
        --border-color: #e5e5e5;
        --text-main: #333333;
        --text-muted: #999999;
    }

    .sp-container {
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        background: var(--shopee-bg);
        padding: 20px;
        min-height: 100vh;
        margin: -30px;
    }

    /* Box Card chung */
    .sp-card {
        background: #fff;
        border-radius: 4px;
        box-shadow: 0 1px 4px rgba(0,0,0,0.05);
        margin-bottom: 20px;
    }

    /* Tiêu đề & Nút bấm */
    .sp-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 20px;
        border-bottom: 1px solid var(--border-color);
    }

    .sp-header h2 {
        font-size: 20px;
        color: var(--text-main);
        font-weight: 500;
        margin: 0;
    }

    .btn-sp-primary {
        background: var(--shopee-red);
        color: #fff;
        border: none;
        padding: 8px 16px;
        border-radius: 4px;
        font-size: 14px;
        cursor: pointer;
        text-decoration: none;
    }

    .btn-sp-primary:hover {
        background: #d73211;
    }

    .btn-sp-outline {
        background: #fff;
        color: #555;
        border: 1px solid #ccc;
        padding: 6px 12px;
        border-radius: 4px;
        font-size: 13px;
        cursor: pointer;
        text-decoration: none;
    }

    /* Tabs Trạng thái */
    .sp-tabs {
        display: flex;
        border-bottom: 1px solid var(--border-color);
        overflow-x: auto;
    }

    .sp-tabs a {
        flex: 1;
        text-align: center;
        padding: 15px 10px;
        color: var(--text-main);
        text-decoration: none;
        font-size: 14px;
        border-bottom: 2px solid transparent;
        cursor: pointer;
        white-space: nowrap;
    }

    .sp-tabs a:hover {
        color: var(--shopee-red);
    }

    .sp-tabs a.active {
        color: var(--shopee-red);
        border-bottom-color: var(--shopee-red);
        font-weight: 500;
    }

    /* Bộ lọc & Tìm kiếm */
    .sp-filter-bar {
        padding: 20px;
        background: #fafafa;
        border-bottom: 1px solid var(--border-color);
        display: flex;
        gap: 15px;
        flex-wrap: wrap;
    }

    .sp-input-group {
        display: flex;
        border: 1px solid var(--border-color);
        border-radius: 4px;
        background: #fff;
        overflow: hidden;
        width: 350px;
    }

    .sp-input-group select {
        border: none;
        padding: 8px 10px;
        border-right: 1px solid var(--border-color);
        outline: none;
        background: #f9f9f9;
        color: #555;
    }

    .sp-input-group input {
        border: none;
        padding: 8px 15px;
        flex: 1;
        outline: none;
    }

    .sp-input-group button {
        background: none;
        border: none;
        padding: 0 15px;
        cursor: pointer;
        color: var(--text-muted);
    }

    /* Bảng dữ liệu */
    .sp-table {
        width: 100%;
        border-collapse: collapse;
    }

    .sp-table th {
        background: #f5f5f5;
        color: var(--text-muted);
        font-weight: 400;
        padding: 12px 20px;
        text-align: left;
        font-size: 14px;
    }

    .sp-table td {
        padding: 15px 20px;
        border-bottom: 1px solid var(--border-color);
        font-size: 14px;
        color: var(--text-main);
        vertical-align: top;
    }

    .sp-table tr:hover {
        background: #fafafa;
    }

    /* Form Thêm/Sửa */
    .sp-form-group {
        margin-bottom: 20px;
    }

    .sp-form-group label {
        display: block;
        margin-bottom: 8px;
        font-size: 14px;
        color: var(--text-main);
    }

    .sp-form-control {
        width: 100%;
        padding: 10px 15px;
        border: 1px solid var(--border-color);
        border-radius: 4px;
        outline: none;
        font-size: 14px;
        box-sizing: border-box;
    }

    .sp-form-control:focus {
        border-color: var(--shopee-red);
    }

    /* Thông báo */
    .sp-alert {
        padding: 12px 20px;
        background: #eaffea;
        border: 1px solid #4caf50;
        color: #2e7d32;
        border-radius: 4px;
        margin-bottom: 20px;
        font-size: 14px;
    }

    /* Phân trang */
    .sp-pagination {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 15px 20px;
        gap: 10px;
        flex-wrap: wrap;
    }

    .sp-pagination-info {
        color: var(--text-muted);
        font-size: 14px;
    }

    .sp-page-links {
        display: flex;
        gap: 6px;
        align-items: center;
        flex-wrap: wrap;
    }

    .sp-page-link {
        min-width: 34px;
        padding: 7px 10px;
        border: 1px solid var(--border-color);
        background: #fff;
        color: var(--text-main);
        text-decoration: none;
        border-radius: 4px;
        text-align: center;
        font-size: 13px;
    }

    .sp-page-link:hover {
        color: var(--shopee-red);
        border-color: var(--shopee-red);
    }

    .sp-page-link.active {
        background: var(--shopee-red);
        color: #fff;
        border-color: var(--shopee-red);
    }

    .sp-page-link.disabled {
        color: #bbb;
        pointer-events: none;
        background: #f8f8f8;
    }


    .sp-search-wrapper {
        position: relative;
        flex: 1;
        display: flex;
        align-items: center;
    }

    .sp-search-wrapper i {
        position: absolute;
        left: 12px;
        color: var(--text-muted);
        font-size: 14px;
        pointer-events: none;
    }

    .sp-input-group .sp-search-input {
        padding-left: 36px;
        width: 100%;
    }


    /* Căn đều bố cục khu vực tìm kiếm */
    .sp-filter-form {
        display: grid;
        grid-template-columns: minmax(360px, 1fr) 190px auto;
        gap: 15px;
        width: 100%;
        align-items: center;
    }

    .sp-filter-form .sp-input-group {
        width: 100%;
    }

    .sp-filter-form .sp-form-control {
        height: 38px;
    }

    .sp-filter-form .btn-sp-outline {
        height: 38px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        white-space: nowrap;
        box-sizing: border-box;
    }

    @media (max-width: 768px) {
        .sp-filter-form {
            grid-template-columns: 1fr;
        }

        .sp-filter-form .sp-input-group,
        .sp-filter-form .sp-form-control,
        .sp-filter-form .btn-sp-outline {
            width: 100% !important;
        }
    }

</style>

<div class="sp-container">

<?php if (isset($_GET['msg'])): ?>
    <?php
        $msgs = [
            'deleted' => 'Đã xóa đơn hàng thành công',
            'updated' => 'Đã cập nhật đơn hàng',
            'created' => 'Tạo đơn hàng mới thành công'
        ];
        $m = $_GET['msg'];
    ?>
    <div class="sp-alert">
        <i class="fa-solid fa-circle-check"></i>
        <?= isset($msgs[$m]) ? $msgs[$m] : 'Thành công' ?>
    </div>
<?php endif; ?>

<?php
if ($action === 'add'):
?>
    <div class="sp-card" style="max-width: 600px; margin: 0 auto;">
        <div class="sp-header">
            <h2>Tạo đơn hàng thủ công</h2>
        </div>

        <div style="padding: 20px;">
            <form action="index.php?page=orders" method="POST">
                <input type="hidden" name="create_order" value="1">

                <div class="sp-form-group">
                    <label>Khách hàng mua</label>
                    <input type="text" name="customer_name" class="sp-form-control" placeholder="Nhập tên khách hàng..." required>
                </div>

                <div class="sp-form-group">
                    <label>Doanh thu đơn hàng (VNĐ)</label>
                    <input type="number" name="total_price" class="sp-form-control" placeholder="0" required>
                </div>

                <div class="sp-form-group">
                    <label>Địa chỉ giao hàng</label>
                    <input type="text" name="shipping_address" class="sp-form-control" required>
                </div>

                <div class="sp-form-group">
                    <label>Trạng thái</label>
                    <select name="status" class="sp-form-control">
                        <option value="pending">Chờ xác nhận</option>
                        <option value="processing">Chuẩn bị hàng</option>
                        <option value="shipped">Đã giao vận chuyển</option>
                        <option value="completed">Hoàn thành</option>
                    </select>
                </div>

                <div style="text-align: right; margin-top: 30px;">
                    <a href="index.php?page=orders" class="btn-sp-outline" style="margin-right: 10px;">Hủy</a>
                    <button type="submit" class="btn-sp-primary">Lưu đơn hàng</button>
                </div>
            </form>
        </div>
    </div>

<?php
// =========================================================================
//FORM CẬP NHẬT
// =========================================================================
elseif ($action === 'edit' && isset($_GET['id'])):
    $id = (int)$_GET['id'];

    $order_stmt = $pdo->prepare("SELECT * FROM orders WHERE id = ?");
    $order_stmt->execute([$id]);
    $order = $order_stmt->fetch();

    if (!$order):
?>
        <div class="sp-card" style="padding: 30px; text-align: center;">
            <p style="color: var(--text-muted);">Không tìm thấy đơn hàng.</p>
            <a href="index.php?page=orders" class="btn-sp-primary">Quay lại danh sách</a>
        </div>
<?php
    else:
?>
    <div class="sp-card" style="max-width: 600px; margin: 0 auto;">
        <div class="sp-header">
            <h2>Cập nhật đơn hàng: <?= htmlspecialchars($order['id']) ?></h2>
        </div>

        <div style="padding: 20px;">
            <form action="index.php?page=orders" method="POST">
                <input type="hidden" name="update_order" value="1">
                <input type="hidden" name="order_id" value="<?= htmlspecialchars($order['id']) ?>">

                <div class="sp-form-group">
                    <label>Địa chỉ nhận hàng</label>
                    <input type="text" name="shipping_address" class="sp-form-control" value="<?= htmlspecialchars($order['shipping_address'] ?? '') ?>" required>
                </div>

                <div class="sp-form-group">
                    <label>Trạng thái đơn hàng</label>
                    <select name="status" class="sp-form-control">
                        <option value="pending" <?= ($order['status'] ?? '') == 'pending' ? 'selected' : '' ?>>Chờ xác nhận</option>
                        <option value="processing" <?= ($order['status'] ?? '') == 'processing' ? 'selected' : '' ?>>Chuẩn bị hàng</option>
                        <option value="shipped" <?= ($order['status'] ?? '') == 'shipped' ? 'selected' : '' ?>>Đã giao vận chuyển</option>
                        <option value="completed" <?= ($order['status'] ?? '') == 'completed' ? 'selected' : '' ?>>Hoàn thành</option>
                        <option value="cancelled" <?= ($order['status'] ?? '') == 'cancelled' ? 'selected' : '' ?>>Đã hủy</option>
                    </select>
                </div>

                <div style="text-align: right; margin-top: 30px;">
                    <a href="index.php?page=orders" class="btn-sp-outline" style="margin-right: 10px;">Hủy</a>
                    <button type="submit" class="btn-sp-primary">Cập nhật</button>
                </div>
            </form>
        </div>
    </div>
<?php
    endif;


else:
    // Xử lý bộ lọc
    $filter_status = isset($_GET['status_filter']) ? $_GET['status_filter'] : 'all';
    $search_kw = isset($_GET['search']) ? trim($_GET['search']) : '';

 
    $sort = isset($_GET['sort']) ? $_GET['sort'] : 'desc';
    $sort_sql = ($sort === 'asc') ? 'ASC' : 'DESC';

    $limit = 8;
    $current_page = isset($_GET['p']) ? (int)$_GET['p'] : 1;
    if ($current_page < 1) {
        $current_page = 1;
    }
    $offset = ($current_page - 1) * $limit;

    $where = "1=1";
    $params = [];

    if ($filter_status !== 'all') {
        $where .= " AND o.status = ?";
        $params[] = $filter_status;
    }


    if ($search_kw !== '') {
        $where .= " AND (
            o.id LIKE ?
            OR o.customer_name LIKE ?
            OR u.fullname LIKE ?
        )";
        $params[] = "%$search_kw%";
        $params[] = "%$search_kw%";
        $params[] = "%$search_kw%";
    }

    // Đếm tổng số đơn phù hợp để tính số trang
    $count_sql = "SELECT COUNT(*)
                  FROM orders o
                  LEFT JOIN users u ON o.user_id = u.id
                  WHERE $where";

    $count_stmt = $pdo->prepare($count_sql);
    $count_stmt->execute($params);
    $total_orders = (int)$count_stmt->fetchColumn();

    $total_pages = (int)ceil($total_orders / $limit);
    if ($total_pages < 1) {
        $total_pages = 1;
    }

    if ($current_page > $total_pages) {
        $current_page = $total_pages;
        $offset = ($current_page - 1) * $limit;
    }

    $sql = "SELECT o.*, u.fullname
            FROM orders o
            LEFT JOIN users u ON o.user_id = u.id
            WHERE $where
            ORDER BY o.id $sort_sql
            LIMIT $limit OFFSET $offset";

    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $orders = $stmt->fetchAll();
?>
    <div class="sp-card">
        <div class="sp-header">
            <h2>Quản lý đơn hàng</h2>
            <a href="index.php?page=orders&action=add" class="btn-sp-primary">
                <i class="fa-solid fa-plus"></i> Thêm đơn hàng
            </a>
        </div>

        <div class="sp-tabs">
            <a href="index.php?page=orders&status_filter=all&sort=<?= urlencode($sort) ?>&p=1" class="<?= $filter_status == 'all' ? 'active' : '' ?>">Tất cả</a>
            <a href="index.php?page=orders&status_filter=pending&sort=<?= urlencode($sort) ?>&p=1" class="<?= $filter_status == 'pending' ? 'active' : '' ?>">Chờ xác nhận</a>
            <a href="index.php?page=orders&status_filter=processing&sort=<?= urlencode($sort) ?>&p=1" class="<?= $filter_status == 'processing' ? 'active' : '' ?>">Chuẩn bị hàng</a>
            <a href="index.php?page=orders&status_filter=shipped&sort=<?= urlencode($sort) ?>&p=1" class="<?= $filter_status == 'shipped' ? 'active' : '' ?>">Đã giao ĐVVC</a>
            <a href="index.php?page=orders&status_filter=completed&sort=<?= urlencode($sort) ?>&p=1" class="<?= $filter_status == 'completed' ? 'active' : '' ?>">Hoàn thành</a>
            <a href="index.php?page=orders&status_filter=cancelled&sort=<?= urlencode($sort) ?>&p=1" class="<?= $filter_status == 'cancelled' ? 'active' : '' ?>">Đã hủy</a>
        </div>

        <div class="sp-filter-bar">
            <form action="index.php" method="GET" class="sp-filter-form">
                <input type="hidden" name="page" value="orders">
                <input type="hidden" name="status_filter" value="<?= htmlspecialchars($filter_status) ?>">
                <input type="hidden" name="p" value="1">

                <div class="sp-input-group">
                    <select disabled>
                        <option>ID đơn hàng / Tên khách</option>
                    </select>
                    <div class="sp-search-wrapper">
                        <i class="fa-solid fa-magnifying-glass"></i>
                        <input
                            type="text"
                            name="search"
                            class="sp-search-input"
                            placeholder="Nhập ID đơn hàng hoặc tên khách"
                            value="<?= htmlspecialchars($search_kw) ?>"
                        >
                    </div>
                    <button type="submit" title="Tìm kiếm">
                        <i class="fa-solid fa-magnifying-glass"></i>
                    </button>
                </div>

                <select name="sort" class="sp-form-control" onchange="this.form.submit()">
                    <option value="desc" <?= $sort == 'desc' ? 'selected' : '' ?>>ID giảm dần</option>
                    <option value="asc" <?= $sort == 'asc' ? 'selected' : '' ?>>ID tăng dần</option>
                </select>

                <?php if ($search_kw !== ''): ?>
                    <a href="index.php?page=orders&status_filter=<?= urlencode($filter_status) ?>&sort=<?= urlencode($sort) ?>&p=1" class="btn-sp-outline">
                        Xóa tìm kiếm
                    </a>
                <?php endif; ?>
            </form>
        </div>

        <table class="sp-table">
            <thead>
                <tr>
                    <th>Sản phẩm / Đơn hàng</th>
                    <th>Doanh thu</th>
                    <th>Trạng thái</th>
                    <th>Thời gian tạo</th>
                    <th style="text-align: right;">Thao tác</th>
                </tr>
            </thead>

            <tbody>
                <?php if (count($orders) > 0): ?>
                    <?php foreach ($orders as $o): ?>
                        <tr>
                            <td>
                                <div style="font-weight: 500; color: var(--shopee-red); margin-bottom: 5px;">
                                    ID: <?= htmlspecialchars($o['id']) ?>
                                </div>

                                <div style="color: var(--text-muted); font-size: 13px;">
                                    Khách:
                                    <?php
                                        $hien_thi_ten = !empty($o['customer_name'])
                                            ? $o['customer_name']
                                            : ($o['fullname'] ?? 'Khách lẻ');

                                        echo htmlspecialchars($hien_thi_ten);
                                    ?>
                                </div>

                                <div style="color: var(--text-muted); font-size: 13px; margin-top: 5px;">
                                    <i class="fa-solid fa-location-dot"></i>
                                    <?= htmlspecialchars($o['shipping_address'] ?? '') ?>
                                </div>
                            </td>

                            <td style="color: var(--text-main); font-weight: 500;">
                                ₫<?= number_format((float)$o['total_price'], 0, ',', '.') ?>
                            </td>

                            <td>
                                <?php
                                    $status_text = '';
                                    if ($o['status'] == 'pending') {
                                        $status_text = 'Chờ xác nhận';
                                    } elseif ($o['status'] == 'processing') {
                                        $status_text = 'Chuẩn bị hàng';
                                    } elseif ($o['status'] == 'shipped') {
                                        $status_text = 'Đã giao vận chuyển';
                                    } elseif ($o['status'] == 'completed') {
                                        $status_text = 'Hoàn thành';
                                    } elseif ($o['status'] == 'cancelled') {
                                        $status_text = 'Đã hủy';
                                    } else {
                                        $status_text = 'Không xác định';
                                    }

                                    echo "<span>" . htmlspecialchars($status_text) . "</span>";
                                ?>
                            </td>

                            <td style="color: var(--text-muted);">
                                <?php
                                    if (!empty($o['created_at'])) {
                                        echo date('d/m/Y H:i', strtotime($o['created_at']));
                                    } else {
                                        echo '—';
                                    }
                                ?>
                            </td>

                            <td style="text-align: right;">
                                <a href="index.php?page=orders&action=edit&id=<?= urlencode($o['id']) ?>" style="color: #0056b3; text-decoration: none; display: block; margin-bottom: 8px;">
                                    Cập nhật
                                </a>

                                <a href="index.php?page=orders&action=delete&id=<?= urlencode($o['id']) ?>" onclick="return confirm('Bạn có chắc chắn muốn xóa?');" style="color: var(--text-muted); text-decoration: none;">
                                    Xóa
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="5" style="text-align: center; padding: 50px 0; color: var(--text-muted);">
                            <div style="font-size: 40px; margin-bottom: 10px;">
                                <i class="fa-solid fa-file-invoice"></i>
                            </div>
                            Không tìm thấy đơn hàng
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>

        <?php
            $query_base = "index.php?page=orders"
                . "&status_filter=" . urlencode($filter_status)
                . "&search=" . urlencode($search_kw)
                . "&sort=" . urlencode($sort);

            $from_order = $total_orders > 0 ? $offset + 1 : 0;
            $to_order = min($offset + $limit, $total_orders);
        ?>

        <div class="sp-pagination">
            

            <div class="sp-page-links">
                <a class="sp-page-link <?= $current_page <= 1 ? 'disabled' : '' ?>"
                   href="<?= $query_base ?>&p=<?= $current_page - 1 ?>">
                    Trước
                </a>

                <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                    <a class="sp-page-link <?= $i == $current_page ? 'active' : '' ?>"
                       href="<?= $query_base ?>&p=<?= $i ?>">
                        <?= $i ?>
                    </a>
                <?php endfor; ?>

                <a class="sp-page-link <?= $current_page >= $total_pages ? 'disabled' : '' ?>"
                   href="<?= $query_base ?>&p=<?= $current_page + 1 ?>">
                    Sau
                </a>
            </div>
        </div>
    </div>
<?php endif; ?>

</div>
