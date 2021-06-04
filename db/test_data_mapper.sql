-- phpMyAdmin SQL Dump
-- version 4.8.4
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jun 04, 2021 at 09:38 PM
-- Server version: 10.1.37-MariaDB
-- PHP Version: 7.3.0

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `test_data_mapper`
--

-- --------------------------------------------------------

--
-- Table structure for table `product`
--

CREATE TABLE `product` (
  `product_id` varchar(25) COLLATE utf8_unicode_ci NOT NULL,
  `product_name` text COLLATE utf8_unicode_ci NOT NULL,
  `img_url` text COLLATE utf8_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `product`
--

INSERT INTO `product` (`product_id`, `product_name`, `img_url`) VALUES
('test_001', 'Youghurt -1', ''),
('test_002', 'Special Youghurt', ''),
('test_003', ' Chocolate Youghurt', ''),
('test_004', 'Curd', ''),
('test_005', 'Orange Drink', ''),
('test_006', 'Youghurt Drink', ''),
('test_007', 'Sterilized Milk', ''),
('test_008', 'Ice Packet', ''),
('test_009', 'Toffee Bottle', '');

-- --------------------------------------------------------

--
-- Table structure for table `product_sku`
--

CREATE TABLE `product_sku` (
  `product_id` varchar(25) COLLATE utf8_unicode_ci NOT NULL,
  `sku_id` varchar(25) COLLATE utf8_unicode_ci NOT NULL,
  `pricing_id` varchar(50) COLLATE utf8_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `product_sku`
--

INSERT INTO `product_sku` (`product_id`, `sku_id`, `pricing_id`) VALUES
('test_001', 'c_500ml', 'test_001_c_500ml'),
('test_001', 'c_500ml_12', 'test_001_c_500ml_12'),
('test_001', 'c_80ml_je', 'test_001_c_80ml_je'),
('test_001', 'c_80ml_je_48', 'test_001_c_80ml_je_48'),
('test_001', 'c_80ml_va', 'test_001_c_80ml'),
('test_001', 'c_80ml_va_48', 'test_001_c_80ml_48'),
('test_002', 'c_90ml', 'test_002_c_90ml'),
('test_002', 'c_90ml_48', 'test_002_c_90ml_48'),
('test_004', 'c_120ml', 'test_004_c_120ml'),
('test_004', 'c_120ml_24', 'test_004_c_120ml_24'),
('test_004', 'c_1l', 'test_004_c_1l'),
('test_005', 'c_200ml', 'test_005_c_200ml'),
('test_005', 'c_200ml_24', 'test_005_c_200ml_24'),
('test_006', 'bo_220ml_str', 'test_006_bo_220ml_str'),
('test_006', 'bo_220ml_str_35', 'test_006_bo_220ml_str_35'),
('test_006', 'bo_220ml_va', 'test_006_bo_220ml_va'),
('test_006', 'bo_220ml_va_35', 'test_006_bo_220ml_va_35'),
('test_007', 'bo_250ml_cho', 'test_007_bo_250ml_cho'),
('test_007', 'bo_250ml_fa', 'test_007_bo_250ml_fa'),
('test_007', 'bo_250ml_va', 'test_007_bo_250ml_va'),
('test_008', 'p_ice_cho', 'test_008_p_ice_cho'),
('test_008', 'p_ice_cho_30', 'test_008_p_ice_cho_30'),
('test_008', 'p_ice_ma', 'test_008_p_ice_ma'),
('test_008', 'p_ice_ma_30', 'test_008_p_ice_ma_30'),
('test_008', 'p_ice_va', 'test_008_p_ice_va'),
('test_008', 'p_ice_va_30', 'test_008_p_ice_va_30'),
('test_009', 'bo_toffee_100', 'test_009_bo_toffee_1');

-- --------------------------------------------------------

--
-- Table structure for table `sku`
--

CREATE TABLE `sku` (
  `sku_id` varchar(25) COLLATE utf8_unicode_ci NOT NULL,
  `sku_name` text COLLATE utf8_unicode_ci NOT NULL,
  `package_qty` int(11) NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `sku`
--

INSERT INTO `sku` (`sku_id`, `sku_name`, `package_qty`) VALUES
('bo_220ml_str', '220 ml Bottle - Strawberry', 1),
('bo_220ml_str_35', '220 ml Bottle - Strawberry * 35 box', 35),
('bo_220ml_va', '220 ml Bottle - Vanilla', 1),
('bo_220ml_va_35', '220 ml Bottle - Vanilla * 35 box', 35),
('bo_250ml_cho', '250 ml Bottle - Chocolate', 1),
('bo_250ml_fa', '250 ml Bottle - Faluda', 1),
('bo_250ml_va', '250 ml Bottle - Vanilla', 1),
('bo_toffee_100', 'Toffee Bottle - 100 pcs', 100),
('c_120ml', '120 ml Cup', 1),
('c_120ml_24', '120 ml Cup * 24 box', 24),
('c_1l', '1 L Cup', 1),
('c_200ml', '200 ml Cup', 1),
('c_200ml_24', '200 ml Cup * 24 box', 24),
('c_500ml', '500 ml Cup', 1),
('c_500ml_12', '500 ml Cup * 12', 12),
('c_80ml_cho', '80 ml Cup - Chocolate', 1),
('c_80ml_cho_48', '80 ml Cup - Chocolate * 48 box', 48),
('c_80ml_je', '80 ml Cup - Fruit Jelly', 1),
('c_80ml_je_48', '80 ml Cup - Fruit Jelly * 48 box', 48),
('c_80ml_va', '80 ml Cup - Vanilla', 1),
('c_80ml_va_48', '80 ml Cup - Vanilla * 48 box', 48),
('c_90ml', '90 ml Cup', 1),
('c_90ml_48', '90 ml Cup * 48', 48),
('p_ice_cho', 'Ice Packet - Chocolate', 1),
('p_ice_cho_30', 'Ice Packet - Chocolate * 30 bundle', 30),
('p_ice_ma', 'Ice Packet - Mango', 1),
('p_ice_ma_30', 'Ice Packet - Mango * 30 bundle', 30),
('p_ice_va', 'Ice Packet - Vanilla', 1),
('p_ice_va_30', 'Ice Packet - Vanilla * 30 bundle', 30);

-- --------------------------------------------------------

--
-- Table structure for table `unit_pricing`
--

CREATE TABLE `unit_pricing` (
  `pricing_id` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `agency_price` decimal(10,2) NOT NULL,
  `retailer_price` decimal(10,2) NOT NULL,
  `mrp` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `unit_pricing`
--

INSERT INTO `unit_pricing` (`pricing_id`, `agency_price`, `retailer_price`, `mrp`) VALUES
('test_001_c_500ml', '27.00', '50.00', '80.00'),
('test_001_c_500ml_12', '27.00', '50.00', '80.00'),
('test_001_c_80ml', '20.00', '25.00', '40.00'),
('test_001_c_80ml_48', '1150.00', '1300.00', '40.00'),
('test_001_c_80ml_je', '20.00', '30.00', '40.00'),
('test_001_c_80ml_je_48', '20.00', '30.00', '40.00'),
('test_002_c_90ml', '30.00', '37.00', '45.00'),
('test_002_c_90ml_48', '1400.00', '1500.00', '45.00'),
('test_003_c_80ml_cho', '0.00', '0.00', '40.00'),
('test_003_c_80ml_cho_48', '0.00', '0.00', '0.00'),
('test_004_c_120ml', '30.00', '40.00', '60.00'),
('test_004_c_120ml_24', '30.00', '40.00', '60.00'),
('test_004_c_1l', '200.00', '250.00', '300.00'),
('test_005_c_200ml', '10.00', '20.00', '25.00'),
('test_005_c_200ml_24', '10.00', '20.00', '25.00'),
('test_006_bo_220ml_str', '50.00', '60.00', '75.00'),
('test_006_bo_220ml_str_35', '50.00', '60.00', '75.00'),
('test_006_bo_220ml_va', '51.00', '62.00', '75.00'),
('test_006_bo_220ml_va_35', '50.00', '60.00', '75.00'),
('test_007_bo_250ml_cho', '60.00', '75.00', '90.00'),
('test_007_bo_250ml_fa', '60.00', '75.00', '90.00'),
('test_007_bo_250ml_va', '60.00', '75.00', '90.00'),
('test_008_p_ice_cho', '5.00', '8.00', '10.00'),
('test_008_p_ice_cho_30', '5.00', '8.00', '10.00'),
('test_008_p_ice_ma', '5.00', '8.00', '10.00'),
('test_008_p_ice_ma_30', '6.00', '8.00', '10.00'),
('test_008_p_ice_va', '6.00', '8.00', '10.00'),
('test_008_p_ice_va_30', '6.00', '8.00', '10.00'),
('test_009_bo_toffee_1', '250.00', '350.00', '500.00');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `product`
--
ALTER TABLE `product`
  ADD PRIMARY KEY (`product_id`);

--
-- Indexes for table `product_sku`
--
ALTER TABLE `product_sku`
  ADD PRIMARY KEY (`product_id`,`sku_id`,`pricing_id`),
  ADD KEY `sku_id` (`sku_id`),
  ADD KEY `pricing_id` (`pricing_id`);

--
-- Indexes for table `sku`
--
ALTER TABLE `sku`
  ADD PRIMARY KEY (`sku_id`);

--
-- Indexes for table `unit_pricing`
--
ALTER TABLE `unit_pricing`
  ADD PRIMARY KEY (`pricing_id`);

--
-- Constraints for dumped tables
--

--
-- Constraints for table `product_sku`
--
ALTER TABLE `product_sku`
  ADD CONSTRAINT `product_sku_ibfk_1` FOREIGN KEY (`product_id`) REFERENCES `product` (`product_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `product_sku_ibfk_2` FOREIGN KEY (`sku_id`) REFERENCES `sku` (`sku_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `product_sku_ibfk_3` FOREIGN KEY (`pricing_id`) REFERENCES `unit_pricing` (`pricing_id`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
