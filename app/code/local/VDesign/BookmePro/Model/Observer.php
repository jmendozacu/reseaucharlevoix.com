<?php

class VDesign_BookmePro_Model_Observer extends VDesign_Bookme_Model_Observer
{
	
	public function saveBookOptions(Varien_Event_Observer $observer){
	
		$order = $observer->getEvent()->getOrder();
	
		date_default_timezone_set(Mage::app()->getStore()->getConfig('general/locale/timezone'));
		$dateFrom = '';
		$dateTo   = '';
		try{
			$book = Mage::getModel('bookme/book');
			$book->setOrderId($order->getId());
			$book->setCustomerFirstname($order->getCustomerFirstname());
			$book->setCustomerLastname($order->getCustomerLastname());
			$book->setCustomerEmail($order->getCustomerEmail());
			$book->setCustomerPhone($order->getBillingAddress()->getData('telephone'));
			$book->save();
				
			foreach ($order->getAllItems() as $item){
				if($item->getProduct()->getTypeId() == 'booking'){
						
					$product = Mage::getModel('catalog/product')->load($item->getProduct()->getId());
						
					$poptions = $item->getProductOptions();
					$options = array();
					if(is_array($poptions) && isset($poptions['options']))
						$options = $poptions['options'];
					else{
						return true;
					}
						
					foreach ($options as $option){
						if($option['option_type'] == 'multidate_type'){
							$data = unserialize($option['value']);
							if($product->getAttributeText('billable_period') == 'Day'){
								$values = explode(',', $data['values']);
							}else{
								$values = explode("#", $data['values']);
							}
							
							$profile_qty = 0;
							foreach ($data['profiles'] as $key => $value)
								$profile_qty += $value;
							
							$qty = ($profile_qty == 0)? $item->getQtyOrdered() : $profile_qty;
							
							$book_type = $product->getAttributeText('billable_period');	
							if($book_type == 'Session'){
								for($i = 0; $i < count($values) - 1; $i++){
									$session = Mage::getModel('bookmepro/session')->load($values[$i]);
									
									$dateFrom = $session['date_from'].' '.date('H:i:s', strtotime($session['time_from']));
									$dateTo = $session['date_to'].' '.date('H:i:s', strtotime($session['time_to']));
										
									$this->saveNBookItem($book->getId(), $product->getId(), $item->getId(), $dateFrom, $dateTo, $qty, $book_type, $session['session_id']);
								}
								break;
							}else{
								if($book_type == 'Adventure')
								{
									$session = Mage::getModel('bookmepro/session')->load($values[1]);
									
									$dateFrom = $session['date_from'].' '.date('H:i:s', strtotime($session['time_from']));
									$dateTo = $session['date_to'].' '.date('H:i:s', strtotime($session['time_to']));
										
									$this->saveNBookItem($book->getId(), $product->getId(), $item->getId(), $dateFrom, $dateTo, $qty, $book_type, $session['session_id']);
									
									break;
								}
								else
								{
									$dateFrom = date('Y-m-d', $values[0] / 1000);
									$dateTo = (count($values) > 1)? date('Y-m-d', $values[count($values) - 2] / 1000) : $dateFrom;
										
									$this->saveNBookItem($book->getId(), $product->getId(), $item->getId(), $dateFrom, $dateTo, $qty, $book_type, null);
									break;
								}
							}
						}
					}
					
					$this->disableProduct($item->getProduct());
				}
			}
				
		}catch (Exception $e){
			Mage::logException($e);
			Mage::throwException(Mage::helper('adminhtml')->__($e));
		}
		return true;
	
	}
	
