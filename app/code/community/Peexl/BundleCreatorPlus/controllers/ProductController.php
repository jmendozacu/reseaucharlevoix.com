<?php

/**
 * @category   Peexl
 * @package    Peexl_BundleCreatorPlus
 * @copyright  Copyright (c) 2012 Peexl Web Development (http://www.peexl.com)
 * @license    http://framework.zend.com/license/new-bsd    New BSD License
 * @version    1.0
 */
class Peexl_BundleCreatorPlus_ProductController extends Mage_Core_Controller_Front_Action {

    protected function _initProduct() {
        Mage::dispatchEvent('catalog_controller_product_init_before', array('controller_action' => $this));
        $categoryId = (int) $this->getRequest()->getParam('category', false);
        $productId = (int) $this->getRequest()->getParam('id');
        $packageId = (int) $this->getRequest()->getParam('package');
        $itemId = (int) $this->getRequest()->getParam('item');

        if (!$productId || !$packageId) {
            return false;
        }

        $product = Mage::getModel('catalog/product')
                ->setStoreId(Mage::app()->getStore()->getId())
                ->load($productId);
        
        $package = Mage::getModel('catalog/product')
                ->setStoreId(Mage::app()->getStore()->getId())
                ->load($packageId);
        
        
        // Check if package product and package session exist
        if($package->getId() && $package->getTypeId() == 'extendedbundle') {
            $packageSession = Mage::helper('bundlecreatorplus')->getPackageSessionById($package->getId());
        }       
        if(!$packageSession) {
            return;
        }

        // Check if product can be shown
        if (!$package->getShowInvisible()) {
            if(!Mage::helper('catalog/product')->canShow($product))
                return false;
        }
        if (!in_array(Mage::app()->getStore()->getWebsiteId(), $product->getWebsiteIds())) {
            return false;
        }
        
        $activeItem = $packageSession->getActiveItem();
        if($itemId && $itemId != $activeItem->getId()) {
            return;
        }
        
        // Check if item can have this product
        $itemProductsIds = $packageSession->getActiveItem()->getInstance()->getProductIds(true);
        if(!is_array($itemProductsIds) || !in_array($productId, $itemProductsIds)) {
            return;
        }


        $category = null;
        if ($categoryId) {
            $category = Mage::getModel('catalog/category')->load($categoryId);
            $product->setCategory($category);
            Mage::register('current_category', $category);
        } elseif ($categoryId = Mage::getSingleton('catalog/session')->getLastVisitedCategoryId()) {
            if ($product->canBeShowInCategory($categoryId)) {
                $category = Mage::getModel('catalog/category')->load($categoryId);
                $product->setCategory($category);
                Mage::register('current_category', $category);
            }
        }
        
        try {
            Mage::dispatchEvent('catalog_controller_product_init', array('product' => $product));
            Mage::dispatchEvent('catalog_controller_product_init_after', array('product' => $product, 'controller_action' => $this));
        } catch (Mage_Core_Exception $e) {
            Mage::logException($e);
            return false;
        }
        
        $_itemProduct = $packageSession->getActiveItem()->getProduct();
        if($_itemProduct) {
            $itemProductId = $_itemProduct->getId();
            $rp = $_itemProduct->getRequestParams();
            if(isset($rp['super_attribute'])) {                 
                $itemProductId = $rp['product'];
            }
            if($itemProductId == $product->getId()) {            
                if($params = $_itemProduct->getRequestParams()) {
                    $buyRequest = new Varien_Object($params);
                    if ($buyRequest) {
                        Mage::helper('catalog/product')->prepareProductOptions($product, $buyRequest);
                        $product->setConfigureMode(true);
                    }
                }
            }
        }

        Mage::register('package_product', $package);
        Mage::register('current_product', $product);
        Mage::register('product', $product);

        

        return $product;
    }

    protected function _initProductLayout($product) {
        $update = $this->getLayout()->getUpdate();
        $update->addHandle('default');
        $this->addActionLayoutHandles();

        $update->addHandle('PRODUCT_TYPE_' . $product->getTypeId());
        $update->addHandle('PRODUCT_' . $product->getId());

        if ($product->getPageLayout()) {
            $this->getLayout()->helper('page/layout')
                    ->applyHandle($product->getPageLayout());
        }

        $this->loadLayoutUpdates();


        $update->addUpdate($product->getCustomLayoutUpdate());

        $this->generateLayoutXml()->generateLayoutBlocks();

        if ($product->getPageLayout()) {
            $this->getLayout()->helper('page/layout')
                    ->applyTemplate($product->getPageLayout());
        }

        $currentCategory = Mage::registry('current_category');
        if ($root = $this->getLayout()->getBlock('root')) {
            $root->addBodyClass('product-' . $product->getUrlKey());
            if ($currentCategory instanceof Mage_Catalog_Model_Category) {
                $root->addBodyClass('categorypath-' . $currentCategory->getUrlPath())
                        ->addBodyClass('category-' . $currentCategory->getUrlKey());
            }
            if (($package = Mage::registry('package_product')) && $package instanceof Mage_Catalog_Model_Product) {
                if($package->getTypeInstance()->getIsFixed()) {
                    $root->addBodyClass('package-fixed');
                }
            }
        }
        return $this;
    }

    public function viewAction() {
        if ($product = $this->_initProduct()) {
            Mage::dispatchEvent('catalog_controller_product_view', array('product' => $product));

            if ($this->getRequest()->getParam('options')) {
                $notice = $product->getTypeInstance(true)->getSpecifyOptionMessage();
                Mage::getSingleton('catalog/session')->addNotice($notice);
            }

            Mage::register('current_package_id', $this->getRequest()->getParam('package'));
            Mage::register('current_item_id', $this->getRequest()->getParam('item'));

            Mage::getSingleton('catalog/session')->setLastViewedProductId($product->getId());
            Mage::getModel('catalog/design')->applyDesign($product, Mage_Catalog_Model_Design::APPLY_FOR_PRODUCT);

            $this->_initProductLayout($product);
            $this->_initLayoutMessages('catalog/session');
            $this->_initLayoutMessages('tag/session');
            $this->_initLayoutMessages('checkout/session');
            $this->renderLayout();
        } else {
            if (isset($_GET['store']) && !$this->getResponse()->isRedirect()) {
                $this->_redirect('');
            } elseif (!$this->getResponse()->isRedirect()) {
                $this->_forward('noRoute');
            }
        }
    }
    
}