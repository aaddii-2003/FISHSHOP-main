-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Mar 21, 2025 at 07:13 PM
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
-- Database: `fishmart`
--

-- --------------------------------------------------------

--
-- Table structure for table `fishitem`
--

CREATE TABLE `fishitem` (
  `id` int(11) NOT NULL,
  `title` varchar(30) NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `category` varchar(100) DEFAULT NULL,
  `image_url` varchar(255) DEFAULT NULL,
  `seller_name` varchar(255) DEFAULT NULL,
  `description` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `fishitem`
--

INSERT INTO `fishitem` (`id`, `title`, `price`, `category`, `image_url`, `seller_name`, `description`) VALUES
(1, 'Carbon Fiber Fishing Rod', 79.99, 'FishingRods', 'https://example.com/fishing-rod-carbon.jpg', 'Pro Anglers', NULL),
(2, 'Telescopic Fishing Rod', 45.50, 'FishingRods', 'https://example.com/fishing-rod-telescopic.jpg', 'Fishing Gear Co.', NULL),
(3, 'Live Worm Bait', 9.99, 'FishingBaits', 'https://www.google.com/imgres?q=fishing%20baits%20image&imgurl=https%3A%2F%2F4.imimg.com%2Fdata4%2FUH%2FVP%2FMY-7073483%2F10wmt1-superjumbo-v3.jpg&imgrefurl=https%3A%2F%2Fwww.indiamart.com%2Fproddetail%2Ffishing-lure-10974913430.html&docid=EpzJf_-pkIe1zM&', 'Bait Masters', 'Its a good product having flexible features to lure fishes'),
(4, 'Artificial Lure Set', 15.75, 'FishingBaits', 'https://example.com/artificial-lure.jpg', 'Lure Pro', NULL),
(5, 'Handcrafted Wooden Spear', 65.00, 'Spears', 'https://example.com/wooden-spear.jpg', 'Tribal Gear', NULL),
(6, 'Stainless Steel Fishing Spear', 120.99, 'Spears', 'https://example.com/steel-spear.jpg', 'Deep Sea Hunters', NULL),
(7, 'Traditional Fishing Net', 55.25, 'Nets', 'https://example.com/fishing-net.jpg', 'Coastal Traders', NULL),
(8, 'Cast Net for Shallow Waters', 89.99, 'Nets', 'https://example.com/cast-net.jpg', 'Ocean Catch', NULL),
(9, 'Crab Trap Cage', 39.50, 'Traps', 'https://example.com/crab-trap.jpg', 'Seafood Supplies', NULL),
(10, 'Lobster Trap Wooden', 99.99, 'Traps', 'https://example.com/lobster-trap.jpg', 'Marine Essentials', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `phone` varchar(12) NOT NULL,
  `address` text NOT NULL,
  `total_price` decimal(10,2) NOT NULL,
  `Payment_mode` varchar(20) NOT NULL,
  `status` enum('Pending','Processing','Shipped','Delivered','Cancelled') DEFAULT 'Pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `orders`
--

INSERT INTO `orders` (`id`, `user_id`, `name`, `phone`, `address`, `total_price`, `Payment_mode`, `status`, `created_at`) VALUES
(2, 2, 'Sahil Kubal', '1234567890', 'audumbar plaza\\r\\naudumbar plaza fez 2 kudal', 159.98, 'cod', 'Pending', '2025-03-20 11:23:07');

-- --------------------------------------------------------

--
-- Table structure for table `order_items`
--

CREATE TABLE `order_items` (
  `id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `fishitem_id` int(11) NOT NULL,
  `quantity` int(3) NOT NULL,
  `price` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `order_items`
--

INSERT INTO `order_items` (`id`, `order_id`, `fishitem_id`, `quantity`, `price`) VALUES
(1, 2, 1, 2, 79.99);

-- --------------------------------------------------------

--
-- Table structure for table `sessions`
--

CREATE TABLE `sessions` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `session_token` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `user_id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `phone` varchar(20) NOT NULL,
  `profile_picture` varchar(255) DEFAULT NULL,
  `date_of_birth` date DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `address_line` varchar(255) DEFAULT NULL,
  `city` varchar(100) DEFAULT NULL,
  `state` varchar(100) DEFAULT NULL,
  `country` varchar(100) DEFAULT NULL,
  `zip_code` varchar(20) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_id`, `name`, `email`, `phone`, `profile_picture`, `date_of_birth`, `password`, `address_line`, `city`, `state`, `country`, `zip_code`, `created_at`) VALUES
(2, 'Sahil Kubal', 'sahil@gmail.com', '1234567890', '', '2025-03-11', '$2y$10$YKosfRUmY20iEJQBVT9rruXMDQc9iaWa.Q6TcdtIihasOdZ2kxjPS', 'vengurla', 'vengurla', 'MAHARASHTRA', 'India', '416520', '2025-02-27 16:20:49');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `fishitem`
--
ALTER TABLE `fishitem`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `order_items`
--
ALTER TABLE `order_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `order_id` (`order_id`),
  ADD KEY `fishitem_id` (`fishitem_id`);

--
-- Indexes for table `sessions`
--
ALTER TABLE `sessions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD UNIQUE KEY `phone` (`phone`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `fishitem`
--
ALTER TABLE `fishitem`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `order_items`
--
ALTER TABLE `order_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `sessions`
--
ALTER TABLE `sessions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `orders_ibfk_1` FOREIGN KEY (`id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE;

--
-- Constraints for table `order_items`
--
ALTER TABLE `order_items`
  ADD CONSTRAINT `order_items_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `order_items_ibfk_2` FOREIGN KEY (`fishitem_id`) REFERENCES `fishitem` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