	public function saveNBookItem($book_id, $product_id, $item_id, $dateFrom, $dateTo, $qty, $book_type, $session_id){
		$helper = Mage::helper('bookme');
		
		$book_item = Mage::getModel('bookme/book_item');
		$book_item->setBookId($book_id);
		$book_item->setProductId($product_id);
		$book_item->setOrderItemId($item_id);
		$book_item->setBookedFrom($helper->formatDate($dateFrom, Varien_Date::DATETIME_INTERNAL_FORMAT));
		$book_item->setBookedTo($helper->formatDate($dateTo, Varien_Date::DATETIME_INTERNAL_FORMAT));
		$book_item->setFromDate($helper->formatDate($dateFrom, Varien_Date::DATE_INTERNAL_FORMAT));
		$book_item->setFromTime(date('H:i:s', strtotime($dateFrom)));
		$book_item->setToDate($helper->formatDate($dateTo, Varien_Date::DATE_INTERNAL_FORMAT));
		$book_item->setToTime(date('H:i:s', strtotime($dateTo)));
		$book_item->setBookType($book_type);
		$book_item->setQty($qty);
		$book_item->save();
		
		if($book_type != 'Day')
		{
			$book_item->setSessionId($session_id);
			$book_item->save();
			
			$session = Mage::getModel('bookmepro/session')->load($session_id);
			$session->setBookedQty($session->getBookedQty() + $qty);
			$session->save();
		}
		
	}
	
	public function disableProduct($product){
		$product = Mage::getModel('catalog/product')->load($product->getId());
		if($product->getTypeId() == 'booking')
		{
			if($product->getAttributeText('billable_period') == 'Session' || $product->getAttributeText('billable_period') == 'Adventure')
			{
				$resource = Mage::getSingleton('core/resource');
				$session_table = $resource->getTableName('bookmepro/session');
		
				$today = Mage::helper('bookme')->formatDate(date('Y-m-d'), Varien_Date::DATE_INTERNAL_FORMAT);
				$id = $product->getId();
		
				$sql = "SELECT `session_id`FROM $session_table WHERE
				`date_from` >= '$today' AND `product_id` = '$id' AND `max_quantity` > `booked_qty`";
		
				$sessionCollection = Mage::getSingleton('core/resource')
				->getConnection('core_read')
				->fetchAll($sql);
				
				if(count($sessionCollection) == 0){
					Mage::getModel('catalog/product_status')
					->updateProductStatus($product->getId(), null, Mage_Catalog_Model_Product_Status::STATUS_DISABLED);
				}
			}elseif($product->getAttributeText('billable_period') == 'Day')
			{
				$to_day = $product->getData('bookable_to');
				if($to_day){
					$now = $this->formatDate(date('Y-m-d'));
					$to_day = $this->formatDate($to_day);
					$available = false;
					for($day = strtotime($now); $day <= strtotime($to_day); $day += 24 * 60 * 60)
					{
						$checked_day = $this->formatDate(date('Y-m-d', $day));
						if(Mage::helper('bookmepro/availible')->isAvailible($product, $checked_day, 1))
						{
							$available = true;
							break;
						}
					}
					if(!$available)
						Mage::getModel('catalog/product_status')
						->updateProductStatus($product->getId(), null, Mage_Catalog_Model_Product_Status::STATUS_DISABLED);
				}
			}
		}
	}
	
	public function formatDate($date)
	{
		if (empty($date)) {
			return null;
		}
		// unix timestamp given - simply instantiate date object
		if (preg_match('/^[0-9]+$/', $date)) {
			$date = new Zend_Date((int)$date);
		}
		// international format
		else if (preg_match('#^\d{4}-\d{2}-\d{2}( \d{2}:\d{2}:\d{2})?$#', $date)) {
			$zendDate = new Zend_Date();
			$date = $zendDate->setIso($date);
		}
		// parse this date in current locale, do not apply GMT offset
		else {
			$date = Mage::app()->getLocale()->date($date,
					Mage::app()->getLocale()->getDateFormat(Mage_Core_Model_Locale::FORMAT_TYPE_SHORT),
					null, false
			);
		}
		return $date->toString(Varien_Date::DATETIME_INTERNAL_FORMAT);
	}
	
	public function cleanCanceledBooks(Varien_Event_Observer $observer){
	
		$item = $observer->getEvent()->getItem();
		if($item->getProduct()->getTypeId() == 'booking')
		{
			$book_items = Mage::getModel('bookme/book_item')->getCollection()
				->addFieldToFilter('order_item_id', $item->getId());
			
			foreach ($book_items as $book_item)
			{
				$session = Mage::getModel('bookmepro/session')->load($book_item['session_id']);
				$session->setBookedQty($session->getBookedQty() - $book_item->getQty());
				$session->save();
				
				$item = Mage::getModel('bookme/book_item')->load($book_item['item_id']);
				
				if($item->getId())
					$item->delete();
			}
		}
		return true;
	}
	
