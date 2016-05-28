<?php


class VDesign_BookmePro_Adminhtml_SessionController extends Mage_Adminhtml_Controller_Action{
	
	protected function _initAction()
	{
		// load layout, set active menu and breadcrumbs
		$this->loadLayout();
		return $this;
	}
	
	public function indexAction()
	{
		$this->loadLayout()->_setActiveMenu('bookmepro/session');
		$this->_addContent($this->getLayout()->createBlock('bookmepro/adminhtml_session'));
		$this->renderLayout();
	}
	public function gridAction()
	{
		$this->loadLayout();
		$this->getResponse()->setBody(
				$this->getLayout()->createBlock('bookmepro/adminhtml_session_grid')->toHtml()
		);
	}
	
	public function newAction(){
		 
		$request = Mage::app()->getResponse()
		->setRedirect(Mage::helper('adminhtml')->getUrl('adminhtml/sales_order_create/index'));
	
	}
	
	public function editAction(){
		$this->_title($this->__('Manage Session'));
		
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
	
	public function exportCsvAction()
	{
		$fileName = 'session.csv';
		$grid = $this->getLayout()->createBlock('bookmepro/adminhtml_session_grid');
		$this->_prepareDownloadResponse($fileName, $grid->getCsvFile());
	}
	
	public function exportExcelAction()
	{
		$fileName = 'session.xml';
		$grid = $this->getLayout()->createBlock('bookmepro/adminhtml_session_grid');
		$this->_prepareDownloadResponse($fileName, $grid->getExcelFile($fileName));
	}
	
	public function deleteAction(){
		$id = $this->getRequest()->getParam('item_id');
		
		$book_item = Mage::getModel('bookme/book_item')->load($id);
		$order_item = Mage::getModel('sales/order_item')->load($book_item->getOrderItemId());
		
		if($order_item->getStatus() == 'Canceled' || $order_item->getStatus() == 'Refunded')
		{
			$session = Mage::getModel('bookmepro/session')->load($book_item->getSessionId());
			$session->setBookedQty($session->getBookedQty() - $book_item->getQty());
			$session->save();
			
			$book_item->delete();
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
			$helper->updateSessionReservation($session_id,$old_session_id,$booked_qty,$book_item);
		}
		
		$this->_redirect('bookmepro/adminhtml_sessions/');
	}
	
// 	public function closeAction(){
// 		$from = $this->getRequest()->getParam('booked_from');
// 		$product_id = $this->getRequest()->getParam('product_id');
		
// 		$product = Mage::getModel('catalog/product')->load($product_id);
		
// 		$book = Mage::getModel('bookme/book');
// 		$book->setOrderId(null);
// 		$book->setCustomerFirstname('closed');
// 		$book->setCustomerLastname('');
// 		$book->setCustomerEmail('closed');
// 		$book->setCustomerPhone('closed');
// 		$book->save();
		
// 		$book_item = Mage::getModel('bookme/book_item');
// 		$book_item->setBookId($book->getId($product_id));
// 		$book_item->setProductId();
// 		$book_item->setOrderItemId(null);
// 		$book_item->setBookedFrom($from);
// 		$book_item->setBookedTo($this->getRequest()->getParam('booked_from'));
// 		$book_item->setFromDate(date('Y-m-d', strtotime($from)));
// 		$book_item->setFromTime(date('H:i:s', strtotime($from)));
// 		$book_item->setToDate(date('Y-m-d', strtotime($from)));
// 		$book_item->setToTime(date('H:i:s', strtotime($from)));
// 		$book_item->setBookType('Session');
// 		$book_item->setQty(Mage::helper('bookmepro/availible')->getAvailibleQty($product, $from));
// 		$book_item->save();
		
// 		$this->_forward('index');
// 	}
}