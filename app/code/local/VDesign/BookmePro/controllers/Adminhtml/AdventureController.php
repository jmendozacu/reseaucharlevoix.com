<?php


class VDesign_BookmePro_Adminhtml_AdventureController extends Mage_Adminhtml_Controller_Action{
	
	protected function _initAction()
	{
		// load layout, set active menu and breadcrumbs
		$this->loadLayout();
		return $this;
	}
	
	public function indexAction()
	{
		$this->loadLayout()->_setActiveMenu('bookmepro/adventure');
		$this->_addContent($this->getLayout()->createBlock('bookmepro/adminhtml_adventure'));
		$this->renderLayout();
	}
	public function gridAction()
	{
		$this->loadLayout();
		$this->getResponse()->setBody(
				$this->getLayout()->createBlock('bookmepro/adminhtml_adventure_grid')->toHtml()
		);
	}
	public function newAction(){
		 
		$request = Mage::app()->getResponse()
		->setRedirect(Mage::helper('adminhtml')->getUrl('adminhtml/sales_order_create/index'));
	
	}
	
	public function exportCsvAction()
	{
		$fileName = 'adventure.csv';
		$grid = $this->getLayout()->createBlock('bookmepro/adminhtml_adventure_grid');
		$this->_prepareDownloadResponse($fileName, $grid->getCsvFile());
	}
	public function exportExcelAction()
	{
		$fileName = 'adventure.xml';
		$grid = $this->getLayout()->createBlock('bookmepro/adminhtml_adventure_grid');
		$this->_prepareDownloadResponse($fileName, $grid->getExcelFile($fileName));
	}
	
	public function editAction(){
		$this->_title($this->__('Manage Adventure'));
		
		// 1. Get ID and create model
		$id = $this->getRequest()->getParam('item_id');
		$model = Mage::getModel('bookme/book_item');
		
		// 2. Initial checking
		if ($id) {
			$model->load($id);
			if (! $model->getId()) {
				Mage::getSingleton('adminhtml/session')->addError(
				Mage::helper('bookmepro')->__('This reservation no longer exists.'));
				$this->_redirect('*/*/');
				return;
			}
		}
		
		$product = Mage::getModel('catalog/product')->load($model->getProductId());
		if(!$product->getId()){
			Mage::getSingleton('adminhtml/session')->addError(
			Mage::helper('bookmepro')->__('This product no longer exists.'));
			$this->_redirect('*/*/');
			return;
		}
		
		$book = Mage::getModel('bookme/book')->load($model->getBookId());
		if(!$book->getId()){
			Mage::getSingleton('adminhtml/session')->addError(
			Mage::helper('bookmepro')->__('This reservation no longer exists.'));
			$this->_redirect('*/*/');
			return;
		}
		
		$order = Mage::getModel('sales/order')->load($book->getOrderId());
		if(!$order->getId()){
			Mage::getSingleton('adminhtml/session')->addError(
			Mage::helper('bookmepro')->__('This order no longer exists.'));
			$this->_redirect('*/*/');
			return;
		}
		
		$model->setData("order_number", $order->getIncrementId());
		$model->setData("customer_name", $book->getCustomerFirstname().' '.$book->getCustomerLastname());
		$model->setData("status", $order->getStatus());
		$model->setData("product", $product->getName());
		$model->setData("created_at", $book->getCreated());
		$model->setData("old_session_id", $model->getSessionId());
		
		$this->_title($product->getName().' '.$model->getBookedFrom());
		 
		// 4. Register model to use later in blocks
		Mage::register('bookme_session', $model);
		
		// 5. Build edit form
		$this->_initAction();
		
		$this->renderLayout();
	}
	
	public function deleteAction(){
		$id = $this->getRequest()->getParam('item_id');
		
		$book_item = Mage::getModel('bookme/book_item')->load($id);
		$order_item = Mage::getModel('sales/order_item')->load($book_item->getOrderItemId());
		
		if($order_item->getStatus() == 'Canceled' || $order_item->getStatus() == 'Refunded')
		{
			$book = Mage::getModel('bookme/book')->load($book_item->getBookId());
			$book->delete();
			Mage::getSingleton('adminhtml/session')->addSuccess('Reservation was succesfully deleted.');
		}else{
			Mage::getSingleton('adminhtml/session')->addError('The order of this reservation is still active. Reservation could not be deleted.');
		}
		
		$this->_redirectUrl($this->getUrl('bookmepro/adminhtml_session/', array('session_id' => $book_item->getSessionId())));
	}
	
	public function saveAction(){
		$session_id = $this->getRequest()->getPost('session_id');
		$old_session_id = $this->getRequest()->getPost('old_session_id');
		$item_id = $this->getRequest()->getParam('id');
		
		$helper = Mage::helper('bookmepro/availible');
		$book_item = Mage::getModel('bookme/book_item')->load($item_id);
		
		if($old_session_id != $session_id)
		{
			$helper->updateAdventureReservation($session_id,$old_session_id,$booked_qty,$book_item);
		}
		
		$this->_redirect('bookmepro/adminhtml_adventures/');
	}
	

}