	public function cleanCreditMemoBooks(Varien_Event_Observer $observer){
		$creditmemo = $observer->getEvent()->getCreditmemo();
	
		foreach ($creditmemo->getAllItems() as $item) {
				
			if($item->getOrderItem()->getProduct()->getTypeId() == 'booking')
			{
				$book_items = Mage::getModel('bookme/book_item')->getCollection()
				->addFieldToFilter('order_item_id', $item->getOrderItem()->getId());
				
				foreach ($book_items as $book_item)
				{
					$session = Mage::getModel('bookmepro/session')->load($book_item['session_id']);
					$session->setBookedQty($session->getBookedQty() - $book_item->getQty());
					$session->save();
					
					$item = Mage::getModel('bookme/book_item')->load($book_item['item_id']);
					
					if($item->getId())
						$item->delete();
				}
			}
		}
		return true;
	}
	
	public function validateBookOptions($order){
	
		date_default_timezone_set(Mage::app()->getStore()->getConfig('general/locale/timezone'));
		
		$helper = Mage::helper('bookmepro/availible');
		$qtys = array();
		foreach ($order->getAllItems() as $item){
			if($item->getProduct()->getTypeId() == 'booking'){
				$poptions = $item->getProductOptions();
				$options = array();
				if(is_array($poptions) && isset($poptions['options']))
					$options = $poptions['options'];
				else{
					return true;
				}
	
				foreach ($options as $option){
					if($option['option_type'] == 'multidate_type'){
						
						$product = Mage::getModel('catalog/product')->load($item->getProduct()->getId());
						$data = unserialize($option['value']);
						
						$profile_qty = 0;
						foreach ($data['profiles'] as $key => $value)
							$profile_qty += $value;
						
						if($product->getAttributeText('billable_period') == 'Day'){
							$values = explode(",", $data['values']);
							if(!(count($values) > 1 && is_numeric($values[0])))
								Mage::throwException(Mage::helper('bookme')->__('Booking data are in wrong format.'));
							
							
							$dateFrom = date('Y-m-d H:i:s', $values[0] / 1000);
							
							if(count($values) > 1)
								$dateTo = date('Y-m-d H:i:s', $values[count($values) - 2] / 1000);
							else
								$dateTo = date('Y-m-d H:i:s', $values[0] / 1000);
							
							$timeFrom = strtotime($dateFrom);
							$timeTo = strtotime($dateTo);
							$qtys[$item->getProduct()->getId()] = (isset($qtys[$item->getProduct()->getId()]))? $qtys[$item->getProduct()->getId()] : array();
							
							
							for($i = 0; $i < count($values) - 1; $i++){
								$checkingDate = date('Y-m-d H:i:s', $values[$i] / 1000);
							
								if(isset($qtys[$item->getProduct()->getId()][$checkingDate]))
									$qtys[$item->getProduct()->getId()][$checkingDate] += ($profile_qty > 0)? $profile_qty : $item->getQtyOrdered();
								else
									$qtys[$item->getProduct()->getId()][$checkingDate] = ($profile_qty > 0)? $profile_qty : $item->getQtyOrdered();
								$timeFrom += 24 * 60 * 60;
							}
							
						}else{
							$values = explode("#", $data['values']);
							
							if($product->getAttributeText('billable_period') == 'Session')
							{
								if(!(count($values) > 1 && is_numeric($values[0])))
									Mage::throwException(Mage::helper('bookme')->__('Booking data are in wrong format.'));
							}
							else{
								if(!(count($values) > 1 && is_numeric($values[1])))
									Mage::throwException(Mage::helper('bookme')->__('Booking data are in wrong format.'));
							}
							
							
							$qtys[$item->getProduct()->getId()] = (isset($qtys[$item->getProduct()->getId()]))? $qtys[$item->getProduct()->getId()] : array();
							
							
							
							if($product->getAttributeText('billable_period') == 'Session')
							{
								for($i = 0; $i < count($values) - 1; $i++){
									if(isset($qtys[$item->getProduct()->getId()][$values[$i]]))
										$qtys[$item->getProduct()->getId()][$values[$i]] += ($profile_qty > 0)? $profile_qty : $item->getQtyOrdered();
									else
										$qtys[$item->getProduct()->getId()][$values[$i]] = ($profile_qty > 0)? $profile_qty : $item->getQtyOrdered();
								}
							}else{
								if(isset($qtys[$item->getProduct()->getId()][$values[1]]))
									$qtys[$item->getProduct()->getId()][$values[1]] += ($profile_qty > 0)? $profile_qty : $item->getQtyOrdered();
								else
									$qtys[$item->getProduct()->getId()][$values[1]] = ($profile_qty > 0)? $profile_qty : $item->getQtyOrdered();
							}
							
						}
					}
				}
			}
		}
	
		foreach ($qtys as $product_id => $values){
			foreach ($values as $day => $qty){
				if($helper->isAvailible($product_id, $day, $qty) === false){
					$product = Mage::getModel('catalog/product')->load($product_id);
					Mage::throwException(Mage::helper('bookme')->__($product->getName(). ' can`t be book in that quantity.'));
				}
			}
		}
	
		return true;
	}
	
	
	public function registerReminder($observer){
		date_default_timezone_set(Mage::app()->getStore()->getConfig('general/locale/timezone'));
		$invoice = $observer->getInvoice();
		
		$order = $invoice->getOrder();
		foreach ($order->getAllItems() as $item){
			
			if($item->getProduct()->getTypeId() == 'booking'){
				$poptions = $item->getProductOptions();
				$options = array();
				if(is_array($poptions) && isset($poptions['options']))
					$options = $poptions['options'];
				else{
					return true;
				}
				foreach ($options as $option){
					if($option['option_type'] == 'multidate_type'){
						
						$product = Mage::getModel('catalog/product')->load($item->getProduct()->getId());
						$data = unserialize($option['value']);
						if($product->getAttributeText('billable_period') == 'Adventure'){
							$values = explode('-', $data['values']);
						}else{
							$values = explode(",", $data['values']);
						}
						
						$book_type = $product->getAttributeText('billable_period');
						
						if($book_type == 'Session'){
							$values = explode("#", $values[0]);
							
							for($i = 0; $i < count($values) - 1; $i++){
								$session = Mage::getModel('bookmepro/session')->load($values[$i]);
								
								$dateFrom = $session->getDateFrom().' '.$session->getTimeFrom();
								$dateTo = $session->getDateTo().' '.$session->getTimeTo();
								
								foreach ($product->getData('mail_reminder') as $rem)
								{
									$reminder = Mage::getModel('bookmepro/book_mailreminder_preparation');
									$reminder->setProduct($product);
									$reminder->setOrderId($order->getId());
									$reminder->setFromDate(Mage::helper('bookme')->formatDate($dateFrom, Varien_Date::DATETIME_INTERNAL_FORMAT));
									$reminder->setToDate(Mage::helper('bookme')->formatDate($dateTo, Varien_Date::DATETIME_INTERNAL_FORMAT));
									$reminder->setItemdata($option['value']);
									$reminder->setReminderId($rem['reminder_id']);
									
									$_rem = Mage::getModel('bookmepro/book_mailreminder');
									$_rem->setData($rem);
									
									if($rem['period'] == 0)
										$this->sendReminder($product, $_rem, $reminder);
									else{
										$reminder->save();
									}
								}
								
							}
							break;
						}else{
							if($book_type == 'Adventure')
							{
								$values = explode("#", $values[1]);
								$session = Mage::getModel('bookmepro/session')->load($values[1]);
								$dateFrom = $session->getDateFrom().' '.$session->getTimeFrom();
								$dateTo = $session->getDateTo().' '.$session->getTimeTo();
								
								foreach ($product->getData('mail_reminder') as $rem)
								{
									$reminder = Mage::getModel('bookmepro/book_mailreminder_preparation');
									$reminder->setProduct($product);
									$reminder->setOrderId($order->getId());
									$reminder->setFromDate(Mage::helper('bookme')->formatDate($dateFrom, Varien_Date::DATETIME_INTERNAL_FORMAT));
									$reminder->setToDate(Mage::helper('bookme')->formatDate($dateTo, Varien_Date::DATETIME_INTERNAL_FORMAT));
									$reminder->setItemdata($option['value']);
									$reminder->setReminderId($rem['reminder_id']);
									
									$_rem = Mage::getModel('bookmepro/book_mailreminder');
									$_rem->setData($rem);
									
									if($rem['period'] == 0)
										$this->sendReminder($product, $_rem, $reminder);
									else
										$reminder->save();
								}
								
								break;
							}
							else
							{
								$dateFrom = date('Y-m-d H:i:s', $values[0] / 1000);
								$dateTo = (count($values) > 1)? date('Y-m-d H:i:s', $values[count($values) - 2] / 1000) : $dateFrom;
								
								foreach ($product->getData('mail_reminder') as $rem)
								{
									$reminder = Mage::getModel('bookmepro/book_mailreminder_preparation');
									$reminder->setProduct($product);
									$reminder->setOrderId($order->getId());
									$reminder->setFromDate(Mage::helper('bookme')->formatDate($dateFrom, Varien_Date::DATETIME_INTERNAL_FORMAT));
									$reminder->setToDate(Mage::helper('bookme')->formatDate($dateTo, Varien_Date::DATETIME_INTERNAL_FORMAT));
									$reminder->setItemdata($option['value']);
									$reminder->setReminderId($rem['reminder_id']);
									
									$_rem = Mage::getModel('bookmepro/book_mailreminder');
									$_rem->setData($rem);
									
									if($rem['period'] == 0)
										$this->sendReminder($product, $_rem, $reminder);
									else
										$reminder->save();
								}
								break;
							}
							
						}
						
					}
				}
			}
		}
	}
	
