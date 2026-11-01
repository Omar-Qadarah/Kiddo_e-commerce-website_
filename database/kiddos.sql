 
 
 
 
 
 
 
 

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT  ;
 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS  ;
 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION  ;
 SET NAMES utf8mb4  ;

 
 
 
CREATE DATABASE IF NOT EXISTS `kiddos` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `kiddos`;

 

 
 
 

CREATE TABLE IF NOT EXISTS `admin_action_usage` (
  `user_id` int(10) UNSIGNED NOT NULL,
  `page` varchar(60) NOT NULL,
  `visits` int(10) UNSIGNED NOT NULL DEFAULT 0,
  PRIMARY KEY (`user_id`,`page`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

 
 
 

INSERT INTO `admin_action_usage` (`user_id`, `page`, `visits`) VALUES
(1, 'orders', 8),
(2, 'add_discount.php', 2),
(2, 'age_groups.php', 30),
(2, 'categories.php', 17),
(2, 'dashboard', 45),
(2, 'discounts.php', 35),
(2, 'orders.php', 40),
(2, 'products', 120),
(2, 'products.php', 66),
(2, 'reviews.php', 6),
(2, 'users.php', 37),
(7, 'age_groups.php', 1),
(7, 'categories.php', 2),
(7, 'discounts.php', 1),
(7, 'orders.php', 4),
(7, 'products.php', 2),
(7, 'reviews.php', 1),
(7, 'users.php', 1);

 

 
 
 

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
) ENGINE=InnoDB AUTO_INCREMENT=26 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

 
 
 

INSERT INTO `age_groups` (`id`, `name`, `slug`, `description`, `min_age`, `max_age`, `image`, `sort_order`) VALUES
(20, 'Infants', 'infants', 'Soft rattle toys, teethers, and plush companions for babies', 0, 1, 'uploads/age-groups/cc7c3a9b3519d003b5fe2ae0e9519521.png', 1),
(21, 'Toddlers', 'toddlers', 'Safe toys designed for toddlers exploring textures and colors', 1, 3, 'assets/images/age-groups/toddlers.jpg', 2),
(22, 'Little Kids', 'little-kids', 'Engaging toys and learning games for growing kids', 4, 7, 'assets/images/age-groups/little-kids.jpg', 3),
(23, 'Teens', 'teens', 'for all cool boys and girls', 10, 16, 'uploads/age-groups/3b2fb7d49d9faa6d6a25050d6afda2a2.jpg', 5),
(24, 'Kids', 'kids', 'qwerty', 7, 10, 'uploads/age-groups/40113439db5ef86f33fb74902823e431.webp', 4),
(25, 'qwertyu', 'qwerty', 'qwertyu', 16, 17, 'uploads/age-groups/42aa57edff9f67ae10410e0e192b9080.jpg', 6);

 

 
 
 

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

 
 
 

INSERT INTO `categories` (`id`, `name`, `slug`, `description`, `icon`, `image`, `created_at`) VALUES
(20, 'Educational Toys', 'educational-toys', 'Learning and skill-building toys for smart kids', 'fa-graduation-cap', 'uploads/categories/educational-toys.jpg', '2026-10-29 10:31:18'),
(21, 'Wooden Toys', 'wooden-toys', 'Eco-friendly and durable wooden toys', 'fa-tree', 'uploads/categories/wooden-toys.jpg', '2026-10-29 01:52:41'),
(22, 'Plush & Soft Toys', 'plush-soft-toys', 'Cuddly stuffed animals and soft companions', 'fa-heart', 'uploads/categories/plush-soft-toys.jpg', '2026-10-29 10:31:18'),
(23, 'Active Play & Ride-ons', 'active-play-ride-ons', 'Safe push cars and toddler scooters', 'fa-child', 'uploads/categories/active-play-ride-ons.jpg', '2026-10-29 10:31:18');

 

 
 
 

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
) ENGINE=InnoDB AUTO_INCREMENT=29 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

 
 
 

INSERT INTO `discounts` (`id`, `title`, `percentage`, `product_id`, `category_id`, `starts_at`, `ends_at`, `is_active`, `created_at`) VALUES
(20, 'Baby Essentials Sale', 15.00, NULL, 20, '2026-09-01', '2026-09-30', 1, '2026-10-29 10:31:18'),
(21, 'Puzzle Special Discount', 10.00, 20, NULL, '2026-10-01', '2026-11-01', 1, '2026-10-29 10:31:18'),
(23, 'gg', 70.00, 41, NULL, NULL, NULL, 1, '2026-10-31 01:36:54'),
(24, '10%', 10.00, 59, NULL, NULL, NULL, 1, '2026-10-31 16:11:22'),
(25, 'Microscope Special', 20.00, 105, NULL, '2026-10-20', '2026-11-30', 1, '2026-10-31 22:01:16'),
(26, 'Teepee Tent Offer', 25.00, 153, NULL, '2026-10-20', '2026-11-30', 1, '2026-10-31 22:01:16'),
(27, 'RC Racing Deal', 12.00, 182, NULL, '2026-10-20', '2026-11-30', 1, '2026-10-31 22:01:16'),
(28, 'Plush Friends Promo', 10.00, 144, NULL, '2026-10-20', '2026-12-15', 1, '2026-10-31 22:01:16');

 

 
 
 

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
) ENGINE=InnoDB AUTO_INCREMENT=325 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

 
 
 

