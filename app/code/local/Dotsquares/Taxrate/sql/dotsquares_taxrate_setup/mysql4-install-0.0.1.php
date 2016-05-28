<?php 
/**
 * dotsquares.com
 *
 * Dotsquares_Taxrate extension
 * 
 * NOTICE OF LICENSE
 * 
 * This source file is subject to the MIT License
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/mit-license.php
 * 
 * @category   	Dotsquares
 * @package		Dotsquares_Taxrate
 * @copyright  	Copyright (c) 2013
 * @license		http://opensource.org/licenses/mit-license.php MIT License
 * @author      Jagdish Ram <jagdish.ram@dotsquares.com>
 */
$this->startSetup();
$this->run("
	CREATE TABLE IF NOT EXISTS `{$this->getTable('taxrate/taxrate')}` (
		`entity_id` int(10) unsigned NOT NULL auto_increment,
		`tax_country_id` text NULL ,
		`taxrate_title` varchar(255) NOT NULL default '',
		`tax_rate` decimal(12,4) NOT NULL default '0',
		`tax_calculation_rate_ids` text NULL ,
		`created_at` datetime NULL,
		`updated_at` datetime NULL,		
		`tax_region_id` int(11) NOT NULL default '0',
		`tax_postcode` varchar(21) NULL,
		`zip_is_range` smallint(6) NULL,
		`zip_from` int(10) NULL,
		`zip_to` int(10) NULL,	
	PRIMARY KEY (`entity_id`))
	ENGINE=InnoDB DEFAULT CHARSET=utf8;
");
$this->endSetup();