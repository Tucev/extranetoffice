-- phpMyAdmin SQL Dump
-- version 2.11.3
-- http://www.phpmyadmin.net
--
-- Host: localhost:3306
-- Generation Time: Mar 27, 2009 at 09:03 PM
-- Server version: 5.0.51
-- PHP Version: 5.2.8

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";

--
-- Database: `extranetoffice_dist`
--

-- --------------------------------------------------------

--
-- Table structure for table `eo_acl_groups`
--

DROP TABLE IF EXISTS `eo_acl_groups`;
CREATE TABLE IF NOT EXISTS `eo_acl_groups` (
  `id` int(11) NOT NULL auto_increment,
  `groupid` int(11) NOT NULL,
  `option` varchar(50) NOT NULL,
  `task` varchar(32) NOT NULL,
  `view` varchar(32) NOT NULL,
  `layout` varchar(32) NOT NULL,
  `value` varchar(4) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

--
-- Dumping data for table `eo_acl_groups`
--

INSERT INTO `eo_acl_groups` (`id`, `groupid`, `option`, `task`, `view`, `layout`, `value`) VALUES
(1, 1, 'com_login', '*', '*', '*', 'all'),
(2, 2, 'com_login', '*', '*', '*', 'all'),
(3, 1, 'com_dashboard', '*', '*', '*', 'own'),
(4, 2, 'com_dashboard', '*', '*', '*', 'own'),
(5, 1, 'com_email', '*', '*', '*', 'own'),
(6, 2, 'com_email', '*', '*', '*', 'own'),
(7, 1, 'com_addressbook', '*', '*', '*', 'own'),
(8, 2, 'com_addressbook', '*', '*', '*', 'own'),
(9, 1, 'com_projects', '*', '*', '*', 'all'),
(10, 2, 'com_projects', '*', '*', '*', 'own'),
(11, 2, 'com_users', '*', '*', '*', 'own'),
(12, 3, 'com_users', '*', '*', '*', 'own'),
(13, 4, 'com_users', '*', '*', '*', 'own'),
(14, 0, 'com_login', '*', '*', '*', 'own'),
(15, 3, 'com_login', '*', '*', '*', 'own'),
(16, 4, 'com_login', '*', '*', '*', 'own'),
(17, 3, 'com_dashboard', '*', '*', '*', 'own'),
(18, 3, 'com_projects', '*', '*', '*', 'own'),
(19, 4, 'com_dashboard', '*', '*', '*', 'own'),
(20, 0, 'com_projects', 'process_incoming_email', '*', '*', 'all'),
(21, 0, 'com_users', 'reset_password', '*', '*', 'all');

-- --------------------------------------------------------

--
-- Table structure for table `eo_activitylog`
--

DROP TABLE IF EXISTS `eo_activitylog`;
CREATE TABLE IF NOT EXISTS `eo_activitylog` (
  `id` int(11) NOT NULL auto_increment,
  `projectid` int(11) NOT NULL,
  `userid` int(11) NOT NULL,
  `type` varchar(16) NOT NULL,
  `action` varchar(50) NOT NULL,
  `title` varchar(50) NOT NULL,
  `description` text NOT NULL,
  `url` varchar(256) NOT NULL,
  `ts` timestamp NOT NULL default CURRENT_TIMESTAMP,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dumping data for table `eo_activitylog`
--


-- --------------------------------------------------------

--
-- Table structure for table `eo_comments`
--

DROP TABLE IF EXISTS `eo_comments`;
CREATE TABLE IF NOT EXISTS `eo_comments` (
  `id` int(11) NOT NULL auto_increment,
  `projectid` int(11) NOT NULL,
  `userid` int(11) NOT NULL,
  `type` varchar(50) NOT NULL,
  `itemid` int(11) NOT NULL,
  `body` text NOT NULL,
  `created` datetime NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dumping data for table `eo_comments`
--


-- --------------------------------------------------------

--
-- Table structure for table `eo_components`
--

DROP TABLE IF EXISTS `eo_components`;
CREATE TABLE IF NOT EXISTS `eo_components` (
  `id` int(11) NOT NULL auto_increment,
  `name` varchar(50) collate utf8_unicode_ci NOT NULL,
  `menu_name` varchar(50) collate utf8_unicode_ci NOT NULL,
  `author` varchar(50) collate utf8_unicode_ci NOT NULL,
  `version` varchar(10) collate utf8_unicode_ci NOT NULL,
  `enabled` enum('0','1') collate utf8_unicode_ci NOT NULL,
  `system` enum('0','1') collate utf8_unicode_ci NOT NULL COMMENT 'system components are required',
  `ordering` tinyint(4) NOT NULL default '0',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `eo_components`
--

INSERT INTO `eo_components` (`id`, `name`, `menu_name`, `author`, `version`, `enabled`, `system`, `ordering`) VALUES
(1, 'dashboard', 'Dashboard', 'Luis Montero', '1.0.0', '1', '1', 1),
(2, 'login', 'Logout', 'Luis Montero', '1.0.0', '1', '1', 99),
(3, 'admin', 'Admin', 'Luis Montero', '1.0.0', '1', '1', 99),
(4, 'email', 'Email', 'Luis Montero', '1.0.0', '1', '0', 2),
(5, 'projects', 'Projects', 'Luis Montero', '1.0.0', '1', '0', 3),
(6, 'users', 'Users', 'Luis Montero', '1.0.0', '1', '1', 99),
(7, 'billing', 'Billing', 'Luis Montero', '1.0.0', '0', '0', 4);

-- --------------------------------------------------------

--
-- Table structure for table `eo_email_accounts`
--

DROP TABLE IF EXISTS `eo_email_accounts`;
CREATE TABLE IF NOT EXISTS `eo_email_accounts` (
  `id` int(11) NOT NULL auto_increment,
  `userid` int(11) NOT NULL,
  `email_signature` tinytext NOT NULL,
  `server_type` varchar(32) NOT NULL default 'IMAP',
  `imap_host` varchar(128) NOT NULL,
  `imap_port` varchar(6) NOT NULL,
  `imap_user` varchar(128) NOT NULL,
  `imap_password` varchar(64) NOT NULL,
  `fromname` varchar(64) NOT NULL,
  `email_address` varchar(128) NOT NULL,
  `smtp_host` varchar(128) NOT NULL,
  `smtp_port` varchar(6) NOT NULL,
  `smtp_auth` enum('0','1') NOT NULL,
  `smtp_user` varchar(128) NOT NULL,
  `smtp_password` varchar(64) NOT NULL,
  `default` enum('0','1') NOT NULL default '0' COMMENT 'Is default account for user?',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dumping data for table `eo_email_accounts`
--


-- --------------------------------------------------------

--
-- Table structure for table `eo_files`
--

DROP TABLE IF EXISTS `eo_files`;
CREATE TABLE IF NOT EXISTS `eo_files` (
  `id` int(11) NOT NULL auto_increment,
  `projectid` int(11) NOT NULL,
  `userid` int(11) NOT NULL,
  `parentid` int(11) default NULL,
  `title` varchar(64) NOT NULL,
  `revision` int(11) NOT NULL,
  `changelog` text NOT NULL,
  `filename` varchar(128) NOT NULL,
  `mimetype` varchar(50) NOT NULL,
  `filesize` int(11) NOT NULL,
  `ts` timestamp NOT NULL default CURRENT_TIMESTAMP,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dumping data for table `eo_files`
--


-- --------------------------------------------------------

--
-- Table structure for table `eo_groups`
--

DROP TABLE IF EXISTS `eo_groups`;
CREATE TABLE IF NOT EXISTS `eo_groups` (
  `id` int(11) NOT NULL auto_increment,
  `name` varchar(50) collate utf8_unicode_ci NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `eo_groups`
--

INSERT INTO `eo_groups` (`id`, `name`) VALUES
(1, 'admin'),
(2, 'staff'),
(3, 'clients'),
(4, 'suppliers');

-- --------------------------------------------------------

--
-- Table structure for table `eo_invoice`
--

DROP TABLE IF EXISTS `eo_invoice`;
CREATE TABLE IF NOT EXISTS `eo_invoice` (
  `id` int(11) NOT NULL auto_increment,
  `customerid` int(11) NOT NULL default '0',
  `billdate` date NOT NULL default '0000-00-00',
  `description` varchar(50) NOT NULL default '',
  `amount` float NOT NULL default '0',
  `subtotal` float NOT NULL default '0',
  `sent` tinyint(4) NOT NULL default '0',
  `status` tinyint(4) NOT NULL default '0',
  `archive` tinyint(4) NOT NULL default '0',
  `sentdate` date default NULL,
  `datepaid` date default NULL,
  `tax` float NOT NULL default '0',
  `processorid` varchar(255) NOT NULL default '',
  `pluginused` varchar(15) NOT NULL default 'none',
  `checknum` varchar(50) NOT NULL,
  PRIMARY KEY  (`id`),
  UNIQUE KEY `id` (`id`),
  KEY `customerid` (`customerid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dumping data for table `eo_invoice`
--


-- --------------------------------------------------------

--
-- Table structure for table `eo_invoiceentry`
--

DROP TABLE IF EXISTS `eo_invoiceentry`;
CREATE TABLE IF NOT EXISTS `eo_invoiceentry` (
  `id` int(11) NOT NULL auto_increment,
  `customerid` int(11) NOT NULL default '0',
  `description` varchar(95) NOT NULL default '',
  `detail` text NOT NULL,
  `invoiceid` int(11) NOT NULL default '0',
  `date` date NOT NULL default '0000-00-00',
  `billingtypeid` int(11) NOT NULL default '0',
  `is_prorating` tinyint(4) NOT NULL default '0',
  `price` float NOT NULL default '0',
  `price_percent` float NOT NULL default '0',
  `recurring` int(11) NOT NULL default '0',
  `recurringappliesto` int(11) default '0',
  `appliestoid` int(11) NOT NULL default '0',
  `coupon_applicable_to` tinyint(4) NOT NULL default '0',
  `includenextpayment` tinyint(4) NOT NULL default '0',
  `paymentterm` tinyint(4) NOT NULL default '0',
  `setup` tinyint(4) NOT NULL default '0',
  `addon_setup` tinyint(4) NOT NULL default '0',
  `taxable` tinyint(4) NOT NULL default '0',
  PRIMARY KEY  (`id`),
  KEY `invoiceid` (`invoiceid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='Maintain information of work completed for customer';

--
-- Dumping data for table `eo_invoiceentry`
--


-- --------------------------------------------------------

--
-- Table structure for table `eo_invoicetransaction`
--

DROP TABLE IF EXISTS `eo_invoicetransaction`;
CREATE TABLE IF NOT EXISTS `eo_invoicetransaction` (
  `id` int(11) NOT NULL auto_increment,
  `invoiceid` int(11) NOT NULL default '0',
  `accepted` tinyint(4) NOT NULL default '0',
  `response` text NOT NULL,
  `transactiondate` datetime NOT NULL default '0000-00-00 00:00:00',
  `transactionid` varchar(25) NOT NULL default 'NA',
  `action` varchar(10) NOT NULL default 'none',
  `last4` varchar(5) NOT NULL default '0000',
  `amount` float default '0',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dumping data for table `eo_invoicetransaction`
--


-- --------------------------------------------------------

--
-- Table structure for table `eo_issues`
--

DROP TABLE IF EXISTS `eo_issues`;
CREATE TABLE IF NOT EXISTS `eo_issues` (
  `id` int(11) NOT NULL auto_increment,
  `projectid` int(11) NOT NULL,
  `issue_type` int(11) NOT NULL,
  `title` varchar(128) NOT NULL,
  `description` text NOT NULL,
  `priority` tinyint(4) NOT NULL,
  `dtstart` date NOT NULL,
  `dtend` date NOT NULL,
  `expected_duration` float(4,2) default NULL,
  `progress` tinyint(3) default '0',
  `access` enum('0','1') NOT NULL COMMENT '0=Public, 1=Private, only owner and assigned users',
  `created_by` int(11) NOT NULL,
  `created` datetime NOT NULL,
  `closed_by` int(11) default '0',
  `closed` datetime default NULL,
  `ts` timestamp NOT NULL default CURRENT_TIMESTAMP,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dumping data for table `eo_issues`
--


-- --------------------------------------------------------

--
-- Table structure for table `eo_meetings`
--

DROP TABLE IF EXISTS `eo_meetings`;
CREATE TABLE IF NOT EXISTS `eo_meetings` (
  `id` int(11) NOT NULL auto_increment,
  `projectid` int(11) NOT NULL,
  `name` varchar(64) NOT NULL,
  `dtstart` datetime NOT NULL,
  `dtend` datetime NOT NULL,
  `description` text,
  `created_by` int(11) NOT NULL,
  `created` datetime NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dumping data for table `eo_meetings`
--


-- --------------------------------------------------------

--
-- Table structure for table `eo_meetings_files`
--

DROP TABLE IF EXISTS `eo_meetings_files`;
CREATE TABLE IF NOT EXISTS `eo_meetings_files` (
  `id` int(11) NOT NULL auto_increment,
  `meetingid` int(11) NOT NULL,
  `fileid` int(11) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `eo_meetings_files`
--


-- --------------------------------------------------------

--
-- Table structure for table `eo_messages`
--

DROP TABLE IF EXISTS `eo_messages`;
CREATE TABLE IF NOT EXISTS `eo_messages` (
  `id` int(11) NOT NULL auto_increment,
  `projectid` int(11) NOT NULL,
  `userid` int(11) NOT NULL,
  `date_sent` datetime NOT NULL,
  `subject` varchar(255) NOT NULL,
  `body` text NOT NULL,
  `status` enum('0','1','2') NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dumping data for table `eo_messages`
--


-- --------------------------------------------------------

--
-- Table structure for table `eo_milestones`
--

DROP TABLE IF EXISTS `eo_milestones`;
CREATE TABLE IF NOT EXISTS `eo_milestones` (
  `id` int(11) NOT NULL auto_increment,
  `projectid` int(11) NOT NULL,
  `title` varchar(50) NOT NULL,
  `due_date` date NOT NULL,
  `description` text,
  `created_by` int(11) NOT NULL,
  `created` datetime NOT NULL,
  `closed_by` int(11) default '0',
  `closed` datetime default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dumping data for table `eo_milestones`
--


-- --------------------------------------------------------

--
-- Table structure for table `eo_modules`
--

DROP TABLE IF EXISTS `eo_modules`;
CREATE TABLE IF NOT EXISTS `eo_modules` (
  `id` int(11) NOT NULL auto_increment,
  `name` varchar(50) collate utf8_unicode_ci NOT NULL,
  `author` varchar(50) collate utf8_unicode_ci NOT NULL,
  `version` varchar(10) collate utf8_unicode_ci NOT NULL,
  `enabled` enum('0','1') collate utf8_unicode_ci NOT NULL,
  `system` enum('0','1') collate utf8_unicode_ci NOT NULL,
  `position` varchar(20) collate utf8_unicode_ci NOT NULL,
  `ordering` tinyint(4) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `eo_modules`
--

INSERT INTO `eo_modules` (`id`, `name`, `author`, `version`, `enabled`, `system`, `position`, `ordering`) VALUES
(1, 'menu', 'Luis Montero [e-noise.com]', '1.0.0', '1', '1', 'mainmenu', 1),
(2, 'projects', 'Luis Montero [e-noise.com]', '1.0.0', '1', '1', 'right', 1),
(3, 'topmenu', 'Luis Montero [e-noise.com]', '1.0.0', '1', '1', 'topmenu', 1),
(4, 'projectswitcher', 'Luis Montero [e-noise.com]', '1.0.0', '1', '1', 'topright', 1),
(5, 'errormsg', 'Luis Montero [e-noise.com]', '1.0.0', '1', '1', 'topright', 1);

-- --------------------------------------------------------

--
-- Table structure for table `eo_modules_options`
--

DROP TABLE IF EXISTS `eo_modules_options`;
CREATE TABLE IF NOT EXISTS `eo_modules_options` (
  `id` int(11) NOT NULL auto_increment,
  `moduleid` int(11) NOT NULL,
  `option` varchar(50) collate utf8_unicode_ci NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `eo_modules_options`
--

INSERT INTO `eo_modules_options` (`id`, `moduleid`, `option`) VALUES
(1, 1, '*'),
(2, 2, 'com_projects'),
(3, 3, '*'),
(4, 4, '*'),
(5, 5, '*');

-- --------------------------------------------------------

--
-- Table structure for table `eo_organisations`
--

DROP TABLE IF EXISTS `eo_organisations`;
CREATE TABLE IF NOT EXISTS `eo_organisations` (
  `id` int(11) NOT NULL auto_increment,
  `name` varchar(100) collate utf8_unicode_ci NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `eo_organisations`
--


-- --------------------------------------------------------

--
-- Table structure for table `eo_projects`
--

DROP TABLE IF EXISTS `eo_projects`;
CREATE TABLE IF NOT EXISTS `eo_projects` (
  `id` int(11) NOT NULL auto_increment,
  `name` varchar(128) NOT NULL,
  `project_type` tinyint(4) NOT NULL,
  `priority` tinyint(4) NOT NULL,
  `company_id` int(11) default NULL,
  `description` text NOT NULL,
  `status` enum('-1','0','1','2','3') NOT NULL COMMENT '-1=Archived, 0=Planning, 1=In progress, 2=Paused, 3=Finished',
  `access` enum('0','1') NOT NULL default '1' COMMENT '0=Public, 1=Private',
  `access_issues` enum('1','2','3','4') NOT NULL default '2' COMMENT '1=Admins, 2=Project workers, 3=Guests, 4=Public',
  `access_milestones` enum('1','2','3','4') NOT NULL default '2',
  `access_files` enum('1','2','3','4') NOT NULL default '2',
  `access_meetings` enum('1','2','3','4') NOT NULL default '3',
  `access_reports` enum('1','2','3','4') NOT NULL default '2',
  `access_polls` enum('1','2','3','4') NOT NULL default '3',
  `access_messages` enum('1','2','3','4') NOT NULL default '2',
  `access_people` enum('1','2','3','4') NOT NULL default '3',
  `access_admin` enum('1','2','3','4') NOT NULL default '1',
  `created_by` int(11) NOT NULL,
  `created` datetime NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dumping data for table `eo_projects`
--


-- --------------------------------------------------------

--
-- Table structure for table `eo_project_types`
--

DROP TABLE IF EXISTS `eo_project_types`;
CREATE TABLE IF NOT EXISTS `eo_project_types` (
  `id` int(11) NOT NULL auto_increment,
  `name` varchar(50) character set utf8 collate utf8_unicode_ci NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

--
-- Dumping data for table `eo_project_types`
--

INSERT INTO `eo_project_types` (`id`, `name`) VALUES
(1, 'Admin'),
(2, 'Web Development'),
(3, 'Online Marketing'),
(4, 'Domains and Hosting'),
(5, 'Training');

-- --------------------------------------------------------

--
-- Table structure for table `eo_roles`
--

DROP TABLE IF EXISTS `eo_roles`;
CREATE TABLE IF NOT EXISTS `eo_roles` (
  `id` int(11) NOT NULL auto_increment,
  `name` varchar(32) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

--
-- Dumping data for table `eo_roles`
--

INSERT INTO `eo_roles` (`id`, `name`) VALUES
(1, 'Project Admin'),
(2, 'Project Worker'),
(3, 'Guest');

-- --------------------------------------------------------

--
-- Table structure for table `eo_session`
--

DROP TABLE IF EXISTS `eo_session`;
CREATE TABLE IF NOT EXISTS `eo_session` (
  `id` varchar(32) collate utf8_unicode_ci NOT NULL,
  `userid` int(11) NOT NULL default '0',
  `groupid` int(11) NOT NULL default '0',
  `data` text collate utf8_unicode_ci NOT NULL,
  `modified` datetime NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `eo_session`
--


-- --------------------------------------------------------

--
-- Table structure for table `eo_slideshows`
--

DROP TABLE IF EXISTS `eo_slideshows`;
CREATE TABLE IF NOT EXISTS `eo_slideshows` (
  `id` int(11) NOT NULL auto_increment,
  `projectid` int(11) NOT NULL,
  `meetingid` int(11) NOT NULL,
  `name` varchar(64) NOT NULL,
  `description` text NOT NULL,
  `created_by` int(11) NOT NULL,
  `created` datetime NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dumping data for table `eo_slideshows`
--


-- --------------------------------------------------------

--
-- Table structure for table `eo_slideshows_slides`
--

DROP TABLE IF EXISTS `eo_slideshows_slides`;
CREATE TABLE IF NOT EXISTS `eo_slideshows_slides` (
  `id` int(11) NOT NULL auto_increment,
  `slideshowid` int(11) NOT NULL,
  `title` varchar(128) NOT NULL,
  `filename` varchar(128) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dumping data for table `eo_slideshows_slides`
--


-- --------------------------------------------------------

--
-- Table structure for table `eo_users`
--

DROP TABLE IF EXISTS `eo_users`;
CREATE TABLE IF NOT EXISTS `eo_users` (
  `id` int(11) NOT NULL auto_increment,
  `groupid` tinyint(4) NOT NULL,
  `username` varchar(50) collate utf8_unicode_ci NOT NULL,
  `password` varchar(100) collate utf8_unicode_ci NOT NULL,
  `email` varchar(100) collate utf8_unicode_ci NOT NULL,
  `firstname` varchar(50) collate utf8_unicode_ci NOT NULL,
  `lastname` varchar(50) collate utf8_unicode_ci NOT NULL,
  `photo` varchar(128) collate utf8_unicode_ci default 'default.png',
  `notifications` enum('0','1') collate utf8_unicode_ci default '1',
  `show_email` enum('0','1') collate utf8_unicode_ci default '1',
  `block` enum('0','1') collate utf8_unicode_ci default '0',
  `created` datetime NOT NULL,
  `last_visit` datetime default NULL,
  `activation` varchar(100) collate utf8_unicode_ci default NULL,
  `params` text collate utf8_unicode_ci,
  `ts` timestamp NULL default CURRENT_TIMESTAMP,
  `deleted` datetime default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `eo_users`
--

INSERT INTO `eo_users` (`id`, `groupid`, `username`, `password`, `email`, `firstname`, `lastname`, `photo`, `notifications`, `show_email`, `block`, `created`, `last_visit`, `activation`, `params`, `ts`, `deleted`) VALUES
(62, 1, 'admin', '59d0d3a4baecc0fe31a46fb5bd879cd1:kEuXamI4LBOIR405xh5tvq5vBmsr8mNp', 'admin@example.com', 'Administrator', 'ChangeMe', 'default.png', '1', '1', '0', '0000-00-00 00:00:00', NULL, NULL, NULL, '0000-00-00 00:00:00', '0000-00-00 00:00:00');

-- --------------------------------------------------------

--
-- Table structure for table `eo_users_files`
--

DROP TABLE IF EXISTS `eo_users_files`;
CREATE TABLE IF NOT EXISTS `eo_users_files` (
  `id` int(11) NOT NULL auto_increment,
  `userid` int(11) NOT NULL,
  `fileid` int(11) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dumping data for table `eo_users_files`
--


-- --------------------------------------------------------

--
-- Table structure for table `eo_users_issues`
--

DROP TABLE IF EXISTS `eo_users_issues`;
CREATE TABLE IF NOT EXISTS `eo_users_issues` (
  `id` int(11) NOT NULL auto_increment,
  `userid` int(11) NOT NULL,
  `issueid` int(11) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dumping data for table `eo_users_issues`
--


-- --------------------------------------------------------

--
-- Table structure for table `eo_users_meetings`
--

DROP TABLE IF EXISTS `eo_users_meetings`;
CREATE TABLE IF NOT EXISTS `eo_users_meetings` (
  `id` int(11) NOT NULL auto_increment,
  `userid` int(11) NOT NULL,
  `meetingid` int(11) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dumping data for table `eo_users_meetings`
--


-- --------------------------------------------------------

--
-- Table structure for table `eo_users_messages`
--

DROP TABLE IF EXISTS `eo_users_messages`;
CREATE TABLE IF NOT EXISTS `eo_users_messages` (
  `id` int(11) NOT NULL auto_increment,
  `userid` int(11) NOT NULL,
  `messageid` int(11) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dumping data for table `eo_users_messages`
--


-- --------------------------------------------------------

--
-- Table structure for table `eo_users_milestones`
--

DROP TABLE IF EXISTS `eo_users_milestones`;
CREATE TABLE IF NOT EXISTS `eo_users_milestones` (
  `id` int(11) NOT NULL auto_increment,
  `userid` int(11) NOT NULL,
  `milestoneid` int(11) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dumping data for table `eo_users_milestones`
--


-- --------------------------------------------------------

--
-- Table structure for table `eo_users_organisations`
--

DROP TABLE IF EXISTS `eo_users_organisations`;
CREATE TABLE IF NOT EXISTS `eo_users_organisations` (
  `id` int(11) NOT NULL auto_increment,
  `userid` int(11) NOT NULL,
  `organisationid` int(11) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `eo_users_organisations`
--


-- --------------------------------------------------------

--
-- Table structure for table `eo_users_roles`
--

DROP TABLE IF EXISTS `eo_users_roles`;
CREATE TABLE IF NOT EXISTS `eo_users_roles` (
  `id` int(11) NOT NULL auto_increment,
  `userid` int(11) NOT NULL,
  `roleid` int(11) NOT NULL,
  `projectid` int(11) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dumping data for table `eo_users_roles`
--


-- --------------------------------------------------------

--
-- Table structure for table `eo_user_openids`
--

DROP TABLE IF EXISTS `eo_user_openids`;
CREATE TABLE IF NOT EXISTS `eo_user_openids` (
  `openid_url` varchar(255) collate utf8_unicode_ci NOT NULL,
  `user_id` int(11) NOT NULL,
  PRIMARY KEY  (`openid_url`),
  KEY `user_id` (`user_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `eo_user_openids`
--

