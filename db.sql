-- phpMyAdmin SQL Dump
-- version 4.7.3
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Mar 18, 2019 at 08:45 AM
-- Server version: 5.6.40-84.0-log
-- PHP Version: 5.6.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `kkliw201_mc-app`
--
CREATE DATABASE IF NOT EXISTS `mc-app` DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci;
USE `mc-app`;

-- --------------------------------------------------------

--
-- Table structure for table `files`
--

CREATE TABLE `files` (
  `file_id` int(11) NOT NULL,
  `message_id` int(11) NOT NULL,
  `original_name` varchar(255) NOT NULL,
  `modified_name` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;

-- --------------------------------------------------------

--
-- Table structure for table `message`
--

CREATE TABLE `message` (
  `id` int(11) NOT NULL,
  `from` int(11) NOT NULL,
  `to` int(11) NOT NULL,
  `type` varchar(255) NOT NULL DEFAULT '',
  `content` text NOT NULL,
  `send_date` datetime NOT NULL,
  `read_date` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;

--
-- Dumping data for table `message`
--

INSERT INTO `message` (`id`, `from`, `to`, `type`, `content`, `send_date`, `read_date`) VALUES
(8, 15, 4, 'text', 'Hello', '2018-12-13 09:30:21', NULL),
(9, 14, 4, 'text', 'xx', '2019-03-04 17:56:30', NULL),
(10, 14, 4, 'text', 'uploaded', '2019-03-04 17:56:51', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(10) UNSIGNED NOT NULL,
  `username` varchar(255) NOT NULL,
  `pwd` varchar(255) NOT NULL,
  `role` tinyint(1) NOT NULL DEFAULT '1',
  `status` datetime DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `pwd`, `role`, `status`) VALUES
(4, 'admin', '9b3ee0cb4d7be5f294e8eb38f234ae9f', 5, '2018-12-06 15:50:25'),
(5, 'steel', '9b3ee0cb4d7be5f294e8eb38f234ae9f', 5, '2019-03-04 15:25:42'),
(13, 'hideo', '1cc73e5086ba2d04eef6ee1448dc8bf0', 1, NULL),
(12, 'jincowboy9174', 'f12a97c4a3343cc0d3fca89ee5598eb3', 1, NULL),
(14, 'wang517', '38e4ccae04f577942b1221be3544c262', 1, '2019-03-04 18:20:33'),
(15, 'test12345', 'c06db68e819be6ec3d26c6038d8e8d1f', 1, '2018-12-20 19:01:53'),
(16, 'joe925', 'edf41505d87cb29dba893bd1edeffb12', 1, '2018-11-10 07:02:07'),
(17, 'wang', 'e08392bb89dedb8ed6fb298f8e729c15', 0, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `user_match`
--

CREATE TABLE `user_match` (
  `id1` int(10) NOT NULL,
  `id2` int(10) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8 ROW_FORMAT=FIXED;

--
-- Dumping data for table `user_match`
--

INSERT INTO `user_match` (`id1`, `id2`) VALUES
(16, 15);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `files`
--
ALTER TABLE `files`
  ADD PRIMARY KEY (`file_id`) USING BTREE;

--
-- Indexes for table `message`
--
ALTER TABLE `message`
  ADD PRIMARY KEY (`id`) USING BTREE;

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`) USING BTREE;

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `files`
--
ALTER TABLE `files`
  MODIFY `file_id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `message`
--
ALTER TABLE `message`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;
--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
