<?php
// admin/users.php
if (!isset($pdo)) die('Truy cập bị từ chối!');

$action = isset($_GET['action']) ? $_GET['action'] : 'list';

// =========================================================================
// 1. XỬ LÝ DATABASE (XÓA, THÊM, CẬP NHẬT)
// =========================================================================

// XÓA NGƯỜI DÙNG
if ($action === 'delete' && isset($_GET['id'])) {
    $id = (int)$_GET['id'];
    try {
        $pdo->prepare("DELETE FROM users WHERE id = ?")->execute([$id]);
        header("Location: index.php?page=users&msg=deleted"); exit;
    } catch (\PDOException $e) {
        // Lỗi khóa ngoại nếu user này đã từng mua hàng (có đơn hàng)
        header("Location: index.php?page=users&msg=error_fk"); exit;
    }
}

// CẬP NHẬT HOẶC THÊM MỚI (Xử lý Form)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $fullname = trim($_POST['fullname']);
    $email = trim($_POST['email']);
    $phone = trim($_POST['phone']);
    $role = $_POST['role'];

    // Xử lý Thêm mới
    if (isset($_POST['create_user'])) {
        $password = md5(trim($_POST['password'])); // Mã hóa mật khẩu
        $sql = "INSERT INTO users (fullname, email, phone, password, role) VALUES (?, ?, ?, ?, ?)";
        $pdo->prepare($sql)->execute([$fullname, $email, $phone, $password, $role]);
        header("Location: index.php?page=users&msg=created"); exit;
    }
    
    // Xử lý Cập nhật
    if (isset($_POST['update_user'])) {
        $id = (int)$_POST['user_id'];
        
        // Nếu admin có nhập mật khẩu mới thì đổi, không thì giữ nguyên mật khẩu cũ
        if (!empty($_POST['password'])) {
            $password = md5(trim($_POST['password']));
            $sql = "UPDATE users SET fullname=?, email=?, phone=?, role=?, password=? WHERE id=?";
            $pdo->prepare($sql)->execute([$fullname, $email, $phone, $role, $password, $id]);
        } else {
            $sql = "UPDATE users SET fullname=?, email=?, phone=?, role=? WHERE id=?";
            $pdo->prepare($sql)->execute([$fullname, $email, $phone, $role, $id]);
        }
        header("Location: index.php?page=users&msg=updated"); exit;
    }
}
?>

