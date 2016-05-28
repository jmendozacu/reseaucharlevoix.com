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
class VDesign_BookmePro_Helper_Availible extends VDesign_Bookme_Helper_Availible{
	
	
	public function updateSessionReservation($session_id, $old_session_id, $booked_qty, $book_item){
		$helper = Mage::helper('bookme');
		
		$session = Mage::getModel('bookmepro/session')->load($session_id)->getData();
		$booked_qty = $this->getSessionBookedQty($session);
			
		if($session['max_quantity'] >= ($booked_qty + $book_item->getQty()))
		{
			/**
			 * UPDATE ORDER ITEM CUSTOM OPTION VALUE
			*/
		
			$order_item = Mage::getModel('sales/order_item')->load($book_item->getOrderItemId());
			$option = $order_item->getProductOptions();
		
			for ($i = 0; $i < count($option['options']); $i++)
			{
				if($option['options'][$i]['option_type'] == 'multidate_type')
				{
					$u_value = unserialize($option['options'][$i]['value']);
					$value = explode("#", $u_value['values']);
				
					for($index = 0; $index < count($value); $index++)
					{
						if($value[$index] == $old_session_id)
							$value[$index] = $session_id;
					}
				
					$value = implode("#", $value);
					$u_value['values'] = $value;
					$option['options'][$i]['value'] = serialize($u_value);
					$option['options'][$i]['print_value'] = serialize($u_value);
					$option['options'][$i]['option_value'] = serialize($u_value);
				}
			}
			$order_item->setData('product_options', serialize($option));
			$order_item->save();
			
			/**
			 * UPDATE BOOK ITEM ENTITY VALUES
			 */
			
			$book_item->setSessionId($session_id);
			
			$book_item->setData('booked_from', $helper->formatDate($session['date_from'].' '.$session['time_from'], Varien_Date::DATETIME_INTERNAL_FORMAT));
			$book_item->setData('booked_to', $helper->formatDate($session['date_to'].' '.$session['time_to'], Varien_Date::DATETIME_INTERNAL_FORMAT));
			$book_item->setData('from_date', $helper->formatDate($session['date_from'], Varien_Date::DATE_INTERNAL_FORMAT));
			$book_item->setData('to_date', $helper->formatDate($session['date_to'], Varien_Date::DATE_INTERNAL_FORMAT));
			$book_item->setData('from_time', $session['time_from']);
			$book_item->setData('to_time', $session['time_to']);
			$book_item->save();
				
			$session = Mage::getModel('bookmepro/session')->load($old_session_id);
			$session->setBookedQty($session->getBookedQty() - $book_item->getQty());
			$session->save();
			
			$session = Mage::getModel('bookmepro/session')->load($session_id);
			$session->setBookedQty($session->getBookedQty() + $book_item->getQty());
			$session->save();
		
			Mage::getSingleton('adminhtml/session')->addSuccess(
			Mage::helper('bookmepro')->__('Reservation was updated.'));
		}else{
				Mage::getSingleton('adminhtml/session')->addError(
			Mage::helper('bookmepro')->__('Such quantity is no more available.'));
		}
	}
	
