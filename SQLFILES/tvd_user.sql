-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Feb 08, 2023 at 06:13 PM
-- Server version: 10.4.24-MariaDB
-- PHP Version: 8.1.6

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `tvd_user`
--

-- --------------------------------------------------------

--
-- Table structure for table `followings`
--

CREATE TABLE `followings` (
  `followed_by` int(11) NOT NULL,
  `followed_to` int(11) NOT NULL,
  `follow_time` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `followings`
--

INSERT INTO `followings` (`followed_by`, `followed_to`, `follow_time`) VALUES
(1, 1, '2022-11-04 09:47:10'),
(11, 1, '2022-12-13 16:42:48'),
(1, 11, '2023-01-31 09:10:49'),
(1, 12, '2023-02-02 12:00:45');

-- --------------------------------------------------------

--
-- Table structure for table `logins`
--

CREATE TABLE `logins` (
  `session_id` varchar(50) NOT NULL,
  `gmail` varchar(50) NOT NULL,
  `verified` tinyint(1) NOT NULL DEFAULT 0,
  `code` int(11) NOT NULL,
  `code_time` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `location` varchar(50) NOT NULL,
  `ip_address` varchar(50) NOT NULL,
  `device` varchar(200) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `logins`
--

INSERT INTO `logins` (`session_id`, `gmail`, `verified`, `code`, `code_time`, `location`, `ip_address`, `device`) VALUES
('BDgu', 'gondalshoaib3333@gmail.com', 1, 12345, '2022-11-30 16:08:51', '', '', ''),
('CxuH', 'gondalshoaib3333@gmail.com', 1, 12345, '2022-12-23 06:29:32', '', '', ''),
('dNjN', 'gondalshoaib3333@gmail.com', 1, 12345, '2023-02-02 11:56:46', '', '', ''),
('e`[t', 'malikusamanawaz852@gmail.com', 1, 12345, '2022-12-03 04:37:53', '', '', ''),
('fh_y', 'gondalshoaib3333@gmail.com', 1, 12345, '2022-12-26 06:57:01', '', '', ''),
('Fi_e', 'malikusamanawaz852@gmail.com', 1, 12333, '2022-10-11 06:25:19', '', '', ''),
('F`a]', 'ahsanbaloch514@gmail.com', 1, 12333, '2022-10-18 18:42:35', '', '', ''),
('GLpZ', 'malikusamanawaz852@gmail.com', 1, 12333, '2022-10-11 06:23:05', '', '', ''),
('g[oH', 'gondalshoaib3333@gmail.com', 1, 12345, '2022-12-23 06:34:11', '', '', ''),
('HOAB', 'gondalshoaib3333@gmail.com', 1, 82427, '2022-08-17 12:25:55', '', '', ''),
('Ie`I', 'gondalshoaib3333@gmail.com', 1, 12345, '2022-12-03 04:38:43', '', '', ''),
('JJJv', 'gondalshoaib3333@gmail.com', 1, 48828, '2022-10-03 14:32:19', '', '', ''),
('jZWH', 'gondalshoaib3333@gmail.com', 1, 97709, '2022-08-10 10:24:59', '', '', ''),
('Lhoh', 'gondalshoaib3333@gmail.com', 1, 12345, '2022-10-10 07:45:38', '', '', ''),
('LUH`', 'gondalshoaib3333@gmail.com', 1, 12345, '2022-12-03 04:36:44', '', '', ''),
('mWqd', 'gondalshoaib3333@gmail.com', 1, 90512, '2022-10-04 07:10:01', '', '', ''),
('MwyM', 'malikusamanawaz852@gmail.com', 1, 12345, '2022-12-06 16:42:43', '', '', ''),
('npid', 'gondalshoaib3333@gmail.com', 1, 12345, '2022-10-18 12:32:54', '', '', ''),
('Np`b', 'gondalshoaib3333@gmail.com', 1, 12345, '2022-10-18 04:54:24', '', '', ''),
('oENr', 'gondalshoaib3333@gmail.com', 1, 12345, '2022-12-23 06:30:06', '', '', ''),
('oOTg', 'gondalshoaib3333@gmail.com', 1, 52514, '2022-08-24 07:39:17', '', '', ''),
('oXrj', 'gondalshoaib3333@gmail.com', 1, 74499, '2022-08-23 05:01:44', '', '', ''),
('pCRP', 'gondalshoaib3333@gmail.com', 1, 12345, '2022-10-18 15:57:42', '', '', ''),
('PsMh', 'gondalshoaib3333@gmail.com', 1, 12345, '2022-11-30 16:17:16', '', '', ''),
('QOgD', 'gondalshoaib3333@gmail.com', 1, 54204, '2022-10-03 07:06:50', '', '', ''),
('REpD', 'gondalshoaib3333@gmail.com', 1, 12345, '2022-10-18 04:33:24', '', '', ''),
('SAH_', 'gondalshoaib3333@gmail.com', 1, 12345, '2022-10-22 10:27:55', '', '', ''),
('ssb^', 'gondalshoaib3333@gmail.com', 1, 12345, '2022-10-16 09:23:37', '', '', ''),
('S[vU', 'gondalshoaib3333@gmail.com', 1, 65657, '2022-08-10 10:27:12', '', '', ''),
('s^IW', 'gondalshoaib3333@gmail.com', 1, 12345, '2022-10-04 16:32:37', '', '', ''),
('S^Qv', 'gondalshoaib3333@gmail.com', 1, 12345, '2022-12-26 11:15:53', '', '', ''),
('TIlK', 'gondalshoaib3332@gmail.com', 1, 12333, '2022-08-10 06:33:42', '', '', ''),
('tIsX', 'gondalshoaib3333@gmail.com', 1, 67484, '2022-08-17 12:49:01', '', '', ''),
('T_Ml', 'gondalshoaib3333@gmail.com', 1, 12345, '2023-01-02 10:59:29', '', '', ''),
('UAK_', 'gondalshoaib3333@gmail.com', 1, 79896, '2022-10-04 07:13:27', '', '', ''),
('UDxy', 'gondalshoaib3333@gmail.com', 1, 12345, '2022-11-04 10:56:41', '', '', ''),
('uHqW', 'malikusamanawaz852@gmail.com', 1, 12333, '2022-10-11 06:23:05', '', '', ''),
('uvBF', 'gondalshoaib3333@gmail.com', 1, 12345, '2022-10-31 17:44:03', '', '', ''),
('u_SL', 'gondalshoaib3333@gmail.com', 1, 12345, '2022-12-26 11:47:27', '', '', ''),
('vcLJ', 'gondalshoaib3333@gmail.com', 1, 60399, '2022-08-10 10:58:30', '', '', ''),
('vUTK', 'gondalshoaib3333@gmail.com', 1, 12345, '2022-10-25 14:40:51', '', '', ''),
('v_Xy', 'gondalshoaib3333@gmail.com', 1, 12345, '2022-10-04 16:22:36', '', '', ''),
('wdt', 'gondalshoaib3333@gmail.com', 1, 12345, '2022-12-09 15:53:32', '', '', ''),
('Xezj', 'gondalshoaib3333@gmail.com', 1, 12345, '2022-10-04 16:21:19', '', '', ''),
('Xxdt', 'gondalshoaib3333@gmail.com', 1, 12345, '2022-10-10 18:27:55', '', '', ''),
('YGjo', 'gondalshoaib3333@gmail.com', 1, 12345, '2022-12-09 12:45:59', '', '', ''),
('yksm', 'malikusamanawaz852@gmail.com', 1, 12345, '2022-10-11 06:25:50', '', '', ''),
('ZFDg', 'gondalshoaib3333@gmail.com', 1, 12345, '2023-01-17 12:46:39', '', '', ''),
('[]xV', 'gondalshoaib3333@gmail.com', 1, 12345, '2022-12-03 04:47:01', '', '', ''),
('^nze', 'malikusamanawaz852@gmail.com', 1, 12345, '2022-10-18 12:35:08', '', '', ''),
('_eXP', 'ahsanbaloch514@gmail.com', 1, 12345, '2022-10-18 18:44:07', '', '', ''),
('`kFa', 'malikusamanawaz852@gmail.com', 1, 12333, '2022-10-04 15:43:04', '', '', '');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `name` varchar(50) NOT NULL,
  `email` varchar(50) NOT NULL,
  `password` varchar(200) NOT NULL,
  `verified` tinyint(1) NOT NULL DEFAULT 0,
  `username` varchar(50) NOT NULL,
  `dp` varchar(50) NOT NULL,
  `description` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `password`, `verified`, `username`, `dp`, `description`) VALUES
(1, 'Shoaib', 'gondalshoaib3333@gmail.com', '5434809aH$', 1, 'shoaib5437', '1shoaib5437.jpeg', 'Solve the cause, not the Symptom !!'),
(11, 'Usama Nawaz', 'malikusamanawaz852@gmail.com', '5434809aH$', 1, 'usama_nawaz', '11usama_nawaz.jpg', 'Live The Moment 😜'),
(12, 'Ahsan Baloch', 'ahsanbaloch514@gmail.com', '5434809aH$', 1, 'ahsan_baloch', '12ahsan_baloch.jpeg', '');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `logins`
--
ALTER TABLE `logins`
  ADD PRIMARY KEY (`session_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
