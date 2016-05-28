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
class VDesign_BookmePro_Model_Product_Attribute_Backend_Priceprofile
extends Mage_Eav_Model_Entity_Attribute_Backend_Abstract{
	
	/**
	 * Retrieve resource instance
	 *
	 */
	protected function _getResource()
	{
		return Mage::getResourceSingleton('bookmepro/product_attribute_backend_priceprofile');
	}
	
	public function afterSave($object)
	{
		$priceRows = $object->getData($this->getAttribute()->getName());
		$types = array();
		foreach ($priceRows as $row){
			$profile = Mage::getModel('bookmepro/priceprofile');
				
			if(isset($row['profile_id'])){
				$profile = $profile->load($row['profile_id']);
				if(!$profile->getId()){
					unset($row['rule_id']);
					$profile = Mage::getModel('bookmepro/priceprofile');
				}else{
					if($row['deleted'] == 1){
						$profile->delete();
						continue;
					}
				}
			}
			if($row['deleted'] == 1){
				continue;
			}
			
			$profile->setProduct($object);
			$profile->setName($row['name']);
			$profile->setMove($row['move']);
			$profile->setAmountType($row['amount_type']);
			$profile->setAmount($row['amount']);
			$profile->save();
			
			if(isset($row['translations']))
			{
				foreach ($row['translations'] as $store)
				{
					$trans = Mage::getModel('bookmepro/priceprofile_store')->getCollection()
					->addFieldToFilter('profile_id', $profile->getId())
					->addFieldToFilter('code', $store['code'])
					->getFirstItem();
						
					if(!$trans->getId())
						$trans = Mage::getModel('bookmepro/priceprofile_store');
						
					$trans->setProfileId($profile->getId());
					$trans->setCode($store['code']);
					$trans->setName($store['name']);
					$trans->save();
				}
			}
			else
			{
				foreach ($this->getStoreData() as $store)
				{
					$trans = Mage::getModel('bookmepro/priceprofile_store')->getCollection()
					->addFieldToFilter('profile_id', $profile->getId())
					->addFieldToFilter('code', $store['code'])
					->getFirstItem();
				
					if(!$trans->getId())
						$trans = Mage::getModel('bookmepro/priceprofile_store');
				
					$trans->setProfileId($profile->getId());
					$trans->setCode($store['code']);
					$trans->setName($row[$store['code']]);
					$trans->save();
				}
			}
		}
	
		return $this;
	}
	
	public function afterLoad($object){
	
		$data = array();
		$collection = Mage::getModel('bookmepro/priceprofile')->getCollection()->addFilter('entity_id', $object->getId());
		foreach ($collection as $item){
			$pom = array();
			foreach ($item->getData() as $key => $value){
				if($value)
					$pom[$key] = $value;
				if($key == 'profile_id')
				{
					$translations = Mage::getModel('bookmepro/priceprofile_store')
					->getCollection()->addFieldToFilter('profile_id', $value);
					foreach ($translations as $translation)
						$pom['translations'][] = $translation->getData();
				}
			}
			$data[] = $pom;
		}
		$object->setData($this->getAttribute()->getAttributeCode(), $data);
		$object->setOrigData($this->getAttribute()->getAttributeCode(), $data);
	
		return $this;
	}
	
	public function getStoreData(){
		$allStores = Mage::app()->getStores();
		$return = array();
		foreach ($allStores as $_storeId => $val)
		{
			$store = array();
			$store['name'] = Mage::app()->getStore($_storeId)->getName();
			$store['code'] = Mage::app()->getStore($_storeId)->getCode();
	
			$return[] = $store;
		}
		return $return;
	}
}