<?php
// admin/suppliers.php

if (!isset($pdo)) die('Truy cập bị từ chối!');

try {
    $pdo->exec("CREATE TABLE IF NOT EXISTS suppliers (
        id INT AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(255) NOT NULL,
        phone VARCHAR(20),
        email VARCHAR(255),
        address TEXT,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )");
} catch (\PDOException $e) {}

$action = isset($_GET['action']) ? $_GET['action'] : 'list';


// ======================================================
// XÓA NHÀ CUNG CẤP
// ======================================================

if ($action === 'delete' && isset($_GET['id'])) {
    $id = (int)$_GET['id'];

    try {
        $pdo->prepare("DELETE FROM suppliers WHERE id = ?")->execute([$id]);

        header("Location: index.php?page=suppliers&msg=deleted");
        exit;

    } catch (\PDOException $e) {

        header("Location: index.php?page=suppliers&msg=error");
        exit;
    }
}


// ======================================================
// THÊM / CẬP NHẬT
// ======================================================

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $name = trim($_POST['name']);
    $phone = trim($_POST['phone']);
    $email = trim($_POST['email']);
    $address = trim($_POST['address']);

    // THÊM
    if (isset($_POST['create_supplier'])) {

        $sql = "INSERT INTO suppliers (name, phone, email, address)
                VALUES (?, ?, ?, ?)";

        $pdo->prepare($sql)->execute([
            $name,
            $phone,
            $email,
            $address
        ]);

        header("Location: index.php?page=suppliers&msg=created");
        exit;
    }

    // CẬP NHẬT
    if (isset($_POST['update_supplier'])) {

        $id = (int)$_POST['supplier_id'];

        $sql = "UPDATE suppliers
                SET name=?,
                    phone=?,
                    email=?,
                    address=?
                WHERE id=?";

        $pdo->prepare($sql)->execute([
            $name,
            $phone,
            $email,
            $address,
            $id
        ]);

        header("Location: index.php?page=suppliers&msg=updated");
        exit;
    }
}
?>

<style>

    .breadcrumb {
        font-size: 13px;
        color: #777;
        margin-bottom: 20px;
    }

    .breadcrumb a {
        color: #ee4d2d;
        text-decoration: none;
    }

    .box {
        background: #fff;
        border-radius: 4px;
        box-shadow: 0 1px 4px rgba(0,0,0,0.05);
        padding: 20px;
        margin-bottom: 20px;
    }

    .sp-table {
        width: 100%;
        border-collapse: collapse;
        margin-top: 15px;
    }

    .sp-table th {
        background: #f8f9fa;
        color: #555;
        font-weight: 500;
        padding: 12px 15px;
        text-align: left;
        font-size: 14px;
        border-bottom: 2px solid #eee;
    }

    .sp-table td {
        padding: 12px 15px;
        border-bottom: 1px solid #eee;
        font-size: 14px;
        color: #333;
        vertical-align: middle;
    }

    .sp-table tr:hover {
        background: #fafafa;
    }

    .btn-create {
        background: #ee4d2d;
        color: #fff;
        padding: 10px 15px;
        text-decoration: none;
        border-radius: 4px;
        font-size: 14px;
        font-weight: 500;
    }

    .btn-create:hover {
        background: #d73211;
    }

    .btn-action {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 32px;
        height: 32px;
        color: #fff;
        border-radius: 4px;
        text-decoration: none;
        margin-right: 5px;
        font-size: 13px;
    }

    .btn-edit {
        background: #007bff;
    }

    .btn-edit:hover {
        background: #0056b3;
    }

    .btn-delete {
        background: #dc3545;
    }

    .btn-delete:hover {
        background: #c82333;
    }

    .sp-form-group {
        margin-bottom: 15px;
    }

    .sp-form-group label {
        display: block;
        margin-bottom: 8px;
        font-size: 14px;
        font-weight: 500;
    }

    .sp-form-control {
        width: 100%;
        padding: 10px;
        border: 1px solid #ddd;
        border-radius: 4px;
        outline: none;
        box-sizing: border-box;
    }

    .sp-form-control:focus {
        border-color: #ee4d2d;
    }

    .sp-alert {
        padding: 12px 20px;
        background: #eaffea;
        border: 1px solid #4caf50;
        color: #2e7d32;
        border-radius: 4px;
        margin-bottom: 20px;
        font-size: 14px;
    }

</style>

<div class="breadcrumb">
    <i class="fa-solid fa-house"></i>
    <a href="index.php">Trang chủ</a>
    / Đối tác / Nhà cung cấp
</div>


