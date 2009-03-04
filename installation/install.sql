-- phpMyAdmin SQL Dump
-- version 2.11.3
-- http://www.phpmyadmin.net
--
-- Host: localhost:3306
-- Generation Time: Mar 04, 2009 at 03:32 PM
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

CREATE TABLE IF NOT EXISTS `eo_acl_groups` (
  `id` int(11) NOT NULL auto_increment,
  `groupid` int(11) NOT NULL,
  `option` varchar(50) NOT NULL,
  `task` varchar(32) NOT NULL,
  `view` varchar(32) NOT NULL,
  `layout` varchar(32) NOT NULL,
  `value` varchar(4) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=83 ;

--
-- Dumping data for table `eo_acl_groups`
--

INSERT INTO `eo_acl_groups` (`id`, `groupid`, `option`, `task`, `view`, `layout`, `value`) VALUES
(1, 1, 'com_login', 'display', 'login', '*', 'all'),
(2, 2, 'com_login', 'display', 'login', '*', 'all'),
(3, 1, 'com_dashboard', 'display', 'dashboard', '*', 'own'),
(4, 2, 'com_dashboard', 'display', 'dashboard', '*', 'own'),
(5, 1, 'com_email', 'display', 'email', '*', 'own'),
(6, 1, 'com_email', 'download_attachment', '', '', 'own'),
(7, 1, 'com_email', 'send_email', '', '', 'own'),
(8, 1, 'com_email', 'move_email', '', '', 'own'),
(9, 1, 'com_email', 'remove_email', '', '', 'own'),
(10, 1, 'com_email', 'restore_email', '', '', 'own'),
(11, 1, 'com_email', 'empty_deleted_items', '', '', 'own'),
(12, 1, 'com_email', 'set_flags', '', '', 'own'),
(13, 1, 'com_email', 'clear_flags', '', '', 'own'),
(14, 1, 'com_email', 'create_mailbox', '', '', 'own'),
(15, 1, 'com_email', 'rename_mailbox', '', '', 'own'),
(16, 1, 'com_email', 'delete_mailbox', '', '', 'own'),
(17, 2, 'com_email', 'download_attachment', '', '', 'own'),
(18, 2, 'com_email', 'send_email', '', '', 'own'),
(19, 2, 'com_email', 'move_email', '', '', 'own'),
(20, 2, 'com_email', 'remove_email', '', '', 'own'),
(21, 2, 'com_email', 'restore_email', '', '', 'own'),
(22, 2, 'com_email', 'empty_deleted_items', '', '', 'own'),
(23, 2, 'com_email', 'set_flags', '', '', 'own'),
(24, 2, 'com_email', 'clear_flags', '', '', 'own'),
(25, 2, 'com_email', 'create_mailbox', '', '', 'own'),
(26, 2, 'com_email', 'rename_mailbox', '', '', 'own'),
(27, 2, 'com_email', 'delete_mailbox', '', '', 'own'),
(28, 2, 'com_email', 'display', 'email', '*', 'own'),
(29, 1, 'com_email', 'add_attachment', '', '', 'own'),
(30, 2, 'com_email', 'add_attachment', '', '', 'own'),
(31, 1, 'com_email', 'empty_email_trash', '', '', 'own'),
(32, 2, 'com_email', 'empty_email_trash', '', '', 'own'),
(33, 1, 'com_addressbook', 'display', 'contacts', '*', 'own'),
(34, 1, 'com_addressbook', 'save_contact', '', '', 'own'),
(35, 1, 'com_addressbook', 'remove_contact', '', '', 'own'),
(36, 1, 'com_addressbook', 'export_contact', '', '', 'own'),
(37, 1, 'com_addressbook', 'import_contact', '', '', 'own'),
(38, 2, 'com_addressbook', 'display', 'contacts', '*', 'own'),
(39, 2, 'com_addressbook', 'save_contact', '', '', 'own'),
(40, 2, 'com_addressbook', 'remove_contact', '', '', 'own'),
(41, 2, 'com_addressbook', 'export_contact', '', '', 'own'),
(42, 2, 'com_addressbook', 'import_contact', '', '', 'own'),
(43, 1, 'com_projects', 'display', 'projects', '*', 'all'),
(44, 2, 'com_projects', 'display', 'projects', '*', 'own'),
(45, 2, 'com_projects', 'remove_member', '', '', 'own'),
(46, 1, 'com_projects', 'remove_member', '', '', 'own'),
(47, 1, 'com_projects', 'save_member', '', '', 'all'),
(48, 2, 'com_projects', 'save_member', '', '', 'own'),
(49, 1, 'com_projects', 'save_project', '', '', 'own'),
(50, 2, 'com_projects', 'save_project', '', '', 'own'),
(51, 1, 'com_projects', 'save_issue', '', '', 'all'),
(52, 2, 'com_projects', 'save_issue', '', '', 'all'),
(53, 1, 'com_projects', 'remove_issue', '', '', 'all'),
(54, 2, 'com_projects', 'remove_issue', '', '', 'all'),
(55, 1, 'com_projects', 'remove_project', '', '', 'own'),
(56, 2, 'com_projects', 'remove_project', '', '', 'own'),
(57, 1, 'com_projects', 'save_file', '', '', 'all'),
(58, 2, 'com_projects', 'save_file', '', '', 'all'),
(59, 1, 'com_projects', 'remove_file', '', '', 'own'),
(60, 2, 'com_projects', 'remove_file', '', '', 'own'),
(61, 1, 'com_projects', 'download_file', '', '', 'all'),
(62, 2, 'com_projects', 'download_file', '', '', 'own'),
(63, 1, 'com_projects', 'save_message', '', '', 'own'),
(64, 2, 'com_projects', 'save_message', '', '', 'own'),
(65, 1, 'com_projects', 'remove_message', '', '', 'own'),
(66, 2, 'com_projects', 'remove_message', '', '', 'own'),
(67, 1, 'com_projects', 'save_comment', '', '', 'own'),
(68, 2, 'com_projects', 'save_comment', '', '', 'own'),
(69, 1, 'com_projects', 'save_meeting', '', '', 'own'),
(70, 2, 'com_projects', 'save_meeting', '', '', 'own'),
(71, 1, 'com_projects', 'remove_meeting', '', '', 'own'),
(72, 2, 'com_projects', 'remove_meeting', '', '', 'own'),
(73, 1, 'com_projects', 'close_issue', '', '', 'all'),
(74, 2, 'com_projects', 'close_issue', '', '', 'own'),
(75, 1, 'com_projects', 'reopen_issue', '', '', 'all'),
(76, 2, 'com_projects', 'reopen_issue', '', '', 'own'),
(77, 1, 'com_projects', 'save_milestone', '', '', 'all'),
(78, 2, 'com_projects', 'save_milestone', '', '', 'own'),
(79, 1, 'com_projects', 'remove_milestone', '', '', 'all'),
(80, 2, 'com_projects', 'remove_milestone', '', '', 'own'),
(81, 1, 'com_projects', 'admin_change_member_role', '', '', 'all'),
(82, 2, 'com_projects', 'admin_change_member_role', '', '', 'all');

-- --------------------------------------------------------

--
-- Table structure for table `eo_activitylog`
--

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
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `eo_activitylog`
--


-- --------------------------------------------------------

--
-- Table structure for table `eo_comments`
--

CREATE TABLE IF NOT EXISTS `eo_comments` (
  `id` int(11) NOT NULL auto_increment,
  `projectid` int(11) NOT NULL,
  `userid` int(11) NOT NULL,
  `type` varchar(50) NOT NULL,
  `itemid` int(11) NOT NULL,
  `body` text NOT NULL,
  `created` datetime NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `eo_comments`
--


-- --------------------------------------------------------

--
-- Table structure for table `eo_components`
--

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
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=8 ;

--
-- Dumping data for table `eo_components`
--

INSERT INTO `eo_components` (`id`, `name`, `menu_name`, `author`, `version`, `enabled`, `system`, `ordering`) VALUES
(1, 'dashboard', 'Dashboard', 'Luis Montero', '1.0.0', '1', '1', 1),
(2, 'login', 'Logout', 'Luis Montero', '1.0.0', '1', '1', 99),
(3, 'admin', 'Admin', 'Luis Montero', '1.0.0', '1', '1', 99),
(4, 'email', 'Email', 'Luis Montero', '1.0.0', '1', '0', 2),
(5, 'projects', 'Projects', 'Luis Montero', '1.0.0', '1', '0', 3),
(6, 'user', 'Account', 'Luis Montero', '1.0.0', '1', '1', 99),
(7, 'billing', 'Billing', 'Luis Montero', '1.0.0', '1', '0', 4);

-- --------------------------------------------------------

--
-- Table structure for table `eo_email_accounts`
--

CREATE TABLE IF NOT EXISTS `eo_email_accounts` (
  `id` int(11) NOT NULL auto_increment,
  `userid` int(11) NOT NULL,
  `email_signature` tinytext NOT NULL,
  `server_type` varchar(32) NOT NULL,
  `incoming_server` varchar(128) NOT NULL,
  `incoming_server_port` varchar(6) NOT NULL,
  `incoming_server_username` varchar(128) NOT NULL,
  `incoming_server_password` varchar(64) NOT NULL,
  `from_name` varchar(64) NOT NULL,
  `email_address` varchar(128) NOT NULL,
  `outgoing_server` varchar(128) NOT NULL,
  `outgoing_server_port` varchar(6) NOT NULL,
  `outgoing_server_auth` enum('0','1') NOT NULL,
  `outgoing_server_username` varchar(128) NOT NULL,
  `outgoing_server_password` varchar(64) NOT NULL,
  `default` enum('0','1') NOT NULL default '0' COMMENT 'Is default account for user?',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=7 ;

--
-- Dumping data for table `eo_email_accounts`
--


-- --------------------------------------------------------

--
-- Table structure for table `eo_files`
--

CREATE TABLE IF NOT EXISTS `eo_files` (
  `id` int(11) NOT NULL auto_increment,
  `projectid` int(11) NOT NULL,
  `userid` int(11) NOT NULL,
  `parentid` int(11) NOT NULL,
  `title` varchar(64) NOT NULL,
  `revision` int(11) NOT NULL,
  `changelog` text NOT NULL,
  `filename` varchar(128) NOT NULL,
  `mimetype` varchar(50) NOT NULL,
  `filesize` int(11) NOT NULL,
  `ts` timestamp NOT NULL default CURRENT_TIMESTAMP,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `eo_files`
--


-- --------------------------------------------------------

--
-- Table structure for table `eo_groups`
--

CREATE TABLE IF NOT EXISTS `eo_groups` (
  `id` int(11) NOT NULL auto_increment,
  `name` varchar(50) collate utf8_unicode_ci NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=4 ;

--
-- Dumping data for table `eo_groups`
--

INSERT INTO `eo_groups` (`id`, `name`) VALUES
(1, 'admin'),
(2, 'staff'),
(3, 'guests');

-- --------------------------------------------------------

--
-- Table structure for table `eo_invoice`
--

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
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `eo_invoice`
--


-- --------------------------------------------------------

--
-- Table structure for table `eo_invoiceentry`
--

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
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='Maintain information of work completed for customer' AUTO_INCREMENT=1 ;

--
-- Dumping data for table `eo_invoiceentry`
--


-- --------------------------------------------------------

--
-- Table structure for table `eo_invoicetransaction`
--

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
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `eo_invoicetransaction`
--


-- --------------------------------------------------------

--
-- Table structure for table `eo_issues`
--

CREATE TABLE IF NOT EXISTS `eo_issues` (
  `id` int(11) NOT NULL auto_increment,
  `projectid` int(11) NOT NULL,
  `issue_type` int(11) NOT NULL,
  `title` varchar(128) NOT NULL,
  `description` text NOT NULL,
  `priority` tinyint(4) NOT NULL,
  `dtstart` date NOT NULL,
  `dtend` date NOT NULL,
  `expected_duration` float(4,2) NOT NULL,
  `progress` tinyint(3) NOT NULL,
  `access` enum('0','1') NOT NULL COMMENT '0=Public, 1=Private, only owner and assigned users',
  `created_by` int(11) NOT NULL,
  `created` datetime NOT NULL,
  `closed_by` int(11) NOT NULL,
  `closed` datetime NOT NULL,
  `ts` timestamp NOT NULL default CURRENT_TIMESTAMP,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `eo_issues`
--


-- --------------------------------------------------------

--
-- Table structure for table `eo_meetings`
--

CREATE TABLE IF NOT EXISTS `eo_meetings` (
  `id` int(11) NOT NULL auto_increment,
  `projectid` int(11) NOT NULL,
  `name` varchar(64) NOT NULL,
  `dtstart` datetime NOT NULL,
  `dtend` datetime NOT NULL,
  `created_by` int(11) NOT NULL,
  `created` datetime NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `eo_meetings`
--


-- --------------------------------------------------------

--
-- Table structure for table `eo_messages`
--

CREATE TABLE IF NOT EXISTS `eo_messages` (
  `id` int(11) NOT NULL auto_increment,
  `projectid` int(11) NOT NULL,
  `userid` int(11) NOT NULL,
  `date_sent` datetime NOT NULL,
  `subject` varchar(255) NOT NULL,
  `body` text NOT NULL,
  `status` enum('0','1','2') NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `eo_messages`
--


-- --------------------------------------------------------

--
-- Table structure for table `eo_milestones`
--

CREATE TABLE IF NOT EXISTS `eo_milestones` (
  `id` int(11) NOT NULL auto_increment,
  `projectid` int(11) NOT NULL,
  `title` varchar(50) NOT NULL,
  `due_date` date NOT NULL,
  `created_by` int(11) NOT NULL,
  `created` datetime NOT NULL,
  `closed_by` int(11) default '0',
  `closed` datetime default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `eo_milestones`
--


-- --------------------------------------------------------

--
-- Table structure for table `eo_modules`
--

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
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=3 ;

--
-- Dumping data for table `eo_modules`
--

INSERT INTO `eo_modules` (`id`, `name`, `author`, `version`, `enabled`, `system`, `position`, `ordering`) VALUES
(1, 'menu', 'Luis Montero [e-noise.com]', '1.0.0', '1', '1', 'topmenu', 1),
(2, 'projects', 'Luis Montero [e-noise.com]', '1.0.0', '1', '1', 'right', 1);

-- --------------------------------------------------------

--
-- Table structure for table `eo_modules_options`
--

CREATE TABLE IF NOT EXISTS `eo_modules_options` (
  `id` int(11) NOT NULL auto_increment,
  `moduleid` int(11) NOT NULL,
  `option` varchar(50) collate utf8_unicode_ci NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=3 ;

--
-- Dumping data for table `eo_modules_options`
--

INSERT INTO `eo_modules_options` (`id`, `moduleid`, `option`) VALUES
(1, 1, '*'),
(2, 2, 'com_projects');

-- --------------------------------------------------------

--
-- Table structure for table `eo_projects`
--

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
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=4 ;

--
-- Dumping data for table `eo_projects`
--


-- --------------------------------------------------------

--
-- Table structure for table `eo_project_types`
--

CREATE TABLE IF NOT EXISTS `eo_project_types` (
  `id` int(11) NOT NULL auto_increment,
  `name` varchar(50) character set utf8 collate utf8_unicode_ci NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=6 ;

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

CREATE TABLE IF NOT EXISTS `eo_roles` (
  `id` int(11) NOT NULL auto_increment,
  `name` varchar(32) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=4 ;

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
-- Table structure for table `eo_users`
--

CREATE TABLE IF NOT EXISTS `eo_users` (
  `id` int(11) NOT NULL auto_increment,
  `username` varchar(50) collate utf8_unicode_ci NOT NULL,
  `password` varchar(100) collate utf8_unicode_ci NOT NULL,
  `email` varchar(100) collate utf8_unicode_ci NOT NULL,
  `firstname` varchar(50) collate utf8_unicode_ci NOT NULL,
  `lastname` varchar(50) collate utf8_unicode_ci NOT NULL,
  `block` enum('0','1') collate utf8_unicode_ci NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=65 ;

--
-- Dumping data for table `eo_users`
--

INSERT INTO `eo_users` (`id`, `username`, `password`, `email`, `firstname`, `lastname`, `block`) VALUES
(62, 'luis.montero', '6dcee317242b8d094e0e56d7aa36e9b3:hNakpNF7iWLijc2Ww53TyfiWoHyB1Zor', 'luis.montero@e-noise.com', 'Luis', 'Montero', '0'),
(63, 'svenlito', '6dcee317242b8d094e0e56d7aa36e9b3:hNakpNF7iWLijc2Ww53TyfiWoHyB1Zor', 'sven.lito@e-noise.com', 'Sven', 'Lito', '0'),
(64, 'will.vdmerwe', '6dcee317242b8d094e0e56d7aa36e9b3:hNakpNF7iWLijc2Ww53TyfiWoHyB1Zor', 'will.vdmerwe@sliderstudio.co.uk', 'Will', 'Van der Merwe', '0');

-- --------------------------------------------------------

--
-- Table structure for table `eo_users_files`
--

CREATE TABLE IF NOT EXISTS `eo_users_files` (
  `id` int(11) NOT NULL auto_increment,
  `userid` int(11) NOT NULL,
  `fileid` int(11) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `eo_users_files`
--


-- --------------------------------------------------------

--
-- Table structure for table `eo_users_groups`
--

CREATE TABLE IF NOT EXISTS `eo_users_groups` (
  `id` int(11) NOT NULL auto_increment,
  `groupid` int(11) NOT NULL,
  `userid` int(11) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=4 ;

--
-- Dumping data for table `eo_users_groups`
--

INSERT INTO `eo_users_groups` (`id`, `groupid`, `userid`) VALUES
(1, 1, 62),
(2, 1, 63),
(3, 1, 64);

-- --------------------------------------------------------

--
-- Table structure for table `eo_users_issues`
--

CREATE TABLE IF NOT EXISTS `eo_users_issues` (
  `id` int(11) NOT NULL auto_increment,
  `userid` int(11) NOT NULL,
  `issueid` int(11) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `eo_users_issues`
--


-- --------------------------------------------------------

--
-- Table structure for table `eo_users_meetings`
--

CREATE TABLE IF NOT EXISTS `eo_users_meetings` (
  `id` int(11) NOT NULL auto_increment,
  `userid` int(11) NOT NULL,
  `meetingid` int(11) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `eo_users_meetings`
--


-- --------------------------------------------------------

--
-- Table structure for table `eo_users_messages`
--

CREATE TABLE IF NOT EXISTS `eo_users_messages` (
  `id` int(11) NOT NULL auto_increment,
  `userid` int(11) NOT NULL,
  `messageid` int(11) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `eo_users_messages`
--


-- --------------------------------------------------------

--
-- Table structure for table `eo_users_milestones`
--

CREATE TABLE IF NOT EXISTS `eo_users_milestones` (
  `id` int(11) NOT NULL auto_increment,
  `userid` int(11) NOT NULL,
  `milestoneid` int(11) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `eo_users_milestones`
--


-- --------------------------------------------------------

--
-- Table structure for table `eo_users_roles`
--

CREATE TABLE IF NOT EXISTS `eo_users_roles` (
  `id` int(11) NOT NULL auto_increment,
  `userid` int(11) NOT NULL,
  `roleid` int(11) NOT NULL,
  `projectid` int(11) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `eo_users_roles`
--