INSERT INTO `images` (`image_id`, `product_id`, `image_path`, `alt_text`, `is_primary`, `sort_order`, `created_at`) VALUES
(62, 59, 'uploads/products/product_6ae5e22f12d423.77618926.webp', 'Junior Bowling Game Set', 1, 0, '2026-10-31 15:42:23'),
(63, 58, 'uploads/products/product_6ae5e267756667.40629730.jpg', 'Soft Foam Building Blocks Set', 1, 0, '2026-10-31 15:43:19'),
(64, 57, 'uploads/products/product_6ae5e28b6f4866.72016872.jpg', 'Ride-On Fire Truck Push Car', 1, 0, '2026-10-31 15:43:55'),
(65, 56, 'uploads/products/product_6ae5e3058f8e57.43631919.webp', 'Activity Tummy Time Water Mat', 1, 0, '2026-10-31 15:45:57'),
(66, 55, 'uploads/products/product_6ae5e335cf1564.68817870.webp', 'Junior Basketball Hoop Set', 1, 0, '2026-10-31 15:46:45'),
(67, 101, 'assets/images/products/educational-toys/rainbow-wooden-abacus-1.jpg', 'Rainbow Wooden Abacus - main photo', 1, 0, '2026-10-01 09:13:00'),
(68, 101, 'assets/images/products/educational-toys/rainbow-wooden-abacus-2.jpg', 'Rainbow Wooden Abacus - second photo', 0, 1, '2026-10-01 09:13:00'),
(69, 102, 'assets/images/products/educational-toys/abc-letter-blocks-28-pcs-1.jpg', 'ABC Letter Blocks (28 pcs) - main photo', 1, 0, '2026-10-01 16:26:00'),
(70, 102, 'assets/images/products/educational-toys/abc-letter-blocks-28-pcs-2.jpg', 'ABC Letter Blocks (28 pcs) - second photo', 0, 1, '2026-10-01 16:26:00'),
(71, 103, 'assets/images/products/educational-toys/foam-alphabet-number-shapes-1.jpg', 'Foam Alphabet & Number Shapes - main photo', 1, 0, '2026-10-01 23:39:00'),
(72, 103, 'assets/images/products/educational-toys/foam-alphabet-number-shapes-2.jpg', 'Foam Alphabet & Number Shapes - second photo', 0, 1, '2026-10-01 23:39:00'),
(73, 104, 'assets/images/products/educational-toys/montessori-number-puzzle-board-1.jpg', 'Montessori Number Puzzle Board - main photo', 1, 0, '2026-10-02 06:52:00'),
(74, 104, 'assets/images/products/educational-toys/montessori-number-puzzle-board-2.jpg', 'Montessori Number Puzzle Board - second photo', 0, 1, '2026-10-02 06:52:00'),
(75, 105, 'assets/images/products/educational-toys/junior-explorer-microscope-1.jpg', 'Junior Explorer Microscope - main photo', 1, 0, '2026-10-02 13:05:00'),
(76, 105, 'assets/images/products/educational-toys/junior-explorer-microscope-2.jpg', 'Junior Explorer Microscope - second photo', 0, 1, '2026-10-02 13:05:00'),
(77, 106, 'assets/images/products/educational-toys/young-astronomer-telescope-1.jpg', 'Young Astronomer Telescope - main photo', 1, 0, '2026-10-02 20:18:00'),
(78, 106, 'assets/images/products/educational-toys/young-astronomer-telescope-2.jpg', 'Young Astronomer Telescope - second photo', 0, 1, '2026-10-02 20:18:00'),
(79, 107, 'assets/images/products/educational-toys/classic-desk-world-globe-1.jpg', 'Classic Desk World Globe - main photo', 1, 0, '2026-10-03 03:31:00'),
(80, 107, 'assets/images/products/educational-toys/classic-desk-world-globe-2.jpg', 'Classic Desk World Globe - second photo', 0, 1, '2026-10-03 03:31:00'),
(81, 108, 'assets/images/products/educational-toys/little-scientist-lab-kit-1.jpg', 'Little Scientist Lab Kit - main photo', 1, 0, '2026-10-03 10:44:00'),
(82, 108, 'assets/images/products/educational-toys/little-scientist-lab-kit-2.jpg', 'Little Scientist Lab Kit - second photo', 0, 1, '2026-10-03 10:44:00'),
(83, 109, 'assets/images/products/educational-toys/speed-cube-3x3-1.jpg', 'Speed Cube 3x3 - main photo', 1, 0, '2026-10-03 17:57:00'),
(84, 109, 'assets/images/products/educational-toys/speed-cube-3x3-2.jpg', 'Speed Cube 3x3 - second photo', 0, 1, '2026-10-03 17:57:00'),
(85, 110, 'assets/images/products/educational-toys/wooden-chess-set-for-kids-1.jpg', 'Wooden Chess Set for Kids - main photo', 1, 0, '2026-10-04 00:10:00'),
(86, 110, 'assets/images/products/educational-toys/wooden-chess-set-for-kids-2.jpg', 'Wooden Chess Set for Kids - second photo', 0, 1, '2026-10-04 00:10:00'),
(87, 111, 'assets/images/products/educational-toys/rainbow-stacking-rings-1.jpg', 'Rainbow Stacking Rings - main photo', 1, 0, '2026-10-04 07:23:00'),
(88, 111, 'assets/images/products/educational-toys/rainbow-stacking-rings-2.jpg', 'Rainbow Stacking Rings - second photo', 0, 1, '2026-10-04 07:23:00'),
(89, 112, 'assets/images/products/educational-toys/wooden-shape-sorter-board-1.jpg', 'Wooden Shape Sorter Board - main photo', 1, 0, '2026-10-04 14:36:00'),
(90, 112, 'assets/images/products/educational-toys/wooden-shape-sorter-board-2.jpg', 'Wooden Shape Sorter Board - second photo', 0, 1, '2026-10-04 14:36:00'),
(91, 113, 'assets/images/products/educational-toys/activity-cube-learning-center-1.jpg', 'Activity Cube Learning Center - main photo', 1, 0, '2026-10-04 21:49:00'),
(92, 113, 'assets/images/products/educational-toys/activity-cube-learning-center-2.jpg', 'Activity Cube Learning Center - second photo', 0, 1, '2026-10-04 21:49:00'),
(93, 114, 'assets/images/products/educational-toys/solar-system-planets-model-1.jpg', 'Solar System Planets Model - main photo', 1, 0, '2026-10-05 04:02:00'),
(94, 114, 'assets/images/products/educational-toys/solar-system-planets-model-2.jpg', 'Solar System Planets Model - second photo', 0, 1, '2026-10-05 04:02:00'),
(95, 115, 'assets/images/products/educational-toys/dinosaur-fossil-dig-kit-1.jpg', 'Dinosaur Fossil Dig Kit - main photo', 1, 0, '2026-10-05 11:15:00'),
(96, 115, 'assets/images/products/educational-toys/dinosaur-fossil-dig-kit-2.jpg', 'Dinosaur Fossil Dig Kit - second photo', 0, 1, '2026-10-05 11:15:00'),
(97, 116, 'assets/images/products/educational-toys/little-doctor-play-kit-1.jpg', 'Little Doctor Play Kit - main photo', 1, 0, '2026-10-05 18:28:00'),
(98, 116, 'assets/images/products/educational-toys/little-doctor-play-kit-2.jpg', 'Little Doctor Play Kit - second photo', 0, 1, '2026-10-05 18:28:00'),
(99, 117, 'assets/images/products/educational-toys/watercolor-painting-set-1.jpg', 'Watercolor Painting Set - main photo', 1, 0, '2026-10-06 01:41:00'),
(100, 117, 'assets/images/products/educational-toys/watercolor-painting-set-2.jpg', 'Watercolor Painting Set - second photo', 0, 1, '2026-10-06 01:41:00'),
(101, 118, 'assets/images/products/educational-toys/kids-jigsaw-puzzle-100-pcs-1.jpg', 'Kids Jigsaw Puzzle (100 pcs) - main photo', 1, 0, '2026-10-06 08:54:00'),
(102, 118, 'assets/images/products/educational-toys/kids-jigsaw-puzzle-100-pcs-2.jpg', 'Kids Jigsaw Puzzle (100 pcs) - second photo', 0, 1, '2026-10-06 08:54:00'),
(103, 119, 'assets/images/products/educational-toys/nature-explorer-magnifying-glass-1.jpg', 'Nature Explorer Magnifying Glass - main photo', 1, 0, '2026-10-06 15:07:00'),
(104, 119, 'assets/images/products/educational-toys/nature-explorer-magnifying-glass-2.jpg', 'Nature Explorer Magnifying Glass - second photo', 0, 1, '2026-10-06 15:07:00'),
(105, 120, 'assets/images/products/educational-toys/color-sorting-bowls-set-1.jpg', 'Color Sorting Bowls Set - main photo', 1, 0, '2026-10-06 22:20:00'),
(106, 120, 'assets/images/products/educational-toys/color-sorting-bowls-set-2.jpg', 'Color Sorting Bowls Set - second photo', 0, 1, '2026-10-06 22:20:00'),
(107, 121, 'assets/images/products/wooden-toys/classic-wooden-train-set-1.jpg', 'Classic Wooden Train Set - main photo', 1, 0, '2026-10-01 10:33:00'),
(108, 121, 'assets/images/products/wooden-toys/classic-wooden-train-set-2.jpg', 'Classic Wooden Train Set - second photo', 0, 1, '2026-10-01 10:33:00'),
(109, 122, 'assets/images/products/wooden-toys/wooden-rainbow-stacker-1.jpg', 'Wooden Rainbow Stacker - main photo', 1, 0, '2026-10-01 17:46:00'),
(110, 122, 'assets/images/products/wooden-toys/wooden-rainbow-stacker-2.jpg', 'Wooden Rainbow Stacker - second photo', 0, 1, '2026-10-01 17:46:00'),
(111, 123, 'assets/images/products/wooden-toys/pastel-wooden-stacking-tower-1.jpg', 'Pastel Wooden Stacking Tower - main photo', 1, 0, '2026-10-02 00:59:00'),
(112, 123, 'assets/images/products/wooden-toys/pastel-wooden-stacking-tower-2.jpg', 'Pastel Wooden Stacking Tower - second photo', 0, 1, '2026-10-02 00:59:00'),
(113, 124, 'assets/images/products/wooden-toys/three-story-wooden-dollhouse-1.jpg', 'Three-Story Wooden Dollhouse - main photo', 1, 0, '2026-10-02 07:12:00'),
(114, 124, 'assets/images/products/wooden-toys/three-story-wooden-dollhouse-2.jpg', 'Three-Story Wooden Dollhouse - second photo', 0, 1, '2026-10-02 07:12:00'),
(115, 125, 'assets/images/products/wooden-toys/wooden-play-kitchen-1.jpg', 'Wooden Play Kitchen - main photo', 1, 0, '2026-10-02 14:25:00'),
(116, 125, 'assets/images/products/wooden-toys/wooden-play-kitchen-2.jpg', 'Wooden Play Kitchen - second photo', 0, 1, '2026-10-02 14:25:00'),
(117, 126, 'assets/images/products/wooden-toys/vintage-rocking-horse-1.jpg', 'Vintage Rocking Horse - main photo', 1, 0, '2026-10-02 21:38:00'),
(118, 126, 'assets/images/products/wooden-toys/vintage-rocking-horse-2.jpg', 'Vintage Rocking Horse - second photo', 0, 1, '2026-10-02 21:38:00'),
(119, 127, 'assets/images/products/wooden-toys/colorful-wooden-building-blocks-1.jpg', 'Colorful Wooden Building Blocks - main photo', 1, 0, '2026-10-03 04:51:00'),
(120, 127, 'assets/images/products/wooden-toys/colorful-wooden-building-blocks-2.jpg', 'Colorful Wooden Building Blocks - second photo', 0, 1, '2026-10-03 04:51:00'),
(121, 128, 'assets/images/products/wooden-toys/natural-wooden-cube-set-50-pcs-1.jpg', 'Natural Wooden Cube Set (50 pcs) - main photo', 1, 0, '2026-10-03 11:04:00'),
(122, 128, 'assets/images/products/wooden-toys/natural-wooden-cube-set-50-pcs-2.jpg', 'Natural Wooden Cube Set (50 pcs) - second photo', 0, 1, '2026-10-03 11:04:00'),
(123, 129, 'assets/images/products/wooden-toys/rainbow-wooden-xylophone-1.jpg', 'Rainbow Wooden Xylophone - main photo', 1, 0, '2026-10-03 18:17:00'),
(124, 129, 'assets/images/products/wooden-toys/rainbow-wooden-xylophone-2.jpg', 'Rainbow Wooden Xylophone - second photo', 0, 1, '2026-10-03 18:17:00'),
(125, 130, 'assets/images/products/wooden-toys/wooden-pull-along-animals-1.jpg', 'Wooden Pull-Along Animals - main photo', 1, 0, '2026-10-04 01:30:00'),
(126, 130, 'assets/images/products/wooden-toys/wooden-pull-along-animals-2.jpg', 'Wooden Pull-Along Animals - second photo', 0, 1, '2026-10-04 01:30:00'),
(127, 131, 'assets/images/products/wooden-toys/wooden-bead-maze-roller-coaster-1.jpg', 'Wooden Bead Maze Roller Coaster - main photo', 1, 0, '2026-10-04 08:43:00'),
(128, 131, 'assets/images/products/wooden-toys/wooden-bead-maze-roller-coaster-2.jpg', 'Wooden Bead Maze Roller Coaster - second photo', 0, 1, '2026-10-04 08:43:00'),
(129, 132, 'assets/images/products/wooden-toys/wooden-tool-box-set-1.jpg', 'Wooden Tool Box Set - main photo', 1, 0, '2026-10-04 15:56:00'),
(130, 132, 'assets/images/products/wooden-toys/wooden-tool-box-set-2.jpg', 'Wooden Tool Box Set - second photo', 0, 1, '2026-10-04 15:56:00'),
(131, 133, 'assets/images/products/wooden-toys/wooden-toy-cars-collection-1.jpg', 'Wooden Toy Cars Collection - main photo', 1, 0, '2026-10-04 22:09:00'),
(132, 133, 'assets/images/products/wooden-toys/wooden-toy-cars-collection-2.jpg', 'Wooden Toy Cars Collection - second photo', 0, 1, '2026-10-04 22:09:00'),
(133, 134, 'assets/images/products/wooden-toys/wooden-biplane-1.jpg', 'Wooden Biplane - main photo', 1, 0, '2026-10-05 05:22:00'),
(134, 134, 'assets/images/products/wooden-toys/wooden-biplane-2.jpg', 'Wooden Biplane - second photo', 0, 1, '2026-10-05 05:22:00'),
(135, 135, 'assets/images/products/wooden-toys/wooden-food-cutting-set-1.jpg', 'Wooden Food Cutting Set - main photo', 1, 0, '2026-10-05 12:35:00'),
(136, 135, 'assets/images/products/wooden-toys/wooden-food-cutting-set-2.jpg', 'Wooden Food Cutting Set - second photo', 0, 1, '2026-10-05 12:35:00'),
(137, 136, 'assets/images/products/wooden-toys/wooden-doll-pram-1.jpg', 'Wooden Doll Pram - main photo', 1, 0, '2026-10-05 19:48:00'),
(138, 136, 'assets/images/products/wooden-toys/wooden-doll-pram-2.jpg', 'Wooden Doll Pram - second photo', 0, 1, '2026-10-05 19:48:00'),
(139, 137, 'assets/images/products/wooden-toys/wooden-spinning-tops-set-1.jpg', 'Wooden Spinning Tops Set - main photo', 1, 0, '2026-10-06 02:01:00'),
(140, 137, 'assets/images/products/wooden-toys/wooden-spinning-tops-set-2.jpg', 'Wooden Spinning Tops Set - second photo', 0, 1, '2026-10-06 02:01:00'),
(141, 138, 'assets/images/products/wooden-toys/wooden-sailboat-model-1.jpg', 'Wooden Sailboat Model - main photo', 1, 0, '2026-10-06 09:14:00'),
(142, 138, 'assets/images/products/wooden-toys/wooden-sailboat-model-2.jpg', 'Wooden Sailboat Model - second photo', 0, 1, '2026-10-06 09:14:00'),
(143, 139, 'assets/images/products/wooden-toys/wooden-brain-teaser-puzzle-1.jpg', 'Wooden Brain Teaser Puzzle - main photo', 1, 0, '2026-10-06 16:27:00'),
(144, 139, 'assets/images/products/wooden-toys/wooden-brain-teaser-puzzle-2.jpg', 'Wooden Brain Teaser Puzzle - second photo', 0, 1, '2026-10-06 16:27:00'),
(145, 140, 'assets/images/products/wooden-toys/wooden-alphabet-number-cubes-1.jpg', 'Wooden Alphabet Number Cubes - main photo', 1, 0, '2026-10-06 23:40:00'),
(146, 140, 'assets/images/products/wooden-toys/wooden-alphabet-number-cubes-2.jpg', 'Wooden Alphabet Number Cubes - second photo', 0, 1, '2026-10-06 23:40:00'),
(147, 141, 'assets/images/products/girls-toys/porcelain-princess-doll-1.jpg', 'Porcelain Princess Doll - main photo', 1, 0, '2026-10-01 11:53:00'),
(148, 141, 'assets/images/products/girls-toys/porcelain-princess-doll-2.jpg', 'Porcelain Princess Doll - second photo', 0, 1, '2026-10-01 11:53:00'),
(149, 142, 'assets/images/products/girls-toys/vintage-classic-doll-1.jpg', 'Vintage Classic Doll - main photo', 1, 0, '2026-10-01 18:06:00'),
(150, 142, 'assets/images/products/girls-toys/vintage-classic-doll-2.jpg', 'Vintage Classic Doll - second photo', 0, 1, '2026-10-01 18:06:00'),
(151, 143, 'assets/images/products/girls-toys/knitted-rag-doll-1.jpg', 'Knitted Rag Doll - main photo', 1, 0, '2026-10-02 01:19:00'),
(152, 143, 'assets/images/products/girls-toys/knitted-rag-doll-2.jpg', 'Knitted Rag Doll - second photo', 0, 1, '2026-10-02 01:19:00'),
(153, 144, 'assets/images/products/girls-toys/fluffy-unicorn-plush-1.jpg', 'Fluffy Unicorn Plush - main photo', 1, 0, '2026-10-02 08:32:00'),
(154, 144, 'assets/images/products/girls-toys/fluffy-unicorn-plush-2.jpg', 'Fluffy Unicorn Plush - second photo', 0, 1, '2026-10-02 08:32:00'),
(155, 145, 'assets/images/products/girls-toys/sparkle-eyes-unicorn-plush-1.jpg', 'Sparkle Eyes Unicorn Plush - main photo', 1, 0, '2026-10-02 15:45:00'),
(156, 145, 'assets/images/products/girls-toys/sparkle-eyes-unicorn-plush-2.jpg', 'Sparkle Eyes Unicorn Plush - second photo', 0, 1, '2026-10-02 15:45:00'),
(157, 146, 'assets/images/products/girls-toys/soft-bunny-plush-1.jpg', 'Soft Bunny Plush - main photo', 1, 0, '2026-10-02 22:58:00'),
(158, 146, 'assets/images/products/girls-toys/soft-bunny-plush-2.jpg', 'Soft Bunny Plush - second photo', 0, 1, '2026-10-02 22:58:00'),
(159, 147, 'assets/images/products/girls-toys/love-teddy-bear-gift-1.jpg', 'Love Teddy Bear Gift - main photo', 1, 0, '2026-10-03 05:11:00'),
(160, 147, 'assets/images/products/girls-toys/love-teddy-bear-gift-2.jpg', 'Love Teddy Bear Gift - second photo', 0, 1, '2026-10-03 05:11:00'),
(161, 150, 'assets/images/products/girls-toys/bead-jewelry-making-kit-1.jpg', 'Bead Jewelry Making Kit - main photo', 1, 0, '2026-10-04 02:50:00'),
(162, 150, 'assets/images/products/girls-toys/bead-jewelry-making-kit-2.jpg', 'Bead Jewelry Making Kit - second photo', 0, 1, '2026-10-04 02:50:00'),
(163, 151, 'assets/images/products/girls-toys/friendship-bracelet-kit-1.jpg', 'Friendship Bracelet Kit - main photo', 1, 0, '2026-10-04 09:03:00'),
(164, 151, 'assets/images/products/girls-toys/friendship-bracelet-kit-2.jpg', 'Friendship Bracelet Kit - second photo', 0, 1, '2026-10-04 09:03:00'),
(165, 153, 'assets/images/products/girls-toys/teepee-play-tent-1.jpg', 'Teepee Play Tent - main photo', 1, 0, '2026-10-04 23:29:00'),
(166, 153, 'assets/images/products/girls-toys/teepee-play-tent-2.jpg', 'Teepee Play Tent - second photo', 0, 1, '2026-10-04 23:29:00'),
(167, 157, 'assets/images/products/girls-toys/rainbow-modeling-clay-kit-1.jpg', 'Rainbow Modeling Clay Kit - main photo', 1, 0, '2026-10-06 03:21:00'),
(168, 157, 'assets/images/products/girls-toys/rainbow-modeling-clay-kit-2.jpg', 'Rainbow Modeling Clay Kit - second photo', 0, 1, '2026-10-06 03:21:00'),
(169, 158, 'assets/images/products/girls-toys/mini-grand-piano-1.jpg', 'Mini Grand Piano - main photo', 1, 0, '2026-10-06 10:34:00'),
(170, 158, 'assets/images/products/girls-toys/mini-grand-piano-2.jpg', 'Mini Grand Piano - second photo', 0, 1, '2026-10-06 10:34:00'),
(171, 159, 'assets/images/products/girls-toys/jumbo-crayons-coloring-set-1.jpg', 'Jumbo Crayons Coloring Set - main photo', 1, 0, '2026-10-06 17:47:00'),
(172, 159, 'assets/images/products/girls-toys/jumbo-crayons-coloring-set-2.jpg', 'Jumbo Crayons Coloring Set - second photo', 0, 1, '2026-10-06 17:47:00'),
(173, 160, 'assets/images/products/girls-toys/cuddly-kitty-plush-1.jpg', 'Cuddly Kitty Plush - main photo', 1, 0, '2026-10-07 00:00:00'),
(174, 160, 'assets/images/products/girls-toys/cuddly-kitty-plush-2.jpg', 'Cuddly Kitty Plush - second photo', 0, 1, '2026-10-07 00:00:00'),
(175, 161, 'assets/images/products/outdoor-sports/kids-balance-bike-1.jpg', 'Kids Balance Bike - main photo', 1, 0, '2026-10-01 12:13:00'),
(176, 161, 'assets/images/products/outdoor-sports/kids-balance-bike-2.jpg', 'Kids Balance Bike - second photo', 0, 1, '2026-10-01 12:13:00'),
(177, 162, 'assets/images/products/outdoor-sports/pink-balance-bike-1.jpg', 'Pink Balance Bike - main photo', 1, 0, '2026-10-01 19:26:00'),
(178, 162, 'assets/images/products/outdoor-sports/pink-balance-bike-2.jpg', 'Pink Balance Bike - second photo', 0, 1, '2026-10-01 19:26:00'),
(179, 163, 'assets/images/products/outdoor-sports/16-inch-blue-kids-bicycle-1.jpg', '16-inch Blue Kids Bicycle - main photo', 1, 0, '2026-10-02 02:39:00'),
(180, 163, 'assets/images/products/outdoor-sports/16-inch-blue-kids-bicycle-2.jpg', '16-inch Blue Kids Bicycle - second photo', 0, 1, '2026-10-02 02:39:00'),
(181, 164, 'assets/images/products/outdoor-sports/16-inch-pink-kids-bicycle-1.jpg', '16-inch Pink Kids Bicycle - main photo', 1, 0, '2026-10-02 09:52:00'),
(182, 164, 'assets/images/products/outdoor-sports/16-inch-pink-kids-bicycle-2.jpg', '16-inch Pink Kids Bicycle - second photo', 0, 1, '2026-10-02 09:52:00'),
(183, 165, 'assets/images/products/outdoor-sports/foldable-kick-scooter-1.jpg', 'Foldable Kick Scooter - main photo', 1, 0, '2026-10-02 16:05:00'),
(184, 165, 'assets/images/products/outdoor-sports/foldable-kick-scooter-2.jpg', 'Foldable Kick Scooter - second photo', 0, 1, '2026-10-02 16:05:00'),
(185, 166, 'assets/images/products/outdoor-sports/pink-kids-scooter-1.jpg', 'Pink Kids Scooter - main photo', 1, 0, '2026-10-02 23:18:00'),
(186, 166, 'assets/images/products/outdoor-sports/pink-kids-scooter-2.jpg', 'Pink Kids Scooter - second photo', 0, 1, '2026-10-02 23:18:00'),
(187, 167, 'assets/images/products/outdoor-sports/kids-football-size-4-1.jpg', 'Kids Football Size 4 - main photo', 1, 0, '2026-10-03 06:31:00'),
(188, 167, 'assets/images/products/outdoor-sports/kids-football-size-4-2.jpg', 'Kids Football Size 4 - second photo', 0, 1, '2026-10-03 06:31:00'),
(189, 168, 'assets/images/products/outdoor-sports/adjustable-basketball-hoop-1.jpg', 'Adjustable Basketball Hoop - main photo', 1, 0, '2026-10-03 13:44:00'),
(190, 168, 'assets/images/products/outdoor-sports/adjustable-basketball-hoop-2.jpg', 'Adjustable Basketball Hoop - second photo', 0, 1, '2026-10-03 13:44:00'),
(191, 169, 'assets/images/products/outdoor-sports/garden-trampoline-with-safety-net-1.jpg', 'Garden Trampoline with Safety Net - main photo', 1, 0, '2026-10-03 20:57:00'),
(192, 169, 'assets/images/products/outdoor-sports/garden-trampoline-with-safety-net-2.jpg', 'Garden Trampoline with Safety Net - second photo', 0, 1, '2026-10-03 20:57:00'),
(193, 170, 'assets/images/products/outdoor-sports/toddler-swing-seat-1.jpg', 'Toddler Swing Seat - main photo', 1, 0, '2026-10-04 03:10:00'),
(194, 170, 'assets/images/products/outdoor-sports/toddler-swing-seat-2.jpg', 'Toddler Swing Seat - second photo', 0, 1, '2026-10-04 03:10:00'),
(195, 171, 'assets/images/products/outdoor-sports/kids-skateboard-1.jpg', 'Kids Skateboard - main photo', 1, 0, '2026-10-04 10:23:00'),
(196, 171, 'assets/images/products/outdoor-sports/kids-skateboard-2.jpg', 'Kids Skateboard - second photo', 0, 1, '2026-10-04 10:23:00'),
(197, 172, 'assets/images/products/outdoor-sports/rainbow-roller-skates-1.jpg', 'Rainbow Roller Skates - main photo', 1, 0, '2026-10-04 17:36:00'),
(198, 172, 'assets/images/products/outdoor-sports/rainbow-roller-skates-2.jpg', 'Rainbow Roller Skates - second photo', 0, 1, '2026-10-04 17:36:00'),
(199, 173, 'assets/images/products/outdoor-sports/rainbow-kite-1.jpg', 'Rainbow Kite - main photo', 1, 0, '2026-10-05 00:49:00'),
(200, 173, 'assets/images/products/outdoor-sports/rainbow-kite-2.jpg', 'Rainbow Kite - second photo', 0, 1, '2026-10-05 00:49:00'),
(201, 174, 'assets/images/products/outdoor-sports/junior-badminton-set-1.jpg', 'Junior Badminton Set - main photo', 1, 0, '2026-10-05 07:02:00'),
(202, 174, 'assets/images/products/outdoor-sports/junior-badminton-set-2.jpg', 'Junior Badminton Set - second photo', 0, 1, '2026-10-05 07:02:00'),
(203, 175, 'assets/images/products/outdoor-sports/kids-bike-helmet-1.jpg', 'Kids Bike Helmet - main photo', 1, 0, '2026-10-05 14:15:00'),
(204, 175, 'assets/images/products/outdoor-sports/kids-bike-helmet-2.jpg', 'Kids Bike Helmet - second photo', 0, 1, '2026-10-05 14:15:00'),
(205, 176, 'assets/images/products/outdoor-sports/beach-sand-toys-set-1.jpg', 'Beach Sand Toys Set - main photo', 1, 0, '2026-10-05 21:28:00'),
(206, 176, 'assets/images/products/outdoor-sports/beach-sand-toys-set-2.jpg', 'Beach Sand Toys Set - second photo', 0, 1, '2026-10-05 21:28:00'),
(207, 177, 'assets/images/products/outdoor-sports/water-blaster-pump-gun-1.jpg', 'Water Blaster Pump Gun - main photo', 1, 0, '2026-10-06 04:41:00'),
(208, 177, 'assets/images/products/outdoor-sports/water-blaster-pump-gun-2.jpg', 'Water Blaster Pump Gun - second photo', 0, 1, '2026-10-06 04:41:00'),
(209, 178, 'assets/images/products/outdoor-sports/junior-tennis-racket-1.jpg', 'Junior Tennis Racket - main photo', 1, 0, '2026-10-06 11:54:00'),
(210, 178, 'assets/images/products/outdoor-sports/junior-tennis-racket-2.jpg', 'Junior Tennis Racket - second photo', 0, 1, '2026-10-06 11:54:00'),
(211, 179, 'assets/images/products/outdoor-sports/rainbow-hula-hoop-1.jpg', 'Rainbow Hula Hoop - main photo', 1, 0, '2026-10-06 18:07:00'),
(212, 179, 'assets/images/products/outdoor-sports/rainbow-hula-hoop-2.jpg', 'Rainbow Hula Hoop - second photo', 0, 1, '2026-10-06 18:07:00'),
(213, 180, 'assets/images/products/outdoor-sports/giant-bubble-wand-set-1.jpg', 'Giant Bubble Wand Set - main photo', 1, 0, '2026-10-07 01:20:00'),
(214, 180, 'assets/images/products/outdoor-sports/giant-bubble-wand-set-2.jpg', 'Giant Bubble Wand Set - second photo', 0, 1, '2026-10-07 01:20:00'),
(215, 181, 'assets/images/products/boys-toys/rc-off-road-rock-crawler-1.jpg', 'RC Off-Road Rock Crawler - main photo', 1, 0, '2026-10-01 13:33:00'),
(216, 181, 'assets/images/products/boys-toys/rc-off-road-rock-crawler-2.jpg', 'RC Off-Road Rock Crawler - second photo', 0, 1, '2026-10-01 13:33:00'),
(217, 182, 'assets/images/products/boys-toys/rc-sports-racing-car-1.jpg', 'RC Sports Racing Car - main photo', 1, 0, '2026-10-01 20:46:00'),
(218, 182, 'assets/images/products/boys-toys/rc-sports-racing-car-2.jpg', 'RC Sports Racing Car - second photo', 0, 1, '2026-10-01 20:46:00'),
(219, 183, 'assets/images/products/boys-toys/monster-truck-1.jpg', 'Monster Truck - main photo', 1, 0, '2026-10-02 03:59:00'),
(220, 183, 'assets/images/products/boys-toys/monster-truck-2.jpg', 'Monster Truck - second photo', 0, 1, '2026-10-02 03:59:00'),
(221, 184, 'assets/images/products/boys-toys/toy-excavator-1.jpg', 'Toy Excavator - main photo', 1, 0, '2026-10-02 10:12:00'),
(222, 184, 'assets/images/products/boys-toys/toy-excavator-2.jpg', 'Toy Excavator - second photo', 0, 1, '2026-10-02 10:12:00'),
(223, 185, 'assets/images/products/boys-toys/construction-wheel-loader-1.jpg', 'Construction Wheel Loader - main photo', 1, 0, '2026-10-02 17:25:00'),
(224, 185, 'assets/images/products/boys-toys/construction-wheel-loader-2.jpg', 'Construction Wheel Loader - second photo', 0, 1, '2026-10-02 17:25:00'),
(225, 186, 'assets/images/products/boys-toys/yellow-dump-truck-1.jpg', 'Yellow Dump Truck - main photo', 1, 0, '2026-10-03 00:38:00'),
(226, 186, 'assets/images/products/boys-toys/yellow-dump-truck-2.jpg', 'Yellow Dump Truck - second photo', 0, 1, '2026-10-03 00:38:00'),
(227, 187, 'assets/images/products/boys-toys/dinosaur-figures-set-12-pcs-1.jpg', 'Dinosaur Figures Set (12 pcs) - main photo', 1, 0, '2026-10-03 07:51:00'),
(228, 187, 'assets/images/products/boys-toys/dinosaur-figures-set-12-pcs-2.jpg', 'Dinosaur Figures Set (12 pcs) - second photo', 0, 1, '2026-10-03 07:51:00'),
(229, 188, 'assets/images/products/boys-toys/t-rex-dinosaur-figure-1.jpg', 'T-Rex Dinosaur Figure - main photo', 1, 0, '2026-10-03 14:04:00'),
(230, 188, 'assets/images/products/boys-toys/t-rex-dinosaur-figure-2.jpg', 'T-Rex Dinosaur Figure - second photo', 0, 1, '2026-10-03 14:04:00'),
(231, 189, 'assets/images/products/boys-toys/classic-tin-robot-1.jpg', 'Classic Tin Robot - main photo', 1, 0, '2026-10-03 21:17:00'),
(232, 189, 'assets/images/products/boys-toys/classic-tin-robot-2.jpg', 'Classic Tin Robot - second photo', 0, 1, '2026-10-03 21:17:00'),
(233, 190, 'assets/images/products/boys-toys/retro-robot-friends-set-1.jpg', 'Retro Robot Friends Set - main photo', 1, 0, '2026-10-04 04:30:00'),
(234, 190, 'assets/images/products/boys-toys/retro-robot-friends-set-2.jpg', 'Retro Robot Friends Set - second photo', 0, 1, '2026-10-04 04:30:00'),
(235, 191, 'assets/images/products/boys-toys/steam-train-railway-set-1.jpg', 'Steam Train Railway Set - main photo', 1, 0, '2026-10-04 11:43:00'),
(236, 191, 'assets/images/products/boys-toys/steam-train-railway-set-2.jpg', 'Steam Train Railway Set - second photo', 0, 1, '2026-10-04 11:43:00'),
(237, 192, 'assets/images/products/boys-toys/rescue-fire-truck-1.jpg', 'Rescue Fire Truck - main photo', 1, 0, '2026-10-04 18:56:00'),
(238, 192, 'assets/images/products/boys-toys/rescue-fire-truck-2.jpg', 'Rescue Fire Truck - second photo', 0, 1, '2026-10-04 18:56:00'),
(239, 193, 'assets/images/products/boys-toys/red-biplane-toy-1.jpg', 'Red Biplane Toy - main photo', 1, 0, '2026-10-05 01:09:00'),
(240, 193, 'assets/images/products/boys-toys/red-biplane-toy-2.jpg', 'Red Biplane Toy - second photo', 0, 1, '2026-10-05 01:09:00'),
(241, 194, 'assets/images/products/boys-toys/building-bricks-set-500-pcs-1.jpg', 'Building Bricks Set (500 pcs) - main photo', 1, 0, '2026-10-05 08:22:00'),
(242, 194, 'assets/images/products/boys-toys/building-bricks-set-500-pcs-2.jpg', 'Building Bricks Set (500 pcs) - second photo', 0, 1, '2026-10-05 08:22:00'),
(243, 195, 'assets/images/products/boys-toys/racing-car-track-set-1.jpg', 'Racing Car Track Set - main photo', 1, 0, '2026-10-05 15:35:00'),
(244, 195, 'assets/images/products/boys-toys/racing-car-track-set-2.jpg', 'Racing Car Track Set - second photo', 0, 1, '2026-10-05 15:35:00'),
(245, 196, 'assets/images/products/boys-toys/mini-camera-drone-1.jpg', 'Mini Camera Drone - main photo', 1, 0, '2026-10-05 22:48:00'),
(246, 196, 'assets/images/products/boys-toys/mini-camera-drone-2.jpg', 'Mini Camera Drone - second photo', 0, 1, '2026-10-05 22:48:00'),
(247, 197, 'assets/images/products/boys-toys/pirate-ship-model-1.jpg', 'Pirate Ship Model - main photo', 1, 0, '2026-10-06 05:01:00'),
(248, 197, 'assets/images/products/boys-toys/pirate-ship-model-2.jpg', 'Pirate Ship Model - second photo', 0, 1, '2026-10-06 05:01:00'),
(249, 198, 'assets/images/products/boys-toys/police-patrol-car-1.jpg', 'Police Patrol Car - main photo', 1, 0, '2026-10-06 12:14:00'),
(250, 198, 'assets/images/products/boys-toys/police-patrol-car-2.jpg', 'Police Patrol Car - second photo', 0, 1, '2026-10-06 12:14:00'),
(251, 199, 'assets/images/products/boys-toys/die-cast-cars-collection-1.jpg', 'Die-Cast Cars Collection - main photo', 1, 0, '2026-10-06 19:27:00'),
(252, 199, 'assets/images/products/boys-toys/die-cast-cars-collection-2.jpg', 'Die-Cast Cars Collection - second photo', 0, 1, '2026-10-06 19:27:00'),
(255, 20, 'assets/images/products/educational-toys/wooden-alphabet-and-number-puzzle-board-1.jpg', 'Wooden Alphabet & Number Puzzle Board - main photo', 1, 0, '2026-10-31 22:01:16'),
(256, 20, 'assets/images/products/educational-toys/wooden-alphabet-and-number-puzzle-board-2.jpg', 'Wooden Alphabet & Number Puzzle Board - second photo', 0, 1, '2026-10-31 22:01:16'),
(257, 21, 'assets/images/products/educational-toys/counting-beads-abacus-1.jpg', 'Counting Beads Abacus - main photo', 1, 0, '2026-10-31 22:01:16'),
(258, 21, 'assets/images/products/educational-toys/counting-beads-abacus-2.jpg', 'Counting Beads Abacus - second photo', 0, 1, '2026-10-31 22:01:16'),
(259, 22, 'assets/images/products/educational-toys/shape-sorting-cube-1.jpg', 'Shape Sorting Cube - main photo', 1, 0, '2026-10-31 22:01:16'),
(260, 22, 'assets/images/products/educational-toys/shape-sorting-cube-2.jpg', 'Shape Sorting Cube - second photo', 0, 1, '2026-10-31 22:01:16'),
(261, 23, 'assets/images/products/educational-toys/flash-card-learning-bundle-1.jpg', 'Flash Card Learning Bundle - main photo', 1, 0, '2026-10-31 22:01:16'),
(262, 23, 'assets/images/products/educational-toys/flash-card-learning-bundle-2.jpg', 'Flash Card Learning Bundle - second photo', 0, 1, '2026-10-31 22:01:16'),
(263, 24, 'assets/images/products/educational-toys/magnetic-spelling-board-1.jpg', 'Magnetic Spelling Board - main photo', 1, 0, '2026-10-31 22:01:16'),
(264, 24, 'assets/images/products/educational-toys/magnetic-spelling-board-2.jpg', 'Magnetic Spelling Board - second photo', 0, 1, '2026-10-31 22:01:16'),
(265, 25, 'assets/images/products/educational-toys/solar-system-planetarium-model-1.jpg', 'Solar System Planetarium Model - main photo', 1, 0, '2026-10-31 22:01:16'),
(266, 25, 'assets/images/products/educational-toys/solar-system-planetarium-model-2.jpg', 'Solar System Planetarium Model - second photo', 0, 1, '2026-10-31 22:01:16'),
(267, 26, 'assets/images/products/educational-toys/geo-board-graphic-array-1.jpg', 'Geo-Board Graphic Array - main photo', 1, 0, '2026-10-31 22:01:16'),
(268, 26, 'assets/images/products/educational-toys/geo-board-graphic-array-2.jpg', 'Geo-Board Graphic Array - second photo', 0, 1, '2026-10-31 22:01:16'),
(269, 27, 'assets/images/products/educational-toys/kids-microscope-starter-kit-1.jpg', 'Kids Microscope Starter Kit - main photo', 1, 0, '2026-10-31 22:01:16'),
(270, 27, 'assets/images/products/educational-toys/kids-microscope-starter-kit-2.jpg', 'Kids Microscope Starter Kit - second photo', 0, 1, '2026-10-31 22:01:16'),
(271, 28, 'assets/images/products/educational-toys/clock-telling-time-puzzle-1.jpg', 'Clock Telling Time Puzzle - main photo', 1, 0, '2026-10-31 22:01:16'),
(272, 28, 'assets/images/products/educational-toys/clock-telling-time-puzzle-2.jpg', 'Clock Telling Time Puzzle - second photo', 0, 1, '2026-10-31 22:01:16'),
(273, 29, 'assets/images/products/educational-toys/montessori-fraction-circles-1.jpg', 'Montessori Fraction Circles - main photo', 1, 0, '2026-10-31 22:01:16'),
(274, 29, 'assets/images/products/educational-toys/montessori-fraction-circles-2.jpg', 'Montessori Fraction Circles - second photo', 0, 1, '2026-10-31 22:01:16'),
(275, 30, 'assets/images/products/wooden-toys/stacking-wooden-rainbow-rings-1.jpg', 'Stacking Wooden Rainbow Rings - main photo', 1, 0, '2026-10-31 22:01:16'),
(276, 30, 'assets/images/products/wooden-toys/stacking-wooden-rainbow-rings-2.jpg', 'Stacking Wooden Rainbow Rings - second photo', 0, 1, '2026-10-31 22:01:16'),
(277, 31, 'assets/images/products/wooden-toys/wooden-pound-a-peg-bench-1.jpg', 'Wooden Pound a Peg Bench - main photo', 1, 0, '2026-10-31 22:01:16'),
(278, 31, 'assets/images/products/wooden-toys/wooden-pound-a-peg-bench-2.jpg', 'Wooden Pound a Peg Bench - second photo', 0, 1, '2026-10-31 22:01:16'),
(279, 32, 'assets/images/products/wooden-toys/wooden-train-and-station-set-1.jpg', 'Classic Wooden Train Set - main photo', 1, 0, '2026-10-31 22:01:16'),
(280, 32, 'assets/images/products/wooden-toys/wooden-train-and-station-set-2.jpg', 'Classic Wooden Train Set - second photo', 0, 1, '2026-10-31 22:01:16'),
(281, 33, 'assets/images/products/wooden-toys/wooden-pull-along-duck-1.jpg', 'Wooden Pull Along Duck - main photo', 1, 0, '2026-10-31 22:01:16'),
(282, 33, 'assets/images/products/wooden-toys/wooden-pull-along-duck-2.jpg', 'Wooden Pull Along Duck - second photo', 0, 1, '2026-10-31 22:01:16'),
(283, 34, 'assets/images/products/wooden-toys/natural-wooden-building-blocks-1.jpg', 'Natural Wooden Building Blocks - main photo', 1, 0, '2026-10-31 22:01:16'),
(284, 34, 'assets/images/products/wooden-toys/natural-wooden-building-blocks-2.jpg', 'Natural Wooden Building Blocks - second photo', 0, 1, '2026-10-31 22:01:16'),
(285, 35, 'assets/images/products/wooden-toys/wooden-tool-bench-playset-1.jpg', 'Wooden Tool Bench Playset - main photo', 1, 0, '2026-10-31 22:01:16'),
(286, 35, 'assets/images/products/wooden-toys/wooden-tool-bench-playset-2.jpg', 'Wooden Tool Bench Playset - second photo', 0, 1, '2026-10-31 22:01:16'),
(287, 36, 'assets/images/products/wooden-toys/wooden-kitchen-cutting-fruits-1.jpg', 'Wooden Kitchen Cutting Fruits - main photo', 1, 0, '2026-10-31 22:01:16'),
(288, 36, 'assets/images/products/wooden-toys/wooden-kitchen-cutting-fruits-2.jpg', 'Wooden Kitchen Cutting Fruits - second photo', 0, 1, '2026-10-31 22:01:16'),
(289, 37, 'assets/images/products/wooden-toys/wooden-lacing-beads-1.jpg', 'Wooden Lacing Beads - main photo', 1, 0, '2026-10-31 22:01:16'),
(290, 37, 'assets/images/products/wooden-toys/wooden-lacing-beads-2.jpg', 'Wooden Lacing Beads - second photo', 0, 1, '2026-10-31 22:01:16'),
(291, 38, 'assets/images/products/wooden-toys/wooden-xylophone-musical-toy-1.jpg', 'Wooden Xylophone Musical Toy - main photo', 1, 0, '2026-10-31 22:01:16'),
(292, 38, 'assets/images/products/wooden-toys/wooden-xylophone-musical-toy-2.jpg', 'Wooden Xylophone Musical Toy - second photo', 0, 1, '2026-10-31 22:01:16'),
(293, 39, 'assets/images/products/wooden-toys/wooden-noahs-ark-playset-1.jpg', 'Wooden Noahs Ark Playset - main photo', 1, 0, '2026-10-31 22:01:16'),
(294, 39, 'assets/images/products/wooden-toys/wooden-noahs-ark-playset-2.jpg', 'Wooden Noahs Ark Playset - second photo', 0, 1, '2026-10-31 22:01:16'),
(295, 40, 'assets/images/products/plush-soft-toys/organic-cotton-baby-rattle-set-1.jpg', 'Organic Cotton Baby Rattle Set - main photo', 1, 0, '2026-10-31 22:01:16'),
(296, 40, 'assets/images/products/plush-soft-toys/organic-cotton-baby-rattle-set-2.jpg', 'Organic Cotton Baby Rattle Set - second photo', 0, 1, '2026-10-31 22:01:16'),
(297, 41, 'assets/images/products/plush-soft-toys/fluffy-teddy-bear-1.jpg', 'Fluffy Teddy Bear - main photo', 1, 0, '2026-10-31 22:01:16'),
(298, 41, 'assets/images/products/plush-soft-toys/fluffy-teddy-bear-2.jpg', 'Fluffy Teddy Bear - second photo', 0, 1, '2026-10-31 22:01:16'),
(299, 42, 'assets/images/products/plush-soft-toys/musical-lullaby-star-plush-1.jpg', 'Musical Lullaby Star Plush - main photo', 1, 0, '2026-10-31 22:01:16'),
(300, 42, 'assets/images/products/plush-soft-toys/musical-lullaby-star-plush-2.jpg', 'Musical Lullaby Star Plush - second photo', 0, 1, '2026-10-31 22:01:16'),
(301, 43, 'assets/images/products/plush-soft-toys/soft-crinkle-cloth-book-1.jpg', 'Soft Crinkle Cloth Book - main photo', 1, 0, '2026-10-31 22:01:16'),
(302, 43, 'assets/images/products/plush-soft-toys/soft-crinkle-cloth-book-2.jpg', 'Soft Crinkle Cloth Book - second photo', 0, 1, '2026-10-31 22:01:16'),
(303, 44, 'assets/images/products/plush-soft-toys/plush-puppy-activity-toy-1.jpg', 'Plush Puppy Activity Toy - main photo', 1, 0, '2026-10-31 22:01:16'),
(304, 44, 'assets/images/products/plush-soft-toys/plush-puppy-activity-toy-2.jpg', 'Plush Puppy Activity Toy - second photo', 0, 1, '2026-10-31 22:01:16'),
(305, 45, 'assets/images/products/plush-soft-toys/stuffed-bunny-with-long-ears-1.jpg', 'Stuffed Bunny with Long Ears - main photo', 1, 0, '2026-10-31 22:01:16'),
(306, 45, 'assets/images/products/plush-soft-toys/stuffed-bunny-with-long-ears-2.jpg', 'Stuffed Bunny with Long Ears - second photo', 0, 1, '2026-10-31 22:01:16'),
(307, 46, 'assets/images/products/plush-soft-toys/squeaker-elephant-toy-1.jpg', 'Squeaker Elephant Toy - main photo', 1, 0, '2026-10-31 22:01:16'),
(308, 46, 'assets/images/products/plush-soft-toys/squeaker-elephant-toy-2.jpg', 'Squeaker Elephant Toy - second photo', 0, 1, '2026-10-31 22:01:16'),
(309, 47, 'assets/images/products/plush-soft-toys/comfy-security-blanket-lovey-1.jpg', 'Comfy Security Blanket Lovey - main photo', 1, 0, '2026-10-31 22:01:16'),
(310, 47, 'assets/images/products/plush-soft-toys/comfy-security-blanket-lovey-2.jpg', 'Comfy Security Blanket Lovey - second photo', 0, 1, '2026-10-31 22:01:16'),
(311, 48, 'assets/images/products/plush-soft-toys/plush-unicorn-pillow-1.jpg', 'Plush Unicorn Pillow - main photo', 1, 0, '2026-10-31 22:01:16'),
(312, 48, 'assets/images/products/plush-soft-toys/plush-unicorn-pillow-2.jpg', 'Plush Unicorn Pillow - second photo', 0, 1, '2026-10-31 22:01:16'),
(313, 49, 'assets/images/products/plush-soft-toys/weighted-dinosaur-plush-1.jpg', 'Weighted Dinosaur Plush - main photo', 1, 0, '2026-10-31 22:01:16'),
(314, 49, 'assets/images/products/plush-soft-toys/weighted-dinosaur-plush-2.jpg', 'Weighted Dinosaur Plush - second photo', 0, 1, '2026-10-31 22:01:16'),
(315, 50, 'assets/images/products/active-play-ride-ons/toddler-push-along-walker-1.jpg', 'Toddler Push Along Walker - main photo', 1, 0, '2026-10-31 22:01:16'),
(316, 50, 'assets/images/products/active-play-ride-ons/toddler-push-along-walker-2.jpg', 'Toddler Push Along Walker - second photo', 0, 1, '2026-10-31 22:01:16'),
(317, 51, 'assets/images/products/active-play-ride-ons/3-wheel-lean-to-steer-scooter-1.jpg', '3-Wheel Lean-to-Steer Scooter - main photo', 1, 0, '2026-10-31 22:01:16'),
(318, 51, 'assets/images/products/active-play-ride-ons/3-wheel-lean-to-steer-scooter-2.jpg', '3-Wheel Lean-to-Steer Scooter - second photo', 0, 1, '2026-10-31 22:01:16'),
(319, 52, 'assets/images/products/active-play-ride-ons/inflatable-hopper-bouncy-animal-1.jpg', 'Inflatable Hopper Bouncy Animal - main photo', 1, 0, '2026-10-31 22:01:16'),
(320, 52, 'assets/images/products/active-play-ride-ons/inflatable-hopper-bouncy-animal-2.jpg', 'Inflatable Hopper Bouncy Animal - second photo', 0, 1, '2026-10-31 22:01:16'),
(321, 53, 'assets/images/products/active-play-ride-ons/toddler-balance-bike-1.jpg', 'Toddler Balance Bike - main photo', 1, 0, '2026-10-31 22:01:16'),
(322, 53, 'assets/images/products/active-play-ride-ons/toddler-balance-bike-2.jpg', 'Toddler Balance Bike - second photo', 0, 1, '2026-10-31 22:01:16'),
(323, 54, 'assets/images/products/active-play-ride-ons/soft-foam-play-tunnel-1.jpg', 'Soft Foam Play Tunnel - main photo', 1, 0, '2026-10-31 22:01:16'),
(324, 54, 'assets/images/products/active-play-ride-ons/soft-foam-play-tunnel-2.jpg', 'Soft Foam Play Tunnel - second photo', 0, 1, '2026-10-31 22:01:16');

 

 
 
 

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
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

 
 
 

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
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

 
 
 

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
  `deleted_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_products_filters` (`category_id`,`gender`,`min_age`,`max_age`)
) ENGINE=InnoDB AUTO_INCREMENT=201 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

 
 
 

