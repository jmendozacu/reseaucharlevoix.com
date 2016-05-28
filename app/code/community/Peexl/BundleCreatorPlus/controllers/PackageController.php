<?php

/**
 * @category   Peexl
 * @package    Peexl_BundleCreatorPlus
 * @copyright  Copyright (c) 2012 Peexl Web Development (http://www.peexl.com)
 * @license    http://framework.zend.com/license/new-bsd    New BSD License
 * @version    1.0
 */
class Peexl_BundleCreatorPlus_PackageController extends Mage_Core_Controller_Front_Action {

    protected function _initSimpleProduct() {
        $productId = (int) $this->getRequest()->getParam('product');
        if ($productId) {
            $product = Mage::getModel('catalog/product')
                    ->setStoreId(Mage::app()->getStore()->getId())
                    ->load($productId);
            if ($product->getId()) {
                if ($product->getTypeId() == 'configurable') {
                    if ($super = $this->getRequest()->getParam('super_attribute')) {
                        $used = $product->getTypeInstance()->getProductByAttributes($super, $product);
                        if ($used->getId()) {                          
                            $product = Mage::getModel('catalog/product')->load($used->getId()); 
                            $product->setRequestParams($this->getRequest()->getParams());
                            return $product;
                        }
                    } else {
                        throw new Exception("Please specify the product option(s)");
                    }
                } else {
                    $product->setRequestParams($this->getRequest()->getParams());
                    $request = new Varien_Object($this->getRequest()->getParams());
                    $cartCandidates = $product->getTypeInstance(true)->prepareForCartAdvanced($request, $product);
                    if (is_string($cartCandidates)) {
                        throw new Exception($cartCandidates);
                    }
                    
                    // Add bundle with default options
                    if ($product->getTypeId() == 'bundle' && $product->getTypeInstance(true)->hasRequiredOptions($product) &&
                        !$this->getRequest()->getParam('bundle_option')) {
                        
                        $selectionCollection = $product->getTypeInstance(true)->getSelectionsCollection($product->getTypeInstance(true)->getOptionsIds($product), $product);
                        $rp = $this->getRequest()->getParams();
                        foreach($selectionCollection as $option) {
                            $rp['bundle_option'][$option->option_id] = $option->selection_id;
                            $rp['bundle_option_qty'][$option->option_id] = $option->selection_qty;
                        }
                        $product->setRequestParams($rp);                        
                    }
                    
                    return $product;
                }
            }
        }
        return false;
    }

    public function addAction() {
        $productId = (int) $this->getRequest()->getParam('product');
        $packageId = (int) $this->getRequest()->getParam('package');
        $itemId = (int) $this->getRequest()->getParam('item');

        if ($packageId && $itemId) {
            try {
                $package = Mage::getModel('catalog/product')->load($packageId);
                if(!$package || !$package->getId() || $package->getTypeId() != 'extendedbundle') {
                    throw new Exception("Incorrect extended bundle product");
                }
                $product = $this->_initSimpleProduct();
                if (!$product) {
                    Mage::getSingleton('catalog/session')->addNotice("Product doesn't exist");
                    $this->getResponse()->setRedirect($package->getProductUrl());
                    return;
                }
                
                $packageSession = Mage::helper('bundlecreatorplus')->getPackageSessionById($package->getId());
                if(!$packageSession) {
                    throw new Exception("Package doesn't exist");
                }
                
                // Check stock
                if($product->getStockItem()->getManageStock() && $product->getStockItem()->getQty() < $product->getCartQty()) {                    $q = round($product->getStockItem()->getQty(), 0, PHP_ROUND_HALF_DOWN);
                    $q = round($product->getStockItem()->getQty(), 0, PHP_ROUND_HALF_DOWN);
                    $message = Mage::helper('cataloginventory')->__('The requested quantity for "%s" is not available.', $product->getProductName());
                    Mage::getSingleton('core/session')->addError($message);
                    if ($this->getRequest()->getParam('isAjax')) {
                        $this->getResponse()->setBody(json_encode(array('error' => $message)));
                    } else {
                        $this->getResponse()->setRedirect(Mage::helper('bundlecreatorplus')->getItemOptionViewUrl($productId, $packageId, $itemId));
                    }
                    return;
                }

                if ($item = $packageSession->getItemById($itemId)) {
                    Mage::register('package', $package);
                    
                    // Check if item can have this product
                    $itemProductsIds = $item->getInstance()->getProductIds(true);
                    
                    $itemProductId = $product->getId();
                    $rp = $product->getRequestParams();
                    if(isset($rp['super_attribute'])) {                 
                        $itemProductId = $rp['product'];
                    }
                    
                    if(!is_array($itemProductsIds) || !in_array($itemProductId, $itemProductsIds)) {
                        throw new Exception("Wrong product");
                    }

                    $item->setProduct($product);
                    $packageSession->incrementActiveItem();
                } else {
                    Mage::getSingleton('catalog/session')->addNotice('Item doesn\'t exist');
                }
                if ($this->getRequest()->getParam('isAjax')) {
                    Mage::register('product', $package);
                    Mage::register('current_product', $package);
                    
                    $layout = $this->getLayout();
                    Mage::helper('catalog/product_view')->initProductLayout($package, $this);
                    $this->_initLayoutMessages('catalog/session');

                    $this->getResponse()->setBody(
                            json_encode(array('update' => array(
                                array('element' => 'package-view', 'content' => $layout->getBlock('package.view')->toHtml()),
                                array('element' => 'package-leftnav', 'content' => $layout->getBlock('catalog.leftnav')->toHtml()),
                                array('element' => 'package-preview', 'content' => $layout->getBlock('package.preview')->toHtml())
                                )))
                    );
                } else {
                    if($itemId = $this->getRequest()->getParam('item_id')) {
                        $this->_redirect('bcp/cart/add', array('product' => $packageId, 'item_id' => $itemId));
                        return;
                    }
                    $this->getResponse()->setRedirect($package->getProductUrl());
                }
                return;
            } catch (Exception $e) {
                Mage::getSingleton('catalog/session')->addNotice($e->getMessage());
                if ($this->getRequest()->getParam('isAjax')) {
                    $this->getResponse()->setBody(
                        json_encode(array('redirect' => Mage::helper('bundlecreatorplus')->getItemOptionViewUrl($productId, $packageId, $itemId)))
                    );
                } else {
                    $this->getResponse()->setRedirect(Mage::helper('bundlecreatorplus')->getItemOptionViewUrl($productId, $packageId, $itemId));
                }
            }
        }
    }

    protected function _getProductUrl($productId, $packageId) {
        $product = Mage::getModel('catalog/product')->load($productId);
        $query = array();
        if ($product->getHasOptions()) {
            $query = array('options' => 'cart', 'package' => $packageId);
        }
        return $product->getUrlModel()->getUrl($product, array('_query' => $query, '_secure' => Mage::app()->getRequest()->isSecure()));
    }

}
