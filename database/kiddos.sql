-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Oct 31, 2026 at 04:40 PM
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
CREATE DATABASE IF NOT EXISTS `kiddos` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `kiddos`;

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

--
-- Dumping data for table `admin_action_usage`
--

INSERT INTO `admin_action_usage` (`user_id`, `page`, `visits`) VALUES
(1, 'orders', 8),
(2, 'add_discount.php', 2),
(2, 'age_groups.php', 30),
(2, 'categories.php', 17),
(2, 'dashboard', 45),
(2, 'discounts.php', 35),
(2, 'orders.php', 40),
(2, 'products', 120),
(2, 'products.php', 53),
(2, 'reviews.php', 6),
(2, 'users.php', 37),
(7, 'age_groups.php', 1),
(7, 'categories.php', 2),
(7, 'discounts.php', 1),
(7, 'orders.php', 4),
(7, 'products.php', 2),
(7, 'reviews.php', 1),
(7, 'users.php', 1);

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
(20, 'Infants', 'infants', 'Soft rattle toys, teethers, and plush companions for babies', 0, 1, 'uploads/age-groups/cc7c3a9b3519d003b5fe2ae0e9519521.png', 1),
(21, 'Toddlers', 'toddlers', 'Safe toys designed for toddlers exploring textures and colors', 1, 3, 'toddlers.jpg', 2),
(22, 'Little Kids', 'little-kids', 'Engaging toys and learning games for growing kids', 4, 7, 'little_kids.jpg', 3),
(23, 'Teens', 'teens', 'for all cool boys and girls', 10, 16, 'uploads/age-groups/3b2fb7d49d9faa6d6a25050d6afda2a2.jpg', 5),
(24, 'Kids', 'kids', 'qwerty', 7, 10, 'uploads/age-groups/40113439db5ef86f33fb74902823e431.webp', 4),
(25, 'qwertyu', 'qwerty', 'qwertyu', 16, 17, 'uploads/age-groups/42aa57edff9f67ae10410e0e192b9080.jpg', 6);

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
  `image` varchar(255) DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_categories_name` (`name`),
  UNIQUE KEY `uq_categories_slug` (`slug`)
) ENGINE=InnoDB AUTO_INCREMENT=24 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `categories`
--

INSERT INTO `categories` (`id`, `name`, `slug`, `description`, `icon`, `image`, `created_at`) VALUES
(20, 'Educational Toys', 'educational-toys', 'Learning and skill-building toys for smart kids', 'fa-graduation-cap', NULL, '2026-10-29 10:31:18'),
(21, 'Wooden Toys', 'wooden-toys', 'Eco-friendly and durable wooden toys', 'fa-tree', NULL, '2026-10-29 01:52:41'),
(22, 'Plush & Soft Toys', 'plush-soft-toys', 'Cuddly stuffed animals and soft companions', 'fa-heart', NULL, '2026-10-29 10:31:18'),
(23, 'Active Play & Ride-ons', 'active-play-ride-ons', 'Safe push cars and toddler scooters', 'fa-child', NULL, '2026-10-29 10:31:18');

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
(20, 'Baby Essentials Sale', 15.00, NULL, 20, '2026-09-01', '2026-09-30', 1, '2026-10-29 10:31:18'),
(21, 'Puzzle Special Discount', 10.00, 20, NULL, '2026-10-01', '2026-11-01', 1, '2026-10-29 10:31:18'),
(23, 'gg', 70.00, 41, NULL, NULL, NULL, 1, '2026-10-31 01:36:54'),
(24, '10%', 10.00, 59, NULL, NULL, NULL, 1, '2026-10-31 16:11:22');

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
) ENGINE=InnoDB AUTO_INCREMENT=67 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `images`
--

INSERT INTO `images` (`image_id`, `product_id`, `image_path`, `alt_text`, `is_primary`, `sort_order`, `created_at`) VALUES
(62, 59, 'uploads/products/product_6ae5e22f12d423.77618926.webp', 'Junior Bowling Game Set', 1, 0, '2026-10-31 15:42:23'),
(63, 58, 'uploads/products/product_6ae5e267756667.40629730.jpg', 'Soft Foam Building Blocks Set', 1, 0, '2026-10-31 15:43:19'),
(64, 57, 'uploads/products/product_6ae5e28b6f4866.72016872.jpg', 'Ride-On Fire Truck Push Car', 1, 0, '2026-10-31 15:43:55'),
(65, 56, 'uploads/products/product_6ae5e3058f8e57.43631919.webp', 'Activity Tummy Time Water Mat', 1, 0, '2026-10-31 15:45:57'),
(66, 55, 'uploads/products/product_6ae5e335cf1564.68817870.webp', 'Junior Basketball Hoop Set', 1, 0, '2026-10-31 15:46:45');

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
  `status` enum('placed','shipped','delivered','cancelled') NOT NULL DEFAULT 'placed',
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
(1, 2, 68.98, 0.00, 2.00, 70.98, '2018-04-10', '+962791111111', 'Amman, Jubaiha, Main Street', 31.9539000, 35.9106000, 'placed', '2026-10-29 10:31:18', '2026-10-29 10:31:18'),
(2, 4, 49.99, 0.00, 2.00, 51.99, NULL, '0798536044', 'russaifah/al farah lands', 0.0000000, 0.0000000, 'placed', '2026-10-30 17:57:12', '2026-10-30 17:57:12'),
(3, 4, 189.90, 0.00, 2.00, 191.90, NULL, '0791234567', 'الرصيفة', 0.0000000, 0.0000000, 'placed', '2026-10-30 18:01:59', '2026-10-30 18:01:59'),
(4, 4, 599.90, 0.00, 2.00, 601.90, NULL, '0791234567', 'الرصيفة', 0.0000000, 0.0000000, 'delivered', '2026-10-30 18:02:54', '2026-10-30 18:27:15'),
(5, 4, 75.96, 0.00, 2.00, 77.96, NULL, '0781234567', 'ضصثقفغعهخ', 0.0000000, 0.0000000, 'shipped', '2026-10-30 18:03:34', '2026-10-30 18:06:56'),
(6, 4, 18.50, 0.00, 2.00, 20.50, NULL, '0781234567', 'asdfghj', 0.0000000, 0.0000000, 'placed', '2026-10-30 20:29:26', '2026-10-30 20:29:26'),
(7, 4, 92.50, 0.00, 2.00, 94.50, NULL, '0791234567', 'rusaifah', 0.0000000, 0.0000000, 'cancelled', '2026-10-30 20:49:38', '2026-10-31 17:06:28'),
(8, 5, 68.49, 0.00, 2.00, 70.49, NULL, '0792345678', 'الرصيفة', 0.0000000, 0.0000000, 'placed', '2026-10-30 23:15:40', '2026-10-30 23:15:40'),
(9, 5, 153.89, 0.00, 2.00, 155.89, NULL, '0792345678', 'ضصثقفغ', 0.0000000, 0.0000000, 'placed', '2026-10-31 00:04:44', '2026-10-31 00:04:44'),
(10, 5, 51.27, 0.00, 2.00, 53.27, NULL, '0792345678', 'ضصثقفغ', 0.0000000, 0.0000000, 'placed', '2026-10-31 00:07:32', '2026-10-31 00:07:32');

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
(1, 1, NULL, 'Wooden Alphabet & Number Puzzle Board', 18.99, 1, 18.99),
(2, 1, NULL, 'Magical Princess Dress-Up Trunk', 34.99, 1, 34.99),
(3, 2, NULL, 'Smart Voice-Controlled Robot', 49.99, 1, 49.99),
(4, 3, NULL, 'Wooden Alphabet & Number Puzzle Board', 18.99, 10, 189.90),
(5, 4, NULL, '3-Wheel Lean-to-Steer Scooter', 59.99, 10, 599.90),
(6, 5, NULL, 'Wooden Alphabet & Number Puzzle Board', 18.99, 4, 75.96),
(7, 6, 59, 'Junior Bowling Game Set', 18.50, 1, 18.50),
(8, 7, 59, 'Junior Bowling Game Set', 18.50, 5, 92.50),
(9, 8, 57, 'Ride-On Fire Truck Push Car', 49.99, 1, 49.99),
(10, 8, 59, 'Junior Bowling Game Set', 18.50, 1, 18.50),
(11, 9, 33, 'Wooden Pull Along Duck', 13.99, 11, 153.89),
(12, 10, 20, 'Wooden Alphabet & Number Puzzle Board', 17.09, 3, 51.27);

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
(20, 20, 'Wooden Alphabet & Number Puzzle Board', 'Colorful wooden puzzle pieces helping kids learn letters and numbers early.', 'Store in a dry place. Recommended for supervised play.', 18.99, 57, 'unisex', 3, 6, 'alphabet_puzzle.jpg', 1, '2026-10-29 10:31:18', '2026-10-31 00:07:32'),
(21, 20, 'Counting Beads Abacus', 'Traditional colorful wooden abacus for foundational math practice.', 'Wipe clean with a dry cloth.', 14.99, 45, 'unisex', 4, 7, 'abacus.jpg', 1, '2026-10-29 10:31:18', '2026-10-29 10:31:18'),
(22, 20, 'Shape Sorting Cube', 'Classic wooden sorting box with geometric blocks.', 'Keep away from moisture.', 16.50, 30, 'unisex', 1, 3, 'shape_sorter.jpg', 1, '2026-10-29 10:31:18', '2026-10-29 10:31:18'),
(23, 20, 'Flash Card Learning Bundle', 'Visual cards for animals, colors, and first words.', 'Store in the included box after use.', 9.99, 50, 'unisex', 2, 5, 'flash_cards.jpg', 1, '2026-10-29 10:31:18', '2026-10-29 10:31:18'),
(24, 20, 'Magnetic Spelling Board', 'Double-sided dry erase board with magnetic letters and numbers.', 'Avoid dropping on hard surfaces.', 24.99, 25, 'unisex', 4, 7, 'magnetic_board.jpg', 1, '2026-10-29 10:31:18', '2026-10-29 10:31:18'),
(25, 20, 'Solar System Planetarium Model', 'Paint and assemble your own glow-in-the-dark solar system.', 'Adult supervision recommended during assembly.', 19.99, 20, 'unisex', 5, 7, 'solar_system.jpg', 1, '2026-10-29 10:31:18', '2026-10-29 10:31:18'),
(26, 20, 'Geo-Board Graphic Array', 'Wooden pegged board with rubber bands for learning shapes and area.', 'Do not over-stretch rubber bands.', 12.50, 35, 'unisex', 4, 7, 'geoboard.jpg', 1, '2026-10-29 10:31:18', '2026-10-29 10:31:18'),
(27, 20, 'Kids Microscope Starter Kit', 'Beginner microscope with LED illumination and prepared slides.', 'Requires 2 AA batteries.', 29.99, 15, 'unisex', 6, 7, 'microscope.jpg', 1, '2026-10-29 10:31:18', '2026-10-29 10:31:18'),
(28, 20, 'Clock Telling Time Puzzle', 'Interactive clock puzzle to teach kids how to read analog and digital time.', 'Handle hands gently.', 15.00, 40, 'unisex', 5, 7, 'clock_puzzle.jpg', 1, '2026-10-29 10:31:18', '2026-10-29 10:31:18'),
(29, 20, 'Montessori Fraction Circles', 'Visual wooden math manipulation plates for basic fractions.', 'Store flat in box.', 21.00, 20, 'unisex', 5, 7, 'fraction_circles.jpg', 1, '2026-10-29 10:31:18', '2026-10-29 10:31:18'),
(30, 21, 'Stacking Wooden Rainbow Rings', 'Classic montessori wooden stacking rings to develop color recognition and motor skills.', 'Wipe clean with a damp cloth.', 22.00, 30, 'unisex', 1, 3, 'stacking_rings.jpg', 1, '2026-10-29 10:31:18', '2026-10-29 10:31:18'),
(31, 21, 'Wooden Pound a Peg Bench', 'Hand-eye coordination toy with wooden mallet and colourful pegs.', 'Inspect mallet before use.', 17.50, 25, 'unisex', 2, 4, 'pound_a_peg.jpg', 1, '2026-10-29 10:31:18', '2026-10-29 10:31:18'),
(32, 21, 'Classic Wooden Train Set', 'Connecting wooden train cars and tracks for building railways.', 'Compatible with major wooden track brands.', 32.99, 20, 'unisex', 3, 7, 'wooden_train.jpg', 1, '2026-10-29 10:31:18', '2026-10-29 10:31:18'),
(33, 21, 'Wooden Pull Along Duck', 'Cute duck on wheels that waddles when pulled by its string.', 'Do not submerge in water.', 13.99, 29, 'unisex', 1, 3, 'pull_duck.jpg', 1, '2026-10-29 10:31:18', '2026-10-31 00:04:44'),
(34, 21, 'Natural Wooden Building Blocks', '100-piece natural finish solid wood block bucket.', 'Store in provided canvas bag.', 29.99, 50, 'unisex', 2, 7, 'wooden_blocks.jpg', 1, '2026-10-29 10:31:18', '2026-10-29 10:31:18'),
(35, 21, 'Wooden Tool Bench Playset', 'Mini workbench with toy hammer, screwdriver, screws, and gears.', 'Contains small parts.', 39.99, 15, 'unisex', 3, 6, 'tool_bench.jpg', 1, '2026-10-29 10:31:18', '2026-10-29 10:31:18'),
(36, 21, 'Wooden Kitchen Cutting Fruits', 'Wooden play food set held together with safe velcro and a wooden knife.', 'Wipe clean.', 19.50, 35, 'unisex', 2, 5, 'cutting_fruits.jpg', 1, '2026-10-29 10:31:18', '2026-10-29 10:31:18'),
(37, 21, 'Wooden Lacing Beads', 'Large wooden beads with stringing laces for fine motor skills.', 'Supervise young toddlers during play.', 11.99, 40, 'unisex', 3, 5, 'lacing_beads.jpg', 1, '2026-10-29 10:31:18', '2026-10-29 10:31:18'),
(38, 21, 'Wooden Xylophone Musical Toy', 'Brightly colored wooden base with metal keys and two mallets.', 'Do not strike keys too hard.', 16.99, 25, 'unisex', 1, 4, 'wooden_xylophone.jpg', 1, '2026-10-29 10:31:18', '2026-10-29 10:31:18'),
(39, 21, 'Wooden Noahs Ark Playset', 'Detailed ark ship with paired wooden animal figures.', 'Store in a dry location.', 34.99, 18, 'unisex', 3, 6, 'noahs_ark.jpg', 1, '2026-10-29 10:31:18', '2026-10-29 10:31:18'),
(40, 22, 'Organic Cotton Baby Rattle Set', 'Soft, organic cotton sensory teethers and rattles designed safely for newborns.', 'Machine washable on gentle cycle. Air dry.', 14.50, 40, 'unisex', 0, 1, 'baby_rattle.jpg', 1, '2026-10-29 10:31:18', '2026-10-29 10:31:18'),
(41, 22, 'Fluffy Teddy Bear', 'Soft hugging plush bear companion for naptime and comfort.', 'Surface wash only.', 18.00, 50, 'unisex', 0, 7, 'teddy_bear.jpg', 1, '2026-10-29 10:31:18', '2026-10-29 10:31:18'),
(42, 22, 'Musical Lullaby Star Plush', 'Gently glows and plays soothing lullabies when squeezed.', 'Remove battery module before washing.', 22.50, 30, 'unisex', 0, 2, 'lullaby_star.jpg', 1, '2026-10-29 10:31:18', '2026-10-29 10:31:18'),
(43, 22, 'Soft Crinkle Cloth Book', 'High contrast sensory fabric book with animal tails sticking out.', 'Machine washable.', 10.99, 60, 'unisex', 0, 2, 'crinkle_book.jpg', 1, '2026-10-29 10:31:18', '2026-10-29 10:31:18'),
(44, 22, 'Plush Puppy Activity Toy', 'Interactive soft dog with teethers, mirrors, and squeakers attached.', 'Spot clean recommended.', 20.00, 25, 'unisex', 0, 3, 'plush_puppy.jpg', 1, '2026-10-29 10:31:18', '2026-10-29 10:31:18'),
(45, 22, 'Stuffed Bunny with Long Ears', 'Ultra-soft plush rabbit companion made of hypoallergenic materials.', 'Gentle machine wash.', 16.00, 45, 'unisex', 0, 7, 'stuffed_bunny.jpg', 1, '2026-10-29 10:31:18', '2026-10-29 10:31:18'),
(46, 22, 'Squeaker Elephant Toy', 'Easy-to-grip soft elephant toy that makes a gentle squeak sound.', 'Wipe clean with damp cloth.', 9.50, 40, 'unisex', 0, 2, 'squeaker_elephant.jpg', 1, '2026-10-29 10:31:18', '2026-10-29 10:31:18'),
(47, 22, 'Comfy Security Blanket Lovey', 'Mini soft blanket with attached animal head for soothing babies to sleep.', 'Machine washable.', 12.00, 35, 'unisex', 0, 2, 'security_blanket.jpg', 1, '2026-10-29 10:31:18', '2026-10-29 10:31:18'),
(48, 22, 'Plush Unicorn Pillow', 'Cuddly unicorn companion that doubles as a comfortable travel pillow.', 'Spot clean only.', 21.99, 20, 'unisex', 2, 7, 'unicorn_pillow.jpg', 1, '2026-10-29 10:31:18', '2026-10-29 10:31:18'),
(49, 22, 'Weighted Dinosaur Plush', 'Calming weighted plush toy to give children a comforting embrace.', 'Spot clean with mild soap.', 26.50, 15, 'unisex', 3, 7, 'weighted_dino.jpg', 1, '2026-10-29 10:31:18', '2026-10-29 10:31:18'),
(50, 23, 'Toddler Push Along Walker', 'Stable wooden push walker with building blocks included for early walkers.', 'Check wheel screws periodically for tightness.', 45.00, 15, 'unisex', 1, 3, 'push_walker.jpg', 1, '2026-10-29 10:31:18', '2026-10-29 10:31:18'),
(51, 23, '3-Wheel Lean-to-Steer Scooter', 'Stable and safe scooter with flashing LED wheels for toddlers and young children.', 'Always wear a helmet and protective pads while riding.', 59.99, 10, 'unisex', 3, 6, 'kids_scooter.jpg', 1, '2026-10-29 10:31:18', '2026-10-29 10:31:18'),
(52, 23, 'Inflatable Hopper Bouncy Animal', 'Stable rubber jumping animal toy with easy-to-grip ears for indoor/outdoor bouncing.', 'Keep away from sharp objects.', 24.50, 20, 'unisex', 2, 5, 'bouncy_hopper.jpg', 1, '2026-10-29 10:31:18', '2026-10-29 10:31:18'),
(53, 23, 'Toddler Balance Bike', 'No-pedal training bicycle designed to build balance and coordination early.', 'Use under direct adult supervision on flat surfaces.', 65.00, 12, 'unisex', 2, 5, 'balance_bike.jpg', 1, '2026-10-29 10:31:18', '2026-10-29 10:31:18'),
(54, 23, 'Soft Foam Play Tunnel', 'Collapsible crawl tunnel for active indoor and outdoor motor play.', 'Wipe clean.', 27.99, 18, 'unisex', 1, 5, 'play_tunnel.jpg', 1, '2026-10-29 10:31:18', '2026-10-29 10:31:18'),
(55, 23, 'Junior Basketball Hoop Set', 'Adjustable height freestanding basketball hoop and soft ball for little kids.', 'Fill base with water or sand for stability.', 38.50, 14, 'unisex', 3, 7, 'uploads/products/product_6ae5e335cf1564.68817870.webp', 1, '2026-10-29 10:31:18', '2026-10-31 15:46:45'),
(56, 23, 'Activity Tummy Time Water Mat', 'Inflatable water play mat to support infant motor development and core strength.', 'Empty and dry after each use.', 11.99, 40, 'unisex', 0, 1, 'uploads/products/product_6ae5e3058f8e57.43631919.webp', 1, '2026-10-29 10:31:18', '2026-10-31 15:45:57'),
(57, 23, 'Ride-On Fire Truck Push Car', 'Foot-to-floor steering ride-on car with under-seat storage compartment.', 'Check wheel alignment regularly.', 49.99, 9, 'unisex', 1, 3, 'uploads/products/product_6ae5e28b6f4866.72016872.jpg', 1, '2026-10-29 10:31:18', '2026-10-31 15:43:55'),
(58, 23, 'Soft Foam Building Blocks Set', 'Large lightweight foam blocks for tumbling, building, and active play safely.', 'Spot clean with damp sponge.', 54.99, 8, 'unisex', 1, 4, 'uploads/products/product_6ae5e267756667.40629730.jpg', 1, '2026-10-29 10:31:18', '2026-10-31 15:43:19'),
(59, 23, 'Junior Bowling Game Set', 'Soft, lightweight pins and ball set for indoor safe bowling fun.', 'Wipe clean.', 18.50, 23, 'unisex', 2, 6, 'uploads/products/product_6ae5e22f12d423.77618926.webp', 1, '2026-10-29 10:31:18', '2026-10-31 17:06:28');

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
(20, 20, 1, 5, 'Great for my preschooler learning letters!', 0, '2026-10-29 10:31:18'),
(21, 40, 3, 5, 'So soft and safe for my newborn baby.', 0, '2026-10-29 10:31:18'),
(22, 27, 4, 5, 'omar', 1, '2026-10-30 20:32:34'),
(23, 59, 4, 4, 'omar', 1, '2026-10-30 20:33:14'),
(24, 33, 5, 4, 'ضصثقفغعهخ', 1, '2026-10-31 00:13:01');

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
) ENGINE=InnoDB AUTO_INCREMENT=54 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `search_history`
--

INSERT INTO `search_history` (`search_id`, `user_id`, `search_term`, `results_count`, `searched_at`) VALUES
(1, 2, 'puzzle', 1, '2026-10-29 10:31:18'),
(2, 3, 'robot', 1, '2026-10-29 10:31:18'),
(20, 1, 'puzzle', 1, '2026-10-29 10:31:18'),
(21, 3, 'rattle', 1, '2026-10-29 10:31:18'),
(53, 4, 'Junior Bowling Game Set', 1, '2026-10-30 20:28:07');

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
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `first_name`, `last_name`, `email`, `password_hash`, `google_id`, `birth_date`, `phone`, `role`, `is_active`, `created_at`) VALUES
(1, 'Omar', 'Saleh', 'omar@kiddo.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', NULL, NULL, '+962791111111', 'customer', 1, '2026-10-29 10:31:18'),
(2, 'Admin', 'User', 'admin123@mail.com', '$2y$10$4wlVeLBGCTb93ysR/2K.welDq/KTV5WE1XV1AXzeGGPz6eblhmGMO', NULL, NULL, NULL, 'admin', 1, '2026-10-29 13:02:00'),
(3, 'Rema', 'Ahmad', 'rema@kiddo.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', NULL, '2020-08-22', '+962792222222', 'customer', 1, '2026-10-29 10:31:18'),
(4, 'omars', 'qadarahs', 'omar@omar.com', '$2y$10$.I9kQSmS6TVAnBjtBxCVe.vXlxgEPu2fScopZdMam8P5eQphSAZjW', NULL, NULL, '0791234567', 'customer', 1, '2026-10-30 17:56:00'),
(5, 'Omar', 'Qadarah', 'omarqadarah1@gmail.com', NULL, '114175494943700861038', '1997-08-13', '0792345678', 'customer', 1, '2026-10-30 22:24:47'),
(6, 'omer', 'nael', 'qw@qw.com', '$2y$10$Sh73uvQ7PrJVF9nLV./.ueiEY7rpJ.7Ybt9dRz6nVc7l5BPZmg4qu', NULL, NULL, NULL, 'customer', 0, '2026-10-30 22:34:09'),
(7, 'omar', 'qad', 'omarss@naelss.com', '$2y$10$bQvxQlVjRB7JEiLDHE9lku0ewYlp6DhrEXaL2xM4MCuSoOLtGtqIW', NULL, '2026-10-31', '0791234567', 'admin', 0, '2026-10-31 02:37:56');

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