INSERT INTO `products` (`id`, `category_id`, `name`, `description`, `instructions`, `price`, `stock`, `gender`, `min_age`, `max_age`, `image`, `is_active`, `created_at`, `updated_at`) VALUES
(20, 20, 'Wooden Alphabet & Number Puzzle Board', 'Colorful wooden puzzle pieces helping kids learn letters and numbers early.', 'Store in a dry place. Recommended for supervised play.', 18.99, 57, 'unisex', 3, 6, 'assets/images/products/educational-toys/wooden-alphabet-and-number-puzzle-board-1.jpg', 1, '2026-10-29 10:31:18', '2026-10-31 22:01:16'),
(21, 20, 'Counting Beads Abacus', 'Traditional colorful wooden abacus for foundational math practice.', 'Wipe clean with a dry cloth.', 14.99, 45, 'unisex', 4, 7, 'assets/images/products/educational-toys/counting-beads-abacus-1.jpg', 1, '2026-10-29 10:31:18', '2026-10-31 22:01:16'),
(22, 20, 'Shape Sorting Cube', 'Classic wooden sorting box with geometric blocks.', 'Keep away from moisture.', 16.50, 30, 'unisex', 1, 3, 'assets/images/products/educational-toys/shape-sorting-cube-1.jpg', 1, '2026-10-29 10:31:18', '2026-10-31 22:01:16'),
(23, 20, 'Flash Card Learning Bundle', 'Visual cards for animals, colors, and first words.', 'Store in the included box after use.', 9.99, 50, 'unisex', 2, 5, 'assets/images/products/educational-toys/flash-card-learning-bundle-1.jpg', 1, '2026-10-29 10:31:18', '2026-10-31 22:01:16'),
(24, 20, 'Magnetic Spelling Board', 'Double-sided dry erase board with magnetic letters and numbers.', 'Avoid dropping on hard surfaces.', 24.99, 25, 'unisex', 4, 7, 'assets/images/products/educational-toys/magnetic-spelling-board-1.jpg', 1, '2026-10-29 10:31:18', '2026-10-31 22:01:16'),
(25, 20, 'Solar System Planetarium Model', 'Paint and assemble your own glow-in-the-dark solar system.', 'Adult supervision recommended during assembly.', 19.99, 20, 'unisex', 5, 7, 'assets/images/products/educational-toys/solar-system-planetarium-model-1.jpg', 1, '2026-10-29 10:31:18', '2026-10-31 22:01:16'),
(26, 20, 'Geo-Board Graphic Array', 'Wooden pegged board with rubber bands for learning shapes and area.', 'Do not over-stretch rubber bands.', 12.50, 35, 'unisex', 4, 7, 'assets/images/products/educational-toys/geo-board-graphic-array-1.jpg', 1, '2026-10-29 10:31:18', '2026-10-31 22:01:16'),
(27, 20, 'Kids Microscope Starter Kit', 'Beginner microscope with LED illumination and prepared slides.', 'Requires 2 AA batteries.', 29.99, 15, 'unisex', 6, 7, 'assets/images/products/educational-toys/kids-microscope-starter-kit-1.jpg', 1, '2026-10-29 10:31:18', '2026-10-31 22:01:16'),
(28, 20, 'Clock Telling Time Puzzle', 'Interactive clock puzzle to teach kids how to read analog and digital time.', 'Handle hands gently.', 15.00, 40, 'unisex', 5, 7, 'assets/images/products/educational-toys/clock-telling-time-puzzle-1.jpg', 1, '2026-10-29 10:31:18', '2026-10-31 22:01:16'),
(29, 20, 'Montessori Fraction Circles', 'Visual wooden math manipulation plates for basic fractions.', 'Store flat in box.', 21.00, 20, 'unisex', 5, 7, 'assets/images/products/educational-toys/montessori-fraction-circles-1.jpg', 1, '2026-10-29 10:31:18', '2026-10-31 22:01:16'),
(30, 21, 'Stacking Wooden Rainbow Rings', 'Classic montessori wooden stacking rings to develop color recognition and motor skills.', 'Wipe clean with a damp cloth.', 22.00, 30, 'unisex', 1, 3, 'assets/images/products/wooden-toys/stacking-wooden-rainbow-rings-1.jpg', 1, '2026-10-29 10:31:18', '2026-10-31 22:01:16'),
(31, 21, 'Wooden Pound a Peg Bench', 'Hand-eye coordination toy with wooden mallet and colourful pegs.', 'Inspect mallet before use.', 17.50, 25, 'unisex', 2, 4, 'assets/images/products/wooden-toys/wooden-pound-a-peg-bench-1.jpg', 1, '2026-10-29 10:31:18', '2026-10-31 22:01:16'),
(32, 21, 'Classic Wooden Train Set', 'Connecting wooden train cars and tracks for building railways.', 'Compatible with major wooden track brands.', 32.99, 20, 'unisex', 3, 7, 'assets/images/products/wooden-toys/wooden-train-and-station-set-1.jpg', 1, '2026-10-29 10:31:18', '2026-10-31 22:01:16'),
(33, 21, 'Wooden Pull Along Duck', 'Cute duck on wheels that waddles when pulled by its string.', 'Do not submerge in water.', 13.99, 29, 'unisex', 1, 3, 'assets/images/products/wooden-toys/wooden-pull-along-duck-1.jpg', 1, '2026-10-29 10:31:18', '2026-10-31 22:01:16'),
(34, 21, 'Natural Wooden Building Blocks', '100-piece natural finish solid wood block bucket.', 'Store in provided canvas bag.', 29.99, 50, 'unisex', 2, 7, 'assets/images/products/wooden-toys/natural-wooden-building-blocks-1.jpg', 1, '2026-10-29 10:31:18', '2026-10-31 22:01:16'),
(35, 21, 'Wooden Tool Bench Playset', 'Mini workbench with toy hammer, screwdriver, screws, and gears.', 'Contains small parts.', 39.99, 15, 'unisex', 3, 6, 'assets/images/products/wooden-toys/wooden-tool-bench-playset-1.jpg', 1, '2026-10-29 10:31:18', '2026-10-31 22:01:16'),
(36, 21, 'Wooden Kitchen Cutting Fruits', 'Wooden play food set held together with safe velcro and a wooden knife.', 'Wipe clean.', 19.50, 35, 'unisex', 2, 5, 'assets/images/products/wooden-toys/wooden-kitchen-cutting-fruits-1.jpg', 1, '2026-10-29 10:31:18', '2026-10-31 22:01:16'),
(37, 21, 'Wooden Lacing Beads', 'Large wooden beads with stringing laces for fine motor skills.', 'Supervise young toddlers during play.', 11.99, 40, 'unisex', 3, 5, 'assets/images/products/wooden-toys/wooden-lacing-beads-1.jpg', 1, '2026-10-29 10:31:18', '2026-10-31 22:01:16'),
(38, 21, 'Wooden Xylophone Musical Toy', 'Brightly colored wooden base with metal keys and two mallets.', 'Do not strike keys too hard.', 16.99, 25, 'unisex', 1, 4, 'assets/images/products/wooden-toys/wooden-xylophone-musical-toy-1.jpg', 1, '2026-10-29 10:31:18', '2026-10-31 22:01:16'),
(39, 21, 'Wooden Noahs Ark Playset', 'Detailed ark ship with paired wooden animal figures.', 'Store in a dry location.', 34.99, 18, 'unisex', 3, 6, 'assets/images/products/wooden-toys/wooden-noahs-ark-playset-1.jpg', 1, '2026-10-29 10:31:18', '2026-10-31 22:01:16'),
(40, 22, 'Organic Cotton Baby Rattle Set', 'Soft, organic cotton sensory teethers and rattles designed safely for newborns.', 'Machine washable on gentle cycle. Air dry.', 14.50, 40, 'unisex', 0, 1, 'assets/images/products/plush-soft-toys/organic-cotton-baby-rattle-set-1.jpg', 1, '2026-10-29 10:31:18', '2026-10-31 22:01:16'),
(41, 22, 'Fluffy Teddy Bear', 'Soft hugging plush bear companion for naptime and comfort.', 'Surface wash only.', 18.00, 50, 'unisex', 0, 7, 'assets/images/products/plush-soft-toys/fluffy-teddy-bear-1.jpg', 1, '2026-10-29 10:31:18', '2026-10-31 22:01:16'),
(42, 22, 'Musical Lullaby Star Plush', 'Gently glows and plays soothing lullabies when squeezed.', 'Remove battery module before washing.', 22.50, 30, 'unisex', 0, 2, 'assets/images/products/plush-soft-toys/musical-lullaby-star-plush-1.jpg', 1, '2026-10-29 10:31:18', '2026-10-31 22:01:16'),
(43, 22, 'Soft Crinkle Cloth Book', 'High contrast sensory fabric book with animal tails sticking out.', 'Machine washable.', 10.99, 60, 'unisex', 0, 2, 'assets/images/products/plush-soft-toys/soft-crinkle-cloth-book-1.jpg', 1, '2026-10-29 10:31:18', '2026-10-31 22:01:16'),
(44, 22, 'Plush Puppy Activity Toy', 'Interactive soft dog with teethers, mirrors, and squeakers attached.', 'Spot clean recommended.', 20.00, 25, 'unisex', 0, 3, 'assets/images/products/plush-soft-toys/plush-puppy-activity-toy-1.jpg', 1, '2026-10-29 10:31:18', '2026-10-31 22:01:16'),
(45, 22, 'Stuffed Bunny with Long Ears', 'Ultra-soft plush rabbit companion made of hypoallergenic materials.', 'Gentle machine wash.', 16.00, 45, 'unisex', 0, 7, 'assets/images/products/plush-soft-toys/stuffed-bunny-with-long-ears-1.jpg', 1, '2026-10-29 10:31:18', '2026-10-31 22:01:16'),
(46, 22, 'Squeaker Elephant Toy', 'Easy-to-grip soft elephant toy that makes a gentle squeak sound.', 'Wipe clean with damp cloth.', 9.50, 40, 'unisex', 0, 2, 'assets/images/products/plush-soft-toys/squeaker-elephant-toy-1.jpg', 1, '2026-10-29 10:31:18', '2026-10-31 22:01:16'),
(47, 22, 'Comfy Security Blanket Lovey', 'Mini soft blanket with attached animal head for soothing babies to sleep.', 'Machine washable.', 12.00, 35, 'unisex', 0, 2, 'assets/images/products/plush-soft-toys/comfy-security-blanket-lovey-1.jpg', 1, '2026-10-29 10:31:18', '2026-10-31 22:01:16'),
(48, 22, 'Plush Unicorn Pillow', 'Cuddly unicorn companion that doubles as a comfortable travel pillow.', 'Spot clean only.', 21.99, 20, 'unisex', 2, 7, 'assets/images/products/plush-soft-toys/plush-unicorn-pillow-1.jpg', 1, '2026-10-29 10:31:18', '2026-10-31 22:01:16'),
(49, 22, 'Weighted Dinosaur Plush', 'Calming weighted plush toy to give children a comforting embrace.', 'Spot clean with mild soap.', 26.50, 15, 'unisex', 3, 7, 'assets/images/products/plush-soft-toys/weighted-dinosaur-plush-1.jpg', 1, '2026-10-29 10:31:18', '2026-10-31 22:01:16'),
(50, 23, 'Toddler Push Along Walker', 'Stable wooden push walker with building blocks included for early walkers.', 'Check wheel screws periodically for tightness.', 45.00, 15, 'unisex', 1, 3, 'assets/images/products/active-play-ride-ons/toddler-push-along-walker-1.jpg', 1, '2026-10-29 10:31:18', '2026-10-31 22:01:16'),
(51, 23, '3-Wheel Lean-to-Steer Scooter', 'Stable and safe scooter with flashing LED wheels for toddlers and young children.', 'Always wear a helmet and protective pads while riding.', 59.99, 10, 'unisex', 3, 6, 'assets/images/products/active-play-ride-ons/3-wheel-lean-to-steer-scooter-1.jpg', 1, '2026-10-29 10:31:18', '2026-10-31 22:01:16'),
(52, 23, 'Inflatable Hopper Bouncy Animal', 'Stable rubber jumping animal toy with easy-to-grip ears for indoor/outdoor bouncing.', 'Keep away from sharp objects.', 24.50, 20, 'unisex', 2, 5, 'assets/images/products/active-play-ride-ons/inflatable-hopper-bouncy-animal-1.jpg', 1, '2026-10-29 10:31:18', '2026-10-31 22:01:16'),
(53, 23, 'Toddler Balance Bike', 'No-pedal training bicycle designed to build balance and coordination early.', 'Use under direct adult supervision on flat surfaces.', 65.00, 12, 'unisex', 2, 5, 'assets/images/products/active-play-ride-ons/toddler-balance-bike-1.jpg', 1, '2026-10-29 10:31:18', '2026-10-31 22:01:16'),
(54, 23, 'Soft Foam Play Tunnel', 'Collapsible crawl tunnel for active indoor and outdoor motor play.', 'Wipe clean.', 27.99, 18, 'unisex', 1, 5, 'assets/images/products/active-play-ride-ons/soft-foam-play-tunnel-1.jpg', 1, '2026-10-29 10:31:18', '2026-10-31 22:01:16'),
(55, 23, 'Junior Basketball Hoop Set', 'Adjustable height freestanding basketball hoop and soft ball for little kids.', 'Fill base with water or sand for stability.', 38.50, 14, 'unisex', 3, 7, 'uploads/products/product_6ae5e335cf1564.68817870.webp', 1, '2026-10-29 10:31:18', '2026-10-31 15:46:45'),
(56, 23, 'Activity Tummy Time Water Mat', 'Inflatable water play mat to support infant motor development and core strength.', 'Empty and dry after each use.', 11.99, 40, 'unisex', 0, 1, 'uploads/products/product_6ae5e3058f8e57.43631919.webp', 1, '2026-10-29 10:31:18', '2026-10-31 15:45:57'),
(57, 23, 'Ride-On Fire Truck Push Car', 'Foot-to-floor steering ride-on car with under-seat storage compartment.', 'Check wheel alignment regularly.', 49.99, 9, 'unisex', 1, 3, 'uploads/products/product_6ae5e28b6f4866.72016872.jpg', 1, '2026-10-29 10:31:18', '2026-10-31 15:43:55'),
(58, 23, 'Soft Foam Building Blocks Set', 'Large lightweight foam blocks for tumbling, building, and active play safely.', 'Spot clean with damp sponge.', 54.99, 8, 'unisex', 1, 4, 'uploads/products/product_6ae5e267756667.40629730.jpg', 1, '2026-10-29 10:31:18', '2026-10-31 15:43:19'),
(59, 23, 'Junior Bowling Game Set', 'Soft, lightweight pins and ball set for indoor safe bowling fun.', 'Wipe clean.', 18.50, 23, 'unisex', 2, 6, 'uploads/products/product_6ae5e22f12d423.77618926.webp', 1, '2026-10-29 10:31:18', '2026-10-31 17:06:28'),
(101, 20, 'Rainbow Wooden Abacus', 'Classic 10-row wooden abacus with bright beads that makes counting, adding and subtracting easy to see and touch.', 'Slide the beads from left to right while counting out loud. Wipe clean with a dry cloth.', 9.50, 40, 'unisex', 3, 7, 'assets/images/products/educational-toys/rainbow-wooden-abacus-1.jpg', 1, '2026-10-01 09:13:00', '2026-10-01 09:13:00'),
(102, 20, 'ABC Letter Blocks (28 pcs)', 'Solid wooden cubes printed with letters, numbers and pictures for first spelling, stacking and sorting games.', 'Build simple words together and say each letter as you stack it. Not for children under 18 months.', 12.00, 45, 'unisex', 2, 5, 'assets/images/products/educational-toys/abc-letter-blocks-28-pcs-1.jpg', 1, '2026-10-01 16:26:00', '2026-10-01 16:26:00'),
(103, 20, 'Foam Alphabet & Number Shapes', 'Soft, colourful foam letters and numbers that are safe for little hands and perfect for spelling first words.', 'Use on a table or in the bath. Rinse and air dry after water play.', 6.50, 60, 'unisex', 3, 6, 'assets/images/products/educational-toys/foam-alphabet-number-shapes-1.jpg', 1, '2026-10-01 23:39:00', '2026-10-01 23:39:00'),
(104, 20, 'Montessori Number Puzzle Board', 'Chunky wooden numbers 0-9 with a rainbow counting wheel that builds number recognition and fine motor skills.', 'Take all pieces out, then match each number to its place. Count the rainbow sections together.', 11.00, 35, 'unisex', 2, 5, 'assets/images/products/educational-toys/montessori-number-puzzle-board-1.jpg', 1, '2026-10-02 06:52:00', '2026-10-02 06:52:00'),
(105, 20, 'Junior Explorer Microscope', 'Real-glass kids microscope with LED light and prepared slides, so young scientists can explore leaves, hair and salt crystals.', 'Start with the lowest zoom. Adult supervision required with glass slides.', 32.00, 15, 'unisex', 6, 12, 'assets/images/products/educational-toys/junior-explorer-microscope-1.jpg', 1, '2026-10-02 13:05:00', '2026-10-02 13:05:00'),
(106, 20, 'Young Astronomer Telescope', 'Easy-to-use refractor telescope with tripod and two eyepieces for viewing the moon and distant landscapes.', 'Never look at the sun. Set the tripod on a flat surface and focus slowly.', 45.00, 10, 'unisex', 6, 12, 'assets/images/products/educational-toys/young-astronomer-telescope-1.jpg', 1, '2026-10-02 20:18:00', '2026-10-02 20:18:00'),
(107, 20, 'Classic Desk World Globe', 'Colourful 25 cm world globe on a sturdy stand that turns geography lessons into a spin-and-discover game.', 'Spin the globe, stop it with a finger and find the country together.', 18.50, 25, 'unisex', 6, 12, 'assets/images/products/educational-toys/classic-desk-world-globe-1.jpg', 1, '2026-10-03 03:31:00', '2026-10-03 03:31:00'),
(108, 20, 'Little Scientist Lab Kit', 'Safe chemistry set with test tubes, goggles and 20 colourful experiments explained in a picture booklet.', 'Adult supervision required. Wear the goggles and follow the booklet step by step.', 24.00, 20, 'unisex', 7, 12, 'assets/images/products/educational-toys/little-scientist-lab-kit-1.jpg', 1, '2026-10-03 10:44:00', '2026-10-03 10:44:00'),
(109, 20, 'Speed Cube 3x3', 'Smooth-turning 3x3 puzzle cube that trains memory, patience and spatial thinking.', 'Learn the beginner method one layer at a time. Do not twist with force.', 4.50, 80, 'unisex', 7, 12, 'assets/images/products/educational-toys/speed-cube-3x3-1.jpg', 1, '2026-10-03 17:57:00', '2026-10-03 17:57:00'),
(110, 20, 'Wooden Chess Set for Kids', 'Hand-finished wooden chess set with a folding board and a beginner rules card.', 'Learn how each piece moves first, then play short practice games.', 15.00, 30, 'unisex', 6, 12, 'assets/images/products/educational-toys/wooden-chess-set-for-kids-1.jpg', 1, '2026-10-04 00:10:00', '2026-10-04 00:10:00'),
(111, 20, 'Rainbow Stacking Rings', 'Soft-edged stacking rings in bright colours that teach size order, colours and hand-eye coordination.', 'Show your child how to stack the rings from biggest to smallest.', 6.00, 55, 'unisex', 1, 3, 'assets/images/products/educational-toys/rainbow-stacking-rings-1.jpg', 1, '2026-10-04 07:23:00', '2026-10-04 07:23:00'),
(112, 20, 'Wooden Shape Sorter Board', 'Colourful wooden shapes that fit onto matching pegs to learn shapes, colours and counting.', 'Name each shape and colour while your child plays.', 8.75, 40, 'unisex', 1, 3, 'assets/images/products/educational-toys/wooden-shape-sorter-board-1.jpg', 1, '2026-10-04 14:36:00', '2026-10-04 14:36:00'),
(113, 20, 'Activity Cube Learning Center', 'Wooden activity cube with bead maze, sliding shapes and counting beads on every side.', 'Adult supervision required. Wipe clean with a dry cloth.', 27.00, 15, 'unisex', 1, 4, 'assets/images/products/educational-toys/activity-cube-learning-center-1.jpg', 1, '2026-10-04 21:49:00', '2026-10-04 21:49:00'),
(114, 20, 'Solar System Planets Model', 'Desktop solar system model with all eight planets that helps kids learn their order and size.', 'Name the planets from the sun outwards. Keep small parts away from toddlers.', 19.00, 20, 'unisex', 6, 12, 'assets/images/products/educational-toys/solar-system-planets-model-1.jpg', 1, '2026-10-05 04:02:00', '2026-10-05 04:02:00'),
(115, 20, 'Dinosaur Fossil Dig Kit', 'Dig out hidden dinosaur skeletons with a chisel and brush, just like a real paleontologist.', 'Dig on a tray or outdoors. Brush away the plaster gently to reveal the fossil.', 13.50, 30, 'boys', 6, 12, 'assets/images/products/educational-toys/dinosaur-fossil-dig-kit-1.jpg', 1, '2026-10-05 11:15:00', '2026-10-05 11:15:00'),
(116, 20, 'Little Doctor Play Kit', 'Pretend doctor kit with stethoscope, thermometer and bandages for caring for teddy patients.', 'Pretend play only. Do not put items in the mouth or ears.', 13.00, 30, 'unisex', 3, 7, 'assets/images/products/educational-toys/little-doctor-play-kit-1.jpg', 1, '2026-10-05 18:28:00', '2026-10-05 18:28:00'),
(117, 20, 'Watercolor Painting Set', 'Washable watercolour palette with brushes and a paper pad for colourful creative afternoons.', 'Cover the table before painting. Rinse brushes after use.', 10.50, 35, 'girls', 4, 10, 'assets/images/products/educational-toys/watercolor-painting-set-1.jpg', 1, '2026-10-06 01:41:00', '2026-10-06 01:41:00'),
(118, 20, 'Kids Jigsaw Puzzle (100 pcs)', 'Thick-card jigsaw puzzle with large colourful pieces that builds patience and problem-solving skills.', 'Start with the edge pieces, then fill the middle.', 5.50, 50, 'unisex', 5, 10, 'assets/images/products/educational-toys/kids-jigsaw-puzzle-100-pcs-1.jpg', 1, '2026-10-06 08:54:00', '2026-10-06 08:54:00'),
(119, 20, 'Nature Explorer Magnifying Glass', 'Shatter-resistant magnifying glass with a comfortable handle for exploring bugs, leaves and stones.', 'Never use it to look at the sun or to focus sunlight.', 5.00, 60, 'unisex', 4, 10, 'assets/images/products/educational-toys/nature-explorer-magnifying-glass-1.jpg', 1, '2026-10-06 15:07:00', '2026-10-06 15:07:00'),
(120, 20, 'Color Sorting Bowls Set', 'Rainbow wooden bowls with matching pieces for sorting, counting and colour recognition.', 'Ask your child to put each piece in the bowl of the same colour.', 9.00, 40, 'unisex', 2, 5, 'assets/images/products/educational-toys/color-sorting-bowls-set-1.jpg', 1, '2026-10-06 22:20:00', '2026-10-06 22:20:00'),
(121, 21, 'Classic Wooden Train Set', 'Wooden locomotive with wagons and track pieces that connect with magnets.', 'Build the track on the floor and connect the wagons with the magnets.', 29.00, 20, 'boys', 3, 7, 'assets/images/products/wooden-toys/classic-wooden-train-set-1.jpg', 1, '2026-10-01 10:33:00', '2026-10-01 10:33:00'),
(122, 21, 'Wooden Rainbow Stacker', 'Seven-piece wooden rainbow for stacking, building and open-ended imaginative play.', 'Stack, balance or line up the arches. Wipe with a damp cloth.', 16.00, 30, 'unisex', 1, 4, 'assets/images/products/wooden-toys/wooden-rainbow-stacker-1.jpg', 1, '2026-10-01 17:46:00', '2026-10-01 17:46:00'),
(123, 21, 'Pastel Wooden Stacking Tower', 'Smooth wooden rings in soft pastel shades on a stable base, made from sustainable wood.', 'Stack the rings by size. Check pieces regularly for damage.', 10.50, 35, 'unisex', 1, 3, 'assets/images/products/wooden-toys/pastel-wooden-stacking-tower-1.jpg', 1, '2026-10-02 00:59:00', '2026-10-02 00:59:00'),
(124, 21, 'Three-Story Wooden Dollhouse', 'Large wooden dollhouse with balconies, stairs and open rooms ready to decorate.', 'Adult assembly required. Wipe with a dry cloth.', 75.00, 8, 'girls', 3, 9, 'assets/images/products/wooden-toys/three-story-wooden-dollhouse-1.jpg', 1, '2026-10-02 07:12:00', '2026-10-02 07:12:00'),
(125, 21, 'Wooden Play Kitchen', 'Wooden play kitchen with oven, sink, shelves and hooks for pretend cooking adventures.', 'Adult assembly required. Keep away from damp areas.', 95.00, 6, 'girls', 3, 7, 'assets/images/products/wooden-toys/wooden-play-kitchen-1.jpg', 1, '2026-10-02 14:25:00', '2026-10-02 14:25:00'),
(126, 21, 'Vintage Rocking Horse', 'Sturdy wooden rocking horse with smooth runners and comfortable handles.', 'Use on a flat floor. Adult supervision required for toddlers.', 58.00, 6, 'unisex', 1, 4, 'assets/images/products/wooden-toys/vintage-rocking-horse-1.jpg', 1, '2026-10-02 21:38:00', '2026-10-02 21:38:00'),
(127, 21, 'Colorful Wooden Building Blocks', 'Painted wooden blocks in many shapes and colours for towers, bridges and houses.', 'Build on a flat floor. Put blocks back in the box after play.', 21.00, 30, 'unisex', 2, 6, 'assets/images/products/wooden-toys/colorful-wooden-building-blocks-1.jpg', 1, '2026-10-03 04:51:00', '2026-10-03 04:51:00'),
(128, 21, 'Natural Wooden Cube Set (50 pcs)', 'Unpainted beech wood cubes that build balance, planning and creativity.', 'Wipe with a dry cloth. Do not soak in water.', 14.00, 30, 'unisex', 3, 8, 'assets/images/products/wooden-toys/natural-wooden-cube-set-50-pcs-1.jpg', 1, '2026-10-03 11:04:00', '2026-10-03 11:04:00'),
(129, 21, 'Rainbow Wooden Xylophone', 'Bright eight-note xylophone with two mallets for first melodies.', 'Use the mallets only. Play simple songs together.', 9.00, 40, 'unisex', 1, 5, 'assets/images/products/wooden-toys/rainbow-wooden-xylophone-1.jpg', 1, '2026-10-03 18:17:00', '2026-10-03 18:17:00'),
(130, 21, 'Wooden Pull-Along Animals', 'Cheerful wooden animals on wheels that follow little walkers on a string.', 'For walking children only. Supervise play with the string.', 8.50, 30, 'unisex', 1, 3, 'assets/images/products/wooden-toys/wooden-pull-along-animals-1.jpg', 1, '2026-10-04 01:30:00', '2026-10-04 01:30:00'),
(131, 21, 'Wooden Bead Maze Roller Coaster', 'Classic bead maze with colourful wires that trains hand-eye coordination.', 'Slide the beads along the wires. Check the base is stable before play.', 15.50, 25, 'unisex', 1, 4, 'assets/images/products/wooden-toys/wooden-bead-maze-roller-coaster-1.jpg', 1, '2026-10-04 08:43:00', '2026-10-04 08:43:00'),
(132, 21, 'Wooden Tool Box Set', 'Wooden tool box with hammer, saw, screwdriver and nuts for little builders.', 'Pretend tools only. Keep small nuts away from younger children.', 19.00, 20, 'boys', 3, 7, 'assets/images/products/wooden-toys/wooden-tool-box-set-1.jpg', 1, '2026-10-04 15:56:00', '2026-10-04 15:56:00'),
(133, 21, 'Wooden Toy Cars Collection', 'Smooth wooden cars with rolling wheels for races, garages and road trips.', 'Roll on flat floors. Wipe clean with a dry cloth.', 12.50, 35, 'boys', 2, 6, 'assets/images/products/wooden-toys/wooden-toy-cars-collection-1.jpg', 1, '2026-10-04 22:09:00', '2026-10-04 22:09:00'),
(134, 21, 'Wooden Biplane', 'Bright yellow wooden biplane with spinning propeller and rolling wheels.', 'Spin the propeller gently. Not suitable for outdoor wet play.', 11.00, 25, 'boys', 3, 8, 'assets/images/products/wooden-toys/wooden-biplane-1.jpg', 1, '2026-10-05 05:22:00', '2026-10-05 05:22:00'),
(135, 21, 'Wooden Food Cutting Set', 'Wooden fruits and vegetables joined with velcro that kids slice with a wooden knife.', 'Wipe with a damp cloth. Do not soak in water.', 10.50, 35, 'girls', 2, 6, 'assets/images/products/wooden-toys/wooden-food-cutting-set-1.jpg', 1, '2026-10-05 12:35:00', '2026-10-05 12:35:00'),
(136, 21, 'Wooden Doll Pram', 'Sturdy doll pram that little ones push around the house while caring for their dolls.', 'Use on flat floors only, away from stairs.', 34.00, 10, 'girls', 2, 5, 'assets/images/products/wooden-toys/wooden-doll-pram-1.jpg', 1, '2026-10-05 19:48:00', '2026-10-05 19:48:00'),
(137, 21, 'Wooden Spinning Tops Set', 'Hand-painted wooden spinning tops that spin for a long time on smooth surfaces.', 'Twist the handle quickly and let go on a flat table.', 6.00, 50, 'unisex', 4, 10, 'assets/images/products/wooden-toys/wooden-spinning-tops-set-1.jpg', 1, '2026-10-06 02:01:00', '2026-10-06 02:01:00'),
(138, 21, 'Wooden Sailboat Model', 'Classic wooden sailboat with cotton sails that floats in pools and ponds.', 'Float only under adult supervision. Dry after use.', 17.50, 15, 'boys', 4, 10, 'assets/images/products/wooden-toys/wooden-sailboat-model-1.jpg', 1, '2026-10-06 09:14:00', '2026-10-06 09:14:00'),
(139, 21, 'Wooden Brain Teaser Puzzle', 'Interlocking wooden puzzle that challenges kids to take it apart and rebuild it.', 'Take it apart slowly and remember the order of the pieces.', 5.50, 45, 'unisex', 7, 12, 'assets/images/products/wooden-toys/wooden-brain-teaser-puzzle-1.jpg', 1, '2026-10-06 16:27:00', '2026-10-06 16:27:00'),
(140, 21, 'Wooden Alphabet Number Cubes', 'Engraved wooden cubes with letters, numbers and pictures for learning through play.', 'Stack the cubes and read the letters together.', 10.00, 40, 'unisex', 2, 5, 'assets/images/products/wooden-toys/wooden-alphabet-number-cubes-1.jpg', 1, '2026-10-06 23:40:00', '2026-10-06 23:40:00'),
(141, 22, 'Porcelain Princess Doll', 'Elegant collectible doll with a lace dress, bonnet and long soft hair.', 'Handle with care. Keep away from younger siblings.', 28.00, 15, 'girls', 5, 12, 'assets/images/products/girls-toys/porcelain-princess-doll-1.jpg', 1, '2026-10-01 11:53:00', '2026-10-01 11:53:00'),
(142, 22, 'Vintage Classic Doll', 'Classic doll with blinking eyes, curly hair and a pretty tartan dress.', 'Comb the hair gently. Surface wash only.', 22.00, 15, 'girls', 4, 10, 'assets/images/products/girls-toys/vintage-classic-doll-1.jpg', 1, '2026-10-01 18:06:00', '2026-10-01 18:06:00'),
(143, 22, 'Knitted Rag Doll', 'Soft hand-knitted rag doll with colourful yarn hair, perfect for cuddles.', 'Hand wash in cold water. Air dry.', 12.00, 25, 'girls', 1, 6, 'assets/images/products/girls-toys/knitted-rag-doll-1.jpg', 1, '2026-10-02 01:19:00', '2026-10-02 01:19:00'),
(144, 22, 'Fluffy Unicorn Plush', 'Super-soft unicorn plush with a golden horn and a rainbow mane.', 'Surface wash only. Air dry.', 14.50, 30, 'girls', 1, 8, 'assets/images/products/girls-toys/fluffy-unicorn-plush-1.jpg', 1, '2026-10-02 08:32:00', '2026-10-02 08:32:00'),
(145, 22, 'Sparkle Eyes Unicorn Plush', 'Pink unicorn plush with big sparkly eyes and glitter horn.', 'Surface wash only. Not for children under 12 months.', 11.00, 30, 'girls', 2, 8, 'assets/images/products/girls-toys/sparkle-eyes-unicorn-plush-1.jpg', 1, '2026-10-02 15:45:00', '2026-10-02 15:45:00'),
(146, 22, 'Soft Bunny Plush', 'Floppy-eared bunny plush made from extra-soft fabric, safe for little ones.', 'Machine wash on gentle cycle in a laundry bag.', 10.00, 35, 'girls', 1, 6, 'assets/images/products/girls-toys/soft-bunny-plush-1.jpg', 1, '2026-10-02 22:58:00', '2026-10-02 22:58:00'),
(147, 22, 'Love Teddy Bear Gift', 'Soft teddy bear holding a red heart, a sweet gift for birthdays and special days.', 'Surface wash only. Air dry.', 13.00, 25, 'girls', 1, 10, 'assets/images/products/girls-toys/love-teddy-bear-gift-1.jpg', 1, '2026-10-03 05:11:00', '2026-10-03 05:11:00'),
(150, 20, 'Bead Jewelry Making Kit', 'Over 500 colourful beads and letter beads to design bracelets and necklaces.', 'Small parts: not for children under 3. Sort beads in the case after play.', 9.50, 40, 'girls', 6, 12, 'assets/images/products/girls-toys/bead-jewelry-making-kit-1.jpg', 1, '2026-10-04 02:50:00', '2026-10-04 02:50:00'),
(151, 20, 'Friendship Bracelet Kit', 'Colourful threads and a step-by-step guide to weave friendship bracelets.', 'Follow the guide patterns from easy to hard.', 7.00, 40, 'girls', 6, 12, 'assets/images/products/girls-toys/friendship-bracelet-kit-1.jpg', 1, '2026-10-04 09:03:00', '2026-10-04 09:03:00'),
(153, 23, 'Teepee Play Tent', 'Cosy cotton teepee with wooden poles and a window for reading corners and sleepovers.', 'Adult assembly required. Spot clean only.', 39.00, 10, 'girls', 3, 9, 'assets/images/products/girls-toys/teepee-play-tent-1.jpg', 1, '2026-10-04 23:29:00', '2026-10-04 23:29:00'),
(157, 20, 'Rainbow Modeling Clay Kit', 'Soft, non-toxic modeling clay in rainbow colours with shaping tools.', 'Close the tubs tightly after play so the clay stays soft. Do not eat.', 7.50, 45, 'girls', 3, 8, 'assets/images/products/girls-toys/rainbow-modeling-clay-kit-1.jpg', 1, '2026-10-06 03:21:00', '2026-10-06 03:21:00'),
(158, 20, 'Mini Grand Piano', 'Elegant mini grand piano with real-sounding keys and a matching stool.', 'Requires 2 AA batteries. Keep the volume low for small ears.', 26.00, 12, 'girls', 3, 8, 'assets/images/products/girls-toys/mini-grand-piano-1.jpg', 1, '2026-10-06 10:34:00', '2026-10-06 10:34:00'),
(159, 20, 'Jumbo Crayons Coloring Set', 'Bright jumbo crayons that are easy to hold, for drawing and colouring adventures.', 'Use on paper only. Not for children under 3.', 5.00, 60, 'girls', 3, 8, 'assets/images/products/girls-toys/jumbo-crayons-coloring-set-1.jpg', 1, '2026-10-06 17:47:00', '2026-10-06 17:47:00'),
(160, 22, 'Cuddly Kitty Plush', 'Fluffy purple kitty plush with soft fur and embroidered face.', 'Surface wash only. Air dry.', 9.50, 30, 'girls', 1, 8, 'assets/images/products/girls-toys/cuddly-kitty-plush-1.jpg', 1, '2026-10-07 00:00:00', '2026-10-07 00:00:00'),
(161, 23, 'Kids Balance Bike', 'Lightweight pedal-free balance bike that helps toddlers learn steering and balance.', 'Adjust the seat so both feet touch the ground. Always wear a helmet.', 45.00, 15, 'unisex', 2, 5, 'assets/images/products/outdoor-sports/kids-balance-bike-1.jpg', 1, '2026-10-01 12:13:00', '2026-10-01 12:13:00'),
(162, 23, 'Pink Balance Bike', 'Pedal-free balance bike in a girls\' colour with soft grips and puncture-proof tyres.', 'Adjust the seat so both feet touch the ground. Always wear a helmet.', 45.00, 12, 'girls', 2, 5, 'assets/images/products/outdoor-sports/pink-balance-bike-1.jpg', 1, '2026-10-01 19:26:00', '2026-10-01 19:26:00'),
(163, 23, '16-inch Blue Kids Bicycle', 'Steel-frame kids bicycle with removable training wheels and front hand brake.', 'Check tyre pressure and brakes before every ride. Always wear a helmet.', 89.00, 10, 'boys', 4, 8, 'assets/images/products/outdoor-sports/16-inch-blue-kids-bicycle-1.jpg', 1, '2026-10-02 02:39:00', '2026-10-02 02:39:00'),
(164, 23, '16-inch Pink Kids Bicycle', 'Pretty kids bicycle with training wheels, chain guard and front basket.', 'Check tyre pressure and brakes before every ride. Always wear a helmet.', 89.00, 10, 'girls', 4, 8, 'assets/images/products/outdoor-sports/16-inch-pink-kids-bicycle-1.jpg', 1, '2026-10-02 09:52:00', '2026-10-02 09:52:00'),
(165, 23, 'Foldable Kick Scooter', 'Height-adjustable scooter with smooth wheels and rear foot brake.', 'Ride on flat, dry surfaces only. Wear a helmet and knee pads.', 35.00, 20, 'unisex', 4, 10, 'assets/images/products/outdoor-sports/foldable-kick-scooter-1.jpg', 1, '2026-10-02 16:05:00', '2026-10-02 16:05:00'),
(166, 23, 'Pink Kids Scooter', 'Pink scooter with light-up wheels and an easy-fold frame.', 'Ride on flat, dry surfaces only. Wear a helmet and knee pads.', 38.00, 15, 'girls', 5, 10, 'assets/images/products/outdoor-sports/pink-kids-scooter-1.jpg', 1, '2026-10-02 23:18:00', '2026-10-02 23:18:00'),
(167, 23, 'Kids Football Size 4', 'Soft-touch stitched football sized for young players.', 'Inflate to the pressure printed on the ball. Play on grass or turf.', 8.50, 60, 'unisex', 5, 12, 'assets/images/products/outdoor-sports/kids-football-size-4-1.jpg', 1, '2026-10-03 06:31:00', '2026-10-03 06:31:00'),
(168, 23, 'Adjustable Basketball Hoop', 'Free-standing hoop that grows with your child, with net and ball included.', 'Fill the base with sand or water before use. Do not hang on the rim.', 27.50, 18, 'unisex', 5, 12, 'assets/images/products/outdoor-sports/adjustable-basketball-hoop-1.jpg', 1, '2026-10-03 13:44:00', '2026-10-03 13:44:00'),
(169, 23, 'Garden Trampoline with Safety Net', 'Outdoor trampoline with padded frame and enclosure net for safe bouncing.', 'One child at a time. Adult supervision required.', 135.00, 5, 'unisex', 4, 12, 'assets/images/products/outdoor-sports/garden-trampoline-with-safety-net-1.jpg', 1, '2026-10-03 20:57:00', '2026-10-03 20:57:00'),
(170, 23, 'Toddler Swing Seat', 'Bucket swing seat with safety belt and adjustable ropes for trees or frames.', 'Check ropes before every use. Adult supervision required.', 24.00, 15, 'unisex', 1, 4, 'assets/images/products/outdoor-sports/toddler-swing-seat-1.jpg', 1, '2026-10-04 03:10:00', '2026-10-04 03:10:00'),
(171, 23, 'Kids Skateboard', 'Maple deck skateboard with grip tape and smooth wheels for beginners.', 'Always wear a helmet and pads. Ride away from traffic.', 29.00, 15, 'boys', 6, 12, 'assets/images/products/outdoor-sports/kids-skateboard-1.jpg', 1, '2026-10-04 10:23:00', '2026-10-04 10:23:00'),
(172, 23, 'Rainbow Roller Skates', 'Adjustable quad roller skates with comfy padding and rainbow details.', 'Wear a helmet, knee and wrist pads. Skate on smooth ground.', 32.00, 15, 'girls', 6, 12, 'assets/images/products/outdoor-sports/rainbow-roller-skates-1.jpg', 1, '2026-10-04 17:36:00', '2026-10-04 17:36:00'),
(173, 23, 'Rainbow Kite', 'Easy-to-fly kite with long colourful tails and 50 m string.', 'Fly in open spaces away from power lines.', 7.50, 40, 'unisex', 5, 12, 'assets/images/products/outdoor-sports/rainbow-kite-1.jpg', 1, '2026-10-05 00:49:00', '2026-10-05 00:49:00'),
(174, 23, 'Junior Badminton Set', 'Two lightweight rackets with shuttlecocks for backyard games.', 'Play in open areas. Replace damaged shuttlecocks.', 12.00, 30, 'unisex', 6, 12, 'assets/images/products/outdoor-sports/junior-badminton-set-1.jpg', 1, '2026-10-05 07:02:00', '2026-10-05 07:02:00'),
(175, 23, 'Kids Bike Helmet', 'Lightweight ventilated helmet with adjustable strap for bikes and scooters.', 'Fit snugly and fasten the strap before every ride.', 16.00, 40, 'unisex', 3, 10, 'assets/images/products/outdoor-sports/kids-bike-helmet-1.jpg', 1, '2026-10-05 14:15:00', '2026-10-05 14:15:00'),
(176, 23, 'Beach Sand Toys Set', 'Bucket, spade, rake and moulds for building sandcastles at the beach or sandbox.', 'Rinse with clean water after the beach.', 8.25, 40, 'unisex', 2, 6, 'assets/images/products/outdoor-sports/beach-sand-toys-set-1.jpg', 1, '2026-10-05 21:28:00', '2026-10-05 21:28:00'),
(177, 23, 'Water Blaster Pump Gun', 'Pump-action water blaster with long range and easy-fill tank.', 'Do not aim at faces. Use outdoors only.', 7.50, 50, 'boys', 5, 12, 'assets/images/products/outdoor-sports/water-blaster-pump-gun-1.jpg', 1, '2026-10-06 04:41:00', '2026-10-06 04:41:00'),
(178, 23, 'Junior Tennis Racket', 'Lightweight junior racket with a cover and two soft tennis balls.', 'Choose the right size for your child\'s height.', 14.00, 25, 'unisex', 6, 12, 'assets/images/products/outdoor-sports/junior-tennis-racket-1.jpg', 1, '2026-10-06 11:54:00', '2026-10-06 11:54:00'),
(179, 23, 'Rainbow Hula Hoop', 'Colourful adjustable hula hoop for fitness, dance and playground games.', 'Use in open spaces away from furniture.', 5.50, 40, 'girls', 4, 12, 'assets/images/products/outdoor-sports/rainbow-hula-hoop-1.jpg', 1, '2026-10-06 18:07:00', '2026-10-06 18:07:00'),
(180, 23, 'Giant Bubble Wand Set', 'Bubble wands with non-toxic solution for giant bubbles in the garden.', 'Use outdoors. Do not drink the solution.', 6.00, 50, 'unisex', 3, 8, 'assets/images/products/outdoor-sports/giant-bubble-wand-set-1.jpg', 1, '2026-10-07 01:20:00', '2026-10-07 01:20:00'),
(181, 23, 'RC Off-Road Rock Crawler', 'Rugged 4x4 remote control crawler with shock absorbers and rechargeable battery.', 'Charge fully before first use. Do not drive through water.', 42.00, 15, 'boys', 6, 12, 'assets/images/products/boys-toys/rc-off-road-rock-crawler-1.jpg', 1, '2026-10-01 13:33:00', '2026-10-01 13:33:00'),
(182, 23, 'RC Sports Racing Car', 'Fast remote control sports car with 2.4 GHz remote and LED lights.', 'Charge fully before first use. Drive on smooth surfaces.', 38.00, 15, 'boys', 6, 12, 'assets/images/products/boys-toys/rc-sports-racing-car-1.jpg', 1, '2026-10-01 20:46:00', '2026-10-01 20:46:00'),
(183, 23, 'Monster Truck', 'Big-wheel monster truck with friction motor and strong suspension.', 'Push and release to drive. Wipe clean, do not submerge.', 16.00, 25, 'boys', 4, 10, 'assets/images/products/boys-toys/monster-truck-1.jpg', 1, '2026-10-02 03:59:00', '2026-10-02 03:59:00'),
(184, 23, 'Toy Excavator', 'Construction excavator with moving arm and digging bucket.', 'Play in sand or on the floor. Do not pull the arm too far.', 14.50, 25, 'boys', 3, 8, 'assets/images/products/boys-toys/toy-excavator-1.jpg', 1, '2026-10-02 10:12:00', '2026-10-02 10:12:00'),
(185, 23, 'Construction Wheel Loader', 'Heavy-duty toy wheel loader with lifting front scoop.', 'Wipe clean after sand play.', 16.50, 20, 'boys', 3, 8, 'assets/images/products/boys-toys/construction-wheel-loader-1.jpg', 1, '2026-10-02 17:25:00', '2026-10-02 17:25:00'),
(186, 23, 'Yellow Dump Truck', 'Chunky dump truck with tipping bed for hauling sand and blocks.', 'Wipe clean after outdoor play.', 12.00, 30, 'boys', 3, 7, 'assets/images/products/boys-toys/yellow-dump-truck-1.jpg', 1, '2026-10-03 00:38:00', '2026-10-03 00:38:00'),
(187, 20, 'Dinosaur Figures Set (12 pcs)', 'Detailed dinosaur figures in bright colours with name cards.', 'Use the cards to match each dinosaur to its name.', 15.00, 30, 'boys', 3, 9, 'assets/images/products/boys-toys/dinosaur-figures-set-12-pcs-1.jpg', 1, '2026-10-03 07:51:00', '2026-10-03 07:51:00'),
(188, 20, 'T-Rex Dinosaur Figure', 'Realistic T-Rex figure with textured skin and posable jaw.', 'Wipe clean with a damp cloth.', 9.50, 35, 'boys', 3, 10, 'assets/images/products/boys-toys/t-rex-dinosaur-figure-1.jpg', 1, '2026-10-03 14:04:00', '2026-10-03 14:04:00'),
(189, 20, 'Classic Tin Robot', 'Retro wind-up tin robot that walks and swings its arms.', 'Wind the key gently. Do not overwind.', 18.00, 15, 'boys', 5, 12, 'assets/images/products/boys-toys/classic-tin-robot-1.jpg', 1, '2026-10-03 21:17:00', '2026-10-03 21:17:00'),
(190, 20, 'Retro Robot Friends Set', 'Set of retro-style robots in bright colours for display and play.', 'Keep away from water. Wipe with a dry cloth.', 22.00, 12, 'boys', 5, 12, 'assets/images/products/boys-toys/retro-robot-friends-set-1.jpg', 1, '2026-10-04 04:30:00', '2026-10-04 04:30:00'),
(191, 23, 'Steam Train Railway Set', 'Battery-powered steam locomotive with wagons, track and station.', 'Requires 2 AA batteries. Build the track on a flat floor.', 48.00, 10, 'boys', 4, 10, 'assets/images/products/boys-toys/steam-train-railway-set-1.jpg', 1, '2026-10-04 11:43:00', '2026-10-04 11:43:00'),
(192, 23, 'Rescue Fire Truck', 'Rescue vehicle with siren sounds, lights and rolling wheels.', 'Requires 3 AAA batteries (included). Switch off after play.', 13.50, 25, 'boys', 3, 8, 'assets/images/products/boys-toys/rescue-fire-truck-1.jpg', 1, '2026-10-04 18:56:00', '2026-10-04 18:56:00'),
(193, 23, 'Red Biplane Toy', 'Classic red biplane with spinning propeller for high-flying adventures.', 'Spin the propeller gently. Keep away from water.', 11.50, 25, 'boys', 3, 9, 'assets/images/products/boys-toys/red-biplane-toy-1.jpg', 1, '2026-10-05 01:09:00', '2026-10-05 01:09:00'),
(194, 20, 'Building Bricks Set (500 pcs)', 'Compatible building bricks in many colours with an ideas booklet.', 'Small parts: not for children under 3. Sort bricks by colour.', 26.00, 25, 'boys', 5, 12, 'assets/images/products/boys-toys/building-bricks-set-500-pcs-1.jpg', 1, '2026-10-05 08:22:00', '2026-10-05 08:22:00'),
(195, 23, 'Racing Car Track Set', 'Flexible race track with curves, start line and two racing cars.', 'Build the track on the floor. Keep small parts away from toddlers.', 28.00, 20, 'boys', 4, 10, 'assets/images/products/boys-toys/racing-car-track-set-1.jpg', 1, '2026-10-05 15:35:00', '2026-10-05 15:35:00'),
(196, 20, 'Mini Camera Drone', 'Easy-to-fly mini drone with altitude hold, one-key take-off and HD camera.', 'Fly outdoors in calm weather. Adult supervision required.', 79.00, 8, 'boys', 10, 12, 'assets/images/products/boys-toys/mini-camera-drone-1.jpg', 1, '2026-10-05 22:48:00', '2026-10-05 22:48:00'),
(197, 21, 'Pirate Ship Model', 'Detailed wooden pirate ship model with sails and cannons.', 'Display piece. Keep away from water and dust regularly.', 35.00, 10, 'boys', 6, 12, 'assets/images/products/boys-toys/pirate-ship-model-1.jpg', 1, '2026-10-06 05:01:00', '2026-10-06 05:01:00'),
(198, 23, 'Police Patrol Car', 'Friendly police car with rolling wheels and rounded edges for little hands.', 'Wipe clean with a damp cloth.', 9.00, 35, 'boys', 2, 6, 'assets/images/products/boys-toys/police-patrol-car-1.jpg', 1, '2026-10-06 12:14:00', '2026-10-06 12:14:00'),
(199, 23, 'Die-Cast Cars Collection', 'Metal toy cars with free-rolling wheels and classic designs.', 'Small parts may come loose: check cars regularly.', 12.00, 40, 'boys', 3, 10, 'assets/images/products/boys-toys/die-cast-cars-collection-1.jpg', 1, '2026-10-06 19:27:00', '2026-10-06 19:27:00');

 

 
 
 

