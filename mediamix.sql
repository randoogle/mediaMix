-- phpMyAdmin SQL Dump
-- version 3.4.10.1deb1
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Nov 22, 2012 at 11:10 AM
-- Server version: 5.5.28
-- PHP Version: 5.3.10-1ubuntu3.4

create database if not exists mediamix;
use mediamix;

grant all on `mediamix`.* to 'mediamix_user'@'localhost' identified by 'whatever';

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `mediamix`
--

-- --------------------------------------------------------

--
-- Table structure for table `genres`
--

DROP TABLE IF EXISTS `genres`;
CREATE TABLE IF NOT EXISTS `genres` (
  `genre_id` int(20) NOT NULL AUTO_INCREMENT,
  `genre_title` varchar(300) NOT NULL,
  PRIMARY KEY (`genre_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=31 ;

-- --------------------------------------------------------

--
-- Table structure for table `media_genre`
--

DROP TABLE IF EXISTS `media_genre`;
CREATE TABLE IF NOT EXISTS `media_genre` (
  `genre_id` int(20) NOT NULL,
  `media_item_id` int(100) NOT NULL,
  UNIQUE KEY `genre_id` (`genre_id`,`media_item_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

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

-- --------------------------------------------------------

--
-- Table structure for table `media_types`
--

DROP TABLE IF EXISTS `media_types`;
CREATE TABLE IF NOT EXISTS `media_types` (
  `media_type_id` int(10) NOT NULL AUTO_INCREMENT,
  `media_type_desc` varchar(128) NOT NULL,
  PRIMARY KEY (`media_type_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=5 ;

-- --------------------------------------------------------

--
-- Table structure for table `mediums`
--

DROP TABLE IF EXISTS `mediums`;
CREATE TABLE IF NOT EXISTS `mediums` (
  `medium_id` int(10) NOT NULL AUTO_INCREMENT,
  `medium_title` varchar(128) NOT NULL,
  `medium_image_location` varchar(256) DEFAULT NULL,
  PRIMARY KEY (`medium_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=6 ;

-- --------------------------------------------------------

--
-- Table structure for table `storage_locations`
--

DROP TABLE IF EXISTS `storage_locations`;
CREATE TABLE IF NOT EXISTS `storage_locations` (
  `storage_location_id` int(20) NOT NULL AUTO_INCREMENT,
  `storage_title` varchar(128) NOT NULL,
  `storage_description` varchar(128) DEFAULT NULL,
  `storage_slot_capacity` int(10) DEFAULT NULL,
  PRIMARY KEY (`storage_location_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=6 ;

-- --------------------------------------------------------

--
-- Table structure for table `storage_slots`
--

DROP TABLE IF EXISTS `storage_slots`;
CREATE TABLE IF NOT EXISTS `storage_slots` (
  `storage_slot_id` int(100) NOT NULL AUTO_INCREMENT,
  `storage_location_id` int(20) NOT NULL,
  `storage_slot_label` varchar(128) NOT NULL,
  PRIMARY KEY (`storage_slot_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=434 ;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
