<?php

class Peexl_Ajaxcart_Model_Observer {

    protected function _initProduct() {
        $productId = (int) Mage::app()->getFrontController()->getRequest()->getParam('product');
        if ($productId) {
            $product = Mage::getModel('catalog/product')
                    ->setStoreId(Mage::app()->getStore()->getId())
                    ->load($productId);
            if ($product->getId()) {
                return $product;
            }
        }
        return false;
    }

    public function getProduct() {
        return $this->_initProduct();
    }

    public function addToCart($observer) {
        if (Mage::getStoreConfig('ajaxcart_cfg/general/enable') && Mage::app()->getFrontController()->getRequest()->getParam('isAjax') != "") {
            $product = $this->_initProduct();

            $message = Mage::getSingleton('checkout/session')->getMessages();
            $hasnotice = false;

            if ($message->getItems('notice')) {
                $hasnotice = true;
            }

            Mage::getSingleton('checkout/session')->getData('messages')->clear();

            $ajax = Mage::app()->getFrontController()->getRequest()->getParams();

            if ($product->getTypeId() == 'grouped' AND Mage::app()->getFrontController()->getRequest()->getParam('isCOPage') != null AND !isset($ajax['related_product'])) {
                Mage::getSingleton('checkout/session')->setIsajax("1");
                return;
            }

            if ($product->getHasOptions() AND $hasnotice) {
                $hasnotice = false;
                Mage::getSingleton('checkout/session')->setIsajax("1");
                Mage::getSingleton('checkout/session')->setIspage(Mage::app()->getFrontController()->getRequest()->getParam('isCOPage'));
                return;
            }
            Mage::helper('peexl_ajaxcart')->sendResponse(Mage::helper('peexl_ajaxcart')->getCartLinkText());
        }
    }

    public function addOptionsWishList($observer) {
        $this->addOptions($observer);
    }

    public function addOptions($observer) {
        if (Mage::getStoreConfig('ajaxcart_cfg/general/enable')) {
            $params = Mage::app()->getFrontController()->getRequest()->getParams();
            if (isset($params['isAjax'])) {
                $product = Mage::registry('current_product');
                if($product->getTypeId() == 'extendedbundle') {                    
                    $layout = Mage::getSingleton('core/layout');             
                    echo json_encode(array('update' => array(
                            array('element' => 'package-view', 'content' => $layout->getBlock('package.view')->toHtml()),
                            array('element' => 'package-leftnav', 'content' => $layout->getBlock('catalog.leftnav')->toHtml()),
                            array('element' => 'package-preview', 'content' => $layout->getBlock('package.preview')->toHtml())
                        )));
                    die;
                }
                Mage::helper('peexl_ajaxcart')->sendResponse("", Mage::helper('peexl_ajaxcart')->renderOptions(isset($params['package'])));
            }
        }
    }

    public function predispatchCheckoutCartAdd($observer) {
        $controllerAction = $observer->getControllerAction();
        $request = $controllerAction->getRequest();
        if ($request->getParam('isAjax')) {
            if (($productId = $request->getParam('product'))) {
                $product = Mage::getModel('catalog/product')->load($productId);
                if (($product->getId() && $product->getHasOptions() || $product->getTypeId() == 'grouped') && $productUrl = $this->_getProductUrl($product)) {
                    $product->setRequestParams($request->getParams());
                    $request = new Varien_Object($request->getParams());
                    $cartCandidates = $product->getTypeInstance(true)->prepareForCartAdvanced($request, $product);
                    if (is_string($cartCandidates) || count($request->getParams()) == 1) {
                        header('content-type: text/javascript');
                        echo json_encode(array('redirect' => $productUrl));   
                        die;
                    }
                }
            }
        }
    }

    protected function _getProductUrl(Mage_Catalog_Model_Product $product) {
        $query = array();
        if ($product->getHasOptions()) {
            $query = array('options' => 'cart');
        }
        return $product->getUrlModel()->getUrl($product, array('_query' => $query, '_secure' => Mage::app()->getRequest()->isSecure()));
    }

}
