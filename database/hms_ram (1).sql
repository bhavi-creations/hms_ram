-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jul 25, 2025 at 07:20 AM
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
(7, 37, 43, '2025-07-25', '17:04:00', 'general', 'Pending', '2025-07-25 05:09:08', '2025-07-25 05:10:19', NULL);

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
(15, 'DOC-250707-0001', 'raheem', 'raj', 'Male', '2025-07-01', '7897897897', 'doc@gmai.com', 'fsvfebsb', 'lingam', '7487477878', NULL, 'eye', 'mbbs', '2958453', '398593', 'ngerhe', 2, 'its a bio', 1, '2025-07-01', 'Full-time', 'temporary', 'senior eye specialist', 400.00, 15.00, 'SBI5572286', 'state bank of india', 'SBIN68', 'erxpp8768', NULL, NULL, NULL, NULL, NULL, '[]', 1, 'Active', '2025-07-06 11:13:00', '2025-07-07 05:43:58', '2025-07-08 11:26:12'),
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
(4, 'PAT-250628-00001', 'General', NULL, 'OPD-250628-00001', 'IPD-250705-00022', 'GEN-250724-00015', NULL, 'ram ram hai', 'raj ', '2025-06-09', 'Male', 'A+', 'single ', 'student', 'ttd', '7897897897', 'bhavicreations3022@gmail.comettttt', 'ttttt', '3213213213', 'errrrt', 't', NULL, 2, 'fg', '[\"1751439791_7786_12345.pdf\",\"1751439814_7833_Sales_by_nanna_20250614_062022.pdf\"]', 606.00, 6.00, 569.64, '2025-06-28 03:48:50', '2025-07-24 05:25:23'),
(5, 'PAT-250628-00002', 'General', NULL, 'OPD-250628-00002', NULL, 'GEN-250709-00014', NULL, 'ramu', 'ling', '2025-06-06', 'Male', '', '', '', 'hg', '7897897897', 'bhavicreations3022@gmail.come', 'rao', '3213213213', 'ytf', 'yf', NULL, NULL, '', NULL, 6660.00, 60.00, 2664.00, '2025-06-28 03:49:50', '2025-07-09 04:05:46'),
(7, 'PAT-250628-00001', 'IPD', NULL, NULL, 'IPD-250705-00028', 'GEN-250628-00001', NULL, 'raja', 'creations', '2025-06-12', 'Male', 'A+', 'single ', 'student', 'dgbgggggggggg', '7897897897', 'bhavicreations@gmail.com', 'rao', '3213213213', 'gfs', 'srtg', NULL, 1, 'dgt', '[\"uploads\\/patient_reports\\/1751085590_031e227b11d0d6ac3658.jpg\"]', 1222.00, 1.00, 1209.78, '2025-06-28 04:39:50', '2025-07-05 10:38:53'),
(8, 'PAT-250628-00002', 'IPD', NULL, 'OPD-250705-00008', 'IPD-250705-00006', 'GEN-250628-00002', NULL, 'raja', 'creations', '2025-06-01', 'Male', 'B+', 'single ', 'student', 'fd', '7897897897', 'vgv@gmail.com', 'rao', '3213213213', 'gf', 'gf', NULL, 1, 'f', '[\"uploads\\/patient_reports\\/1751085659_9d981954beb66d21e9ad.png\"]', 1345.00, 1.00, 1331.55, '2025-06-28 04:40:59', '2025-07-05 07:31:11'),
(9, 'PAT-250628-00003', 'IPD', NULL, 'OPD-250628-00001', 'IPD-250705-00020', NULL, NULL, 'raja', 'creations ', '2025-06-04', 'Female', 'o+', 'single ', 'fd', 'df', '7897897897', 'latha@gmail.com', 'rao', '3213213213', 'febd', 'erb', NULL, 1, 'fet', '[\"uploads\\/patient_reports\\/1751085717_226cc379f0d3c564a203.png\"]', 3453.00, 4.00, 3314.88, '2025-06-28 04:41:57', '2025-07-05 10:02:07'),
(10, 'PAT-250628-00004', 'IPD', NULL, NULL, 'IPD-250705-00018', NULL, 'CUS-250628-00001', 'ramu', 'raju', '2025-06-02', 'Other', 'A+', 'single ', 'student', 'rgvs', '7897897897', 'latha@gmail.com', 'rao', '3213213213', 'srg', 'sreg', NULL, 1, 'czsrszgg', '[\"uploads\\/patient_reports\\/1751085772_a098277ebe53be0504f7.png\"]', 43234.00, 5.00, 41072.30, '2025-06-28 04:42:52', '2025-07-05 09:58:32'),
(11, 'PAT-250628-00005', 'IPD', NULL, NULL, 'IPD-250705-00004', 'GEN-250628-00003', NULL, 'ramu', 'healthcare', '2025-06-10', 'Female', 'A+', 'single ', 'student', 'eaf', '7897897897', 'visoi@gmail.com', 'ttttt', '3213213213', 'sdWvge', 'wegv', NULL, 1, 'vdew', '[\"1751086114_96a6950429f76305c189.jpg\"]', 534.00, 3.00, 517.98, '2025-06-28 04:48:34', '2025-07-05 07:12:12'),
(13, 'PAT-250628-00007', 'IPD', NULL, 'OPD-250628-00003', 'IPD-250705-00003', NULL, NULL, 'ramu', 'creations', '2025-06-11', 'Male', 'A+', 'rer', 'er', 'ef bes', '7897897897', 'bhavicreations@gmail.com', 'rao', '3213213213', 'esr', 'er', NULL, 2, '', '[\"1751698881_2515_vk poster 9.png\"]', 555.00, 5.00, 527.25, '2025-06-28 07:31:24', '2025-07-05 07:02:10'),
(14, 'PAT-250628-00008', 'IPD', NULL, 'OPD-250628-00004', 'IPD-250705-00010', NULL, NULL, 'raja', 'healthcare', '2025-06-14', 'Female', 'A+', 'single ', 'student', 'ewf', '2525252525', 'bhavicreations@gmail.com', 'rao', '3213213213', 'wef', 'wef', NULL, 1, 'dvse', '[\"1751095934_657b769b4755bdc13eb1.png\"]', 2432.00, 4.00, 2334.72, '2025-06-28 07:32:14', '2025-07-25 09:44:46'),
(15, 'PAT-250628-00010', 'IPD', NULL, NULL, 'IPD-250705-00030', 'GEN-250628-00005', NULL, 'jana', 'creations', '2025-06-10', 'Male', 'B-', 'single ', 'student', 'hgmd', '2525252525', 'bhavicreations@gmail.com', 'rao', '3213213213', 'ghg', 'dhg', NULL, 3, 'fc', NULL, 645.00, 5.00, 612.75, '2025-06-28 09:18:19', '2025-07-25 09:44:41'),
(16, 'PAT-250628-00011', 'IPD', NULL, NULL, 'IPD-250705-00032', 'GEN-250628-00006', NULL, 'ramu', 'ling', '2025-06-10', 'Male', '', '', '', '', '', '', '', '', '', '', NULL, NULL, '', NULL, 0.00, 0.00, 0.00, '2025-06-28 09:41:39', '2025-07-05 10:52:21'),
(17, 'PAT-250628-00012', 'IPD', 'General', NULL, 'IPD-250710-00043', 'GEN-250628-00007', NULL, 'niranjan', 'wfw', '2025-06-24', 'Male', 'AB-', 'single ', '', '', '', '', '', '', '', '', NULL, NULL, '', NULL, 0.00, 0.00, 0.00, '2025-06-28 09:57:11', '2025-07-10 14:09:04'),
(18, 'PAT-250702-00013', 'IPD', NULL, 'OPD-250702-00005', 'IPD-250705-00008', NULL, NULL, 'peter', 'raj', '2025-07-01', 'Female', 'A+', '', '', '', '', '', '', '', '', '', NULL, NULL, '', NULL, 0.00, 0.00, 0.00, '2025-07-02 04:40:53', '2025-07-05 07:50:54'),
(19, 'PAT-250702-00014', 'Discharged', NULL, NULL, NULL, 'GEN-250702-00008', NULL, 'ratnam', 'dvfd', '2025-07-01', 'Male', '', '', '', '', '', '', '', '', '', '', NULL, NULL, '', NULL, 0.00, 0.00, 0.00, '2025-07-02 04:46:56', '2025-07-05 12:07:19'),
(20, 'PAT-250702-00015', 'IPD', NULL, NULL, 'IPD-250705-00016', 'GEN-250702-00009', NULL, 'bravo', 'marco', '2025-07-01', 'Male', 'A+', 'single ', 'student', 'fdb ttrbn', '1231231231', 'bhavicreations@gmail.com', 'rao', '3213213213', 'dtr', 'trf s', NULL, 1, 'fdsd', '[\"1751433226_8683_arjuna online iti _1_.pdf\",\"1751433226_5226_stock-vector-alphabet-letters-icon-logo-vk-or-kv-monogram-2203519181_1747721447.jpg\",\"1751433226_3706_vk logo 6.jpg\"]', 5555.00, 5.00, 5277.25, '2025-07-02 05:13:46', '2025-07-25 09:41:07'),
(21, 'PAT-250702-00016', 'General', NULL, NULL, NULL, 'GEN-250702-00010', NULL, 'ramu', 'ling', '2025-07-01', 'Male', 'A+', '', '', '', '', '', '', '', '', '', NULL, NULL, '', '[\"1751434062_8995_Vendor_Report_20250607_102423.pdf\",\"1751434062_7977_vk new 4.png\"]', 0.00, 0.00, 0.00, '2025-07-02 05:27:42', '2025-07-09 04:05:42'),
(22, 'PAT-250702-00017', 'IPD', NULL, NULL, 'IPD-250705-00002', NULL, 'CUS-250702-00002', 'ramuraju', 'creations', '2025-06-11', 'Male', '', '', '', '', '', '', '', '', '', '', NULL, NULL, '', NULL, 0.00, 0.00, 0.00, '2025-07-02 11:53:40', '2025-07-05 06:43:02'),
(23, 'PAT-250705-00018', 'IPD', 'General', 'OPD-250705-00006', 'IPD-250705-00041', 'GEN-250705-00013', NULL, 'vinay', 'trh', '2025-04-01', 'Male', 'O+', '', '', '', '', '', '', '', '', '', NULL, NULL, '', '[\"1751688337_4093_stock_in_details_13.pdf\",\"1751688337_8056_vk poster 9.png\"]', 0.00, 0.00, 0.00, '2025-07-05 04:05:37', '2025-07-05 12:03:30'),
(24, 'PAT-250705-00019', 'IPD', NULL, NULL, 'IPD-250705-00034', 'GEN-250705-00011', NULL, 'mango', 'kai', '2025-07-01', 'Male', '', '', '', '', '', '', '', '', '', '', NULL, NULL, '', NULL, 0.00, 0.00, 0.00, '2025-07-05 10:05:39', '2025-07-05 10:56:00'),
(25, 'PAT-250705-00020', 'IPD', 'General', NULL, 'IPD-250705-00040', 'GEN-250705-00012', NULL, 'apple', 'zz', '2025-07-02', 'Female', '', '', '', '', '', '', '', '', '', '', NULL, NULL, '', NULL, 0.00, 0.00, 0.00, '2025-07-05 10:06:05', '2025-07-05 12:03:06'),
(26, 'PAT-250705-00021', 'IPD', NULL, 'OPD-250705-00009', 'IPD-250705-00036', NULL, NULL, 'banana', 'raju', '2025-07-01', 'Female', '', '', '', '', '', '', '', '', '', '', NULL, NULL, '', NULL, 0.00, 0.00, 0.00, '2025-07-05 10:06:22', '2025-07-05 11:07:16'),
(27, 'PAT-250705-00022', 'Discharged', NULL, 'OPD-250705-00010', NULL, NULL, NULL, 'jack', 'fruit', '2025-07-02', 'Other', '', '', '', '', '', '', '', '', '', '', NULL, NULL, '', NULL, 0.00, 0.00, 0.00, '2025-07-05 10:06:49', '2025-07-09 04:07:20'),
(28, 'PAT-250705-00023', 'OPD', NULL, 'OPD-250705-00011', NULL, NULL, NULL, 'painapple', 'ss', '2025-07-01', 'Other', '', '', '', '', '', '', '', '', '', '', NULL, NULL, '', NULL, 0.00, 0.00, 0.00, '2025-07-05 10:07:06', '2025-07-05 10:07:06'),
(29, 'PAT-250705-00024', 'IPD', NULL, NULL, 'IPD-250705-00026', NULL, 'CUS-250705-00003', 'rappa', 'rappa', '2025-07-01', 'Female', '', '', '', '', '', '', '', '', '', '', NULL, NULL, '', NULL, 0.00, 0.00, 0.00, '2025-07-05 10:07:25', '2025-07-05 10:33:00'),
(30, 'PAT-250705-00025', 'IPD', NULL, NULL, 'IPD-250705-00024', NULL, 'CUS-250705-00004', 'promo', 'granite', '2025-07-01', 'Other', '', '', '', '', '', '', '', '', '', '', NULL, NULL, '', NULL, 0.00, 0.00, 0.00, '2025-07-05 10:07:56', '2025-07-05 10:25:27'),
(31, 'PAT-250709-00026', 'IPD', 'Casualty', NULL, 'IPD-250709-00042', NULL, 'CUS-250709-00005', 'sample', 'patient', '2025-07-01', 'Male', '', '', '', '', '', '', '', '', '', '', 26, NULL, '', NULL, 232.00, 0.00, 232.00, '2025-07-09 05:20:04', '2025-07-09 05:22:46'),
(32, 'PAT-250709-00027', 'Casualty', NULL, NULL, NULL, NULL, 'CUS-250709-00006', 'ramu', 'creations', '2025-07-03', 'Male', '', '', '', '', '', '', '', '', '', '', 15, NULL, '', NULL, 400.00, 8.00, 368.00, '2025-07-09 05:46:50', '2025-07-09 05:46:50'),
(33, 'PAT-250709-00028', 'Casualty', NULL, NULL, NULL, NULL, 'CUS-250709-00007', 'wednesday ', 'week', '2025-07-01', 'Male', '', '', '', '', '', '', '', '', '', '', 18, NULL, '', NULL, 400.00, 2.00, 392.00, '2025-07-09 06:50:10', '2025-07-09 07:09:36'),
(34, 'PAT-250724-00029', 'General', NULL, NULL, NULL, 'GEN-250724-00016', NULL, 'janu', 'wing', '2025-07-25', 'Male', 'A+', 'single ', '', 'kkd', '7897897897', 'bhavicreatiodns@gmail.com', 'ttttt', '3213213213', 'e', 'e', 15, 1, 'k', NULL, 400.00, 0.00, 400.00, '2025-07-24 05:27:15', '2025-07-24 05:27:15'),
(35, 'PAT-250724-00030', 'General', NULL, NULL, NULL, 'GEN-250724-00017', NULL, 'kumar', 'gorri', '2025-07-07', 'Male', '', '', '', '', '', '', '', '', '', '', 43, 2, '', NULL, 300.00, 0.00, 300.00, '2025-07-24 10:50:15', '2025-07-24 10:50:15'),
(36, 'PAT-250724-00031', 'General', NULL, NULL, NULL, 'GEN-250724-00018', NULL, 'kanta', 'mani', '2025-07-02', 'Male', 'A-', '', '', '', '', '', '', '', '', '', 43, NULL, 'hi ', '[\"1753355997_7591_bhavi logo.png\",\"1753356877_dd4e96ceb6e0e64dd084.pdf\"]', 300.00, 1.00, 297.00, '2025-07-24 11:19:57', '2025-07-24 11:39:58'),
(37, 'PAT-250725-00032', 'General', NULL, NULL, NULL, 'GEN-250725-00019', NULL, 'naidu', 'raddy', '2025-07-09', 'Male', 'B-', '', '', '', '4564564564', '', '', '', '', '', 43, 3, 'g', NULL, 300.00, 0.00, 300.00, '2025-07-25 05:09:08', '2025-07-25 05:09:08');

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
(1, 'PAT', 32, '2025-07-25 10:39:08'),
(2, 'OPD', 11, '2025-07-05 15:37:06'),
(3, 'IPD', 43, '2025-07-10 19:39:04'),
(4, 'GEN', 19, '2025-07-25 10:39:08'),
(5, 'CUS', 7, '2025-07-09 12:20:10');

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
(6, 'Nurse', 'Provides patient care and manages medical records (basic).', '2025-06-25 13:05:41', '2025-06-25 13:05:41');

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
(1, 1, 'System', 'Admin', 'admin', 'admin@example.com', '$2y$10$f1/gyLrKkbOx7vySl.bDV.JyiyyHRGrgfQ9zw0yP7s9OOt0KrKVha', '9876543210', '123 Admin Street, City', 'active', '2025-07-25 03:58:24', '2025-06-25 13:05:57', '2025-07-25 03:58:24'),
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
(13, 2, 'ramesh', 'pilli', 'ramesh', 'rameshpilli@gmail.com', '$2y$10$h4NrHZLzeQxYCPgnoMSjwucG3/ypEkzDD9DCGpCjyuDRM8sYAltkG', '7897897895', 'kkkd', 'active', '2025-07-25 04:20:53', '2025-07-24 10:45:53', '2025-07-25 04:20:53'),
(14, 2, 'Harsh vardhan', 'Basara', 'harsha', 'abhvasi@gmail.com', '$2y$10$CDdwoNTxRR38zKognEZOteNUFqoGfhYSnji8jXj0YY8xIvF5deMki', '7531598254', '', 'active', NULL, '2025-07-25 05:13:34', '2025-07-25 05:13:34');

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
-- Indexes for table `patient_id_sequences`
--
ALTER TABLE `patient_id_sequences`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `prefix` (`prefix`);

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
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `appointments`
--
ALTER TABLE `appointments`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

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
-- AUTO_INCREMENT for table `patients`
--
ALTER TABLE `patients`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=38;

--
-- AUTO_INCREMENT for table `patient_id_sequences`
--
ALTER TABLE `patient_id_sequences`
  MODIFY `id` int(5) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `referred_persons`
--
ALTER TABLE `referred_persons`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `roles`
--
ALTER TABLE `roles`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `patients`
--
ALTER TABLE `patients`
  ADD CONSTRAINT `fk_patients_referred_by` FOREIGN KEY (`referred_by_id`) REFERENCES `referred_persons` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_patients_referred_doctor` FOREIGN KEY (`referred_to_doctor_id`) REFERENCES `doctors` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Constraints for table `users`
--
ALTER TABLE `users`
  ADD CONSTRAINT `users_ibfk_1` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