	public function updateAdventureReservation($session_id, $old_session_id, $booked_qty, $book_item){
		$helper = Mage::helper('bookme');
		$session = Mage::getModel('bookmepro/session')->load($session_id)->getData();
		$booked_qty = $this->getSessionBookedQty($session);
			
		if($session['max_quantity'] >= ($booked_qty + $book_item->getQty()))
		{
			/**
			 * UPDATE BOOK ITEM ENTITY VALUES
			 */
	
			$book_item->setSessionId($session_id);
			$book_item->setData('booked_from', $helper->formatDate($session['date_from'].' '.$session['time_from'], Varien_Date::DATETIME_INTERNAL_FORMAT));
			$book_item->setData('booked_to', $helper->formatDate($session['date_to'].' '.$session['time_to'], Varien_Date::DATETIME_INTERNAL_FORMAT));
			$book_item->setData('from_date', $helper->formatDate($session['date_from'], Varien_Date::DATE_INTERNAL_FORMAT));
			$book_item->setData('to_date', $helper->formatDate($session['date_to'], Varien_Date::DATE_INTERNAL_FORMAT));
			$book_item->setData('from_time', $session['time_from']);
			$book_item->setData('to_time', $session['time_to']);
			$book_item->save();
			
			$session = Mage::getModel('bookmepro/session')->load($old_session_id);
			$session->setBookedQty($session->getBookedQty() - $book_item->getQty());
			$session->save();
				
			$session = Mage::getModel('bookmepro/session')->load($session_id);
			$session->setBookedQty($session->getBookedQty() + $book_item->getQty());
			$session->save();
	
			/**
			 * UPDATE ORDER ITEM CUSTOM OPTION VALUE
			*/
	
			$order_item = Mage::getModel('sales/order_item')->load($book_item->getOrderItemId());
			$option = $order_item->getProductOptions();
	
			for ($i = 0; $i < count($option['options']); $i++)
			{
				if($option['options'][$i]['option_type'] == 'multidate_type')
				{
					$u_value = unserialize($option['options'][$i]['value']);
					
					$value = explode("#", $u_value['values']);
	
					if($value[1] == $old_session_id)
						$value[1] = $session_id;
	
					$value = implode("#", $value);
					$u_value['values'] = $value;
					$option['options'][$i]['value'] = serialize($u_value);
					$option['options'][$i]['print_value'] = serialize($u_value);
					$option['options'][$i]['option_value'] = serialize($u_value);
				}
			}
			$order_item->setData('product_options', serialize($option));
			$order_item->save();
	
			Mage::getSingleton('adminhtml/session')->addSuccess(
			Mage::helper('bookmepro')->__('Reservation was updated.'));
		}else{
			Mage::getSingleton('adminhtml/session')->addError(
			Mage::helper('bookmepro')->__('Such quantity is no more available.'));
		}
	}
	
