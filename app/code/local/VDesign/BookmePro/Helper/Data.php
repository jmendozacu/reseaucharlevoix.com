<?php


class VDesign_BookmePro_Helper_Data extends Mage_Core_Helper_Abstract{
	
	public function getProfilePrice($price, $profile){
		
		if(!isset($profile['amount']))
			$profile['amount'] = 0;
		
		if($profile['move'] == "1"){
			if($profile['amount_type'] == "1")
				$price = $price + ($price * ($profile['amount'] / 100));
			else
				$price = ($price + $profile['amount']);
		}else{
			if($profile['amount_type'] == "1")
				$price = $price - ($price * ($profile['amount'] / 100));
			else
				$price = $price - ((isset($profile['amount']))? $profile['amount'] : 0);
		}
	
		return $price;
	}
	
	public function getProfileTranslations($profile_id){
		$translations = Mage::getModel('bookmepro/priceprofile_store')->getCollection()
		->addFieldToFilter('profile_id', $profile_id);
	
		$return = array();
	
		foreach ($translations as $trans)
		{
			$return[$trans['code']] = $trans['name'];
		}
		return $return;
	}
	
	public function getProfileTranslation($profile_id, $store_code){
		$translation = Mage::getModel('bookmepro/priceprofile_store')->getCollection()
		->addFieldToFilter('profile_id', $profile_id)
		->addFieldToFilter('code', $store_code)
		->getFirstItem();
	
	
		return $translation['name'];
	}
	
	public function getAvailableSessionList($book_item){
		date_default_timezone_set(Mage::app()->getStore()->getConfig('general/locale/timezone'));
		$output = array();
		$output[$book_item->getData('session_id')] = Mage::helper('core')->formatDate($book_item->getData('booked_from'), 'medium', true);
		
		$sessions = Mage::getModel('bookmepro/session')->getCollection()
		->addFilter("product_id", $book_item->getData('product_id'));
		
		$now = strtotime(date('Y/m/d'));
		
		foreach ($sessions as $session)
		{
			$b_qty = Mage::helper('bookmepro/availible')->getSessionBookedQty($session);
			if($now <= strtotime($session['date_from']) && $session['max_quantity'] >= ($b_qty + $book_item->getQty()))
				$output[$session['session_id']] = Mage::helper('core')->formatDate($session['date_from'].' '.date('H:i:s', strtotime($session['time_from'])), 'medium', true);
		}
		
		return $output;
	}
	
	public function getAvailableDateList($book_item){
		date_default_timezone_set(Mage::app()->getStore()->getConfig('general/locale/timezone'));
		$output = array();
		$from_to = Mage::helper('core')->formatDate($book_item->getBookedFrom(), 'medium', false).' - '.Mage::helper('core')->formatDate($book_item->getBookedTo(), 'medium', false);
		$output[strtotime($book_item->getData('booked_from'))] = $from_to;
		$helper = Mage::helper('bookmepro/availible');
		$product = Mage::getModel('catalog/product')->load($book_item->getProductId());
		
		$from = strtotime($book_item->getBookedFrom());
		$to = strtotime($book_item->getBookedTo());
		
		$diff = $to - $from; 
		$max_date = strtotime(date('Y-m-d')) + (93 * 24 * 60 *60);
		
		if($product->getData('bookable_to'))
			$max_date = (strtotime($product->getData('bookable_to')) < $max_date)? strtotime($product->getData('bookable_to')) : $max_date;
		
		$ex_days = $helper->getExcludeDays($book_item->getProductId(), date('m'), date('Y'));
		$ex_days .= ','.$helper->getExcludeDays($book_item->getProductId(), date('m') + 1, date('Y'));
		$ex_days = explode(",", $ex_days);
		for($index = 0; $index < count($ex_days); $index++)
			$ex_days[$index] = strtotime($ex_days[$index]);
		
		$book_days = $helper->getFullyBookedDays($product, date('m'), date('Y'));
		foreach ($book_days as $key => $value)
			if($value + $book_item->getQty() >= $product->getData('bookable_qty'))
				$ex_days[] = $key;
			
		$book_days = $helper->getFullyBookedDays($product, date('m') + 1, date('Y'));
		foreach ($book_days as $key => $value)
			if($value + $book_item->getQty() >= $product->getData('bookable_qty'))
				$ex_days[] = $key;
			
		$book_days = $helper->getFullyBookedDays($product, date('m') + 2, date('Y'));
		foreach ($book_days as $key => $value)
			if($value + $book_item->getQty() >= $product->getData('bookable_qty'))
				$ex_days[] = $key;
		
		$book_days = $helper->getFullyBookedDays($product, date('m') + 3, date('Y'));
		foreach ($book_days as $key => $value)
			if($value + $book_item->getQty() >= $product->getData('bookable_qty'))
				$ex_days[] = $key;
			
		for($date = strtotime(date('Y-m-d')); ($date + $diff) <= $max_date; $date = strtotime(date('Y-m-d', $date) . ' + 1 day'))
		{
			$bool = false;
			for($d = $date; $d <= ($date + $diff); $d = strtotime(date('Y-m-d', $d) . ' + 1 day'))
			{
				if(in_array($d, $ex_days))
				{
					$bool = true;
					break;
				}
			}
			
			if(!$bool)
			{
				$output[$date] = Mage::helper('core')->formatDate(date('Y-m-d', $date), 'medium', false).' - '.Mage::helper('core')->formatDate(date('Y-m-d', $date + $diff), 'medium', false);
			}
		}
		
		return $output;
	}
	
