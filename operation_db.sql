-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Apr 17, 2026 at 07:02 AM
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
-- Database: `operation_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `drivers`
--

CREATE TABLE `drivers` (
  `driver_id` int(11) NOT NULL,
  `driver_lname` varchar(200) NOT NULL,
  `driver_fname` varchar(200) NOT NULL,
  `driver_mname` varchar(200) NOT NULL,
  `driver_IdNumber` varchar(200) NOT NULL,
  `driver_dailyRate` decimal(11,2) NOT NULL,
  `driver_hourlyRate` decimal(11,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `drivers`
--

INSERT INTO `drivers` (`driver_id`, `driver_lname`, `driver_fname`, `driver_mname`, `driver_IdNumber`, `driver_dailyRate`, `driver_hourlyRate`) VALUES
(1, '', '', '', '', 0.00, 0.00);

-- --------------------------------------------------------

--
-- Table structure for table `location`
--

CREATE TABLE `location` (
  `location_id` int(11) NOT NULL,
  `location_name` varchar(200) NOT NULL,
  `latitude` varchar(255) NOT NULL,
  `longitude` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `location`
--

INSERT INTO `location` (`location_id`, `location_name`, `latitude`, `longitude`) VALUES
(1, 'PH01', '7.3653', '125.595517');

-- --------------------------------------------------------

--
-- Table structure for table `operations`
--

CREATE TABLE `operations` (
  `entry_id` int(11) NOT NULL,
  `entry_type` enum('RV ENTRY','Others ENTRY','BBHM ENTRY','DPC_KDs & OPM ENTRY','CARGO TRUCK ENTRY') NOT NULL,
  `segment` varchar(50) DEFAULT NULL,
  `activity` varchar(50) DEFAULT NULL,
  `segment_empty` varchar(50) NOT NULL,
  `activity_empty` varchar(50) NOT NULL,
  `remarks` text DEFAULT NULL,
  `pullout_location_arrival_date` date DEFAULT NULL,
  `pullout_location_arrival_time` time DEFAULT NULL,
  `pullout_location_departure_date` date DEFAULT NULL,
  `pullout_location_departure_time` time DEFAULT NULL,
  `ph_arrival_date` date DEFAULT NULL,
  `ph_arrival_time` time DEFAULT NULL,
  `van_alpha` varchar(20) DEFAULT NULL,
  `van_number` varchar(20) DEFAULT NULL,
  `van_name` varchar(100) DEFAULT NULL,
  `ph` varchar(50) DEFAULT NULL,
  `shipper` varchar(100) DEFAULT NULL,
  `ecs` varchar(50) DEFAULT NULL,
  `tr` varchar(50) DEFAULT NULL,
  `gs` varchar(50) DEFAULT NULL,
  `waybill` varchar(50) DEFAULT NULL,
  `waybill_empty` varchar(50) DEFAULT NULL,
  `waybill_date` date NOT NULL,
  `prime_mover` varchar(50) DEFAULT NULL,
  `driver` varchar(100) DEFAULT NULL,
  `driver_idNumber` int(11) NOT NULL,
  `empty_pullout_location` varchar(100) DEFAULT NULL,
  `loaded_van_loading_start_date` date DEFAULT NULL,
  `loaded_van_loading_start_time` time DEFAULT NULL,
  `loaded_van_loading_finish_date` date DEFAULT NULL,
  `loaded_van_loading_finish_time` time DEFAULT NULL,
  `loaded_van_delivery_departure_date` date DEFAULT NULL,
  `loaded_van_delivery_departure_time` time DEFAULT NULL,
  `loaded_van_delivery_arrival_date` date DEFAULT NULL,
  `loaded_van_delivery_arrival_time` time DEFAULT NULL,
  `genset_shutoff_date` date DEFAULT NULL,
  `genset_shutoff_time` time DEFAULT NULL,
  `end_uploading_date` date DEFAULT NULL,
  `end_uploading_time` time DEFAULT NULL,
  `dr_no` varchar(50) DEFAULT NULL,
  `load_description` varchar(255) DEFAULT NULL,
  `delivered_by_prime_mover` varchar(50) DEFAULT NULL,
  `delivered_by_driver` varchar(100) DEFAULT NULL,
  `delivered_by_driverIdNumber` varchar(200) NOT NULL,
  `delivered_to` varchar(100) DEFAULT NULL,
  `delivered_remarks` text DEFAULT NULL,
  `genset_hr_meter_start` decimal(10,2) DEFAULT NULL,
  `genset_hr_meter_end` decimal(10,2) DEFAULT NULL,
  `genset_start_date` date DEFAULT NULL,
  `genset_start_time` time DEFAULT NULL,
  `genset_end_date` date DEFAULT NULL,
  `genset_end_time` time DEFAULT NULL,
  `others_date` date DEFAULT NULL,
  `truck` varchar(50) DEFAULT NULL,
  `operations_ph` varchar(50) DEFAULT NULL,
  `load_quantity_weight` decimal(10,2) DEFAULT NULL,
  `unit_of_measure` varchar(20) DEFAULT NULL,
  `deliver_from` varchar(100) DEFAULT NULL,
  `production_date` date DEFAULT NULL,
  `finished_loading_date` date DEFAULT NULL,
  `finished_loading_time` time DEFAULT NULL,
  `ph_departure_date` date DEFAULT NULL,
  `ph_departure_time` time DEFAULT NULL,
  `wharf_arrival_date` date DEFAULT NULL,
  `wharf_arrival_time` time DEFAULT NULL,
  `wharf_departure_date` date DEFAULT NULL,
  `wharf_departure_time` time DEFAULT NULL,
  `tls_number` varchar(50) DEFAULT NULL,
  `13_kgs` decimal(10,2) DEFAULT NULL,
  `sp_3kgs` decimal(10,2) DEFAULT NULL,
  `total_load` decimal(10,2) DEFAULT NULL,
  `bbhm_type` varchar(50) DEFAULT NULL,
  `dpc_date` date DEFAULT NULL,
  `evita_farmind` varchar(100) DEFAULT NULL,
  `departure` datetime DEFAULT NULL,
  `arrival` datetime DEFAULT NULL,
  `13_body` int(11) DEFAULT NULL,
  `13_cover` int(11) DEFAULT NULL,
  `13_pads` int(11) DEFAULT NULL,
  `18_body` int(11) DEFAULT NULL,
  `18_cover` int(11) DEFAULT NULL,
  `18_pads` int(11) DEFAULT NULL,
  `13_total` int(11) DEFAULT NULL,
  `18_total` int(11) DEFAULT NULL,
  `fgtr_no` varchar(50) DEFAULT NULL,
  `cargo_date` date DEFAULT NULL,
  `customer_ph` varchar(100) DEFAULT NULL,
  `outside` varchar(100) DEFAULT NULL,
  `compound` varchar(100) DEFAULT NULL,
  `total_trips` int(11) DEFAULT NULL,
  `kms` varchar(50) NOT NULL,
  `operations` varchar(100) DEFAULT NULL,
  `piece_rate` decimal(11,2) NOT NULL,
  `billing_sku` varchar(200) NOT NULL,
  `created_by` varchar(50) DEFAULT NULL,
  `created_date` timestamp NOT NULL DEFAULT current_timestamp(),
  `modified_by` varchar(50) DEFAULT NULL,
  `modified_date` timestamp NULL DEFAULT NULL ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `operations`
--

INSERT INTO `operations` (`entry_id`, `entry_type`, `segment`, `activity`, `segment_empty`, `activity_empty`, `remarks`, `pullout_location_arrival_date`, `pullout_location_arrival_time`, `pullout_location_departure_date`, `pullout_location_departure_time`, `ph_arrival_date`, `ph_arrival_time`, `van_alpha`, `van_number`, `van_name`, `ph`, `shipper`, `ecs`, `tr`, `gs`, `waybill`, `waybill_empty`, `waybill_date`, `prime_mover`, `driver`, `driver_idNumber`, `empty_pullout_location`, `loaded_van_loading_start_date`, `loaded_van_loading_start_time`, `loaded_van_loading_finish_date`, `loaded_van_loading_finish_time`, `loaded_van_delivery_departure_date`, `loaded_van_delivery_departure_time`, `loaded_van_delivery_arrival_date`, `loaded_van_delivery_arrival_time`, `genset_shutoff_date`, `genset_shutoff_time`, `end_uploading_date`, `end_uploading_time`, `dr_no`, `load_description`, `delivered_by_prime_mover`, `delivered_by_driver`, `delivered_by_driverIdNumber`, `delivered_to`, `delivered_remarks`, `genset_hr_meter_start`, `genset_hr_meter_end`, `genset_start_date`, `genset_start_time`, `genset_end_date`, `genset_end_time`, `others_date`, `truck`, `operations_ph`, `load_quantity_weight`, `unit_of_measure`, `deliver_from`, `production_date`, `finished_loading_date`, `finished_loading_time`, `ph_departure_date`, `ph_departure_time`, `wharf_arrival_date`, `wharf_arrival_time`, `wharf_departure_date`, `wharf_departure_time`, `tls_number`, `13_kgs`, `sp_3kgs`, `total_load`, `bbhm_type`, `dpc_date`, `evita_farmind`, `departure`, `arrival`, `13_body`, `13_cover`, `13_pads`, `18_body`, `18_cover`, `18_pads`, `13_total`, `18_total`, `fgtr_no`, `cargo_date`, `customer_ph`, `outside`, `compound`, `total_trips`, `kms`, `operations`, `piece_rate`, `billing_sku`, `created_by`, `created_date`, `modified_by`, `modified_date`) VALUES
(1, 'CARGO TRUCK ENTRY', 'sas', 'asdas', '', '', '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '', NULL, '0000-00-00', NULL, '', 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '', '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '', NULL, NULL, NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '0000-00-00', '', '', '', 0, '', '', 0.00, '', 'Admin', '2026-03-31 20:59:54', 'Admin', '2026-03-31 21:20:29');

-- --------------------------------------------------------

--
-- Table structure for table `sku`
--

CREATE TABLE `sku` (
  `sku_id` int(11) NOT NULL,
  `sku_name` varchar(200) NOT NULL,
  `sku_shipper_segment` varchar(200) NOT NULL,
  `sku_farm` varchar(200) NOT NULL,
  `sku_rountripDistance` decimal(11,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `sku`
--

INSERT INTO `sku` (`sku_id`, `sku_name`, `sku_shipper_segment`, `sku_farm`, `sku_rountripDistance`) VALUES
(1, 'Dole-1-PNB-PNB', 'Dole', '1', 107.00);

-- --------------------------------------------------------

--
-- Table structure for table `trailer`
--

CREATE TABLE `trailer` (
  `trailer_id` int(11) NOT NULL,
  `trailer_name` varchar(200) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `trailer`
--

INSERT INTO `trailer` (`trailer_id`, `trailer_name`) VALUES
(1, 'TR760');

-- --------------------------------------------------------

--
-- Table structure for table `trip_rates`
--

CREATE TABLE `trip_rates` (
  `id` int(11) NOT NULL,
  `segment` varchar(200) NOT NULL,
  `activity` varchar(200) NOT NULL,
  `baseRate` decimal(11,2) NOT NULL,
  `additional` decimal(11,2) NOT NULL,
  `totalRates` decimal(11,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `trip_rates`
--

INSERT INTO `trip_rates` (`id`, `segment`, `activity`, `baseRate`, `additional`, `totalRates`) VALUES
(1, 'ABCRV', 'ABCDonMar.RV.Empty', 000.00, 000.00, 000.00);

-- --------------------------------------------------------

--
-- Table structure for table `units`
--

CREATE TABLE `units` (
  `unit_id` int(11) NOT NULL,
  `unit_name` varchar(200) NOT NULL,
  `unit_std` double(11,2) NOT NULL,
  `unit_model` varchar(200) NOT NULL,
  `unit_cluster` varchar(200) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `units`
--

INSERT INTO `units` (`unit_id`, `unit_name`, `unit_std`, `unit_model`, `unit_cluster`) VALUES
(1, 'PM855', 2.50, 'Shacman X5000', 'Cluster 5');

-- --------------------------------------------------------

--
-- Table structure for table `user`
--

CREATE TABLE `user` (
  `user_id` int(10) NOT NULL,
  `user_name` varchar(255) NOT NULL,
  `user_fname` varchar(255) NOT NULL,
  `user_lname` varchar(255) NOT NULL,
  `user_mname` varchar(1) NOT NULL,
  `user_email` varchar(255) NOT NULL,
  `user_pass` varchar(255) NOT NULL,
  `user_type` varchar(255) NOT NULL,
  `user_image` varchar(255) NOT NULL,
  `user_accountStat` varchar(200) NOT NULL,
  `user_code` int(10) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `user`
--

INSERT INTO `user` (`user_id`, `user_name`, `user_fname`, `user_lname`, `user_mname`, `user_email`, `user_pass`, `user_type`, `user_image`, `user_accountStat`, `user_code`) VALUES
(1, 'Admin', 'John', 'Niel Baliguat', '', 'a@gmail.com', '$2y$10$OhXjpSUwcL3cHUxN8xZs/eJmJh9wMjyqjXYmfi0PcAlxxZooMy8j.', 'Admin', 'assets/uploads/profiles/profile_1_1774018679.png', 'Active', 0),
(2, 'samp', 'John', 'Baliguat', '', 'samp@gmail.com', '$2y$10$aIk.ZGPKzPgjN7QxBNr9OehWgmZDUwB99.8bTJGMzB9vbQI1usoIG', 'User', 'VMG Final1_1.jpg', 'Active', 0);

-- --------------------------------------------------------

--
-- Table structure for table `user_preferences`
--

CREATE TABLE `user_preferences` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `email_notifications` tinyint(4) DEFAULT 1,
  `push_notifications` tinyint(4) DEFAULT 1,
  `weekly_reports` tinyint(4) DEFAULT 0,
  `theme` varchar(20) DEFAULT 'light',
  `language` varchar(5) DEFAULT 'en',
  `timezone` varchar(20) DEFAULT 'est',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `drivers`
--
ALTER TABLE `drivers`
  ADD PRIMARY KEY (`driver_id`);

--
-- Indexes for table `location`
--
ALTER TABLE `location`
  ADD PRIMARY KEY (`location_id`);

--
-- Indexes for table `operations`
--
ALTER TABLE `operations`
  ADD PRIMARY KEY (`entry_id`),
  ADD KEY `idx_entry_type` (`entry_type`),
  ADD KEY `idx_waybill` (`waybill`),
  ADD KEY `idx_date` (`others_date`,`production_date`,`dpc_date`,`cargo_date`),
  ADD KEY `idx_driver` (`driver`),
  ADD KEY `idx_ph` (`ph`);

--
-- Indexes for table `sku`
--
ALTER TABLE `sku`
  ADD PRIMARY KEY (`sku_id`);

--
-- Indexes for table `trailer`
--
ALTER TABLE `trailer`
  ADD PRIMARY KEY (`trailer_id`);

--
-- Indexes for table `trip_rates`
--
ALTER TABLE `trip_rates`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `units`
--
ALTER TABLE `units`
  ADD PRIMARY KEY (`unit_id`);

--
-- Indexes for table `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`user_id`);

