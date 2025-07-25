-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jul 25, 2025 at 07:10 AM
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
-- Database: `frozen_foods`
--

-- --------------------------------------------------------

--
-- Table structure for table `admins`
--

CREATE TABLE `admins` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `admin_id` varchar(100) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `password_last_changed` datetime DEFAULT NULL,
  `first_name` varchar(100) DEFAULT NULL,
  `last_name` varchar(100) DEFAULT NULL,
  `phone` varchar(11) NOT NULL,
  `avatar` varchar(255) NOT NULL,
  `permissions` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL CHECK (json_valid(`permissions`)),
  `role` varchar(50) DEFAULT 'Admin',
  `mfa_enabled` tinyint(1) NOT NULL DEFAULT 0,
  `login_notifications` tinyint(1) NOT NULL DEFAULT 0,
  `session_timeout` int(11) NOT NULL DEFAULT 60,
  `system_alerts` tinyint(1) NOT NULL DEFAULT 0,
  `user_activity` tinyint(1) NOT NULL DEFAULT 0,
  `position` varchar(100) NOT NULL DEFAULT 'Product Designer',
  `address` varchar(255) NOT NULL,
  `last_login` timestamp NOT NULL DEFAULT current_timestamp(),
  `active_sessions` int(11) NOT NULL DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admins`
--

INSERT INTO `admins` (`id`, `admin_id`, `email`, `password_hash`, `password_last_changed`, `first_name`, `last_name`, `phone`, `avatar`, `permissions`, `role`, `mfa_enabled`, `login_notifications`, `session_timeout`, `system_alerts`, `user_activity`, `position`, `address`, `last_login`, `active_sessions`, `created_at`, `updated_at`) VALUES
(1, '1001', 'kabriacid01@gmail.com', '$2y$10$ioPYLaGSnop5IJbMnr.JLueN9U4vnX/9WvPG6cB1XKixbsS6o6Coa', NULL, 'Curran', 'Clayton', '09013336679', 'admin_1_1753281972.png', '{   \"can_manage_users\": 1,   \"can_view_reports\": 1,   \"can_configure_system\": 0,   \"can_manage_db\": 0,   \"can_manage_security\": 1,   \"can_manage_api\": 0,   \"can_backup\": 1,   \"can_view_audit\": 1,   \"is_super_admin\": 0 }', 'Soluta corrupti non', 0, 1, 60, 1, 0, 'Product designer', '145 Cowley Parkway', '2025-07-23 17:33:19', 1, '2025-07-19 00:22:45', '2025-07-23 13:23:03');

-- --------------------------------------------------------

--
-- Table structure for table `admin_activity_log`
--

CREATE TABLE `admin_activity_log` (
  `id` int(11) NOT NULL,
  `admin_id` int(11) NOT NULL,
  `action` varchar(64) NOT NULL,
  `details` varchar(255) DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admin_activity_log`
--

INSERT INTO `admin_activity_log` (`id`, `admin_id`, `action`, `details`, `created_at`) VALUES
(1, 1, 'login', 'Logged in from IP 102.89.12.1', '2025-07-23 08:30:00'),
(2, 1, 'update_user', 'Updated user #15', '2025-07-22 16:10:00'),
(3, 1, 'backup', 'Performed system backup', '2025-07-22 14:00:00'),
(4, 1, 'view_report', 'Viewed monthly sales report', '2025-07-21 10:45:00'),
(5, 1, 'logout', 'Logged out', '2025-07-21 09:00:00');

-- --------------------------------------------------------

--
-- Table structure for table `card_details`
--

CREATE TABLE `card_details` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `card_name` varchar(100) NOT NULL,
  `card_number` varchar(24) NOT NULL,
  `card_expiry` varchar(7) NOT NULL,
  `card_cvc` varchar(4) DEFAULT NULL,
  `billing_address_same` tinyint(1) DEFAULT 1,
  `payment_method` varchar(32) NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `currency` varchar(8) NOT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `card_details`
--

