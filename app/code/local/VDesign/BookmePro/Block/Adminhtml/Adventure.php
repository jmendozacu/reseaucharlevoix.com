<?php


class VDesign_BookmePro_Block_Adminhtml_Adventure extends Mage_Adminhtml_Block_Widget_Grid_Container
{
	public function __construct()
	{
		
		$this->_blockGroup = 'bookmepro';
		$this->_controller = 'adminhtml_adventure';
		
		$session = Mage::getModel('bookmepro/session')->load($this->getRequest()->getParam('session_id'));
		$this->_headerText = Mage::helper('bookmepro')->__($session->getDateFrom().' '.$session->getTimeFrom().', '.$this->getRequest()->getParam('booked_from').' reservations');
		
		$this->_addButton('back_button', array(
						'label'     => Mage::helper('bookmepro')->__('Back'),
						'onclick'   => 'setLocation(\'' . $this->getUrl('*/adminhtml_adventures/index') .'\')',
						'class'     => 'back',
				));
		
		parent::__construct();
	}
}