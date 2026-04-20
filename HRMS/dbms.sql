-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Apr 14, 2026 at 09:27 PM
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
-- Database: `dbms`
--

-- --------------------------------------------------------

--
-- Table structure for table `admins`
--

CREATE TABLE `admins` (
  `id` int(11) NOT NULL,
  `full_name` varchar(100) NOT NULL DEFAULT 'RIPO Admin',
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `aPhoto` varchar(255) DEFAULT 'default.png',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admins`
--

INSERT INTO `admins` (`id`, `full_name`, `email`, `password`, `aPhoto`, `created_at`) VALUES
(1, 'Multi-Service & Multi-Fixing Admin', 'mail-adminmultiservicefixing@service.com', '1234', 'default.png', '2026-04-10 18:57:56'),
(2, 'Multi-Service & Multi-Fixing Admin', 'adminmultiservicefixing@service.com', '1234', 'adm_1775848555_69d94c6bba68b.jpg', '2026-04-10 19:14:57');

-- --------------------------------------------------------

--
-- Table structure for table `cart`
--

CREATE TABLE `cart` (
  `id` int(11) NOT NULL,
  `customer_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `customers`
--

CREATE TABLE `customers` (
  `email` varchar(100) NOT NULL,
  `id` int(11) NOT NULL,
  `full_name` varchar(100) NOT NULL,
  `mobile_number` varchar(15) NOT NULL,
  `location` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `cPhoto` varchar(100) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `joined` year(4) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `customers`
--

INSERT INTO `customers` (`email`, `id`, `full_name`, `mobile_number`, `location`, `password`, `cPhoto`, `created_at`, `joined`) VALUES
('firstacc@tutamail.com', 1, 'Kazi', '01632090910', 'Ashuliya', '1234', 'cst_1775846741_69d945557e5b9.jpg', '2026-04-10 17:33:21', '2026'),
('testnew@abc.com', 2, 'TestUser', '0123', 'Test', '123', 'default.png', '2026-04-11 08:52:40', '2026');

-- --------------------------------------------------------

--
-- Table structure for table `feedback`
--

CREATE TABLE `feedback` (
  `id` int(11) NOT NULL,
  `customer_name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `feedback` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `feedback`
--

INSERT INTO `feedback` (`id`, `customer_name`, `email`, `feedback`, `created_at`) VALUES
(1, 'Kazi Nayeem', 'firstacc@tutamail.com', 'Thanks', '2026-04-10 19:21:58');

-- --------------------------------------------------------

--
-- Table structure for table `otp_tokens`
--

CREATE TABLE `otp_tokens` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `role` varchar(20) NOT NULL,
  `otp_code` varchar(6) NOT NULL,
  `expires_at` datetime NOT NULL,
  `used` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `otp_tokens`
--

INSERT INTO `otp_tokens` (`id`, `user_id`, `role`, `otp_code`, `expires_at`, `used`, `created_at`) VALUES
(1, 2, 'provider', '218274', '2026-04-14 20:56:15', 0, '2026-04-14 18:51:15');

-- --------------------------------------------------------

--
-- Table structure for table `product`
--

CREATE TABLE `product` (
  `id` int(11) NOT NULL,
  `product_name` varchar(60) NOT NULL,
  `product_code` varchar(60) NOT NULL,
  `category` varchar(60) NOT NULL,
  `description` varchar(60) NOT NULL,
  `duration` varchar(60) NOT NULL,
  `offer_off` varchar(60) NOT NULL,
  `price` varchar(60) NOT NULL,
  `rating` varchar(60) DEFAULT NULL,
  `provider_email` varchar(100) NOT NULL,
  `provider_name` varchar(30) NOT NULL,
  `image` varchar(255) DEFAULT 'service-1.png'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `product`
--

INSERT INTO `product` (`id`, `product_name`, `product_code`, `category`, `description`, `duration`, `offer_off`, `price`, `rating`, `provider_email`, `provider_name`, `image`) VALUES
(1, 'Laptop Repair', '3501', 'Electronics', 'EC', '2 Hours', '10', '1000', NULL, 'admin@provider.com', 'Kazi', 'srv_1775845502_69d9407e7e769.jpg'),
(2, 'Oven Repair', '3502', 'Electronics', 'EC', '2 Hours', '0', '1200', NULL, 'admin@provider.com', 'Kazi', 'srv_1775845533_69d9409d9ee71.jpg'),
(3, 'Fan Repair', '3503', 'Electronics', 'EC', '2 Hours', '10', '500', NULL, 'admin@provider.com', 'Kazi', 'srv_1775845557_69d940b52116e.webp'),
(4, 'Fridge Repair', '3504', 'Electronics', 'EC', '2 Hours', '10', '1500', NULL, 'admin@provider.com', 'Kazi', 'srv_1775845581_69d940cdbe8fc.jpeg'),
(5, 'AC Repair', '3505', 'Electronics', 'EC', '2 Hours', '10', '2000', NULL, 'admin@provider.com', 'Kazi', 'srv_1775845608_69d940e8aa983.jpg'),
(6, 'TV Repair', '3500', 'Electronics', 'EC', '2 Hours', '10', '500', NULL, 'admin@provider.com', 'Kazi', 'srv_1775845625_69d940f9144d6.jpg'),
(7, 'Washing Machine Repair', '3506', 'Electronics', 'EC', '2 Hours', '10', '2000', NULL, 'admin@provider.com', 'Kazi', 'srv_1775851480_69d957d8aa662.jpeg'),
(8, 'Mobile Repair', '3506', 'Electronics', 'EC', '2 Hours', '10', '1000', NULL, 'admin@provider.com', 'Kazi', 'srv_1775930187_69da8b4bbe79b.jpg'),
(9, 'Plumbing', '3507', 'Electronics', 'EC', '2 Hours', '10', '500', NULL, 'admin@provider.com', 'Kazi', 'srv_1775932712_69da95280df46.jpg'),
(10, 'Carpentry Repair', '3508', 'Wood Work', 'WW', '2 Hours', '10', '500', NULL, 'admin@provider.com', 'Kazi', 'srv_1775932829_69da959d46925.jpg'),
(11, 'Pest Maintenance', '3509', 'Chemical', 'CC', '2 Hours', '10', '1000', NULL, 'admin@provider.com', 'Kazi', 'srv_1775932949_69da9615bf115.webp'),
(12, 'Drill Maintenance', '3510', 'Hardware', 'HW', '2 Hours', '10', '500', NULL, 'admin@provider.com', 'Kazi', 'srv_1775933074_69da969200ff3.webp'),
(13, 'Sewing Machine Maintenance', '3511', 'Hardware', 'HW', '2 Hours', '10', '500', NULL, 'admin@provider.com', 'Kazi', 'srv_1775933200_69da97109bb54.webp'),
(14, 'Geyser Maintenance', '3512', 'Electronics', 'EC', '2 Hours', '10', '1000', NULL, 'admin@provider.com', 'Kazi', 'srv_1775933506_69da9842c22b1.jpg'),
(15, 'Water Filtration Maintenance', '3513', 'Hardware', 'HW', '2 Hours', '10', '500', NULL, 'admin@provider.com', 'Kazi', 'srv_1775933577_69da988922261.webp'),
(16, 'Gas Stove Repair', '3514', 'Hardware', 'HW', '2 Hours', '10', '500', NULL, 'admin@provider.com', 'Kazi', 'srv_1775933663_69da98dfdee90.jpg'),
(17, 'Ceiling Repair', '3515', 'Hardware', 'HW', '2 Hours', '10', '2000', NULL, 'admin@provider.com', 'Kazi', 'srv_1775933827_69da9983eacc0.jpg');

-- --------------------------------------------------------

--
-- Table structure for table `providers`
--

CREATE TABLE `providers` (
  `id` int(11) NOT NULL,
  `full_name` varchar(100) NOT NULL,
  `mobile_number` varchar(15) NOT NULL,
  `location` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `pPhoto` varchar(100) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `email` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `providers`
--

INSERT INTO `providers` (`id`, `full_name`, `mobile_number`, `location`, `password`, `pPhoto`, `created_at`, `email`) VALUES
(1, 'Pro Services', '0123456789', 'Dhaka', '123456', 'default.png', '2026-04-10 17:41:45', 'pro@multiservicefixing.com'),
(2, 'Kazi', '01632090910', 'Ashuliya', '1234', 'prv_1775850366_69d9537e9eca5.jpg', '2026-04-10 17:49:33', 'admin@provider.com');

-- --------------------------------------------------------

--
-- Table structure for table `services`
--

CREATE TABLE `services` (
  `id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `product_name` varchar(100) NOT NULL,
  `provider_name` varchar(100) NOT NULL,
  `provider_email` varchar(100) NOT NULL,
  `customer_name` varchar(100) NOT NULL,
  `customer_email` varchar(100) NOT NULL,
  `customer_location` varchar(255) NOT NULL,
  `customer_phone` varchar(15) NOT NULL,
  `booking_date` date NOT NULL,
  `booking_time` time NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `status` enum('pending','accepted','canceled','completed') DEFAULT 'pending',
  `paid` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `booking_note` varchar(100) NOT NULL,
  `star` int(11) NOT NULL,
  `comment` varchar(100) NOT NULL,
  `payment_method` varchar(20) DEFAULT NULL,
  `payment_ref` varchar(120) DEFAULT NULL,
  `paid_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `services`
--

INSERT INTO `services` (`id`, `product_id`, `product_name`, `provider_name`, `provider_email`, `customer_name`, `customer_email`, `customer_location`, `customer_phone`, `booking_date`, `booking_time`, `price`, `status`, `paid`, `created_at`, `booking_note`, `star`, `comment`, `payment_method`, `payment_ref`, `paid_at`) VALUES
(1, 2, 'TV Repair', 'Pro Services', 'pro@multiservicefixing.com', 'Kazi', 'firstacc@tutamail.com', 'Ashuliya', '01632090910', '2026-04-11', '23:50:00', 650.00, 'pending', 0, '2026-04-10 17:48:07', '', 0, '', NULL, NULL, NULL),
(2, 4, 'TV Repair', 'Kazi', 'admin@provider.com', 'Kazi', 'firstacc@tutamail.com', 'Ashuliya', '01632090910', '2026-04-11', '23:51:00', 500.00, 'accepted', 1, '2026-04-10 17:50:55', '', 5, 'Thanks', NULL, NULL, NULL),
(3, 1, 'Laptop Repair', 'Kazi', 'admin@provider.com', 'Kazi', 'firstacc@tutamail.com', 'Ashuliya', '01632090910', '2026-04-12', '16:05:00', 1000.00, 'accepted', 1, '2026-04-10 20:05:27', '', 5, 'Thanks', NULL, NULL, NULL),
(4, 7, 'Washing Machine Repair', 'Kazi', 'admin@provider.com', 'Kazi', 'firstacc@tutamail.com', 'Ashuliya', '01632090910', '2026-04-12', '06:50:00', 2000.00, 'accepted', 2, '2026-04-11 09:50:55', '', 5, 'Thanks', 'cod', 'COD-0F71CCCE', NULL),
(5, 7, 'Washing Machine Repair', 'Kazi', 'admin@provider.com', 'Kazi', 'firstacc@tutamail.com', 'Ashuliya', '01632090910', '2026-04-21', '18:15:00', 2000.00, 'accepted', 1, '2026-04-11 10:15:39', '', 5, 'Thanks', NULL, NULL, NULL),
(6, 3, 'Fan Repair', 'Kazi', 'admin@provider.com', 'Kazi', 'firstacc@tutamail.com', 'Ashuliya', '01632090910', '2026-04-14', '06:15:00', 500.00, 'accepted', 1, '2026-04-11 10:15:39', '', 5, 'Thanks', 'online', 'ONLINE-499330CE', '2026-04-12 00:36:09'),
(7, 4, 'Fridge Repair', 'Kazi', 'admin@provider.com', 'Kazi', 'firstacc@tutamail.com', 'Ashuliya', '01632090910', '2026-04-13', '15:30:00', 1350.00, 'pending', 0, '2026-04-11 19:28:27', 'Coupon FIX10 applied (10% off).', 0, '', NULL, NULL, NULL),
(8, 3, 'Fan Repair', 'Kazi', 'admin@provider.com', 'Kazi', 'firstacc@tutamail.com', 'Ashuliya', '01632090910', '2026-04-22', '15:30:00', 450.00, 'pending', 0, '2026-04-11 19:28:27', 'Coupon FIX10 applied (10% off).', 0, '', NULL, NULL, NULL),
(9, 16, 'Gas Stove Repair', 'Kazi', 'admin@provider.com', 'Kazi', 'firstacc@tutamail.com', 'Ashuliya', '01632090910', '2026-03-30', '05:21:00', 500.00, 'pending', 0, '2026-04-11 21:19:21', '', 0, '', NULL, NULL, NULL),
(10, 1, 'Laptop Repair', 'Kazi', 'admin@provider.com', 'Kazi', 'firstacc@tutamail.com', 'Ashuliya', '01632090910', '2026-04-01', '07:23:00', 1000.00, 'pending', 0, '2026-04-11 21:19:21', '', 0, '', NULL, NULL, NULL),
(11, 2, 'Oven Repair', 'Kazi', 'admin@provider.com', 'Kazi', 'firstacc@tutamail.com', 'Ashuliya', '01632090910', '2026-04-14', '00:33:00', 1080.00, 'accepted', 1, '2026-04-13 16:32:19', 'Coupon FIX10 applied (10% off).', 5, 'Thanks', 'online', 'ONLINE-AD9EF434', '2026-04-13 22:33:29'),
(12, 1, 'Laptop Repair', 'Kazi', 'admin@provider.com', 'Kazi', 'firstacc@tutamail.com', 'Ashuliya', '01632090910', '2026-04-15', '00:34:00', 900.00, 'pending', 0, '2026-04-13 16:32:19', 'Coupon FIX10 applied (10% off).', 0, '', NULL, NULL, NULL);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admins`
--
ALTER TABLE `admins`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `cart`
--
ALTER TABLE `cart`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `customers`
--
ALTER TABLE `customers`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `feedback`
--
ALTER TABLE `feedback`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `otp_tokens`
--
ALTER TABLE `otp_tokens`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `product`
--
ALTER TABLE `product`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `providers`
--
ALTER TABLE `providers`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `services`
--
ALTER TABLE `services`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admins`
--
ALTER TABLE `admins`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `cart`
--
ALTER TABLE `cart`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `customers`
--
ALTER TABLE `customers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `feedback`
--
ALTER TABLE `feedback`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `otp_tokens`
--
ALTER TABLE `otp_tokens`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `product`
--
ALTER TABLE `product`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT for table `providers`
--
ALTER TABLE `providers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `services`
--
ALTER TABLE `services`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