INSERT INTO `card_details` (`id`, `user_id`, `card_name`, `card_number`, `card_expiry`, `card_cvc`, `billing_address_same`, `payment_method`, `amount`, `currency`, `created_at`) VALUES
(1, 1, 'Axel Wells', '8590798767876567', '09/03', NULL, 1, 'mastercard', 38808.00, 'NGN', '2025-07-25 02:30:52'),
(2, 1, 'Axel Wells', '8590798767876567', '09/03', NULL, 1, 'mastercard', 38808.00, 'NGN', '2025-07-25 02:31:05'),
(3, 1, 'William Patterson', '3224567898765434', '09/76', NULL, 1, 'mastercard', 38808.00, 'NGN', '2025-07-25 02:33:12'),
(4, 1, 'William Patterson', '3224567898765434', '09/76', NULL, 1, 'mastercard', 38808.00, 'NGN', '2025-07-25 02:35:23'),
(5, 1, 'William Patterson', '3224567898765434', '09/76', NULL, 1, 'mastercard', 38808.00, 'NGN', '2025-07-25 02:38:59');

-- --------------------------------------------------------

--
-- Table structure for table `cart_items`
--

CREATE TABLE `cart_items` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `quantity` int(11) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `categories`
--

CREATE TABLE `categories` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(100) NOT NULL,
  `slug` varchar(100) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `image_url` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `categories`
--

INSERT INTO `categories` (`id`, `name`, `slug`, `description`, `image_url`, `created_at`) VALUES
(1, 'Chicken', 'chicken', 'Frozen chicken varieties.', 'images/categories/chicken.jpg', '2025-07-18 12:07:29'),
(2, 'Fish', 'fish', 'Frozen fish types.', 'images/categories/fish.jpg', '2025-07-18 12:07:29'),
(3, 'Turkey', 'turkey', 'Frozen turkey cuts.', 'images/categories/turkey.jpg', '2025-07-18 12:07:29'),
(4, 'Goat Meat', 'goat-meat', 'Premium goat meat options.', 'images/categories/goat.jpg', '2025-07-18 12:07:29'),
(5, 'Beef', 'beef', 'High-quality beef.', 'images/categories/beef.jpg', '2025-07-18 12:07:29'),
(6, 'Snails', 'snails', 'Cleaned and frozen snails.', 'images/categories/snails.jpg', '2025-07-18 12:07:29'),
(7, 'Prawns', 'prawns', 'Frozen prawns for cooking.', 'images/categories/prawns.jpg', '2025-07-18 12:07:29'),
(8, 'Crabs', 'crabs', 'Frozen crab varieties.', 'images/categories/crabs.jpg', '2025-07-18 12:07:29'),
(9, 'Lamb', 'lamb', 'Tender lamb cuts.', 'images/categories/lamb.jpg', '2025-07-18 12:07:29'),
(10, 'Assorted', 'assorted', 'Assorted frozen mix.', 'images/categories/assorted.jpg', '2025-07-18 12:07:29');

-- --------------------------------------------------------

--
-- Table structure for table `checkouts`
--

