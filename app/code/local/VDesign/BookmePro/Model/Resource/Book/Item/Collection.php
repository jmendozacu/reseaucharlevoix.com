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

class VDesign_BookmePro_Model_Resource_Book_Item_Collection
extends VDesign_Bookme_Model_Resource_Book_Item_Collection {
	
	public function groupBySessionId()
	{
		$this->getSelect()
			->group('session_id');
		
		return $this;
	}
	
	public function groupByBookTime()
    {
        $this->getSelect()
            ->where('booked_from IS NOT NULL')
            ->group('booked_from');
        
        return $this;
    }
    
    public function groupByBookToTime()
    {
    	$this->getSelect()
    	->where('booked_to IS NOT NULL')
    	->group('booked_to');
    
    	return $this;
    }
    
    public function addBookedCount(){
    	$this->getSelect()
    	->columns(array('booked_count' => 'SUM(main_table.qty)'));
    	return $this;
    }
    
    public function getSessionsOnly()
    {
    	$this->getSelect()
    	->where('book_type = "Session"');
    	
    	return $this;
    }
    
    public function getAdventuresOnly()
    {
    	$this->getSelect()
    	->where('main_table.book_type = "Adventure"');
    	 
    	return $this;
    }
    
    public function fromToday(){
    	date_default_timezone_set(Mage::app()->getStore()->getConfig('general/locale/timezone'));
    	$today = date('Y-m-d');
    	
    	$this->getSelect()
    	->where("booked_from >= '$today'");
    	
    	return $this;
    }
    
    public function addAvailableCount(){

    	$this->join(array('session' => 'bookmepro/session'), "main_table.session_id = session.session_id", array(
     				'aQty' => 'max_quantity',
    				'date_from' => 'date_from',
    				'time_from' => 'time_from',
    				'date_to' => 'date_to',
    				'time_to' => 'time_to'
   		));
    	return $this;
    }
    
    public function joinEavTables(){
    
    	$entityType = Mage::getModel('eav/entity_type')->loadByCode('catalog_product');
    	$attributes = $entityType->getAttributeCollection();
    	$entityTable = $this->getTable($entityType->getEntityTable());
    	$attr = Mage::getSingleton('eav/config')->getAttribute('catalog_product', 'bookable_qty');
    	$alias = 'ea';
    	$table = $entityTable. '_text';
    	$field = 'ea.value';
    	
    	$this->getSelect()
    			->joinLeft(array($alias => $table),
    					'main_table.product_id = '.$alias.'.entity_id and '.$alias.'.attribute_id = '.$attr->getAttributeId(),
    					array($attr->getAttributeCode() => $field)
    			);
    		
    
    	return $this;
    
    }
    
    public function getWhereBookedFrom($bookedFrom){
    	$this->getSelect()
    	->where("main_table.booked_from = '$bookedFrom'");
    	
    	return $this;
    }
    
    public function getWhereSessionId($session_id){
    	$this->getSelect()
    	->where("main_table.session_id = '$session_id'");
    	
    	return $this;
    }
    
    public function getWhereProduct($product_id)
    {
    	$this->getSelect()
    	->where("main_table.product_id = $product_id");
    	 
    	return $this;
    }
    
}