CREATE TABLE IF NOT EXISTS `reviews` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `product_id` int(10) UNSIGNED NOT NULL,
  `user_id` int(10) UNSIGNED NOT NULL,
  `rating` tinyint(3) UNSIGNED NOT NULL,
  `comment` varchar(500) DEFAULT NULL,
  `is_visible` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_review_once` (`product_id`,`user_id`),
  KEY `idx_reviews_user` (`user_id`)
) ENGINE=InnoDB AUTO_INCREMENT=41 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

 
 
 

INSERT INTO `reviews` (`id`, `product_id`, `user_id`, `rating`, `comment`, `is_visible`, `created_at`) VALUES
(20, 20, 1, 5, 'Great for my preschooler learning letters!', 0, '2026-10-29 10:31:18'),
(21, 40, 3, 5, 'So soft and safe for my newborn baby.', 0, '2026-10-29 10:31:18'),
(22, 27, 4, 5, 'omar', 1, '2026-10-30 20:32:34'),
(23, 59, 4, 4, 'omar', 1, '2026-10-30 20:33:14'),
(24, 33, 5, 4, 'ضصثقفغعهخ', 1, '2026-10-31 00:13:01'),
(25, 110, 1, 5, 'Beautiful wooden pieces, my son learned the moves in one weekend.', 1, '2026-10-15 10:00:00'),
(26, 110, 4, 4, 'Good quality board, the folding hinge could be a bit stronger.', 1, '2026-10-16 03:00:00'),
(27, 101, 1, 5, 'Bright colours and very solid. Great for practising maths at home.', 1, '2026-10-16 20:00:00'),
(28, 105, 4, 5, 'My daughter checks every leaf in the garden now. Clear image and easy to focus.', 1, '2026-10-17 13:00:00'),
(29, 161, 1, 5, 'Light enough for my 3 year old to carry. He was gliding after two days.', 1, '2026-10-18 06:00:00'),
(30, 144, 4, 5, 'So soft! It is the first thing she takes to bed every night.', 1, '2026-10-18 23:00:00'),
(31, 124, 1, 4, 'Lovely dollhouse, assembly took about an hour but the result is worth it.', 1, '2026-10-19 16:00:00'),
(32, 181, 4, 5, 'Climbs everything in the backyard and the battery lasts long.', 1, '2026-10-20 09:00:00'),
(33, 194, 1, 5, 'Fits with our other bricks perfectly, lots of colours.', 1, '2026-10-21 02:00:00'),
(34, 187, 4, 4, 'Detailed figures, kids love the name cards.', 1, '2026-10-21 19:00:00'),
(35, 125, 4, 5, 'Sturdy and beautiful. My kids cook for us every evening now.', 1, '2026-10-22 12:00:00'),
(36, 108, 1, 4, 'Fun experiments with clear instructions. Needs adult help for some steps.', 1, '2026-10-23 05:00:00'),
(37, 172, 1, 5, 'Comfortable and adjustable, perfect birthday gift.', 1, '2026-10-23 22:00:00'),
(38, 121, 4, 5, 'Magnets connect easily and the wood feels premium.', 1, '2026-10-24 15:00:00'),
(39, 153, 1, 5, 'Turned her corner into a reading nook. Very cute.', 1, '2026-10-25 08:00:00'),
(40, 109, 4, 4, 'Turns smoothly, my son solved it after watching the beginner guide.', 1, '2026-10-26 01:00:00');

 

 
 
 

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

 
 
 

INSERT INTO `search_history` (`search_id`, `user_id`, `search_term`, `results_count`, `searched_at`) VALUES
(1, 2, 'puzzle', 1, '2026-10-29 10:31:18'),
(2, 3, 'robot', 1, '2026-10-29 10:31:18'),
(20, 1, 'puzzle', 1, '2026-10-29 10:31:18'),
(21, 3, 'rattle', 1, '2026-10-29 10:31:18'),
(53, 4, 'Junior Bowling Game Set', 1, '2026-10-30 20:28:07');

 

 
 
 

CREATE TABLE IF NOT EXISTS `users` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `first_name` varchar(50) NOT NULL,
  `last_name` varchar(50) NOT NULL,
  `email` varchar(120) NOT NULL,
  `password_hash` varchar(255) DEFAULT NULL,
  `google_id` varchar(64) DEFAULT NULL,
  `profile_image` varchar(255) DEFAULT NULL,
  `birth_date` date DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `role` enum('customer','admin') NOT NULL DEFAULT 'customer',
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_users_email` (`email`),
  UNIQUE KEY `uq_users_google_id` (`google_id`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

 
 
 

INSERT INTO `users` (`id`, `first_name`, `last_name`, `email`, `password_hash`, `google_id`, `profile_image`, `birth_date`, `phone`, `role`, `is_active`, `created_at`) VALUES
(1, 'Omar', 'Saleh', 'omar@kiddo.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', NULL, NULL, NULL, '+962791111111', 'customer', 1, '2026-10-29 10:31:18'),
(2, 'Admin', 'User', 'admin123@mail.com', '$2y$10$4wlVeLBGCTb93ysR/2K.welDq/KTV5WE1XV1AXzeGGPz6eblhmGMO', NULL, NULL, NULL, NULL, 'admin', 1, '2026-10-29 13:02:00'),
(3, 'Rema', 'Ahmad', 'rema@kiddo.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', NULL, NULL, '2020-08-22', '+962792222222', 'customer', 1, '2026-10-29 10:31:18'),
(4, 'omars', 'qadarahs', 'omar@omar.com', '$2y$10$.I9kQSmS6TVAnBjtBxCVe.vXlxgEPu2fScopZdMam8P5eQphSAZjW', NULL, NULL, NULL, '0791234567', 'customer', 1, '2026-10-30 17:56:00'),
(5, 'Omar', 'Qadarah', 'omarqadarah1@gmail.com', NULL, '114175494943700861038', NULL, '1997-08-13', '0792345678', 'customer', 1, '2026-10-30 22:24:47'),
(6, 'omer', 'nael', 'qw@qw.com', '$2y$10$Sh73uvQ7PrJVF9nLV./.ueiEY7rpJ.7Ybt9dRz6nVc7l5BPZmg4qu', NULL, NULL, NULL, NULL, 'customer', 0, '2026-10-30 22:34:09'),
(7, 'omar', 'qad', 'omarss@naelss.com', '$2y$10$bQvxQlVjRB7JEiLDHE9lku0ewYlp6DhrEXaL2xM4MCuSoOLtGtqIW', NULL, NULL, '2026-10-31', '0791234567', 'admin', 0, '2026-10-31 02:37:56');

 
 
 

 
 
 
ALTER TABLE `admin_action_usage`
  ADD CONSTRAINT `fk_admin_action_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

 
 
 
ALTER TABLE `discounts`
  ADD CONSTRAINT `fk_discounts_category` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_discounts_product` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

 
 
 
ALTER TABLE `images`
  ADD CONSTRAINT `fk_images_product` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

 
 
 
ALTER TABLE `orders`
  ADD CONSTRAINT `fk_orders_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON UPDATE CASCADE;

 
 
 
ALTER TABLE `order_items`
  ADD CONSTRAINT `fk_items_order` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_items_product` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;

 
 
 
ALTER TABLE `products`
  ADD CONSTRAINT `fk_products_category` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON UPDATE CASCADE;

 
 
 
ALTER TABLE `reviews`
  ADD CONSTRAINT `fk_reviews_product` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_reviews_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

 
 
 
ALTER TABLE `search_history`
  ADD CONSTRAINT `fk_search_history_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;
COMMIT;

 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT  ;
 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS  ;
 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION  ;
