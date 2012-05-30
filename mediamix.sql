-- phpMyAdmin SQL Dump
-- version 3.3.9
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: May 30, 2012 at 03:10 AM
-- Server version: 5.5.8
-- PHP Version: 5.3.5

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `mediamix`
--

-- --------------------------------------------------------

--
-- Table structure for table `media_items`
--

DROP TABLE IF EXISTS `media_items`;
CREATE TABLE IF NOT EXISTS `media_items` (
  `id` int(30) NOT NULL AUTO_INCREMENT,
  `title` varchar(128) NOT NULL COMMENT 'media title',
  `media_type_id` int(10) DEFAULT NULL,
  `length` time DEFAULT NULL,
  `image_location` varchar(300) DEFAULT NULL COMMENT 'thumbnail image location',
  `rating` int(2) DEFAULT NULL COMMENT 'custom rating 0-10',
  `notes` longtext,
  `medium_id` int(10) DEFAULT NULL,
  `barcode` bigint(12) DEFAULT NULL,
  `isbn` int(10) DEFAULT NULL,
  `storage_slot_id` int(100) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=93 ;