	public function updateBookReservation($from_to, $book_item){
		$helper = Mage::helper('bookme');
		date_default_timezone_set(Mage::app()->getStore()->getConfig('general/locale/timezone'));
		
		$from = strtotime($book_item->getData('booked_from'));
		$to = strtotime($book_item->getData('booked_to'));
		if($from == $from_to){
			Mage::getSingleton('adminhtml/session')->addSuccess(
			Mage::helper('bookmepro')->__('Reservation closed without change.'));
			return;
		}
		
		$product = Mage::getModel('catalog/product')->load($book_item->getProductId());
		$diff = strtotime($book_item->getBookedTo()) - strtotime($book_item->getBookedFrom());
		
		$to = $from_to + $diff;
		for($d = $from_to; $d <= $to; $d += 24 * 60 * 60)
		{
			if(!$this->isAvailible($product, date('Y-m-d H:i:s', $d), $book_item->getQty())){
				Mage::getSingleton('adminhtml/session')->addError(
			Mage::helper('bookmepro')->__('Such quantity is no more available.'));
				return;
			}
		}
		/**
		 * UPDATE BOOK ITEM ENTITY VALUES
		 */
	
		$book_item->setSessionId($session_id);
		
		$book_item->setData('booked_from', $helper->formatDate(date('Y-m-d H:i:s', $from_to), Varien_Date::DATETIME_INTERNAL_FORMAT));
		$book_item->setData('booked_to', $helper->formatDate(date('Y-m-d H:i:s', $to), Varien_Date::DATETIME_INTERNAL_FORMAT));
		$book_item->setData('from_date', $helper->formatDate(date('Y-m-d', $from_to), Varien_Date::DATE_INTERNAL_FORMAT));
		$book_item->setData('to_date', $helper->formatDate(date('Y-m-d', $to), Varien_Date::DATE_INTERNAL_FORMAT));
		$book_item->setData('from_time', date('H:i:s', $from_to));
		$book_item->setData('to_time', date('H:i:s', $to));
		
		$book_item->save();
	
		/**
		 * UPDATE ORDER ITEM CUSTOM OPTION VALUE
		*/
	
		$order_item = Mage::getModel('sales/order_item')->load($book_item->getOrderItemId());
		$option = $order_item->getProductOptions();
	
		for ($i = 0; $i < count($option['options']); $i++)
		{
		if($option['options'][$i]['option_type'] == 'multidate_type')
		{
			$u_value = unserialize($option['options'][$i]['value']);
			
			$val = '';
			for($d = $from_to; $d <= $to; $d += 24 * 60 * 60)
				$val .= ($d * 1000).',';
			
			$u_value['values'] = $val;
			$option['options'][$i]['value'] = serialize($u_value);
			$option['options'][$i]['print_value'] = serialize($u_value);
			$option['options'][$i]['option_value'] = serialize($u_value);
		}
		}
		$order_item->setData('product_options', serialize($option));
		$order_item->save();
	
		Mage::getSingleton('adminhtml/session')->addSuccess(
		Mage::helper('bookmepro')->__('Reservation was updated.'));
		
	}
	
	
	public function isAvailible($product, $date, $qty){
		$helper = Mage::helper('bookme');
		if(is_numeric($product))
			$product = Mage::getModel('catalog/product')->load($product);
		else
			$product = Mage::getModel('catalog/product')->load($product->getId());
		
		if(!$product->getId())
			return false;
		
		if($product->getAttributeText('billable_period') == 'Day')
		{
			if($this->isInExcluded($product, $date))
				return false;
			
			$resource = Mage::getSingleton('core/resource');
			$booke_item_table = $resource->getTableName('bookme/book_item');
			
			$date = $helper->formatDate($date, Varien_Date::DATE_INTERNAL_FORMAT);
			
			$sql = "SELECT * FROM $booke_item_table".
					" WHERE `product_id` = ".$product->getId().
					" AND `booked_from` <= '$date'".
					" AND `booked_to` >= '$date'";
			
			$bookCollection = Mage::getSingleton('core/resource')
					->getConnection('core_read')
							->fetchAll($sql);
	
			$bookedQty = 0;
			foreach ($bookCollection as $item){
				$bookedQty += $item['qty'];
			}
			$bookedQty += $this->addRefreshingItems($product, $date);
			
			$qty = $bookedQty + $qty;
			$availible = $product->getData('bookable_qty');
	
			if ($qty <= $availible){
				return true;
			}
			else{
				return false;
			}
			
		}else{
			$session = Mage::getModel('bookmepro/session')->load($date);
			return ($session['max_quantity'] >= $session['booked_qty'] + $qty);
		}
	}
	
	
// 	public function isAvailable($product, $date_from, $date_to, $qty){
// 		if(is_numeric($product))
// 			$product = Mage::getModel('catalog/product')->load($product);
// 		else
// 			$product = Mage::getModel('catalog/product')->load($product->getId());
	
// 		if($this->isInExcluded($product, $date))
// 			return false;
	
// 		$resource = Mage::getSingleton('core/resource');
// 		$booke_item_table = $resource->getTableName('bookme/book_item');
	
// 		$sql = "SELECT * FROM $booke_item_table".
// 				" WHERE `product_id` = ".$product->getId().
// 				" AND `booked_from` <= '$date'".
// 				" AND `booked_to` >= '$date'";
	
// 				$bookCollection = Mage::getSingleton('core/resource')
// 						->getConnection('core_read')
// 						->fetchAll($sql);
	
// 				$bookedQty = 0;
// 				foreach ($bookCollection as $item){
// 					$bookedQty += $item['qty'];
// 				}
	
// 				$qty = $bookedQty + $qty;
// 				$availible = $product->getData('bookable_qty');
	
// 				if ($qty <= $availible){
// 					return true;
// 				}
// 				else{
// 					return false;
// 				}
	
// 				return true;
// 	}
	
	
	public function getAvailibleQty($product, $date){
		$helper = Mage::helper('bookme');
		$date = $helper->formatDate($date, Varien_Date::DATETIME_INTERNAL_FORMAT);
		
		$resource = Mage::getSingleton('core/resource');
		$booke_item_table = $resource->getTableName('bookme/book_item');
		
		$sql = "SELECT * FROM $booke_item_table".
				" WHERE `product_id` = ".$product->getId().
				" AND `booked_from` <= '$date'".
				" AND `booked_to` >= '$date'";
		
		$bookCollection = Mage::getSingleton('core/resource')
				->getConnection('core_read')
				->fetchAll($sql);
	
		$bookedQty = 0;
		foreach ($bookCollection as $item){
			$bookedQty += $item['qty'];
		}
		
		$bookedQty += $this->addRefreshingItems($product, $date);
		
		$availible = $product->getData('bookable_qty');

		return $availible - $bookedQty;
	}
	