<style>
    /* CSS Bố cục chung */
    .breadcrumb { font-size: 13px; color: #777; margin-bottom: 20px; }
    .breadcrumb a { color: #ee4d2d; text-decoration: none; }
    .box { background: #fff; border-radius: 4px; box-shadow: 0 1px 4px rgba(0,0,0,0.05); padding: 20px; margin-bottom: 20px; }
    
    /* Bảng dữ liệu */
    .sp-table { width: 100%; border-collapse: collapse; margin-top: 15px; }
    .sp-table th { background: #f8f9fa; color: #555; font-weight: 500; padding: 12px 15px; text-align: left; font-size: 14px; border-bottom: 2px solid #eee; }
    .sp-table td { padding: 12px 15px; border-bottom: 1px solid #eee; font-size: 14px; color: #333; vertical-align: middle; }
    .sp-table tr:hover { background: #fafafa; }
    
    /* Nút bấm */
    .btn-create { background: #ee4d2d; color: #fff; padding: 8px 15px; text-decoration: none; border-radius: 4px; font-size: 14px; font-weight: 500; float: right; }
    .btn-create:hover { background: #d73211; }
    .btn-action { display: inline-flex; align-items: center; justify-content: center; width: 32px; height: 32px; color: #fff; border-radius: 4px; text-decoration: none; margin-right: 5px; font-size: 13px; }
    .btn-edit { background: #007bff; }
    .btn-edit:hover { background: #0056b3; }
    .btn-delete { background: #dc3545; }
    .btn-delete:hover { background: #c82333; }
    
    /* Form */
    .sp-form-group { margin-bottom: 15px; }
    .sp-form-group label { display: block; margin-bottom: 8px; font-size: 14px; font-weight: 500; }
    .sp-form-control { width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px; outline: none; box-sizing: border-box; }
    .sp-form-control:focus { border-color: #ee4d2d; }
    .sp-alert { padding: 12px 20px; background: #eaffea; border: 1px solid #4caf50; color: #2e7d32; border-radius: 4px; margin-bottom: 20px; font-size: 14px; }
    .sp-alert.error { background: #ffebeb; border-color: #f44336; color: #d32f2f; }

    /* Thanh tìm kiếm */
    .search-bar {
        display: flex;
        gap: 10px;
        align-items: center;
        flex-wrap: wrap;
        margin-bottom: 15px;
    }

    .search-input {
        width: 300px;
        padding: 10px;
        border: 1px solid #ddd;
        border-radius: 4px;
        outline: none;
        box-sizing: border-box;
    }

    .search-input:focus {
        border-color: #ee4d2d;
    }

    .btn-search {
        background: #ee4d2d;
        color: #fff;
        border: none;
        padding: 10px 15px;
        border-radius: 4px;
        cursor: pointer;
        font-size: 14px;
    }

    .btn-search:hover {
        background: #138496;
    }

    .btn-clear {
        background: #fff;
        color: #555;
        border: 1px solid #ccc;
        padding: 9px 15px;
        border-radius: 4px;
        text-decoration: none;
        font-size: 14px;
    }

    .btn-clear:hover {
        background: #f8f8f8;
    }

</style>

<div class="breadcrumb">
    <i class="fa-solid fa-house"></i> <a href="index.php">Trang chủ</a> / Người dùng / Danh sách
</div>

<?php if (isset($_GET['msg'])): ?>
    <?php 
        if ($_GET['msg'] == 'deleted') echo '<div class="sp-alert"><i class="fa-solid fa-check"></i> Đã xóa tài khoản thành công!</div>';
        if ($_GET['msg'] == 'updated') echo '<div class="sp-alert"><i class="fa-solid fa-check"></i> Đã cập nhật thông tin người dùng!</div>';
        if ($_GET['msg'] == 'created') echo '<div class="sp-alert"><i class="fa-solid fa-check"></i> Đã cấp mới một tài khoản thành công!</div>';
        if ($_GET['msg'] == 'error_fk') echo '<div class="sp-alert error"><i class="fa-solid fa-triangle-exclamation"></i> Không thể xóa vì tài khoản này đã phát sinh đơn hàng trên hệ thống.</div>';
    ?>
<?php endif; ?>

<?php
// =========================================================================
// 2. FORM THÊM / SỬA NGƯỜI DÙNG
// =========================================================================
if ($action === 'add' || $action === 'edit'):
    $u = ['id'=>'', 'fullname'=>'', 'email'=>'', 'phone'=>'', 'role'=>'user'];
    if ($action === 'edit') {
        $stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
        $stmt->execute([(int)$_GET['id']]);
        $u = $stmt->fetch();
    }
?>
    <div class="box" style="max-width: 600px;">
        <h3 style="margin-top: 0; margin-bottom: 20px; border-bottom: 1px solid #eee; padding-bottom: 15px;">
            <?= $action=='add' ? 'Tạo Tài Khoản Mới' : 'Cập Nhật Tài Khoản' ?>
        </h3>
        
        <form action="index.php?page=users" method="POST">
            <?php if($action=='add'): ?>
                <input type="hidden" name="create_user" value="1">
            <?php else: ?>
                <input type="hidden" name="update_user" value="1">
                <input type="hidden" name="user_id" value="<?= $u['id'] ?>">
            <?php endif; ?>
            
            <div class="sp-form-group">
                <label>Họ và Tên *</label>
                <input type="text" name="fullname" class="sp-form-control" value="<?= htmlspecialchars($u['fullname']) ?>" required>
            </div>
            
            <div class="sp-form-group">
                <label>Email đăng nhập *</label>
                <input type="email" name="email" class="sp-form-control" value="<?= htmlspecialchars($u['email']) ?>" required>
            </div>

            <div class="sp-form-group">
                <label>Số điện thoại liên hệ *</label>
                <input type="text" name="phone" class="sp-form-control" value="<?= htmlspecialchars($u['phone']) ?>" required>
            </div>

            <div class="sp-form-group">
                <label><?= $action=='add' ? 'Mật khẩu *' : 'Mật khẩu mới (Để trống nếu không muốn đổi)' ?></label>
                <input type="password" name="password" class="sp-form-control" <?= $action=='add' ? 'required' : '' ?> placeholder="Nhập mật khẩu...">
            </div>

            <div class="sp-form-group">
                <label>Phân quyền hệ thống *</label>
                <select name="role" class="sp-form-control" required>
                    <option value="user" <?= $u['role'] == 'user' ? 'selected' : '' ?>>Khách hàng (Chỉ mua sắm)</option>
                    <option value="admin" <?= $u['role'] == 'admin' ? 'selected' : '' ?>>Quản trị viên (Truy cập Admin)</option>
                </select>
            </div>

            <div style="text-align: right; margin-top: 25px;">
                <a href="index.php?page=users" style="padding: 8px 15px; color: #555; text-decoration: none; margin-right: 10px; border: 1px solid #ccc; border-radius: 4px;">Hủy bỏ</a>
                <button type="submit" style="background: #ee4d2d; color: #fff; border: none; padding: 9px 20px; border-radius: 4px; cursor: pointer; font-weight: 500;">
                    <i class="fa-solid fa-floppy-disk"></i> LƯU TÀI KHOẢN
                </button>
            </div>
        </form>
    </div>

<?php
// =========================================================================
// 3. BẢNG DANH SÁCH NGƯỜI DÙNG
// =========================================================================
else:
    $keyword = isset($_GET['keyword']) ? trim($_GET['keyword']) : '';
?>
    <div class="box">
        <div style="display:flex; justify-content:space-between; align-items:center; gap:10px; flex-wrap:wrap; margin-bottom:15px;">

            <form method="GET" action="index.php" class="search-bar">
                <input type="hidden" name="page" value="users">

                <input
                    type="text"
                    name="keyword"
                    class="search-input"
                    placeholder="Tìm theo tên hoặc STT..."
                    value="<?= htmlspecialchars($keyword) ?>"
                >

                <button type="submit" class="btn-search">
                    <i class="fa-solid fa-magnifying-glass"></i>
                    Tìm kiếm
                </button>

                <?php if ($keyword !== ''): ?>
                    <a href="index.php?page=users" class="btn-clear">Xóa tìm kiếm</a>
                <?php endif; ?>
            </form>

            <a href="index.php?page=users&action=add" class="btn-create">
                <i class="fa-solid fa-plus"></i> Thêm người dùng
            </a>
        </div>

        <table class="sp-table">
            <thead>
                <tr>
                    <th style="width: 50px; text-align: center;">STT</th>
                    <th>Họ tên</th>
                    <th>Email</th>
                    <th>Số điện thoại</th>
                    <th>Vai trò</th>
                    <th style="text-align: center; width: 100px;">Hành động</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $sql = "SELECT * FROM users ORDER BY id DESC";
                $all_users = $pdo->query($sql)->fetchAll();

                $users = [];
                $display_index = 1;

                // Tìm kiếm theo ký tự/từ khóa trong họ tên và theo STT đang hiển thị
                if ($keyword !== '') {
                    $keyword = preg_replace('/\s+/', ' ', $keyword);
                    $keywords = explode(' ', $keyword);

                    foreach ($all_users as $user_item) {
                        $fullname_lower = mb_strtolower($user_item['fullname'], 'UTF-8');
                        $stt_text = (string)$display_index;

                        $matched_all_keywords = true;

                        foreach ($keywords as $kw) {
                            $kw = trim($kw);

                            if ($kw === '') {
                                continue;
                            }

                            $kw_lower = mb_strtolower($kw, 'UTF-8');

                            // Nếu từ khóa là số: tìm theo STT hoặc ký tự trong tên
                            if (is_numeric($kw)) {
                                if (
                                    strpos($stt_text, $kw) === false &&
                                    mb_strpos($fullname_lower, $kw_lower, 0, 'UTF-8') === false
                                ) {
                                    $matched_all_keywords = false;
                                    break;
                                }
                            } else {
                                // Nếu từ khóa là chữ: lọc dần theo ký tự/từ khóa trong họ tên
                                if (mb_strpos($fullname_lower, $kw_lower, 0, 'UTF-8') === false) {
                                    $matched_all_keywords = false;
                                    break;
                                }
                            }
                        }

                        if ($matched_all_keywords) {
                            $user_item['display_stt'] = $display_index;
                            $users[] = $user_item;
                        }

                        $display_index++;
                    }
                } else {
                    foreach ($all_users as $user_item) {
                        $user_item['display_stt'] = $display_index;
                        $users[] = $user_item;
                        $display_index++;
                    }
                }

                if(count($users) > 0):
                    foreach ($users as $row):
                ?>
                <tr>
                    <td style="text-align: center; font-weight: bold; color: #777;"><?= htmlspecialchars($row['display_stt']) ?></td>
                    <td style="font-weight: 500; color: #2c3e50;"><i class="fa-regular fa-circle-user" style="color:#ccc; margin-right:5px;"></i> <?= htmlspecialchars($row['fullname']) ?></td>
                    <td><?= htmlspecialchars($row['email']) ?></td>
                    <td><?= htmlspecialchars($row['phone']) ?></td>
                    <td>
                        <?php if($row['role'] == 'admin'): ?>
                            <span style="background: #ffebeb; color: #dc3545; padding: 4px 8px; border-radius: 4px; font-size: 12px; font-weight: bold;">Quản trị viên</span>
                        <?php else: ?>
                            <span style="background: #eaffea; color: #28a745; padding: 4px 8px; border-radius: 4px; font-size: 12px; font-weight: bold;">Khách hàng</span>
                        <?php endif; ?>
                    </td>
                    <td style="text-align: center;">
                        <a href="index.php?page=users&action=edit&id=<?= $row['id'] ?>" class="btn-action btn-edit"><i class="fa-solid fa-pencil"></i></a>
                        <a href="index.php?page=users&action=delete&id=<?= $row['id'] ?>" class="btn-action btn-delete" onclick="return confirm('Cảnh báo: Bạn có chắc chắn muốn xóa tài khoản này?');"><i class="fa-solid fa-trash"></i></a>
                    </td>
                </tr>
                <?php
                    endforeach;
                else:
                ?>
                    <tr><td colspan="6" style="text-align: center; padding: 30px; color: #777;">Không tìm thấy người dùng phù hợp.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
<?php endif; ?>