<?php

/**
 * @category   Peexl
 * @package    Peexl_BundleCreatorPlus
 * @copyright  Copyright (c) 2012 Peexl Web Development (http://www.peexl.com)
 * @license    http://framework.zend.com/license/new-bsd    New BSD License
 * @version    1.0
 */
class Peexl_BundleCreatorPlus_Model_Resource_Item_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract {

    protected $_storeId;
    
    protected $_useAttributes = true;

    public function _construct() {
        parent::_construct();
        $this->_init('bundlecreatorplus/item');
    }
    
    protected function _beforeLoad() {
        if (!$this->_useAttributes) {
            return parent::_beforeLoad();
        }
        $storeId = $this->getStoreId();
        foreach (Peexl_BundleCreatorPlus_Model_Resource_Item::$ATTRIBUTES as $attributeCode) {   
            $alias = 'item_' . $attributeCode .'_value';
            $this->getSelect()
                ->joinLeft(
                    array($alias => $this->getTable('bundlecreatorplus/item_attribute_value')),
                    "main_table.item_id = $alias.item_id and $alias.attribute_code = '$attributeCode' and $alias.store_id = '$storeId'",
                    array()
                )
                ->columns(array($attributeCode => new Zend_Db_Expr("IFNULL($alias.value, '')")));
        }
        
        return parent::_beforeLoad();
    }
    
    /**
     * Set store scope
     *
     * @param int $storeId
     * @return Peexl_BundleCreatorPlus_Model_Resource_Item_Collection
     */
    public function setStoreId($storeId)
    {
        $this->_storeId = (int)$storeId;
        return $this;
    }

    /**
     * Return current store id
     *
     * @return int
     */
    public function getStoreId()
    {
        if (is_null($this->_storeId)) {
            $this->setStoreId(Mage::app()->getStore()->getId());
        }
        return $this->_storeId;
    }
    
    public function setUseAttributes($value)
    {
        $this->_useAttributes = $value;
        
        return $this;
    }

}