<?php if (isset($_GET['msg'])): ?>

    <?php
        if ($_GET['msg'] == 'deleted') {
            echo '<div class="sp-alert">
                    <i class="fa-solid fa-check"></i>
                    Đã xóa thông tin nhà cung cấp!
                  </div>';
        }

        if ($_GET['msg'] == 'updated') {
            echo '<div class="sp-alert">
                    <i class="fa-solid fa-check"></i>
                    Đã cập nhật thông tin thành công!
                  </div>';
        }

        if ($_GET['msg'] == 'created') {
            echo '<div class="sp-alert">
                    <i class="fa-solid fa-check"></i>
                    Đã thêm nhà cung cấp mới!
                  </div>';
        }
    ?>

<?php endif; ?>


<?php

// ======================================================
// FORM THÊM / SỬA
// ======================================================

if ($action === 'add' || $action === 'edit'):

    $s = [
        'id' => '',
        'name' => '',
        'phone' => '',
        'email' => '',
        'address' => ''
    ];

    if ($action === 'edit') {

        $stmt = $pdo->prepare("SELECT * FROM suppliers WHERE id = ?");
        $stmt->execute([(int)$_GET['id']]);

        $s = $stmt->fetch();
    }

?>

<div class="box" style="max-width: 600px;">

    <h3 style="
        margin-top: 0;
        margin-bottom: 20px;
        border-bottom: 1px solid #eee;
        padding-bottom: 15px;
    ">
        <?= $action == 'add'
            ? 'Thêm Nhà Cung Cấp Mới'
            : 'Cập Nhật Nhà Cung Cấp' ?>
    </h3>

    <form action="index.php?page=suppliers" method="POST">

        <?php if ($action == 'add'): ?>

            <input type="hidden" name="create_supplier" value="1">

        <?php else: ?>

            <input type="hidden" name="update_supplier" value="1">
            <input type="hidden" name="supplier_id" value="<?= $s['id'] ?>">

        <?php endif; ?>


        <div class="sp-form-group">
            <label>Tên nhà cung cấp / Hãng sản xuất *</label>

            <input
                type="text"
                name="name"
                class="sp-form-control"
                value="<?= htmlspecialchars($s['name']) ?>"
                placeholder="Ví dụ: Công ty LEGO VN..."
                required
            >
        </div>


        <div style="display:flex; gap:15px;">

            <div class="sp-form-group" style="flex:1;">
                <label>Số điện thoại liên hệ</label>

                <input
                    type="text"
                    name="phone"
                    class="sp-form-control"
                    value="<?= htmlspecialchars($s['phone']) ?>"
                >
            </div>

            <div class="sp-form-group" style="flex:1;">
                <label>Email hỗ trợ</label>

                <input
                    type="email"
                    name="email"
                    class="sp-form-control"
                    value="<?= htmlspecialchars($s['email']) ?>"
                >
            </div>

        </div>


        <div class="sp-form-group">
            <label>Địa chỉ xưởng / Kho hàng</label>

            <input
                type="text"
                name="address"
                class="sp-form-control"
                value="<?= htmlspecialchars($s['address']) ?>"
            >
        </div>


        <div style="text-align:right; margin-top:25px;">

            <a href="index.php?page=suppliers"
               style="
                    padding:8px 15px;
                    color:#555;
                    text-decoration:none;
                    margin-right:10px;
                    border:1px solid #ccc;
                    border-radius:4px;
               ">
                Hủy bỏ
            </a>

            <button type="submit"
                    style="
                        background:#ee4d2d;
                        color:#fff;
                        border:none;
                        padding:9px 20px;
                        border-radius:4px;
                        cursor:pointer;
                        font-weight:500;
                    ">

                <i class="fa-solid fa-floppy-disk"></i>
                LƯU THÔNG TIN

            </button>

        </div>

    </form>

</div>

<?php
// ======================================================
// DANH SÁCH
// ======================================================
else:

    $keyword = isset($_GET['keyword'])
        ? trim($_GET['keyword'])
        : '';

?>

