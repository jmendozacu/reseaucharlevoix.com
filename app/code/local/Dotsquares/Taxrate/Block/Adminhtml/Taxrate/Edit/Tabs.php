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
class Dotsquares_Taxrate_Block_Adminhtml_Taxrate_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs{
	/**
	 * constructor
	 * @access public
	 * @return void	 
	 */
	public function __construct(){
		parent::__construct();
		$this->setId('taxrate_tabs');
		$this->setDestElementId('edit_form');
		$this->setTitle(Mage::helper('taxrate')->__('Tax Rate'));
	}
	/**
	 * before render html
	 * @access protected
	 * @return Dotsquares_Taxrate_Block_Adminhtml_Taxrate_Edit_Tabs
	 */
	protected function _beforeToHtml(){
		$this->addTab('form_section', array(
			'label'		=> Mage::helper('taxrate')->__('Tax Rate'),
			'title'		=> Mage::helper('taxrate')->__('Tax Rate'),
			'content' 	=> $this->getLayout()->createBlock('taxrate/adminhtml_taxrate_edit_tab_form')->toHtml(),
		));
		return parent::_beforeToHtml();
	}
}