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
class VDesign_BookmePro_Adminhtml_BookController extends Mage_Adminhtml_Controller_Action
{
	protected function _initAction()
	{
		// load layout, set active menu and breadcrumbs
		$this->loadLayout();
		return $this;
	}
	
    public function indexAction()
    {
    	
        $this->loadLayout()->_setActiveMenu('bookmepro/daily');
        $this->_addContent($this->getLayout()->createBlock('bookme/adminhtml_book'));
        $this->renderLayout();
    }
    public function gridAction()
    {
        $this->loadLayout();
        $this->getResponse()->setBody(
            $this->getLayout()->createBlock('bookme/adminhtml_book_grid')->toHtml()
        );
    }
    public function newAction(){
    	
    	$request = Mage::app()->getResponse()
    	->setRedirect(Mage::helper('adminhtml')->getUrl('adminhtml/sales_order_create/index'));

    }
    
    public function editAction(){
    	$this->_title($this->__('Manage Reservations'));
    	
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
    	
    	date_default_timezone_set(Mage::app()->getStore()->getConfig('general/locale/timezone'));
    	$from_to = Mage::helper('core')->formatDate($model->getBookedFrom(), 'medium', true).' - '.Mage::helper('core')->formatDate($model->getBookedTo(), 'medium', true);
    	$model->setData('from_to', $from_to);
    	
    	$this->_title($product->getName().' '.$model->getBookedFrom());
    		
    	// 4. Register model to use later in blocks
    	Mage::register('bookme_session', $model);
    	
    	// 5. Build edit form
    	$this->_initAction();
    	
    	$this->renderLayout();
    }
    
    public function saveAction(){
    	$from_to = $this->getRequest()->getPost('from_to');
    	$item_id = $this->getRequest()->getParam('id');
    	
    	$helper = Mage::helper('bookmepro/availible');
    	$book_item = Mage::getModel('bookme/book_item')->load($item_id);
    	
    	$helper->updateBookReservation($from_to, $book_item);
    	
    	$this->_redirect('bookmepro/adminhtml_book/');
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
    	
    	$this->_redirectUrl($this->getUrl('bookmepro/adminhtml_book/'));
    }
    
    public function reminderAction(){
    	$ids = $this->getRequest()->getParam('item');
    	$template_id = $this->getRequest()->getParam('template');
    	
    	foreach ($ids as $id)
    	{
    		$bool = true;
    		$item = Mage::getModel('bookme/book_item')->load($id);
    		$book = Mage::getModel('bookme/book')->load($item->getBookId());
    		$order_item = Mage::getModel('sales/order_item')->load($item->getOrderItemId());
    		$product = Mage::getModel('catalog/product')->load($item->getProductId());
    		$order = Mage::getModel('sales/order')->load($book->getOrderId());
    		
    		$option = $order_item->getProductOptions();
    		for ($i = 0; $i < count($option['options']); $i++)
    		{
    			if($option['options'][$i]['option_type'] == 'multidate_type')
				{
    				$data = unserialize($option['options'][$i]['value']);
    				$reservation_text = Mage::getModel('bookmepro/observer')->getReservationText($data, $book->getOrderId());
    				break;
    			}
    		}
    		
    		$customer_email = $book->getCustomerEmail();
    		$customer_name = $book->getCustomerFirstname().' '.$book->getCustomerLastname();
    		$product_name = $product->getName();
    		
    		$translate = Mage::getSingleton('core/translate');
    		$translate->setTranslateInline(false);
    		$mailTemplate = Mage::getModel('core/email_template');
    		
    		if(Mage::getStoreConfig('bookmepro_reminder/reminder/sender_name') == '' ||  Mage::getStoreConfig('bookmepro_reminder/reminder/sender_email') == '')
    		{
    			$bool = false;
    			break;
    		}
    		
    		$senderName = Mage::getStoreConfig('bookmepro_reminder/reminder/sender_name');
			$senderEmail = Mage::getStoreConfig('bookmepro_reminder/reminder/sender_email');
			$sendCopy = Mage::getStoreConfig('bookmepro_reminder/reminder/send_copy');
			$sendCopyTo = Mage::getStoreConfig('bookmepro_reminder/reminder/send_copy_to');
    		
    		$sender = array('name' => $senderName,
						'email' => $senderEmail);
    		
    		$productMediaConfig = Mage::getModel('catalog/product_media_config');
    		$baseImageUrl = $productMediaConfig->getMediaUrl($product->getImage());
    		
    		$store = Mage::getModel('core/store')->load($order->getStoreId());
			try{
				$mailTemplate->sendTransactional($template_id, $sender, $customer_email, $customer_name, array(
						'reservation_data' => $reservation_text,
						'product_image' => $baseImageUrl,
						'product_description' => $product->getShortDescription(),
						'product_url' => $product->getProductUrl(),
						'product_name' => $product->getName(),
						'customer_name' => $customer_name
				), $store->getId());
				
				if($sendCopy == 1){
					$mailTemplate->sendTransactional($template_id, $sender, $sendCopyTo, $customer_name, array(
							'reservation_data' => $reservation_text,
							'product_name' => $product->getName(),
							'product_image' => $baseImageUrl,
							'product_description' => $product->getShortDescription(),
							'product_url' => $product->getProductUrl(),
							'customer_name' => $customer_name
					), $store->getId());
				}
				
				$translate->setTranslateInline(true);
				
			}catch (Exception $e)
			{
				$bool = false;
				Mage::throwException($e);
				break;
			}
    	}
    	if($bool)
    		Mage::getSingleton('adminhtml/session')->addSuccess('The reminders was send to customers.');
    	else
    		Mage::getSingleton('adminhtml/session')->addError('The reminders was not send due to errors, please check system-config settings for email reminders or log files.');
    		
    	$this->indexAction();
    }

}