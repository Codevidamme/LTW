<?php
// admin/reviews.php
if (!isset($pdo)) die('Truy cập bị từ chối!');

// TỰ ĐỘNG THÊM CỘT NẾU CHƯA CÓ (Tự chữa lỗi)
try {
    $stmt = $pdo->query("SHOW COLUMNS FROM orders LIKE 'shipping_rating'");
    if ($stmt->rowCount() == 0) {
        $pdo->exec("ALTER TABLE orders ADD COLUMN shipping_rating INT NULL, ADD COLUMN shipping_feedback TEXT NULL");
    }
} catch (\PDOException $e) {}

// =========================================================================
// 1. THỐNG KÊ TỔNG QUAN PHẢN HỒI
// =========================================================================
$total_reviews = $pdo->query("SELECT COUNT(*) FROM orders WHERE shipping_rating IS NOT NULL")->fetchColumn();
$avg_rating = $pdo->query("SELECT AVG(shipping_rating) FROM orders WHERE shipping_rating IS NOT NULL")->fetchColumn();
$avg_rating = number_format($avg_rating, 1);

// Đếm số lượng theo từng mức sao
$star_counts = [];
for ($i = 5; $i >= 1; $i--) {
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM orders WHERE shipping_rating = ?");
    $stmt->execute([$i]);
    $star_counts[$i] = $stmt->fetchColumn();
}

// ... (phần còn lại của code vẫn giữ nguyên như cũ)

// =========================================================================
// 2. XỬ LÝ BỘ LỌC
// =========================================================================
$filter_star = isset($_GET['star']) ? (int)$_GET['star'] : 0;
$where = "shipping_rating IS NOT NULL";
$params = [];

if ($filter_star > 0) {
    $where .= " AND shipping_rating = ?";
    $params[] = $filter_star;
}

// Lấy danh sách đánh giá (Join với bảng users để lấy thêm thông tin nếu cần)
$sql = "SELECT id, customer_name, shipping_rating, shipping_feedback, created_at, total_price 
        FROM orders 
        WHERE $where 
        ORDER BY created_at DESC";
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$reviews = $stmt->fetchAll();
?>

