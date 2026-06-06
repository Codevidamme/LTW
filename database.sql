-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Máy chủ: 127.0.0.1
-- Thời gian đã tạo: Th6 06, 2026 lúc 06:03 AM
-- Phiên bản máy phục vụ: 10.4.32-MariaDB
-- Phiên bản PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Cơ sở dữ liệu: `database`
--

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `brands`
--

CREATE TABLE `brands` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `logo_image` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `brands`
--

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

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `categories`
--

CREATE TABLE `categories` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `parent_id` int(11) DEFAULT NULL,
  `status` varchar(50) DEFAULT 'Hiển thị'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `categories`
--

INSERT INTO `categories` (`id`, `name`, `parent_id`, `status`) VALUES
(1, 'Đồ chơi lắp ráp', NULL, 'Hiển thị'),
(2, 'Búp bê & Đồ chơi mô phỏng', NULL, 'Hiển thị'),
(3, 'Xe mô hình & Điều khiển', NULL, 'Hiển thị'),
(4, 'Đồ chơi giáo dục & Sáng tạo', NULL, 'Hiển thị'),
(5, 'Đồ chơi vận động ngoài trời', NULL, 'Hiển thị'),
(6, 'LEGO', 1, 'Hiển thị'),
(7, 'Búp bê thời trang', 2, 'Hiển thị'),
(8, 'Siêu xe mô hình', 3, 'Hiển thị'),
(9, 'Đồ chơi học chữ & số', 4, 'Hiển thị'),
(10, 'Board Game & Giải đố', 4, 'Hiển thị');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `orders`
--

CREATE TABLE `orders` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `customer_name` varchar(255) DEFAULT NULL,
  `total_price` decimal(10,2) NOT NULL,
  `status` varchar(50) NOT NULL DEFAULT 'pending',
  `payment_status` varchar(50) DEFAULT 'Chưa thanh toán',
  `source` varchar(50) DEFAULT 'Website',
  `shipping_address` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `shipping_rating` int(11) DEFAULT NULL,
  `shipping_feedback` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `orders`
--

INSERT INTO `orders` (`id`, `user_id`, `customer_name`, `total_price`, `status`, `payment_status`, `source`, `shipping_address`, `created_at`, `shipping_rating`, `shipping_feedback`) VALUES
(1, 2, NULL, 299000.00, 'completed', 'Chưa thanh toán', 'Website', '123 Nguyễn Trãi, Thanh Xuân, Hà Nội', '2026-05-10 01:30:00', NULL, NULL),
(2, 3, NULL, 450000.00, 'completed', 'Chưa thanh toán', 'Website', '456 Lê Lợi, Quận 1, TP Hồ Chí Minh', '2026-05-11 07:15:00', NULL, NULL),
(3, 4, NULL, 199000.00, 'shipped', 'Chưa thanh toán', 'Website', '789 Trần Hưng Đạo, Quy Nhơn, Bình Định', '2026-05-12 02:00:00', NULL, NULL),
(4, 5, NULL, 1298000.00, 'processing', 'Chưa thanh toán', 'Website', '22 Hòa Bình, Ninh Kiều, Cần Thơ', '2026-05-13 04:20:00', NULL, NULL),
(5, 6, NULL, 150000.00, 'pending', 'Chưa thanh toán', 'Website', '102 Quang Trung, Hà Đông, Hà Nội', '2026-05-14 09:45:00', NULL, NULL),
(6, 7, NULL, 585000.00, 'completed', 'Chưa thanh toán', 'Website', '15 Lê Hồng Phong, Hải Phòng', '2026-05-15 03:05:00', NULL, NULL),
(7, 8, NULL, 398000.00, 'cancelled', 'Chưa thanh toán', 'Website', '88 Nguyễn Văn Linh, Đà Nẵng', '2026-05-15 06:00:00', NULL, NULL),
(8, 9, NULL, 480000.00, 'processing', 'Chưa thanh toán', 'Website', '74 Hùng Vương, Nha Trang, Khánh Hòa', '2026-05-16 08:30:00', NULL, NULL),
(9, 10, NULL, 320000.00, 'completed', 'Chưa thanh toán', 'Website', '05 Trần Phú, Vinh, Nghệ An', '2026-05-17 01:00:00', NULL, NULL),
(10, 2, NULL, 798000.00, 'completed', 'Chưa thanh toán', 'Website', '123 Nguyễn Trãi, Thanh Xuân, Hà Nội', '2026-05-17 12:20:00', NULL, NULL),
(11, 3, NULL, 800000.00, 'pending', 'Chưa thanh toán', 'Website', '05 Trần Phú, Vinh, Nghệ An', '2026-05-22 04:48:04', NULL, NULL),
(12, NULL, 'Phạm Lê An', 650000.00, 'processing', 'Chưa thanh toán', 'Website', '123 Nguyễn Trãi, Thanh Xuân, Hà Nội', '2026-05-22 05:01:21', NULL, NULL),
(13, NULL, 'Phạm Lê An', 400000.00, 'completed', 'Chưa thanh toán', 'Website', '123 Nguyễn Trãi, Thanh Xuân, Hà Nội', '2026-05-22 05:02:04', NULL, NULL);

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `order_details`
--

