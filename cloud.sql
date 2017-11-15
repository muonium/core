-- phpMyAdmin SQL Dump
-- version 4.5.4.1
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Aug 24, 2017 at 10:40 PM
-- Server version: 5.7.11
-- PHP Version: 5.6.18

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `cloud`
--

-- --------------------------------------------------------

--
-- Table structure for table `ban`
--

CREATE TABLE `ban` (
  `id` int(11) NOT NULL,
  `id_user` int(11) NOT NULL,
  `reason` varchar(128) NOT NULL,
  `duration` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `files`
--

CREATE TABLE `files` (
  `id` bigint(20) NOT NULL,
  `id_owner` int(11) NOT NULL,
  `folder_id` bigint(20) NOT NULL,
  `name` varchar(128) NOT NULL,
  `size` bigint(20) NOT NULL,
  `last_modification` int(11) NOT NULL,
  `favorite` tinyint(1) NOT NULL,
  `trash` tinyint(1) NOT NULL,
  `expires` int(11) DEFAULT NULL,
  `dk` varchar(330) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `folders`
--

CREATE TABLE `folders` (
  `id` bigint(20) NOT NULL,
  `id_owner` int(11) NOT NULL,
  `name` varchar(128) NOT NULL,
  `size` bigint(20) NOT NULL,
  `parent` bigint(20) NOT NULL,
  `trash` tinyint(1) NOT NULL,
  `path` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `storage`
--

CREATE TABLE `storage` (
  `id` int(11) NOT NULL,
  `id_user` int(11) NOT NULL,
  `user_quota` bigint(20) NOT NULL,
  `size_stored` bigint(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `storage_plans`
--

CREATE TABLE `storage_plans` (
  `id` int(11) NOT NULL,
  `size` bigint(20) NOT NULL,
  `price` float NOT NULL,
  `currency` varchar(10) DEFAULT NULL,
  `duration` int(11) NOT NULL,
  `product_id` varchar(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `upgrade`
--

CREATE TABLE `upgrade` (
  `id` int(11) NOT NULL,
  `id_user` int(11) NOT NULL,
  `txn_id` varchar(64) NOT NULL,
  `size` bigint(20) NOT NULL,
  `price` float NOT NULL,
  `currency` varchar(10) DEFAULT NULL,
  `start` int(11) NOT NULL,
  `end` int(11) NOT NULL,
  `removed` tinyint(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `login` varchar(20) NOT NULL,
  `password` varchar(128) NOT NULL,
  `email` varchar(254) NOT NULL,
  `registration_date` int(11) NOT NULL,
  `last_connection` int(11) NOT NULL,
  `cek` varchar(330) NOT NULL,
  `double_auth` tinyint(1) NOT NULL,
  `auth_code` varchar(8) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `user_lostpass`
--

CREATE TABLE `user_lostpass` (
  `id` int(11) NOT NULL,
  `id_user` int(11) NOT NULL,
  `val_key` varchar(128) NOT NULL,
  `expire` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `user_validation`
--

CREATE TABLE `user_validation` (
  `id` int(11) NOT NULL,
  `id_user` int(11) NOT NULL,
  `val_key` varchar(128) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `ban`
--
ALTER TABLE `ban`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `files`
--
ALTER TABLE `files`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `folders`
--
ALTER TABLE `folders`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `storage`
--
ALTER TABLE `storage`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `storage_plans`
--
ALTER TABLE `storage_plans`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `upgrade`
--
ALTER TABLE `upgrade`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `txn_id` (`txn_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `user_lostpass`
--
ALTER TABLE `user_lostpass`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `user_validation`
--
ALTER TABLE `user_validation`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `ban`
--
ALTER TABLE `ban`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `files`
--
ALTER TABLE `files`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=449;
--
-- AUTO_INCREMENT for table `folders`
--
ALTER TABLE `folders`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `storage`
--
ALTER TABLE `storage`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;
--
-- AUTO_INCREMENT for table `storage_plans`
--
ALTER TABLE `storage_plans`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
--
-- AUTO_INCREMENT for table `upgrade`
--
ALTER TABLE `upgrade`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;
--
-- AUTO_INCREMENT for table `user_lostpass`
--
ALTER TABLE `user_lostpass`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `user_validation`
--
ALTER TABLE `user_validation`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
