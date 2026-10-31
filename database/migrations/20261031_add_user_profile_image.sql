-- Run once on databases created before customer profile image uploads were added.
ALTER TABLE `users`
  ADD COLUMN `profile_image` varchar(255) DEFAULT NULL AFTER `google_id`;
