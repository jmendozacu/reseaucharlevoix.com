<?php


class VDesign_BookmePro_Adminhtml_SessionsController extends Mage_Adminhtml_Controller_Action{
	
	public function indexAction()
	{
		$this->loadLayout()->_setActiveMenu('bookmepro/session');
		$this->_addContent($this->getLayout()->createBlock('bookmepro/adminhtml_sessions'));
		$this->renderLayout();
	}
	public function gridAction()
	{
		$this->loadLayout();
		$this->getResponse()->setBody(
				$this->getLayout()->createBlock('bookmepro/adminhtml_sessions_grid')->toHtml()
		);
	}
	public function newAction(){
		 
		$request = Mage::app()->getResponse()
		->setRedirect(Mage::helper('adminhtml')->getUrl('adminhtml/sales_order_create/index'));
	
	}
	
	public function exportCsvAction()
	{
		$fileName = 'sessions.csv';
		$grid = $this->getLayout()->createBlock('bookmepro/adminhtml_sessions_grid');
		$this->_prepareDownloadResponse($fileName, $grid->getCsvFile());
	}
	public function exportExcelAction()
	{
		$fileName = 'sessions.xml';
		$grid = $this->getLayout()->createBlock('bookmepro/adminhtml_sessions_grid');
		$this->_prepareDownloadResponse($fileName, $grid->getExcelFile($fileName));
	}
	
	public function increaseAction(){
		$ids = $this->getRequest()->getParam('session');
		$amount = $this->getRequest()->getParam('amount');
	
		foreach ($ids as $id)
		{
			$session = Mage::getModel('bookmepro/session')->load($id);
			$session->setMaxQuantity($session->getMaxQuantity() + $amount);
			$session->save();
		}
		Mage::getSingleton('adminhtml/session')->addSuccess('The capacity of selected sessions was increased.');
		$this->indexAction();
	}
	
	
	public function decreaseAction(){
		$ids = $this->getRequest()->getParam('session');
		$amount = $this->getRequest()->getParam('amount');
	
		foreach ($ids as $id)
		{
			$session = Mage::getModel('bookmepro/session')->load($id);
			$b_qty = $session->getBookedQty();
			if($b_qty <= ($session->getMaxQuantity() - $amount))
				$session->setMaxQuantity($session->getMaxQuantity() - $amount);
			else
				Mage::getSingleton('adminhtml/session')->addError('The capacity of session at '.$session->getDateFrom().' '.$session->getTimeFrom().' was not decreased, because of not enough free capacity.');
			$session->save();
		}
	
		$this->indexAction();
	}
	
	public function reminderAction(){
		$ids = $this->getRequest()->getParam('session');
		$template_id = $this->getRequest()->getParam('template');
	
		if(Mage::helper('bookmepro')->sendMassReminders($ids, $template_id))
			Mage::getSingleton('adminhtml/session')->addSuccess('The reminders was send to customers.');
		else
			Mage::getSingleton('adminhtml/session')->addError('The reminders was not send due to errors, please check system-config settings for email reminders or log files.');
		
		$this->indexAction();
	}
}