	public function getMailTemplates(){
		$emails = Mage::getModel('core/email_template')->getCollection();
		$out = array();
		$out[] = array('value'=>'', 'label'=>'--select email template--');
		foreach ($emails as $email)
		{
			$out[] = array('value'=>$email['template_id'], 'label'=>$email['template_code']);
		}
			
		return $out;
	}
	
	public function sendMassReminders($ids, $template_id){
		foreach ($ids as $id)
		{
			$items = Mage::getModel('bookme/book_item')->getCollection()
			->addFieldToFilter('session_id', array('eq' => $id));
				
			$session = Mage::getModel('bookmepro/session')->load($id);
			$product = Mage::getModel('catalog/product')->load($session->getProductId());
				
			foreach ($items as $item)
			{
				$book = Mage::getModel('bookme/book')->load($item['book_id']);
				$order_item = Mage::getModel('sales/order_item')->load($item['order_item_id']);
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
				/* @var $translate Mage_Core_Model_Translate */
				$translate->setTranslateInline(false);
		
				$mailTemplate = Mage::getModel('core/email_template');
				/* @var $mailTemplate Mage_Core_Model_Email_Template */
				
				if(Mage::getStoreConfig('bookmepro_reminder/reminder/sender_name') == '' ||  Mage::getStoreConfig('bookmepro_reminder/reminder/sender_email') == '')
				{
					return false;
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
					Mage::throwException($e);
				}
				
			}
		}
		return true;
	}
	
	public function getTimeFromAttribute($data){
		$hour = 60 * 60;
		switch ($data) {
		    case '1 day':
		        return 24 * $hour;
		    case '2 days':
		        return 2 * 24 * $hour;
	        case '3 days':
	        	return 3 * 24 * $hour;
        	case '4 days':
        		return 4 * 24 * $hour;
        	case '5 days':
        		return 5 * 24 * $hour;
        	case '1 week':
        		return 7 * 24 * $hour;
        	default:
        		return 0;
		}
	}
	
	public function getTimeFromAttributeSession($data){
		$hour = 60 * 60;
		switch ($data) {
			case 1:
				return 24 * $hour;
			case 2:
				return 7 * 24 * $hour;
			case 3:
				return 14 * 24 * $hour;
			case 4:
				return 30 * 24 * $hour;
			default:
				return 0;
		}
	}
	
	public function getTimeFromOrderAttribute($data){
		$hour = 60 * 60;
		switch ($data) {
			case 1:
				return $hour;
			case 2:
				return 2 * $hour;
			case 3:
				return 6 * $hour;
			case 4:
				return 12 * $hour;
			case 5:
				return 24 * $hour;
			case 6:
				return 2 * 24 * $hour;
			case 7:
				return 4 * 24 * $hour;
			default:
				return 0;
		}
	}
}