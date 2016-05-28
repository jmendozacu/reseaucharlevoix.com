<?php
/**
 *
 * NOTICE OF LICENSE
 * @copyright  Copyright (c) 2012 3Works Webdesign
 *
 */?>
<?php

$installer = $this;

$installer->startSetup();

$installer->run("
SET SQL_MODE='NO_AUTO_VALUE_ON_ZERO';

CREATE TABLE IF NOT EXISTS `{$installer->getTable('ticket_barcode')}` (
  `ticket_id` int(11) NOT NULL,
  `barcode` bigint(13) NOT NULL,
  `order_id` varchar(255) NOT NULL,
  `invoice_id` varchar(255) NOT NULL,
  `available_for` enum('A','C','E') NOT NULL,
  `deleted` int(1) DEFAULT NULL,
  PRIMARY KEY  (`barcode`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;



CREATE TABLE IF NOT EXISTS `{$installer->getTable('ticket_orders')}` (
  `ticket_id` int(11) NOT NULL auto_increment,
  `ticket_type` varchar(255) NOT NULL,
  `gender` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `birthday` date NOT NULL,
  `adress` varchar(255) NOT NULL,
  `zipcode` varchar(255) NOT NULL,
  `city` varchar(255) NOT NULL,
  `themecolor` varchar(255) NOT NULL,
  `themebg` varchar(255) NOT NULL,
  `product` varchar(255) NOT NULL,
  `price` float NOT NULL,
  `date` datetime NOT NULL,
  `order_id` int(11) NOT NULL,
  PRIMARY KEY  (`ticket_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=166 ;

");

$installer->endSetup(); 