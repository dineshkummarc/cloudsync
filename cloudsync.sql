-- phpMyAdmin SQL Dump
-- version 2.11.4
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Mar 05, 2015 at 07:29 AM
-- Server version: 5.1.57
-- PHP Version: 5.2.17

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";

--
-- Database: `a2381334_project`
--

-- --------------------------------------------------------

--
-- Table structure for table `clouds`
--

CREATE TABLE `clouds` (
  `id` int(10) NOT NULL,
  `cloudnum` int(10) NOT NULL,
  `username` varchar(100) NOT NULL,
  `password` varchar(100) NOT NULL,
  `cloudname` varchar(100) NOT NULL,
  `url` varchar(300) NOT NULL,
  `number` int(10) NOT NULL AUTO_INCREMENT,
  PRIMARY KEY (`number`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=4 ;

--
-- Dumping data for table `clouds`
--

INSERT INTO `clouds` VALUES(1, 2, '', '', 'G Drive', '{"access_token":"ya29.1.AADtN_WB9Vfc7c84am8QQvUR1grGz0AfIJIaLUtJT27gmwAG_LwWqHd259B5R7BKL7eInQ","token_type":"Bearer","expires_in":3600,"refresh_token":"1\\/8-PIhkoy8bUAGO7zZK6XjtiOe3nG8d9Q0BGyNPlwi9w","created":1398112418}', 1);
INSERT INTO `clouds` VALUES(1, 1, 'shazvi@outlook.com', 'KÊqƒ¯}×€JR', 'Cubby', 'http://webdav.cubby.com', 2);
INSERT INTO `clouds` VALUES(2, 2, '', '', 'test', '', 3);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `username` varchar(50) NOT NULL,
  `hash` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `token` varchar(300) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`,`email`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=3 ;

--
-- Dumping data for table `users`
--

INSERT INTO `users` VALUES(1, 'shazvi', '$1$Z6ZwbZaz$AGyr3cqtrclSVbMR68O/Z1', 'shazvi@outlook.com', '');
INSERT INTO `users` VALUES(2, 'AzadiOS', '$1$8LutnhC6$hXA3brwC79BbHgQzCuUzX1', 'info@azadios.com', '');
