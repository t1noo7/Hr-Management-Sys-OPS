-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Dec 27, 2025 at 11:35 AM
-- Server version: 10.4.28-MariaDB
-- PHP Version: 8.0.28

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `hr_management`
--

-- --------------------------------------------------------

--
-- Table structure for table `alogin`
--

CREATE TABLE `alogin` (
  `id` int(11) NOT NULL,
  `email` tinytext NOT NULL,
  `password` longtext NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `alogin`
--

INSERT INTO `alogin` (`id`, `email`, `password`) VALUES
(1, 'admin', 'admin');

-- --------------------------------------------------------

--
-- Table structure for table `attendance`
--

CREATE TABLE `attendance` (
  `id` int(11) NOT NULL,
  `emp_id` int(11) NOT NULL,
  `work_date` date NOT NULL,
  `check_in_morning` datetime DEFAULT NULL,
  `check_out_lunch` datetime DEFAULT NULL,
  `check_in_afternoon` datetime DEFAULT NULL,
  `check_out_evening` datetime DEFAULT NULL,
  `check_in_ot` datetime DEFAULT NULL,
  `check_out_ot` datetime DEFAULT NULL,
  `work_hours` decimal(5,2) DEFAULT 0.00 COMMENT 'Tổng giờ làm việc',
  `ot_hours` decimal(6,2) DEFAULT 0.00,
  `total_hours` decimal(6,2) DEFAULT 0.00,
  `fraud_flag` tinyint(4) DEFAULT 0,
  `fraud_reason` varchar(255) DEFAULT NULL COMMENT 'Gian lận',
  `morning_absent_reason` text DEFAULT NULL,
  `morning_presence_confirmed` tinyint(1) DEFAULT 0 COMMENT 'Xác nhận hiện diện sáng (11:59)',
  `morning_presence_confirmed_at` datetime DEFAULT NULL COMMENT 'Thời gian xác nhận sáng',
  `afternoon_presence_confirmed` tinyint(1) DEFAULT 0 COMMENT 'Xác nhận hiện diện chiều (17:59)',
  `presence_verified` tinyint(1) DEFAULT 0 COMMENT 'Đã xác minh bằng ảnh',
  `presence_confirmed_at` datetime DEFAULT NULL COMMENT 'Thời gian xác nhận chiều',
  `presence_image` varchar(255) DEFAULT NULL COMMENT 'Đường dẫn ảnh xác thực',
  `presence_hash` varchar(64) DEFAULT NULL COMMENT 'Hash của ảnh',
  `presence_question` text DEFAULT NULL COMMENT 'Câu hỏi ngẫu nhiên',
  `presence_answer` text DEFAULT NULL COMMENT 'Câu trả lời của nhân viên'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `attendance`
--

INSERT INTO `attendance` (`id`, `emp_id`, `work_date`, `check_in_morning`, `check_out_lunch`, `check_in_afternoon`, `check_out_evening`, `check_in_ot`, `check_out_ot`, `work_hours`, `ot_hours`, `total_hours`, `fraud_flag`, `fraud_reason`, `morning_absent_reason`, `morning_presence_confirmed`, `morning_presence_confirmed_at`, `afternoon_presence_confirmed`, `presence_verified`, `presence_confirmed_at`, `presence_image`, `presence_hash`, `presence_question`, `presence_answer`) VALUES
(7, 107, '2025-12-07', '2025-12-07 02:18:41', '2025-12-07 12:25:33', '2025-12-07 15:39:49', '2025-12-07 15:49:59', '2025-12-07 15:50:23', '2025-12-07 19:30:29', 0.00, 5.93, 13.93, 0, NULL, NULL, 0, NULL, 0, 0, NULL, NULL, NULL, NULL, NULL),
(14, 104, '2025-12-07', NULL, NULL, '2025-12-07 15:41:23', NULL, NULL, NULL, 0.00, 0.00, 0.00, 0, NULL, NULL, 0, NULL, 0, 0, NULL, NULL, NULL, NULL, NULL),
(15, 106, '2025-12-07', NULL, NULL, '2025-12-07 15:52:47', NULL, NULL, NULL, 0.00, 0.00, 0.00, 0, NULL, NULL, 0, NULL, 0, 0, NULL, NULL, NULL, NULL, NULL),
(16, 102, '2025-12-07', NULL, NULL, '2025-12-07 15:53:06', '2025-12-07 19:10:36', NULL, NULL, 0.00, 0.00, 0.00, 0, NULL, NULL, 0, NULL, 0, 0, NULL, NULL, NULL, NULL, NULL),
(17, 109, '2025-12-07', NULL, NULL, '2025-12-07 15:53:18', NULL, NULL, NULL, 0.00, 0.00, 0.00, 0, NULL, NULL, 0, NULL, 0, 0, NULL, NULL, NULL, NULL, NULL),
(18, 112, '2025-12-07', NULL, NULL, '2025-12-07 15:53:37', '2025-12-07 19:25:43', NULL, NULL, 0.00, 0.00, 3.72, 0, NULL, NULL, 0, NULL, 0, 0, NULL, NULL, NULL, NULL, NULL),
(19, 105, '2025-12-07', NULL, NULL, '2025-12-07 16:09:35', '2025-12-07 18:31:08', NULL, NULL, 0.00, 0.00, 2.22, 0, NULL, NULL, 0, NULL, 0, 0, NULL, NULL, NULL, NULL, NULL),
(20, 111, '2025-12-07', NULL, NULL, '2025-12-07 16:30:53', NULL, NULL, NULL, 0.00, 0.00, 0.00, 0, NULL, NULL, 0, NULL, 0, 0, NULL, NULL, NULL, NULL, NULL),
(21, 101, '2025-12-07', NULL, NULL, '2025-12-07 16:31:07', '2025-12-07 19:31:07', NULL, NULL, 0.00, 0.00, 3.00, 0, NULL, NULL, 0, NULL, 0, 0, NULL, NULL, NULL, NULL, NULL),
(22, 103, '2025-12-07', NULL, NULL, '2025-12-07 16:31:37', NULL, NULL, NULL, 0.00, 0.00, 0.00, 0, NULL, NULL, 0, NULL, 0, 0, NULL, NULL, NULL, NULL, NULL),
(23, 108, '2025-12-07', NULL, NULL, '2025-12-07 16:42:05', '2025-12-07 17:31:21', NULL, NULL, 0.00, 0.00, 0.00, 0, NULL, NULL, 0, NULL, 0, 0, NULL, NULL, NULL, NULL, NULL),
(24, 107, '2025-12-08', '2025-12-08 01:41:49', '2025-12-08 16:37:09', '2025-12-08 16:37:11', '2025-12-08 18:29:06', '2025-12-08 18:29:08', NULL, 0.00, 8.77, 16.77, 0, NULL, NULL, 0, NULL, 0, 0, NULL, NULL, NULL, NULL, NULL),
(25, 101, '2025-12-08', '2025-12-08 01:42:05', '2025-12-08 18:30:50', '2025-12-08 18:30:51', NULL, NULL, NULL, 0.00, 0.00, 0.00, 0, NULL, NULL, 0, NULL, 0, 0, NULL, NULL, NULL, NULL, NULL),
(26, 105, '2025-12-08', '2025-12-08 01:42:22', '2025-12-08 18:30:03', '2025-12-08 18:30:04', NULL, NULL, NULL, 0.00, 0.00, 0.00, 0, NULL, NULL, 0, NULL, 0, 0, NULL, NULL, NULL, NULL, NULL),
(27, 106, '2025-12-08', '2025-12-08 01:42:42', '2025-12-08 18:31:17', '2025-12-08 18:31:18', NULL, NULL, NULL, 0.00, 0.00, 0.00, 0, NULL, NULL, 0, NULL, 0, 0, NULL, NULL, NULL, NULL, NULL),
(28, 107, '2025-12-13', '2025-12-12 10:01:02', NULL, NULL, NULL, NULL, NULL, 0.00, 0.00, 0.00, 0, NULL, NULL, 0, NULL, 0, 0, NULL, NULL, NULL, NULL, NULL),
(29, 106, '2025-12-13', '2025-12-12 10:01:23', NULL, NULL, NULL, NULL, NULL, 0.00, 0.00, 0.00, 0, NULL, NULL, 0, NULL, 0, 0, NULL, NULL, NULL, NULL, NULL),
(30, 108, '2025-12-13', '2025-12-12 10:01:40', NULL, NULL, NULL, NULL, NULL, 0.00, 0.00, 0.00, 0, NULL, NULL, 0, NULL, 0, 0, NULL, NULL, NULL, NULL, NULL),
(31, 105, '2025-12-13', '2025-12-12 10:01:57', NULL, NULL, NULL, NULL, NULL, 0.00, 0.00, 0.00, 0, NULL, NULL, 0, NULL, 0, 0, NULL, NULL, NULL, NULL, NULL),
(32, 110, '2025-12-13', '2025-12-12 10:02:14', NULL, NULL, NULL, NULL, NULL, 0.00, 0.00, 0.00, 0, NULL, NULL, 0, NULL, 0, 0, NULL, NULL, NULL, NULL, NULL),
(33, 112, '2025-12-13', '2025-12-12 10:02:32', NULL, NULL, NULL, NULL, NULL, 0.00, 0.00, 0.00, 0, NULL, NULL, 0, NULL, 0, 0, NULL, NULL, NULL, NULL, NULL),
(34, 102, '2025-12-13', '2025-12-12 10:02:45', NULL, NULL, NULL, NULL, NULL, 0.00, 0.00, 0.00, 0, NULL, NULL, 0, NULL, 0, 0, NULL, NULL, NULL, NULL, NULL),
(35, 104, '2025-12-13', '2025-12-12 10:02:57', NULL, NULL, NULL, NULL, NULL, 0.00, 0.00, 0.00, 0, NULL, NULL, 0, NULL, 0, 0, NULL, NULL, NULL, NULL, NULL),
(36, 109, '2025-12-13', '2025-12-12 10:03:09', NULL, NULL, NULL, NULL, NULL, 0.00, 0.00, 0.00, 0, NULL, NULL, 0, NULL, 0, 0, NULL, NULL, NULL, NULL, NULL),
(37, 103, '2025-12-13', '2025-12-12 10:03:55', NULL, NULL, NULL, NULL, NULL, 0.00, 0.00, 0.00, 0, NULL, NULL, 0, NULL, 0, 0, NULL, NULL, NULL, NULL, NULL),
(38, 101, '2025-12-13', '2025-12-13 10:04:06', '2025-12-13 14:44:41', '2025-12-13 14:44:42', NULL, NULL, NULL, 0.00, 0.00, 0.00, 0, NULL, NULL, 0, NULL, 0, 0, NULL, NULL, NULL, NULL, NULL),
(45, 105, '2025-12-18', '2025-12-18 11:21:46', '2025-12-18 15:22:47', NULL, NULL, NULL, NULL, 0.00, 0.00, 0.00, 0, NULL, NULL, 0, NULL, 0, 0, NULL, NULL, NULL, NULL, NULL),
(46, 107, '2025-12-18', '2025-12-18 11:21:58', NULL, NULL, NULL, NULL, NULL, 0.00, 0.00, 0.00, 0, NULL, NULL, 0, NULL, 0, 0, NULL, NULL, NULL, NULL, NULL),
(47, 109, '2025-12-18', '2025-12-18 11:22:11', NULL, NULL, NULL, NULL, NULL, 0.00, 0.00, 0.00, 0, NULL, NULL, 0, NULL, 0, 0, NULL, NULL, NULL, NULL, NULL),
(55, 105, '2025-12-19', NULL, NULL, '2025-12-19 19:00:53', '2025-12-19 19:02:07', '2025-12-19 19:02:07', NULL, 0.00, 0.00, 0.00, 0, NULL, 'đi some', 0, NULL, 0, 0, NULL, NULL, NULL, NULL, NULL),
(56, 106, '2025-12-19', NULL, NULL, NULL, NULL, NULL, NULL, 0.00, 0.00, 0.00, 0, NULL, 'tôi bị ngu', 0, NULL, 0, 0, NULL, NULL, NULL, NULL, NULL),
(57, 107, '2025-12-19', '2025-12-19 20:02:17', '2025-12-19 12:00:00', NULL, NULL, NULL, NULL, 0.00, 0.00, 0.00, 0, NULL, NULL, 0, NULL, 0, 0, NULL, NULL, NULL, NULL, NULL),
(58, 109, '2025-12-19', NULL, NULL, '2025-12-19 20:13:22', NULL, NULL, NULL, 0.00, 0.00, 0.00, 0, NULL, 'buồn!', 0, NULL, 0, 0, NULL, NULL, NULL, NULL, NULL),
(59, 105, '2025-12-20', '2025-12-20 10:53:55', '2025-12-20 12:00:00', '2025-12-20 14:34:13', NULL, NULL, NULL, 0.00, 0.00, 0.00, 0, NULL, NULL, 0, NULL, 1, 1, '2025-12-20 16:19:28', 'uploads/presence/presence_105_20251220161928.png', 'd998c4948d7c830fadb3d3cdba0ed98212c5da672a262e2534f975da3651d7cf', 'Moby, an American DJ, singer, and musician, achieved worldwide success for the 1999 release of which of the following albums?', '1234'),
(62, 107, '2025-12-20', '2025-12-20 11:11:23', '2025-12-20 12:00:00', NULL, NULL, NULL, NULL, 0.00, 0.00, 0.00, 0, NULL, NULL, 0, NULL, 0, 0, NULL, NULL, NULL, NULL, NULL),
(84, 109, '2025-12-20', NULL, NULL, '2025-12-20 14:34:00', NULL, NULL, NULL, 0.00, 0.00, 0.00, 0, NULL, 'đá gà', 0, NULL, 1, 1, '2025-12-20 17:57:02', 'uploads/presence/presence_109_20251220175702.png', '28776c08406c2195c27a79727c3994aa24cc353ee613fcb21fdd37065bbd0536', 'Which of these board games do NOT utilize standard 6-sided dice?', 'f***'),
(91, 106, '2025-12-20', NULL, NULL, '2025-12-20 15:19:59', NULL, NULL, NULL, 0.00, 0.00, 0.00, 0, NULL, 'tôi bị bạn gái thịt', 0, NULL, 0, 0, NULL, NULL, NULL, NULL, NULL),
(92, 107, '2025-12-21', NULL, NULL, '2025-12-21 15:46:28', '2025-12-21 23:59:01', '2025-12-21 18:10:39', '2025-12-21 23:49:34', 2.23, 5.65, 7.88, 1, 'No realtime presence confirmation', 'chịch trai', 0, NULL, 0, 0, NULL, NULL, NULL, NULL, NULL),
(93, 101, '2025-12-21', NULL, NULL, '2025-12-21 15:47:19', '2025-12-21 23:59:01', '2025-12-21 18:17:34', '2025-12-21 23:59:00', 2.21, 5.69, 7.90, 1, 'No realtime presence confirmation', 'ngủ quên', 0, NULL, 0, 0, NULL, NULL, NULL, NULL, NULL),
(94, 111, '2025-12-21', NULL, NULL, '2025-12-21 15:47:36', '2025-12-21 23:59:01', '2025-12-21 18:16:28', '2025-12-21 23:59:00', 2.21, 5.71, 7.92, 1, 'No realtime presence confirmation', 'weitei', 0, NULL, 0, 0, NULL, NULL, NULL, NULL, NULL),
(95, 102, '2025-12-21', NULL, NULL, '2025-12-21 15:48:01', '2025-12-21 18:00:00', NULL, NULL, 2.20, 0.00, 2.20, 1, NULL, 'bay sâu quá', 0, NULL, 0, 0, NULL, NULL, NULL, NULL, NULL),
(96, 112, '2025-12-21', NULL, NULL, '2025-12-21 15:48:28', '2025-12-21 23:59:01', '2025-12-21 18:17:50', '2025-12-21 23:50:41', 2.19, 5.55, 7.74, 1, 'No realtime presence confirmation', 'đu idol', 0, NULL, 0, 0, NULL, NULL, NULL, NULL, NULL),
(97, 108, '2025-12-21', NULL, NULL, '2025-12-21 16:27:06', '2025-12-21 17:44:20', NULL, NULL, 1.29, 0.00, 1.29, 0, NULL, 'quên ngày đi làm', 0, NULL, 0, 0, NULL, NULL, NULL, NULL, NULL),
(98, 105, '2025-12-27', NULL, NULL, '2025-12-27 15:21:14', NULL, NULL, NULL, 0.00, 0.00, 0.00, 0, NULL, 'đi họp lớp', 0, NULL, 0, 0, NULL, NULL, NULL, NULL, NULL);

--
-- Triggers `attendance`
--
DELIMITER $$
CREATE TRIGGER `update_fraud_flag` AFTER UPDATE ON `attendance` FOR EACH ROW BEGIN
    -- Kiểm tra nếu fraud_flag trong bảng attendance được cập nhật thành 1 (gian lận)
    IF NEW.fraud_flag = 1 THEN
        -- Cập nhật fraud_flag trong bảng employee cho nhân viên có emp_id tương ứng
        UPDATE employee
        SET fraud_flag = 1
        WHERE id = NEW.emp_id;
    END IF;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `attendance_evidence`
--

CREATE TABLE `attendance_evidence` (
  `id` int(11) NOT NULL,
  `attendance_id` int(11) NOT NULL,
  `image_path` varchar(255) NOT NULL,
  `image_hash` varchar(64) NOT NULL,
  `captured_at` datetime NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `attendance_evidence`
--

INSERT INTO `attendance_evidence` (`id`, `attendance_id`, `image_path`, `image_hash`, `captured_at`, `created_at`) VALUES
(1, 59, 'uploads/presence/presence_105_20251220161928.png', 'd998c4948d7c830fadb3d3cdba0ed98212c5da672a262e2534f975da3651d7cf', '2025-12-20 16:19:28', '2025-12-20 09:19:28'),
(2, 84, 'uploads/presence/presence_109_20251220172125.png', '7c7af96c43b4250c5dcbddf3057892d137764c167fea27fe6439cec7b74be406', '2025-12-20 17:21:25', '2025-12-20 10:21:25'),
(3, 84, 'uploads/presence/presence_109_20251220175702.png', '28776c08406c2195c27a79727c3994aa24cc353ee613fcb21fdd37065bbd0536', '2025-12-20 17:57:02', '2025-12-20 10:57:02');

-- --------------------------------------------------------

--
-- Table structure for table `attendance_logs`
--

CREATE TABLE `attendance_logs` (
  `id` int(11) NOT NULL,
  `emp_id` int(11) NOT NULL,
  `action_type` varchar(50) NOT NULL,
  `action_time` datetime NOT NULL,
  `source` text NOT NULL,
  `notes` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `attendance_logs`
--

INSERT INTO `attendance_logs` (`id`, `emp_id`, `action_type`, `action_time`, `source`, `notes`) VALUES
(1, 109, 'submitted_absence_reason', '2025-12-20 14:32:16', 'system', 'Submitted absence reason for morning'),
(59, 105, 'check_in_afternoon', '2025-12-20 14:34:13', 'user', 'Afternoon check-in'),
(84, 109, 'check_in_afternoon', '2025-12-20 14:34:00', 'user', 'Afternoon check-in after morning absence'),
(90, 106, 'submitted_absence_reason', '2025-12-20 15:19:56', 'system', 'Submitted absence reason for morning'),
(91, 106, 'check_in_afternoon', '2025-12-20 15:19:59', 'user', 'Afternoon check-in after morning absence'),
(92, 109, 'afternoon_presence_confirmed', '2025-12-20 17:57:02', 'user', 'Confirmed with photo and question'),
(93, 107, 'submitted_absence_reason', '2025-12-21 15:46:23', 'system', 'Submitted absence reason for morning'),
(94, 107, 'check_in_afternoon', '2025-12-21 15:46:28', 'user', 'Afternoon check-in after morning absence'),
(95, 101, 'submitted_absence_reason', '2025-12-21 15:47:16', 'system', 'Submitted absence reason for morning'),
(96, 101, 'check_in_afternoon', '2025-12-21 15:47:19', 'user', 'Afternoon check-in after morning absence'),
(97, 111, 'submitted_absence_reason', '2025-12-21 15:47:33', 'system', 'Submitted absence reason for morning'),
(98, 111, 'check_in_afternoon', '2025-12-21 15:47:36', 'user', 'Afternoon check-in after morning absence'),
(99, 102, 'submitted_absence_reason', '2025-12-21 15:47:58', 'system', 'Submitted absence reason for morning'),
(100, 102, 'check_in_afternoon', '2025-12-21 15:48:01', 'user', 'Afternoon check-in after morning absence'),
(101, 112, 'submitted_absence_reason', '2025-12-21 15:48:25', 'system', 'Submitted absence reason for morning'),
(102, 112, 'check_in_afternoon', '2025-12-21 15:48:28', 'user', 'Afternoon check-in after morning absence'),
(103, 108, 'submitted_absence_reason', '2025-12-21 16:27:02', 'system', 'Submitted absence reason for morning'),
(104, 108, 'check_in_afternoon', '2025-12-21 16:27:06', 'user', 'Afternoon check-in after morning absence'),
(105, 108, 'check_out_evening', '2025-12-21 17:44:20', 'user', 'Evening check-out'),
(106, 107, 'auto_checkout_evening', '2025-12-21 18:00:00', 'cron', 'Cron ép checkout 18:00'),
(107, 101, 'auto_checkout_evening', '2025-12-21 18:00:00', 'cron', 'Cron ép checkout 18:00'),
(108, 111, 'auto_checkout_evening', '2025-12-21 18:00:00', 'cron', 'Cron ép checkout 18:00'),
(109, 102, 'auto_checkout_evening', '2025-12-21 18:00:00', 'cron', 'Cron ép checkout 18:00'),
(110, 112, 'auto_checkout_evening', '2025-12-21 18:00:00', 'cron', 'Cron ép checkout 18:00'),
(111, 107, 'check_in_ot', '2025-12-21 18:10:39', 'user', 'OT start'),
(112, 111, 'check_in_ot', '2025-12-21 18:16:28', 'user', 'OT start'),
(113, 101, 'check_in_ot', '2025-12-21 18:17:34', 'user', 'OT start'),
(114, 112, 'check_in_ot', '2025-12-21 18:17:50', 'user', 'OT start'),
(115, 107, 'check_out_ot', '2025-12-21 23:49:34', 'user', 'OT end'),
(116, 112, 'check_out_ot', '2025-12-21 23:50:41', 'user', 'OT end'),
(117, 101, 'auto_checkout_ot', '2025-12-21 23:59:01', 'cron', 'Cron đóng OT 23:59'),
(118, 111, 'auto_checkout_ot', '2025-12-21 23:59:01', 'cron', 'Cron đóng OT 23:59'),
(119, 107, 'fraud_detected', '2025-12-21 23:59:01', 'cron', 'No realtime presence confirmation'),
(120, 101, 'fraud_detected', '2025-12-21 23:59:01', 'cron', 'No realtime presence confirmation'),
(121, 111, 'fraud_detected', '2025-12-21 23:59:01', 'cron', 'No realtime presence confirmation'),
(122, 112, 'fraud_detected', '2025-12-21 23:59:01', 'cron', 'No realtime presence confirmation'),
(123, 105, 'submitted_absence_reason', '2025-12-27 15:21:11', 'system', 'Submitted absence reason for morning'),
(124, 105, 'check_in_afternoon', '2025-12-27 15:21:14', 'user', 'Afternoon check-in after morning absence');

-- --------------------------------------------------------

--
-- Table structure for table `attendance_policy`
--

CREATE TABLE `attendance_policy` (
  `id` int(11) NOT NULL DEFAULT 1,
  `morning_checkin_start` time DEFAULT NULL,
  `morning_checkin_end` time DEFAULT NULL,
  `afternoon_checkin_start` time DEFAULT NULL,
  `lunch_auto_checkout` time DEFAULT NULL,
  `evening_auto_checkout` time DEFAULT NULL,
  `ot_auto_checkout` time DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `attendance_policy`
--

INSERT INTO `attendance_policy` (`id`, `morning_checkin_start`, `morning_checkin_end`, `afternoon_checkin_start`, `lunch_auto_checkout`, `evening_auto_checkout`, `ot_auto_checkout`) VALUES
(1, '07:45:00', '11:59:00', '13:00:00', '12:00:00', '18:00:00', '23:59:00');

-- --------------------------------------------------------

--
-- Table structure for table `banners`
--

CREATE TABLE `banners` (
  `id` int(11) NOT NULL,
  `banner_title` varchar(255) DEFAULT NULL,
  `image_url` varchar(255) DEFAULT NULL,
  `is_visible` tinyint(1) NOT NULL DEFAULT 1,
  `is_active` tinyint(1) DEFAULT 0,
  `active_order` bigint(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `banners`
--

INSERT INTO `banners` (`id`, `banner_title`, `image_url`, `is_visible`, `is_active`, `active_order`) VALUES
(1, 'Trumpet', 'uploads/banners/1764950922_banner1.jpg', 1, 1, 4),
(4, 'XYXX', 'uploads/banners/1764954262_banner3.jpg', 1, 0, NULL),
(5, 'Milk', 'uploads/banners/1764959624_banner4.jpg', 0, 0, NULL),
(6, 'My Bro...', 'uploads/banners/1765051550_banner.jpg', 1, 0, NULL),
(7, 'WTF???', 'uploads/banners/1765087222_banner.jpg', 1, 1, 3),
(8, 'Ố là la', 'uploads/banners/1765088323_banner.jpeg', 1, 1, 5),
(9, 'SIUUUUUU', 'uploads/banners/1765096598_banner.jpg', 1, 1, 6),
(10, 'Bean', 'uploads/banners/1765120094_banner.jpeg', 1, 1, 1),
(12, 'Bố đời', 'uploads/banners/1765122848_banner.png', 1, 0, NULL),
(13, 'Beauty Nextdoor', 'uploads/banners/1765122910_banner.jpg', 0, 0, NULL),
(15, 'Pessi', 'uploads/banners/1765968846_pessi.png', 1, 0, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `employee`
--

CREATE TABLE `employee` (
  `id` int(11) NOT NULL,
  `firstName` varchar(100) NOT NULL,
  `lastName` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` text NOT NULL,
  `birthday` date NOT NULL,
  `gender` varchar(10) NOT NULL,
  `contact` varchar(20) NOT NULL,
  `nid` int(20) NOT NULL,
  `address` varchar(100) DEFAULT NULL,
  `dept` varchar(100) NOT NULL,
  `degree` varchar(100) NOT NULL,
  `pic` text NOT NULL,
  `fraud_flag` tinyint(4) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `employee`
--

INSERT INTO `employee` (`id`, `firstName`, `lastName`, `email`, `password`, `birthday`, `gender`, `contact`, `nid`, `address`, `dept`, `degree`, `pic`, `fraud_flag`) VALUES
(101, 'Mehadi', 'Hassan', 'mehadi@xyz.corp', '1234', '1994-04-04', 'Male', '01919', 12121, 'Razarbagh', 'IT', 'Head', 'images/no.jpg', 0),
(102, 'Test', 'Pilots', 'testpilot@gmail.com', '1234', '2018-01-01', 'Male', '0202', 303, 'Ad_______', 'CSE', 'CSE', 'images/no.jpg', 1),
(103, 'Steven', 'Wilson', 'wilson@xyz.corp', '1234', '1990-02-02', 'Male', '5252', 6262, 'Thames, UK', 'Creative', 'MSc', 'images/sw-google.png', 0),
(104, 'Guthrie', 'Govan', 'guthrie@xyz.corp', '1234', '1971-12-01', 'Male', '9595', 5959, 'Chemsford, USA', 'Creative', 'MSc', 'images/test.jpg', 0),
(105, 'Elon', 'Musk', 'elon@spacex.com', '1234', '1971-06-28', 'Male', '8585', 5858, 'LA, USA', 'SpaceTech', 'BSc', 'images/330px-Elon_Musk_Royal_Society.jpg', 0),
(106, 'Hacker', 'Man', 'hackerman@xyz.corp', '1234', '1990-02-02', 'Male', '7575', 5757, 'Underground, Dhaka', 'NetworkSecurity', 'MSc', 'images/hacker.png', 0),
(107, 'Wonder ', 'Woman', 'woman@xyz.corp', '1234', '1993-03-03', 'Female', '4545', 5454, 'USA', 'Defense ', 'MS', 'images/no.jpg', 0),
(108, 'Andrew', ' Ng', 'andrew@xyz.corp', '1234', '1976-04-16', 'Male', '758758', 857857, 'USA', 'AI', 'PhD', 'images/download.jpeg', 0),
(109, 'Ian ', 'Goodfellow', 'ian@xyz.corp', '1234', '1985-01-01', 'Male', '852852', 258258, 'USA', 'AI', 'PhD', 'images/1-5.jpg', 0),
(110, 'Christopher ', 'Manning', 'christopher@xyz.corp', '1234', '1965-09-18', 'Male', '147147', 741741, 'USA', 'NLP', 'PhD', 'images/download (1).jpeg', 0),
(111, 'Jon', 'Snow', 'john@xyz.corp', '1234', '2011-02-01', 'Male', '0187282', 112233, 'Winterfell', 'Management', 'BSc.', 'images/jon-snow.jpg', 0),
(112, 't1noo', 'choCo', 'dark.eden007@gmail.com', '12', '1991-10-01', 'Male', '0902150809', 6868, '36 ngo 325 Kim Nguu', 'IT', 'CSE', 'images/t1noo.jpg', 0);

-- --------------------------------------------------------

--
-- Table structure for table `employee_leave`
--

CREATE TABLE `employee_leave` (
  `id` int(11) DEFAULT NULL,
  `token` int(11) NOT NULL,
  `start` date DEFAULT NULL,
  `end` date DEFAULT NULL,
  `reason` char(100) DEFAULT NULL,
  `status` char(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `employee_leave`
--

INSERT INTO `employee_leave` (`id`, `token`, `start`, `end`, `reason`, `status`) VALUES
(101, 301, '2019-04-07', '2019-04-08', 'Sick Leave', 'Approved'),
(102, 305, '2019-04-07', '2019-04-08', 'Urgent Family Cause', 'Approved'),
(103, 306, '2019-04-08', '2019-04-08', 'Concert Tour', 'Approved'),
(101, 307, '2019-04-14', '2019-04-30', 'Want to see GOT', 'Pending'),
(105, 308, '2019-04-26', '2019-04-30', 'Launching Tesla Model Y', 'Pending'),
(111, 309, '2019-04-09', '2019-04-13', 'Visit to Kings Landing', 'Pending'),
(104, 310, '2019-04-08', '2019-04-09', 'Emergency Leave', 'Pending');

-- --------------------------------------------------------

--
-- Table structure for table `project`
--

CREATE TABLE `project` (
  `pid` int(11) NOT NULL,
  `eid` int(11) DEFAULT NULL,
  `pname` varchar(100) DEFAULT NULL,
  `duedate` date DEFAULT NULL,
  `subdate` date DEFAULT '0000-00-00',
  `mark` int(11) NOT NULL,
  `status` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `project`
--

INSERT INTO `project` (`pid`, `eid`, `pname`, `duedate`, `subdate`, `mark`, `status`) VALUES
(213, 101, 'Database', '2019-04-07', '2019-04-04', 10, 'Submitted'),
(214, 102, 'Test', '2019-04-10', '0000-00-00', 0, 'Due'),
(215, 105, 'Tesla Model Y', '2019-04-19', '2019-04-06', 10, 'Submitted'),
(216, 106, 'Hack', '2019-05-04', '2019-04-05', 5, 'Submitted'),
(217, 111, 'Do Nothing', '2019-04-02', '2019-04-01', 8, 'Submitted'),
(218, 105, 'Tesla Model X', '2019-04-03', '2019-04-03', 10, 'Submitted'),
(219, 101, 'PHP', '2019-04-07', '0000-00-00', 0, 'Due'),
(220, 110, 'Data Analysis', '2019-04-16', '2019-04-04', 8, 'Submitted'),
(221, 110, 'Data Analysis', '2019-04-16', '2019-04-04', 7, 'Submitted'),
(222, 103, 'Statistical', '2019-04-19', '2019-04-04', 6, 'Submitted'),
(223, 108, 'Software Scema', '2019-04-09', '2019-04-02', 3, 'Submitted'),
(224, 107, 'Security Check', '2019-04-26', '2019-04-05', 9, 'Submitted'),
(225, 109, 'ML', '2019-04-03', '2019-04-04', 6, 'Submitted');

-- --------------------------------------------------------

--
-- Table structure for table `rank`
--

CREATE TABLE `rank` (
  `eid` int(11) NOT NULL,
  `points` int(11) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `rank`
--

INSERT INTO `rank` (`eid`, `points`) VALUES
(101, 10),
(102, 0),
(103, 6),
(104, 0),
(105, 20),
(106, 5),
(107, 9),
(108, 3),
(109, 6),
(110, 15),
(111, 8),
(112, 0);

-- --------------------------------------------------------

--
-- Table structure for table `salary`
--

CREATE TABLE `salary` (
  `id` int(11) NOT NULL,
  `base` int(11) NOT NULL,
  `bonus` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `salary`
--

INSERT INTO `salary` (`id`, `base`, `bonus`) VALUES
(101, 55000, 20),
(102, 16500, 5),
(103, 65000, 6),
(104, 78000, 0),
(105, 105000, 20),
(106, 60000, 5),
(107, 77000, 9),
(108, 50000, 4),
(109, 85000, 6),
(110, 47000, 15),
(111, 45000, 8),
(112, 1000000, 0);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `alogin`
--
ALTER TABLE `alogin`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `attendance`
--
ALTER TABLE `attendance`
  ADD PRIMARY KEY (`id`),
  ADD KEY `emp_id` (`emp_id`),
  ADD KEY `idx_morning_presence` (`morning_presence_confirmed`,`work_date`),
  ADD KEY `idx_afternoon_presence` (`afternoon_presence_confirmed`,`work_date`);

--
-- Indexes for table `attendance_evidence`
--
ALTER TABLE `attendance_evidence`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_attendance` (`attendance_id`),
  ADD KEY `idx_captured_at` (`captured_at`);

--
-- Indexes for table `attendance_logs`
--
ALTER TABLE `attendance_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_emp_id` (`emp_id`),
  ADD KEY `idx_action_time` (`action_time`);

--
-- Indexes for table `attendance_policy`
--
ALTER TABLE `attendance_policy`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `banners`
--
ALTER TABLE `banners`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `employee`
--
ALTER TABLE `employee`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `employee_leave`
--
ALTER TABLE `employee_leave`
  ADD PRIMARY KEY (`token`),
  ADD KEY `employee_leave_ibfk_1` (`id`);

--
-- Indexes for table `project`
--
ALTER TABLE `project`
  ADD PRIMARY KEY (`pid`),
  ADD KEY `project_ibfk_1` (`eid`);

--
-- Indexes for table `rank`
--
ALTER TABLE `rank`
  ADD PRIMARY KEY (`eid`);

--
-- Indexes for table `salary`
--
ALTER TABLE `salary`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `alogin`
--
ALTER TABLE `alogin`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `attendance`
--
ALTER TABLE `attendance`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=99;

--
-- AUTO_INCREMENT for table `attendance_evidence`
--
ALTER TABLE `attendance_evidence`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `attendance_logs`
--
ALTER TABLE `attendance_logs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=125;

--
-- AUTO_INCREMENT for table `banners`
--
ALTER TABLE `banners`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `employee`
--
ALTER TABLE `employee`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=114;

--
-- AUTO_INCREMENT for table `employee_leave`
--
ALTER TABLE `employee_leave`
  MODIFY `token` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=311;

--
-- AUTO_INCREMENT for table `project`
--
ALTER TABLE `project`
  MODIFY `pid` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=226;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `attendance`
--
ALTER TABLE `attendance`
  ADD CONSTRAINT `attendance_ibfk_1` FOREIGN KEY (`emp_id`) REFERENCES `employee` (`id`);

--
-- Constraints for table `attendance_evidence`
--
ALTER TABLE `attendance_evidence`
  ADD CONSTRAINT `attendance_evidence_ibfk_1` FOREIGN KEY (`attendance_id`) REFERENCES `attendance` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `attendance_logs`
--
ALTER TABLE `attendance_logs`
  ADD CONSTRAINT `fk_attendanceLogs_empId` FOREIGN KEY (`emp_id`) REFERENCES `employee` (`id`);

--
-- Constraints for table `employee_leave`
--
ALTER TABLE `employee_leave`
  ADD CONSTRAINT `employee_leave_ibfk_1` FOREIGN KEY (`id`) REFERENCES `employee` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `project`
--
ALTER TABLE `project`
  ADD CONSTRAINT `project_ibfk_1` FOREIGN KEY (`eid`) REFERENCES `employee` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `rank`
--
ALTER TABLE `rank`
  ADD CONSTRAINT `rank_ibfk_1` FOREIGN KEY (`eid`) REFERENCES `employee` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `salary`
--
ALTER TABLE `salary`
  ADD CONSTRAINT `salary_ibfk_1` FOREIGN KEY (`id`) REFERENCES `employee` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