	public function getAdvAvailibleQty($product, $date_from, $date_to){
		
		$helper = Mage::helper('bookme');
		$date_from = $helper->formatDate($date_from, Varien_Date::DATETIME_INTERNAL_FORMAT);
		$date_to = $helper->formatDate($date_to, Varien_Date::DATETIME_INTERNAL_FORMAT);
	
		$resource = Mage::getSingleton('core/resource');
		$booke_item_table = $resource->getTableName('bookme/book_item');
	
		$sql = "SELECT * FROM $booke_item_table".
				" WHERE `product_id` = ".$product->getId().
				" AND `booked_from` = '$date_from'".
				" AND `booked_to` = '$date_to'";
	
		$bookCollection = Mage::getSingleton('core/resource')
		->getConnection('core_read')
		->fetchAll($sql);
	
		$bookedQty = 0;
		foreach ($bookCollection as $item){
			$bookedQty += $item['qty'];
		}
	
		$availible = $product->getData('bookable_qty');
		
		$advs = $this->getAdventures($product->getId(), $date_from, 0);
		
		return $availible - $bookedQty;
	}
	
	public function isInExcluded($product, $date){
		$helper = Mage::helper('bookme');
		$date = $helper->formatDate($date, Varien_Date::DATE_INTERNAL_FORMAT);
		
		$exludeDays = $product->getData('exclude_day');
		foreach ($exludeDays as $exday){
			if($exday['period_type'] == 1){
				$exfrom = $exday['from_date'];
				$exTo = $exday['to_date'];
				if(strtotime($exfrom) <= strtotime($date) && strtotime($exTo) >= strtotime($date))
					return true;
				else continue;
			}
			
			if($exday['period_type'] == 2){
				$exday = strtotime($exday['value']);
				$exday = date('Y-m-d', $exday);
				if(strtotime($date) == strtotime($exday))
					return true;
				else continue;
			}
			
			if($exday['period_type'] == 3){
				$dw = date( "w", strtotime($date));
				if($exday['value'] == $dw)
					return true;
				else continue;
			}
			
			if($exday['period_type'] == 4){
				$day = explode("-", date('Y-m-d', strtotime($date)));
				if($day[2] == $exday['value'])
					return true;
				else continue;
			}
		}
		return false;
	}
	
	public function getNameOfDay($day){
		if($day == 1)
			return 'monday';
		if($day == 2)
			return 'tuesday';
		if($day == 3)
			return 'wednesday';
		if($day == 4)
			return 'thuersday';
		if($day == 5)
			return 'friday';
		if($day == 6)
			return 'saturday';
		if($day == 7)
			return 'sunday';
	}
	
