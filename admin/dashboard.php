<?php
// admin/dashboard.php
if (!isset($pdo)) die('Truy cập bị từ chối!');

// =========================================================================
// 1. TRUY VẤN DỮ LIỆU THỐNG KÊ (Chỉ tính các đơn hàng đã 'completed')
// =========================================================================

// Lấy doanh thu & số đơn của THÁNG NÀY
$sql_curr = "SELECT SUM(total_price) as rev, COUNT(id) as orders FROM orders 
             WHERE status = 'completed' AND MONTH(created_at) = MONTH(CURRENT_DATE()) AND YEAR(created_at) = YEAR(CURRENT_DATE())";
$curr = $pdo->query($sql_curr)->fetch();
$curr_rev = $curr['rev'] ? (float)$curr['rev'] : 0;
$curr_orders = $curr['orders'] ? (int)$curr['orders'] : 0;

// Lấy doanh thu của THÁNG TRƯỚC
$sql_prev = "SELECT SUM(total_price) as rev FROM orders 
             WHERE status = 'completed' AND MONTH(created_at) = MONTH(CURRENT_DATE() - INTERVAL 1 MONTH) AND YEAR(created_at) = YEAR(CURRENT_DATE() - INTERVAL 1 MONTH)";
$prev = $pdo->query($sql_prev)->fetch();
$prev_rev = $prev['rev'] ? (float)$prev['rev'] : 0;

// Tính % Tăng trưởng doanh thu so với tháng trước
$growth_percent = 0;
$trend = 'neutral';
if ($prev_rev > 0) {
    $growth_percent = (($curr_rev - $prev_rev) / $prev_rev) * 100;
} elseif ($curr_rev > 0) {
    $growth_percent = 100; // Nếu tháng trước bằng 0đ và tháng này bắt đầu có doanh thu
}

if ($growth_percent > 0) $trend = 'up';
elseif ($growth_percent < 0) $trend = 'down';

// Lấy dữ liệu doanh thu của 12 THÁNG TRONG NĂM NAY (Phục vụ vẽ biểu đồ)
$chart_data = array_fill(1, 12, 0); 
$sql_year = "SELECT MONTH(created_at) as m, SUM(total_price) as rev FROM orders 
             WHERE status = 'completed' AND YEAR(created_at) = YEAR(CURRENT_DATE()) 
             GROUP BY MONTH(created_at)";