--
-- Indexes for table `user_preferences`
--
ALTER TABLE `user_preferences`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `drivers`
--
ALTER TABLE `drivers`
  MODIFY `driver_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=176;

--
-- AUTO_INCREMENT for table `location`
--
ALTER TABLE `location`
  MODIFY `location_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=131;

--
-- AUTO_INCREMENT for table `operations`
--
ALTER TABLE `operations`
  MODIFY `entry_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=31;

--
-- AUTO_INCREMENT for table `sku`
--
ALTER TABLE `sku`
  MODIFY `sku_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=118;

--
-- AUTO_INCREMENT for table `trailer`
--
ALTER TABLE `trailer`
  MODIFY `trailer_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=441;

--
-- AUTO_INCREMENT for table `trip_rates`
--
ALTER TABLE `trip_rates`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=40;

--
-- AUTO_INCREMENT for table `units`
--
ALTER TABLE `units`
  MODIFY `unit_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=407;

--
-- AUTO_INCREMENT for table `user`
--
ALTER TABLE `user`
  MODIFY `user_id` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=26;

--
-- AUTO_INCREMENT for table `user_preferences`
--
ALTER TABLE `user_preferences`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `user_preferences`
--
ALTER TABLE `user_preferences`
  ADD CONSTRAINT `user_preferences_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `user` (`user_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
