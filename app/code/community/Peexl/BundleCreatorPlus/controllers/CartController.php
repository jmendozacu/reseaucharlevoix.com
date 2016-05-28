<?php

/**
 * @category   Peexl
 * @package    Peexl_BundleCreatorPlus
 * @copyright  Copyright (c) 2012 Peexl Web Development (http://www.peexl.com)
 * @license    http://framework.zend.com/license/new-bsd    New BSD License
 * @version    1.0
 */
class Peexl_BundleCreatorPlus_CartController extends Mage_Core_Controller_Front_Action {

    public function addAction() {
        if ($package = $this->getRequest()->getParam('product')) {
            try {
                
                $product = Mage::getModel('catalog/product')->load($package);
                $cart = Mage::getSingleton('checkout/cart');
                $uniqueKey = rand(0, 999999999);
                $items = $this->_prepareItems($package);
                $params = $this->getRequest()->getParams();
                $params = array_merge($params, array('items' => $items, 'uniqueKey' => $uniqueKey));
                
                $sessionPackage = Mage::helper('bundlecreatorplus')->getPackageSessionById($package);
                
                if(!($itemId = $this->getRequest()->getParam('item_id'))) { 
                    $itemId = $sessionPackage->getCartItemId();
                }
                
                if ($itemId) {
                    $_item = Mage::getSingleton('checkout/cart')->getQuote()->getItemById($itemId);
                    if ($_item && $_item->getId()) {
                        if ($_item->getProduct()->getTypeId() == 'extendedbundle') {
                            $_parentItem = $_item;
                        } elseif ($parentItemId = $_item->getParentItemId()) {
                            $_parentItem = Mage::getSingleton('checkout/cart')->getQuote()->getItemById($parentItemId);
                        }
                        if ($_parentItem && $_parentItem->getId() && $_parentItem->getProduct()->getTypeId() == 'extendedbundle') {
                            $params = $_parentItem->getBuyRequest()->getData();
                            $params['items'] = $items;
                            if($qty = $this->getRequest()->getParam('qty')) {
                                $params['qty'] = $qty;
                            }
                            $removeItemId = $itemId;
                        }
                    }
                }
                
                $cart->addProduct($product, $params);
        
                if(isset($removeItemId) && $removeItemId) {
                    $quote = $cart->getQuote();
                    $quote->removeItem($removeItemId);
                }
                
                $cart->save();
                
                Mage::dispatchEvent("package_product_added_to_quote", array('quote' => $cart->getQuote(), 'package' => array('items' => $items, 'productId' => $product->getId()), 'uniqueKey' => $uniqueKey));
                Mage::dispatchEvent('checkout_cart_add_product_complete', array('product' => $product, 'request' => $this->getRequest()));
                if ($this->getRequest()->getParam('isAjax')) {
                    Mage::helper('peexl_ajaxcart')->sendResponse(Mage::helper('peexl_ajaxcart')->getCartLinkText());
                } else {
                    Mage::helper('bundlecreatorplus')->resetPackageSession($product);
                    $this->_redirect('checkout/cart');
                }
            } catch (Mage_Core_Exception $e) {
                Mage::getSingleton('core/session')->addError($e->getMessage());
                if ($this->getRequest()->getParam('isAjax')) {
                    $this->getResponse()->setBody(json_encode(array('error' => $e->getMessage())));    
                } else {
                    $this->getResponse()->setRedirect($this->_getRefererUrl());
                }
                return;
            } catch (Exception $e) {
                Mage::getSingleton('core/session')->addError($e->getMessage());
                $this->getResponse()->setRedirect($this->_getRefererUrl());
                return;
            }
        }
    }
    
    public function editAction() {
        $itemId = $this->getRequest()->getParam('item_id');
        if($itemId) {
            $_item = Mage::getSingleton('checkout/cart')->getQuote()->getItemById($itemId);
            if(!$_item) {
                $this->_forward('noRoute');
                return;
            }
            $parentItemId = $_item->getParentItemId();
            if($parentItemId) {
                $_parentItem = Mage::getSingleton('checkout/cart')->getQuote()->getItemById($parentItemId);
                if($_parentItem->getProduct()->getTypeId() == 'extendedbundle') {       
                    $_package = Mage::getModel('catalog/product')->load($_parentItem->getProduct()->getId());
                    try {
                        $buyRequest = $_parentItem->getBuyRequest();
                        if ($buyRequest) {
                            Mage::helper('catalog/product')->prepareProductOptions($_package, $buyRequest);
                        }
                    } catch (Exception $e) {
                        Mage::getSingleton('core/session')->addError($this->__($e->getMessage()));
                        Mage::logException($e);
                        $this->getResponse()->setRedirect($this->_getRefererUrl());
                        return;
                    }
                     
                    $_request = $_item->getBuyRequest();
                    
                    Mage::helper('bundlecreatorplus')->getPackageSessionById($_package->getId())->setActiveItem($_request->getItem());
                    
                    $this->_redirect('*/product/view', array('id' => $_request->getProduct(), 'package' => $_request->getPackage(), 'item' => $_request->getItem(), 'item_id' => $_item->getId()));
                    return;
                }
            }
        }
    }

    protected function _prepareItems($packageId) {
        $items = array();
        $sessionItems = Mage::helper('bundlecreatorplus')->getPackageSessionById($packageId)->getItems();
        foreach ($sessionItems as $item) {
            if ($item->getProduct()) {
                $items[$item->getCode()] = array('product_id' => $item->getProduct()->getId(), 'request_params' => $item->getProduct()->getRequestParams());
            }
            if (!$item->getProduct() && !$item->getOptional()) {
                Mage::getSingleton('core/session')->addError('Missing required item options');
                $this->getResponse()->setRedirect($this->_getRefererUrl());
                return;
            }
        }
        return $items;
    }

}