<div class="box">

    <div style="
        display:flex;
        justify-content:space-between;
        align-items:center;
        gap:10px;
        flex-wrap:wrap;
        margin-bottom:15px;
    ">

        <!-- FORM TÌM KIẾM -->
        <form method="GET"
              action="index.php"
              style="display:flex; gap:10px;">

            <input type="hidden" name="page" value="suppliers">

            <input
                type="text"
                name="keyword"
                placeholder="Nhập tên, ký tự, SĐT hoặc email..."
                value="<?= htmlspecialchars($keyword) ?>"
                class="sp-form-control"
                style="width:280px;"
            >

            <button type="submit"
                    style="
                        background:#ee4d2d;
                        color:#fff;
                        border:none;
                        padding:10px 15px;
                        border-radius:4px;
                        cursor:pointer;
                    ">

                <i class="fa-solid fa-magnifying-glass"></i>
                Tìm kiếm

            </button>

        </form>


        <!-- NÚT THÊM -->
        <a href="index.php?page=suppliers&action=add"
           class="btn-create">

            <i class="fa-solid fa-plus"></i>
            Thêm Nhà cung cấp

        </a>

    </div>


    <table class="sp-table">

        <thead>
            <tr>
                <th style="width:50px; text-align:center;">STT</th>
                <th>Tên đối tác / Hãng</th>
                <th>Số điện thoại</th>
                <th>Email</th>
                <th>Địa chỉ</th>
                <th style="text-align:center; width:100px;">
                    Hành động
                </th>
            </tr>
        </thead>

        <tbody>

        <?php

        // ======================================================
        // TÌM KIẾM THEO KÝ TỰ VÀ TỪ KHÓA
        // ======================================================
        // Cách hoạt động:
        // - Nhập chữ/tên: tìm trong tên nhà cung cấp và địa chỉ
        // - Nhập số: tìm thêm trong số điện thoại
        // - Nhập email hoặc có ký tự @: tìm thêm trong email
        // - Nhập nhiều từ khóa, ví dụ "Phạm An": mỗi từ khóa đều phải khớp

        if ($keyword != '') {

            // Chuẩn hóa khoảng trắng
            $keyword = preg_replace('/\s+/', ' ', $keyword);
            $keywords = explode(' ', $keyword);

            $whereParts = [];
            $params = [];

            foreach ($keywords as $kw) {
                $kw = trim($kw);

                if ($kw === '') {
                    continue;
                }

                $conditions = [];

                // Luôn tìm theo tên và địa chỉ
                $conditions[] = "name LIKE ?";
                $params[] = "%$kw%";

                $conditions[] = "address LIKE ?";
                $params[] = "%$kw%";

                // Nếu từ khóa có số thì tìm thêm trong số điện thoại
                if (preg_match('/[0-9]/', $kw)) {
                    $conditions[] = "phone LIKE ?";
                    $params[] = "%$kw%";
                }

                // Nếu từ khóa có @ hoặc dấu chấm email thì tìm thêm trong email
                if (strpos($kw, '@') !== false || strpos($kw, '.') !== false) {
                    $conditions[] = "email LIKE ?";
                    $params[] = "%$kw%";
                }

                $whereParts[] = "(" . implode(" OR ", $conditions) . ")";
            }

            if (count($whereParts) > 0) {
                $sql = "SELECT * FROM suppliers
                        WHERE " . implode(" AND ", $whereParts) . "
                        ORDER BY
                            CASE
                                WHEN name LIKE ? THEN 0
                                WHEN address LIKE ? THEN 1
                                ELSE 2
                            END,
                            id DESC";

                // Tham số để ưu tiên kết quả khớp tên trước
                $params[] = "%$keyword%";
                $params[] = "%$keyword%";

                $stmt = $pdo->prepare($sql);
                $stmt->execute($params);
                $suppliers = $stmt->fetchAll();

            } else {
                $sql = "SELECT * FROM suppliers ORDER BY id DESC";
                $suppliers = $pdo->query($sql)->fetchAll();
            }

        } else {

            $sql = "SELECT * FROM suppliers ORDER BY id DESC";

            $suppliers = $pdo->query($sql)->fetchAll();
        }

        $stt = 1;

        if (count($suppliers) > 0):

            foreach ($suppliers as $row):

        ?>

            <tr>

                <td style="
                    text-align:center;
                    font-weight:bold;
                    color:#777;
                ">
                    <?= $stt++ ?>
                </td>

                <td style="
                    font-weight:500;
                    color:#2c3e50;
                ">
                    <i class="fa-solid fa-building"
                       style="color:#17a2b8; margin-right:5px;"></i>

                    <?= htmlspecialchars($row['name']) ?>
                </td>

                <td>
                    <?= htmlspecialchars($row['phone'] ?? '---') ?>
                </td>

                <td>
                    <?= htmlspecialchars($row['email'] ?? '---') ?>
                </td>

                <td style="
                    font-size:13px;
                    color:#555;
                ">
                    <?= htmlspecialchars($row['address'] ?? '---') ?>
                </td>

                <td style="text-align:center;">

                    <a href="index.php?page=suppliers&action=edit&id=<?= $row['id'] ?>"
                       class="btn-action btn-edit"
                       title="Chỉnh sửa">

                        <i class="fa-solid fa-pencil"></i>

                    </a>

                    <a href="index.php?page=suppliers&action=delete&id=<?= $row['id'] ?>"
                       class="btn-action btn-delete"
                       onclick="return confirm('Bạn có chắc chắn muốn xóa nhà cung cấp này?');"
                       title="Xóa bỏ">

                        <i class="fa-solid fa-trash"></i>

                    </a>

                </td>

            </tr>

        <?php
            endforeach;

        else:
        ?>

            <tr>
                <td colspan="6"
                    style="
                        text-align:center;
                        padding:40px;
                        color:#777;
                    ">

                    Không tìm thấy dữ liệu nhà cung cấp.

                </td>
            </tr>

        <?php endif; ?>

        </tbody>

    </table>

</div>

<?php endif; ?>