	public function getExcludeDays($product_id, $month, $year){
		
		$product = Mage::getModel('catalog/product')->load($product_id);
		
		if(!$product->getId())
			return '';
		
		date_default_timezone_set(Mage::app()->getStore()->getConfig('general/locale/timezone'));
		$out = '';
		foreach ($product->getData('exclude_day') as $day){
			if($day['period_type'] == 1)
				$out .= $this->addExFromRange($day);
			if($day['period_type'] == 2)
				$out .= date('Y/m/d', strtotime($day['value'])).',';
			if($day['period_type'] == 3)
				$out .= $this->addExFromWeek($month, $day['value'], $year);
			if($day['period_type'] == 4)
				$out .= $this->addExFromMonth($month, $day['value'], $year).',';
		}
		
		if($product->getAttributeText('billable_period') != 'Session')
		{
			foreach ($this->getFullyBookedDays($product, $month, $year) as $key => $value){
				if($value >= $product->getData('bookable_qty'))
					$out .= date('Y/m/d', $key).',';
			}	
		}
		
		
		$out = ($out != '')? substr($out, 0, strlen($out) - 1) : 'null';
		
		return $out;
	}
	
	public function getFullyBookedDays($product, $month, $year){
		
		$id = $product->getId();
		date_default_timezone_set(Mage::app()->getStore()->getConfig('general/locale/timezone'));
		//$month ++;
		$helper = Mage::helper('bookme');
		
		$s_month = ($month < 10)? '0'.$month : $month;
		$from_date = $year.'-'.$s_month.'-01 00:00:00';
		$from_date = $helper->formatDate($from_date, Varien_Date::DATETIME_INTERNAL_FORMAT);
		$month++;
		$s_month = ($month < 10)? '0'.$month : $month;
		$to_date = $year.'-'.$s_month.'-01 00:00:00';
		$to_date = $helper->formatDate($to_date, Varien_Date::DATETIME_INTERNAL_FORMAT);
		
		$resource = Mage::getSingleton('core/resource');
		$booke_item_table = $resource->getTableName('bookme/book_item');
		
		$sql = "SELECT `booked_from`, `booked_to`, `qty` 
				FROM $booke_item_table WHERE (
					(`booked_from` >= '$from_date' AND `booked_from` <= '$to_date')
						 OR 
					(`booked_to` >= '$from_date' AND `booked_to` <= '$to_date')
				) AND `product_id` = $id";
		
		$bookCollection = Mage::getSingleton('core/resource')
			->getConnection('core_read')
			->fetchAll($sql);
		
		$bookdata = array();
		foreach ($bookCollection as $item){
			$from = strtotime($item['booked_from']);
			$to = $this->addRefreshDay($product, strtotime($item['booked_to']));
			
			for($i = $from; $i <= $to; $i += 24 * 60 *60){
				$bookdata[$i] = (isset($bookdata[$i]))? $bookdata[$i] + $item['qty'] : $item['qty'];
			}
			
		}
		return $bookdata;
	}
	
	public function addExFromRange($day){
		$timeFrom = strtotime($day['from_date']);
		$timeTo   = strtotime($day['to_date']);
		$out = '';
		for($i = $timeFrom; $i <= $timeTo; $i += 24 * 60 * 60)
			$out .= date('Y/m/d', $i).',';
		$out .= date('Y/m/d', $timeTo).',';
		return $out;
	}
	
