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
class VDesign_BookmePro_Model_Product_Attribute_Backend_Mailreminder
extends Mage_Eav_Model_Entity_Attribute_Backend_Abstract{
	
	/**
	 * Retrieve resource instance
	 *
	 */
	protected function _getResource()
	{
		return Mage::getResourceSingleton('bookmepro/product_attribute_backend_mailreminder');
	}
	
	public function afterSave($object)
	{
	
		$nameRows = $object->getData($this->getAttribute()->getName());
	
		$types = array();
		foreach ($nameRows as $row){
			$reminder = Mage::getModel('bookmepro/book_mailreminder');
				
			if(isset($row['reminder_id'])){
				$reminder = $reminder->load($row['reminder_id']);
				if(!$reminder->getId()){
					unset($row['reminder_id']);
					$reminder = Mage::getModel('bookmepro/book_mailreminder');
				}else{
					if($row['deleted'] == 1){
						$reminder->delete();
						continue;
					}
				}
			}
			if($row['deleted'] == 1){
				continue;
			}
			
			$reminder->setProduct($object);
			$reminder->setEmailId($row['email_id']);
			$reminder->setPeriod($row['period']);
			
			$reminder->save();
		}
	
		return $this;
	}
	
	public function afterLoad($object){
	
		$data = array();
		$collection = Mage::getModel('bookmepro/book_mailreminder')->getCollection()->addFilter('product_id', $object->getId());
		foreach ($collection as $item){
			$pom = array();
			foreach ($item->getData() as $key => $value){
				if($value)
					$pom[$key] = $value;
			}
			$data[] = $pom;
		}
		$object->setData($this->getAttribute()->getAttributeCode(), $data);
		$object->setOrigData($this->getAttribute()->getAttributeCode(), $data);
	
		return $this;
	}
}