<?php

/**
 * @category   Peexl
 * @package    Peexl_BundleCreatorPlus
 * @copyright  Copyright (c) 2012 Peexl Web Development (http://www.peexl.com)
 * @license    http://framework.zend.com/license/new-bsd    New BSD License
 * @version    1.0
 */
class Peexl_BundleCreatorPlus_Model_Package_Session_Product extends Varien_Object {

    const STATE_COMPLETE = 'complete';
    const STATE_INCOMPLETE = 'incomplete';

    protected $_state;
    protected $_items = array();
    protected $_id;
    protected $_url;
    protected $_cartItemId;
   
    public function getProduct() {
        return Mage::getModel('catalog/product')->load($this->getId());
    }
    
    public function getId() {
        return $this->_id;
    }
    
    public function getCartItemId() {
        return $this->_cartItemId;
    }
    
    public function setCartItemId($cartItemId) {
        $this->_cartItemId = $cartItemId;
    }

    public function getUrl() {
        return $this->_url;
    }

    public function getIsComplete() {
        return ($this->_state == self::STATE_COMPLETE) ? true : false;
    }
    
    public function setIsComplete() {
        $this->_state = self::STATE_COMPLETE;
    }

    public function getItems() {
        return $this->_items;
    }

    public function getItemById($itemId) {
        foreach ($this->_items as $item) {
            if ($item->getId() == $itemId) {
                return $item;
            }
        }
    }

    public function getActiveItem() {
        foreach ($this->getItems() as $item) {
            if ($item->getIsActive()) {
                return $item;
            }
        }
    }

    public function setActiveItem($itemId) {
        foreach ($this->getItems() as $item) {
            if ($item->getId() == $itemId) {
                $item->setIsActive(true);
            } else {
                $item->setIsActive(false);
            }
        }
        //$this->_state = self::STATE_INCOMPLETE;
    }

    public function buildFromProduct($product) {
        if(!$product->getId()) {
            throw new Exception("Package doesn't exist");
        }
        $this->_id = $product->getId();
        $this->_url = Mage::getBaseUrl('web') . $product->getUrlPath();
        $this->setQty(1);
        $items = $product->getTypeInstance()->getItems();
        if (count($items) > 0) {
            foreach ($items as $item) {
                $itemSession = Mage::getModel('bundlecreatorplus/package_session_product_item')->buildFromItem($item);

                if (!$this->_items) {
                    $this->_items = array();
                    $itemSession->setIsActive(true);
                }
                $this->_items[] = $itemSession;
            }
        }
        return $this;
    }

    public function incrementActiveItem() {
        $prevActiveItemId = $this->getActiveItem()->getId();
        $this->getActiveItem()->setIsActive(false);
        $activeIsSet = false;
        $index = 0;
        foreach ($this->getItems() as $item) {
            $index++;
            
            if($prevActiveItemId == $item->getId()) {
                $prevActiveItemIndex = $index;
            }
            
            if (!$activeIsSet && !$item->getIsComplete()) {
                $item->setIsActive(true);
                $activeItemIndex = $index;
                $activeIsSet = true;
            } elseif ($activeIsSet && !$item->getIsComplete() && $this->getActiveItem()->getOptional() && 
                      isset($prevActiveItemIndex) && $activeItemIndex < $prevActiveItemIndex && $index > $prevActiveItemIndex) {
                $this->getActiveItem()->setIsActive(false);
                $item->setIsActive(true);
                $activeItemIndex = $index;         
            }
            if(!$item->getOptional() && !$item->getIsComplete()) {
                return $this;
            }
        }
        $this->_state = self::STATE_COMPLETE;
        return $this;
    }

    public function reset() {
        $this->_state = self::STATE_INCOMPLETE;
        foreach ($this->_items as $item) {
            $item->reset();
        }
        // set the first component as the active
        $this->_items[0]->setIsActive(true);
        return $this;
    }

}