	public function addExFromMonth($month, $day, $year){
		if($day == 32){
			$time = strtotime($year.'-'.($month + 1).'-01');
			$date = date('Y-m-t', $time);
			$time = strtotime($date) * 1000;
		}else
			$time = strtotime($year.'-'.($month + 1).'-'.$day)*1000;
		return date('Y/m/d', $time / 1000);
	}
	
public function addExFromWeek($month, $day, $year){
		
		date_default_timezone_set(Mage::app()->getStore()->getConfig('general/locale/timezone'));
		$out = ''; $day = ($day == 7)? 0 : $day;
		$first_d_in_m = strtotime($year.'-'.$month.'-01 00:00:00');
		
		$day_in_week = (int) date('w', $first_d_in_m);
		
		$first_d_in_m = $first_d_in_m * 1000;
		for($i = ($day_in_week > $day)? ($day_in_week - 7) : $day_in_week; $i < $day; $i++){
			$first_d_in_m += 24 * 60 * 60 * 1000;
			if($i == 7) $i = 0;
		}
		
		if((int) date('w', $first_d_in_m / 1000) < $day)
			$first_d_in_m += 3 * 60 * 60 * 1000;
		
		if((int) date('w', $first_d_in_m / 1000) > $day)
			$first_d_in_m -= 3 * 60 * 60 * 1000;
		
		$milis = $first_d_in_m;
		for($i = 0; $i <= 5; $i++)
		{
			if(date('m', $milis / 1000) == $month)
				$out .= date('Y/m/d', $milis / 1000).',';
			$milis += 7*24.5*60*60*1000;
		}
		return $out;
	}
	
	
	public function getAdventures($product, $date, $days){
		date_default_timezone_set(Mage::app()->getStore()->getConfig('general/locale/timezone'));
		$helper = Mage::helper('bookme');
		$date = $helper->formatDate(date('Y-m-d', $date / 1000), Varien_Date::DATE_INTERNAL_FORMAT);
		
		$output = '';
		
		$adventures = Mage::getModel('bookmepro/session')->getCollection()
		->addFieldToFilter('product_id', array('eq' => $product->getId()))
		->addFieldToFilter('date_from', array('eq' => $date));
		$output = '';
		
		$note = ($days > 0)? $days.' '.Mage::getStoreConfig('bookmepro_options/locales/days_later', Mage::app()->getStore()->getId()) : (($days != 0)? ($days * (-1)).' '.Mage::getStoreConfig('bookmepro_options/locales/days_earlier', Mage::app()->getStore()->getId()) : '');
		
		foreach ($adventures as $adventure)
		{
			if($adventure['max_quantity'] > $adventure['booked_qty'])
			{
				$_time = explode(" ", $adventure['time_from']);
				$time = explode(":", $_time[1]);
				
				$start_timestamp = strtotime($adventure['time_from']) * 1000;
				$end_timestamp = strtotime($adventure['time_to']) * 1000;
				$start = Mage::helper('core')->formatDate($adventure['date_from'], 'medium', false).' '.Mage::helper('bookme')->__(date('(D)', strtotime($adventure['date_from']))).' '.date("H:i", $start_timestamp/1000);
				$end = Mage::helper('core')->formatDate($adventure['date_to'], 'medium', false).' '.Mage::helper('bookme')->__(date('(D)', strtotime($adventure['date_from']))).' '.date("H:i", $end_timestamp/1000);
				
				$qty = $adventure['max_quantity'] - $adventure['booked_qty'];
				
				$output .= $start_timestamp.'-'.$end_timestamp.'#'.$start.'#'.$end.'#'.$qty.'#'.$note.'#'.$adventure['session_id'].'+';
			}
		}
		
		return $output;
	}
	
	public function getNearestAdventures($product, $date, $qty)
	{
		date_default_timezone_set(Mage::app()->getStore()->getConfig('general/locale/timezone'));
		$helper = Mage::helper('bookme');
		$date = $helper->formatDate(date('Y-m-d', $date / 1000), Varien_Date::DATE_INTERNAL_FORMAT);
		
		$output .= 'null_from_form+';
		$_date = $date;//date('Y-m-d', $date / 1000);
		$resource = Mage::getSingleton('core/resource');
		$readConnection = $resource->getConnection('core_read');
		
		$sessionTable = $resource->getTableName('bookmepro/session');
		$qty = Mage::getStoreConfig('bookmepro_sales/sales/nearest_count', Mage::app()->getStore()->getId());
		
		$sql = "SELECT * FROM $sessionTable WHERE `product_id` = ".$product->getId()." AND `date_from` >= '".$date."'
		ORDER BY `date_from` ASC , `time_from` LIMIT 0 , $qty";
		
		$rows = $readConnection->fetchAll($sql);
		
		foreach ($rows as $row)
		{
			if($row['booked_qty'] < $row['max_quantity']){
				$_time = explode(" ", $row['time_from']);
				$time = explode(":", $_time[1]);
					
				$start_timestamp = strtotime($row['time_from']) * 1000;
				$end_timestamp = strtotime($row['time_to']) * 1000;
				$start = Mage::helper('core')->formatDate($row['date_from'], 'medium', false).' '.date('(D)', strtotime($row['date_from'])).' '.date("H:i", $start_timestamp/1000);
				$end = Mage::helper('core')->formatDate($row['date_to'], 'medium', false).' '.date('(D)', strtotime($row['date_to'])).' '.date("H:i", $end_timestamp/1000);
					
				$qty = $row['max_quantity'] - $row['booked_qty'];
					
				$date = explode("-", date('Y-m-d', strtotime($row['date_from'])));
				$ctime = explode(":", $row['time_from']);
				$time = mktime($ctime[0],$ctime[1],0,$date[1],$date[2],$date[0]);
				$diff = (strtotime($_date) - strtotime(date('Y-m-d', $time))) / 24 / 60 / 60;
					
				$d = $days - $diff;
				$d = round($d);
				$note = ($d > 0)? $d.' '.Mage::getStoreConfig('bookmepro_options/locales/days_later', Mage::app()->getStore()->getId()) : (($d != 0)? ($d * (-1)).' '.Mage::getStoreConfig('bookmepro_options/locales/days_earlier', Mage::app()->getStore()->getId()) : '');
					
				$output .= $start_timestamp.'-'.$end_timestamp.'#'.$start.'#'.$end.'#'.$qty.'#'.$note.'#'.$row['session_id'].'+';
			}
		}
		
		return $output;
	}
	
