-- phpMyAdmin SQL Dump
-- version 2.11.6
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Jul 09, 2010 at 04:50 PM
-- Server version: 5.1.48
-- PHP Version: 5.3.1

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: 'officefinder'
--

-- --------------------------------------------------------

--
-- Table structure for table 'departments'
--

CREATE TABLE IF NOT EXISTS departments (
  id int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  org_unit varchar(45) COLLATE utf8_unicode_ci DEFAULT NULL,
  building varchar(45) COLLATE utf8_unicode_ci DEFAULT NULL,
  room varchar(45) COLLATE utf8_unicode_ci DEFAULT NULL,
  city varchar(45) COLLATE utf8_unicode_ci DEFAULT NULL,
  state varchar(45) COLLATE utf8_unicode_ci DEFAULT NULL,
  postal_code varchar(45) COLLATE utf8_unicode_ci DEFAULT NULL,
  address varchar(45) COLLATE utf8_unicode_ci DEFAULT NULL,
  phone varchar(45) COLLATE utf8_unicode_ci DEFAULT NULL,
  fax varchar(45) COLLATE utf8_unicode_ci DEFAULT NULL,
  email varchar(45) COLLATE utf8_unicode_ci DEFAULT NULL,
  website varchar(45) COLLATE utf8_unicode_ci DEFAULT NULL,
  acronym varchar(45) COLLATE utf8_unicode_ci DEFAULT NULL,
  alternate varchar(45) COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (id),
  UNIQUE KEY name_UNIQUE (`name`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table 'department_permissions'
--

CREATE TABLE IF NOT EXISTS department_permissions (
  department_id int(11) NOT NULL,
  uid varchar(45) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (department_id,uid)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table 'listings'
--

CREATE TABLE IF NOT EXISTS listings (
  id int(11) NOT NULL AUTO_INCREMENT,
  department_id int(11) NOT NULL,
  `name` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  phone varchar(45) COLLATE utf8_unicode_ci DEFAULT NULL,
  sort int(11) DEFAULT NULL,
  address varchar(45) COLLATE utf8_unicode_ci DEFAULT NULL,
  email varchar(45) COLLATE utf8_unicode_ci DEFAULT NULL,
  uid varchar(45) COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (id)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table 'listing_permissions'
--

CREATE TABLE IF NOT EXISTS listing_permissions (
  listing_id int(11) NOT NULL,
  uid varchar(45) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (listing_id,uid)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table 'listing_uid'
--

CREATE TABLE IF NOT EXISTS listing_uid (
  listing_id int(11) NOT NULL,
  uid varchar(45) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (listing_id,uid)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table 'telecom_departments'
--

CREATE TABLE IF NOT EXISTS telecom_departments (
  lGroup_id bigint(20) DEFAULT NULL,
  tiSplit varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  sLstTyp varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  sLstSty varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  iSeqNbr int(11) DEFAULT NULL,
  tiIndDrg varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  sNPA1 varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  sNPA2 varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  sNPA3 varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  sPhoneNbr1 varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  sPhoneNbr2 varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  sPhoneNbr3 varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  sExtension1 varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  sExtension2 varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  szLname varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  szFname varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  szAddtText varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  szAddress varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  szDepartmentalAddress varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  szLocName varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  szStateName varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  sZipCd5 varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  sZipCd4 varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  szDirLname varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  szDirFname varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  szDirAddText varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  mFreeFormText varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  szNonStdPh varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
