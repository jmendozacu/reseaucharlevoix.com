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
$installer = $this;

$installer->startSetup();
date_default_timezone_set(Mage::app()->getStore()->getConfig('general/locale/timezone'));

$yesterday = strtotime(date('Y-m-d')) - (24 * 60 * 60);

$book_items = Mage::getModel('bookme/book_item')->getCollection()
->addFieldToFilter('from_date', array('gt' => date('Y-m-d', $yesterday)));

foreach ($book_items as $item)
{
	$item = Mage::getModel('bookme/book_item')->load($item['item_id']);
	
	$product = Mage::getModel('catalog/product')->load($item->getProductId());
	if($product->getAttributeText('billable_period') == 'Day')
		continue;
	
	$session = Mage::getModel('bookmepro/session')
	->getCollection()
	->addFieldToFilter('product_id', array('eq' => $item->getProductId()))
	->addFieldToFilter('date_from', array('eq' => $item->getFromDate()))
	->addFieldToFilter('time_from', array('eq' => $item->getFromTime()));
	
	if($session->count() > 0)
	{
		$session = $session->getFirstItem();
		$session = Mage::getModel('bookmepro/session')->load($session['session_id']);
		
		$item->setSessionId($session->getId());
		$item->save();
		
		$session->setBookedQty($session->getBookedQty() + $item->getQty());
		$session->save();
	}else{
		$session_object = Mage::getModel('bookmepro/session');
		$session_object->setData('product_id', $product->getId());
		$session_object->setDateFrom($item->getFromDate());
		$session_object->setTimeFrom($item->getFromTime());
		$session_object->setDateTo($item->getFromDate());
		$session_object->setTimeTo($item->getFromTime());
		$session_object->setMaxQuantity($item->getQty());
		$session_object->setBookedQty($item->getQty());
		$session_object->setBookType('Session');
		$session_object->save();
	}
	
}



$products = Mage::getModel('catalog/product')->getCollection()
->addFieldToFilter('type_id', array('eq' => 'booking'));

foreach ($products as $product)
{
	if($product['billable_period'] == 'Day')
		continue;

	$order_items = Mage::getModel('sales/order_item')->getCollection()
	->addFieldToFilter('product_id', $product['entity_id']);

	foreach ($order_items as $item)
	{
		$item = Mage::getModel('sales/order_item')->load($item['item_id']);
		$option = $item->getProductOptions();

		for ($i = 0; $i < count($option['options']); $i++)
		{
			if($option['options'][$i]['option_type'] == 'multidate_type')
			{
				$u_value = $option['options'][$i]['value'];
				$out = '';
				$book_items = Mage::getModel('bookme/book_item')->getCollection()
				->addFieldToFilter('order_item_id', array('eq' => $item->getId()));

				foreach ($book_items as $book_item)
				{
					if($book_item['book_type'] == 'Day')
						$out = $u_value;
					else
						$out .= $book_item['session_id'].'#';
				}
				$val = array('values' => $out);
				$option['options'][$i]['value'] = serialize($val);
				$option['options'][$i]['print_value'] = serialize($val);
				$option['options'][$i]['option_value'] = serialize($val);
			}
		}
		$item->setData('product_options', serialize($option));
		$item->save();
	}
}

$installer->endSetup();