	public function sendreminders(){
		date_default_timezone_set(Mage::app()->getStore()->getConfig('general/locale/timezone'));
		$reminders = Mage::getModel('bookmepro/book_mailreminder')->getCollection();
		$now = date('Y-m-d H:i:s');
		foreach ($reminders as $reminder)
		{
			$period = $reminder->getPeriod();
			$time = strtotime($now) + ($period * 60 * 60);
			
			$prepared_reminders = $this->getAllPrepared($reminder, $time);
			$product = Mage::getModel('catalog/product')->load($reminder->getProductId());
			foreach($prepared_reminders as $prepared_reminder)
			{
				$this->sendReminder($product, $reminder, $prepared_reminder);
			}
		}
	}
	
	
	public function getAllPrepared($reminder, $time)
	{
		date_default_timezone_set(Mage::app()->getStore()->getConfig('general/locale/timezone'));
		$time = Mage::helper('bookme')->formatDate(date('Y-m-d H:i:s', $time), Varien_Date::DATETIME_INTERNAL_FORMAT);
		
		$resource = Mage::getSingleton('core/resource');
		$readConnection = $resource->getConnection('core_read');
    
		
    	$query = "SELECT * FROM " . $resource->getTableName('bookmepro/book_mailreminder_preparation') .
    	" WHERE product_id = ".$reminder->getProductId()." AND reminder_id = ".$reminder->getId()." AND from_date <= '$time'";
    	
    	$results = $readConnection->fetchAll($query);
    	return $results;
	}
	
	
	
