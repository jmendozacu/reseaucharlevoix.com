<?php

/**
 * @category   Peexl
 * @package    Peexl_BundleCreatorPlus
 * @copyright  Copyright (c) 2012 Peexl Web Development (http://www.peexl.com)
 * @license    http://framework.zend.com/license/new-bsd    New BSD License
 * @version    1.0
 */
class Peexl_BundleCreatorPlus_Model_Package_Session_Product_Item extends Varien_Object {

    const STATUS_ACTIVE = 'active';
    const STATUS_INACTIVE = 'inactive';
    const STATE_INCOMPLETE = 'incomplete';
    const STATE_COMPLETE = 'complete';

    protected $_status;
    protected $_state;
    protected $_isUpgrade = false;
    protected $_product;
    protected $_preview_image;

    public function getId() {
        return $this->getItemId();
    }

    public function getIsActive() {
        return ($this->_status == self::STATUS_ACTIVE) ? true : false;
    }

    public function setIsActive($cond) {
        $this->_status = ($cond) ? self::STATUS_ACTIVE : self::STATUS_INACTIVE;
    }

    public function getIsComplete() {
        return ($this->_state == self::STATE_COMPLETE) ? true : false;
    }

    public function getIsUpgrade() {
        return $this->_isUpgrade;
    }

    public function setIsUpgrade($cond) {
        $this->_isUpgrade = $cond;
        return $this;
    }

    public function getStatus() {
        return $this->_status;
    }

    public function getState() {
        return $this->_state;
    }

    public function getProduct() {
        return $this->_product;
    }
    
    public function getPreviewImage() {
        return $this->_preview_image;
    }
    
    public function getOption() {
        return $this->_option;
    }

    public function setProduct($product) {
        $package = Mage::registry('package');
        $this->_product = new Varien_Object(array('id' => $product->getId(), 'name' => $product->getName(), 'request_params' => $product->getRequestParams(), 'is_salable' => $product->getIsSalable()));
        $this->_option = Mage::helper('bundlecreatorplus')->getOptionByProductId($this->getId(), $product->getId());
        if($package && $package->getPreviewType() == Peexl_BundleCreatorPlus_Model_Preview_Type::GRID) {
            $this->_preview_image = (string)Mage::helper('catalog/image')->init($product, 'small_image')->resize(80, 80);
        } else {            
            $this->_preview_image = $this->_option->getPreviewImage();
        }
        $this->_state = self::STATE_COMPLETE;
        return $this;
    }

    public function getInstance() {
        return Mage::getModel('bundlecreatorplus/item')->load($this->getId());
    }

    public function buildFromItem($item) {
        $this->_state = self::STATE_INCOMPLETE;
        $this->_status = self::STATUS_INACTIVE;
        $this->setData($item->getData());
        $this->setStoreId(Mage::app()->getStore()->getId());
        return $this;
    }

    public function reset() {
        $this->_state = self::STATE_INCOMPLETE;
        $this->_status = self::STATUS_INACTIVE;
        unset($this->_product);
    }
    
    public function getData($key='', $index=null)
    {
        if (in_array($key, Peexl_BundleCreatorPlus_Model_Resource_Item::$ATTRIBUTES) &&
            Mage::app()->getStore()->getId() !== $this->getStoreId()) {
            $item = Mage::getModel('bundlecreatorplus/item')->load($this->getId());
            foreach (Peexl_BundleCreatorPlus_Model_Resource_Item::$ATTRIBUTES as $attributeCode) {
                $this->setData($attributeCode, $item->getData($attributeCode));
            }
            $this->setStoreId(Mage::app()->getStore()->getId());
        }
        
        return parent::getData($key, $index);
    }

}