CREATE TABLE `checkouts` (
  `id` int(11) NOT NULL,
  `first_name` varchar(100) DEFAULT NULL,
  `last_name` varchar(100) DEFAULT NULL,
  `phone` varchar(30) DEFAULT NULL,
  `email` varchar(150) DEFAULT NULL,
  `city` varchar(100) DEFAULT NULL,
  `address` varchar(255) DEFAULT NULL,
  `postal_code` varchar(20) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `checkouts`
--

INSERT INTO `checkouts` (`id`, `first_name`, `last_name`, `phone`, `email`, `city`, `address`, `postal_code`, `created_at`, `updated_at`) VALUES
(1, 'Marshall', 'Grant', '09078664435', 'giquryceky@gmail.com', 'abuja', '621 East Nobel Extension', 'In aut dolore dolor ', '2025-07-24 15:17:12', '2025-07-25 01:46:24');

-- --------------------------------------------------------

--
-- Table structure for table `contact_messages`
--

CREATE TABLE `contact_messages` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(100) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `subject` varchar(200) DEFAULT NULL,
  `message` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `contact_messages`
--

INSERT INTO `contact_messages` (`id`, `name`, `email`, `subject`, `message`, `created_at`) VALUES
(1, 'John Doe', 'john@example.com', 'Order Help', 'Need help with my recent order.', '2025-07-18 12:07:29'),
(2, 'Aisha Bello', 'aisha@example.com', 'Bulk Order', 'Can I get bulk pricing?', '2025-07-18 12:07:29'),
(3, 'Chuka Obi', 'chuka@example.com', 'Product Inquiry', 'Is the turkey pre-smoked?', '2025-07-18 12:07:29'),
(4, 'Mary Ann', 'mary@example.com', 'Feedback', 'Love your products!', '2025-07-18 12:07:29'),
(5, 'Ahmed Musa', 'ahmed@example.com', 'Delivery Delay', 'My order hasn’t arrived.', '2025-07-18 12:07:29'),
(6, 'Kelechi Ugo', 'kelechi@example.com', 'Coupons', 'Can I use two coupons?', '2025-07-18 12:07:29'),
(7, 'Fatima Sani', 'fatima@example.com', 'Quality Issue', 'One item arrived opened.', '2025-07-18 12:07:29'),
(8, 'Kingsley Joe', 'kingsley@example.com', 'Account Access', 'Lost my password.', '2025-07-18 12:07:29'),
(9, 'Zainab Haruna', 'zainab@example.com', 'New Location', 'Do you deliver to Ilorin?', '2025-07-18 12:07:29'),
(10, 'Victor Eze', 'victor@example.com', 'Refund', 'Requesting refund for last order.', '2025-07-18 12:07:29');

-- --------------------------------------------------------

--
-- Table structure for table `coupons`
--

CREATE TABLE `coupons` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `code` varchar(50) NOT NULL,
  `name` varchar(100) DEFAULT NULL,
  `discount_type` varchar(20) DEFAULT NULL,
  `discount_value` decimal(10,2) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `coupons`
--

INSERT INTO `coupons` (`id`, `code`, `name`, `discount_type`, `discount_value`, `created_at`) VALUES
(1, 'WELCOME10', '10% Welcome Discount', 'percentage', 10.00, '2025-07-18 12:07:29'),
(2, 'FROZENFREE', 'Free Delivery Promo', 'flat', 1000.00, '2025-07-18 12:07:29'),
(3, 'HOTDEAL5', '5% Off Everything', 'percentage', 5.00, '2025-07-18 12:07:29'),
(4, 'TURKEYLOVE', 'N500 Off Turkey', 'flat', 500.00, '2025-07-18 12:07:29'),
(5, 'CHICKENFAN', '7% Off Chicken', 'percentage', 7.00, '2025-07-18 12:07:29'),
(6, 'EIDMEAT', 'Eid Special 15%', 'percentage', 15.00, '2025-07-18 12:07:29'),
(7, 'SNACKTIME', 'Free Delivery Snack Pack', 'flat', 700.00, '2025-07-18 12:07:29'),
(8, 'BIGBUY20', 'Buy Big Save 20%', 'percentage', 20.00, '2025-07-18 12:07:29'),
(9, 'FISHFRIDAY', 'Fish Friday Deal', 'flat', 400.00, '2025-07-18 12:07:29'),
(10, 'APPONLY', 'App Exclusive 12%', 'percentage', 12.00, '2025-07-18 12:07:29');

-- --------------------------------------------------------

--
-- Table structure for table `delivery_zones`
--

CREATE TABLE `delivery_zones` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `zone_name` varchar(100) NOT NULL,
  `state` varchar(100) NOT NULL,
  `city` varchar(100) NOT NULL,
  `delivery_fee` decimal(10,2) DEFAULT 0.00,
  `estimated_delivery_hours` int(11) DEFAULT 24,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `delivery_zones`
--

INSERT INTO `delivery_zones` (`id`, `zone_name`, `state`, `city`, `delivery_fee`, `estimated_delivery_hours`, `created_at`) VALUES
(1, 'Yaba Zone', 'Lagos', 'Yaba', 1000.00, 24, '2025-07-18 12:07:29'),
(2, 'Ikeja Zone', 'Lagos', 'Ikeja', 1200.00, 24, '2025-07-18 12:07:29'),
(3, 'Surulere Zone', 'Lagos', 'Surulere', 1000.00, 24, '2025-07-18 12:07:29'),
(4, 'Wuse Zone', 'FCT', 'Wuse', 1500.00, 36, '2025-07-18 12:07:29'),
(5, 'Gwarinpa Zone', 'FCT', 'Gwarinpa', 1300.00, 36, '2025-07-18 12:07:29'),
(6, 'PH Zone A', 'Rivers', 'Port Harcourt', 1400.00, 48, '2025-07-18 12:07:29'),
(7, 'Aba Town', 'Abia', 'Aba', 1600.00, 48, '2025-07-18 12:07:29'),
(8, 'Enugu City', 'Enugu', 'Enugu', 1700.00, 48, '2025-07-18 12:07:29'),
(9, 'Kano Central', 'Kano', 'Kano', 1800.00, 72, '2025-07-18 12:07:29'),
(10, 'Ibadan North', 'Oyo', 'Ibadan', 1100.00, 24, '2025-07-18 12:07:29');

-- --------------------------------------------------------

--
-- Table structure for table `newsletter_subscriptions`
--

CREATE TABLE `newsletter_subscriptions` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `email` varchar(255) NOT NULL,
  `subscribed_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `newsletter_subscriptions`
--

INSERT INTO `newsletter_subscriptions` (`id`, `email`, `subscribed_at`) VALUES
(1, 'user1@example.com', '2025-07-18 12:07:29'),
(2, 'user2@example.com', '2025-07-18 12:07:29'),
(3, 'user3@example.com', '2025-07-18 12:07:29'),
(4, 'user4@example.com', '2025-07-18 12:07:29'),
(5, 'user5@example.com', '2025-07-18 12:07:29'),
(6, 'user6@example.com', '2025-07-18 12:07:29'),
(7, 'user7@example.com', '2025-07-18 12:07:29'),
(8, 'user8@example.com', '2025-07-18 12:07:29'),
(9, 'user9@example.com', '2025-07-18 12:07:29'),
(10, 'user10@example.com', '2025-07-18 12:07:29');

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `product_id` int(11) NOT NULL,
  `order_number` varchar(50) DEFAULT NULL,
  `user_id` int(11) NOT NULL,
  `delivery_fee` decimal(10,2) DEFAULT 0.00,
  `total_amount` decimal(10,2) NOT NULL,
  `status` enum('processing','pending','delivered','cancelled') DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `orders`
--

INSERT INTO `orders` (`id`, `product_id`, `order_number`, `user_id`, `delivery_fee`, `total_amount`, `status`, `created_at`, `updated_at`) VALUES
(2, 8, 'ORD-1001', 1, 500.00, 5500.00, 'pending', '2024-07-01 09:15:00', '2025-07-19 10:09:52'),
(3, 6, 'ORD-1002', 2, 500.00, 3200.00, 'processing', '2024-07-02 11:30:00', '2025-07-19 10:09:52'),
(4, 8, 'ORD-1003', 1, 500.00, 7800.00, 'delivered', '2024-07-03 08:45:00', '2025-07-19 10:09:52'),
(5, 4, 'ORD-1004', 0, 700.00, 12000.00, 'cancelled', '2024-07-04 13:20:00', '2025-07-19 10:09:52'),
(6, 2, 'ORD-1005', 2, 500.00, 4500.00, 'delivered', '2024-07-05 15:10:00', '2025-07-19 10:09:52'),
(7, 8, 'ORD-1006', 4, 600.00, 9000.00, 'pending', '2024-07-06 10:05:00', '2025-07-19 10:09:52'),
(8, 7, 'ORD-1007', 0, 500.00, 3000.00, 'delivered', '2024-07-07 12:25:00', '2025-07-19 10:09:52'),
(9, 2, 'ORD-1008', 5, 800.00, 15000.00, 'delivered', '2024-07-08 16:40:00', '2025-07-19 10:09:52');

-- --------------------------------------------------------

--
-- Table structure for table `order_items`
--

CREATE TABLE `order_items` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `order_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL,
  `unit_price` decimal(10,2) DEFAULT NULL,
  `total_price` decimal(10,2) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE `products` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `category_id` int(11) NOT NULL,
  `in_stock` tinyint(1) DEFAULT 1,
  `reviews_count` int(10) UNSIGNED DEFAULT 0,
  `features` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`features`)),
  `nutritional_info` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`nutritional_info`)),
  `image` varchar(255) DEFAULT 'default.png',
  `name` varchar(200) NOT NULL,
  `slug` varchar(200) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `price` decimal(10,2) NOT NULL,
  `rating` varchar(100) DEFAULT NULL,
  `stock_quantity` int(11) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `weight` decimal(10,2) DEFAULT NULL,
  `dimensions` varchar(255) DEFAULT NULL,
  `sku` varchar(100) DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT 1,
  `is_featured` tinyint(1) DEFAULT 0,
  `meta_title` varchar(255) DEFAULT NULL,
  `meta_description` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`id`, `category_id`, `in_stock`, `reviews_count`, `features`, `nutritional_info`, `image`, `name`, `slug`, `description`, `price`, `rating`, `stock_quantity`, `created_at`, `updated_at`, `weight`, `dimensions`, `sku`, `is_active`, `is_featured`, `meta_title`, `meta_description`) VALUES