	public function sendReminder($product, $reminder, $prepared_reminder)
	{
		$order = Mage::getModel('sales/order')->load($prepared_reminder['order_id']);
		
		$translate = Mage::getSingleton('core/translate');
		/* @var $translate Mage_Core_Model_Translate */
		$translate->setTranslateInline(false);
		
		$mailTemplate = Mage::getModel('core/email_template');
		/* @var $mailTemplate Mage_Core_Model_Email_Template */
		
		$senderName = Mage::getStoreConfig('bookmepro_reminder/reminder/sender_name');
		$senderEmail = Mage::getStoreConfig('bookmepro_reminder/reminder/sender_email');
		$sendCopy = Mage::getStoreConfig('bookmepro_reminder/reminder/send_copy');
		$sendCopyTo = Mage::getStoreConfig('bookmepro_reminder/reminder/send_copy_to');
		
		$sender = array('name' => $senderName,
				'email' => $senderEmail);
		
		$data = unserialize($prepared_reminder['itemdata']);
		$reservation_text = $this->getReservationText($data, $prepared_reminder['order_id']);
		
		if($reminder->getPeriod() < 24)
			$less_than = $reminder->getPeriod().' hours';
		else
			$less_than = ($reminder->getPeriod() / 24).' days';
		
		$productMediaConfig = Mage::getModel('catalog/product_media_config');
		$baseImageUrl = $productMediaConfig->getMediaUrl($product->getImage());
		
		$mailTemplate->sendTransactional($reminder->getEmailId(), $sender, $order->getCustomerEmail(), $order->getCustomerFirstname().' '.$order->getCustomerLastname(), array(
		'less_than' => $less_than,
		'reservation_data' => $reservation_text,
		'product_name' => $product->getName(),
		'product_image' => $baseImageUrl,
		'product_description' => $product->getShortDescription(),
		'product_url' => $product->getProductUrl(),
		'product_name' => $product->getName(),
		'customer_name' => $order->getCustomerFirstname().' '.$order->getCustomerLastname()
		), Mage::app()->getStore()->getId());
		
		if($sendCopy == 1){
			$mailTemplate->sendTransactional($reminder->getEmailId(), $sender, $sendCopyTo, $order->getCustomerFirstname().' '.$order->getCustomerLastname(), array(
					'less_than' => $less_than,
					'reservation_data' => $reservation_text,
					'product_name' => $product->getName(),
					'product_image' => $baseImageUrl,
					'product_description' => $product->getShortDescription(),
					'product_url' => $product->getProductUrl(),
					'product_name' => $product->getName(),
					'customer_name' => $order->getCustomerFirstname().' '.$order->getCustomerLastname()
			), Mage::app()->getStore()->getId());
		}
		
		$translate->setTranslateInline(true);
		
		$prepared_r = Mage::getModel('bookmepro/book_mailreminder_preparation')->load($prepared_reminder['preparation_id']);
		$prepared_r->delete();
	}
	
