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

class VDesign_BookmePro_Model_Resource_Session_Collection
extends Mage_Core_Model_Resource_Db_Collection_Abstract {
	protected function _construct()
	{
		$this->_init('bookmepro/session');
	}
	
	public function joinEavTables(){
	
		$entityType = Mage::getModel('eav/entity_type')->loadByCode('catalog_product');
		$attributes = $entityType->getAttributeCollection();
		$entityTable = $this->getTable($entityType->getEntityTable());
		$attr = Mage::getSingleton('eav/config')->getAttribute('catalog_product', 'name');
		$alias = 'ea';
		$table = $entityTable. '_varchar';
		$field = 'ea.value';
		 
		$this->getSelect()
		->joinLeft(array($alias => $table),
				'main_table.product_id = '.$alias.'.entity_id and '.$alias.'.attribute_id = '.$attr->getAttributeId(),
				array($attr->getAttributeCode() => $field)
		);
	
	
		return $this;
	
	}
	
	public function groupByBookTime()
	{
		$this->getSelect()
		->where('time_from IS NOT NULL')
		->group('time_from');
	
		return $this;
	}
	
	public function groupByBookToTime()
	{
		$this->getSelect()
		->where('time_to IS NOT NULL')
		->group('time_to');
	
		return $this;
	}
	
	public function getAdventuresOnly()
	{
		$this->getSelect()
		->where('book_type = "Adventure"');
	
		return $this;
	}
	

	public function getSessionsOnly()
	{
		$this->getSelect()
		->where('book_type = "Session"');
	
		return $this;
	}
}