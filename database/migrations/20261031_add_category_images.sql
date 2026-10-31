-- Run once on databases created before category image uploads were added.
ALTER TABLE `categories`
  ADD COLUMN `image` varchar(255) DEFAULT NULL AFTER `icon`;
