-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Sep 25, 2025 at 12:44 PM
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
-- Database: `hms_ram`
--

-- --------------------------------------------------------

--
-- Table structure for table `appointments`
--

CREATE TABLE `appointments` (
  `id` int(11) UNSIGNED NOT NULL,
  `patient_id` int(11) NOT NULL,
  `doctor_id` int(11) NOT NULL,
  `appointment_date` date NOT NULL,
  `appointment_time` time NOT NULL,
  `reason_for_visit` text DEFAULT NULL,
  `status` enum('Pending','Confirmed','Cancelled','Completed') NOT NULL DEFAULT 'Pending',
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `deleted_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Dumping data for table `appointments`
--

INSERT INTO `appointments` (`id`, `patient_id`, `doctor_id`, `appointment_date`, `appointment_time`, `reason_for_visit`, `status`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 33, 35, '2025-07-19', '14:21:00', 'wil think latert', 'Pending', '2025-07-09 06:50:10', '2025-07-24 05:24:15', '2025-07-24 05:24:15'),
(2, 34, 15, '2025-07-24', '17:26:00', 'ee', 'Pending', '2025-07-24 05:27:15', '2025-07-25 05:00:10', NULL),
(3, 35, 43, '2025-07-24', '22:49:00', '', 'Completed', '2025-07-24 10:50:15', '2025-07-24 10:59:29', NULL),
(4, 36, 43, '2025-07-24', '23:19:00', '', 'Cancelled', '2025-07-24 11:19:57', '2025-07-25 05:00:23', NULL),
(5, 27, 43, '2025-07-26', '19:50:00', 'hh', 'Confirmed', '2025-07-24 12:18:21', '2025-07-24 12:18:21', NULL),
(6, 20, 43, '2025-07-25', '16:14:00', 'asthma problem\r\n', 'Confirmed', '2025-07-25 04:19:33', '2025-07-25 04:19:33', NULL),
(7, 37, 43, '2025-07-25', '17:04:00', 'general', 'Confirmed', '2025-07-25 05:09:08', '2025-07-29 05:45:53', NULL),
(8, 38, 43, '2025-08-22', '13:00:00', '', 'Confirmed', '2025-08-22 05:00:13', '2025-08-22 05:10:10', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `assets_equipment`
--

CREATE TABLE `assets_equipment` (
  `id` int(11) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `asset_tag` varchar(100) DEFAULT NULL,
  `category` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `purchase_date` date DEFAULT NULL,
  `warranty_expiry_date` date DEFAULT NULL,
  `location` varchar(255) DEFAULT NULL,
  `status` enum('Operational','Under Maintenance','Out of Service','Disposed') NOT NULL DEFAULT 'Operational',
  `notes` text DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `beds`
--

CREATE TABLE `beds` (
  `id` int(11) UNSIGNED NOT NULL,
  `ward_id` int(11) UNSIGNED NOT NULL,
  `bed_number` varchar(50) NOT NULL,
  `status` enum('Available','Occupied','Under Maintenance','Dirty') NOT NULL DEFAULT 'Available',
  `notes` text DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `beds`
--

INSERT INTO `beds` (`id`, `ward_id`, `bed_number`, `status`, `notes`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 1, 'GEN-1', 'Available', NULL, '2025-07-26 05:38:05', '2025-07-28 11:46:53', NULL),
(2, 1, 'GEN-2', 'Available', NULL, '2025-07-26 05:38:05', '2025-07-28 09:32:19', NULL),
(3, 1, 'GEN-3', 'Occupied', NULL, '2025-07-26 05:38:05', '2025-07-26 06:32:52', NULL),
(4, 1, 'GEN-4', 'Occupied', NULL, '2025-07-26 05:38:05', '2025-07-26 06:32:58', NULL),
(5, 1, 'GEN-5', 'Occupied', NULL, '2025-07-26 05:38:05', '2025-07-26 06:33:04', NULL),
(6, 1, 'GEN-6', 'Occupied', NULL, '2025-07-26 05:38:05', '2025-07-26 06:33:11', NULL),
(7, 1, 'GEN-7', 'Occupied', NULL, '2025-07-26 05:38:05', '2025-07-26 06:33:16', NULL),
(8, 1, 'GEN-8', 'Occupied', NULL, '2025-07-26 05:38:05', '2025-07-26 06:33:22', NULL),
(9, 1, 'GEN-9', 'Available', NULL, '2025-07-26 05:38:05', '2025-07-26 05:53:00', '2025-07-26 05:53:00'),
(10, 1, 'GEN-10', 'Available', NULL, '2025-07-26 05:38:05', '2025-07-26 05:53:00', '2025-07-26 05:53:00'),
(11, 1, 'GEN-11', 'Available', NULL, '2025-07-26 05:51:53', '2025-07-26 05:53:00', '2025-07-26 05:53:00'),
(12, 1, 'GEN-12', 'Available', NULL, '2025-07-26 05:51:53', '2025-07-26 05:53:00', '2025-07-26 05:53:00'),
(13, 2, 'P-1', 'Available', NULL, '2025-07-26 06:05:01', '2025-08-22 06:48:26', NULL),
(14, 2, 'P-2', 'Available', NULL, '2025-07-26 06:05:01', '2025-07-28 11:31:41', NULL),
(15, 2, 'P-3', 'Available', NULL, '2025-07-26 06:05:01', '2025-07-29 04:24:29', NULL),
(16, 2, 'P-4', 'Available', NULL, '2025-07-26 06:05:01', '2025-07-26 06:05:01', NULL),
(17, 2, 'P-5', 'Available', NULL, '2025-07-26 06:05:01', '2025-07-26 06:05:01', NULL),
(18, 2, 'P-6', 'Available', NULL, '2025-07-26 06:05:01', '2025-07-26 06:05:01', NULL),
(19, 2, 'P-7', 'Available', NULL, '2025-07-26 06:05:01', '2025-07-26 06:05:01', NULL),
(20, 2, 'P-8', 'Available', NULL, '2025-07-26 06:05:01', '2025-07-26 06:05:01', NULL),
(21, 2, 'P-9', 'Available', NULL, '2025-07-26 06:05:01', '2025-07-26 06:05:01', NULL),
(22, 2, 'P-10', 'Available', NULL, '2025-07-26 06:05:01', '2025-08-22 06:48:42', NULL),
(23, 2, 'P-11', 'Available', NULL, '2025-07-26 06:05:01', '2025-07-26 06:05:01', NULL),
(24, 2, 'P-12', 'Available', NULL, '2025-07-26 06:05:01', '2025-07-26 06:05:01', NULL),
(25, 2, 'P-13', 'Available', NULL, '2025-07-26 06:05:01', '2025-07-26 06:05:01', NULL),
(26, 2, 'P-14', 'Available', NULL, '2025-07-26 06:05:01', '2025-07-26 06:05:01', NULL),
(27, 2, 'P-15', 'Available', NULL, '2025-07-26 06:05:01', '2025-07-26 06:05:01', NULL),
(28, 2, 'P-16', 'Available', NULL, '2025-07-26 06:05:01', '2025-07-26 06:05:01', NULL),
(29, 2, 'P-17', 'Available', NULL, '2025-07-26 06:05:01', '2025-07-26 06:05:01', NULL),
(30, 2, 'P-18', 'Available', NULL, '2025-07-26 06:05:01', '2025-07-26 06:05:01', NULL),
(31, 2, 'P-19', 'Available', NULL, '2025-07-26 06:05:01', '2025-07-26 06:05:01', NULL),
(32, 2, 'P-20', 'Available', NULL, '2025-07-26 06:05:01', '2025-07-26 06:05:01', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `doctors`
--

CREATE TABLE `doctors` (
  `id` int(11) NOT NULL,
  `doctor_id_code` varchar(50) DEFAULT NULL,
  `first_name` varchar(100) NOT NULL,
  `last_name` varchar(100) NOT NULL,
  `gender` enum('Male','Female','Other') DEFAULT NULL,
  `date_of_birth` date DEFAULT NULL,
  `phone_number` varchar(20) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `emergency_contact_name` varchar(255) DEFAULT NULL,
  `emergency_contact_phone` varchar(20) DEFAULT NULL,
  `user_id` int(11) DEFAULT NULL,
  `specialization` varchar(255) NOT NULL,
  `qualification` varchar(255) DEFAULT NULL,
  `medical_license_no` varchar(100) DEFAULT NULL,
  `registration_number` varchar(100) DEFAULT NULL,
  `medical_council` varchar(255) DEFAULT NULL,
  `experience_years` int(11) DEFAULT NULL,
  `bio` text DEFAULT NULL,
  `department_id` int(11) DEFAULT NULL,
  `joining_date` date DEFAULT NULL,
  `employment_status` enum('Full-time','Part-time','Consultant','On-Leave','Resigned','Terminated') DEFAULT 'Full-time',
  `contract_type` varchar(100) DEFAULT NULL,
  `designation` varchar(100) DEFAULT NULL,
  `opd_fee` decimal(10,2) DEFAULT 0.00,
  `ipd_charge_percentage` decimal(5,2) DEFAULT 0.00,
  `bank_account_number` varchar(50) DEFAULT NULL,
  `bank_name` varchar(255) DEFAULT NULL,
  `ifsc_code` varchar(20) DEFAULT NULL,
  `pan_number` varchar(10) DEFAULT NULL,
  `profile_picture` varchar(255) DEFAULT NULL,
  `signature_image` varchar(255) DEFAULT NULL,
  `resume_path` varchar(255) DEFAULT NULL,
  `degree_certificate_path` varchar(255) DEFAULT NULL,
  `license_certificate_path` varchar(255) DEFAULT NULL,
  `other_certificates_path` text DEFAULT NULL,
  `is_available` tinyint(1) DEFAULT 1,
  `status` enum('Active','Inactive','On Leave','Suspended') DEFAULT 'Active',
  `last_login_at` datetime DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `doctors`
--

INSERT INTO `doctors` (`id`, `doctor_id_code`, `first_name`, `last_name`, `gender`, `date_of_birth`, `phone_number`, `email`, `address`, `emergency_contact_name`, `emergency_contact_phone`, `user_id`, `specialization`, `qualification`, `medical_license_no`, `registration_number`, `medical_council`, `experience_years`, `bio`, `department_id`, `joining_date`, `employment_status`, `contract_type`, `designation`, `opd_fee`, `ipd_charge_percentage`, `bank_account_number`, `bank_name`, `ifsc_code`, `pan_number`, `profile_picture`, `signature_image`, `resume_path`, `degree_certificate_path`, `license_certificate_path`, `other_certificates_path`, `is_available`, `status`, `last_login_at`, `created_at`, `updated_at`) VALUES
(15, 'DOC-250707-0001', 'raheem', 'raj', 'Male', '2025-07-01', '7897897897', 'doc@gmai.com', 'fsvfebsb', 'lingam', '7487477878', NULL, 'eye', 'mbbs', '2958453', '398593', 'ngerhe', 2, 'its a bio', 1, '2025-07-01', 'Full-time', 'temporary', 'senior eye specialist', 400.00, 15.00, 'SBI5572286', 'state bank of india', 'SBIN68', 'erxpp8768', '1755839005_a041515abcdb989e0ffb.jpg', NULL, NULL, NULL, NULL, '[]', 1, 'Active', '2025-07-06 11:13:00', '2025-07-07 05:43:58', '2025-08-22 05:03:25'),
(18, 'DOC-250707-0004', 'ramu', 'raj', 'Male', '2025-07-01', '7897897897', 'doc@gmai.com', 'fsvfebsb', 'lingam', '7487477878', NULL, 'eye', 'mbbs', '29584433', '398593', 'ngerhe', 2, 'its a bio', 1, '2025-07-01', 'Full-time', 'temporary', 'senior eye specialist', 400.00, 15.00, 'SBI5572286', 'state bank of india', 'SBIN68', 'erxpp8768', NULL, NULL, NULL, NULL, NULL, NULL, 1, 'Active', '2025-07-06 11:13:00', '2025-07-07 06:05:36', '2025-07-07 06:05:36'),
(25, 'DOC-250707-0011', 'divya', 'rani', '', '0000-00-00', '', 'bhavicreations@gmail.com', '', '', '', 6, 'heart ', '', '', '', '', 0, '', 1, '0000-00-00', '', '', '', 0.00, 0.00, '', '', '', '', NULL, NULL, NULL, NULL, NULL, NULL, 1, 'Active', NULL, '2025-07-07 12:23:24', '2025-07-07 12:23:24'),
(26, 'DOC-250707-0012', 'mango', 'seed', 'Male', '2025-07-01', '7897897897', 'bhavicreations@gmail.com', 'sb g', 'rao', '3213213213', 7, 'lungs', 'mbbsd', '2958453', '19990000', 'ngerhe', 4, 'fdnzg', 1, '2025-07-01', 'Full-time', 'temporary', 'senior eye specialist', 232.00, 2.00, 'SBI5572286', 'state bank of india', 'SBIN68', 'erxpp8768', '1751891154_2a9fb865dccb14e65b39.png', '1751891154_ea4ed32b08b0aec2bd0d.png', '1751968038_14b1fad366eddcf3826c.png', '1751891154_44923ec93f9c96c4b1c7.pdf', '1751891154_b843355d4dc97c7ec8d7.jpg', '[]', 1, 'Active', NULL, '2025-07-07 12:25:54', '2025-07-08 09:47:18'),
(27, 'DOC-250708-0013', 'mango', 'seed', 'Male', '2025-07-01', '7897897897', 'bhavicreations@gmail.com', 'sb g', 'rao', '3213213213', NULL, 'lungs', 'mbbs', '2958453', '19990000', 'ngerhe', 4, 'fdnzg', 1, '2025-07-01', 'Full-time', 'temporary', 'senior eye specialist', 232.00, 2.00, 'SBI5572286', 'state bank of india', 'SBIN68', 'erxpp8768', NULL, NULL, NULL, NULL, NULL, NULL, 1, 'Active', NULL, '2025-07-08 05:23:37', '2025-07-08 05:23:37'),
(28, 'DOC-250708-0014', 'raheem', 'raj', 'Male', '2025-07-01', '7897897897', 'doc@gmai.com', 'fsvfebsb', 'lingam', '7487477878', NULL, 'eye', 'mbbs', '2958453', '398593', 'ngerhe', 2, 'its a bio', 1, '2025-07-01', 'Full-time', 'temporary', 'senior eye specialist', 400.00, 15.00, 'SBI5572286', 'state bank of india', 'SBIN68', 'erxpp8768', NULL, NULL, NULL, NULL, NULL, NULL, 1, 'Active', NULL, '2025-07-08 05:24:12', '2025-07-08 05:24:12'),
(29, 'DOC-250708-0015', 'mango', 'seed', 'Male', '2025-07-01', '7897897897', 'bhavicreations@gmail.com', 'sb g', 'rao', '3213213213', NULL, 'lungs', 'mbbs', '2958453', '19990000', 'ngerhe', 4, 'fdnzg', 2, '2025-07-01', 'Full-time', 'temporary', 'senior eye specialist', 232.00, 2.00, 'SBI5572286', 'state bank of india', 'SBIN68', 'erxpp8768', NULL, NULL, NULL, NULL, NULL, NULL, 1, 'Active', NULL, '2025-07-08 06:09:21', '2025-07-08 06:09:21'),
(33, 'DOC-250708-0019', 'mango', 'seed', 'Male', '2025-07-01', '7897897897', 'bhavicreations@gmail.com', 'sb g', 'rao', '3213213213', NULL, 'lungs', 'mbbs', '2958453', '19990000', 'ngerhe', 4, 'fdnzg', 1, '2025-07-01', 'Full-time', 'temporary', 'senior eye specialist', 232.00, 2.00, 'SBI5572286', 'state bank of india', 'SBIN68', 'erxpp8768', NULL, NULL, NULL, NULL, NULL, NULL, 1, 'Active', NULL, '2025-07-08 06:27:34', '2025-07-08 06:27:34'),
(34, 'DOC-250708-0020', 'apple', 'first', 'Female', '2025-07-01', '5684446467', 'bhavicreations@gmail.com', 'rgw', 'rao', '3213213213', NULL, 'card', 'mbbs', '2958453', '398593', 'ngerhe', 2, 'fwef', 4, '2025-07-01', 'Part-time', 'temporary', 'senior eye specialist', 555.00, 5.00, 'SBI5572286', 'state bank of india', 'SBIN68', 'erxpp8768', '1751959289_66a997dec10acc7f3143.png', '1751959289_5504dcc64d3a2849b741.png', '1751959289_d2621d6903b37c4620e9.pdf', '1751959289_cb54a1dd621319ab5c54.jpeg', '1751959289_90b1cf00462503627984.jpeg', '[\"1751959289_7c340526bedabe82a099.pdf\",\"1751959289_cb3bb3aceb7cd49f1097.png\"]', 1, 'Active', NULL, '2025-07-08 07:21:29', '2025-07-08 07:21:29'),
(35, 'DOC-250708-0021', 'grapes', 'farm', '', '0000-00-00', '', '', '', '', '', NULL, 'heart ', '', '', '', '', 0, '', 1, '0000-00-00', '', '', '', 0.00, 0.00, '', '', '', '', NULL, NULL, NULL, NULL, '1751959712_7ea04c48b24feda3feeb.jpeg', '[\"1751959712_2f50b3280c13f47d4048.pdf\",\"1751959712_1bc4f5f5a25f95f83145.pdf\"]', 1, 'Active', NULL, '2025-07-08 07:28:32', '2025-07-08 07:28:32'),
(36, 'DOC-250708-0022', 'ggggg', 'ggg', '', '0000-00-00', '', '', '', '', '', NULL, 'lungs', '', '', '', '', 0, '', 3, '0000-00-00', '', '', '', 0.00, 0.00, '', '', '', '', '1751960220_c226f71dd15cc6c0c7a3.png', '1751960220_4d1072cd7daf5b6d097e.png', '1751960220_bce0822ab04eceabe7e4.pdf', '1751960220_7fedc199d4fa804222b6.png', '1751960220_e2f58b9a783047bc18b3.jpg', '[\"1751960220_d19e9b087d0b9635077c.pdf\",\"1751960220_adb2ab842da27f219693.png\",\"1751960220_60b5c3c90f5d06865977.pdf\"]', 1, 'Active', NULL, '2025-07-08 07:37:00', '2025-07-08 07:37:00'),
(37, 'DOC-250708-0023', 'litchi', 'fruit', 'Male', '2025-07-02', '345453543', 'bhavicreations@gmail.com', 'gefd', 'lingam', '3213213213', NULL, 'eye', 'degreee', '29584433', '19990000', 'ngerhe', 3, 'dfb', 7, '2025-07-03', 'Consultant', 'temporary', 'senior eye specialist', 444.00, 4.00, 'SBI5572286', 'state bank of india', 'SBIN68', 'erxpp8768', '1751960965_c4c7631b3f3fd5c739ca.jpg', '1751960965_3f671e1d8fa7fd37d761.png', '1751960965_2b7b1cfe2b593e77233c.pdf', '1751960965_806474ab2e20a68a770a.pdf', '1751960965_fc8cf96d98b8491c2a9c.pdf', '[\"1751960965_57a1472b534e0cd5f68f.pdf\",\"1751960965_1b0e37dfd939f961bc61.jpg\"]', 1, 'Active', NULL, '2025-07-08 07:49:25', '2025-07-08 07:49:25'),
(38, 'DOC-250708-0024', 'papaya', 'papaya', '', '0000-00-00', '', 'bhavicreations@gmail.com', '', '', '', 8, 'heart ', '', '', '', '', 0, '', 2, '0000-00-00', '', '', '', 0.00, 0.00, '', '', '', '', NULL, NULL, NULL, NULL, NULL, NULL, 1, 'Active', NULL, '2025-07-08 07:58:28', '2025-07-08 07:58:28'),
(39, 'DOC-250708-0025', 'watermelon', 'fruit', '', '0000-00-00', '', 'bhavicreations@gmail.com', '', '', '', 9, 'kidney', '', '', '', '', 0, '', 2, '0000-00-00', '', '', '', 0.00, 0.00, '', '', '', '', NULL, NULL, NULL, NULL, NULL, '[]', 1, 'Active', NULL, '2025-07-08 08:00:45', '2025-07-08 08:00:45'),
(40, 'DOC-250708-0026', 'bheam', 'bheam', '', '0000-00-00', '', 'abhi@gmail.com', '', '', '', 10, 'kidney', '', '', '', '', 0, '', 3, '0000-00-00', '', '', '', 0.00, 0.00, '', '', '', '', '1751961836_ba13246065a5b19b78af.jpg', '1751961836_a0131f0df4c02d787fb3.png', '1751961836_24375a0a097920c8fb67.pdf', '1751961836_daaffcc7fb6e65958727.pdf', '1751961836_faefe8a6d2daba726c7e.pdf', '[\"1751961836_1a53895813d0f03375d3.pdf\",\"1751961836_602309adf0e82cb656df.png\"]', 1, 'Active', NULL, '2025-07-08 08:03:56', '2025-07-08 08:03:56'),
(41, 'DOC-250708-0027', 'sharak', 'sharak', '', '0000-00-00', '', 'bhavicreations3022@gmail.come', '', '', '', 11, 'lasik', '', '', '', '', 0, '', 2, '0000-00-00', '', '', '', 0.00, 0.00, '', '', '', '', '1751966301_8340a3871ce5e8513d79.jpeg', '1751966301_06211f14c01cb23a0872.png', '1751966301_117b5707e9b58a2f4c20.pdf', '1751966301_1d52e8f447ea3488342e.png', '1751966301_1b6a691edec85926fd59.png', '[\"1751966301_595dbe16bf369d805360.pdf\",\"1751966301_5a55f267d7f38e29f84f.jpg\"]', 1, 'Active', NULL, '2025-07-08 09:18:21', '2025-07-08 09:18:21'),
(42, 'DOC-250708-0028', 'ramun', 'creations', '', '0000-00-00', '', 'bhavicreations@gmail.com', '', '', '', 12, 'heart ', '', '', '', '', 0, '', 3, '0000-00-00', '', '', '', 0.00, 0.00, '', '', '', '', '1751966513_d2773bcea54b85f2ea29.jpeg', '1751966513_0b3211291bb410e52a5d.png', '1751966513_deae65bb5153b8bcc444.pdf', '1751968618_5118cbfa2db17f1063a5.pdf', NULL, '[]', 1, 'Active', NULL, '2025-07-08 09:21:53', '2025-07-08 11:15:23'),
(43, 'DOC-250724-0029', 'ramesh', 'pilli', 'Male', '2008-01-24', '7897897895', 'rameshpilli@gmail.com', 'kkkd', 'ram', '6547896547', 13, 'lungs', 'mbbs', '295845324', '494989', 'council', 2, 'bio will', 3, '2025-07-16', 'Full-time', 'temporary', 'lungs spealis', 300.00, 2.00, '68768768768', 'SBI', 'SBIN68', 'erxpp8768', NULL, NULL, NULL, NULL, NULL, '[]', 1, 'Active', NULL, '2025-07-24 10:45:53', '2025-07-24 10:45:53'),
(44, 'DOC-250725-0030', 'Harsh vardhan', 'Basara', 'Male', '2002-01-25', '7531598254', 'abhvasi@gmail.com', '', '', '', 14, 'bones', '', '', '', '', 0, '', 4, '2025-07-24', '', '', '', 400.00, 2.00, '', '', '', '', NULL, NULL, NULL, NULL, NULL, '[]', 1, 'Active', NULL, '2025-07-25 05:13:34', '2025-07-25 05:13:34');

-- --------------------------------------------------------

--
-- Table structure for table `doctor_id_sequences`
--

CREATE TABLE `doctor_id_sequences` (
  `name` varchar(50) NOT NULL,
  `current_value` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `doctor_id_sequences`
--

INSERT INTO `doctor_id_sequences` (`name`, `current_value`) VALUES
('doctor_sequence', 30);

-- --------------------------------------------------------

--
-- Table structure for table `hospital_departments`
--

CREATE TABLE `hospital_departments` (
  `id` int(11) UNSIGNED NOT NULL,
  `name` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `deleted_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `hospital_departments`
--

INSERT INTO `hospital_departments` (`id`, `name`, `description`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 'Cardiology', 'Specializes in heart-related conditions.', '2025-07-07 15:26:08', '2025-07-07 15:26:08', NULL),
(2, 'Pediatrics', 'Focuses on the medical care of infants, children, and adolescents.', '2025-07-07 15:26:08', '2025-07-07 15:26:08', NULL),
(3, 'General Medicine', 'Deals with the prevention, diagnosis, and treatment of adult diseases.', '2025-07-07 15:26:08', '2025-07-07 15:26:08', NULL),
(4, 'Orthopedics', 'Concerned with conditions involving the musculoskeletal system.', '2025-07-07 15:26:08', '2025-07-07 15:26:08', NULL),
(5, 'Dermatology', 'Specializes in conditions of the skin, hair, and nails.', '2025-07-07 15:26:08', '2025-07-07 15:26:08', NULL),
(6, 'Neurology', 'Deals with disorders of the nervous system.', '2025-07-07 15:26:08', '2025-07-07 15:26:08', NULL),
(7, 'Oncology', 'Focuses on the diagnosis and treatment of cancer.', '2025-07-07 15:26:08', '2025-07-07 15:26:08', NULL),
(8, 'Emergency Department', 'Provides immediate treatment for acute illnesses and injuries.', '2025-07-07 15:26:08', '2025-07-07 15:26:08', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `lab_orders`
--

CREATE TABLE `lab_orders` (
  `id` int(11) NOT NULL,
  `order_id_code` varchar(255) DEFAULT NULL,
  `patient_id` int(11) NOT NULL,
  `ordered_by` int(11) NOT NULL,
  `order_date` datetime NOT NULL DEFAULT current_timestamp(),
  `status` enum('Pending','Completed','Cancelled') DEFAULT 'Pending',
  `remarks` text DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `lab_orders`
--

INSERT INTO `lab_orders` (`id`, `order_id_code`, `patient_id`, `ordered_by`, `order_date`, `status`, `remarks`, `created_at`, `updated_at`) VALUES
(1, 'LABRPOS-20250925-0001', 37, 1, '2025-09-23 11:38:04', 'Completed', 'three s\r\n', '2025-09-23 17:08:04', '2025-09-25 10:26:44'),
(2, 'LABRPOS-20250925-0002', 36, 1, '2025-09-23 12:03:17', 'Completed', 'd', '2025-09-23 17:33:17', '2025-09-25 09:57:11'),
(4, 'LABRPOS-20250925-0004', 32, 1, '2025-09-25 06:08:42', 'Completed', 'dsv', '2025-09-25 06:08:42', '2025-09-25 09:54:20');

-- --------------------------------------------------------

--
-- Table structure for table `lab_order_files`
--

CREATE TABLE `lab_order_files` (
  `id` int(11) NOT NULL,
  `lab_order_item_id` int(11) NOT NULL,
  `file_name` varchar(255) NOT NULL,
  `file_path` varchar(255) NOT NULL,
  `uploaded_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `lab_order_files`
--

INSERT INTO `lab_order_files` (`id`, `lab_order_item_id`, `file_name`, `file_path`, `uploaded_at`) VALUES
(8, 5, 'Gemini_Generated_Image_13lkrv13lkrv13lk.png', '1758794060_b79a0cb6903c61b3efdf.png', '2025-09-25 09:54:20'),
(10, 13, 'th_1747721447_2024393584.jpg', '1758796004_9b7f6389b46361c0ac7e.jpg', '2025-09-25 10:26:44');

-- --------------------------------------------------------

--
-- Table structure for table `lab_order_items`
--

CREATE TABLE `lab_order_items` (
  `id` int(11) NOT NULL,
  `lab_order_id` int(11) NOT NULL,
  `lab_test_id` int(11) NOT NULL,
  `result` text DEFAULT NULL,
  `result_date` datetime DEFAULT NULL,
  `status` enum('Not Started','In Progress','Completed') DEFAULT 'Not Started',
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `lab_order_items`
--

INSERT INTO `lab_order_items` (`id`, `lab_order_id`, `lab_test_id`, `result`, `result_date`, `status`, `created_at`, `updated_at`) VALUES
(5, 4, 2, 'positive', '2025-09-25 09:54:20', 'Completed', '2025-09-25 06:08:42', '2025-09-25 09:54:20'),
(11, 2, 2, NULL, NULL, '', '2025-09-25 09:57:11', '2025-09-25 09:57:11'),
(13, 1, 2, 'on gong ', '2025-09-25 10:26:44', 'Completed', '2025-09-25 10:26:02', '2025-09-25 10:26:44');

-- --------------------------------------------------------

--
-- Table structure for table `lab_tests`
--

CREATE TABLE `lab_tests` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `test_type_id` int(11) NOT NULL,
  `price` decimal(10,2) DEFAULT 0.00,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `lab_tests`
--

INSERT INTO `lab_tests` (`id`, `name`, `description`, `test_type_id`, `price`, `created_at`, `updated_at`) VALUES
(1, 'Complete Blood Count (CBC)', 'Measures levels of red blood cells, white blood cells, etc.', 1, 100.00, '2025-09-23 11:07:54', '2025-09-23 11:07:54'),
(2, 'Blood Glucose Test	', 'Measures blood sugar level	', 2, 50.00, '2025-09-23 11:08:26', '2025-09-23 11:08:26'),
(3, 'Lipid Panel	', 'Measures cholesterol and triglycerides	', 2, 120.00, '2025-09-23 11:08:45', '2025-09-23 11:08:45'),
(4, 'Thyroid Stimulating Hormone (TSH) Test	', 'Evaluates thyroid function	', 7, 150.00, '2025-09-23 11:09:09', '2025-09-23 11:09:09'),
(5, 'Urinalysis', 'Analyzes urine for abnormalities	', 9, 70.00, '2025-09-23 11:09:24', '2025-09-23 11:09:24'),
(6, 'Prothrombin Time (PT)	', 'Measures blood clotting time	', 1, 880.00, '2025-09-23 11:09:48', '2025-09-25 06:52:21'),
(7, 'C-reactive Protein (CRP)	', 'Measures inflammation in the body	', 4, 90.00, '2025-09-23 11:10:05', '2025-09-23 11:10:05'),
(8, 'Vitamin D	', 'Measures vitamin D levels	', 7, 130.00, '2025-09-23 11:10:32', '2025-09-23 11:10:32'),
(9, 'Serum Creatinine	', 'Tests kidney function	', 2, 75.00, '2025-09-23 11:10:49', '2025-09-23 11:10:49'),
(10, 'Liver Function Test (LFT)', 'Evaluates liver enzymes and proteins	', 2, 140.00, '2025-09-23 11:11:07', '2025-09-23 11:19:30');

-- --------------------------------------------------------

--
-- Table structure for table `lab_test_types`
--

CREATE TABLE `lab_test_types` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `lab_test_types`
--

INSERT INTO `lab_test_types` (`id`, `name`, `description`, `created_at`, `updated_at`) VALUES
(1, 'Hematology', 'Tests related to blood, blood cells, and coagulation disorders.', '2025-09-23 07:59:07', '2025-09-23 07:59:37'),
(2, 'Biochemistry', 'Tests evaluating organ function and chemical balance in the blood.\r\n', '2025-09-23 07:59:18', '2025-09-23 07:59:18'),
(3, 'Microbiology', 'Detection of infectious agents like bacteria, viruses, and fungi.\r\n', '2025-09-23 07:59:54', '2025-09-23 07:59:54'),
(4, 'Immunology	', 'Tests examining the immune system\'s functioning and responses.\r\n', '2025-09-23 08:00:06', '2025-09-23 08:00:06'),
(5, 'Molecular Diagnostics	', 'Genetic and DNA/RNA-based tests for inherited or infectious diseases.\r\n', '2025-09-23 08:00:15', '2025-09-23 08:00:15'),
(6, 'Serology', 'Detection of antibodies and antigens in the blood for infections.\r\n', '2025-09-23 08:00:26', '2025-09-23 08:00:26'),
(7, 'Hormone Testing	', 'Measurement of hormones regulating bodily functions.\r\n', '2025-09-23 08:00:37', '2025-09-23 08:00:37'),
(8, 'Toxicology', 'Detection of toxins, drugs, and poisons in body fluids.\r\n', '2025-09-23 08:00:47', '2025-09-23 08:00:47'),
(9, 'Urinalysis', 'Tests analyzing urine composition and abnormalities.\r\n', '2025-09-23 08:00:56', '2025-09-23 08:00:56'),
(10, 'Tumor Markers	', 'Tests for proteins indicating presence of certain cancers.\r\n', '2025-09-23 08:01:16', '2025-09-23 08:01:16');

-- --------------------------------------------------------

--
-- Table structure for table `patients`
--

CREATE TABLE `patients` (
  `id` int(11) NOT NULL,
  `patient_id_code` varchar(50) DEFAULT NULL,
  `patient_type` enum('General','OPD','IPD','Casualty','Discharged') NOT NULL DEFAULT 'General',
  `previous_patient_type` enum('General','OPD','Casualty') DEFAULT NULL,
  `opd_id_code` varchar(50) DEFAULT NULL,
  `ipd_id_code` varchar(50) DEFAULT NULL,
  `gen_id_code` varchar(50) DEFAULT NULL,
  `cus_id_code` varchar(50) DEFAULT NULL,
  `first_name` varchar(100) NOT NULL,
  `last_name` varchar(100) NOT NULL,
  `date_of_birth` date NOT NULL,
  `gender` enum('Male','Female','Other') NOT NULL,
  `blood_group` varchar(50) DEFAULT NULL,
  `marital_status` varchar(50) DEFAULT NULL,
  `occupation` varchar(100) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `phone_number` varchar(20) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `emergency_contact_name` varchar(255) DEFAULT NULL,
  `emergency_contact_phone` varchar(20) DEFAULT NULL,
  `known_allergies` text DEFAULT NULL,
  `pre_existing_conditions` text DEFAULT NULL,
  `referred_to_doctor_id` int(11) DEFAULT NULL,
  `referred_by_id` int(11) DEFAULT NULL,
  `remarks` text DEFAULT NULL,
  `reports` text DEFAULT NULL,
  `fee` decimal(10,2) DEFAULT 0.00,
  `discount_percentage` decimal(5,2) DEFAULT 0.00,
  `final_amount` decimal(10,2) DEFAULT 0.00,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `patients`
--

INSERT INTO `patients` (`id`, `patient_id_code`, `patient_type`, `previous_patient_type`, `opd_id_code`, `ipd_id_code`, `gen_id_code`, `cus_id_code`, `first_name`, `last_name`, `date_of_birth`, `gender`, `blood_group`, `marital_status`, `occupation`, `address`, `phone_number`, `email`, `emergency_contact_name`, `emergency_contact_phone`, `known_allergies`, `pre_existing_conditions`, `referred_to_doctor_id`, `referred_by_id`, `remarks`, `reports`, `fee`, `discount_percentage`, `final_amount`, `created_at`, `updated_at`) VALUES
(4, 'PAT-250628-00001', 'Discharged', NULL, 'OPD-250628-00001', NULL, 'GEN-250724-00015', NULL, 'ram ram hai', 'raj ', '2025-06-09', 'Male', 'A+', 'single ', 'student', 'ttd', '7897897897', 'bhavicreations3022@gmail.comettttt', 'ttttt', '3213213213', 'errrrt', 't', NULL, 2, 'fg', '[\"1751439791_7786_12345.pdf\",\"1751439814_7833_Sales_by_nanna_20250614_062022.pdf\"]', 606.00, 6.00, 569.64, '2025-06-28 03:48:50', '2025-07-28 07:14:28'),
(5, 'PAT-250628-00002', 'General', NULL, 'OPD-250628-00002', NULL, 'GEN-250709-00014', NULL, 'ramu', 'ling', '2025-06-06', 'Male', '', '', '', 'hg', '7897897897', 'bhavicreations3022@gmail.come', 'rao', '3213213213', 'ytf', 'yf', NULL, NULL, '', NULL, 6660.00, 60.00, 2664.00, '2025-06-28 03:49:50', '2025-07-09 04:05:46'),
(7, 'PAT-250628-00001', 'Discharged', NULL, NULL, NULL, 'GEN-250628-00001', NULL, 'raja', 'creations', '2025-06-12', 'Male', 'A+', 'single ', 'student', 'dgbgggggggggg', '7897897897', 'bhavicreations@gmail.com', 'rao', '3213213213', 'gfs', 'srtg', NULL, 1, 'dgt', '[\"uploads\\/patient_reports\\/1751085590_031e227b11d0d6ac3658.jpg\"]', 1222.00, 1.00, 1209.78, '2025-06-28 04:39:50', '2025-07-28 07:12:21'),
(8, 'PAT-250628-00002', 'Discharged', NULL, 'OPD-250705-00008', NULL, 'GEN-250628-00002', NULL, 'raja', 'creations', '2025-06-01', 'Male', 'B+', 'single ', 'student', 'fd', '7897897897', 'vgv@gmail.com', 'rao', '3213213213', 'gf', 'gf', NULL, 1, 'f', '[\"uploads\\/patient_reports\\/1751085659_9d981954beb66d21e9ad.png\"]', 1345.00, 1.00, 1331.55, '2025-06-28 04:40:59', '2025-07-28 09:23:22'),
(9, 'PAT-250628-00003', 'Discharged', NULL, 'OPD-250628-00001', NULL, NULL, NULL, 'raja', 'creations ', '2025-06-04', 'Female', 'o+', 'single ', 'fd', 'df', '7897897897', 'latha@gmail.com', 'rao', '3213213213', 'febd', 'erb', NULL, 1, 'fet', '[\"uploads\\/patient_reports\\/1751085717_226cc379f0d3c564a203.png\"]', 3453.00, 4.00, 3314.88, '2025-06-28 04:41:57', '2025-07-28 09:28:15'),
(10, 'PAT-250628-00004', 'General', NULL, NULL, NULL, 'GEN-250728-00020', 'CUS-250628-00001', 'ramu', 'raju', '2025-06-02', 'Other', 'A+', 'single ', 'student', 'rgvs', '7897897897', 'latha@gmail.com', 'rao', '3213213213', 'srg', 'sreg', NULL, 1, 'czsrszgg', '[\"uploads\\/patient_reports\\/1751085772_a098277ebe53be0504f7.png\"]', 43234.00, 5.00, 41072.30, '2025-06-28 04:42:52', '2025-07-28 09:30:54'),
(11, 'PAT-250628-00005', 'Discharged', NULL, NULL, 'IPD-250728-00045', 'GEN-250628-00003', NULL, 'ramu', 'eeeee', '2025-06-10', 'Female', 'A+', 'single ', 'student', 'eaf', '7897897897', 'visoi@gmail.com', 'ttttt', '3213213213', 'sdWvge', 'wegv', NULL, 1, 'vdew', '[\"1751086114_96a6950429f76305c189.jpg\"]', 534.00, 3.00, 517.98, '2025-06-28 04:48:34', '2025-07-28 11:31:41'),
(13, 'PAT-250628-00007', 'Discharged', NULL, 'OPD-250628-00003', NULL, NULL, NULL, 'ramu', 'creations', '2025-06-11', 'Male', 'A+', 'rer', 'er', 'ef bes', '7897897897', 'bhavicreations@gmail.com', 'rao', '3213213213', 'esr', 'er', NULL, 2, '', '[\"1751698881_2515_vk poster 9.png\"]', 555.00, 5.00, 527.25, '2025-06-28 07:31:24', '2025-07-28 09:31:36'),
(14, 'PAT-250628-00008', 'Discharged', NULL, 'OPD-250628-00004', NULL, NULL, NULL, 'raja', 'healthcare', '2025-06-14', 'Female', 'A+', 'single ', 'student', 'ewf', '2525252525', 'bhavicreations@gmail.com', 'rao', '3213213213', 'wef', 'wef', NULL, 1, 'dvse', '[\"1751095934_657b769b4755bdc13eb1.png\"]', 2432.00, 4.00, 2334.72, '2025-06-28 07:32:14', '2025-07-28 10:15:25'),
(15, 'PAT-250628-00010', 'Discharged', NULL, NULL, 'IPD-250728-00046', 'GEN-250628-00005', NULL, 'jana', 'creations', '2025-06-10', 'Male', 'B-', 'single ', 'student', 'hgmd', '2525252525', 'bhavicreations@gmail.com', 'rao', '3213213213', 'ghg', 'dhg', NULL, 3, 'fc', NULL, 645.00, 5.00, 612.75, '2025-06-28 09:18:19', '2025-07-28 11:46:53'),
(16, 'PAT-250628-00011', 'Discharged', NULL, NULL, 'IPD-250705-00032', 'GEN-250628-00006', NULL, 'ramu', 'ling', '2025-06-10', 'Male', '', '', '', '', '', '', '', '', '', '', NULL, NULL, '', NULL, 0.00, 0.00, 0.00, '2025-06-28 09:41:39', '2025-07-28 11:36:12'),
(17, 'PAT-250628-00012', 'IPD', 'General', NULL, 'IPD-250710-00043', 'GEN-250628-00007', NULL, 'niranjan', 'wfw', '2025-06-24', 'Male', 'AB-', 'single ', '', '', '', '', '', '', '', '', NULL, NULL, '', NULL, 0.00, 0.00, 0.00, '2025-06-28 09:57:11', '2025-07-10 14:09:04'),
(18, 'PAT-250702-00013', 'Discharged', NULL, 'OPD-250702-00005', 'IPD-250705-00008', NULL, NULL, 'peter', 'raj', '2025-07-01', 'Female', 'A+', '', '', '', '', '', '', '', '', '', NULL, NULL, '', NULL, 0.00, 0.00, 0.00, '2025-07-02 04:40:53', '2025-07-29 06:02:38'),
(19, 'PAT-250702-00014', 'Discharged', NULL, NULL, NULL, 'GEN-250702-00008', NULL, 'ratnam', 'dvfd', '2025-07-01', 'Male', '', '', '', '', '', '', '', '', '', '', NULL, NULL, '', NULL, 0.00, 0.00, 0.00, '2025-07-02 04:46:56', '2025-07-05 12:07:19'),
(20, 'PAT-250702-00015', 'IPD', NULL, NULL, 'IPD-250705-00016', 'GEN-250702-00009', NULL, 'bravo', 'marco', '2025-07-01', 'Male', 'A+', 'single ', 'student', 'fdb ttrbn', '1231231231', 'bhavicreations@gmail.com', 'rao', '3213213213', 'dtr', 'trf s', NULL, 1, 'fdsd', '[\"1751433226_8683_arjuna online iti _1_.pdf\",\"1751433226_5226_stock-vector-alphabet-letters-icon-logo-vk-or-kv-monogram-2203519181_1747721447.jpg\",\"1751433226_3706_vk logo 6.jpg\"]', 5555.00, 5.00, 5277.25, '2025-07-02 05:13:46', '2025-07-25 09:41:07'),
(21, 'PAT-250702-00016', 'IPD', 'General', NULL, 'IPD-250726-00044', 'GEN-250702-00010', NULL, 'ramu', 'ling', '2025-07-01', 'Male', 'A+', '', '', '', '', '', '', '', '', '', NULL, NULL, '', '[\"1751434062_8995_Vendor_Report_20250607_102423.pdf\",\"1751434062_7977_vk new 4.png\"]', 0.00, 0.00, 0.00, '2025-07-02 05:27:42', '2025-07-26 09:53:29'),
(22, 'PAT-250702-00017', 'IPD', NULL, NULL, 'IPD-250705-00002', NULL, 'CUS-250702-00002', 'ramuraju', 'creations', '2025-06-11', 'Male', '', '', '', '', '', '', '', '', '', '', NULL, NULL, '', NULL, 0.00, 0.00, 0.00, '2025-07-02 11:53:40', '2025-07-05 06:43:02'),
(23, 'PAT-250705-00018', 'IPD', 'General', 'OPD-250705-00006', 'IPD-250705-00041', 'GEN-250705-00013', NULL, 'vinay', 'trh', '2025-04-01', 'Male', 'O+', '', '', '', '', '', '', '', '', '', NULL, NULL, '', '[\"1751688337_4093_stock_in_details_13.pdf\",\"1751688337_8056_vk poster 9.png\"]', 0.00, 0.00, 0.00, '2025-07-05 04:05:37', '2025-07-05 12:03:30'),
(24, 'PAT-250705-00019', 'IPD', NULL, NULL, 'IPD-250705-00034', 'GEN-250705-00011', NULL, 'mango', 'kai', '2025-07-01', 'Male', '', '', '', '', '', '', '', '', '', '', NULL, NULL, '', NULL, 0.00, 0.00, 0.00, '2025-07-05 10:05:39', '2025-07-05 10:56:00'),
(25, 'PAT-250705-00020', 'IPD', 'General', NULL, 'IPD-250705-00040', 'GEN-250705-00012', NULL, 'apple', 'zz', '2025-07-02', 'Female', '', '', '', '', '', '', '', '', '', '', NULL, NULL, '', NULL, 0.00, 0.00, 0.00, '2025-07-05 10:06:05', '2025-07-05 12:03:06'),
(26, 'PAT-250705-00021', 'IPD', NULL, 'OPD-250705-00009', 'IPD-250705-00036', NULL, NULL, 'banana', 'raju', '2025-07-01', 'Female', '', '', '', '', '9988997789', '', '', '', '', '', 43, NULL, '', NULL, 0.00, 0.00, 0.00, '2025-07-05 10:06:22', '2025-09-02 15:22:43'),
(27, 'PAT-250705-00022', 'Discharged', NULL, 'OPD-250705-00010', NULL, NULL, NULL, 'jack', 'fruit', '2025-07-02', 'Other', '', '', '', '', '', '', '', '', '', '', NULL, NULL, '', NULL, 0.00, 0.00, 0.00, '2025-07-05 10:06:49', '2025-07-09 04:07:20'),
(28, 'PAT-250705-00023', 'IPD', 'OPD', 'OPD-250705-00011', 'IPD-250822-00050', NULL, NULL, 'painapple', 'ss', '2025-07-01', 'Other', '', '', '', '', '', '', '', '', '', '', NULL, NULL, '', NULL, 0.00, 0.00, 0.00, '2025-07-05 10:07:06', '2025-08-22 05:32:36'),
(29, 'PAT-250705-00024', 'IPD', NULL, NULL, 'IPD-250705-00026', NULL, 'CUS-250705-00003', 'rappa', 'rappa', '2025-07-01', 'Female', '', '', '', '', '', '', '', '', '', '', NULL, NULL, '', NULL, 0.00, 0.00, 0.00, '2025-07-05 10:07:25', '2025-07-05 10:33:00'),
(30, 'PAT-250705-00025', 'Discharged', NULL, NULL, NULL, NULL, 'CUS-250705-00004', 'promo', 'granite', '2025-07-01', 'Other', '', '', '', '', '', '', '', '', '', '', NULL, NULL, '', NULL, 0.00, 0.00, 0.00, '2025-07-05 10:07:56', '2025-07-28 11:17:07'),
(31, 'PAT-250709-00026', 'General', NULL, NULL, NULL, 'GEN-250729-00021', 'CUS-250709-00005', 'sample', 'patient', '2025-07-01', 'Female', '', '', '', '', '', '', '', '', '', '', 26, NULL, '', NULL, 232.00, 0.00, 232.00, '2025-07-09 05:20:04', '2025-07-29 07:08:29'),
(32, 'PAT-250709-00027', 'Casualty', NULL, NULL, NULL, NULL, 'CUS-250709-00006', 'ramu', 'creations', '2025-07-03', 'Male', '', '', '', '', '', '', '', '', '', '', 15, NULL, '', NULL, 400.00, 8.00, 368.00, '2025-07-09 05:46:50', '2025-07-09 05:46:50'),
(33, 'PAT-250709-00028', 'IPD', 'Casualty', NULL, 'IPD-250822-00049', NULL, 'CUS-250709-00007', 'wednesday ', 'week', '2025-07-01', 'Male', '', '', '', 'kkd', '9898989898', '', '', '', '', '', 18, NULL, '', NULL, 400.00, 2.00, 392.00, '2025-07-09 06:50:10', '2025-09-02 13:00:23'),
(34, 'PAT-250724-00029', 'General', NULL, NULL, NULL, 'GEN-250724-00016', NULL, 'janu', 'wing', '2025-07-25', 'Male', 'A+', 'single ', '', 'kkd', '7897897897', 'bhavicreatiodns@gmail.com', 'ttttt', '3213213213', 'e', 'e', 15, 1, 'k', NULL, 400.00, 0.00, 400.00, '2025-07-24 05:27:15', '2025-07-24 05:27:15'),
(35, 'PAT-250724-00030', 'General', NULL, NULL, NULL, 'GEN-250724-00017', NULL, 'kumar', 'gorri', '2025-07-07', 'Male', '', '', '', '', '', '', '', '', '', '', 43, 2, '', NULL, 300.00, 0.00, 300.00, '2025-07-24 10:50:15', '2025-07-24 10:50:15'),
(36, 'PAT-250724-00031', 'General', NULL, NULL, NULL, 'GEN-250724-00018', NULL, 'kanta', 'mani', '2025-07-02', 'Male', 'A-', '', '', '', '', '', '', '', '', '', 43, NULL, 'hi ', '[\"1753355997_7591_bhavi logo.png\",\"1753356877_dd4e96ceb6e0e64dd084.pdf\"]', 300.00, 1.00, 297.00, '2025-07-24 11:19:57', '2025-07-24 11:39:58'),
(37, 'PAT-250725-00032', 'General', NULL, NULL, 'IPD-250729-00047', 'GEN-250725-00019', NULL, 'naidu', 'raddy', '2025-07-09', 'Male', 'B-', '', '', '', '4564564564', '', '', '', '', '', 43, 3, 'g', '[\"1753703819_6435_sample-blue-round-grunge-stamp-J1D0R6.jpg\"]', 300.00, 0.00, 300.00, '2025-07-25 05:09:08', '2025-07-29 05:52:22'),
(38, 'PAT-250822-00033', 'Discharged', NULL, 'OPD-250822-00012', 'IPD-250822-00051', 'GEN-250822-00022', 'CUS-250822-00008', 'jaswanth', 'raj', '2019-01-07', 'Male', 'O+', 'single ', 'student', 'rajya', '4564564564', 'visoi@gmail.com', 'rao', '7487477878', 'as', 'as', 43, 3, 'raj', '[\"1755838813_4858_WhatsApp Image 2025-08-06 at 10.24.53 AM.jpeg\"]', 300.00, 1.00, 297.00, '2025-08-22 05:00:13', '2025-08-22 06:48:42');

-- --------------------------------------------------------

--
-- Table structure for table `patient_admissions`
--

CREATE TABLE `patient_admissions` (
  `id` int(11) UNSIGNED NOT NULL,
  `patient_id` int(11) NOT NULL,
  `ward_id` int(11) UNSIGNED DEFAULT NULL,
  `bed_id` int(11) UNSIGNED DEFAULT NULL,
  `admission_date` datetime NOT NULL,
  `discharge_date` datetime DEFAULT NULL,
  `admission_status` enum('Admitted','Discharged','Transferred','Waiting Assignment') NOT NULL DEFAULT 'Waiting Assignment',
  `notes` text DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `patient_admissions`
--

INSERT INTO `patient_admissions` (`id`, `patient_id`, `ward_id`, `bed_id`, `admission_date`, `discharge_date`, `admission_status`, `notes`, `created_at`, `updated_at`, `deleted_at`) VALUES
(5, 10, NULL, NULL, '2025-07-28 09:30:41', '2025-07-28 09:30:54', 'Discharged', '', '2025-07-28 09:30:41', '2025-07-28 09:30:54', NULL),
(6, 11, NULL, NULL, '2025-07-28 09:31:17', '2025-07-28 09:31:40', 'Discharged', '', '2025-07-28 09:31:17', '2025-07-28 09:31:40', NULL),
(7, 13, NULL, NULL, '2025-07-28 09:31:23', '2025-07-28 09:31:36', 'Discharged', '', '2025-07-28 09:31:23', '2025-07-28 09:31:36', NULL),
(8, 14, NULL, NULL, '2025-07-28 09:32:07', '2025-07-28 10:15:25', 'Discharged', '', '2025-07-28 09:32:07', '2025-07-28 10:15:25', NULL),
(9, 15, NULL, NULL, '2025-07-28 09:32:14', '2025-07-28 09:32:19', 'Discharged', '', '2025-07-28 09:32:14', '2025-07-28 09:32:19', NULL),
(10, 31, NULL, NULL, '2025-07-28 10:58:32', '2025-07-28 10:58:41', 'Discharged', '', '2025-07-28 10:58:32', '2025-07-28 10:58:41', NULL),
(11, 30, NULL, NULL, '2025-07-28 11:16:46', '2025-07-28 11:17:07', 'Discharged', '', '2025-07-28 11:16:46', '2025-07-28 11:17:07', NULL),
(12, 11, NULL, NULL, '2025-07-28 11:31:15', '2025-07-28 11:31:41', 'Discharged', 'Admitted to IPD, awaiting ward/bed assignment.', '2025-07-28 11:31:15', '2025-07-28 11:31:41', NULL),
(13, 16, NULL, NULL, '2025-07-28 11:35:59', '2025-07-28 11:36:11', 'Discharged', '', '2025-07-28 11:35:59', '2025-07-28 11:36:11', NULL),
(14, 15, NULL, NULL, '2025-07-28 11:46:07', '2025-07-28 11:46:53', 'Discharged', 'Admitted to IPD, awaiting ward/bed assignment.', '2025-07-28 11:46:07', '2025-07-28 11:46:53', NULL),
(15, 37, NULL, NULL, '2025-07-29 04:24:03', '2025-07-29 04:24:29', 'Discharged', 'Admitted to IPD, awaiting ward/bed assignment.', '2025-07-29 04:24:03', '2025-07-29 04:24:29', NULL),
(16, 18, NULL, NULL, '2025-07-29 06:02:34', '2025-07-29 06:02:38', 'Discharged', '', '2025-07-29 06:02:34', '2025-07-29 06:02:38', NULL),
(17, 38, NULL, NULL, '2025-08-22 05:10:20', '2025-08-22 06:45:36', 'Discharged', 'Admitted to IPD, awaiting ward/bed assignment.', '2025-08-22 05:10:20', '2025-08-22 06:45:36', NULL),
(18, 33, NULL, NULL, '2025-08-22 05:31:05', NULL, 'Waiting Assignment', 'Admitted to IPD, awaiting ward/bed assignment.', '2025-08-22 05:31:05', '2025-08-22 05:31:05', NULL),
(19, 28, NULL, NULL, '2025-08-22 05:32:36', NULL, 'Waiting Assignment', 'Admitted to IPD, awaiting ward/bed assignment.', '2025-08-22 05:32:36', '2025-08-22 05:32:36', NULL),
(20, 38, NULL, NULL, '2025-08-22 06:46:09', '2025-08-22 06:48:42', 'Discharged', 'Admitted to IPD, awaiting ward/bed assignment.', '2025-08-22 06:46:09', '2025-08-22 06:48:42', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `patient_id_sequences`
--

CREATE TABLE `patient_id_sequences` (
  `id` int(5) UNSIGNED NOT NULL,
  `prefix` varchar(50) NOT NULL COMMENT 'e.g., PAT, OPD, GEN, CUS, IPD',
  `next_sequence_number` bigint(20) UNSIGNED NOT NULL DEFAULT 1,
  `updated_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `patient_id_sequences`
--

INSERT INTO `patient_id_sequences` (`id`, `prefix`, `next_sequence_number`, `updated_at`) VALUES
(1, 'PAT', 33, '2025-08-22 10:30:13'),
(2, 'OPD', 12, '2025-08-22 10:32:16'),
(3, 'IPD', 51, '2025-08-22 12:16:09'),
(4, 'GEN', 22, '2025-08-22 10:30:13'),
(5, 'CUS', 8, '2025-08-22 10:31:07');

-- --------------------------------------------------------

--
-- Table structure for table `pharmacy_batches`
--

CREATE TABLE `pharmacy_batches` (
  `id` int(11) NOT NULL,
  `medicine_id` int(11) NOT NULL,
  `batch_number` varchar(100) NOT NULL,
  `manufacturing_date` date NOT NULL,
  `expiry_date` date NOT NULL,
  `initial_quantity` int(11) NOT NULL,
  `current_stock` int(11) NOT NULL DEFAULT 0,
  `purchase_price` decimal(10,2) NOT NULL,
  `selling_price` decimal(10,2) NOT NULL,
  `supplier_id` int(11) NOT NULL,
  `status` enum('available','expired','damaged','recalled') NOT NULL DEFAULT 'available',
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `pharmacy_batches`
--

INSERT INTO `pharmacy_batches` (`id`, `medicine_id`, `batch_number`, `manufacturing_date`, `expiry_date`, `initial_quantity`, `current_stock`, `purchase_price`, `selling_price`, `supplier_id`, `status`, `created_at`, `updated_at`) VALUES
(9, 13, '34r3f4t3523', '2025-07-27', '2025-08-28', 25, 11, 2.00, 22.00, 1, 'available', '2025-08-04 11:41:06', '2025-09-09 04:14:46'),
(11, 12, '34r3f4t356234', '2025-08-03', '2025-09-06', 145, 88, 11.00, 22.00, 1, 'available', '2025-08-04 11:45:20', '2025-09-19 08:02:35'),
(12, 13, '34r3f4t3r', '2025-08-11', '2025-09-06', 5, 3, 10.00, 30.00, 5, 'available', '2025-08-23 06:40:08', '2025-08-28 11:54:39'),
(13, 12, '34r3f4t3523t', '2025-08-25', '2025-09-06', 20, 0, 20.00, 30.00, 1, 'available', '2025-08-25 06:23:01', '2025-09-18 12:28:23'),
(14, 12, '34r3f4t35w', '2025-08-26', '2025-08-26', 30, 0, 6.00, 66.00, 5, 'available', '2025-08-25 06:24:11', '2025-09-12 05:07:11'),
(15, 12, '34r3f4t356q', '2025-08-26', '2025-10-09', 22, 22, 12.00, 122.00, 1, 'available', '2025-08-25 07:17:09', '2025-08-29 09:38:16'),
(16, 15, '34r3f4t353d', '2025-08-25', '2025-08-26', 3, 3, 3.00, 33.00, 7, 'available', '2025-08-25 12:22:28', '2025-08-25 12:22:39'),
(17, 17, '34r3f4t3565', '2025-08-26', '2025-08-27', 100, 100, 10.00, 100.00, 1, 'available', '2025-08-26 08:01:28', '2025-08-26 08:01:28'),
(18, 16, '34r3f4t35re', '2025-08-29', '2025-09-06', 55, 44, 5.00, 55.00, 1, 'available', '2025-08-29 10:17:30', '2025-09-09 04:09:06'),
(20, 19, '34r3f4t356', '2025-09-10', '2025-09-11', 50, 50, 20.00, 40.00, 1, 'available', '2025-09-10 09:58:51', '2025-09-10 10:03:04'),
(22, 21, '34r3f4t356q', '2025-09-09', '2025-10-11', 41, 31, 40.00, 80.00, 5, 'available', '2025-09-10 10:07:39', '2025-09-19 04:39:08'),
(23, 21, '34r3f4t35w', '2025-09-11', '2025-09-11', 20, 0, 10.00, 100.00, 7, 'available', '2025-09-10 10:14:06', '2025-09-18 07:07:25'),
(24, 21, '34r3f4t35ytfh', '2025-09-08', '2025-09-11', 550, 472, 50.00, 100.00, 1, 'available', '2025-09-10 10:14:33', '2025-09-19 08:02:35'),
(25, 21, '34r3f4t3523', '2025-09-10', '2025-09-11', 13, 0, 10.00, 20.00, 7, 'available', '2025-09-10 11:44:36', '2025-09-12 04:37:08'),
(26, 22, '34r3f4t356', '2025-09-10', '2025-09-11', 5, 5, 10.00, 50.00, 1, 'available', '2025-09-10 11:51:36', '2025-09-10 11:51:36'),
(27, 22, '34r3f4t35ytfh', '2025-09-01', '2025-09-11', 50, 50, 20.00, 30.00, 1, 'available', '2025-09-10 11:52:35', '2025-09-10 11:52:35');

-- --------------------------------------------------------

--
-- Table structure for table `pharmacy_billings`
--

CREATE TABLE `pharmacy_billings` (
  `id` int(11) NOT NULL,
  `patient_id` int(11) NOT NULL,
  `sales_person_id` int(11) DEFAULT NULL,
  `bill_id` varchar(255) NOT NULL,
  `bill_date` datetime NOT NULL,
  `total_amount` decimal(10,2) NOT NULL,
  `paid_amount` decimal(10,2) NOT NULL DEFAULT 0.00,
  `due_amount` decimal(10,2) NOT NULL DEFAULT 0.00,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `pharmacy_billings`
--

INSERT INTO `pharmacy_billings` (`id`, `patient_id`, `sales_person_id`, `bill_id`, `bill_date`, `total_amount`, `paid_amount`, `due_amount`, `created_at`, `updated_at`) VALUES
(3, 33, NULL, 'PHM-IP-20250902-00002', '2025-09-02 04:16:16', 55.00, 0.00, 55.00, '2025-09-02 04:16:16', '2025-09-02 04:16:16'),
(4, 33, NULL, 'PHM-IP-20250902-00003', '2025-09-02 05:23:45', 44.00, 0.00, 44.00, '2025-09-02 05:23:45', '2025-09-02 05:23:45'),
(5, 26, NULL, 'PHM-IP-20250902-00004', '2025-09-02 09:53:25', 132.00, 0.00, 132.00, '2025-09-02 09:53:25', '2025-09-02 09:53:25'),
(6, 26, NULL, 'PHM-IP-20250902-00005', '2025-09-02 10:05:48', 55.00, 0.00, 55.00, '2025-09-02 10:05:48', '2025-09-02 10:05:48'),
(7, 26, NULL, 'PHM-IP-20250902-00006', '2025-09-02 10:07:53', 77.00, 0.00, 77.00, '2025-09-02 10:07:53', '2025-09-02 10:07:53'),
(8, 29, NULL, 'PHM-IP-20250903-00007', '2025-09-03 07:13:43', 55.00, 0.00, 55.00, '2025-09-03 07:13:43', '2025-09-03 07:13:43'),
(9, 33, NULL, 'PHM-IP-20250903-00008', '2025-09-03 08:10:31', 55.00, 55.00, 0.00, '2025-09-03 08:10:31', '2025-09-03 08:10:31'),
(10, 33, NULL, 'PHM-IP-20250903-00009', '2025-09-03 08:11:05', 55.00, 0.00, 55.00, '2025-09-03 08:11:05', '2025-09-03 08:11:05'),
(11, 33, NULL, 'PHM-IP-20250906-00010', '2025-09-06 11:04:14', 66.00, 0.00, 66.00, '2025-09-06 11:04:14', '2025-09-06 11:04:14'),
(12, 17, NULL, 'PHM-IP-20250906-00011', '2025-09-06 11:18:17', 264.00, 0.00, 264.00, '2025-09-06 11:18:17', '2025-09-06 11:18:17'),
(13, 33, NULL, 'PHM-IP-20250906-00012', '2025-09-06 11:35:37', 132.00, 0.00, 132.00, '2025-09-06 11:35:37', '2025-09-06 11:35:37'),
(14, 33, NULL, 'PHM-IP-20250908-00013', '2025-09-08 12:02:02', 286.00, 0.00, 286.00, '2025-09-08 12:02:02', '2025-09-09 05:52:18'),
(15, 33, NULL, 'PHM-IP-20250911-00014', '2025-09-11 05:13:57', 66.00, 66.00, 0.00, '2025-09-11 05:13:57', '2025-09-11 05:13:57'),
(16, 33, NULL, 'PHM-IP-20250911-00015', '2025-09-11 10:15:17', 217.00, 0.00, 217.00, '2025-09-11 10:15:17', '2025-09-11 10:15:17'),
(17, 17, NULL, 'PHM-IP-20250911-00016', '2025-09-11 12:27:01', 228.00, 0.00, 228.00, '2025-09-11 12:27:01', '2025-09-11 12:27:01'),
(18, 33, NULL, 'PHM-IP-20250912-00017', '2025-09-12 04:13:02', 217.00, 0.00, 217.00, '2025-09-12 04:13:02', '2025-09-12 04:13:02'),
(19, 33, NULL, 'PHM-IP-20250912-00018', '2025-09-12 05:07:11', 98.00, 0.00, 98.00, '2025-09-12 05:07:11', '2025-09-12 05:07:11'),
(20, 17, NULL, 'PHM-IP-20250912-00019', '2025-09-12 05:36:41', 65.00, 0.00, 62.00, '2025-09-12 05:36:41', '2025-09-12 05:36:41'),
(21, 17, NULL, 'PHM-IP-20250912-00020', '2025-09-12 06:05:43', 65.00, 0.00, 65.00, '2025-09-12 06:05:43', '2025-09-12 06:05:43'),
(22, 33, NULL, 'PHM-IP-20250912-00021', '2025-09-12 06:06:47', 65.00, 65.00, 0.00, '2025-09-12 06:06:47', '2025-09-12 06:06:47'),
(23, 33, NULL, 'PHM-IP-20250912-00022', '2025-09-12 11:03:49', 30.00, 0.00, 30.00, '2025-09-12 11:03:49', '2025-09-12 12:06:24'),
(24, 33, 1, 'PHM-IP-20250913-00023', '2025-09-13 10:19:03', 0.00, 0.00, 0.00, '2025-09-13 10:19:03', '2025-09-15 12:14:46'),
(25, 33, 17, 'PHM-IP-20250915-00024', '2025-09-15 06:08:42', 130.00, 0.00, 130.00, '2025-09-15 06:08:42', '2025-09-15 06:09:29'),
(26, 29, 17, 'PHM-IP-20250918-00025', '2025-09-18 07:07:25', 124.00, 0.00, 124.00, '2025-09-18 07:07:25', '2025-09-18 07:07:25'),
(27, 17, 16, 'PHM-IP-20250918-00026', '2025-09-18 07:13:39', 720.00, 720.00, 0.00, '2025-09-18 07:13:39', '2025-09-18 07:13:39'),
(28, 33, NULL, 'PHM-IP-20250918-00027', '2025-09-18 12:27:56', 810.00, 810.00, 0.00, '2025-09-18 12:27:56', '2025-09-18 12:27:56'),
(29, 33, NULL, 'PHM-IP-20250918-00028', '2025-09-18 12:28:49', 21.00, 0.00, 21.00, '2025-09-18 12:28:49', '2025-09-18 12:28:49'),
(30, 26, 20, 'PHM-IP-20250919-00029', '2025-09-19 04:39:08', 800.00, 0.00, 800.00, '2025-09-19 04:39:08', '2025-09-19 04:39:08'),
(31, 33, 17, 'PHM-IP-20250919-00030', '2025-09-19 04:51:39', 352.00, 352.00, 0.00, '2025-09-19 04:51:39', '2025-09-19 04:51:39');

-- --------------------------------------------------------

--
-- Table structure for table `pharmacy_billing_payments`
--

CREATE TABLE `pharmacy_billing_payments` (
  `id` int(11) NOT NULL,
  `bill_id` varchar(255) NOT NULL,
  `payment_date` datetime NOT NULL DEFAULT current_timestamp(),
  `payment_amount` decimal(10,2) NOT NULL,
  `payment_method` varchar(50) DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `pharmacy_billing_payments`
--

INSERT INTO `pharmacy_billing_payments` (`id`, `bill_id`, `payment_date`, `payment_amount`, `payment_method`, `created_at`, `updated_at`) VALUES
(1, 'PHM-IP-20250902-00005', '2025-09-02 10:05:48', 0.00, 'Credit', '2025-09-02 10:05:48', '2025-09-02 15:35:48'),
(2, 'PHM-IP-20250902-00006', '2025-09-02 10:07:53', 0.00, 'Card', '2025-09-02 10:07:53', '2025-09-02 15:37:53'),
(3, 'PHM-IP-20250902-00006', '2025-09-02 12:09:10', 7.00, 'Cash', '2025-09-02 17:39:10', '2025-09-02 17:39:10'),
(4, 'PHM-IP-20250902-00005', '2025-09-02 12:09:53', 55.00, 'Cash', '2025-09-02 17:39:53', '2025-09-02 17:39:53'),
(5, 'PHM-IP-20250902-00006', '2025-09-02 12:24:12', 70.00, 'Cash', '2025-09-02 17:54:12', '2025-09-02 17:54:12'),
(6, 'PHM-IP-20250902-00004', '2025-09-02 12:32:51', 132.00, 'Cash', '2025-09-02 18:02:51', '2025-09-02 18:02:51'),
(7, 'PHM-IP-20250903-00007', '2025-09-03 07:13:43', 0.00, 'Cash', '2025-09-03 07:13:43', '2025-09-03 12:43:43'),
(8, 'PHM-IP-20250903-00008', '2025-09-03 08:10:31', 55.00, 'Card', '2025-09-03 08:10:31', '2025-09-03 13:40:31'),
(9, 'PHM-IP-20250903-00009', '2025-09-03 08:11:05', 0.00, 'Credit', '2025-09-03 08:11:05', '2025-09-03 13:41:05'),
(10, 'PHM-IP-20250903-00009', '2025-09-03 08:11:42', 5.00, 'Cash', '2025-09-03 13:41:42', '2025-09-03 13:41:42'),
(11, 'PHM-IP-20250906-00010', '2025-09-06 11:04:14', 0.00, 'Credit', '2025-09-06 11:04:14', '2025-09-06 16:34:14'),
(12, 'PHM-IP-20250906-00011', '2025-09-06 11:18:17', 0.00, 'Credit', '2025-09-06 11:18:17', '2025-09-06 16:48:17'),
(13, 'PHM-IP-20250906-00012', '2025-09-06 11:35:37', 0.00, 'Credit', '2025-09-06 11:35:37', '2025-09-06 17:05:37'),
(14, 'PHM-IP-20250906-00012', '2025-09-08 07:37:24', 32.00, 'Card', '2025-09-08 13:07:24', '2025-09-08 13:07:24'),
(15, 'PHM-IP-20250906-00012', '2025-09-08 09:53:54', 10.00, 'Card', '2025-09-08 15:23:54', '2025-09-08 15:23:54'),
(16, 'PHM-IP-20250908-00013', '2025-09-08 12:02:02', 0.00, 'Credit', '2025-09-08 12:02:02', '2025-09-08 17:32:02'),
(17, 'PHM-IP-20250911-00014', '2025-09-11 05:13:57', 66.00, 'Card', '2025-09-11 05:13:57', '2025-09-11 10:43:57'),
(18, 'PHM-IP-20250911-00015', '2025-09-11 10:15:17', 0.00, 'Credit', '2025-09-11 10:15:17', '2025-09-11 15:45:17'),
(19, 'PHM-IP-20250911-00015', '2025-09-11 11:37:32', 7.00, 'Card', '2025-09-11 17:07:32', '2025-09-11 17:07:32'),
(20, 'PHM-IP-20250911-00016', '2025-09-11 12:27:01', 0.00, 'Credit', '2025-09-11 12:27:01', '2025-09-11 17:57:01'),
(21, 'PHM-IP-20250912-00017', '2025-09-12 04:13:02', 0.00, 'Credit', '2025-09-12 04:13:02', '2025-09-12 09:43:02'),
(22, 'PHM-IP-20250912-00018', '2025-09-12 05:07:11', 0.00, 'Credit', '2025-09-12 05:07:11', '2025-09-12 10:37:11'),
(23, 'PHM-IP-20250912-00019', '2025-09-12 05:36:41', 0.00, 'Credit', '2025-09-12 05:36:41', '2025-09-12 11:06:41'),
(24, 'PHM-IP-20250912-00020', '2025-09-12 06:05:43', 0.00, 'Credit', '2025-09-12 06:05:43', '2025-09-12 11:35:43'),
(25, 'PHM-IP-20250912-00021', '2025-09-12 06:06:47', 65.00, 'Cash', '2025-09-12 06:06:47', '2025-09-12 11:36:47'),
(26, 'PHM-IP-20250912-00020', '2025-09-12 06:08:21', 5.00, 'Cash', '2025-09-12 11:38:21', '2025-09-12 11:38:21'),
(27, 'PHM-IP-20250912-00020', '2025-09-12 06:09:12', 10.00, 'Other', '2025-09-12 11:39:12', '2025-09-12 11:39:12'),
(28, 'PHM-IP-20250912-00019', '2025-09-12 06:12:29', 2.00, 'Cash', '2025-09-12 11:42:29', '2025-09-12 11:42:29'),
(29, 'PHM-IP-20250912-00022', '2025-09-12 11:03:49', 0.00, 'Credit', '2025-09-12 11:03:49', '2025-09-12 16:33:49'),
(30, 'PHM-IP-20250912-00022', '2025-09-12 11:05:02', 25.00, 'Cash', '2025-09-12 16:35:02', '2025-09-12 16:35:02'),
(31, 'PHM-IP-20250912-00022', '2025-09-12 11:05:25', 10.00, 'UPI', '2025-09-12 16:35:25', '2025-09-12 16:35:25'),
(32, 'PHM-IP-20250913-00023', '2025-09-13 10:19:03', 0.00, 'Credit', '2025-09-13 10:19:03', '2025-09-13 15:49:03'),
(33, 'PHM-IP-20250913-00023', '2025-09-15 04:26:56', 25.00, 'Cash', '2025-09-15 09:56:56', '2025-09-15 09:56:56'),
(34, 'PHM-IP-20250913-00023', '2025-09-15 04:35:43', 10.00, 'Cash', '2025-09-15 10:05:43', '2025-09-15 10:05:43'),
(35, 'PHM-IP-20250913-00023', '2025-09-15 05:16:13', 20.00, 'Cash', '2025-09-15 10:46:13', '2025-09-15 10:46:13'),
(36, 'PHM-IP-20250915-00024', '2025-09-15 06:08:42', 0.00, 'Credit', '2025-09-15 06:08:42', '2025-09-15 11:38:42'),
(37, 'PHM-IP-20250915-00024', '2025-09-15 06:10:34', 55.00, 'Cash', '2025-09-15 11:40:34', '2025-09-15 11:40:34'),
(38, 'PHM-IP-20250918-00025', '2025-09-18 07:07:25', 0.00, 'Credit', '2025-09-18 07:07:25', '2025-09-18 12:37:25'),
(39, 'PHM-IP-20250918-00026', '2025-09-18 07:13:39', 720.00, 'Cash', '2025-09-18 07:13:39', '2025-09-18 12:43:39'),
(40, 'PHM-IP-20250918-00027', '2025-09-18 12:27:56', 810.00, 'Card', '2025-09-18 12:27:56', '2025-09-18 17:57:56'),
(41, 'PHM-IP-20250918-00028', '2025-09-18 12:28:49', 0.00, 'Credit', '2025-09-18 12:28:49', '2025-09-18 17:58:49'),
(42, 'PHM-IP-20250919-00029', '2025-09-19 04:39:08', 0.00, 'Credit', '2025-09-19 04:39:08', '2025-09-19 10:09:08'),
(43, 'PHM-IP-20250919-00029', '2025-09-19 04:49:23', 100.00, 'Cash', '2025-09-19 10:19:23', '2025-09-19 10:19:23'),
(44, 'PHM-IP-20250919-00029', '2025-09-19 04:51:12', 100.00, 'Cash', '2025-09-19 10:21:12', '2025-09-19 10:21:12'),
(45, 'PHM-IP-20250919-00030', '2025-09-19 04:51:39', 352.00, 'Cash', '2025-09-19 04:51:39', '2025-09-19 10:21:39');

-- --------------------------------------------------------

--
-- Table structure for table `pharmacy_brands`
--

CREATE TABLE `pharmacy_brands` (
  `id` int(11) NOT NULL,
  `brand_name` varchar(255) NOT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `pharmacy_brands`
--

INSERT INTO `pharmacy_brands` (`id`, `brand_name`, `created_at`, `updated_at`) VALUES
(1, 'Cipla', '2025-09-09 11:13:07', '2025-09-09 12:32:18'),
(3, 'Sun Pharma', '2025-09-10 04:27:17', '2025-09-10 04:27:17'),
(4, 'Dr. Reddys', '2025-09-10 04:27:38', '2025-09-10 04:27:38'),
(5, 'Lupin', '2025-09-10 04:28:01', '2025-09-10 04:28:01'),
(6, 'Zydus', '2025-09-10 04:28:49', '2025-09-10 04:28:49'),
(7, 'Glenmark', '2025-09-10 04:28:56', '2025-09-10 04:28:56'),
(8, 'Abbott', '2025-09-10 04:29:01', '2025-09-10 04:29:01'),
(9, 'Alkem', '2025-09-10 04:30:02', '2025-09-10 04:30:02'),
(10, 'Pfizer', '2025-09-10 04:30:32', '2025-09-10 04:30:32'),
(11, 'Intas', '2025-09-10 04:31:33', '2025-09-10 04:31:33');

-- --------------------------------------------------------

--
-- Table structure for table `pharmacy_categories`
--

CREATE TABLE `pharmacy_categories` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `pharmacy_categories`
--

INSERT INTO `pharmacy_categories` (`id`, `name`, `description`, `created_at`, `updated_at`) VALUES
(1, 'Analgesics', 'Medicines that relieve pain.\r\nExamples: Paracetamol, Ibuprofen, Aspirin.\r\n', '2025-08-01 04:41:53', '2025-08-25 04:50:30'),
(2, 'Antibiotics', 'Medicines used to treat bacterial infections.\r\nExamples: Amoxicillin, Azithromycin, Ciprofloxacin.\r\n\r\n', '2025-08-01 04:42:10', '2025-08-01 04:44:48'),
(3, 'Antihistamines', ' Medicines that treat allergy symptoms. Examples: Cetirizine, Loratadine, Diphenhydramine.', '2025-08-01 04:42:40', '2025-08-01 04:45:01'),
(5, 'Antifungals', 'Medicines used to treat fungal infections.\r\n\r\nExamples: Fluconazole, Clotrimazole.\r\n\r\n', '2025-08-01 04:45:20', '2025-08-01 04:45:20'),
(8, 'Dermatologicals', 'Medicines for skin conditions.\r\n\r\nExamples: Hydrocortisone cream, Neosporin.\r\n\r\n', '2025-08-01 04:46:12', '2025-08-01 04:46:12');

-- --------------------------------------------------------

--
-- Table structure for table `pharmacy_dosage_forms`
--

CREATE TABLE `pharmacy_dosage_forms` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `pharmacy_dosage_forms`
--

INSERT INTO `pharmacy_dosage_forms` (`id`, `name`, `created_at`, `updated_at`) VALUES
(1, 'Tablet', '2025-08-01 05:37:50', '2025-08-01 06:09:05'),
(2, 'Capsule', '2025-08-01 05:37:58', '2025-08-01 05:37:58'),
(3, 'Syrup', '2025-08-01 05:38:03', '2025-08-01 05:38:03'),
(4, 'Suspension', '2025-08-01 05:38:08', '2025-08-01 05:38:08'),
(5, 'Cream', '2025-08-01 05:38:15', '2025-08-01 05:38:15'),
(6, 'Ointment', '2025-08-01 05:38:22', '2025-08-01 05:38:22'),
(7, 'Injection', '2025-08-01 05:38:28', '2025-08-01 05:38:28'),
(10, 'Suppository', '2025-08-01 05:38:53', '2025-08-25 05:06:42');

-- --------------------------------------------------------

--
-- Table structure for table `pharmacy_generics`
--

CREATE TABLE `pharmacy_generics` (
  `id` int(11) NOT NULL,
  `generic_name` varchar(255) NOT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `pharmacy_generics`
--

INSERT INTO `pharmacy_generics` (`id`, `generic_name`, `created_at`, `updated_at`) VALUES
(2, 'Paracetamol', '2025-09-09 12:33:19', '2025-09-09 12:33:19'),
(3, 'Ibuprofen', '2025-09-10 04:33:13', '2025-09-10 04:33:13'),
(4, 'Amoxicillin', '2025-09-10 04:33:18', '2025-09-10 04:33:18'),
(5, 'Azithromycin', '2025-09-10 04:33:26', '2025-09-10 04:33:26'),
(6, 'Metformin', '2025-09-10 04:34:20', '2025-09-10 04:34:20'),
(7, 'Amlodipine', '2025-09-10 04:35:25', '2025-09-10 04:35:25'),
(8, 'Atorvastatin', '2025-09-10 04:35:36', '2025-09-10 04:35:36'),
(9, 'Omeprazole', '2025-09-10 04:36:39', '2025-09-10 04:36:39'),
(10, 'Levothyroxine', '2025-09-10 04:37:18', '2025-09-10 04:37:18'),
(11, 'Cetirizine', '2025-09-10 04:38:07', '2025-09-10 04:38:07');

-- --------------------------------------------------------

--
-- Table structure for table `pharmacy_invoice_sequences`
--

CREATE TABLE `pharmacy_invoice_sequences` (
  `prefix` varchar(10) NOT NULL COMMENT 'Invoice prefix, e.g., PHM-IP, PHM-OP',
  `next_sequence_number` int(11) NOT NULL DEFAULT 1 COMMENT 'The next sequence number for this prefix and date'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `pharmacy_invoice_sequences`
--

INSERT INTO `pharmacy_invoice_sequences` (`prefix`, `next_sequence_number`) VALUES
('PHM-IP', 30),
('PHM-OP', 30);

-- --------------------------------------------------------

--
-- Table structure for table `pharmacy_manufacturers`
--

CREATE TABLE `pharmacy_manufacturers` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `contact_person` varchar(100) DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `pharmacy_manufacturers`
--

INSERT INTO `pharmacy_manufacturers` (`id`, `name`, `contact_person`, `phone`, `email`, `address`, `created_at`, `updated_at`) VALUES
(1, 'Pfizer', 'John Doe', '3214563214', 'info@pfizer.com', '235 E 42nd St, New York, NY 10017, UAE\r\n', '2025-08-01 05:02:16', '2025-08-01 05:18:04'),
(2, 'GlaxoSmithKline (GSK)', 'Jane Smith', '9232223432', 'contact@gsk.com', '980 Great West Rd, Brentford TW8 9GS, UK', '2025-08-01 05:04:40', '2025-08-01 05:04:40'),
(4, 'Johnson & Johnson', 'Sarah Williams', ' 1-800-526-3967', 'support@jnj.com', 'One Johnson & Johnson Plaza, New Brunswick, NJ 08933, USA', '2025-08-01 05:19:26', '2025-08-01 05:19:26');

-- --------------------------------------------------------

--
-- Table structure for table `pharmacy_medicines`
--

CREATE TABLE `pharmacy_medicines` (
  `id` int(11) NOT NULL,
  `generic_id` int(11) NOT NULL,
  `brand_id` int(11) NOT NULL,
  `dosage_form_id` int(11) DEFAULT NULL,
  `strength` varchar(100) NOT NULL,
  `unit_of_measure_id` int(11) DEFAULT NULL,
  `manufacturer_id` int(11) NOT NULL,
  `category_id` int(11) NOT NULL,
  `reorder_level` int(11) NOT NULL DEFAULT 0,
  `created_by_user_id` int(11) NOT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_by_user_id` int(11) DEFAULT NULL,
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `gst_rate` decimal(5,2) NOT NULL DEFAULT 0.00,
  `hsn_code` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `pharmacy_medicines`
--

INSERT INTO `pharmacy_medicines` (`id`, `generic_id`, `brand_id`, `dosage_form_id`, `strength`, `unit_of_measure_id`, `manufacturer_id`, `category_id`, `reorder_level`, `created_by_user_id`, `created_at`, `updated_by_user_id`, `updated_at`, `gst_rate`, `hsn_code`) VALUES
(12, 2, 3, 1, '500', 1, 2, 2, 5, 1, '2025-08-01 07:54:16', 1, '2025-09-10 17:56:17', 5.00, '3004'),
(13, 2, 3, 1, '200', 1, 1, 2, 11, 1, '2025-08-01 07:56:09', NULL, '2025-09-10 17:56:21', 12.00, ''),
(15, 9, 3, 2, '5', 1, 2, 2, 3, 1, '2025-08-25 12:21:56', 1, '2025-09-10 17:56:25', 18.00, ''),
(16, 9, 3, 1, '5', 1, 1, 1, 1, 1, '2025-08-26 06:52:43', NULL, '2025-09-10 17:56:29', 28.00, '3004'),
(17, 6, 3, 1, '5', 2, 1, 1, 22, 1, '2025-08-26 06:55:48', NULL, '2025-09-10 17:56:32', 5.00, '3000'),
(18, 6, 3, 1, '2', 1, 4, 1, 10, 1, '2025-09-10 05:46:17', NULL, '2025-09-10 17:56:35', 5.00, '3004'),
(19, 2, 3, 2, '500', 1, 1, 1, 10, 1, '2025-09-10 05:54:17', 1, '2025-09-10 07:11:36', 18.00, '3004'),
(21, 9, 1, 5, '10', 2, 4, 8, 10, 1, '2025-09-10 10:05:16', 1, '2025-09-12 04:23:50', 12.00, '3004'),
(22, 6, 3, 3, '50', 3, 1, 2, 10, 1, '2025-09-10 11:50:38', NULL, '2025-09-10 11:50:38', 10.00, '3004');

-- --------------------------------------------------------

--
-- Table structure for table `pharmacy_purchases`
--

CREATE TABLE `pharmacy_purchases` (
  `id` int(11) NOT NULL,
  `supplier_id` int(11) NOT NULL,
  `purchase_date` datetime NOT NULL DEFAULT current_timestamp(),
  `invoice_number` varchar(100) DEFAULT NULL,
  `total_amount` decimal(10,2) NOT NULL DEFAULT 0.00,
  `status` enum('pending','received','partially_received','cancelled') NOT NULL DEFAULT 'pending',
  `ordered_by_user_id` int(11) NOT NULL,
  `received_by_user_id` int(11) DEFAULT NULL,
  `received_at` datetime DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `pharmacy_purchase_items`
--

CREATE TABLE `pharmacy_purchase_items` (
  `id` int(11) NOT NULL,
  `purchase_id` int(11) NOT NULL,
  `medicine_id` int(11) NOT NULL,
  `batch_id` int(11) DEFAULT NULL,
  `ordered_quantity` int(11) NOT NULL,
  `received_quantity` int(11) NOT NULL DEFAULT 0,
  `unit_purchase_price` decimal(10,2) NOT NULL,
  `sub_total` decimal(10,2) NOT NULL DEFAULT 0.00,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `pharmacy_returns`
--

CREATE TABLE `pharmacy_returns` (
  `id` int(11) NOT NULL,
  `sale_id` int(11) DEFAULT NULL,
  `billing_id` int(11) DEFAULT NULL,
  `sale_item_id` int(11) DEFAULT NULL,
  `medicine_id` int(11) NOT NULL,
  `batch_id` int(11) NOT NULL,
  `quantity_returned` int(11) NOT NULL,
  `return_date` datetime NOT NULL DEFAULT current_timestamp(),
  `return_reason` text NOT NULL,
  `requested_by_user_id` int(11) NOT NULL,
  `approval_status` enum('pending','approved','rejected') NOT NULL DEFAULT 'pending',
  `approved_by_user_id` int(11) DEFAULT NULL,
  `approval_date` datetime DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `medicine_condition` enum('Good','Damaged','Expired','Other') NOT NULL DEFAULT 'Good',
  `follow_up_status` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `pharmacy_returns`
--

INSERT INTO `pharmacy_returns` (`id`, `sale_id`, `billing_id`, `sale_item_id`, `medicine_id`, `batch_id`, `quantity_returned`, `return_date`, `return_reason`, `requested_by_user_id`, `approval_status`, `approved_by_user_id`, `approval_date`, `notes`, `created_at`, `updated_at`, `medicine_condition`, `follow_up_status`) VALUES
(1, 32, NULL, 48, 16, 18, 1, '2025-09-06 05:55:18', 'rrr sdgfdbeb fsbbtb dsfbs', 1, 'approved', 1, '2025-09-06 07:16:19', 'g\nApproval/Rejection Notes: ', '2025-09-06 05:55:18', '2025-09-06 07:16:19', 'Good', NULL),
(2, 32, NULL, 48, 16, 18, 1, '2025-09-06 06:01:01', 'eeeeee', 1, 'rejected', 1, '2025-09-06 07:16:11', 'rr\nApproval/Rejection Notes: ', '2025-09-06 06:01:01', '2025-09-06 07:16:11', 'Good', NULL),
(3, 32, NULL, 48, 16, 18, 1, '2025-09-06 06:34:36', 'rrrrr', 1, 'approved', 1, '2025-09-06 07:14:59', 'r\nApproval/Rejection Notes: ', '2025-09-06 06:34:36', '2025-09-06 07:14:59', 'Good', NULL),
(4, 32, NULL, 49, 13, 9, 1, '2025-09-06 07:32:13', 'ssssss', 1, 'rejected', 1, '2025-09-06 09:51:40', 's\nApproval/Rejection Notes: ', '2025-09-06 07:32:13', '2025-09-06 09:51:40', 'Good', NULL),
(5, 10, NULL, 47, 16, 18, 1, '2025-09-06 09:51:21', 'fdffdvfd', 1, 'approved', 1, '2025-09-06 09:51:57', '\nApproval/Rejection Notes: ', '2025-09-06 09:51:21', '2025-09-06 09:51:57', 'Good', NULL),
(6, 4, NULL, 37, 13, 9, 1, '2025-09-06 09:55:14', 'vevwev', 1, 'approved', 1, '2025-09-06 10:16:54', 'wev\nApproval/Rejection Notes: ', '2025-09-06 09:55:14', '2025-09-06 10:16:54', 'Good', NULL),
(7, 8, NULL, 44, 16, 18, 1, '2025-09-06 09:57:10', 'ascdsvv', 1, 'approved', 1, '2025-09-06 09:58:35', 'sdv\nApproval/Rejection Notes: ', '2025-09-06 09:57:10', '2025-09-06 09:58:35', 'Good', NULL),
(8, 3, NULL, 35, 16, 18, 1, '2025-09-06 10:55:00', 'fewvsv', 1, 'approved', 1, '2025-09-06 10:55:15', 'Approval/Rejection Notes:', '2025-09-06 10:55:00', '2025-09-06 10:55:15', 'Good', NULL),
(9, 11, NULL, 50, 12, 14, 1, '2025-09-06 11:05:40', 'brebv er', 1, 'approved', 1, '2025-09-06 11:08:03', 'Approval/Rejection Notes:', '2025-09-06 11:05:40', '2025-09-06 11:08:03', 'Good', NULL),
(10, 12, NULL, 51, 12, 14, 1, '2025-09-06 11:18:45', 'fffdsfsd', 1, 'approved', 1, '2025-09-06 11:18:54', 'Approval/Rejection Notes:', '2025-09-06 11:18:45', '2025-09-06 11:18:54', 'Good', NULL),
(11, 13, NULL, 52, 12, 14, 1, '2025-09-08 07:46:03', 'sddsf', 1, 'approved', 1, '2025-09-08 09:59:32', 'ff\nApproval/Rejection Notes:', '2025-09-08 07:46:03', '2025-09-08 09:59:32', 'Good', NULL),
(12, 13, NULL, 52, 12, 14, 1, '2025-09-08 10:23:36', 'dxcds', 1, 'approved', 1, '2025-09-08 10:24:08', 'Approval/Rejection Notes:', '2025-09-08 10:23:36', '2025-09-08 10:24:08', 'Good', NULL),
(13, 4, NULL, 37, 13, 9, 1, '2025-09-08 10:35:15', 'fdz dfb', 1, 'approved', 1, '2025-09-08 10:36:19', 'b\nApproval/Rejection Notes:', '2025-09-08 10:35:15', '2025-09-08 10:36:19', 'Good', NULL),
(14, 12, NULL, 51, 12, 14, 1, '2025-09-08 10:59:26', 'dbz fd', 1, 'approved', 1, '2025-09-08 10:59:43', 'Approval/Rejection Notes:', '2025-09-08 10:59:26', '2025-09-08 10:59:43', 'Good', NULL),
(15, 12, NULL, 51, 12, 14, 1, '2025-09-08 11:33:03', 'ar  fdbfbz', 1, 'approved', 1, '2025-09-08 11:33:17', 'Approval/Rejection Notes: fdbfdb', '2025-09-08 11:33:03', '2025-09-08 11:33:17', 'Good', NULL),
(16, 12, NULL, 51, 12, 14, 1, '2025-09-08 11:36:31', 'dfbz fd', 1, 'approved', 1, '2025-09-08 11:39:03', 'Approval/Rejection Notes:', '2025-09-08 11:36:31', '2025-09-08 11:39:03', 'Good', NULL),
(18, NULL, 14, 53, 12, 11, 2, '2025-09-08 12:18:11', 'avd fvd', 1, 'approved', 1, '2025-09-08 12:21:13', 'Approval/Rejection Notes:', '2025-09-08 12:18:11', '2025-09-08 12:21:13', 'Good', NULL),
(19, 31, NULL, 45, 16, 18, 1, '2025-09-08 12:25:41', 'fdbzdf', 1, 'approved', 1, '2025-09-09 04:09:06', 'Approval/Rejection Notes:', '2025-09-08 12:25:41', '2025-09-09 04:09:06', 'Good', NULL),
(20, NULL, 14, 53, 12, 11, 2, '2025-09-09 04:09:31', 'lkmk lkl  km', 1, 'approved', 1, '2025-09-09 04:09:43', 'Approval/Rejection Notes:', '2025-09-09 04:09:31', '2025-09-09 04:09:43', 'Good', NULL),
(21, NULL, 14, 53, 12, 11, 1, '2025-09-09 04:11:39', 'f dfbf fdb', 1, 'approved', 1, '2025-09-09 04:12:07', 'Approval/Rejection Notes:', '2025-09-09 04:11:39', '2025-09-09 04:12:07', 'Good', NULL),
(22, 30, NULL, 43, 13, 9, 1, '2025-09-09 04:14:03', 'erhgaeb sb ', 1, 'rejected', 1, '2025-09-09 04:14:13', 'Approval/Rejection Notes:', '2025-09-09 04:14:03', '2025-09-09 04:14:13', 'Good', NULL),
(23, 30, NULL, 43, 13, 9, 1, '2025-09-09 04:14:41', 'sgr fdd', 1, 'approved', 1, '2025-09-09 04:14:46', 'Approval/Rejection Notes:', '2025-09-09 04:14:41', '2025-09-09 04:14:46', 'Good', NULL),
(24, NULL, 14, 53, 12, 11, 1, '2025-09-09 04:16:18', 'dsv dfb', 1, 'rejected', 1, '2025-09-09 04:16:37', 'Approval/Rejection Notes:', '2025-09-09 04:16:18', '2025-09-09 04:16:37', 'Good', NULL),
(25, 32, NULL, 49, 13, 9, 1, '2025-09-09 04:51:12', 'fdv vde', 1, 'rejected', 1, '2025-09-09 04:57:06', 'vevfre\nApproval/Rejection Notes:', '2025-09-09 04:51:12', '2025-09-09 04:57:06', 'Good', NULL),
(26, NULL, 14, 53, 12, 11, 1, '2025-09-09 05:48:33', 'sgvgr dfb', 1, 'approved', 1, '2025-09-09 05:52:18', '\nApproval/Rejection Notes: done', '2025-09-09 05:48:33', '2025-09-09 05:52:18', 'Good', NULL),
(27, NULL, 14, 53, 12, 11, 1, '2025-09-09 05:48:57', 'wzb dfs', 1, 'approved', 1, '2025-09-09 05:49:09', 'checking\nApproval/Rejection Notes: ok done', '2025-09-09 05:48:57', '2025-09-09 05:49:09', 'Good', NULL),
(28, 39, NULL, 81, 21, 23, 1, '2025-09-12 11:25:10', 're reg ', 1, 'approved', 1, '2025-09-12 11:37:53', '\nApproval/Rejection Notes: ok tested', '2025-09-12 11:25:10', '2025-09-12 11:37:53', 'Good', NULL),
(29, NULL, 23, 83, 21, 24, 1, '2025-09-12 11:25:49', 'fdbz bszre', 1, 'approved', 1, '2025-09-12 11:38:59', 'ragvgreg\nApproval/Rejection Notes: ok done', '2025-09-12 11:25:49', '2025-09-12 11:38:59', 'Good', NULL),
(30, 32, NULL, 49, 13, 9, 1, '2025-09-12 11:39:59', 'dsvn lf vd', 1, 'rejected', 1, '2025-09-12 11:40:08', 'vvd\nApproval/Rejection Notes: fewew', '2025-09-12 11:39:59', '2025-09-12 11:40:08', 'Good', NULL),
(31, NULL, 23, 83, 21, 24, 1, '2025-09-12 12:06:13', ' fbaer g st', 1, 'approved', 1, '2025-09-12 12:06:24', 'etb ae\nApproval/Rejection Notes: ewvre brt', '2025-09-12 12:06:13', '2025-09-12 12:06:24', 'Good', NULL),
(32, NULL, 24, 85, 21, 23, 1, '2025-09-15 05:16:53', 'fdv df r e', 1, 'approved', 1, '2025-09-15 05:17:08', 're\nApproval/Rejection Notes: rev re ve', '2025-09-15 05:16:53', '2025-09-15 05:17:08', 'Good', NULL),
(33, NULL, 24, 85, 21, 23, 1, '2025-09-15 05:19:19', 'jjn jnkj', 1, 'approved', 1, '2025-09-15 05:20:50', 'kk\nApproval/Rejection Notes: ewcwe', '2025-09-15 05:19:19', '2025-09-15 05:20:50', 'Good', NULL),
(34, NULL, 25, 87, 21, 23, 1, '2025-09-15 06:09:17', 'sdbb sb', 1, 'approved', 1, '2025-09-15 06:09:29', 'efb d\nApproval/Rejection Notes: rv vswdv', '2025-09-15 06:09:17', '2025-09-15 06:09:29', 'Good', NULL),
(35, 40, NULL, 89, 21, 24, 2, '2025-09-15 10:46:21', 'dsvds wvw', 1, 'approved', 1, '2025-09-15 10:47:13', 'rrwe\nApproval/Rejection Notes: fv rgtr', '2025-09-15 10:46:21', '2025-09-15 10:47:13', 'Good', NULL),
(36, 41, NULL, 90, 12, 13, 1, '2025-09-15 11:36:00', 'rae ree', 1, 'approved', 1, '2025-09-15 11:36:29', 'fd sber\nApproval/Rejection Notes: reve fre', '2025-09-15 11:36:00', '2025-09-15 11:36:29', 'Good', NULL),
(37, 41, NULL, 91, 21, 24, 2, '2025-09-15 11:36:16', 'reab trb sr', 1, 'approved', 1, '2025-09-15 11:36:46', 'ergvet\nApproval/Rejection Notes: ervgr et4et', '2025-09-15 11:36:16', '2025-09-15 11:36:46', 'Good', NULL),
(38, 41, NULL, 91, 21, 24, 2, '2025-09-15 11:36:17', 'reab trb sr', 1, 'rejected', 1, '2025-09-15 11:37:56', 'ergvet\nApproval/Rejection Notes: fdh gh', '2025-09-15 11:36:17', '2025-09-15 11:37:56', 'Good', NULL),
(39, 42, NULL, 92, 12, 13, 1, '2025-09-15 11:39:26', 'fdx gf', 1, 'approved', 1, '2025-09-15 11:39:35', 'sbst\nApproval/Rejection Notes: dnyt rths', '2025-09-15 11:39:26', '2025-09-15 11:39:35', 'Good', NULL),
(40, NULL, 24, 84, 12, 13, 1, '2025-09-15 12:14:40', 'fsbvs fdd', 1, 'approved', 1, '2025-09-15 12:14:46', 'r\nApproval/Rejection Notes: dvwd fd', '2025-09-15 12:14:40', '2025-09-15 12:14:46', 'Good', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `pharmacy_sales`
--

CREATE TABLE `pharmacy_sales` (
  `id` int(11) NOT NULL,
  `invoice_number` varchar(100) NOT NULL,
  `sale_date` datetime NOT NULL DEFAULT current_timestamp(),
  `sales_person_id` int(11) NOT NULL,
  `prescription_type` enum('in_hospital','outside_sale') NOT NULL DEFAULT 'outside_sale',
  `patient_id` int(11) DEFAULT NULL,
  `doctor_id` int(11) DEFAULT NULL,
  `outside_patient_name` varchar(255) DEFAULT NULL,
  `outside_patient_phone` varchar(20) DEFAULT NULL,
  `outside_patient_address` text DEFAULT NULL,
  `total_amount` decimal(10,2) NOT NULL DEFAULT 0.00,
  `discount_amount` decimal(10,2) NOT NULL DEFAULT 0.00,
  `net_amount` decimal(10,2) NOT NULL DEFAULT 0.00,
  `payment_method` varchar(50) DEFAULT NULL,
  `status` enum('completed','returned','partial_return') NOT NULL DEFAULT 'completed',
  `notes` text DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `pharmacy_sales`
--

INSERT INTO `pharmacy_sales` (`id`, `invoice_number`, `sale_date`, `sales_person_id`, `prescription_type`, `patient_id`, `doctor_id`, `outside_patient_name`, `outside_patient_phone`, `outside_patient_address`, `total_amount`, `discount_amount`, `net_amount`, `payment_method`, `status`, `notes`, `created_at`, `updated_at`) VALUES
(1, 'PHM-2025-000001', '2025-08-28 07:31:23', 1, 'outside_sale', NULL, NULL, 'ram', '9879879879', 'ddd', 132.00, 0.00, 132.00, 'Cash', 'completed', 'f', '2025-08-28 07:31:23', '2025-08-28 07:31:23'),
(2, 'PHM-2025-000002', '2025-08-28 07:31:55', 1, 'outside_sale', NULL, NULL, 'ram', '3', 'df', 66.00, 0.00, 66.00, 'Cash', 'completed', '', '2025-08-28 07:31:55', '2025-08-28 07:31:55'),
(3, 'PHM-2025-000003', '2025-08-28 07:48:13', 1, 'outside_sale', NULL, NULL, 'ram', '9879879879', 'dddd', 66.00, 0.00, 66.00, 'Cash', 'completed', 'frbr', '2025-08-28 07:48:13', '2025-09-06 10:55:15'),
(4, 'PHM-2025-000004', '2025-08-28 09:21:25', 1, 'outside_sale', NULL, NULL, 'ram', '9879879879', 'dddd', 66.00, 0.00, 66.00, 'Cash', 'completed', 'frbr', '2025-08-28 09:21:25', '2025-09-08 10:36:19'),
(5, 'PHM-2025-000005', '2025-08-28 09:37:12', 1, 'outside_sale', NULL, NULL, 'ram', '43534', 'ggh', 66.00, 0.00, 66.00, 'Cash', 'completed', '', '2025-08-28 09:37:12', '2025-08-28 09:37:12'),
(6, 'PHM-2025-000006', '2025-08-28 09:50:16', 1, 'outside_sale', NULL, NULL, 'ram', '45454', 'gv', 66.00, 0.00, 66.00, 'Cash', 'completed', '', '2025-08-28 09:50:16', '2025-08-28 09:50:16'),
(7, 'PHM-2025-000007', '2025-08-28 09:55:12', 1, 'outside_sale', NULL, NULL, 'ram', '9879879879', 'dgregevre', 110.00, 0.00, 110.00, 'Cash', 'completed', 'v', '2025-08-28 09:55:12', '2025-08-28 09:55:12'),
(8, 'PHM-2025-000008', '2025-08-28 10:06:00', 1, 'outside_sale', NULL, NULL, 'jjj', '98798745', 'b', 110.00, 0.00, 108.00, 'Cash', 'completed', '', '2025-08-28 10:06:00', '2025-09-06 09:58:35'),
(9, 'PHM-2025-000009', '2025-08-28 11:34:35', 1, 'outside_sale', NULL, NULL, 'tata', '9879879879', 't', 110.00, 0.00, 109.00, 'Cash', 'completed', '', '2025-08-28 11:34:35', '2025-08-28 11:34:35'),
(10, 'PHM-OP-20250828-00001', '2025-08-28 11:46:46', 1, 'outside_sale', NULL, NULL, 'mani', '9879879879', 'reb', 110.00, 0.00, 108.00, 'Cash', 'completed', '', '2025-08-28 11:46:46', '2025-09-06 09:51:57'),
(11, 'PHM-OP-20250828-00002', '2025-08-28 11:54:39', 1, 'outside_sale', NULL, NULL, 'king', '4646466464', 'rfbejgne', 30.00, 0.00, 30.00, 'Cash', 'completed', '', '2025-08-28 11:54:39', '2025-09-06 11:08:03'),
(12, 'PHM-OP-20250828-00003', '2025-08-28 12:06:50', 1, 'outside_sale', NULL, NULL, 'raju', '9879879879', 'ryr', 66.00, 0.00, 66.00, 'Card', 'completed', '', '2025-08-28 12:06:50', '2025-09-08 11:39:03'),
(13, 'PHM-OP-20250828-00004', '2025-08-28 12:15:29', 1, 'outside_sale', NULL, NULL, 'www', '9879879879', 'g', 66.00, 0.00, 66.00, 'Cash', 'completed', '', '2025-08-28 12:15:29', '2025-09-08 10:24:08'),
(14, 'PHM-OP-20250828-00005', '2025-08-28 12:28:30', 1, 'outside_sale', NULL, NULL, 'nnnn', '', '', 66.00, 0.00, 66.00, 'Cash', 'completed', '', '2025-08-28 12:28:30', '2025-08-28 12:28:30'),
(15, 'PHM-OP-20250829-00001', '2025-08-29 05:12:55', 1, 'outside_sale', NULL, NULL, 'wqwqw', '', '', 66.00, 0.00, 66.00, 'Card', 'completed', '', '2025-08-29 05:12:55', '2025-08-29 05:12:55'),
(16, 'PHM-OP-20250829-00002', '2025-08-29 11:10:12', 1, 'outside_sale', NULL, NULL, 'revgr', '4333535', 'grgg', 44.00, 0.00, 44.00, 'Cash', 'completed', 'f', '2025-08-29 11:10:12', '2025-08-29 11:10:12'),
(17, 'PHM-OP-20250829-00003', '2025-08-29 11:47:53', 1, 'outside_sale', NULL, NULL, 'ram', '9879879879', 'vg', 77.00, 0.00, 77.00, 'Cash', 'completed', '', '2025-08-29 11:47:53', '2025-08-29 11:47:53'),
(18, 'PHM-OP-20250829-00004', '2025-08-29 12:22:44', 1, 'outside_sale', NULL, NULL, 'kaja', '9879879879', 'dr', 66.00, 0.00, 66.00, 'Credit', 'completed', '', '2025-08-29 12:22:44', '2025-08-29 12:22:44'),
(19, 'PHM-OP-20250830-00001', '2025-08-30 05:40:55', 1, 'outside_sale', NULL, NULL, 'asas', '9879879879', 'rv', 66.00, 0.00, 66.00, 'Cash', 'completed', '', '2025-08-30 05:40:55', '2025-08-30 05:40:55'),
(20, 'PHM-OP-20250830-00006', '2025-08-30 06:01:30', 1, 'outside_sale', NULL, NULL, 'nana', '9879879879', 'te', 55.00, 0.00, 55.00, 'Cash', 'completed', '', '2025-08-30 06:01:30', '2025-08-30 06:01:30'),
(21, 'PHM-OP-20250830-00007', '2025-08-30 06:21:57', 1, 'outside_sale', NULL, NULL, 'raj', '43534', '', 55.00, 0.00, 55.00, 'Card', 'completed', '', '2025-08-30 06:21:57', '2025-08-30 06:21:57'),
(26, 'PHM-OP-20250830-00008', '2025-08-30 06:56:18', 1, 'outside_sale', NULL, NULL, 'wewew', '22122', '', 55.00, 0.00, 55.00, 'Cash', 'completed', '', '2025-08-30 06:56:18', '2025-08-30 06:56:18'),
(27, 'PHM-OP-20250830-00009', '2025-08-30 09:53:24', 1, 'outside_sale', NULL, NULL, 'ffff', '9879879879', 'e', 55.00, 0.00, 55.00, 'Cash', 'completed', '', '2025-08-30 09:53:24', '2025-08-30 09:53:24'),
(28, 'PHM-OP-20250830-00010', '2025-08-30 11:43:01', 1, 'outside_sale', NULL, NULL, 'b dfb', '', '', 55.00, 0.00, 55.00, 'Cash', 'completed', '', '2025-08-30 11:43:01', '2025-08-30 11:43:01'),
(29, 'PHM-OP-20250902-00011', '2025-09-02 04:25:27', 1, 'outside_sale', NULL, NULL, 'ram', '9879879879', 'g', 55.00, 0.00, 55.00, 'Card', 'completed', '', '2025-09-02 04:25:27', '2025-09-02 04:25:27'),
(30, 'PHM-OP-20250902-00012', '2025-09-02 10:10:21', 1, 'outside_sale', NULL, NULL, 'ddwe', '9879879879', 'f', 0.00, 0.00, 0.00, 'Cash', 'completed', '', '2025-09-02 10:10:21', '2025-09-09 04:14:46'),
(31, 'PHM-OP-20250903-00013', '2025-09-03 08:08:57', 1, 'outside_sale', NULL, NULL, 'ramy', '4646466464', 'gagt4', 0.00, 0.00, 0.00, 'Cash', 'completed', 'g', '2025-09-03 08:08:57', '2025-09-09 04:09:06'),
(32, 'PHM-OP-20250903-00014', '2025-09-03 09:33:45', 1, 'outside_sale', NULL, NULL, 'rams', '9879879879', 'df', 22.00, 0.00, 20.00, 'Card', 'completed', '', '2025-09-03 09:33:45', '2025-09-06 07:16:19'),
(33, 'PHM-OP-20250911-00015', '2025-09-11 04:07:00', 1, 'outside_sale', NULL, NULL, 'nayana', '989889898', 'efw', 106.00, 0.00, 105.00, 'Cash', 'completed', 'note period', '2025-09-11 04:07:00', '2025-09-11 04:07:00'),
(34, 'PHM-OP-20250911-00016', '2025-09-11 06:08:46', 1, 'outside_sale', NULL, NULL, 'nayana', '989889898', 'efw', 132.00, 0.00, 131.00, 'Card', 'completed', 'note period', '2025-09-11 06:08:46', '2025-09-11 06:08:46'),
(35, 'PHM-OP-20250911-00017', '2025-09-11 10:04:51', 1, 'outside_sale', NULL, NULL, 'sathsh', '9879879879', 'fdb', 106.00, 5.00, 98.00, 'Cash', 'completed', 'febserbae', '2025-09-11 10:04:51', '2025-09-11 10:04:51'),
(36, 'PHM-OP-20250912-00018', '2025-09-12 04:37:08', 1, 'outside_sale', NULL, NULL, 'ramji', '9879879879', 'gre', 106.00, 5.00, 98.00, 'Card', 'completed', '', '2025-09-12 04:37:08', '2025-09-12 04:37:08'),
(37, 'PHM-OP-20250912-00019', '2025-09-12 10:05:33', 1, 'outside_sale', NULL, NULL, 'ram', '43443', 'rt', 65.00, 5.00, 65.00, 'Card', 'completed', '', '2025-09-12 10:05:33', '2025-09-12 10:05:33'),
(38, 'PHM-OP-20250912-00020', '2025-09-12 10:17:56', 1, 'outside_sale', NULL, NULL, 'fddfbs', '9879879879', 're', 65.00, 5.00, 65.00, 'UPI', 'completed', '', '2025-09-12 10:17:56', '2025-09-12 10:17:56'),
(39, 'PHM-OP-20250912-00021', '2025-09-12 11:00:28', 1, 'outside_sale', NULL, NULL, 'mango', '22222222', 'fbre', 50.00, 5.00, 47.00, 'Card', 'completed', '', '2025-09-12 11:00:28', '2025-09-12 11:37:53'),
(40, 'PHM-OP-20250915-00022', '2025-09-15 10:40:22', 1, 'outside_sale', NULL, NULL, 'ram', '9879879879', 'dcswde', 30.00, 7.00, 26.00, 'Card', 'completed', 'bsrtbsr', '2025-09-15 10:40:22', '2025-09-15 10:47:13'),
(41, 'PHM-OP-20250915-00023', '2025-09-15 10:48:14', 1, 'outside_sale', NULL, NULL, 'gg wr', '9879879879', 'rgv egr', 0.00, 7.00, -4.00, 'UPI', 'completed', 'rva eb', '2025-09-15 10:48:14', '2025-09-15 11:36:46'),
(42, 'PHM-OP-20250915-00024', '2025-09-15 11:39:05', 1, 'outside_sale', NULL, NULL, 'reer  ', '43453', 'gregf', 200.00, 7.00, 196.00, 'Card', 'completed', '', '2025-09-15 11:39:05', '2025-09-15 11:39:35'),
(43, 'PHM-OP-20250918-00025', '2025-09-18 07:12:42', 16, 'outside_sale', NULL, NULL, 'lala', '7894564567', 'eewre', 105.00, 50.00, 100.00, 'Card', 'completed', '', '2025-09-18 07:12:42', '2025-09-18 07:12:42'),
(44, 'PHM-OP-20250918-00026', '2025-09-18 07:14:58', 16, 'outside_sale', NULL, NULL, 'nayana', '43534', 'veer', 4032.00, 400.00, 3600.00, 'Card', 'completed', '', '2025-09-18 07:14:58', '2025-09-18 07:14:58'),
(45, 'PHM-OP-20250918-00027', '2025-09-18 07:16:11', 17, 'outside_sale', NULL, NULL, 'bbb', '232', 'f', 1008.00, 100.00, 900.00, 'Card', 'completed', '', '2025-09-18 07:16:11', '2025-09-18 07:16:11'),
(46, 'PHM-OP-20250918-00028', '2025-09-18 12:12:51', 18, 'outside_sale', NULL, NULL, 'raov', '43', 're', 112.00, 0.00, 100.00, 'Card', 'completed', '', '2025-09-18 12:12:51', '2025-09-18 12:12:51'),
(47, 'PHM-OP-20250918-00029', '2025-09-18 12:28:23', 20, 'outside_sale', NULL, NULL, 'sathsh', '345', 'h', 157.50, 0.00, 150.00, 'Cash', 'completed', '', '2025-09-18 12:28:23', '2025-09-18 12:28:23'),
(48, 'PHM-OP-20250919-00030', '2025-09-19 08:02:35', 18, 'outside_sale', NULL, NULL, 'wrv', '43', 'fx fbd', 1135.40, 36.00, 1028.00, 'Card', 'completed', '', '2025-09-19 08:02:35', '2025-09-19 08:02:35');

-- --------------------------------------------------------

--
-- Table structure for table `pharmacy_sales_persons`
--

CREATE TABLE `pharmacy_sales_persons` (
  `id` int(11) NOT NULL,
  `salesperson_id` varchar(255) NOT NULL,
  `first_name` varchar(255) NOT NULL,
  `last_name` varchar(255) NOT NULL,
  `phone` varchar(20) NOT NULL,
  `address` text DEFAULT NULL,
  `email` varchar(255) NOT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `status` tinyint(1) NOT NULL DEFAULT 1 COMMENT '0=inactive, 1=active'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `pharmacy_sales_persons`
--

INSERT INTO `pharmacy_sales_persons` (`id`, `salesperson_id`, `first_name`, `last_name`, `phone`, `address`, `email`, `created_at`, `updated_at`, `status`) VALUES
(1, 'PHY-20250917-0001', 'Rama Rao', 'kankipatis', '9052555607', 'kakinada', 'ramarao@gmail.com', '2025-09-17 07:20:49', '2025-09-17 07:21:04', 1),
(3, 'PHY-20250917-0003', 'kanna', 'rao', '9876543210', 'gre', 'ramumarketing@gmail.com', '2025-09-17 07:26:25', '2025-09-17 07:33:12', 1),
(6, 'PHY-20250917-0004', 'nani', 'kankipati', '9876543210', 'sr', 'ramaraor@gmail.com', '2025-09-17 07:35:07', '2025-09-17 07:35:55', 1),
(7, 'PHY-20250918-0005', 'suresh', 'ganta', '7894561230', 'kkdrjy\r\n', 'sureshganta@gmail.com', '2025-09-18 06:09:54', '2025-09-18 12:31:40', 1),
(9, 'PHY-20250918-0006', 'range', 'rao', '1231231230', 'jjk', 'rangarao@gmail.com', '2025-09-18 12:06:29', '2025-09-18 12:09:46', 1),
(10, 'PHY-20250918-0007', 'ramu', 'lankey', '7894561237', 'ewwre', 'ramaraoew@gmail.com', '2025-09-18 12:26:45', '2025-09-18 12:26:45', 1),
(11, 'PHY-20250919-0008', 'Bhanu', 'prakesh', '1234567890', 'enee\r\n', 'bhanu@gmail.com', '2025-09-19 04:53:33', '2025-09-19 04:53:33', 1);

-- --------------------------------------------------------

--
-- Table structure for table `pharmacy_sale_items`
--

CREATE TABLE `pharmacy_sale_items` (
  `id` int(11) NOT NULL,
  `sale_id` int(11) DEFAULT NULL,
  `billing_id` int(11) DEFAULT NULL,
  `medicine_id` int(11) NOT NULL,
  `batch_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL,
  `unit_selling_price` decimal(10,2) NOT NULL,
  `discount_per_item` decimal(10,2) NOT NULL DEFAULT 0.00,
  `sub_total` decimal(10,2) NOT NULL DEFAULT 0.00,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `pharmacy_sale_items`
--

INSERT INTO `pharmacy_sale_items` (`id`, `sale_id`, `billing_id`, `medicine_id`, `batch_id`, `quantity`, `unit_selling_price`, `discount_per_item`, `sub_total`, `created_at`, `updated_at`) VALUES
(1, 1, NULL, 12, 14, 1, 66.00, 0.00, 66.00, '2025-08-28 07:31:23', '2025-08-28 07:31:23'),
(2, 1, NULL, 13, 9, 3, 22.00, 0.00, 66.00, '2025-08-28 07:31:23', '2025-08-28 07:31:23'),
(3, 2, NULL, 12, 14, 1, 66.00, 0.00, 66.00, '2025-08-28 07:31:55', '2025-08-28 07:31:55'),
(4, 3, NULL, 12, 14, 1, 66.00, 0.00, 66.00, '2025-08-28 07:48:13', '2025-08-28 07:48:13'),
(5, 4, NULL, 12, 14, 1, 66.00, 0.00, 66.00, '2025-08-28 09:21:25', '2025-08-28 09:21:25'),
(6, 5, NULL, 12, 14, 1, 66.00, 0.00, 66.00, '2025-08-28 09:37:12', '2025-08-28 09:37:12'),
(7, 6, NULL, 12, 14, 1, 66.00, 0.00, 66.00, '2025-08-28 09:50:16', '2025-08-28 09:50:16'),
(8, 7, NULL, 12, 14, 1, 66.00, 0.00, 66.00, '2025-08-28 09:55:12', '2025-08-28 09:55:12'),
(9, 7, NULL, 13, 9, 2, 22.00, 0.00, 44.00, '2025-08-28 09:55:12', '2025-08-28 09:55:12'),
(10, 8, NULL, 12, 14, 1, 66.00, 0.00, 66.00, '2025-08-28 10:06:00', '2025-08-28 10:06:00'),
(11, 8, NULL, 13, 9, 2, 22.00, 2.00, 42.00, '2025-08-28 10:06:00', '2025-08-28 10:06:00'),
(12, 9, NULL, 12, 14, 1, 66.00, 0.00, 66.00, '2025-08-28 11:34:35', '2025-08-28 11:34:35'),
(13, 9, NULL, 13, 9, 2, 22.00, 1.00, 43.00, '2025-08-28 11:34:35', '2025-08-28 11:34:35'),
(14, 10, NULL, 12, 14, 1, 66.00, 0.00, 66.00, '2025-08-28 11:46:46', '2025-08-28 11:46:46'),
(15, 10, NULL, 13, 9, 2, 22.00, 2.00, 42.00, '2025-08-28 11:46:46', '2025-08-28 11:46:46'),
(16, 11, NULL, 13, 12, 1, 30.00, 0.00, 30.00, '2025-08-28 11:54:39', '2025-08-28 11:54:39'),
(17, 12, NULL, 12, 14, 1, 66.00, 0.00, 66.00, '2025-08-28 12:06:50', '2025-08-28 12:06:50'),
(18, 13, NULL, 12, 14, 1, 66.00, 0.00, 66.00, '2025-08-28 12:15:29', '2025-08-28 12:15:29'),
(19, 14, NULL, 12, 14, 1, 66.00, 0.00, 66.00, '2025-08-28 12:28:30', '2025-08-28 12:28:30'),
(20, 15, NULL, 12, 14, 1, 66.00, 0.00, 66.00, '2025-08-29 05:12:55', '2025-08-29 05:12:55'),
(21, 16, NULL, 12, 11, 2, 22.00, 0.00, 44.00, '2025-08-29 11:10:12', '2025-08-29 11:10:12'),
(22, 17, NULL, 16, 18, 1, 55.00, 0.00, 55.00, '2025-08-29 11:47:53', '2025-08-29 11:47:53'),
(23, 17, NULL, 13, 9, 1, 22.00, 0.00, 22.00, '2025-08-29 11:47:53', '2025-08-29 11:47:53'),
(24, 18, NULL, 12, 14, 1, 66.00, 0.00, 66.00, '2025-08-29 12:22:44', '2025-08-29 12:22:44'),
(25, 19, NULL, 12, 14, 1, 66.00, 0.00, 66.00, '2025-08-30 05:40:56', '2025-08-30 05:40:56'),
(26, 20, NULL, 16, 18, 1, 55.00, 0.00, 55.00, '2025-08-30 06:01:30', '2025-08-30 06:01:30'),
(27, 21, NULL, 16, 18, 1, 55.00, 0.00, 55.00, '2025-08-30 06:21:57', '2025-08-30 06:21:57'),
(32, 26, NULL, 16, 18, 1, 55.00, 0.00, 55.00, '2025-08-30 06:56:18', '2025-08-30 06:56:18'),
(33, 27, NULL, 16, 18, 1, 55.00, 0.00, 55.00, '2025-08-30 09:53:24', '2025-08-30 09:53:24'),
(34, 28, NULL, 16, 18, 1, 55.00, 0.00, 55.00, '2025-08-30 11:43:01', '2025-08-30 11:43:01'),
(35, NULL, 3, 16, 18, 0, 55.00, 0.00, 55.00, '2025-09-02 04:16:16', '2025-09-06 10:55:15'),
(36, 29, NULL, 16, 18, 1, 55.00, 0.00, 55.00, '2025-09-02 04:25:27', '2025-09-02 04:25:27'),
(37, NULL, 4, 13, 9, 0, 22.00, 0.00, 44.00, '2025-09-02 05:23:45', '2025-09-08 10:36:19'),
(38, NULL, 5, 16, 18, 2, 55.00, 0.00, 110.00, '2025-09-02 09:53:25', '2025-09-02 09:53:25'),
(39, NULL, 5, 13, 9, 1, 22.00, 0.00, 22.00, '2025-09-02 09:53:25', '2025-09-02 09:53:25'),
(40, NULL, 6, 16, 18, 1, 55.00, 0.00, 55.00, '2025-09-02 10:05:48', '2025-09-02 10:05:48'),
(41, NULL, 7, 12, 11, 1, 22.00, 0.00, 22.00, '2025-09-02 10:07:53', '2025-09-02 10:07:53'),
(42, NULL, 7, 16, 18, 1, 55.00, 0.00, 55.00, '2025-09-02 10:07:53', '2025-09-02 10:07:53'),
(43, 30, NULL, 13, 9, 0, 22.00, 0.00, 22.00, '2025-09-02 10:10:21', '2025-09-09 04:14:46'),
(44, NULL, 8, 16, 18, 0, 55.00, 0.00, 55.00, '2025-09-03 07:13:43', '2025-09-06 09:58:35'),
(45, 31, NULL, 16, 18, 0, 55.00, 0.00, 55.00, '2025-09-03 08:08:57', '2025-09-09 04:09:06'),
(46, NULL, 9, 16, 18, 1, 55.00, 0.00, 55.00, '2025-09-03 08:10:31', '2025-09-03 08:10:31'),
(47, NULL, 10, 16, 18, 0, 55.00, 0.00, 55.00, '2025-09-03 08:11:05', '2025-09-06 09:51:57'),
(48, 32, NULL, 16, 18, 0, 55.00, 0.00, 55.00, '2025-09-03 09:33:45', '2025-09-06 07:16:19'),
(49, 32, NULL, 13, 9, 1, 22.00, 2.00, 20.00, '2025-09-03 09:33:45', '2025-09-03 09:33:45'),
(50, NULL, 11, 12, 14, 0, 66.00, 0.00, 66.00, '2025-09-06 11:04:14', '2025-09-06 11:08:03'),
(51, NULL, 12, 12, 14, 0, 66.00, 0.00, 264.00, '2025-09-06 11:18:17', '2025-09-08 11:39:03'),
(52, NULL, 13, 12, 14, 0, 66.00, 0.00, 132.00, '2025-09-06 11:35:37', '2025-09-08 10:24:08'),
(53, NULL, 14, 12, 11, 13, 22.00, 0.00, 440.00, '2025-09-08 12:02:02', '2025-09-09 05:52:18'),
(54, 33, NULL, 12, 14, 1, 66.00, 0.00, 66.00, '2025-09-11 04:07:00', '2025-09-11 04:07:00'),
(55, 33, NULL, 21, 25, 2, 20.00, 1.00, 39.00, '2025-09-11 04:07:00', '2025-09-11 04:07:00'),
(56, NULL, 15, 12, 14, 1, 66.00, 0.00, 66.00, '2025-09-11 05:13:57', '2025-09-11 05:13:57'),
(57, 34, NULL, 12, 14, 2, 66.00, 1.00, 131.00, '2025-09-11 06:08:46', '2025-09-11 06:08:46'),
(58, 35, NULL, 12, 14, 1, 66.00, 1.00, 65.00, '2025-09-11 10:04:51', '2025-09-11 10:04:51'),
(59, 35, NULL, 21, 25, 2, 20.00, 2.00, 38.00, '2025-09-11 10:04:51', '2025-09-11 10:04:51'),
(60, NULL, 16, 21, 25, 5, 20.00, 2.00, 98.00, '2025-09-11 10:15:17', '2025-09-11 10:15:17'),
(61, NULL, 16, 12, 14, 2, 66.00, 1.00, 131.00, '2025-09-11 10:15:17', '2025-09-11 10:15:17'),
(62, NULL, 17, 12, 14, 3, 66.00, 1.00, 197.00, '2025-09-11 12:27:01', '2025-09-11 12:27:01'),
(63, NULL, 17, 21, 25, 2, 20.00, 2.00, 38.00, '2025-09-11 12:27:01', '2025-09-11 12:27:01'),
(64, NULL, 18, 12, 14, 2, 66.00, 1.00, 131.00, '2025-09-12 04:13:02', '2025-09-12 04:13:02'),
(65, NULL, 18, 21, 23, 5, 20.00, 2.00, 98.00, '2025-09-12 04:13:02', '2025-09-12 04:13:02'),
(66, 36, NULL, 12, 14, 1, 66.00, 1.00, 65.00, '2025-09-12 04:37:08', '2025-09-12 04:37:08'),
(67, 36, NULL, 21, 25, 2, 20.00, 2.00, 38.00, '2025-09-12 04:37:08', '2025-09-12 04:37:08'),
(68, NULL, 19, 12, 14, 1, 66.00, 1.00, 65.00, '2025-09-12 05:07:11', '2025-09-12 05:07:11'),
(69, NULL, 19, 21, 23, 2, 20.00, 2.00, 38.00, '2025-09-12 05:07:11', '2025-09-12 05:07:11'),
(70, NULL, 20, 12, 13, 1, 30.00, 1.00, 29.00, '2025-09-12 05:36:41', '2025-09-12 05:36:41'),
(71, NULL, 20, 21, 23, 2, 20.00, 2.00, 38.00, '2025-09-12 05:36:41', '2025-09-12 05:36:41'),
(72, NULL, 21, 12, 13, 1, 30.00, 1.00, 29.00, '2025-09-12 06:05:43', '2025-09-12 06:05:43'),
(73, NULL, 21, 21, 23, 2, 20.00, 2.00, 36.00, '2025-09-12 06:05:43', '2025-09-12 06:05:43'),
(74, NULL, 22, 12, 13, 1, 30.00, 1.00, 29.00, '2025-09-12 06:06:47', '2025-09-12 06:06:47'),
(75, NULL, 22, 21, 23, 2, 20.00, 2.00, 36.00, '2025-09-12 06:06:47', '2025-09-12 06:06:47'),
(76, 37, NULL, 12, 13, 1, 30.00, 1.00, 29.00, '2025-09-12 10:05:33', '2025-09-12 10:05:33'),
(77, 37, NULL, 21, 23, 2, 20.00, 2.00, 36.00, '2025-09-12 10:05:33', '2025-09-12 10:05:33'),
(78, 38, NULL, 12, 13, 1, 30.00, 1.00, 29.00, '2025-09-12 10:17:56', '2025-09-12 10:17:56'),
(79, 38, NULL, 21, 23, 2, 20.00, 2.00, 36.00, '2025-09-12 10:17:56', '2025-09-12 10:17:56'),
(80, 39, NULL, 12, 13, 1, 30.00, 1.00, 29.00, '2025-09-12 11:00:28', '2025-09-12 11:00:28'),
(81, 39, NULL, 21, 23, 1, 20.00, 2.00, 36.00, '2025-09-12 11:00:28', '2025-09-12 11:37:53'),
(82, NULL, 23, 12, 13, 1, 30.00, 1.00, 29.00, '2025-09-12 11:03:49', '2025-09-12 11:03:49'),
(83, NULL, 23, 21, 24, 0, 100.00, 2.00, 196.00, '2025-09-12 11:03:49', '2025-09-12 12:06:24'),
(84, NULL, 24, 12, 13, 0, 30.00, 1.00, 29.00, '2025-09-13 10:19:03', '2025-09-15 12:14:46'),
(85, NULL, 24, 21, 23, 0, 100.00, 2.00, 196.00, '2025-09-13 10:19:03', '2025-09-15 05:20:50'),
(86, NULL, 25, 12, 13, 1, 30.00, 1.00, 29.00, '2025-09-15 06:08:42', '2025-09-15 06:08:42'),
(87, NULL, 25, 21, 23, 1, 100.00, 2.00, 196.00, '2025-09-15 06:08:42', '2025-09-15 06:09:29'),
(88, 40, NULL, 12, 13, 1, 30.00, 1.00, 29.00, '2025-09-15 10:40:22', '2025-09-15 10:40:22'),
(89, 40, NULL, 21, 24, 0, 100.00, 3.00, 194.00, '2025-09-15 10:40:22', '2025-09-15 10:47:13'),
(90, 41, NULL, 12, 13, 0, 30.00, 1.00, 29.00, '2025-09-15 10:48:14', '2025-09-15 11:36:29'),
(91, 41, NULL, 21, 24, 0, 100.00, 3.00, 194.00, '2025-09-15 10:48:14', '2025-09-15 11:36:46'),
(92, 42, NULL, 12, 13, 0, 30.00, 1.00, 29.00, '2025-09-15 11:39:05', '2025-09-15 11:39:35'),
(93, 42, NULL, 21, 24, 2, 100.00, 3.00, 194.00, '2025-09-15 11:39:05', '2025-09-15 11:39:05'),
(94, NULL, 26, 12, 13, 1, 30.00, 1.00, 29.00, '2025-09-18 07:07:25', '2025-09-18 07:07:25'),
(95, NULL, 26, 21, 23, 1, 100.00, 5.00, 95.00, '2025-09-18 07:07:25', '2025-09-18 07:07:25'),
(96, 43, NULL, 12, 13, 5, 30.00, 10.00, 100.00, '2025-09-18 07:12:42', '2025-09-18 07:12:42'),
(97, NULL, 27, 21, 24, 8, 100.00, 10.00, 720.00, '2025-09-18 07:13:39', '2025-09-18 07:13:39'),
(98, 44, NULL, 21, 24, 40, 100.00, 10.00, 3600.00, '2025-09-18 07:14:58', '2025-09-18 07:14:58'),
(99, 45, NULL, 21, 24, 10, 100.00, 10.00, 900.00, '2025-09-18 07:16:11', '2025-09-18 07:16:11'),
(100, 46, NULL, 21, 24, 1, 100.00, 0.00, 100.00, '2025-09-18 12:12:51', '2025-09-18 12:12:51'),
(101, NULL, 28, 21, 24, 9, 100.00, 10.00, 810.00, '2025-09-18 12:27:56', '2025-09-18 12:27:56'),
(102, 47, NULL, 12, 13, 5, 30.00, 0.00, 150.00, '2025-09-18 12:28:23', '2025-09-18 12:28:23'),
(103, NULL, 29, 12, 11, 1, 22.00, 1.00, 21.00, '2025-09-18 12:28:49', '2025-09-18 12:28:49'),
(104, NULL, 30, 21, 22, 10, 80.00, 0.00, 800.00, '2025-09-19 04:39:08', '2025-09-19 04:39:08'),
(105, NULL, 31, 12, 11, 16, 22.00, 0.00, 352.00, '2025-09-19 04:51:39', '2025-09-19 04:51:39'),
(106, 48, NULL, 12, 11, 12, 22.00, 3.00, 228.00, '2025-09-19 08:02:35', '2025-09-19 08:02:35'),
(107, 48, NULL, 21, 24, 8, 100.00, 0.00, 800.00, '2025-09-19 08:02:35', '2025-09-19 08:02:35');

-- --------------------------------------------------------

--
-- Table structure for table `pharmacy_stock_adjustments`
--

CREATE TABLE `pharmacy_stock_adjustments` (
  `id` int(11) NOT NULL,
  `medicine_id` int(11) NOT NULL,
  `batch_id` int(11) NOT NULL,
  `adjustment_type` enum('in','out') NOT NULL,
  `quantity` int(11) NOT NULL,
  `reason` text NOT NULL,
  `adjusted_by_user_id` int(11) NOT NULL,
  `adjustment_date` datetime NOT NULL DEFAULT current_timestamp(),
  `notes` text DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `pharmacy_suppliers`
--

CREATE TABLE `pharmacy_suppliers` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `contact_person` varchar(100) DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `pharmacy_suppliers`
--

INSERT INTO `pharmacy_suppliers` (`id`, `name`, `contact_person`, `phone`, `email`, `address`, `created_at`, `updated_at`) VALUES
(1, 'Dr.Reddys', 'Raju', '9879879879', 'reddy@gmail.com', 'kkd-5\r\n\r\n', '2025-08-01 04:33:02', '2025-08-01 04:35:25'),
(2, 'My Doctor Pharmacy', 'shaym', '9239423434', 'doc@gmail.com', 'rjy\r\n', '2025-08-01 04:33:50', '2025-08-01 04:33:50'),
(5, 'MedSupply Distributors', 'Michael Johnson', '(555) 123-4567', 'sales@medsupply.com', '123 Health Blvd, Suite 200, Anytown, State 12345', '2025-08-01 05:21:05', '2025-08-01 05:21:05'),
(7, 'Global Healthcare Solutions', 'Samantha Lee', '(555) 234-5678', 'slee@ghs.co', '789 Medical Park, Cityville, State 54321', '2025-08-01 05:22:13', '2025-08-01 05:22:13');

-- --------------------------------------------------------

--
-- Table structure for table `pharmacy_units_of_measure`
--

CREATE TABLE `pharmacy_units_of_measure` (
  `id` int(11) NOT NULL,
  `name` varchar(50) NOT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `pharmacy_units_of_measure`
--

INSERT INTO `pharmacy_units_of_measure` (`id`, `name`, `created_at`, `updated_at`) VALUES
(1, 'mg', '2025-08-01 11:53:09', '2025-08-01 11:53:09'),
(2, 'g', '2025-08-01 11:53:09', '2025-08-01 11:53:09'),
(3, 'ml', '2025-08-01 11:53:09', '2025-08-01 11:53:09'),
(4, 'L', '2025-08-01 11:53:09', '2025-08-01 11:53:09'),
(5, 'capsule(s)', '2025-08-01 11:53:09', '2025-08-01 11:53:09'),
(6, 'tablet(s)', '2025-08-01 11:53:09', '2025-08-01 11:53:09');

-- --------------------------------------------------------

--
-- Table structure for table `referred_persons`
--

CREATE TABLE `referred_persons` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `contact_info` varchar(255) DEFAULT NULL,
  `type` varchar(100) DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `referred_persons`
--

INSERT INTO `referred_persons` (`id`, `name`, `contact_info`, `type`, `created_at`, `updated_at`) VALUES
(1, 'Dr. Ramesh Kumar (Clinic A)', '9876543210', 'Doctor', '2025-06-25 16:04:43', '2025-06-25 16:04:43'),
(2, 'Local Clinic (HealthCare)', 'clinic@healthcare.com', 'Clinic', '2025-06-25 16:04:43', '2025-06-25 16:04:43'),
(3, 'Self Referred', NULL, 'Self', '2025-06-25 16:04:43', '2025-06-25 16:04:43');

-- --------------------------------------------------------

--
-- Table structure for table `roles`
--

CREATE TABLE `roles` (
  `id` int(11) NOT NULL,
  `name` varchar(50) NOT NULL,
  `description` text DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `roles`
--

INSERT INTO `roles` (`id`, `name`, `description`, `created_at`, `updated_at`) VALUES
(1, 'Admin', 'System Administrator with full access.', '2025-06-25 13:05:41', '2025-06-25 13:05:41'),
(2, 'Doctor', 'Medical Doctor with patient management capabilities.', '2025-06-25 13:05:41', '2025-06-25 13:05:41'),
(3, 'Receptionist', 'Front desk staff, manages appointments and basic patient info.', '2025-06-25 13:05:41', '2025-06-25 13:05:41'),
(4, 'Pharmacist', 'Manages pharmacy inventory and dispenses drugs.', '2025-06-25 13:05:41', '2025-06-25 13:05:41'),
(5, 'Lab Technician', 'Manages lab tests and enters results.', '2025-06-25 13:05:41', '2025-06-25 13:05:41'),
(6, 'Nurse', 'Provides patient care and manages medical records (basic).', '2025-06-25 13:05:41', '2025-06-25 13:05:41'),
(7, 'Pharmacy_Manager', 'Manages pharmacy inventory, suppliers, staff, and approves returns.', '2025-07-29 15:54:23', '2025-07-29 15:54:23'),
(8, 'Pharmacy_Sales_Person', 'Handles medicine sales, billing, and basic patient interactions.', '2025-07-29 15:54:23', '2025-07-29 15:54:23');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `role_id` int(11) NOT NULL,
  `first_name` varchar(100) NOT NULL,
  `last_name` varchar(100) NOT NULL,
  `username` varchar(100) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `phone_number` varchar(20) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `status` enum('active','inactive','suspended') DEFAULT 'active',
  `last_login` datetime DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `role_id`, `first_name`, `last_name`, `username`, `email`, `password`, `phone_number`, `address`, `status`, `last_login`, `created_at`, `updated_at`) VALUES
(1, 1, 'System', 'Admin', 'admin', 'admin@example.com', '$2y$10$Sh/3KsbkbQ12FXx.ZBngVOi1ZOTuKa4swBJBgjtIhvZ7HhXQ.Dh3W', '9876543210', '123 Admin Street, City', 'active', '2025-09-25 06:50:09', '2025-06-25 13:05:57', '2025-09-25 06:50:09'),
(2, 2, 'Jane', 'Smith', 'drsmith', 'jane.smith@hms.com', '$2y$10$f1/gyLrKkbOx7vySl.bDV.JyiyyHRGrgfQ9zw0yP7s9OOt0KrKVha', '9988776655', '456 Medical Avenue, City', 'active', '2025-06-25 10:43:02', '2025-06-25 16:10:32', '2025-07-02 10:07:34'),
(3, 2, 'ramapappa', 'rao', 'doctor', 'vgv@gmail.com', '$2y$10$Xdxj1UOk1PmJc9FCOFHA3uY37u2DtyTh31iBvbPuhUFx7XdjeV1.a', '7897897897', 'fgb', 'active', NULL, '2025-07-07 11:19:37', '2025-07-07 11:19:37'),
(4, 2, 'mani', 'man', 'mani', 'bhavicreations@gmail.com', '$2y$10$TfEyAe07XY3Eaj0rcVIeB.hi6evsQ3kKidZnUBwJw1KuVqWmFOlgK', '', '', 'active', '2025-07-08 04:59:54', '2025-07-07 12:05:19', '2025-07-08 04:59:54'),
(5, 2, 'mani', 'man', 'maniraj', 'bhavicreations@gmail.com', '$2y$10$rwP8b1ymOL5B10deOvjyFe5eiq5jhWcHEcJLHBEXsyj8bHmXs42MW', '', '', 'active', NULL, '2025-07-07 12:09:03', '2025-07-07 12:09:03'),
(6, 2, 'divya', 'rani', 'mani', 'bhavicreations@gmail.com', '$2y$10$vK/lrcY2P1gNSrzMSu8O/eJuY6XlH5J.z5fcGZE71uITtd9SpWKQC', '', '', 'active', NULL, '2025-07-07 12:23:24', '2025-07-07 12:23:24'),
(7, 2, 'mango', 'seed', 'mango', 'bhavicreations@gmail.com', '$2y$10$ve/LjK8bNLLPsgiuXc9/peOipoFswf6r47nX2ZGdUvg2Pz8eyMrJC', '7897897897', 'sb g', 'active', NULL, '2025-07-07 12:25:54', '2025-07-07 12:25:54'),
(8, 2, 'papaya', 'papaya', 'papaya', 'bhavicreations@gmail.com', '$2y$10$LVF..XS3HN3gHUo.oiSNPedoeQNlVIU7opG.A/dzc/zlkU4FlPh/K', '', '', 'active', NULL, '2025-07-08 07:58:28', '2025-07-08 07:58:28'),
(9, 2, 'watermelon', 'fruit', 'watermelon', 'bhavicreations@gmail.com', '$2y$10$v505zIh/uAK2RXbfTvbrT.w0I.imDV9q89ayeiRIZWZPqbSppXU7.', '', '', 'active', NULL, '2025-07-08 08:00:45', '2025-07-08 08:00:45'),
(10, 2, 'bheam', 'bheam', 'bheam', 'abhi@gmail.com', '$2y$10$kjsLBUKrFUysEAG6igivd.yGE6W8S0.pNpzhH8/nN9IgjA1sa6TEm', '', '', 'active', NULL, '2025-07-08 08:03:56', '2025-07-08 08:03:56'),
(11, 2, 'sharak', 'sharak', 'sharak', 'bhavicreations3022@gmail.come', '$2y$10$W6chLlM85QpZIu6ERUwfGear8ptlGYWrCgoD.DDbg1BR80BiU.kIq', '', '', 'active', '2025-07-08 11:49:57', '2025-07-08 09:18:21', '2025-07-08 11:49:57'),
(12, 2, 'ramun', 'creations', 'ramun', 'bhavicreations@gmail.com', '$2y$10$o5.M0hPP2XITfdiH06/bIOxKmsf4FaOLbsb63S10DonI61tHyPdHK', '', '', 'active', '2025-07-08 11:49:14', '2025-07-08 09:21:53', '2025-07-08 11:49:14'),
(13, 2, 'ramesh', 'pilli', 'ramesh', 'rameshpilli@gmail.com', '$2y$10$h4NrHZLzeQxYCPgnoMSjwucG3/ypEkzDD9DCGpCjyuDRM8sYAltkG', '7897897895', 'kkkd', 'active', '2025-09-18 05:16:10', '2025-07-24 10:45:53', '2025-09-18 05:16:10'),
(14, 2, 'Harsh vardhan', 'Basara', 'harsha', 'abhvasi@gmail.com', '$2y$10$CDdwoNTxRR38zKognEZOteNUFqoGfhYSnji8jXj0YY8xIvF5deMki', '7531598254', '', 'active', NULL, '2025-07-25 05:13:34', '2025-07-25 05:13:34'),
(15, 7, 'Pharmacy', 'manager', 'pharmacymanager', 'pharmacymanager@gmail.com', '$2y$10$k5D2EBzmfLmXf27c8k/bxetJHqKRXIro0XhyG9NYc.k6Vw33cQlri', '1234567890', '123 Manager St, City', 'active', '2025-09-20 09:37:40', '2025-09-17 17:38:30', '2025-09-20 09:37:40'),
(16, 8, 'sales', 'person', 'salesperson', 'salesperson@gmail.com', '$2y$10$oQylRKBYrYu5ih55WP4P..gx.A3Y.i8q3oNTh9BdWsSv6hHDNqTuq', NULL, NULL, 'active', '2025-09-18 06:49:22', '2025-09-18 10:46:01', '2025-09-18 06:49:22'),
(17, 8, 'suresh', 'ganta', 'PHY-20250918-0005', 'sureshganta@gmail.com', '$2y$10$2Sz657pfJ34JKEn55wtw0u6tp4SoRPJ0RWC3KvVYTBgZnkAS5y2oe', '7894561230', 'kkdrjy\r\n', 'active', '2025-09-22 04:13:37', '2025-09-18 06:09:54', '2025-09-22 04:13:37'),
(18, 8, 'suresh', 'raja', 'PHY-20250918-0006', 'sureshgantaw@gmail.com', '$2y$10$zqELzrAPJSmR9VG4HvFKoe8.ucd.ODus4mLrbCqjqnc3GgtWqC75G', '7894561230', NULL, 'active', '2025-09-19 12:28:45', '2025-09-18 06:11:08', '2025-09-19 12:28:45'),
(19, 8, 'range', 'rao', 'PHY-20250918-0006', 'rangarao@gmail.com', '$2y$10$2eSsEcN97Tpepv7BGIR1be2yV.qyAybHzIB9OY1weZRJpMPZFg8.q', '0000000000', 'rjy', 'active', NULL, '2025-09-18 12:06:29', '2025-09-23 06:52:10'),
(20, 8, 'ramu', 'lankey', 'PHY-20250918-0007', 'ramaraoew@gmail.com', '$2y$10$QalH7X.IXGET5XpR4vIVz.eW./ilCsiu5ohBItrVAMokpRuyqhjri', '7894561237', NULL, 'active', '2025-09-19 04:13:26', '2025-09-18 12:26:45', '2025-09-19 04:13:26'),
(21, 8, 'Bhanu', 'prakesh', 'PHY-20250919-0008', 'bhanu@gmail.com', '$2y$10$grTUWBU20qOMhcuDBdjbNO6hIq7J9Nl9xL282WJKPMUCMn2W31C9K', '1234567890', NULL, 'active', NULL, '2025-09-19 04:53:33', '2025-09-19 04:53:33');

-- --------------------------------------------------------

--
-- Table structure for table `wards`
--

CREATE TABLE `wards` (
  `id` int(11) UNSIGNED NOT NULL,
  `name` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `capacity` int(11) UNSIGNED NOT NULL DEFAULT 0,
  `status` enum('Active','Inactive','Under Maintenance') NOT NULL DEFAULT 'Active',
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  `bed_prefix` varchar(20) NOT NULL DEFAULT 'BED'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `wards`
--

INSERT INTO `wards` (`id`, `name`, `description`, `capacity`, `status`, `created_at`, `updated_at`, `deleted_at`, `bed_prefix`) VALUES
(1, 'General Ward', '', 8, 'Active', '2025-07-26 05:38:05', '2025-07-26 06:34:20', NULL, 'GEN'),
(2, 'Pediatric Ward', 'This ward for children', 20, 'Active', '2025-07-26 06:05:01', '2025-07-26 06:05:01', NULL, 'P');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `appointments`
--
ALTER TABLE `appointments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `appointment_patient_id_foreign` (`patient_id`),
  ADD KEY `appointment_doctor_id_foreign` (`doctor_id`);

--
-- Indexes for table `assets_equipment`
--
ALTER TABLE `assets_equipment`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `asset_tag` (`asset_tag`);

--
-- Indexes for table `beds`
--
ALTER TABLE `beds`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `bed_number_per_ward` (`ward_id`,`bed_number`),
  ADD KEY `ward_id` (`ward_id`);

--
-- Indexes for table `doctors`
--
ALTER TABLE `doctors`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `doctor_id_code` (`doctor_id_code`);

--
-- Indexes for table `doctor_id_sequences`
--
ALTER TABLE `doctor_id_sequences`
  ADD PRIMARY KEY (`name`);

--
-- Indexes for table `hospital_departments`
--
ALTER TABLE `hospital_departments`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `name` (`name`);

--
-- Indexes for table `lab_orders`
--
ALTER TABLE `lab_orders`
  ADD PRIMARY KEY (`id`),
  ADD KEY `patient_id` (`patient_id`),
  ADD KEY `ordered_by` (`ordered_by`);

--
-- Indexes for table `lab_order_files`
--
ALTER TABLE `lab_order_files`
  ADD PRIMARY KEY (`id`),
  ADD KEY `lab_order_item_id` (`lab_order_item_id`);

--
-- Indexes for table `lab_order_items`
--
ALTER TABLE `lab_order_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `lab_order_id` (`lab_order_id`),
  ADD KEY `lab_test_id` (`lab_test_id`);

--
-- Indexes for table `lab_tests`
--
ALTER TABLE `lab_tests`
  ADD PRIMARY KEY (`id`),
  ADD KEY `test_type_id` (`test_type_id`);

--
-- Indexes for table `lab_test_types`
--
ALTER TABLE `lab_test_types`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `name` (`name`);

--
-- Indexes for table `patients`
--
ALTER TABLE `patients`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_patient_type` (`patient_type`),
  ADD KEY `idx_opd_id_code` (`opd_id_code`),
  ADD KEY `idx_ipd_id_code` (`ipd_id_code`),
  ADD KEY `fk_patients_referred_by` (`referred_by_id`),
  ADD KEY `fk_patients_referred_doctor` (`referred_to_doctor_id`);

--
-- Indexes for table `patient_admissions`
--
ALTER TABLE `patient_admissions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_pa_patient_id` (`patient_id`),
  ADD KEY `fk_pa_ward_id` (`ward_id`),
  ADD KEY `fk_pa_bed_id` (`bed_id`);

--
-- Indexes for table `patient_id_sequences`
--
ALTER TABLE `patient_id_sequences`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `prefix` (`prefix`);

--
-- Indexes for table `pharmacy_batches`
--
ALTER TABLE `pharmacy_batches`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `medicine_id` (`medicine_id`,`batch_number`),
  ADD KEY `supplier_id` (`supplier_id`);

--
-- Indexes for table `pharmacy_billings`
--
ALTER TABLE `pharmacy_billings`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_bill_id` (`bill_id`),
  ADD KEY `patient_id` (`patient_id`);

--
-- Indexes for table `pharmacy_billing_payments`
--
ALTER TABLE `pharmacy_billing_payments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_pharmacy_billing_payments_bill` (`bill_id`);

--
-- Indexes for table `pharmacy_brands`
--
ALTER TABLE `pharmacy_brands`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `brand_name` (`brand_name`);

--
-- Indexes for table `pharmacy_categories`
--
ALTER TABLE `pharmacy_categories`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `name` (`name`);

--
-- Indexes for table `pharmacy_dosage_forms`
--
ALTER TABLE `pharmacy_dosage_forms`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `name` (`name`);

--
-- Indexes for table `pharmacy_generics`
--
ALTER TABLE `pharmacy_generics`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `generic_name` (`generic_name`);

--
-- Indexes for table `pharmacy_invoice_sequences`
--
ALTER TABLE `pharmacy_invoice_sequences`
  ADD PRIMARY KEY (`prefix`);

--
-- Indexes for table `pharmacy_manufacturers`
--
ALTER TABLE `pharmacy_manufacturers`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `name` (`name`);

--
-- Indexes for table `pharmacy_medicines`
--
ALTER TABLE `pharmacy_medicines`
  ADD PRIMARY KEY (`id`),
  ADD KEY `manufacturer_id` (`manufacturer_id`),
  ADD KEY `category_id` (`category_id`),
  ADD KEY `created_by_user_id` (`created_by_user_id`),
  ADD KEY `fk_dosage_form` (`dosage_form_id`),
  ADD KEY `fk_unit_of_measure` (`unit_of_measure_id`);

--
-- Indexes for table `pharmacy_purchases`
--
ALTER TABLE `pharmacy_purchases`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `invoice_number` (`invoice_number`),
  ADD KEY `supplier_id` (`supplier_id`),
  ADD KEY `ordered_by_user_id` (`ordered_by_user_id`),
  ADD KEY `received_by_user_id` (`received_by_user_id`);

--
-- Indexes for table `pharmacy_purchase_items`
--
ALTER TABLE `pharmacy_purchase_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `purchase_id` (`purchase_id`),
  ADD KEY `medicine_id` (`medicine_id`),
  ADD KEY `batch_id` (`batch_id`);

--
-- Indexes for table `pharmacy_returns`
--
ALTER TABLE `pharmacy_returns`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sale_id` (`sale_id`),
  ADD KEY `sale_item_id` (`sale_item_id`),
  ADD KEY `medicine_id` (`medicine_id`),
  ADD KEY `batch_id` (`batch_id`),
  ADD KEY `requested_by_user_id` (`requested_by_user_id`),
  ADD KEY `approved_by_user_id` (`approved_by_user_id`),
  ADD KEY `billing_id` (`billing_id`);

--
-- Indexes for table `pharmacy_sales`
--
ALTER TABLE `pharmacy_sales`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `invoice_number` (`invoice_number`),
  ADD KEY `sales_person_id` (`sales_person_id`),
  ADD KEY `patient_id` (`patient_id`),
  ADD KEY `doctor_id` (`doctor_id`);

--
-- Indexes for table `pharmacy_sales_persons`
--
ALTER TABLE `pharmacy_sales_persons`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `salesperson_id` (`salesperson_id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `pharmacy_sale_items`
--
ALTER TABLE `pharmacy_sale_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sale_id` (`sale_id`),
  ADD KEY `medicine_id` (`medicine_id`),
  ADD KEY `batch_id` (`batch_id`);

--
-- Indexes for table `pharmacy_stock_adjustments`
--
ALTER TABLE `pharmacy_stock_adjustments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `medicine_id` (`medicine_id`),
  ADD KEY `batch_id` (`batch_id`),
  ADD KEY `adjusted_by_user_id` (`adjusted_by_user_id`);

--
-- Indexes for table `pharmacy_suppliers`
--
ALTER TABLE `pharmacy_suppliers`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `name` (`name`);

--
-- Indexes for table `pharmacy_units_of_measure`
--
ALTER TABLE `pharmacy_units_of_measure`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `name` (`name`);

--
-- Indexes for table `referred_persons`
--
ALTER TABLE `referred_persons`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `name` (`name`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD KEY `role_id` (`role_id`);

--
-- Indexes for table `wards`
--
ALTER TABLE `wards`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `name` (`name`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `appointments`
--
ALTER TABLE `appointments`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `assets_equipment`
--
ALTER TABLE `assets_equipment`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `beds`
--
ALTER TABLE `beds`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=33;

--
-- AUTO_INCREMENT for table `doctors`
--
ALTER TABLE `doctors`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=45;

--
-- AUTO_INCREMENT for table `hospital_departments`
--
ALTER TABLE `hospital_departments`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `lab_orders`
--
ALTER TABLE `lab_orders`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `lab_order_files`
--
ALTER TABLE `lab_order_files`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `lab_order_items`
--
ALTER TABLE `lab_order_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `lab_tests`
--
ALTER TABLE `lab_tests`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `lab_test_types`
--
ALTER TABLE `lab_test_types`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `patients`
--
ALTER TABLE `patients`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=39;

--
-- AUTO_INCREMENT for table `patient_admissions`
--
ALTER TABLE `patient_admissions`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT for table `patient_id_sequences`
--
ALTER TABLE `patient_id_sequences`
  MODIFY `id` int(5) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `pharmacy_batches`
--
ALTER TABLE `pharmacy_batches`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=28;

--
-- AUTO_INCREMENT for table `pharmacy_billings`
--
ALTER TABLE `pharmacy_billings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=32;

--
-- AUTO_INCREMENT for table `pharmacy_billing_payments`
--
ALTER TABLE `pharmacy_billing_payments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=46;

--
-- AUTO_INCREMENT for table `pharmacy_brands`
--
ALTER TABLE `pharmacy_brands`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `pharmacy_categories`
--
ALTER TABLE `pharmacy_categories`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `pharmacy_dosage_forms`
--
ALTER TABLE `pharmacy_dosage_forms`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `pharmacy_generics`
--
ALTER TABLE `pharmacy_generics`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `pharmacy_manufacturers`
--
ALTER TABLE `pharmacy_manufacturers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `pharmacy_medicines`
--
ALTER TABLE `pharmacy_medicines`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;

--
-- AUTO_INCREMENT for table `pharmacy_purchases`
--
ALTER TABLE `pharmacy_purchases`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `pharmacy_purchase_items`
--
ALTER TABLE `pharmacy_purchase_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `pharmacy_returns`
--
ALTER TABLE `pharmacy_returns`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=41;

--
-- AUTO_INCREMENT for table `pharmacy_sales`
--
ALTER TABLE `pharmacy_sales`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=49;

--
-- AUTO_INCREMENT for table `pharmacy_sales_persons`
--
ALTER TABLE `pharmacy_sales_persons`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `pharmacy_sale_items`
--
ALTER TABLE `pharmacy_sale_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=108;

--
-- AUTO_INCREMENT for table `pharmacy_stock_adjustments`
--
ALTER TABLE `pharmacy_stock_adjustments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `pharmacy_suppliers`
--
ALTER TABLE `pharmacy_suppliers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `pharmacy_units_of_measure`
--
ALTER TABLE `pharmacy_units_of_measure`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `referred_persons`
--
ALTER TABLE `referred_persons`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `roles`
--
ALTER TABLE `roles`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT for table `wards`
--
ALTER TABLE `wards`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `beds`
--
ALTER TABLE `beds`
  ADD CONSTRAINT `fk_beds_ward_id` FOREIGN KEY (`ward_id`) REFERENCES `wards` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `lab_orders`
--
ALTER TABLE `lab_orders`
  ADD CONSTRAINT `lab_orders_ibfk_1` FOREIGN KEY (`patient_id`) REFERENCES `patients` (`id`),
  ADD CONSTRAINT `lab_orders_ibfk_2` FOREIGN KEY (`ordered_by`) REFERENCES `users` (`id`);

--
-- Constraints for table `lab_order_files`
--
ALTER TABLE `lab_order_files`
  ADD CONSTRAINT `lab_order_files_ibfk_1` FOREIGN KEY (`lab_order_item_id`) REFERENCES `lab_order_items` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `lab_order_items`
--
ALTER TABLE `lab_order_items`
  ADD CONSTRAINT `lab_order_items_ibfk_1` FOREIGN KEY (`lab_order_id`) REFERENCES `lab_orders` (`id`),
  ADD CONSTRAINT `lab_order_items_ibfk_2` FOREIGN KEY (`lab_test_id`) REFERENCES `lab_tests` (`id`);

--
-- Constraints for table `lab_tests`
--
ALTER TABLE `lab_tests`
  ADD CONSTRAINT `lab_tests_ibfk_1` FOREIGN KEY (`test_type_id`) REFERENCES `lab_test_types` (`id`);

--
-- Constraints for table `patients`
--
ALTER TABLE `patients`
  ADD CONSTRAINT `fk_patients_referred_by` FOREIGN KEY (`referred_by_id`) REFERENCES `referred_persons` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_patients_referred_doctor` FOREIGN KEY (`referred_to_doctor_id`) REFERENCES `doctors` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Constraints for table `patient_admissions`
--
ALTER TABLE `patient_admissions`
  ADD CONSTRAINT `fk_pa_bed_id` FOREIGN KEY (`bed_id`) REFERENCES `beds` (`id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_pa_patient_id` FOREIGN KEY (`patient_id`) REFERENCES `patients` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_pa_ward_id` FOREIGN KEY (`ward_id`) REFERENCES `wards` (`id`) ON UPDATE CASCADE;

--
-- Constraints for table `pharmacy_batches`
--
ALTER TABLE `pharmacy_batches`
  ADD CONSTRAINT `pharmacy_batches_ibfk_1` FOREIGN KEY (`medicine_id`) REFERENCES `pharmacy_medicines` (`id`),
  ADD CONSTRAINT `pharmacy_batches_ibfk_2` FOREIGN KEY (`supplier_id`) REFERENCES `pharmacy_suppliers` (`id`);

--
-- Constraints for table `pharmacy_billing_payments`
--
ALTER TABLE `pharmacy_billing_payments`
  ADD CONSTRAINT `fk_pharmacy_billing_payments_bill` FOREIGN KEY (`bill_id`) REFERENCES `pharmacy_billings` (`bill_id`);

--
-- Constraints for table `pharmacy_medicines`
--
ALTER TABLE `pharmacy_medicines`
  ADD CONSTRAINT `fk_dosage_form` FOREIGN KEY (`dosage_form_id`) REFERENCES `pharmacy_dosage_forms` (`id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_unit_of_measure` FOREIGN KEY (`unit_of_measure_id`) REFERENCES `pharmacy_units_of_measure` (`id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `pharmacy_medicines_ibfk_1` FOREIGN KEY (`manufacturer_id`) REFERENCES `pharmacy_manufacturers` (`id`),
  ADD CONSTRAINT `pharmacy_medicines_ibfk_2` FOREIGN KEY (`category_id`) REFERENCES `pharmacy_categories` (`id`),
  ADD CONSTRAINT `pharmacy_medicines_ibfk_3` FOREIGN KEY (`created_by_user_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `pharmacy_purchases`
--
ALTER TABLE `pharmacy_purchases`
  ADD CONSTRAINT `pharmacy_purchases_ibfk_1` FOREIGN KEY (`supplier_id`) REFERENCES `pharmacy_suppliers` (`id`),
  ADD CONSTRAINT `pharmacy_purchases_ibfk_2` FOREIGN KEY (`ordered_by_user_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `pharmacy_purchases_ibfk_3` FOREIGN KEY (`received_by_user_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `pharmacy_purchase_items`
--
ALTER TABLE `pharmacy_purchase_items`
  ADD CONSTRAINT `pharmacy_purchase_items_ibfk_1` FOREIGN KEY (`purchase_id`) REFERENCES `pharmacy_purchases` (`id`),
  ADD CONSTRAINT `pharmacy_purchase_items_ibfk_2` FOREIGN KEY (`medicine_id`) REFERENCES `pharmacy_medicines` (`id`),
  ADD CONSTRAINT `pharmacy_purchase_items_ibfk_3` FOREIGN KEY (`batch_id`) REFERENCES `pharmacy_batches` (`id`);

--
-- Constraints for table `pharmacy_returns`
--
ALTER TABLE `pharmacy_returns`
  ADD CONSTRAINT `fk_pharmacy_returns_billing_id` FOREIGN KEY (`billing_id`) REFERENCES `pharmacy_billings` (`id`),
  ADD CONSTRAINT `pharmacy_returns_ibfk_1` FOREIGN KEY (`sale_id`) REFERENCES `pharmacy_sales` (`id`),
  ADD CONSTRAINT `pharmacy_returns_ibfk_2` FOREIGN KEY (`sale_item_id`) REFERENCES `pharmacy_sale_items` (`id`),
  ADD CONSTRAINT `pharmacy_returns_ibfk_3` FOREIGN KEY (`medicine_id`) REFERENCES `pharmacy_medicines` (`id`),
  ADD CONSTRAINT `pharmacy_returns_ibfk_4` FOREIGN KEY (`batch_id`) REFERENCES `pharmacy_batches` (`id`),
  ADD CONSTRAINT `pharmacy_returns_ibfk_5` FOREIGN KEY (`requested_by_user_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `pharmacy_returns_ibfk_6` FOREIGN KEY (`approved_by_user_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `pharmacy_sales`
--
ALTER TABLE `pharmacy_sales`
  ADD CONSTRAINT `pharmacy_sales_ibfk_1` FOREIGN KEY (`sales_person_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `pharmacy_sales_ibfk_2` FOREIGN KEY (`patient_id`) REFERENCES `patients` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `pharmacy_sales_ibfk_3` FOREIGN KEY (`doctor_id`) REFERENCES `doctors` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `pharmacy_sale_items`
--
ALTER TABLE `pharmacy_sale_items`
  ADD CONSTRAINT `pharmacy_sale_items_ibfk_1` FOREIGN KEY (`sale_id`) REFERENCES `pharmacy_sales` (`id`),
  ADD CONSTRAINT `pharmacy_sale_items_ibfk_2` FOREIGN KEY (`medicine_id`) REFERENCES `pharmacy_medicines` (`id`),
  ADD CONSTRAINT `pharmacy_sale_items_ibfk_3` FOREIGN KEY (`batch_id`) REFERENCES `pharmacy_batches` (`id`);

--
-- Constraints for table `pharmacy_stock_adjustments`
--
ALTER TABLE `pharmacy_stock_adjustments`
  ADD CONSTRAINT `pharmacy_stock_adjustments_ibfk_1` FOREIGN KEY (`medicine_id`) REFERENCES `pharmacy_medicines` (`id`),
  ADD CONSTRAINT `pharmacy_stock_adjustments_ibfk_2` FOREIGN KEY (`batch_id`) REFERENCES `pharmacy_batches` (`id`),
  ADD CONSTRAINT `pharmacy_stock_adjustments_ibfk_3` FOREIGN KEY (`adjusted_by_user_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `users`
--
ALTER TABLE `users`
  ADD CONSTRAINT `users_ibfk_1` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
