--
-- Estructura de la taula `#__affiliate_tracker_accounts`
--

CREATE TABLE IF NOT EXISTS `#__affiliate_tracker_accounts` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `account_name` varchar(255) NOT NULL,
  `user_id` int(11) NOT NULL,
  `publish` tinyint(1) NOT NULL,
  `params` text NOT NULL,
  `comission` decimal(8,2) NOT NULL,
  `type` varchar(255) NOT NULL,
  `payment_options` text NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `address` text NOT NULL,
  `zipcode` varchar(255) NOT NULL,
  `city` varchar(255) NOT NULL,
  `state` varchar(255) NOT NULL,
  `country` varchar(255) NOT NULL,
  `phone` varchar(255) NOT NULL,
  `company` varchar(255) NOT NULL,
  `ref_word` varchar(255) NOT NULL,
  `variable_comissions` text NOT NULL,
  `refer_url` varchar(255) NOT NULL,
  `parent_id` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`)
) ENGINE=MyISAM ;

-- --------------------------------------------------------

--
-- Estructura de la taula `#__affiliate_tracker_conversions`
--

CREATE TABLE IF NOT EXISTS `#__affiliate_tracker_conversions` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `atid` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `extended_name` varchar(255) NOT NULL,
  `value` decimal(10,2) NOT NULL,
  `comission` decimal(10,2) NOT NULL,
  `date_created` datetime NOT NULL,
  `approved` tinyint(1) NOT NULL,
  `type` varchar(255) NOT NULL,
  `reference_id` int(11) NOT NULL,
  `notes` text NOT NULL,
  `params` text NOT NULL,
  `component` varchar(255) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `atid` (`atid`)
) ENGINE=MyISAM  ;

-- --------------------------------------------------------

--
-- Estructura de la taula `#__affiliate_tracker_logs`
--

CREATE TABLE IF NOT EXISTS `#__affiliate_tracker_logs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `datetime` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `sessionid` varchar(255) NOT NULL,
  `user_id` int(11) NOT NULL DEFAULT '0',
  `account_id` int(11) NOT NULL DEFAULT '0',
  `atid` int(11) NOT NULL,
  `ip` varchar(255) NOT NULL DEFAULT '',
  `refer` varchar(255) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `account_id` (`account_id`),
  KEY `atid` (`atid`),
  KEY `ip` (`ip`,`datetime`)
) ENGINE=MyISAM  ;

-- --------------------------------------------------------

--
-- Estructura de la taula `#__affiliate_tracker_payments`
--

CREATE TABLE IF NOT EXISTS `#__affiliate_tracker_payments` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `created_datetime` datetime NOT NULL,
  `payment_id` varchar(255) NOT NULL,
  `payment_type` varchar(255) NOT NULL,
  `payment_status` tinyint(1) NOT NULL,
  `payment_amount` double(11,2) NOT NULL,
  `payment_details` text NOT NULL,
  `payment_datetime` datetime NOT NULL,
  `payment_duedate` datetime NOT NULL,
  `payment_description` mediumtext NOT NULL,
  `ordering` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  ;

-- --------------------------------------------------------

--
-- Estructura de la taula `#__affiliate_tracker_marketing_material`
--

CREATE TABLE IF NOT EXISTS `#__affiliate_tracker_marketing_material` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `html_code` text NOT NULL,
  `publish` tinyint(1) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  ;
