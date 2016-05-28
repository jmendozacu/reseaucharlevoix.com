<?php

/**
 * @category   Peexl
 * @package    Peexl_BundleCreatorPlus
 * @copyright  Copyright (c) 2012 Peexl Web Development (http://www.peexl.com)
 * @license    http://framework.zend.com/license/new-bsd    New BSD License
 * @version    1.0
 */
class Peexl_BundleCreatorPlus_Block_Package_Item_List_Filter extends Mage_Core_Block_Template
{
	
	protected $_productCollection;
	protected $_attributeCollection;
	protected $_setIds;
	protected $_productIds;
	protected $_filteredAttributes		= array();
	
  
	public function getFilterableAttributes()
  {
        /** @var $collection Mage_Catalog_Model_Resource_Product_Attribute_Collection */
        $this->_attributeCollection = Mage::getResourceModel('catalog/product_attribute_collection');
        $this->_attributeCollection
            ->setItemObjectClass('catalog/resource_eav_attribute')
            ->setAttributeSetFilter($this->getSetIds())
            ->addStoreLabel(Mage::app()->getStore()->getId())
            ->setOrder('position', 'ASC');
        $this->_attributeCollection = $this->_prepareAttributeCollection($this->_attributeCollection);
        $this->_attributeCollection->load();

        return $this->_attributeCollection;
  }
  
    /**
     * Prepare attribute for use in layered navigation
     *
     * @param   Mage_Eav_Model_Entity_Attribute $attribute
     * @return  Mage_Eav_Model_Entity_Attribute
     */
    protected function _prepareAttribute($attribute)
    {
        Mage::getResourceSingleton('catalog/product')->getAttribute($attribute);
        return $attribute;
    }
    
    /**
     * Add filters to attribute collection
     *
     * @param   Mage_Catalog_Model_Resource_Eav_Mysql4_Attribute_Collection $collection
     * @return  Mage_Catalog_Model_Resource_Eav_Mysql4_Attribute_Collection
     */
    protected function _prepareAttributeCollection($collection)
    {
        $collection->addIsFilterableFilter();
        return $collection;
    }

	public function setProductCollection($collection)
	{
		$this->_productCollection = $collection;
		$this->getFilterableAttributes();
		$this->getProductIds();
		$this->getSetIds();
		$this->applyFilter();
		return $this;
	}
	
	public function getProductCollection()
	{
		return $this->_productCollection;
	}
	
	public function applyFilter()
	{
		$get = $this->getRequest()->getParams();
		foreach ($this->getFilterableAttributes() as $attribute) {
			$code = $attribute->getAttributeCode();
			if (array_key_exists($code, $get)) {
                            echo $code.' ';
				$this->_filteredAttributes[] = $code;
				$this->getProductCollection()->addAttributeToFilter($code, $get[$code]);
			}
		}
	}
	
	public function getProductIds()
	{
		if(!$this->_productIds){
		    $select = clone $this->getProductCollection()->getSelect();
	        /* @var $select Zend_Db_Select */
	        $select->reset(Zend_Db_Select::COLUMNS);
			    $select->reset(Zend_Db_Select::ORDER);
	        $select->distinct(true);
	        $select->from('', 'e.entity_id');
         	$this->_productIds = $this->getProductCollection()->getConnection()->fetchCol($select);
		}
		return $this->_productIds;
	}
	
	public function getSetIds()
	{
		if(!$this->_setIds){
			// add caching around this
			// have to use a temporary collection because the original has a Sort Order that bombs the setIds
			$tempCollection = clone $this->getProductCollection();
			$tempCollection->getSelect()->reset(Zend_Db_Select::ORDER);
			$this->_setIds = $tempCollection->getSetIds();
		}
		return $this->_setIds;
	}
	
	public function getFilteredAttributes()
	{
		return $this->_filteredAttributes;
	}
	
	public function getVisibleFilterableAttributes()
	{
		$visible = new Varien_Data_Collection();
		foreach ($this->getFilterableAttributes() as $attribute) {
			if(!in_array($attribute->getAttributeCode(), $this->getFilteredAttributes())){
				$visible->addItem($attribute);
			}
		}
		return $visible;
	}
	
	public function getAttributeOptions($attribute)
	{
		$storeId = Mage::app()->getStore()->getId(); 
		$optionValueTable = Mage::getSingleton('core/resource')->getTableName('eav/attribute_option_value');
		$attributeTable = Mage::getSingleton('core/resource')->getTableName('catalog/product');
		$collection = Mage::getResourceModel('eav/entity_attribute_option_collection');
		$collection->getSelect()
			->joinLeft(array('option_values' => $optionValueTable), "option_values.option_id=main_table.option_id AND store_id=$storeId", 'value')
			->where('main_table.attribute_id = ?', $attribute->getId())
			->join(
				 array('product_int_values' => $attributeTable . "_int")
				,"product_int_values.attribute_id=main_table.attribute_id AND product_int_values.value = main_table.option_id"
				,'')
			->where('product_int_values.entity_id IN (?)', $this->getProductIds())
			->where('option_values.value IS NOT NULL')
			->distinct();
		return $collection;
	}
	
	public function getFilteredValue($code)
	{
		$storeId = Mage::app()->getStore()->getId(); 
		$optionValueTable = Mage::getSingleton('core/resource')->getTableName('eav/attribute_option_value');
		$collection = Mage::getResourceModel('eav/entity_attribute_option_collection');
		$collection->getSelect()
			->joinLeft(array('option_values' => $optionValueTable), "option_values.option_id=main_table.option_id AND store_id=$storeId", 'value')
			->where('main_table.option_id = ?', $this->getRequest()->getParam($code));
		return $collection->getFirstItem()->getValue();
	}
	
	public function buildUrl($get=array(), $remove=array())	
	{
		$url = $this->getBaseUrl();
		$getString = '';
		foreach ($this->getRequest()->getParams() as $key => $value) {
			if($key != 'id' && !array_key_exists($key, $get) && !in_array($key, $remove)){
				if($getString != ""){
					$getString .= "&";
				}
				$getString .= "$key=$value";
			}
		}
		foreach ($get as $key => $value) {
			if (!in_array($key, $remove)) {
				if($getString != ""){
					$getString .= "&";
				}
				$getString .= "$key=$value";
			}
		}
		if($getString != ''){
			$url .= "?$getString";
		}
		return $url;
	}
	
	public function buildUrlWithout($code)
	{
		return $this->buildUrl(array(), array($code));
	}
	
	public function buildUrlWith($code, $value)
	{
		return $this->buildUrl(array($code => $value));
	}
	
	public function getBaseUrl()
	{
		return Mage::getUrl('catalog/product/view', array('id'=>Mage::registry('current_product')->getId()));
	}
}
