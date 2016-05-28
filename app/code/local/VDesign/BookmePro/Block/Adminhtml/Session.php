<?php


class VDesign_BookmePro_Block_Adminhtml_Session extends Mage_Adminhtml_Block_Widget_Grid_Container
{
	public function __construct()
	{
		
		$this->_blockGroup = 'bookmepro';
		$this->_controller = 'adminhtml_session';
		
		$session = Mage::getModel('bookmepro/session')->load($this->getRequest()->getParam('session_id'));
		$this->_headerText = Mage::helper('bookmepro')->__($session->getDateFrom().' '.$session->getTimeFrom().', '.$this->getRequest()->getParam('booked_from').' reservations');
		
		$this->_addButton('back_button', array(
						'label'     => Mage::helper('bookmepro')->__('Back'),
						'onclick'   => 'setLocation(\'' . $this->getUrl('*/adminhtml_sessions/index') .'\')',
						'class'     => 'back',
				));
		
// 		$this->_addButton('close_button', array(
// 				'label'     => Mage::helper('bookmepro')->__('Close'),
// 				'onclick'   => 'setLocation(\'' . $this->getUrl('*/adminhtml_session/close' , array('booked_from' => $this->getRequest()->getParam('booked_from'), 'product_id' => $this->getRequest()->getParam('product_id'))) .'\')'
// 		));
		
		parent::__construct();
	}
}