	public function isSessionAvailable($session){
		return ($session['max_quantity'] > $this->getSessionBookedQty($session))? true : false;
	}
	
	public function getSessionBookedQty($session){
		$resource = Mage::getSingleton('core/resource');
		$readConnection = $resource->getConnection('core_read');
		
		$itemTable = $resource->getTableName('bookme/book_item');
		
		$sql = "Select sum(qty) from $itemTable where session_id = ".$session['session_id']." group by session_id";
		
		$qty = $readConnection->fetchCol($sql);
		return (isset($qty[0]))? $qty[0] : 0;
	}
	
	
	public function getSessions($product_id, $date){
		date_default_timezone_set(Mage::app()->getStore()->getConfig('general/locale/timezone'));
		$helper = Mage::helper('bookme');
		$date = $helper->formatDate(date('Y-m-d', $date / 1000), Varien_Date::DATE_INTERNAL_FORMAT);
		$output = '';
		$product = Mage::getModel('catalog/product')->load($product_id);
		
		if(!$product->getId())
			return '';
		
		$sessions = Mage::getModel('bookmepro/session')->getCollection()
		->addFieldToFilter('product_id', array('eq' => $product->getId()))
		->addFieldToFilter('date_from', array('eq' => $date))
		->setOrder('time_from', 'ASC');
		$output = '';
		
		foreach ($sessions as $session)
		{
			if($session['max_quantity'] > $session['booked_qty'])
			{
				$time = explode(":", $session['time_from']);
				$output .= $time[0].":".$time[1].'#'.$session['session_id'].',';
			}
		}
		return ($output == '')? '' : substr($output, 0, strlen($output) - 1);
		
	}
// 		if($product->getAttributeText('billable_period') == 'Session'){
// 			$sessions = $product->getData('custom_session');
// 			Mage::log($sessions);
// 			$dw = date("N", ($date / 1000));
			
// 			/*
// 			 * at first look up by for 'every day' session
// 			*/
// 			foreach ($sessions as $session){
// 				if($session['day_start'] == 10){
// 					$output = '';
// 					foreach ($session['sessions'] as $time){
// 						$start_timestamp = ($time['start_hour'] * 60 * 60) + ($time['start_minute'] * 60);
// 						$output .= $start_timestamp.'#'.$this->isSessionAvailible($product, ($date + ($start_timestamp * 1000))).',';
// 					}
// 				}
// 			}
			
// 			/*
// 			 * next search for weekend or business day
// 			 */
// 			if($dw > 0 && $dw < 6){
// 				foreach ($sessions as $session){
// 					if($session['day_start'] == 8){
// 						$output = '';
// 						foreach ($session['sessions'] as $time){
// 							$session_timestamp = (($time['start_hour'] * 60 * 60) + ($time['start_minute'] * 60));
// 							$output .= $session_timestamp.'#'.$this->isSessionAvailible($product, $date + ($session_timestamp * 1000)).',';
// 						}
// 					}
// 				}	
// 			}else{
// 				foreach ($sessions as $session){
// 					if($session['day_start'] == 9){
// 						$output = '';
// 						foreach ($session['sessions'] as $time){
// 							$session_timestamp = (($time['start_hour'] * 60 * 60) + ($time['start_minute'] * 60));
// 							$output .= $session_timestamp.'#'.$this->isSessionAvailible($product, $date + ($session_timestamp * 1000)).',';
// 						}
// 					}
// 				}
// 			}
			
			
// 			/*
// 			 * next search for specific day in week
// 			 */
// 			foreach ($sessions as $session){
// 				if($session['day_start'] == $dw){
// 					$output = '';
// 					foreach ($session['sessions'] as $time){
// 						$session_timestamp = (($time['start_hour'] * 60 * 60) + ($time['start_minute'] * 60));
// 						$output .= $session_timestamp.'#'.$this->isSessionAvailible($product, $date + ($session_timestamp * 1000)).',';
// 					}
// 				}
// 			}
			
// 			/*
// 			 * at last check if there is secific day setted
// 			*/
// 			foreach ($sessions as $session){
// 				if($session['day_start'] == 11 && isset($session['specific_date_start'])){
// 					if($session['specific_date_start'] == date('Y-m-d', $date / 1000)){
// 						$output = '';
// 						foreach ($session['sessions'] as $time){
// 							$session_timestamp = (($time['start_hour'] * 60 * 60) + ($time['start_minute'] * 60));
// 							$output .= $session_timestamp.'#'.$this->isSessionAvailible($product, $date + ($session_timestamp * 1000)).',';
// 						}
// 					}
// 				}
// 			}
			
// 			return substr($output, 0, strlen($output) - 1);
//	}
	
