-- Sử dụng bảng mã utf8mb4 để hỗ trợ tiếng Việt đầy đủ
SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- =========================================================================
-- 1. BẢNG DANH MỤC (CATEGORIES)
-- =========================================================================
DROP TABLE IF EXISTS `categories`;
CREATE TABLE `categories` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `parent_id` int(11) DEFAULT NULL, -- Phục vụ menu đa cấp (ví dụ: LEGO là con của Đồ chơi lắp ráp)
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `categories` (`id`, `name`, `parent_id`) VALUES
(1, 'Đồ chơi lắp ráp', NULL),
(2, 'Búp bê & Đồ chơi mô phỏng', NULL),
(3, 'Xe mô hình & Điều khiển', NULL),
(4, 'Đồ chơi giáo dục & Sáng tạo', NULL),
(5, 'Đồ chơi vận động ngoài trời', NULL),
(6, 'LEGO', 1),
(7, 'Búp bê thời trang', 2),
(8, 'Siêu xe mô hình', 3),
(9, 'Đồ chơi học chữ & số', 4),
(10, 'Board Game & Giải đố', 4);

-- =========================================================================
-- 2. BẢNG THƯƠNG HIỆU (BRANDS)
-- =========================================================================
DROP TABLE IF EXISTS `brands`;
CREATE TABLE `brands` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `logo_image` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `brands` (`id`, `name`, `logo_image`) VALUES
(1, 'LEGO', 'logo_lego.png'),
(2, 'Barbie', 'logo_barbie.png'),
(3, 'Hot Wheels', 'logo_hotwheels.png'),
(4, 'Fisher-Price', 'logo_fisher_price.png'),
(5, 'Hasbro', 'logo_hasbro.png'),
(6, 'Bandai', 'logo_bandai.png'),
(7, 'Maisto', 'logo_maisto.png'),
(8, 'Takara Tomy', 'logo_takara.png'),
(9, 'Vtech', 'logo_vtech.png'),
(10, 'MyKingdom Choice', 'logo_mykingdom.png');

