-- Minimal schema for the features your code uses
-- DB: recipe_blog (import to this DB, or change USE to recipe_blog_app to reuse your current name)

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS=0;

-- Drop in child->parent order (safe to run repeatedly)
DROP TABLE IF EXISTS `recipe_comments`;
DROP TABLE IF EXISTS `recipe_ingredients`;
DROP TABLE IF EXISTS `recipe_steps`;
DROP TABLE IF EXISTS `recipe_likes`;
DROP TABLE IF EXISTS `recipe_saves`;
DROP TABLE IF EXISTS `user_recovery`;
DROP TABLE IF EXISTS `recipe`;
DROP TABLE IF EXISTS `user`;

SET FOREIGN_KEY_CHECKS=1;

-- Create database (adjust if you want to keep recipe_blog_app)
CREATE DATABASE IF NOT EXISTS `recipe_blog`
  DEFAULT CHARACTER SET utf8mb4
  COLLATE utf8mb4_general_ci;
USE `recipe_blog`;

-- Users
CREATE TABLE `user` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` varchar(50) NOT NULL DEFAULT 'user',
  PRIMARY KEY (`id`),
  UNIQUE KEY `uniq_user_username` (`username`),
  UNIQUE KEY `uniq_user_email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
-- Promote an existing user to admin role by email (run AFTER importing user data)
-- Set the email below before executing this file or run the UPDATE manually.
SET @ADMIN_EMAIL = 'put-existing-email-here@example.com';
UPDATE `user` SET `role`='admin' WHERE `email`=@ADMIN_EMAIL;

-- Recipes
CREATE TABLE `recipe` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `title` varchar(200) NOT NULL,
  `description` text DEFAULT NULL,
  `category` ENUM('Appetizer / Starter','Main Course','Side Dish','Dessert','Snack','Soup / Salad','Bread / Pastry','Drink / Beverage','Sauce / Dip / Spread') NOT NULL,
  `tags` varchar(255) DEFAULT NULL,
  `image_main` varchar(255) DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_recipe_user` (`user_id`),
  CONSTRAINT `fk_recipe_user`
    FOREIGN KEY (`user_id`) REFERENCES `user`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Account recovery (fixed 5-word model used by current code)
CREATE TABLE `user_recovery` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `word_hash1` varchar(255) NOT NULL,
  `word_hash2` varchar(255) NOT NULL,
  `word_hash3` varchar(255) NOT NULL,
  `word_hash4` varchar(255) NOT NULL,
  `word_hash5` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `uniq_recovery_user` (`user_id`),
  CONSTRAINT `fk_recovery_user`
    FOREIGN KEY (`user_id`) REFERENCES `user`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Ingredients
CREATE TABLE `recipe_ingredients` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `recipe_id` int(11) NOT NULL,
  `ingredient_name` varchar(150) NOT NULL,
  `quantity` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_ing_recipe` (`recipe_id`),
  CONSTRAINT `fk_ing_recipe`
    FOREIGN KEY (`recipe_id`) REFERENCES `recipe`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Steps
CREATE TABLE `recipe_steps` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `recipe_id` int(11) NOT NULL,
  `step_number` int(11) NOT NULL,
  `step_description` text NOT NULL,
  `step_image` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uniq_step_per_recipe` (`recipe_id`,`step_number`),
  KEY `idx_steps_recipe` (`recipe_id`),
  CONSTRAINT `fk_steps_recipe`
    FOREIGN KEY (`recipe_id`) REFERENCES `recipe`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Comments
CREATE TABLE `recipe_comments` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `recipe_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `comment_text` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_cmt_recipe` (`recipe_id`),
  KEY `idx_cmt_user` (`user_id`),
  CONSTRAINT `fk_cmt_recipe`
    FOREIGN KEY (`recipe_id`) REFERENCES `recipe`(`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_cmt_user`
    FOREIGN KEY (`user_id`) REFERENCES `user`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Likes (toggle via unique user+recipe)
CREATE TABLE `recipe_likes` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `recipe_id` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `uniq_like` (`user_id`,`recipe_id`),
  KEY `idx_like_recipe` (`recipe_id`),
  CONSTRAINT `fk_like_user`
    FOREIGN KEY (`user_id`) REFERENCES `user`(`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_like_recipe`
    FOREIGN KEY (`recipe_id`) REFERENCES `recipe`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Saves (toggle via unique user+recipe)
CREATE TABLE `recipe_saves` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `recipe_id` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `uniq_save` (`user_id`,`recipe_id`),
  KEY `idx_save_recipe` (`recipe_id`),
  CONSTRAINT `fk_save_user`
    FOREIGN KEY (`user_id`) REFERENCES `user`(`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_save_recipe`
    FOREIGN KEY (`recipe_id`) REFERENCES `recipe`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;