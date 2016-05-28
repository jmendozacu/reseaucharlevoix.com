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
class Dotsquares_Taxrate_Block_Adminhtml_Taxrate extends Mage_Adminhtml_Block_Widget_Grid_Container{
	/**
	 * constructor
	 * @access public
	 * @return void	 
	 */
	public function __construct(){
		$this->_controller 		= 'adminhtml_taxrate';
		$this->_blockGroup 		= 'taxrate';
		$this->_headerText 		= Mage::helper('taxrate')->__('Tax Rate');
		$this->_addButtonLabel 	= Mage::helper('taxrate')->__('Add Tax Rate');
		parent::__construct();
	}
}