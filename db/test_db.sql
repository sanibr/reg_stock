-- phpMyAdmin SQL Dump
-- version 4.7.4
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Generation Time: Nov 17, 2019 at 05:59 PM
-- Server version: 5.7.19
-- PHP Version: 5.6.31

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `test_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `common_settings`
--

DROP TABLE IF EXISTS `common_settings`;
CREATE TABLE IF NOT EXISTS `common_settings` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `is_multiple_upload` tinyint(4) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;

--
-- Dumping data for table `common_settings`
--

INSERT INTO `common_settings` (`id`, `is_multiple_upload`) VALUES
(1, 0);

-- --------------------------------------------------------

--
-- Table structure for table `page_content`
--

DROP TABLE IF EXISTS `page_content`;
CREATE TABLE IF NOT EXISTS `page_content` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `about_title` text,
  `about_description` text,
  `home_tilte` text,
  `home_description` text,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

DROP TABLE IF EXISTS `products`;
CREATE TABLE IF NOT EXISTS `products` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) DEFAULT NULL,
  `category_id` int(11) DEFAULT NULL,
  `description` text,
  `price` decimal(10,6) DEFAULT NULL,
  `price_currency` varchar(10) DEFAULT NULL,
  `product_code` varchar(50) DEFAULT NULL,
  `measurement_type` varchar(100) DEFAULT NULL,
  `measurement_value` decimal(10,6) DEFAULT NULL,
  `measurement_unit` varchar(20) DEFAULT NULL,
  `status` tinyint(4) NOT NULL DEFAULT '1',
  `added_date` datetime DEFAULT NULL,
  `added_by` varchar(100) DEFAULT NULL,
  `update_date` datetime DEFAULT NULL,
  `updated_by` varchar(100) DEFAULT NULL,
  `is_deleted` tinyint(4) NOT NULL DEFAULT '0',
  `deleted_by` varchar(100) DEFAULT NULL,
  `deleted_date` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=85 DEFAULT CHARSET=utf8;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`id`, `name`, `category_id`, `description`, `price`, `price_currency`, `product_code`, `measurement_type`, `measurement_value`, `measurement_unit`, `status`, `added_date`, `added_by`, `update_date`, `updated_by`, `is_deleted`, `deleted_by`, `deleted_date`) VALUES
(70, 'Cake_1', 1, 'test description', '500.000000', NULL, 'CK1', 'Weight', '5.000000', 'Kgm', 1, '2019-11-17 20:37:11', 'admin', NULL, NULL, 0, NULL, NULL),
(71, 'Cake_2', 1, 'description', '123.000000', NULL, 'CK2', 'Weight', '1.000000', 'Kgm', 1, '2019-11-17 20:38:10', 'admin', NULL, NULL, 0, NULL, NULL),
(72, 'Cake_3', 1, 'description', '233.000000', NULL, 'CK3', 'Weight', '1.000000', 'gm', 1, '2019-11-17 20:38:40', 'admin', NULL, NULL, 0, NULL, NULL),
(73, 'Cake_4', 1, '', '566.000000', NULL, 'CK4', 'Weight', '3.000000', 'gm', 1, '2019-11-17 20:38:58', 'admin', NULL, NULL, 0, NULL, NULL),
(74, 'Cake_5', 1, 'It\'s hard to beat our best-selling, Signature Chocolate Cake! We guarantee you\'ll be coming back time and again to indulge in this extraordinary masterpiece. It\'s hard to beat our best-selling, Signature Chocolate Cake! We guarantee you\'ll be coming back time and again to indulge in this extraordinary masterpiece.', '134.500000', NULL, 'CK5', 'Weight', '33.600000', 'gm', 1, '2019-11-17 20:39:24', 'admin', '2019-11-17 22:41:36', 'admin', 0, NULL, NULL),
(75, 'Chocolate_1', 2, 'test description', '23.000000', NULL, 'CH1', 'Weight', '4.000000', 'gm', 1, '2019-11-17 20:40:48', 'admin', NULL, NULL, 0, NULL, NULL),
(76, 'Chocolate_1', 2, '', '33.000000', NULL, 'CH2', 'Weight', '33.000000', 'gm', 1, '2019-11-17 20:41:09', 'admin', NULL, NULL, 0, NULL, NULL),
(77, 'Chocolate_3', 2, '', '63.000000', NULL, 'CH3', 'Weight', '100.000000', 'gm', 1, '2019-11-17 20:41:42', 'admin', NULL, NULL, 0, NULL, NULL),
(78, 'Chocolate_4', 2, '', '44.000000', NULL, 'CH4', 'Weight', '400.000000', 'gm', 1, '2019-11-17 20:42:09', 'admin', NULL, NULL, 0, NULL, NULL),
(79, 'Chocolate_5', 2, 'description', '55.000000', NULL, 'CH5', 'Weight', '500.000000', 'gm', 1, '2019-11-17 20:42:33', 'admin', NULL, NULL, 0, NULL, NULL),
(80, 'Gift Box_1', 3, '', '100.000000', NULL, 'GF1', 'Weight', '100.000000', 'gm', 1, '2019-11-17 20:43:07', 'admin', NULL, NULL, 0, NULL, NULL),
(81, 'Gift Box_2', 3, 'gift box decription', '200.000000', NULL, 'GF2', 'Weight', '200.000000', 'gm', 1, '2019-11-17 20:43:39', 'admin', NULL, NULL, 0, NULL, NULL),
(82, 'Gift Box_3', 3, '', '300.000000', NULL, 'GF3', 'Weight', '350.000000', 'gm', 1, '2019-11-17 20:44:03', 'admin', NULL, NULL, 0, NULL, NULL),
(83, 'Gift Box_4', 3, '', '600.000000', NULL, 'GF4', 'Weight', '500.000000', 'gm', 1, '2019-11-17 20:44:27', 'admin', NULL, NULL, 0, NULL, NULL),
(84, 'Gift Box_5', 3, 'test description', '560.000000', NULL, 'GF5', 'Weight', '250.000000', 'gm', 1, '2019-11-17 20:44:56', 'admin', NULL, NULL, 0, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `products_images`
--

DROP TABLE IF EXISTS `products_images`;
CREATE TABLE IF NOT EXISTS `products_images` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `product_id` int(11) NOT NULL,
  `image_path` text NOT NULL,
  `thumb_path` text,
  `added_date` datetime DEFAULT NULL,
  `added_by` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=64 DEFAULT CHARSET=utf8;

--
-- Dumping data for table `products_images`
--

INSERT INTO `products_images` (`id`, `product_id`, `image_path`, `thumb_path`, `added_date`, `added_by`) VALUES
(49, 70, 'front_end/gallery/1574003231_cake1.jpg', 'front_end/gallery/thumb/1574003231_cake1.jpg', '2019-11-17 20:37:11', 'admin'),
(50, 71, 'front_end/gallery/1574003290_cake2.jpg', 'front_end/gallery/thumb/1574003290_cake2.jpg', '2019-11-17 20:38:10', 'admin'),
(51, 72, 'front_end/gallery/1574003320_c-1.jpg', 'front_end/gallery/thumb/1574003320_c-1.jpg', '2019-11-17 20:38:40', 'admin'),
(52, 73, 'front_end/gallery/1574003339_c2.jpg', 'front_end/gallery/thumb/1574003339_c2.jpg', '2019-11-17 20:38:58', 'admin'),
(53, 74, 'front_end/gallery/1574003364_c-5.jpg', 'front_end/gallery/thumb/1574003364_c-5.jpg', '2019-11-17 20:39:24', 'admin'),
(54, 75, 'front_end/gallery/1574003449_fb-36.jpg', 'front_end/gallery/thumb/1574003449_fb-36.jpg', '2019-11-17 20:40:49', 'admin'),
(55, 76, 'front_end/gallery/1574003469_fb-37.jpg', 'front_end/gallery/thumb/1574003469_fb-37.jpg', '2019-11-17 20:41:09', 'admin'),
(56, 77, 'front_end/gallery/1574003503_fb-38.jpg', 'front_end/gallery/thumb/1574003503_fb-38.jpg', '2019-11-17 20:41:42', 'admin'),
(57, 78, 'front_end/gallery/1574003530_fb-4.jpg', 'front_end/gallery/thumb/1574003530_fb-4.jpg', '2019-11-17 20:42:10', 'admin'),
(58, 79, 'front_end/gallery/1574003555_cake2.jpg', 'front_end/gallery/thumb/1574003555_cake2.jpg', '2019-11-17 20:42:35', 'admin'),
(59, 80, 'front_end/gallery/1574003587_P1.jpg', 'front_end/gallery/thumb/1574003587_P1.jpg', '2019-11-17 20:43:07', 'admin'),
(60, 81, 'front_end/gallery/1574003620_P2.jpg', 'front_end/gallery/thumb/1574003620_P2.jpg', '2019-11-17 20:43:39', 'admin'),
(61, 82, 'front_end/gallery/1574003643_IG1.jpg', 'front_end/gallery/thumb/1574003643_IG1.jpg', '2019-11-17 20:44:03', 'admin'),
(62, 83, 'front_end/gallery/1574003668_P5.jpg', 'front_end/gallery/thumb/1574003668_P5.jpg', '2019-11-17 20:44:27', 'admin'),
(63, 84, 'front_end/gallery/1574003697_fb-38.jpg', 'front_end/gallery/thumb/1574003697_fb-38.jpg', '2019-11-17 20:44:57', 'admin');

-- --------------------------------------------------------

--
-- Table structure for table `product_category`
--

DROP TABLE IF EXISTS `product_category`;
CREATE TABLE IF NOT EXISTS `product_category` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `category` varchar(255) DEFAULT NULL,
  `status` tinyint(4) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;

--
-- Dumping data for table `product_category`
--

INSERT INTO `product_category` (`id`, `category`, `status`) VALUES
(1, 'cakes', 0),
(2, 'chocolate', 0),
(3, 'gift box', 0);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
CREATE TABLE IF NOT EXISTS `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `password`) VALUES
(1, 'lush_admin', '6b86b273ff34fce19d6b804eff5a3f5747ada4eaa22f1d49c01e52ddb7875b4b');
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
