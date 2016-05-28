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
class Dotsquares_Taxrate_Helper_Taxrate extends Mage_Core_Helper_Abstract{
	/**
	 * check if breadcrumbs can be used
	 * @access public
	 * @return bool
	 */
	public function getUseBreadcrumbs(){
		return Mage::getStoreConfigFlag('taxrate/taxrate/breadcrumbs');
	}
}