(1, 1, 1, 0, NULL, NULL, 'chicken-1.png', 'Chicken Drumsticks', 'chicken-drumsticks', 'Tasty frozen chicken drumsticks.', 2800.00, '4.3', 50, '2025-07-18 12:07:29', '2025-07-20 01:41:43', NULL, NULL, NULL, 1, 0, NULL, NULL),
(2, 1, 1, 0, NULL, NULL, 'chicken-1.png', 'Chicken Wings', 'chicken-wings', 'Juicy frozen chicken wings.', 3200.00, '4.3', 40, '2025-07-18 12:07:29', '2025-07-20 01:41:43', NULL, NULL, NULL, 1, 0, NULL, NULL),
(3, 2, 1, 0, NULL, NULL, 'chicken-1.png', 'Titus Fish', 'titus-fish', 'Premium frozen Titus fish.', 4000.00, '4.3', 35, '2025-07-18 12:07:29', '2025-07-20 01:41:43', NULL, NULL, NULL, 1, 0, NULL, NULL),
(4, 2, 1, 0, NULL, NULL, 'chicken-1.png', 'Catfish', 'catfish', 'Fresh frozen catfish.', 3500.00, '4.3', 30, '2025-07-18 12:07:29', '2025-07-20 01:41:43', NULL, NULL, NULL, 1, 0, NULL, NULL),
(5, 6, 88, 0, NULL, NULL, 'chicken-1.png', 'White Turkey', 'smoked-turkey', 'Experience the pinnacle of innovation and craftsmanship with this exceptional product that seamlessly blends cutting-edge technology with timeless design principles. Meticulously engineered to exceed your expectations, this remarkable item represents years of research, development, and refinement to deliver an unparalleled user experience.', 38808.00, '4.3', 25, '2025-07-18 12:07:29', '2025-07-23 19:13:22', 69.00, 'Consectetur assumen', 'SK63134', 1, 1, 'Sapiente sunt dicta', 'Praesentium labore p.'),
(6, 4, 1, 0, NULL, NULL, 'chicken-1.png', 'Goat Meat Chops', 'goat-meat-chops', 'Chopped goat meat.', 6000.00, '4.3', 20, '2025-07-18 12:07:29', '2025-07-20 01:41:43', NULL, NULL, NULL, 1, 0, NULL, NULL),
(7, 5, 1, 0, NULL, NULL, 'chicken-1.png', 'Beef Suya Cuts', 'beef-suya-cuts', 'Perfect for suya.', 4500.00, '4.3', 30, '2025-07-18 12:07:29', '2025-07-20 01:41:43', NULL, NULL, NULL, 1, 0, NULL, NULL),
(8, 6, 1, 0, NULL, NULL, 'chicken-1.png', 'Cleaned Snails', 'cleaned-snails', 'Ready to cook.', 8000.00, '4.3', 15, '2025-07-18 12:07:29', '2025-07-20 01:41:43', NULL, NULL, NULL, 1, 0, NULL, NULL),
(9, 7, 1, 0, NULL, NULL, 'chicken-1.png', 'Frozen Prawns', 'frozen-prawns', 'Large size prawns.', 7500.00, '4.3', 18, '2025-07-18 12:07:29', '2025-07-20 01:41:43', NULL, NULL, NULL, 1, 0, NULL, NULL),
(10, 8, 1, 0, NULL, NULL, 'chicken-1.png', 'Crab Legs', 'crab-legs', 'Tasty frozen crab legs.', 8500.00, '4.3', 10, '2025-07-18 12:07:29', '2025-07-20 01:41:43', NULL, NULL, NULL, 1, 0, NULL, NULL),
(14, 4, 1, 30, '[\"Grass Fed\",\"Thick Cut\"]', '{\"calories\":200,\"protein\":\"27g\",\"fat\":\"8g\"}', 'chicken-1.png', 'Beef Steak', NULL, 'Frozen beef steak, ideal for roasting.', 4000.00, NULL, 0, '2025-07-18 14:16:23', '2025-07-20 01:41:43', NULL, NULL, NULL, 1, 0, NULL, NULL),
(16, 1, 1, 20, '[\"Juicy\",\"Easy to Cook\"]', '{\"calories\":130,\"protein\":\"22g\",\"fat\":\"4g\"}', 'chicken-1.png', 'Chicken Drumsticks', NULL, 'Frozen chicken drumsticks, family pack.', 2500.00, NULL, 0, '2025-07-18 14:16:23', '2025-07-20 01:41:43', NULL, NULL, NULL, 1, 0, NULL, NULL),
(17, 2, 1, 10, '[\"Whole Fish\",\"Fresh Taste\"]', '{\"calories\":100,\"protein\":\"19g\",\"fat\":\"2g\"}', 'chicken-1.png', 'Tilapia Whole', NULL, 'Whole frozen tilapia, cleaned and gutted.', 2700.00, NULL, 0, '2025-07-18 14:16:23', '2025-07-20 01:41:43', NULL, NULL, NULL, 1, 0, NULL, NULL),
(18, 4, 1, 22, '[\"Lean Meat\",\"Rich Flavor\"]', '{\"calories\":180,\"protein\":\"26g\",\"fat\":\"6g\"}', 'chicken-1.png', 'Goat Meat', NULL, 'Frozen goat meat, diced for stews.', 4200.00, NULL, 0, '2025-07-18 14:16:23', '2025-07-20 01:41:43', NULL, NULL, NULL, 1, 0, NULL, NULL),
(19, 1, 1, 28, '[\"Spicy\",\"Ready to Cook\"]', '{\"calories\":140,\"protein\":\"21g\",\"fat\":\"5g\"}', 'chicken-1.png', 'Chicken Wings', NULL, 'Frozen chicken wings, spicy marinade.', 3000.00, NULL, 0, '2025-07-18 14:16:23', '2025-07-20 01:41:43', NULL, NULL, NULL, 1, 0, NULL, NULL),
(20, 2, 1, 16, '[\"Large Size\",\"Quick Thaw\"]', '{\"calories\":85,\"protein\":\"17g\",\"fat\":\"1g\"}', 'chicken-1.png', 'Shrimp', NULL, 'Frozen shrimp, large size.', 5200.00, NULL, 0, '2025-07-18 14:16:23', '2025-07-20 01:41:43', NULL, NULL, NULL, 1, 0, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `reviews`
--

CREATE TABLE `reviews` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `rating` int(11) NOT NULL,
  `title` varchar(200) DEFAULT NULL,
  `comment` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `settings`
--

CREATE TABLE `settings` (
  `key` varchar(100) NOT NULL,
  `value` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `settings`
--

INSERT INTO `settings` (`key`, `value`) VALUES
('business_address', '85 First Freeway'),
('business_email', 'vyfi@gmail.com'),
('business_name', 'Sharon Lang'),
('confirm_password', '12'),
('current_password', '12'),
('email_notifications', '1'),
('marketing_updates', '1'),
('new_password', 'Pa$$w0rd!'),
('notify_low_stock', '1'),
('notify_new_order', '1'),
('notify_system_alert', '0'),
('order_notifications', '1'),
('phone_number', '09017697435'),
('Salaya', '$2y$10$2ZZOg.QnrIspfj1BJIgR7u4iqHgczWFvQF35nsy4MCYOk/suSoMf2'),
('stock_alerts', ''),
('timezone', 'Eastern Time (ET)');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `email` varchar(255) NOT NULL,
  `phone` varchar(20) NOT NULL,
  `first_name` varchar(100) NOT NULL,
  `last_name` varchar(100) NOT NULL,
  `avatar` varchar(255) NOT NULL DEFAULT 'default.png',
  `password_hash` varchar(255) NOT NULL,
  `address` varchar(255) DEFAULT NULL,
  `role` enum('loyal','regular') NOT NULL,
  `status` enum('Active','Inactive','Suspended','Pending') NOT NULL DEFAULT 'Active',
  `verified` enum('verified','unverified') NOT NULL DEFAULT 'unverified',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `last_login` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `email`, `phone`, `first_name`, `last_name`, `avatar`, `password_hash`, `address`, `role`, `status`, `verified`, `created_at`, `updated_at`, `last_login`) VALUES
(1, 'user@gmail.com', '09016717078', 'Sierra', 'Herring', 'avatar.jpg', '$2y$10$GHqXxMgKaiol5cToqIHI8.jY6.ikA6ONY1u751pBtXYvw6Wng4L5G', NULL, 'loyal', 'Active', '', '2025-07-18 23:14:20', '2025-07-22 01:23:15', '2025-07-20 05:57:46'),
(2, 'tobyfo@gmail.com', '09085162219', 'Regina', 'Lewis', 'default.png', '$2y$10$.0zlQxdDkyYdGDkuQ5K..ORXstcj1plMSs.Nz6wKr4mzHVQF.nk7e', NULL, 'loyal', 'Active', '', '2025-07-19 09:32:39', '2025-07-22 01:23:15', '2025-07-20 05:57:46'),
(3, 'dybubipa@gmail.com', '09023310738', 'Scott', 'Wiley', 'default.png', '$2y$10$9KFEA9mW6pfk5/jcviP2CuogagA1XffFqW.RO60zurbg6t7815xRO', NULL, 'loyal', 'Active', '', '2025-07-19 09:32:49', '2025-07-22 01:23:15', '2025-07-20 05:57:46'),
(4, 'silyfotyl@gmail.com', '09085560831', 'Alec', 'Hodge', 'default.png', '$2y$10$IrOWX0/ILdNKaAkI10dhgul8N/DfXu4CaLcPYN3WLU3MwBbvpjjU6', NULL, 'loyal', 'Active', '', '2025-07-19 09:32:59', '2025-07-22 01:23:15', '2025-07-20 05:57:46'),
(5, 'gynazifohu@gmail.com', '09058236533', 'Elvis', 'Howe', 'default.png', '$2y$10$WQFQ9hdMAy6eucCPbCMaweOkpn5zaQu8gtswK1BTOlNM2VnTeMBU6', NULL, 'loyal', 'Active', '', '2025-07-19 09:34:46', '2025-07-22 01:23:15', '2025-07-20 05:57:46'),
(6, 'tiniq@gmail.com', '09068494577', 'Mannix', 'Nolan', 'default.png', '$2y$10$bISX7ejq8OGpwWtsnDCQ0u3tUPw1zGAvxXoIYWJNLV60cOUpAP7Bq', NULL, 'loyal', 'Active', '', '2025-07-19 09:41:25', '2025-07-22 01:23:15', '2025-07-20 05:57:46'),
(7, 'fimumiqo@gmail.com', '09080761037', 'Violet', 'Cleveland', 'default.png', '$2y$10$4ouWlLXLLUPt91yT9NxzounEm56m6g2aEh/wEqGIyQebuWSNumEaq', NULL, 'loyal', 'Active', '', '2025-07-19 09:41:35', '2025-07-22 01:23:15', '2025-07-20 05:57:46'),
(8, 'wabycyhe@gmail.com', '09073371332', 'Matthew', 'Garrison', 'default.png', '$2y$10$ApF1MBHm/rjmritcHS8v.O3PLoNNdB6Q1dasdLdHrPpPFUbfb3gdy', NULL, 'loyal', 'Active', '', '2025-07-19 09:41:42', '2025-07-22 01:23:15', '2025-07-20 05:57:46'),
(9, 'gosolug@gmail.com', '09014554927', 'Brenden', 'Finch', 'default.png', '$2y$10$qwjDKVNRCIAzuauJgNokqOjjHGM./YjANT.Rmd8iQOEsSjR9P6zwu', NULL, 'loyal', 'Active', '', '2025-07-19 09:41:49', '2025-07-22 01:23:15', '2025-07-20 05:57:46'),
(10, 'luwypi@gmail.com', '09099331065', 'Cheyenne', 'Koch', 'default.png', '$2y$10$XmgQM2EQi0TjceQ8bpPq2emCt83WaP6gjHH8oVKViAfgZhu99L8r.', NULL, 'loyal', 'Active', '', '2025-07-19 09:42:03', '2025-07-22 01:23:15', '2025-07-20 05:57:46'),
(12, 'fejibilyly@gmail.com', '09035959141', 'Dana', 'Graves', 'default.png', '$2y$10$7/F3nUHtWYcKWqyA787aYuiO6i9pJdEBWUYzjN2XFIvo9C4rJhqvy', NULL, 'loyal', 'Active', 'unverified', '2025-07-22 00:38:32', '2025-07-22 01:23:15', '2025-07-22 00:38:32'),
(13, 'cijapirypu@gmail.com', '09016318851', 'Sade', 'Vang', 'default.png', '$2y$10$c7hauqsN5tF0A/570CGg0uO642UHZquk33r61bwhfUwYxElAUK/tO', NULL, 'loyal', 'Active', 'unverified', '2025-07-22 00:39:15', '2025-07-22 01:23:15', '2025-07-22 00:39:15'),
(14, 'zosupygyb@gmail.com', '09073769723', 'Claire', 'Suarez', 'default.png', '$2y$10$QB1sFbHSxKLl5LVCYDGzBOpi/sYLvDzcL1kgXeBSmVBexLkKotP8m', NULL, 'loyal', 'Active', 'unverified', '2025-07-22 00:47:29', '2025-07-22 01:23:15', '2025-07-22 00:47:29'),
(17, 'ali@gmail.com', '0904565433', 'Ali', 'Ali', 'default.png', '', NULL, '', 'Active', 'unverified', '2025-07-23 13:59:53', '2025-07-23 13:59:53', '2025-07-23 13:59:53');

-- --------------------------------------------------------

--
-- Table structure for table `user_addresses`
--

CREATE TABLE `user_addresses` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` int(11) NOT NULL,
  `street_address` text NOT NULL,
  `city` varchar(100) NOT NULL,
  `state` varchar(100) NOT NULL,
  `postal_code` varchar(20) DEFAULT NULL,
  `landmark` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `wallets`
--

CREATE TABLE `wallets` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `balance` decimal(10,2) NOT NULL,
  `email` varchar(255) NOT NULL,
  `phone` varchar(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `wallets`
--

INSERT INTO `wallets` (`id`, `user_id`, `balance`, `email`, `phone`, `created_at`, `updated_at`) VALUES
(1, 1, 34.00, '', '', '2025-07-22 00:35:58', '2025-07-22 00:35:58');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admins`
--
ALTER TABLE `admins`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `admin_id` (`admin_id`);

--
-- Indexes for table `admin_activity_log`
--
ALTER TABLE `admin_activity_log`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `card_details`
--
ALTER TABLE `card_details`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `cart_items`
--
ALTER TABLE `cart_items`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `checkouts`
--
ALTER TABLE `checkouts`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `contact_messages`
--
ALTER TABLE `contact_messages`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `coupons`
--
ALTER TABLE `coupons`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `delivery_zones`
--
ALTER TABLE `delivery_zones`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `newsletter_subscriptions`
--
ALTER TABLE `newsletter_subscriptions`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `order_number` (`order_number`);

--
-- Indexes for table `order_items`
--
ALTER TABLE `order_items`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `reviews`
--
ALTER TABLE `reviews`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `settings`
--
ALTER TABLE `settings`
  ADD PRIMARY KEY (`key`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `user_addresses`
--
ALTER TABLE `user_addresses`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `wallets`
--
ALTER TABLE `wallets`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admins`
--
ALTER TABLE `admins`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `admin_activity_log`
--
ALTER TABLE `admin_activity_log`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `card_details`
--
ALTER TABLE `card_details`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `cart_items`
--
ALTER TABLE `cart_items`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `categories`
--
ALTER TABLE `categories`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `checkouts`
--
ALTER TABLE `checkouts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `contact_messages`
--
ALTER TABLE `contact_messages`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `coupons`
--
ALTER TABLE `coupons`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `delivery_zones`
--
ALTER TABLE `delivery_zones`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `newsletter_subscriptions`
--
ALTER TABLE `newsletter_subscriptions`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `order_items`
--
ALTER TABLE `order_items`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;

--
-- AUTO_INCREMENT for table `reviews`
--
ALTER TABLE `reviews`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT for table `user_addresses`
--
ALTER TABLE `user_addresses`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `wallets`
--
ALTER TABLE `wallets`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
