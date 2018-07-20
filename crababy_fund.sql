-- phpMyAdmin SQL Dump
-- version 4.7.9
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: 2018-07-20 17:17:00
-- 服务器版本： 5.7.20
-- PHP Version: 7.2.0RC2

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `crababy_fund`
--

-- --------------------------------------------------------

--
-- 表的结构 `fund`
--

CREATE TABLE `fund` (
  `id` int(11) NOT NULL,
  `fund_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '基金名称',
  `fund_code` varchar(32) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '基金代码',
  `fund_type` enum('股票型','混合型','债券型','指数型','ETF联接','QDII','LOF','FOF') COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '基金类型',
  `fund_scale` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '基金规模',
  `fund_data` date NOT NULL COMMENT '基金成立日期',
  `fund_manager` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '基金经理',
  `fund_admin` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '基金管理人',
  `fund_deposit` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '基金托管人',
  `addtime` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `fund`
--
ALTER TABLE `fund`
  ADD PRIMARY KEY (`id`);

--
-- 在导出的表使用AUTO_INCREMENT
--

--
-- 使用表AUTO_INCREMENT `fund`
--
ALTER TABLE `fund`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