$year_records = $pdo->query($sql_year)->fetchAll();
foreach ($year_records as $r) {
    $chart_data[$r['m']] = (float)$r['rev'];
}
$chart_json = json_encode(array_values($chart_data)); 
?>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<style>
    /* CSS Định dạng bố cục & Thẻ chỉ số chuẩn phong cách hệ thống */
    .breadcrumb { font-size: 13px; color: #777; margin-bottom: 20px; }
    .breadcrumb a { color: #ee4d2d; text-decoration: none; }
    
    .sp-stats-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 20px; margin-bottom: 25px; }
    .sp-stat-card { background: #fff; border-radius: 4px; padding: 25px 20px; box-shadow: 0 1px 4px rgba(0,0,0,0.05); border-left: 4px solid #ee4d2d; display: flex; align-items: center; justify-content: space-between; }
    .sp-stat-info h4 { margin: 0 0 10px 0; color: #999; font-size: 14px; font-weight: 500; text-transform: uppercase; }
    .sp-stat-info h2 { margin: 0; color: #333; font-size: 28px; font-weight: 600; }
    .sp-stat-icon { width: 60px; height: 60px; background: #fff1ee; color: #ee4d2d; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 24px; }
    
    /* Trạng thái biến động phần trăm tăng trưởng */
    .trend-up { color: #2e7d32; font-size: 13px; font-weight: bold; margin-top: 10px; display: inline-block; background: #eaffea; padding: 3px 8px; border-radius: 20px; }
    .trend-down { color: #d32f2f; font-size: 13px; font-weight: bold; margin-top: 10px; display: inline-block; background: #ffebeb; padding: 3px 8px; border-radius: 20px; }
    .trend-neutral { color: #999; font-size: 13px; font-weight: bold; margin-top: 10px; display: inline-block; background: #f5f5f5; padding: 3px 8px; border-radius: 20px; }
    
    /* Khung chứa biểu đồ cột */
    .sp-chart-card { background: #fff; border-radius: 4px; padding: 25px; box-shadow: 0 1px 4px rgba(0,0,0,0.05); }
    .sp-chart-header { margin-bottom: 20px; border-bottom: 1px solid #e5e5e5; padding-bottom: 15px; }
    .sp-chart-header h3 { margin: 0; color: #333; font-size: 18px; font-weight: 500; }
</style>

<div class="breadcrumb">
    <i class="fa-solid fa-house"></i> <a href="index.php">Trang chủ</a> / Thống kê / Tổng quan doanh thu
</div>

<div class="sp-stats-grid">
    <div class="sp-stat-card">
        <div class="sp-stat-info">
            <h4>Doanh thu tháng này</h4>
            <h2>₫<?= number_format($curr_rev, 0, ',', '.') ?></h2>
            
            <?php if($trend == 'up'): ?>
                <span class="trend-up"><i class="fa-solid fa-arrow-trend-up"></i> Tăng <?= number_format($growth_percent, 1) ?>% so với tháng trước</span>
            <?php elseif($trend == 'down'): ?>
                <span class="trend-down"><i class="fa-solid fa-arrow-trend-down"></i> Giảm <?= number_format(abs($growth_percent), 1) ?>% so với tháng trước</span>
            <?php else: ?>
                <span class="trend-neutral"><i class="fa-solid fa-minus"></i> Không có biến động</span>
            <?php endif; ?>
        </div>
        <div class="sp-stat-icon"><i class="fa-solid fa-sack-dollar"></i></div>
    </div>

    <div class="sp-stat-card" style="border-left-color: #2e7d32;">
        <div class="sp-stat-info">
            <h4>Đơn hoàn thành (Tháng này)</h4>
            <h2><?= number_format($curr_orders) ?> Đơn</h2>
            <span class="trend-neutral" style="background: transparent; padding: 0; color: #2e7d32;">Giao hàng thành công</span>
        </div>
        <div class="sp-stat-icon" style="background: #eaffea; color: #2e7d32;"><i class="fa-solid fa-box-open"></i></div>
    </div>

    <div class="sp-stat-card" style="border-left-color: #007bff;">
        <div class="sp-stat-info">
            <h4>Doanh thu tháng trước</h4>
            <h2 style="color: #007bff;">₫<?= number_format($prev_rev, 0, ',', '.') ?></h2>
            <span class="trend-neutral" style="background: transparent; padding: 0; color: #007bff;">Dữ liệu đối soát hệ thống</span>
        </div>
        <div class="sp-stat-icon" style="background: #e7f3ff; color: #007bff;"><i class="fa-solid fa-clock-rotate-left"></i></div>
    </div>
</div>

<div class="sp-chart-card">
    <div class="sp-chart-header">
        <h3><i class="fa-solid fa-chart-column" style="color: #ee4d2d;"></i> Thống kê hiệu suất doanh thu năm <?= date('Y') ?></h3>
    </div>
    <div style="height: 400px; width: 100%;">
        <canvas id="revenueChart"></canvas>
    </div>
</div>

<script>
    const ctx = document.getElementById('revenueChart').getContext('2d');
    const revenueData = <?= $chart_json ?>;
    
    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: ['Tháng 1', 'Tháng 2', 'Tháng 3', 'Tháng 4', 'Tháng 5', 'Tháng 6', 'Tháng 7', 'Tháng 8', 'Tháng 9', 'Tháng 10', 'Tháng 11', 'Tháng 12'],
            datasets: [{
                label: 'Doanh thu thực tế (VNĐ)',
                data: revenueData,
                backgroundColor: '#ee4d2d', // Tông cam đỏ chủ đạo
                borderRadius: 4,
                barPercentage: 0.5
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            let value = context.raw;
                            return ' ' + new Intl.NumberFormat('vi-VN', { style: 'currency', currency: 'VND' }).format(value);
                        }
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: function(value) {
                            if (value >= 1000000) return (value / 1000000) + ' Tr';
                            if (value >= 1000) return (value / 1000) + ' K';
                            return value;
                        }
                    }
                }
            }
        }
    });
</script>