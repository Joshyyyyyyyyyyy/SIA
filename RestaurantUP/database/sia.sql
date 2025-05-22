-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: May 22, 2025 at 02:55 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `sia`
--

-- --------------------------------------------------------

--
-- Table structure for table `menu_items`
--

CREATE TABLE `menu_items` (
  `id` int(11) NOT NULL,
  `name` varchar(100) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `price` decimal(6,2) DEFAULT NULL,
  `category` enum('starters','mains','desserts','drinks') DEFAULT NULL,
  `image_url` varchar(255) DEFAULT NULL,
  `badge` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `menu_items`
--

INSERT INTO `menu_items` (`id`, `name`, `description`, `price`, `category`, `image_url`, `badge`) VALUES
(1, 'Diwata Siken Inasal', 'Unlimited soup, rice, softdrinks', 159.00, 'mains', '../uploads/1746604891_1.jpg', 'New'),
(2, 'Mega Diwata Siken Inasal', '1/2 Chicken, tomato relish & cabbage unlimited rice', 149.00, 'mains', '../uploads/1746668054_meal1.png', 'Popular');

-- --------------------------------------------------------

--
-- Table structure for table `reservations`
--

CREATE TABLE `reservations` (
  `id` int(11) NOT NULL,
  `reservation_code` varchar(20) NOT NULL,
  `customer_name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `phone` varchar(20) NOT NULL,
  `reservation_date` date NOT NULL,
  `time_slot` enum('morning','afternoon','evening') NOT NULL,
  `guests` int(11) NOT NULL,
  `food_package` enum('basic','standard','premium','deluxe') NOT NULL,
  `theme` varchar(50) DEFAULT NULL,
  `special_request` text DEFAULT NULL,
  `total_price` decimal(10,2) NOT NULL,
  `status` enum('pending','approved','cancelled','completed') NOT NULL DEFAULT 'pending',
  `table_id` int(11) DEFAULT NULL,
  `approved_by` int(11) DEFAULT NULL,
  `approved_at` timestamp NULL DEFAULT NULL,
  `table_assigned_by` int(11) DEFAULT NULL,
  `table_assigned_at` timestamp NULL DEFAULT NULL,
  `checkout_by` int(11) DEFAULT NULL,
  `checkout_at` timestamp NULL DEFAULT NULL,
  `cancelled_by` int(11) DEFAULT NULL,
  `cancelled_at` timestamp NULL DEFAULT NULL,
  `cancel_reason` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `reservations`
--

INSERT INTO `reservations` (`id`, `reservation_code`, `customer_name`, `email`, `phone`, `reservation_date`, `time_slot`, `guests`, `food_package`, `theme`, `special_request`, `total_price`, `status`, `table_id`, `approved_by`, `approved_at`, `table_assigned_by`, `table_assigned_at`, `checkout_by`, `checkout_at`, `cancelled_by`, `cancelled_at`, `cancel_reason`, `created_at`, `updated_at`) VALUES
(1, 'DP20250521B72EE8', 'Suruiz Joshua Andrie Rivero', 'suruizjoshuaandrierivero@gmail.com', '09103840798', '2025-05-22', 'afternoon', 8, 'deluxe', 'wedding', '', 18000.00, 'completed', 3, NULL, '2025-05-21 14:51:35', NULL, '2025-05-21 15:00:24', NULL, '2025-05-21 15:35:45', NULL, NULL, NULL, '2025-05-21 14:42:59', '2025-05-21 15:35:45'),
(2, 'DP202505210E688A', 'Joshua Suruiz', 'Staff@gmail.com', '09111222333', '2025-05-22', 'morning', 8, 'deluxe', 'anniversary', '', 13000.00, 'completed', 3, NULL, '2025-05-21 16:48:54', NULL, '2025-05-21 16:50:35', NULL, '2025-05-21 16:59:03', NULL, NULL, NULL, '2025-05-21 16:30:30', '2025-05-21 16:59:03'),
(3, 'DP2025052135A9A0', 'joshua', 'Joshua@gmail.com', '09111222333', '2025-05-22', 'afternoon', 8, 'deluxe', 'birthday', '', 12500.00, 'completed', 3, NULL, '2025-05-21 17:03:00', NULL, '2025-05-21 17:03:07', NULL, '2025-05-21 17:06:30', NULL, NULL, NULL, '2025-05-21 17:02:33', '2025-05-21 17:06:30');

-- --------------------------------------------------------

--
-- Table structure for table `staff`
--

CREATE TABLE `staff` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `email` varchar(100) NOT NULL,
  `role` enum('admin','staff') NOT NULL DEFAULT 'staff',
  `status` enum('active','inactive') NOT NULL DEFAULT 'active',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('admin','staff','customer') NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `password`, `role`, `created_at`) VALUES
(1, 'Admin User', 'Admin@gmail.com', '$2y$10$8KlsQB8aHTJw0MWLFl.YUOqyX9qbHXN9KVgBGD5SXNyUcO5DGxMOi', 'admin', '2025-05-06 08:26:46'),
(2, 'Staff User', 'Staff@gmail.com', '$2y$10$YHZpK.mCgbdl4xyR.M/Cg.D0ysXwjzg.QMmIjbkfMXhLUwKyBQzAa', 'staff', '2025-05-06 08:26:46'),
(3, 'Joshua Suruiz', 'suruizandrie@gmail.com', '$2y$10$09aUwEsdaZCgE0LGZXRzjughw1wmMTauHUAO2QPo.hN/MCselQoyW', 'customer', '2025-05-06 08:40:51'),
(4, 'suruiz', 'suruiz@gmail.com', '$2y$10$WgCCJDfgztEGWY.QkxnO5u1/1eagGFLhhZ488Q2fzSvj/khxnbp7y', 'customer', '2025-05-21 13:07:06'),
(5, 'Suruiz Joshua Andrie Rivero', 'example@gmail.com', '$2y$10$/reLjxjPeNDzvZx5hGMGIelG/1MofYJVXbU3fjFxY8jeEzq/PHb2S', 'staff', '2025-05-21 17:51:29'),
(6, 'Suruiz Joshua Andrie Rivero', 'suruizjoshuaandrierivero@gmail.com', '$2y$10$9PyODw/Dcg80wCmkoxwC3.hMta66mjUO1XRmly3eLSGSKsQ7/2jhG', 'staff', '2025-05-21 18:02:10');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `menu_items`
--
ALTER TABLE `menu_items`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `reservations`
--
ALTER TABLE `reservations`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `reservation_code` (`reservation_code`),
  ADD KEY `approved_by` (`approved_by`),
  ADD KEY `table_assigned_by` (`table_assigned_by`),
  ADD KEY `checkout_by` (`checkout_by`),
  ADD KEY `cancelled_by` (`cancelled_by`);

--
-- Indexes for table `staff`
--
ALTER TABLE `staff`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `menu_items`
--
ALTER TABLE `menu_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `reservations`
--
ALTER TABLE `reservations`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `staff`
--
ALTER TABLE `staff`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `reservations`
--
ALTER TABLE `reservations`
  ADD CONSTRAINT `reservations_ibfk_1` FOREIGN KEY (`approved_by`) REFERENCES `staff` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `reservations_ibfk_2` FOREIGN KEY (`table_assigned_by`) REFERENCES `staff` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `reservations_ibfk_3` FOREIGN KEY (`checkout_by`) REFERENCES `staff` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `reservations_ibfk_4` FOREIGN KEY (`cancelled_by`) REFERENCES `staff` (`id`) ON DELETE SET NULL;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