	public function getReservationText($data, $order_id){
		date_default_timezone_set(Mage::app()->getStore()->getConfig('general/locale/timezone'));
		$out = '';
		$p_out = '';
		$helper = Mage::helper('bookmepro');
		$order = Mage::getModel('sales/order')->load($order_id);
		$store = Mage::getModel('core/store')->load($order->getStoreId());
		
		$profiles = $data['profiles'];
    	foreach ($profiles as $key => $value)
			if($value > 0)
			{
				$id = explode("#", $key);
				$p_out .= $helper->getProfileTranslation($id[1], $store->getCode())." : ".$value."<br />";
			}
    			
    	if (strpos($data['values'], '-') !== FALSE){ //adventure
    		$values = explode("#", $data['values']);
    		$session = Mage::getModel('bookmepro/session')->load($values[1]);
    		
    		$from = strtotime($session['date_from'].' '.date('H:i:s', strtotime($session['time_from'])));
    		$to = strtotime($session['date_to'].' '.date('H:i:s', strtotime($session['time_to'])));
    		$out .= Mage::helper('core')->formatDate(date("d-m-Y H:i:s",$from), 'medium', true)." - ".Mage::helper('core')->formatDate(date("d-m-Y H:i:s",$to), 'medium', true)."<br />";
    	}
    	
    	
    	if (strpos($data['values'], ',') !== FALSE){ //date
    		$values = explode(",", $data['values']);
    		$from = date("d-m-Y", $values[0]/1000);
    		$to = date("d-m-Y", $values[count($values)-2]/1000);
    		$out .= Mage::helper('core')->formatDate($from, 'medium', false)." - ".Mage::helper('core')->formatDate($to, 'medium', false)."<br />";
    	}
    	
    	
    	if (strpos($data['values'], '#') !== FALSE && strpos($data['values'], '-') == FALSE){ //session
    		$values = explode("#", $data['values']);
    	
    			foreach ($values as $id)
    			{
    				$session = Mage::getModel('bookmepro/session')->load($id);
    				if($session->getId())
    					$out .= Mage::helper('core')->formatDate($session['date_from']." ".date('H:i:s', strtotime($session['time_from'])), 'medium', true)."<br />";
    			}
    	}
    	
     	return $out.$p_out;
	}
	
	
	public function generateSessions(){
		
		$gen_interval = Mage::helper('bookmepro')->getTimeFromAttributeSession(Mage::getStoreConfig('bookmepro_scheduler/task/generate_session'));
		Mage::log($gen_interval);
		$helper = Mage::helper('bookmepro/session');
		$products = Mage::getModel('catalog/product')->getCollection()
		->addFieldToFilter('type_id', array('eq' => 'booking'));
		
		foreach ($products as $product)
		{
			if($product->getAttributeText('billable_period') != 'Day')
			{
				$helper->generateSessions($product, $gen_interval);
			}
		}
	}
	
	
	public function autoCancel(){
		date_default_timezone_set(Mage::app()->getStore()->getConfig('general/locale/timezone'));
		$pp = Mage::helper('bookmepro')->getTimeFromOrderAttribute(Mage::getStoreConfig('bookmepro_scheduler/task/pending_payment'));
		$p = Mage::helper('bookmepro')->getTimeFromOrderAttribute(Mage::getStoreConfig('bookmepro_scheduler/task/pending'));
		
		$pending_payment = strtotime(date('Y-m-d H:i:s')) - $pp;
		$pending = strtotime(date('Y-m-d H:i:s')) - $p;
		
		$pending_payment = date('Y-m-d H:i:s', $pending_payment);
		$pending = date('Y-m-d H:i:s', $pending);
		
		if($pp > 0){
			$orderCollection = Mage::getResourceModel('sales/order_collection');
	        $orderCollection
	        ->join(array('b' => 'bookme/book'), 'main_table.entity_id = b.order_id', array(
	        	'book_id' => 'book_id'
	        ))
	        ->addFieldToFilter('status', 'pending_payment')
			->addFieldToFilter('created_at', array('lt' =>  $pending_payment));
			
	        
			foreach($orderCollection->getItems() as $order)
			{
				$orderModel = Mage::getModel('sales/order');
				$orderModel->load($order['entity_id']);
				if(!$orderModel->canCancel())
					continue;
				
				$orderModel->cancel();
				$orderModel->setStatus('canceled');
				$orderModel->save();
				
				$book_items = Mage::getModel('bookme/book_item')->getCollection()
				->addFieldToFilter('book_id', $order['book_id']);
				foreach ($book_items as $item)
				{
					$session = Mage::getModel('bookmepro/session')->load($item->getSessionId());
					$session->setBookedQty($session->getBookedQty() - $item->getQty());
					$session->save();
				
					if($item->getId())
						$item->delete();
				}
					
				$book = Mage::getModel('bookme/book')->load($order['book_id']);
				$book->delete();
			}
		}
		
		if($p > 0){
			$orderCollection = Mage::getResourceModel('sales/order_collection');
			$orderCollection
			->join(array('b' => 'bookme/book'), 'main_table.entity_id = b.order_id')
			->addFieldToFilter('status', 'pending')
			->addFieldToFilter('created_at', array('lt' =>  $pending));
			
			foreach($orderCollection->getItems() as $order)
			{
				$orderModel = Mage::getModel('sales/order');
				$orderModel->load($order['entity_id']);
				if(!$orderModel->canCancel())
					continue;
				
				$orderModel->cancel();
				$orderModel->setStatus('canceled');
				$orderModel->save();
				
				$book_items = Mage::getModel('bookme/book_item')->getCollection()
				->addFieldToFilter('book_id', $order['book_id']);
				foreach ($book_items as $item)
				{
					$session = Mage::getModel('bookmepro/session')->load($item->getSessionId());
					$session->setBookedQty($session->getBookedQty() - $item->getQty());
					$session->save();
					
					if($item->getId())
						$item->delete();
				}
				
				$book = Mage::getModel('bookme/book')->load($order['book_id']);
				$book->delete();
			}
		}
	}
	
	public function duplicateProduct($observer){
		$current_product = $observer->getCurrentProduct();
		$new_product = $observer->getNewProduct();
		
		//price profiles
		$profiles = $current_product->getData('price_profile');
		
		for ($i = 0; $i < count($profiles); $i++)
		{
			unset($profiles[$i]['profile_id']);
			for ($j = 0; $j < count($profiles[$i]['translations']); $j++)
			{
				unset($profiles[$i]['translations'][$j]['trans_id']);
			}
		}
		
		$new_product->setData('price_profile', $profiles);
		
		//email reminders
		$reminders = $current_product->getData('mail_reminder');
		for ($i = 0; $i < count($reminders); $i++)
		{
			unset($reminders[$i]['reminder_id']);
		}
		
		$new_product->setData('mail_reminder', $reminders);
		$observer->setNewProduct($new_product);
		parent::duplicateProduct($observer);
		return $observer;
	}
	
}