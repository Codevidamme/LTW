-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Máy chủ: 127.0.0.1
-- Thời gian đã tạo: Th6 05, 2026 lúc 08:33 PM
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
-- Cơ sở dữ liệu: `final_ltweb`
--

DELIMITER $$
--
-- Thủ tục
--
CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_add_supplier` (IN `p_name` VARCHAR(100), IN `p_email` VARCHAR(100), IN `p_phone` VARCHAR(20), IN `p_address` TEXT, IN `p_status` VARCHAR(20), OUT `p_result_id` INT, OUT `p_message` VARCHAR(255))   BEGIN
    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        SET p_result_id = -1;
        SET p_message = 'Lỗi: Email đã tồn tại hoặc dữ liệu không hợp lệ';
    END;
    
    IF p_name IS NULL OR p_name = '' THEN
        SET p_result_id = -1;
        SET p_message = 'Tên nhà cung cấp không được để trống';
    ELSEIF p_email IS NULL OR p_email = '' THEN
        SET p_result_id = -1;
        SET p_message = 'Email không được để trống';
    ELSE
        INSERT INTO suppliers (supplier_name, contact_email, phone_number, address, status)
        VALUES (p_name, p_email, p_phone, p_address, COALESCE(p_status, 'Active'));
        
        SET p_result_id = LAST_INSERT_ID();
        SET p_message = 'Thêm nhà cung cấp thành công';
    END IF;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_check_email_exists` (IN `p_email` VARCHAR(100), IN `p_exclude_id` INT, OUT `p_exists` BOOLEAN)   BEGIN
    SELECT COUNT(*) INTO @count
    FROM suppliers
    WHERE contact_email = p_email
    AND (p_exclude_id IS NULL OR supplier_id != p_exclude_id);
    
    SET p_exists = (@count > 0);
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_delete_supplier` (IN `p_id` INT, OUT `p_success` BOOLEAN, OUT `p_message` VARCHAR(255))   BEGIN
    IF NOT EXISTS (SELECT 1 FROM suppliers WHERE supplier_id = p_id) THEN
        SET p_success = FALSE;
        SET p_message = 'Nhà cung cấp không tồn tại';
    ELSE
        DELETE FROM suppliers WHERE supplier_id = p_id;
        SET p_success = TRUE;
        SET p_message = 'Xóa nhà cung cấp thành công';
    END IF;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_get_all_suppliers` ()   BEGIN
    SELECT 
        s.supplier_id, s.supplier_name, s.contact_email,
        s.phone_number, s.address, s.status, s.created_at,
        COUNT(po.order_id) AS total_orders,
        SUM(CASE WHEN po.order_status = 'Completed' THEN 1 ELSE 0 END) AS success_orders
    FROM suppliers s
    LEFT JOIN purchase_orders po ON s.supplier_id = po.supplier_id
    GROUP BY s.supplier_id
    ORDER BY s.created_at DESC;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_get_supplier_by_id` (IN `p_id` INT)   BEGIN
    SELECT supplier_id, supplier_name, contact_email, phone_number, address, status, created_at
    FROM suppliers
    WHERE supplier_id = p_id;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_update_supplier` (IN `p_id` INT, IN `p_name` VARCHAR(100), IN `p_email` VARCHAR(100), IN `p_phone` VARCHAR(20), IN `p_address` TEXT, IN `p_status` VARCHAR(20), OUT `p_success` BOOLEAN, OUT `p_message` VARCHAR(255))   BEGIN
    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        SET p_success = FALSE;
        SET p_message = 'Lỗi: Email đã tồn tại hoặc dữ liệu không hợp lệ';
    END;
    
    IF NOT EXISTS (SELECT 1 FROM suppliers WHERE supplier_id = p_id) THEN
        SET p_success = FALSE;
        SET p_message = 'Nhà cung cấp không tồn tại';
    ELSE
        UPDATE suppliers
        SET supplier_name = p_name,
            contact_email = p_email,
            phone_number = p_phone,
            address = p_address,
            status = COALESCE(p_status, 'Active')
        WHERE supplier_id = p_id;
        
        SET p_success = TRUE;
        SET p_message = 'Cập nhật nhà cung cấp thành công';
    END IF;
END$$

DELIMITER ;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `materials`
--

