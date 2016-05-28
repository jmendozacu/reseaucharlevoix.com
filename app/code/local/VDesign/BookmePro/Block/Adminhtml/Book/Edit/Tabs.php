<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the End User License Agreement (EULA)
 * that is bundled with this package in the file LICENSE.txt.
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to vdesign.support@outlook.com so we can send you a copy immediately.
 *
 * @category   Mage
 * @copyright  Copyright (c) 2014 VDesign
 * @license    End User License Agreement (EULA)
 */

class VDesign_BookmePro_Block_Adminhtml_Book_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{

	public function __construct()
	{
		parent::__construct();
		$this->setId('book_edit_tabs');
		$this->setDestElementId('book_editForm');
		$this->setTitle(Mage::helper('bookmepro')->__('Reservation Information'));
	}
	
	protected function _beforeToHtml()
	{	
		return parent::_beforeToHtml();
	}


		
}