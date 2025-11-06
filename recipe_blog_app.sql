-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Oct 26, 2025 at 12:16 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `recipe_blog_app`
--

-- --------------------------------------------------------

--
-- Table structure for table `categories`
--

CREATE TABLE `categories` (
  `id` int(10) UNSIGNED NOT NULL,
  `name` varchar(100) NOT NULL,
  `slug` varchar(120) GENERATED ALWAYS AS (replace(lcase(`name`),' ','-')) VIRTUAL,
  `active` tinyint(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `categories`
--

INSERT INTO `categories` (`id`, `name`, `active`) VALUES
(1, 'Appetizer / Starter', 1),
(2, 'Main Course', 1),
(3, 'Side Dish', 1),
(4, 'Dessert', 1),
(5, 'Snack', 1),
(6, 'Soup / Salad', 1),
(7, 'Bread / Pastry', 1),
(8, 'Drink / Beverage', 1),
(9, 'Sauce / Dip / Spread', 1);

-- --------------------------------------------------------

--
-- Table structure for table `recipe`
--

CREATE TABLE `recipe` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `title` varchar(200) NOT NULL,
  `description` text DEFAULT NULL,
  `category` varchar(100) NOT NULL,
  `tags` varchar(255) DEFAULT NULL,
  `image_main` varchar(255) DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `category_id` int(10) UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `recipe`
--

INSERT INTO `recipe` (`id`, `user_id`, `title`, `description`, `category`, `tags`, `image_main`, `created_at`, `updated_at`, `category_id`) VALUES
(1, 1, 'The Test Cookie', NULL, 'Dessert', 'cookies, testing', 'uploads/recipe_68f8ec5b61225.jpg', '2025-10-22 20:08:19', '2025-10-25 20:19:47', 4),
(7, 10, 'Garlic Bread', '**Crispy on the outside and buttery on the inside,** this homemade garlic bread is packed with rich garlic flavor and a touch of parsley. **Perfect as a side for pasta or a quick snack, it’s simple, delicious, and ready in minutes!**', 'Bread / Pastry', 'easy, quick, snack, side dish, garlic, butter, oven, vegetarian', 'uploads/recipe_68fd10c98664b5.41257533.jpg', '2025-10-25 19:09:09', '2025-10-25 23:32:49', 7);

-- --------------------------------------------------------

--
-- Table structure for table `recipe_comments`
--

CREATE TABLE `recipe_comments` (
  `id` int(11) NOT NULL,
  `recipe_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `comment_text` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `recipe_comments`
--

INSERT INTO `recipe_comments` (`id`, `recipe_id`, `user_id`, `comment_text`, `created_at`) VALUES
(2, 1, 1, 'sss', '2025-10-23 17:53:11'),
(6, 1, 1, 'ssss', '2025-10-23 17:54:34'),
(8, 1, 1, 'aaaaa', '2025-10-23 17:56:18'),
(9, 1, 1, 'd', '2025-10-23 17:56:30'),
(10, 1, 8, 'dss', '2025-10-24 16:19:29');

-- --------------------------------------------------------

--
-- Table structure for table `recipe_ingredients`
--

CREATE TABLE `recipe_ingredients` (
  `id` int(11) NOT NULL,
  `recipe_id` int(11) NOT NULL,
  `ingredient_name` varchar(150) NOT NULL,
  `quantity` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `recipe_ingredients`
--

INSERT INTO `recipe_ingredients` (`id`, `recipe_id`, `ingredient_name`, `quantity`) VALUES
(43, 1, 'Flour', '150g'),
(44, 1, 'Sugar', '150g'),
(45, 1, 'choco', '1kg'),
(76, 7, '1 baguette or 4 slices of bread', ''),
(77, 7, 'Butter', '4 tbsp'),
(78, 7, 'Garlic cloves (Chopped)', '3–4'),
(79, 7, 'Chopped Parsley (optional)', '1 tbsp'),
(80, 7, 'Salt to taste', ''),
(81, 7, 'Pepper to taste', ''),
(82, 7, 'Grated cheese (optional) as needed', '');

-- --------------------------------------------------------

--
-- Table structure for table `recipe_likes`
--

CREATE TABLE `recipe_likes` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `recipe_id` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `recipe_likes`
--

INSERT INTO `recipe_likes` (`id`, `user_id`, `recipe_id`, `created_at`) VALUES
(7, 8, 1, '2025-10-24 16:16:30'),
(9, 3, 1, '2025-10-24 19:44:45'),
(11, 10, 7, '2025-10-25 14:31:53'),
(12, 11, 7, '2025-10-25 21:34:37'),
(15, 1, 7, '2025-10-25 21:35:56');

-- --------------------------------------------------------

--
-- Table structure for table `recipe_saves`
--

CREATE TABLE `recipe_saves` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `recipe_id` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `recipe_saves`
--

INSERT INTO `recipe_saves` (`id`, `user_id`, `recipe_id`, `created_at`) VALUES
(3, 1, 3, '2025-10-24 16:55:44'),
(4, 3, 1, '2025-10-24 19:44:46'),
(5, 10, 6, '2025-10-25 12:14:23'),
(6, 10, 7, '2025-10-25 17:22:27'),
(7, 1, 7, '2025-10-25 21:37:01');

-- --------------------------------------------------------

--
-- Table structure for table `recipe_steps`
--

CREATE TABLE `recipe_steps` (
  `id` int(11) NOT NULL,
  `recipe_id` int(11) NOT NULL,
  `step_number` int(11) NOT NULL,
  `step_description` text NOT NULL,
  `step_image` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `recipe_steps`
--

INSERT INTO `recipe_steps` (`id`, `recipe_id`, `step_number`, `step_description`, `step_image`) VALUES
(39, 1, 1, 's', NULL),
(40, 1, 2, 'dd', 'uploads/step_68fa764bd1a06.png'),
(41, 1, 3, 'dddf', NULL),
(71, 7, 1, 'Preheat oven to 180°C (350°F).', NULL),
(72, 7, 2, 'Mix butter, garlic, parsley, salt, and pepper.', NULL),
(73, 7, 3, 'Spread on bread and top with cheese if desired.', NULL),
(74, 7, 4, 'Bake 8–10 minutes until golden and crisp.', NULL),
(75, 7, 5, 'Serve hot.', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `user`
--

CREATE TABLE `user` (
  `id` int(11) NOT NULL,
  `username` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` varchar(50) DEFAULT 'author'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `user`
--

INSERT INTO `user` (`id`, `username`, `email`, `password`, `role`) VALUES
(1, 'tester', 'test@gmail.com', '$2y$10$0IZjQAmepPp5fwxWwWEBWO6qp0QfFhm3X5r7elyc.x4nR3DvBQD6C', 'user'),
(2, 'terer', 'teste@gmail.com', '$2y$10$rvwlD60DkRbFxXgEd1kcD.L.HDg6jutgK1Y/buG7N.VAJ0jnr7caa', 'user'),
(3, 'dull', '3@mail.com', '$2y$10$LQebSsH7jw29vGTbkd1rleTM39WdqHW9JzCQRJ0mJ26cB59tGQwky', 'user'),
(4, 'aloo', 'testr@gmail.com', '$2y$10$/EJ2H/ZqfLP0pNnGm7k.XOAWYqm9bwpHzCx4GORK2JfsOXYqCVPvC', 'user'),
(5, 'local', 'abc@d.com', '$2y$10$LsK0MbX3fZwqVAzFQWZYSO/MHqP040jIFEKpApeJEYOkoP2nX.5e6', 'user'),
(6, 'Hello', 'teset@gmail.com', '$2y$10$mAoxbPupV8AlS5A1uVLF1uk1lyleKtEE7QzZ/F54ful3ZvcmG73eK', 'user'),
(7, 'Hellos', 'tesset@gmail.com', '$2y$10$k0rCzXeN7XxVLUo.H3GlNOn4oK2Ff6D5oih9hwLT1j8Ehrbg1eEo.', 'user'),
(8, 'Das', 'ab@c.d', '$2y$10$ojkiH14MovhDeE/5YriH8e7YV69U91YmpmUD87AAcawGGUQ6in7iu', 'user'),
(9, '123', '1@a.com', '$2y$10$Sje.JP1HlYUoRrMYqjrni.JQ5E0vJ7L0OWbUmoVUt79MJoWET8IL2', 'user'),
(10, 'Sathnuwan Dassana', 'faa119@gmail.com', '$2y$10$aGNc9.IEUjmDAx/wKml/AelrpqTCOnHu1wmcRYymAY7jdLce04ami', 'user'),
(11, 'dazno', 'faacc119@gmail.com', '$2y$10$CXHe907ilRymeqnKzPfd/.kRlrM/qXM.cTMA3B0120hoBVPFgDjuS', 'user');

-- --------------------------------------------------------

--
-- Table structure for table `user_recovery`
--

CREATE TABLE `user_recovery` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `word_hash1` varchar(255) NOT NULL,
  `word_hash2` varchar(255) NOT NULL,
  `word_hash3` varchar(255) NOT NULL,
  `word_hash4` varchar(255) NOT NULL,
  `word_hash5` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `user_recovery`
--

INSERT INTO `user_recovery` (`id`, `user_id`, `word_hash1`, `word_hash2`, `word_hash3`, `word_hash4`, `word_hash5`, `created_at`) VALUES
(1, 11, '$2y$10$v2nrPR13NU9mcfOUHzaE7ul0TxKCe8Lxr2vdRhHP9b3ZH2spO.lUu', '$2y$10$kaZ9xTDLikDKhPUrnkJk7.FOnz8AGUjhHTKvHCZYnj/MSEC7qZ/M6', '$2y$10$l05PTv/.4HMFSrQLn2/u.OW1JwVoCoFBqQOGCs/wHeTHrnYWdxEi6', '$2y$10$Yqa4Ptw6lDKSPIiIbwE9WOENqYCD0kFeLtqPLM9E9ehEQUvW51Ktm', '$2y$10$hJxYEz/sU4zOmnDhAp08cutOJik2KqqimvybRHMfmCeWr1F2/7jD6', '2025-10-25 21:30:05');

-- --------------------------------------------------------

--
-- Table structure for table `user_recovery_words`
--

CREATE TABLE `user_recovery_words` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `pos` tinyint(4) NOT NULL,
  `word_hash` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `user_recovery_words`
--

INSERT INTO `user_recovery_words` (`id`, `user_id`, `pos`, `word_hash`, `created_at`) VALUES
(1, 9, 1, '$2y$10$m20U3fau7NIbLHK6mYp7LeQvO/kHuT.Wxqm47jSdF2/BVFv15N7EG', '2025-10-24 23:22:21'),
(2, 9, 2, '$2y$10$.O8tuHzFzaJ9Sqb71JR9Ee.snLuTpUH6/Qk5fWL05DzSiLmF3jAqu', '2025-10-24 23:22:21'),
(3, 9, 3, '$2y$10$XG0ZXM8fCwqFGhF7PfV5/uLiH5crHELNJ1QLfln522ZPI.OIhwELC', '2025-10-24 23:22:21'),
(4, 9, 4, '$2y$10$qu/dDbVzsM4M2WtxgreKYupI6uvRA727TuO3naRJy4WOMLAxzVuCa', '2025-10-24 23:22:21'),
(5, 9, 5, '$2y$10$MHYBsJa2KxsMVLsiI.srEOXx3kkM7NmzBchgRL5.KB0EIJ3Qa.lnS', '2025-10-24 23:22:21'),
(6, 10, 1, '$2y$10$Ss/WPDSOkCd/P/VYaGrz9Ou1mD4hRgufcHVtGakjXwZ8qxWnqRxvq', '2025-10-25 12:09:20'),
(7, 10, 2, '$2y$10$/jzu48ZRVpWcKojVP8Cnc.tKSxpeFJIyGKlUdfDuBE1DWJLU9dyzm', '2025-10-25 12:09:20'),
(8, 10, 3, '$2y$10$9tswhdyk4g0URvZEt2m.2uFPOGi7x5uwZlhbuobPqXaX8nHRVsANS', '2025-10-25 12:09:20'),
(9, 10, 4, '$2y$10$SbIieyA0hP3n8goScaCmZOm44ZRJTzLeFqt1igWBv56bUjqGqi.Zm', '2025-10-25 12:09:20'),
(10, 10, 5, '$2y$10$Nlo0Ro1mcjyMkDMRkfHVDOW4GzgYtAdPZTLcCuP.JYCO3SqR9EvC2', '2025-10-25 12:09:20');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `name` (`name`);

--
-- Indexes for table `recipe`
--
ALTER TABLE `recipe`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `recipe_comments`
--
ALTER TABLE `recipe_comments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `recipe_id` (`recipe_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `recipe_ingredients`
--
ALTER TABLE `recipe_ingredients`
  ADD PRIMARY KEY (`id`),
  ADD KEY `recipe_id` (`recipe_id`);

--
-- Indexes for table `recipe_likes`
--
ALTER TABLE `recipe_likes`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `user_id` (`user_id`,`recipe_id`),
  ADD KEY `recipe_id` (`recipe_id`);

--
-- Indexes for table `recipe_saves`
--
ALTER TABLE `recipe_saves`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uniq_save` (`user_id`,`recipe_id`);

--
-- Indexes for table `recipe_steps`
--
ALTER TABLE `recipe_steps`
  ADD PRIMARY KEY (`id`),
  ADD KEY `recipe_id` (`recipe_id`);

--
-- Indexes for table `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `user_recovery`
--
ALTER TABLE `user_recovery`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `user_recovery_words`
--
ALTER TABLE `user_recovery_words`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uniq_user_pos` (`user_id`,`pos`),
  ADD KEY `idx_user` (`user_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `categories`
--
ALTER TABLE `categories`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `recipe`
--
ALTER TABLE `recipe`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `recipe_comments`
--
ALTER TABLE `recipe_comments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `recipe_ingredients`
--
ALTER TABLE `recipe_ingredients`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=83;

--
-- AUTO_INCREMENT for table `recipe_likes`
--
ALTER TABLE `recipe_likes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `recipe_saves`
--
ALTER TABLE `recipe_saves`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `recipe_steps`
--
ALTER TABLE `recipe_steps`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=76;

--
-- AUTO_INCREMENT for table `user`
--
ALTER TABLE `user`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `user_recovery`
--
ALTER TABLE `user_recovery`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `user_recovery_words`
--
ALTER TABLE `user_recovery_words`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `recipe`
--
ALTER TABLE `recipe`
  ADD CONSTRAINT `recipe_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `recipe_comments`
--
ALTER TABLE `recipe_comments`
  ADD CONSTRAINT `recipe_comments_ibfk_1` FOREIGN KEY (`recipe_id`) REFERENCES `recipe` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `recipe_comments_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `recipe_ingredients`
--
ALTER TABLE `recipe_ingredients`
  ADD CONSTRAINT `recipe_ingredients_ibfk_1` FOREIGN KEY (`recipe_id`) REFERENCES `recipe` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `recipe_likes`
--
ALTER TABLE `recipe_likes`
  ADD CONSTRAINT `recipe_likes_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `recipe_likes_ibfk_2` FOREIGN KEY (`recipe_id`) REFERENCES `recipe` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `recipe_steps`
--
ALTER TABLE `recipe_steps`
  ADD CONSTRAINT `recipe_steps_ibfk_1` FOREIGN KEY (`recipe_id`) REFERENCES `recipe` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `user_recovery`
--
ALTER TABLE `user_recovery`
  ADD CONSTRAINT `user_recovery_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