CREATE TABLE `materials` (
  `material_id` int(11) NOT NULL,
  `material_name` varchar(100) NOT NULL,
  `unit` varchar(20) NOT NULL,
  `description` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `materials`
--

INSERT INTO `materials` (`material_id`, `material_name`, `unit`, `description`, `created_at`) VALUES
(1, 'Xi măng', 'Tấn', 'Xi măng Portland PC40', '2026-05-20 09:53:45'),
(2, 'Sắt thép xây dựng', 'Tấn', 'Thép cây phi 10-25', '2026-05-20 09:53:45'),
(3, 'Cát xây dựng', 'M3', 'Cát vàng loại 1', '2026-05-20 09:53:45'),
(4, 'Gạch ốp lát', 'Thùng', 'Gạch ceramic 60x60', '2026-05-20 09:53:45'),
(5, 'Gỗ công nghiệp', 'M3', 'Ván MDF, OSB', '2026-05-20 09:53:45'),
(6, 'Nhôm kính', 'M2', 'Nhôm Việt Pháp', '2026-05-20 09:53:45');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `order_details`
--

CREATE TABLE `order_details` (
  `order_id` int(11) NOT NULL,
  `material_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL,
  `unit_price` decimal(15,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `order_details`
--

INSERT INTO `order_details` (`order_id`, `material_id`, `quantity`, `unit_price`) VALUES
(1, 2, 10, 18500000.00),
(2, 5, 20, 4500000.00),
(3, 4, 90, 320000.00),
(4, 2, 2, 18500000.00),
(5, 5, 10, 4500000.00),
(6, 3, 60, 260000.00),
(7, 2, 4, 18500000.00);

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `purchase_orders`
--

CREATE TABLE `purchase_orders` (
  `order_id` int(11) NOT NULL,
  `supplier_id` int(11) NOT NULL,
  `admin_id` int(11) NOT NULL,
  `order_date` datetime DEFAULT current_timestamp(),
  `expected_date` datetime NOT NULL,
  `actual_date` datetime DEFAULT NULL,
  `total_amount` decimal(15,2) DEFAULT 0.00,
  `order_status` enum('Pending','Completed','Cancelled') DEFAULT 'Pending'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `purchase_orders`
--

INSERT INTO `purchase_orders` (`order_id`, `supplier_id`, `admin_id`, `order_date`, `expected_date`, `actual_date`, `total_amount`, `order_status`) VALUES
(1, 2, 1, '2026-04-01 00:00:00', '2026-04-15 00:00:00', '2026-04-14 00:00:00', 185000000.00, 'Completed'),
(2, 1, 1, '2026-04-10 00:00:00', '2026-04-25 00:00:00', '2026-04-30 00:00:00', 90000000.00, 'Completed'),
(3, 3, 1, '2026-04-20 00:00:00', '2026-05-05 00:00:00', '2026-05-05 00:00:00', 28800000.00, 'Completed'),
(4, 2, 1, '2026-05-01 00:00:00', '2026-05-10 00:00:00', '2026-05-09 00:00:00', 37000000.00, 'Completed'),
(5, 1, 1, '2026-05-05 00:00:00', '2026-05-12 00:00:00', NULL, 45000000.00, 'Pending'),
(6, 3, 1, '2026-05-08 00:00:00', '2026-05-10 00:00:00', NULL, 15600000.00, 'Pending'),
(7, 2, 1, '2026-05-10 00:00:00', '2026-05-20 00:00:00', NULL, 74000000.00, 'Pending');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `suppliers`
--

CREATE TABLE `suppliers` (
  `supplier_id` int(11) NOT NULL,
  `supplier_name` varchar(100) NOT NULL,
  `contact_email` varchar(100) DEFAULT NULL,
  `phone_number` varchar(20) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `status` enum('Active','Inactive') DEFAULT 'Active',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `suppliers`
--

INSERT INTO `suppliers` (`supplier_id`, `supplier_name`, `contact_email`, `phone_number`, `address`, `status`, `created_at`) VALUES
(1, 'Công ty Gỗ Nhựa', 'gonhua@gmail.com', '0911223344', 'Hà Nội', 'Active', '2026-05-20 09:53:45'),
(2, 'Công ty Thép Xây Dựng', 'thepxd@gmail.com', '0988776655', 'TP.HCM', 'Active', '2026-05-20 09:53:45'),
(3, 'Công ty TNHH Vật Tư ABC', 'abc@gmail.com', '0977665544', 'Đà Nẵng', 'Active', '2026-05-20 09:53:45'),
(4, 'Tập Đoàn Công Nghệ XYZ', 'xyz@gmail.com', '0966554433', 'Hà Nội', 'Inactive', '2026-05-20 09:53:45');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `supplier_materials`
--

CREATE TABLE `supplier_materials` (
  `supplier_id` int(11) NOT NULL,
  `material_id` int(11) NOT NULL,
  `supply_price` decimal(15,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `supplier_materials`
--

INSERT INTO `supplier_materials` (`supplier_id`, `material_id`, `supply_price`) VALUES
(1, 5, 4500000.00),
(1, 6, 350000.00),
(2, 1, 1850000.00),
(2, 2, 18500000.00),
(2, 3, 280000.00),
(3, 1, 1800000.00),
(3, 3, 260000.00),
(3, 4, 320000.00);

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `users`
--

CREATE TABLE `users` (
  `admin_id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `full_name` varchar(100) DEFAULT NULL,
  `role` enum('Admin','Staff') DEFAULT 'Staff',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `users`
--

INSERT INTO `users` (`admin_id`, `username`, `password`, `full_name`, `role`, `created_at`) VALUES
(1, 'admin', '123456', 'Quản trị viên', 'Admin', '2026-05-20 09:53:45');

--
-- Chỉ mục cho các bảng đã đổ
--

--
-- Chỉ mục cho bảng `materials`
--
ALTER TABLE `materials`
  ADD PRIMARY KEY (`material_id`);

--
-- Chỉ mục cho bảng `order_details`
--
ALTER TABLE `order_details`
  ADD PRIMARY KEY (`order_id`,`material_id`),
  ADD KEY `material_id` (`material_id`);

--
-- Chỉ mục cho bảng `purchase_orders`
--
ALTER TABLE `purchase_orders`
  ADD PRIMARY KEY (`order_id`),
  ADD KEY `supplier_id` (`supplier_id`),
  ADD KEY `admin_id` (`admin_id`);

--
-- Chỉ mục cho bảng `suppliers`
--
ALTER TABLE `suppliers`
  ADD PRIMARY KEY (`supplier_id`),
  ADD UNIQUE KEY `contact_email` (`contact_email`);

--
-- Chỉ mục cho bảng `supplier_materials`
--
ALTER TABLE `supplier_materials`
  ADD PRIMARY KEY (`supplier_id`,`material_id`),
  ADD KEY `material_id` (`material_id`);

--
-- Chỉ mục cho bảng `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`admin_id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- AUTO_INCREMENT cho các bảng đã đổ
--

--
-- AUTO_INCREMENT cho bảng `materials`
--
ALTER TABLE `materials`
  MODIFY `material_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT cho bảng `purchase_orders`
--
ALTER TABLE `purchase_orders`
  MODIFY `order_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT cho bảng `suppliers`
--
ALTER TABLE `suppliers`
  MODIFY `supplier_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT cho bảng `users`
--
ALTER TABLE `users`
  MODIFY `admin_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- Các ràng buộc cho các bảng đã đổ
--

--
-- Các ràng buộc cho bảng `order_details`
--
ALTER TABLE `order_details`
  ADD CONSTRAINT `order_details_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `purchase_orders` (`order_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `order_details_ibfk_2` FOREIGN KEY (`material_id`) REFERENCES `materials` (`material_id`);

--
-- Các ràng buộc cho bảng `purchase_orders`
--
ALTER TABLE `purchase_orders`
  ADD CONSTRAINT `purchase_orders_ibfk_1` FOREIGN KEY (`supplier_id`) REFERENCES `suppliers` (`supplier_id`),
  ADD CONSTRAINT `purchase_orders_ibfk_2` FOREIGN KEY (`admin_id`) REFERENCES `users` (`admin_id`);

--
-- Các ràng buộc cho bảng `supplier_materials`
--
ALTER TABLE `supplier_materials`
  ADD CONSTRAINT `supplier_materials_ibfk_1` FOREIGN KEY (`supplier_id`) REFERENCES `suppliers` (`supplier_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `supplier_materials_ibfk_2` FOREIGN KEY (`material_id`) REFERENCES `materials` (`material_id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
