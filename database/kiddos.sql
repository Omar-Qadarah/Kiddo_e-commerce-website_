-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Oct 29, 2026 at 11:45 PM
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
-- Database: `kiddos`
--

-- --------------------------------------------------------

--
-- Table structure for table `admin_action_usage`
--

CREATE TABLE IF NOT EXISTS `admin_action_usage` (
  `user_id` int(10) UNSIGNED NOT NULL,
  `page` varchar(60) NOT NULL,
  `visits` int(10) UNSIGNED NOT NULL DEFAULT 0,
  PRIMARY KEY (`user_id`,`page`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `age_groups`
--

CREATE TABLE IF NOT EXISTS `age_groups` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(60) NOT NULL,
  `slug` varchar(60) NOT NULL,
  `description` varchar(255) NOT NULL,
  `min_age` tinyint(3) UNSIGNED NOT NULL,
  `max_age` tinyint(3) UNSIGNED NOT NULL,
  `image` varchar(255) DEFAULT NULL,
  `sort_order` tinyint(3) UNSIGNED NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_age_groups_slug` (`slug`)
) ;

--
-- Dumping data for table `age_groups`
--

INSERT INTO `age_groups` (`id`, `name`, `slug`, `description`, `min_age`, `max_age`, `image`, `sort_order`) VALUES
(1, 'Toddlers', 'toddlers', 'Safe toys designed for toddlers exploring textures and colors', 1, 3, 'toddlers.jpg', 1),
(2, 'Little Kids', 'little-kids', 'Engaging toys for growing kids learning core concepts', 4, 7, 'little_kids.jpg', 2),
(3, 'Big Kids & Tweens', 'big-kids-tweens', 'Advanced building sets, electronics, and active sports', 8, 12, 'tweens.jpg', 3);

-- --------------------------------------------------------

--
-- Table structure for table `categories`
--

CREATE TABLE IF NOT EXISTS `categories` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(60) NOT NULL,
  `slug` varchar(60) NOT NULL,
  `description` varchar(255) DEFAULT NULL,
  `icon` varchar(50) DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_categories_name` (`name`),
  UNIQUE KEY `uq_categories_slug` (`slug`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `categories`
--

INSERT INTO `categories` (`id`, `name`, `slug`, `description`, `icon`, `created_at`) VALUES
(1, 'Educational Toys', 'educational-toys', 'Learning and skill-building toys for smart kids', 'fa-graduation-cap', '2026-10-29 10:31:18'),
(2, 'wooden toys', 'wooden-toys', 'qwertyuio', NULL, '2026-10-29 01:52:41'),
(3, 'Girls Toys', 'girls-toys', 'Dolls, dress-up kits, and creative playsets', 'fa-child', '2026-10-29 10:31:18'),
(4, 'Outdoor & Sports', 'outdoor-sports', 'Scooters, bikes, and active outdoor play gear', 'fa-football-ball', '2026-10-29 10:31:18'),
(5, 'Boys Toys', 'boys-toys', 'Action figures, remote control cars, and building sets', 'fa-robot', '2026-10-29 10:31:18');

-- --------------------------------------------------------

--
-- Table structure for table `discounts`
--

CREATE TABLE IF NOT EXISTS `discounts` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `title` varchar(100) NOT NULL,
  `percentage` decimal(5,2) NOT NULL,
  `product_id` int(10) UNSIGNED DEFAULT NULL,
  `category_id` int(10) UNSIGNED DEFAULT NULL,
  `starts_at` date DEFAULT NULL,
  `ends_at` date DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_discounts_product` (`product_id`),
  KEY `idx_discounts_category` (`category_id`)
) ;

--
-- Dumping data for table `discounts`
--

INSERT INTO `discounts` (`id`, `title`, `percentage`, `product_id`, `category_id`, `starts_at`, `ends_at`, `is_active`, `created_at`) VALUES
(1, 'Back to School Sale', 15.00, NULL, 1, '2026-09-01', '2026-09-30', 1, '2026-10-29 10:31:18'),
(2, 'Robot Special Discount', 10.00, 2, NULL, '2026-10-01', '2026-11-01', 1, '2026-10-29 10:31:18');

-- --------------------------------------------------------

--
-- Table structure for table `images`
--

CREATE TABLE IF NOT EXISTS `images` (
  `image_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `product_id` int(10) UNSIGNED NOT NULL,
  `image_path` varchar(255) NOT NULL,
  `alt_text` varchar(255) DEFAULT NULL,
  `is_primary` tinyint(1) NOT NULL DEFAULT 0,
  `sort_order` tinyint(3) UNSIGNED NOT NULL DEFAULT 0,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`image_id`),
  KEY `idx_images_product` (`product_id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `images`
--

INSERT INTO `images` (`image_id`, `product_id`, `image_path`, `alt_text`, `is_primary`, `sort_order`, `created_at`) VALUES
(1, 1, 'uploads/products/alphabet_puzzle_1.jpg', 'Alphabet Puzzle Front View', 1, 0, '2026-10-29 10:31:18'),
(2, 2, 'uploads/products/smart_robot_1.jpg', 'Smart Robot Active Pose', 1, 0, '2026-10-29 10:31:18'),
(3, 3, 'uploads/products/princess_trunk_1.jpg', 'Princess Trunk Outfit Collection', 1, 0, '2026-10-29 10:31:18'),
(4, 4, 'uploads/products/kids_scooter_1.jpg', 'Kids Scooter Red', 1, 0, '2026-10-29 10:31:18');

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE IF NOT EXISTS `orders` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` int(10) UNSIGNED NOT NULL,
  `subtotal` decimal(10,2) NOT NULL,
  `birthday_discount` decimal(10,2) NOT NULL DEFAULT 0.00,
  `delivery_fee` decimal(6,2) NOT NULL DEFAULT 2.00,
  `total` decimal(10,2) NOT NULL,
  `child_birth_date` date DEFAULT NULL,
  `phone` varchar(20) NOT NULL,
  `address_text` varchar(255) NOT NULL,
  `latitude` decimal(10,7) NOT NULL,
  `longitude` decimal(10,7) NOT NULL,
  `status` enum('placed','shipped','delivered') NOT NULL DEFAULT 'placed',
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_orders_user` (`user_id`),
  KEY `idx_orders_status` (`status`),
  KEY `idx_orders_created` (`created_at`)
) ;

--
-- Dumping data for table `orders`
--

INSERT INTO `orders` (`id`, `user_id`, `subtotal`, `birthday_discount`, `delivery_fee`, `total`, `child_birth_date`, `phone`, `address_text`, `latitude`, `longitude`, `status`, `created_at`, `updated_at`) VALUES
(1, 2, 68.98, 0.00, 2.00, 70.98, '2018-04-10', '+962791111111', 'Amman, Jubaiha, Main Street', 31.9539000, 35.9106000, 'placed', '2026-10-29 10:31:18', '2026-10-29 10:31:18');

-- --------------------------------------------------------

--
-- Table structure for table `order_items`
--

CREATE TABLE IF NOT EXISTS `order_items` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `order_id` int(10) UNSIGNED NOT NULL,
  `product_id` int(10) UNSIGNED DEFAULT NULL,
  `product_name` varchar(120) NOT NULL,
  `unit_price` decimal(8,2) NOT NULL,
  `quantity` int(10) UNSIGNED NOT NULL,
  `line_total` decimal(10,2) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_order_items_order` (`order_id`),
  KEY `idx_order_items_product` (`product_id`)
) ;

--
-- Dumping data for table `order_items`
--

INSERT INTO `order_items` (`id`, `order_id`, `product_id`, `product_name`, `unit_price`, `quantity`, `line_total`) VALUES
(1, 1, 1, 'Wooden Alphabet & Number Puzzle Board', 18.99, 1, 18.99),
(2, 1, 3, 'Magical Princess Dress-Up Trunk', 34.99, 1, 34.99);

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE IF NOT EXISTS `products` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `category_id` int(10) UNSIGNED NOT NULL,
  `name` varchar(120) NOT NULL,
  `description` text NOT NULL,
  `instructions` text DEFAULT NULL,
  `price` decimal(8,2) NOT NULL,
  `stock` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `gender` enum('girls','boys','unisex') NOT NULL DEFAULT 'unisex',
  `min_age` tinyint(3) UNSIGNED NOT NULL,
  `max_age` tinyint(3) UNSIGNED NOT NULL,
  `image` varchar(255) DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_products_filters` (`category_id`,`gender`,`min_age`,`max_age`)
) ;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`id`, `category_id`, `name`, `description`, `instructions`, `price`, `stock`, `gender`, `min_age`, `max_age`, `image`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 1, 'Wooden Alphabet & Number Puzzle Board', 'Colorful wooden puzzle pieces helping kids learn letters and numbers early.', 'Store in a dry place. Recommended for supervised play.', 18.99, 60, 'unisex', 2, 5, 'alphabet_puzzle.jpg', 1, '2026-10-29 10:31:18', '2026-10-29 10:31:18'),
(2, 2, 'Smart Voice-Controlled Robot', 'An interactive robot that dances, sings, and responds to voice commands.', 'Requires 3 AA batteries. Do not mix old and new batteries.', 49.99, 20, 'boys', 5, 12, 'smart_robot.jpg', 1, '2026-10-29 10:31:18', '2026-10-29 10:31:18'),
(3, 3, 'Magical Princess Dress-Up Trunk', 'Complete dress-up set including tiaras, necklaces, skirts, and bracelets.', 'Hand wash only. Air dry.', 34.50, 15, 'girls', 3, 7, 'princess_trunk.jpg', 1, '2026-10-29 10:31:18', '2026-10-29 10:31:18'),
(4, 4, '3-Wheel Lean-to-Steer Scooter', 'Stable and safe scooter with flashing LED wheels for toddlers and young children.', 'Always wear a helmet and protective pads while riding.', 59.99, 10, 'unisex', 3, 6, 'kids_scooter.jpg', 1, '2026-10-29 10:31:18', '2026-10-29 10:31:18');

-- --------------------------------------------------------

--
-- Table structure for table `reviews`
--

CREATE TABLE IF NOT EXISTS `reviews` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `product_id` int(10) UNSIGNED NOT NULL,
  `user_id` int(10) UNSIGNED NOT NULL,
  `rating` tinyint(3) UNSIGNED NOT NULL,
  `comment` varchar(500) DEFAULT NULL,
  `is_visible` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_review_once` (`product_id`,`user_id`),
  KEY `idx_reviews_user` (`user_id`)
) ;

--
-- Dumping data for table `reviews`
--

INSERT INTO `reviews` (`id`, `product_id`, `user_id`, `rating`, `comment`, `is_visible`, `created_at`) VALUES
(1, 1, 2, 5, 'Amazing educational toy! My child loves it.', 1, '2026-10-29 10:31:18'),
(2, 2, 3, 4, 'Very fun robot, battery life could be slightly better.', 1, '2026-10-29 10:31:18');

-- --------------------------------------------------------

--
-- Table structure for table `search_history`
--

CREATE TABLE IF NOT EXISTS `search_history` (
  `search_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` int(10) UNSIGNED DEFAULT NULL,
  `search_term` varchar(255) NOT NULL,
  `results_count` int(10) UNSIGNED DEFAULT NULL,
  `searched_at` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`search_id`),
  KEY `idx_search_user_date` (`user_id`,`searched_at`),
  KEY `idx_search_term` (`search_term`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `search_history`
--

INSERT INTO `search_history` (`search_id`, `user_id`, `search_term`, `results_count`, `searched_at`) VALUES
(1, 2, 'puzzle', 1, '2026-10-29 10:31:18'),
(2, 3, 'robot', 1, '2026-10-29 10:31:18');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE IF NOT EXISTS `users` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `first_name` varchar(50) NOT NULL,
  `last_name` varchar(50) NOT NULL,
  `email` varchar(120) NOT NULL,
  `password_hash` varchar(255) DEFAULT NULL,
  `google_id` varchar(64) DEFAULT NULL,
  `birth_date` date DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `role` enum('customer','admin') NOT NULL DEFAULT 'customer',
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_users_email` (`email`),
  UNIQUE KEY `uq_users_google_id` (`google_id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `first_name`, `last_name`, `email`, `password_hash`, `google_id`, `birth_date`, `phone`, `role`, `is_active`, `created_at`) VALUES
(1, 'Omar', 'Saleh', 'omar@kiddo.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', NULL, '2018-04-10', '+962791111111', 'customer', 1, '2026-10-29 10:31:18'),
(2, 'Admin', 'User', 'admin123@mail.com', '$2y$10$4wlVeLBGCTb93ysR/2K.welDq/KTV5WE1XV1AXzeGGPz6eblhmGMO', NULL, NULL, NULL, 'admin', 1, '2026-10-29 13:02:00'),
(3, 'Rema', 'Ahmad', 'rema@kiddo.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', NULL, '2020-08-22', '+962792222222', 'customer', 1, '2026-10-29 10:31:18');

--
-- Constraints for dumped tables
--

--
-- Constraints for table `admin_action_usage`
--
ALTER TABLE `admin_action_usage`
  ADD CONSTRAINT `fk_admin_action_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `discounts`
--
ALTER TABLE `discounts`
  ADD CONSTRAINT `fk_discounts_category` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_discounts_product` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `images`
--
ALTER TABLE `images`
  ADD CONSTRAINT `fk_images_product` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `fk_orders_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON UPDATE CASCADE;

--
-- Constraints for table `order_items`
--
ALTER TABLE `order_items`
  ADD CONSTRAINT `fk_items_order` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_items_product` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Constraints for table `products`
--
ALTER TABLE `products`
  ADD CONSTRAINT `fk_products_category` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON UPDATE CASCADE;

--
-- Constraints for table `reviews`
--
ALTER TABLE `reviews`
  ADD CONSTRAINT `fk_reviews_product` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_reviews_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `search_history`
--
ALTER TABLE `search_history`
  ADD CONSTRAINT `fk_search_history_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