	public function sessionAvailible($product, $start_time, $end_time, $session_qty){
		
		$date_from = date('Y-m-d H:i:s', $start_time / 1000);
		$date_to = date('Y-m-d H:i:s', $end_time / 1000);
		
		$resource = Mage::getSingleton('core/resource');
		$booke_item_table = $resource->getTableName('bookme/book_item');
		
		$sql = "SELECT * FROM $booke_item_table".
				" WHERE `product_id` = ".$product->getId().
				" AND ( `booked_from` = '$date_from'".
				" OR `booked_to` = '$date_to' )";
		
		$bookCollection = Mage::getSingleton('core/resource')
				->getConnection('core_read')
						->fetchAll($sql);

		$bookedQty = 0;
		foreach ($bookCollection as $item){
			$bookedQty += $item['qty'];
		}
		
		if($session_qty < 1)
			$session_qty = $product->getData('bookable_qty');
		
		return ($bookedQty < $session_qty)? '1' : '0';
	}
	
	
	public function getSessionAvailableQty($product, $start_time, $end_time, $session_qty){
		$date_from = date('Y-m-d H:i:s', $start_time / 1000);
		$date_to = date('Y-m-d H:i:s', $end_time / 1000);
	
		$resource = Mage::getSingleton('core/resource');
		$booke_item_table = $resource->getTableName('bookme/book_item');
	
		$sql = "SELECT * FROM $booke_item_table".
				" WHERE `product_id` = ".$product->getId().
				" AND ( `booked_from` = '$date_from'".
				" OR `booked_to` = '$date_to' )";
	
		$bookCollection = Mage::getSingleton('core/resource')
		->getConnection('core_read')
		->fetchAll($sql);
	
		$bookedQty = 0;
		foreach ($bookCollection as $item){
			$bookedQty += $item['qty'];
		}
		
		if($session_qty < 1)
			$session_qty = $product->getData('bookable_qty');
	
		return  $session_qty - $bookedQty;
	}
	
}