<style>
    :root {
        --bg-dark: #0F172A;
        --card-bg: #1E293B;
        --accent-indigo: #6366F1;
        --text-gray: #CBD5E1;
        --star-yellow: #F59E0B;
    }

    /* Thống kê dạng Card */
    .review-stats-grid { display: grid; grid-template-columns: 1fr 2fr; gap: 20px; margin-bottom: 30px; }
    
    .rating-summary { 
        background: var(--card-bg); padding: 30px; border-radius: 12px; 
        text-align: center; border: 1px solid rgba(99, 102, 241, 0.2);
    }
    .rating-summary h1 { font-size: 64px; color: var(--accent-indigo); margin: 0; }
    .rating-summary .stars { color: var(--star-yellow); font-size: 24px; margin: 10px 0; }

    .star-bars { background: var(--card-bg); padding: 25px; border-radius: 12px; border: 1px solid rgba(99, 102, 241, 0.1); }
    .star-row { display: flex; align-items: center; gap: 15px; margin-bottom: 10px; color: var(--text-gray); font-size: 14px; }
    .progress-bar { flex-grow: 1; height: 8px; background: #334155; border-radius: 4px; overflow: hidden; }
    .progress-fill { height: 100%; background: var(--accent-indigo); }

    /* Bộ lọc */
    .filter-tabs { display: flex; gap: 10px; margin-bottom: 20px; flex-wrap: wrap; }
    .filter-tab { 
        padding: 8px 20px; border-radius: 20px; background: var(--card-bg); 
        color: var(--text-gray); text-decoration: none; border: 1px solid #334155;
        font-size: 14px; transition: 0.3s;
    }
    .filter-tab.active, .filter-tab:hover { background: var(--accent-indigo); color: white; border-color: var(--accent-indigo); }

    /* Danh sách đánh giá */
    .review-list { display: flex; flex-direction: column; gap: 15px; }
    .review-item { 
        background: var(--card-bg); padding: 20px; border-radius: 12px; 
        border: 1px solid rgba(255, 255, 255, 0.05); position: relative;
    }
    .review-item .header { display: flex; justify-content: space-between; margin-bottom: 10px; }
    .review-item .cust-name { font-weight: 600; color: white; font-size: 16px; }
    .review-item .date { color: #64748b; font-size: 12px; }
    .review-item .stars { color: var(--star-yellow); font-size: 14px; margin-bottom: 10px; }
    .review-item .comment { color: var(--text-gray); line-height: 1.6; font-style: italic; }
    .review-item .order-ref { 
        margin-top: 15px; padding-top: 10px; border-top: 1px solid #334155;
        font-size: 12px; color: #64748b; display: flex; justify-content: space-between;
    }

    .badge-order { color: var(--accent-indigo); text-decoration: none; font-weight: bold; }
</style>

<div style="margin-bottom: 25px;">
    <h2 style="color: white; font-size: 24px;"><i class="fa-solid fa-comments" style="color: var(--accent-indigo);"></i> Phản hồi từ người mua</h2>
    <p style="color: var(--text-gray); font-size: 14px;">Quản lý và theo dõi mức độ hài lòng của khách hàng đối với dịch vụ vận chuyển.</p>
</div>

<div class="review-stats-grid">
    <div class="rating-summary">
        <p style="color: var(--text-gray); text-transform: uppercase; font-size: 12px; letter-spacing: 1px;">Đánh giá trung bình</p>
        <h1><?= $avg_rating ?></h1>
        <div class="stars">
            <?php 
            $full_stars = round($avg_rating);
            for($i=1; $i<=5; $i++) echo $i <= $full_stars ? '<i class="fa-solid fa-star"></i>' : '<i class="fa-regular fa-star"></i>';
            ?>
        </div>
        <p style="color: #64748b; font-size: 13px;">(Dựa trên <?= $total_reviews ?> lượt phản hồi)</p>
    </div>

    <div class="star-bars">
        <?php foreach ($star_counts as $star => $count): 
            $percent = $total_reviews > 0 ? ($count / $total_reviews) * 100 : 0;
        ?>
        <div class="star-row">
            <span style="width: 50px;"><?= $star ?> Sao</span>
            <div class="progress-bar">
                <div class="progress-fill" style="width: <?= $percent ?>%"></div>
            </div>
            <span style="width: 40px; text-align: right;"><?= $count ?></span>
        </div>
        <?php endforeach; ?>
    </div>
</div>

<div class="filter-tabs">
    <a href="index.php?page=reviews&star=0" class="filter-tab <?= $filter_star==0?'active':'' ?>">Tất cả</a>
    <?php for($i=5; $i>=1; $i--): ?>
        <a href="index.php?page=reviews&star=<?= $i ?>" class="filter-tab <?= $filter_star==$i?'active':'' ?>">
            <?= $i ?> Sao (<?= $star_counts[$i] ?>)
        </a>
    <?php endfor; ?>
</div>

<div class="review-list">
    <?php if(count($reviews) > 0): ?>
        <?php foreach ($reviews as $rev): ?>
            <div class="review-item">
                <div class="header">
                    <span class="cust-name"><i class="fa-solid fa-circle-user"></i> <?= htmlspecialchars($rev['customer_name'] ?? 'Khách lẻ') ?></span>
                    <span class="date"><?= date('d/m/Y H:i', strtotime($rev['created_at'])) ?></span>
                </div>
                <div class="stars">
                    <?php for($i=1; $i<=5; $i++) echo $i <= $rev['shipping_rating'] ? '<i class="fa-solid fa-star"></i>' : '<i class="fa-regular fa-star"></i>'; ?>
                </div>
                <div class="comment">
                    "<?= htmlspecialchars($rev['shipping_feedback']) ?>"
                </div>
                <div class="order-ref">
                    <span>Đơn hàng: <a href="index.php?page=orders&action=edit&id=<?= $rev['id'] ?>" class="badge-order">#<?= str_pad($rev['id'], 4, '0', STR_PAD_LEFT) ?></a></span>
                    <span>Giá trị đơn: <?= number_format($rev['total_price']) ?>đ</span>
                </div>
            </div>
        <?php endforeach; ?>
    <?php else: ?>
        <div style="text-align: center; padding: 50px; color: #64748b; background: var(--card-bg); border-radius: 12px;">
            <i class="fa-solid fa-comment-slash" style="font-size: 40px; margin-bottom: 15px;"></i>
            <p>Chưa có phản hồi nào phù hợp với bộ lọc này.</p>
        </div>
    <?php endif; ?>
</div>