-- =========================================================================
-- 3. BẢNG SẢN PHẨM (PRODUCTS)
-- =========================================================================
DROP TABLE IF EXISTS `products`;
CREATE TABLE `products` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `discount_price` decimal(10,2) DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `category_id` int(11) DEFAULT NULL,
  `brand_id` int(11) DEFAULT NULL,
  `stock` int(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  CONSTRAINT `fk_prod_cat` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE SET NULL,
  CONSTRAINT `fk_prod_brand` FOREIGN KEY (`brand_id`) REFERENCES `brands` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `products` (`id`, `name`, `price`, `discount_price`, `image`, `description`, `category_id`, `brand_id`, `stock`) VALUES
(1, 'LEGO City Xe Cảnh Sát Tuần Tra', 399000.00, 299000.00, 'lego_city_police.png', 'Bộ lắp ráp xe cảnh sát năng động dành cho trẻ từ 5 tuổi trở lên.', 6, 1, 50),
(2, 'Búp Bê Barbie Phong Cách Thời Trang', 450000.00, NULL, 'barbie_fashion.png', 'Búp bê Barbie thế hệ mới với trang phục dạo phố sành điệu.', 7, 2, 35),
(3, 'Hộp 5 Xe Mô Hình Hot Wheels Cơ Bản', 225000.00, 199000.00, 'hotwheels_5pack.png', 'Set 5 xe mô hình tỷ lệ 1:64 bằng kim loại siêu bền.', 8, 3, 120),
(4, 'Súng Nước Nerf Super Soaker', 549000.00, 499000.00, 'nerf_water_gun.png', 'Đồ chơi vận động bắn nước áp lực cao an toàn cho bé.', 5, 5, 25),
(5, 'Mô Hình Lắp Ráp Gundam Aerial HG', 480000.00, NULL, 'gundam_aerial.png', 'Mô hình robot chính hãng Bandai Nhật Bản độ chi tiết cao.', 1, 6, 15),
(6, 'Xe Điều Khiển Từ Xa Maisto Rock Crawler', 899000.00, 799000.00, 'maisto_crawler.png', 'Xe địa hình điều khiển từ xa sóng 2.4GHz vượt mọi địa hình.', 3, 7, 10),
(7, 'Bộ Cờ Tỷ Phú Việt Nam Cao Cấp', 150000.00, NULL, 'co_ty_phu.png', 'Trò chơi trí tuệ giải trí dành cho gia đình và nhóm bạn.', 10, 10, 60),
(8, 'Sách Điện Tử Học Chữ Cái Vtech', 650000.00, 585000.00, 'vtech_book.png', 'Sách phát âm tiếng Anh tương tác thông minh cho trẻ mầm nôm.', 9, 9, 18),
(9, 'Tháp Cầu Vồng Xếp Chồng Fisher-Price', 199000.00, 169000.00, 'fisher_stack.png', 'Đồ chơi phát triển kỹ năng cầm nắm và nhận biết màu sắc.', 4, 4, 40),
(10, 'Bộ Lắp Ráp Tàu Chiến Thomas & Friends', 320000.00, NULL, 'thomas_train.png', 'Mô hình xe lửa chạy pin kết hợp đường ray lượn sóng.', 3, 8, 22);

-- =========================================================================
-- 4. BẢNG NGƯỜI DÙNG (USERS)
-- =========================================================================
DROP TABLE IF EXISTS `users`;
CREATE TABLE `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `fullname` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL UNIQUE,
  `password` varchar(255) NOT NULL, -- Thường lưu chuỗi hash md5 hoặc password_hash bcrpyt
  `phone` varchar(15) DEFAULT NULL,
  `role` varchar(20) NOT NULL DEFAULT 'customer', -- 'admin' hoặc 'customer'
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `users` (`id`, `fullname`, `email`, `password`, `phone`, `role`) VALUES
(1, 'Nguyễn Văn Admin', 'admin@mykingdom.club', 'e10adc3949ba59abbe56e057f20f883e', '0912345678', 'admin'),
(2, 'Lê Thùy Linh', 'linhle@gmail.com', 'e10adc3949ba59abbe56e057f20f883e', '0987654321', 'customer'),
(3, 'Trần Minh Quang', 'quangtran@gmail.com', 'e10adc3949ba59abbe56e057f20f883e', '0905111222', 'customer'),
(4, 'Phạm Yến Nhi', 'nhipham@gmail.com', 'e10adc3949ba59abbe56e057f20f883e', '0934555666', 'customer'),
(5, 'Hoàng Đình Cường', 'cuonghoang@gmail.com', 'e10adc3949ba59abbe56e057f20f883e', '0977888999', 'customer'),
(6, 'Đặng Thu Thảo', 'thaodang@gmail.com', 'e10adc3949ba59abbe56e057f20f883e', '0866222333', 'customer'),
(7, 'Bùi Tiến Dũng', 'dungbui@gmail.com', 'e10adc3949ba59abbe56e057f20f883e', '0944333555', 'customer'),
(8, 'Vũ Hoàng Yến', 'yenvoo@gmail.com', 'e10adc3949ba59abbe56e057f20f883e', '0911777888', 'customer'),
(9, 'Ngô Quốc Anh', 'quocanh@gmail.com', 'e10adc3949ba59abbe56e057f20f883e', '0922444666', 'customer'),
(10, 'Đỗ Thị Mai', 'maido@gmail.com', 'e10adc3949ba59abbe56e057f20f883e', '0988000111', 'customer');

-- =========================================================================
-- 5. BẢNG ĐƠN HÀNG (ORDERS)
-- =========================================================================
DROP TABLE IF EXISTS `orders`;
CREATE TABLE `orders` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) DEFAULT NULL,
  `total_price` decimal(10,2) NOT NULL,
  `status` varchar(50) NOT NULL DEFAULT 'pending', -- pending, processing, shipped, completed, cancelled
  `shipping_address` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  CONSTRAINT `fk_order_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `orders` (`id`, `user_id`, `total_price`, `status`, `shipping_address`, `created_at`) VALUES
(1, 2, 299000.00, 'completed', '123 Nguyễn Trãi, Thanh Xuân, Hà Nội', '2026-05-10 08:30:00'),
(2, 3, 450000.00, 'completed', '456 Lê Lợi, Quận 1, TP Hồ Chí Minh', '2026-05-11 14:15:00'),
(3, 4, 199000.00, 'shipped', '789 Trần Hưng Đạo, Quy Nhơn, Bình Định', '2026-05-12 09:00:00'),
(4, 5, 1298000.00, 'processing', '22 Hòa Bình, Ninh Kiều, Cần Thơ', '2026-05-13 11:20:00'),
(5, 6, 150000.00, 'pending', '102 Quang Trung, Hà Đông, Hà Nội', '2026-05-14 16:45:00'),
(6, 7, 585000.00, 'completed', '15 Lê Hồng Phong, Hải Phòng', '2026-05-15 10:05:00'),
(7, 8, 398000.00, 'cancelled', '88 Nguyễn Văn Linh, Đà Nẵng', '2026-05-15 13:00:00'),
(8, 9, 480000.00, 'processing', '74 Hùng Vương, Nha Trang, Khánh Hòa', '2026-05-16 15:30:00'),
(9, 10, 320000.00, 'pending', '05 Trần Phú, Vinh, Nghệ An', '2026-05-17 08:00:00'),
(10, 2, 798000.00, 'completed', '123 Nguyễn Trãi, Thanh Xuân, Hà Nội', '2026-05-17 19:20:00');

-- =========================================================================
-- 6. BẢNG CHI TIẾT ĐƠN HÀNG (ORDER_DETAILS)
-- =========================================================================
DROP TABLE IF EXISTS `order_details`;
CREATE TABLE `order_details` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `order_id` int(11) NOT NULL,
  `product_id` int(11) DEFAULT NULL,
  `quantity` int(11) NOT NULL,
  `price` decimal(10,2) NOT NULL, -- Giá lúc mua (phòng trường hợp sau này sản phẩm đổi giá)
  PRIMARY KEY (`id`),
  CONSTRAINT `fk_detail_order` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_detail_prod` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `order_details` (`id`, `order_id`, `product_id`, `quantity`, `price`) VALUES
(1, 1, 1, 1, 299000.00), -- Đơn 1 mua 1 xe LEGO City
(2, 2, 2, 1, 450000.00), -- Đơn 2 mua 1 búp bê Barbie
(3, 3, 3, 1, 199000.00), -- Đơn 3 mua 1 set Hot Wheels
(4, 4, 1, 1, 299000.00), -- Đơn 4 mua lẻ 1 LEGO City...
(5, 4, 6, 1, 799000.00), -- ...và mua thêm 1 xe điều khiển Maisto
(6, 4, 9, 1, 169000.00), -- ...và mua thêm 1 tháp xếp chồng
(7, 5, 7, 1, 150000.00), -- Đơn 5 mua 1 cờ tỷ phú
(8, 6, 8, 1, 585000.00), -- Đơn 6 mua 1 sách điện tử Vtech
(9, 7, 3, 2, 199000.00), -- Đơn 7 mua 2 set Hot Wheels (đã hủy)
(10, 8, 5, 1, 480000.00), -- Đơn 8 mua 1 mô hình Gundam
(11, 9, 10, 1, 320000.00), -- Đơn 9 mua 1 bộ tàu chiến Thomas
(12, 10, 1, 2, 299000.00), -- Đơn 10 mua 2 xe LEGO City...
(13, 10, 9, 1, 169000.00); -- ...và mua thêm 1 tháp xếp chồng

SET FOREIGN_KEY_CHECKS = 1;