CREATE TABLE `order_details` (
  `id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `product_id` int(11) DEFAULT NULL,
  `quantity` int(11) NOT NULL,
  `price` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `order_details`
--

INSERT INTO `order_details` (`id`, `order_id`, `product_id`, `quantity`, `price`) VALUES
(1, 1, 1, 1, 299000.00),
(2, 2, 2, 1, 450000.00),
(3, 3, 3, 1, 199000.00),
(4, 4, 1, 1, 299000.00),
(5, 4, 6, 1, 799000.00),
(6, 4, 9, 1, 169000.00),
(7, 5, 7, 1, 150000.00),
(8, 6, 8, 1, 585000.00),
(9, 7, 3, 2, 199000.00),
(10, 8, 5, 1, 480000.00),
(11, 9, 10, 1, 320000.00),
(12, 10, 1, 2, 299000.00),
(13, 10, 9, 1, 169000.00);

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `products`
--

CREATE TABLE `products` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `discount_price` decimal(10,2) DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `category_id` int(11) DEFAULT NULL,
  `supplier_id` int(11) DEFAULT NULL,
  `brand_id` int(11) DEFAULT NULL,
  `stock` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `products`
--

INSERT INTO `products` (`id`, `name`, `price`, `discount_price`, `image`, `description`, `category_id`, `supplier_id`, `brand_id`, `stock`) VALUES
(1, 'LEGO City Xe Cảnh Sát Tuần Tra', 399000.00, 299000.00, 'lego_city_police.png', 'Bộ lắp ráp xe cảnh sát năng động dành cho trẻ từ 5 tuổi trở lên.', 6, NULL, 1, 50),
(2, 'Búp Bê Barbie Phong Cách Thời Trang', 450000.00, NULL, 'barbie_fashion.png', 'Búp bê Barbie thế hệ mới với trang phục dạo phố sành điệu.', 7, NULL, 2, 35),
(3, 'Hộp 5 Xe Mô Hình Hot Wheels Cơ Bản', 225000.00, 199000.00, 'hotwheels_5pack.png', 'Set 5 xe mô hình tỷ lệ 1:64 bằng kim loại siêu bền.', 8, NULL, 3, 120),
(4, 'Súng Nước Nerf Super Soaker', 549000.00, 499000.00, 'nerf_water_gun.png', 'Đồ chơi vận động bắn nước áp lực cao an toàn cho bé.', 5, NULL, 5, 25),
(5, 'Mô Hình Lắp Ráp Gundam Aerial HG', 480000.00, NULL, 'gundam_aerial.png', 'Mô hình robot chính hãng Bandai Nhật Bản độ chi tiết cao.', 1, NULL, 6, 15),
(6, 'Xe Điều Khiển Từ Xa Maisto Rock Crawler', 899000.00, 799000.00, 'maisto_crawler.png', 'Xe địa hình điều khiển từ xa sóng 2.4GHz vượt mọi địa hình.', 3, NULL, 7, 10),
(7, 'Bộ Cờ Tỷ Phú Việt Nam Cao Cấp', 150000.00, NULL, 'co_ty_phu.png', 'Trò chơi trí tuệ giải trí dành cho gia đình và nhóm bạn.', 10, NULL, 10, 60),
(8, 'Sách Điện Tử Học Chữ Cái Vtech', 650000.00, 585000.00, 'vtech_book.png', 'Sách phát âm tiếng Anh tương tác thông minh cho trẻ mầm nôm.', 9, NULL, 9, 18),
(9, 'Tháp Cầu Vồng Xếp Chồng Fisher-Price', 199000.00, 169000.00, 'fisher_stack.png', 'Đồ chơi phát triển kỹ năng cầm nắm và nhận biết màu sắc.', 4, NULL, 4, 40),
(10, 'Bộ Lắp Ráp Tàu Chiến Thomas & Friends', 320000.00, 0.00, 'thomas_train.png', 'Mô hình xe lửa chạy pin kết hợp đường ray lượn sóng.', 3, 2, 8, 22);

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `reviews`
--

CREATE TABLE `reviews` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `rating` int(11) NOT NULL DEFAULT 5,
  `comment` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `suppliers`
--

CREATE TABLE `suppliers` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `suppliers`
--

INSERT INTO `suppliers` (`id`, `name`, `phone`, `email`, `address`, `created_at`) VALUES
(1, 'Phạm Lê An', '0826128198', 'quangtran@gmail.com', 'BBBBBB', '2026-05-22 03:38:51'),
(2, 'Bruno Fesnandes', '0344953446', 'phamleanodin1@gmail.com', 'Hà Nội', '2026-05-22 05:17:56'),
(3, 'Cristiano Ronaldo', '0000000000', 'AnPL.B23CE003@stu.ptit.edu.vn', 'fff', '2026-05-22 07:11:19');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `fullname` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `phone` varchar(15) DEFAULT NULL,
  `role` varchar(20) NOT NULL DEFAULT 'customer'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `users`
--

INSERT INTO `users` (`id`, `fullname`, `email`, `password`, `phone`, `role`) VALUES
(1, 'Phạm Lê An', 'admin@mykingdom.club', 'e10adc3949ba59abbe56e057f20f883e', '0912345678', 'admin'),
(2, 'Lê Thùy Linh', 'linhle@gmail.com', 'e10adc3949ba59abbe56e057f20f883e', '0987654321', 'customer'),
(3, 'Trần Minh Quang', 'quangtran@gmail.com', 'e10adc3949ba59abbe56e057f20f883e', '0905111222', 'user'),
(4, 'Phạm Yến Nhi', 'nhipham@gmail.com', 'e10adc3949ba59abbe56e057f20f883e', '0934555666', 'customer'),
(5, 'Hoàng Đình Cường', 'cuonghoang@gmail.com', 'e10adc3949ba59abbe56e057f20f883e', '0977888999', 'customer'),
(6, 'Đặng Thu Thảo', 'thaodang@gmail.com', 'e10adc3949ba59abbe56e057f20f883e', '0866222333', 'customer'),
(7, 'Bùi Tiến Dũng', 'dungbui@gmail.com', 'e10adc3949ba59abbe56e057f20f883e', '0944333555', 'customer'),
(8, 'Vũ Hoàng Yến', 'yenvoo@gmail.com', 'e10adc3949ba59abbe56e057f20f883e', '0911777888', 'customer'),
(9, 'Ngô Quốc Anh', 'quocanh@gmail.com', 'e10adc3949ba59abbe56e057f20f883e', '0922444666', 'customer'),
(10, 'Đỗ Thị Mai', 'maido@gmail.com', '25d55ad283aa400af464c76d713c07ad', '0988000111', 'user');

--
-- Chỉ mục cho các bảng đã đổ
--

--
-- Chỉ mục cho bảng `brands`
--
ALTER TABLE `brands`
  ADD PRIMARY KEY (`id`);

--
-- Chỉ mục cho bảng `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`id`);

