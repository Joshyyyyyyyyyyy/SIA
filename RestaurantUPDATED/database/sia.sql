-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: May 08, 2025 at 04:29 AM
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
  `name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `phone` varchar(20) NOT NULL,
  `date` date NOT NULL,
  `time` time NOT NULL,
  `time_period` enum('AM','PM') NOT NULL,
  `guests` varchar(10) NOT NULL,
  `theme` varchar(50) DEFAULT NULL,
  `message` text DEFAULT NULL,
  `status` enum('Pending','Approved','Declined') DEFAULT 'Pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `table_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `reservations`
--

INSERT INTO `reservations` (`id`, `name`, `email`, `phone`, `date`, `time`, `time_period`, `guests`, `theme`, `message`, `status`, `created_at`, `table_id`) VALUES
(4, 'TEST', 'example@gmail.com', '(123) 456-7890', '2025-05-07', '07:00:00', 'AM', '8', '', 'non', '', '2025-05-07 14:24:03', 31),
(5, 'TEST', 'example@gmail.com', '(123) 456-7890', '2025-05-07', '07:00:00', 'AM', '8', '', 'non', 'Approved', '2025-05-07 15:17:16', 4),
(6, 'TEST', 'example@gmail.com', '(123) 456-7890', '2025-05-07', '07:00:00', 'AM', '8', '', 'non', '', '2025-05-07 15:58:49', 9),
(7, 'TEST', 'example@gmail.com', '(123) 456-7890', '2025-05-07', '07:00:00', 'AM', '8', '', 'non', 'Approved', '2025-05-07 23:36:42', 17);

-- --------------------------------------------------------

--
-- Table structure for table `tables`
--

CREATE TABLE `tables` (
  `id` int(11) NOT NULL,
  `table_number` varchar(10) NOT NULL,
  `seats` int(11) NOT NULL,
  `status` enum('available','occupied','reserved') DEFAULT 'available'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tables`
--

INSERT INTO `tables` (`id`, `table_number`, `seats`, `status`) VALUES
(1, '1', 2, 'available'),
(2, '2', 4, 'available'),
(3, '3', 6, 'available'),
(4, '4', 8, 'occupied'),
(5, '5', 10, 'available'),
(6, '6', 2, 'available'),
(7, '7', 4, 'available'),
(8, '8', 6, 'available'),
(9, '9', 8, 'occupied'),
(10, '10', 10, 'available'),
(11, '11', 2, 'available'),
(12, '12', 3, 'available'),
(13, '13', 4, 'available'),
(14, '14', 5, 'available'),
(15, '15', 6, 'available'),
(16, '16', 7, 'available'),
(17, '17', 8, 'occupied'),
(18, '18', 9, 'available'),
(19, '19', 10, 'available'),
(20, '20', 2, 'available');

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
(3, 'Joshua Suruiz', 'suruizandrie@gmail.com', '$2y$10$09aUwEsdaZCgE0LGZXRzjughw1wmMTauHUAO2QPo.hN/MCselQoyW', 'customer', '2025-05-06 08:40:51');

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
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tables`
--
ALTER TABLE `tables`
  ADD PRIMARY KEY (`id`);

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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `tables`
--
ALTER TABLE `tables`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
