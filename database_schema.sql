-- Database Schema for Amawal (Anamek Dictionary)
-- Dumped on: 2026-02-04

--
-- Table structure for table `users`
--
CREATE TABLE `users` (
  `id` int NOT NULL AUTO_INCREMENT,
  `username` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `google_id` varchar(255) DEFAULT NULL,
  `password_hash` varchar(255) DEFAULT NULL,
  `full_name` varchar(100) DEFAULT NULL,
  `avatar_url` varchar(255) DEFAULT NULL,
  `bio` text,
  `native_language` varchar(50) DEFAULT NULL,
  `learning_languages` text,
  `language_expertise` text,
  `contribution_points` int DEFAULT '0',
  `reputation_score` int DEFAULT '0',
  `user_type` enum('regular','contributor','expert','moderator','admin') DEFAULT 'regular',
  `email_verified` tinyint(1) DEFAULT '0',
  `verification_token` varchar(100) DEFAULT NULL,
  `reset_token` varchar(100) DEFAULT NULL,
  `reset_token_expires` timestamp NULL DEFAULT NULL,
  `preferences` json DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `last_login` timestamp NULL DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT '1',
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`),
  UNIQUE KEY `email` (`email`),
  UNIQUE KEY `google_id` (`google_id`),
  KEY `idx_username` (`username`),
  KEY `idx_email` (`email`),
  KEY `idx_user_type` (`user_type`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Table structure for table `words`
--
CREATE TABLE `words` (
  `id` int NOT NULL AUTO_INCREMENT,
  `word_tfng` text NOT NULL,
  `word_lat` text NOT NULL,
  `definition_tfng` text,
  `definition_lat` text,
  `translation_fr` text,
  `plural_tfng` text,
  `plural_lat` text,
  `feminine_tfng` text,
  `feminine_lat` text,
  `annexed_tfng` text,
  `annexed_lat` text,
  `root_tfng` text,
  `root_lat` text,
  `part_of_speech` text,
  `example_tfng` text,
  `example_lat` text,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_word_lat` (`word_lat`(255)),
  KEY `idx_word_tfng` (`word_tfng`(255))
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Table structure for table `verb_conjugations`
--
CREATE TABLE `verb_conjugations` (
  `id` int NOT NULL AUTO_INCREMENT,
  `word_id` int NOT NULL,
  `tense` varchar(50) DEFAULT NULL,
  `aspect` varchar(50) DEFAULT NULL,
  `mood` varchar(50) DEFAULT NULL,
  `person` enum('1st','2nd','3rd') DEFAULT NULL,
  `number` enum('singular','plural') DEFAULT NULL,
  `gender` enum('masculine','feminine','neutral') DEFAULT NULL,
  `conjugation_tfng` text NOT NULL,
  `conjugation_lat` text NOT NULL,
  `is_common` tinyint(1) DEFAULT '1',
  `notes` text,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_verb_form` (`word_id`,`tense`,`person`,`number`),
  CONSTRAINT `verb_conjugations_ibfk_1` FOREIGN KEY (`word_id`) REFERENCES `words` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Table structure for table `word_categories`
--
CREATE TABLE `word_categories` (
  `id` int NOT NULL AUTO_INCREMENT,
  `category_name_tfng` varchar(100) DEFAULT NULL,
  `category_name_lat` varchar(100) DEFAULT NULL,
  `category_name_fr` varchar(100) NOT NULL,
  `category_slug` varchar(100) NOT NULL,
  `parent_id` int DEFAULT NULL,
  `description_tfng` text,
  `description_lat` text,
  `description_fr` text,
  `icon` varchar(50) DEFAULT NULL,
  `display_order` int DEFAULT '0',
  `is_active` tinyint(1) DEFAULT '1',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `category_slug` (`category_slug`),
  KEY `idx_parent` (`parent_id`),
  KEY `idx_slug` (`category_slug`),
  CONSTRAINT `word_categories_ibfk_1` FOREIGN KEY (`parent_id`) REFERENCES `word_categories` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Table structure for table `word_category_mapping`
--
CREATE TABLE `word_category_mapping` (
  `word_id` int NOT NULL,
  `category_id` int NOT NULL,
  `added_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`word_id`,`category_id`),
  KEY `category_id` (`category_id`),
  CONSTRAINT `word_category_mapping_ibfk_1` FOREIGN KEY (`word_id`) REFERENCES `words` (`id`) ON DELETE CASCADE,
  CONSTRAINT `word_category_mapping_ibfk_2` FOREIGN KEY (`category_id`) REFERENCES `word_categories` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Table structure for table `word_ratings`
--
CREATE TABLE `word_ratings` (
  `id` int NOT NULL AUTO_INCREMENT,
  `word_id` int NOT NULL,
  `user_id` int NOT NULL,
  `rating` int DEFAULT NULL,
  `comment` text,
  `helpful_count` int DEFAULT '0',
  `is_verified_user` tinyint(1) DEFAULT '0',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_rating` (`word_id`,`user_id`),
  KEY `user_id` (`user_id`),
  KEY `idx_word_rating` (`word_id`,`rating`),
  CONSTRAINT `word_ratings_ibfk_1` FOREIGN KEY (`word_id`) REFERENCES `words` (`id`) ON DELETE CASCADE,
  CONSTRAINT `word_ratings_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `word_ratings_chk_1` CHECK ((`rating` between 1 and 5))
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