--
-- Chỉ mục cho bảng `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_order_user` (`user_id`);

--
-- Chỉ mục cho bảng `order_details`
--
ALTER TABLE `order_details`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_detail_order` (`order_id`),
  ADD KEY `fk_detail_prod` (`product_id`);

--
-- Chỉ mục cho bảng `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_prod_cat` (`category_id`),
  ADD KEY `fk_prod_brand` (`brand_id`);

--
-- Chỉ mục cho bảng `reviews`
--
ALTER TABLE `reviews`
  ADD PRIMARY KEY (`id`);

--
-- Chỉ mục cho bảng `suppliers`
--
ALTER TABLE `suppliers`
  ADD PRIMARY KEY (`id`);

--
-- Chỉ mục cho bảng `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT cho các bảng đã đổ
--

--
-- AUTO_INCREMENT cho bảng `brands`
--
ALTER TABLE `brands`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT cho bảng `categories`
--
ALTER TABLE `categories`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT cho bảng `orders`
--
ALTER TABLE `orders`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT cho bảng `order_details`
--
ALTER TABLE `order_details`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT cho bảng `products`
--
ALTER TABLE `products`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT cho bảng `reviews`
--
ALTER TABLE `reviews`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT cho bảng `suppliers`
--
ALTER TABLE `suppliers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT cho bảng `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- Các ràng buộc cho các bảng đã đổ
--

--
-- Các ràng buộc cho bảng `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `fk_order_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Các ràng buộc cho bảng `order_details`
--
ALTER TABLE `order_details`
  ADD CONSTRAINT `fk_detail_order` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_detail_prod` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE SET NULL;

--
-- Các ràng buộc cho bảng `products`
--
ALTER TABLE `products`
  ADD CONSTRAINT `fk_prod_brand` FOREIGN KEY (`brand_id`) REFERENCES `brands` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_prod_cat` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE SET NULL;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
