<?php


class VDesign_BookmePro_Block_Adminhtml_Sessions extends Mage_Adminhtml_Block_Widget_Grid_Container
{
	public function __construct()
	{
		parent::__construct();
		$this->_blockGroup = 'bookmepro';
		$this->_controller = 'adminhtml_sessions';
		$this->_headerText = Mage::helper('bookmepro')->__('Session Manager');
	}
}