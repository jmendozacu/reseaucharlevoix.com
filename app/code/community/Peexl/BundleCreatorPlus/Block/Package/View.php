<?php

/**
 * @category   Peexl
 * @package    Peexl_BundleCreatorPlus
 * @copyright  Copyright (c) 2012 Peexl Web Development (http://www.peexl.com)
 * @license    http://framework.zend.com/license/new-bsd    New BSD License
 * @version    1.0
 */
class Peexl_BundleCreatorPlus_Block_Package_View extends Mage_Core_Block_Template {

    protected $_product;
    protected $_packageSession;

    public function _construct() {    
        //$this->_product = Mage::getModel('catalog/product')->load($this->getProduct()->getId());
    }

    protected function _loadCache() {
        return false;
    }

    public function getProduct() {
        if(!$this->_product) {
            $product = Mage::registry('product');
            if (!$product || $product->getTypeId() != 'extendedbundle') {
                $packageId = Mage::app()->getRequest()->getParam('package');
                if ($packageId) {
                    $product = Mage::getModel('catalog/product')->load($packageId);
                }
            }
            $this->_product = $product;
        }
        return $this->_product;
    }

    public function getPackageSession() {
        if(!$this->_packageSession) {
            $this->_packageSession = Mage::helper('bundlecreatorplus')->getPackageSession($this->getProduct());
        }
        return $this->_packageSession;
    }

    public function getItems() {
        return $this->getPackageSession()->getItems();
    }

    public function getActiveItem() {
        return $this->getPackageSession()->getActiveItem();
    }

    public function getPackageTotal() {
        return $this->getProduct()->getTypeInstance()->getPrice();
    }

    public function getBaseUrl() {
        return $this->getPackageSession()->getUrl();
    }

    public function getResetUrl() {
        return $this->getProduct()->getProductUrl() . "?reset=true";
    }

    public function getItemUrl($itemId) {
        return $this->getProduct()->getProductUrl() . "?item=$itemId";
    }

    public function getAddToCartUrl() {
        return $this->getUrl('bcp/cart/add', array('product' => $this->getPackageSession()->getId()));
    }

    public function getAddToWishlistUrl() {
        return $this->getUrl('bcp/wishlist/add', array('product' => $this->getPackageSession()->getId()));
    }

    /**
     * Get JSON encoded configuration array which can be used for JS dynamic
     * price calculation depending on product options
     *
     * @return string
     */
    public function getJsonConfig() {
        $config = array();
        if (!$this->hasOptions()) {
            return Mage::helper('core')->jsonEncode($config);
        }

        $_request = Mage::getSingleton('tax/calculation')->getRateRequest(false, false, false);
        /* @var $product Mage_Catalog_Model_Product */
        $product = $this->getProduct();
        $_request->setProductClassId($product->getTaxClassId());
        $defaultTax = Mage::getSingleton('tax/calculation')->getRate($_request);

        $_request = Mage::getSingleton('tax/calculation')->getRateRequest();
        $_request->setProductClassId($product->getTaxClassId());
        $currentTax = Mage::getSingleton('tax/calculation')->getRate($_request);

        $_regularPrice = $product->getPrice();
        $_finalPrice = $product->getFinalPrice();
        $_priceInclTax = Mage::helper('tax')->getPrice($product, $_finalPrice, true);
        $_priceExclTax = Mage::helper('tax')->getPrice($product, $_finalPrice);
        $_tierPrices = array();
        $_tierPricesInclTax = array();
        foreach ($product->getTierPrice() as $tierPrice) {
            $_tierPrices[] = Mage::helper('core')->currency($tierPrice['website_price'], false, false);
            $_tierPricesInclTax[] = Mage::helper('core')->currency(
                    Mage::helper('tax')->getPrice($product, (int) $tierPrice['website_price'], true), false, false);
        }
        $config = array(
            'productId' => $product->getId(),
            'priceFormat' => Mage::app()->getLocale()->getJsPriceFormat(),
            'includeTax' => Mage::helper('tax')->priceIncludesTax() ? 'true' : 'false',
            'showIncludeTax' => Mage::helper('tax')->displayPriceIncludingTax(),
            'showBothPrices' => Mage::helper('tax')->displayBothPrices(),
            'productPrice' => Mage::helper('core')->currency($_finalPrice, false, false),
            'productOldPrice' => Mage::helper('core')->currency($_regularPrice, false, false),
            'priceInclTax' => Mage::helper('core')->currency($_priceInclTax, false, false),
            'priceExclTax' => Mage::helper('core')->currency($_priceExclTax, false, false),
            /**
             * @var skipCalculate
             * @deprecated after 1.5.1.0
             */
            'skipCalculate' => ($_priceExclTax != $_priceInclTax ? 0 : 1),
            'defaultTax' => $defaultTax,
            'currentTax' => $currentTax,
            'idSuffix' => '_clone',
            'oldPlusDisposition' => 0,
            'plusDisposition' => 0,
            'plusDispositionTax' => 0,
            'oldMinusDisposition' => 0,
            'minusDisposition' => 0,
            'tierPrices' => $_tierPrices,
            'tierPricesInclTax' => $_tierPricesInclTax,
        );

        $responseObject = new Varien_Object();
        Mage::dispatchEvent('catalog_product_view_config', array('response_object' => $responseObject));
        if (is_array($responseObject->getAdditionalOptions())) {
            foreach ($responseObject->getAdditionalOptions() as $option => $value) {
                $config[$option] = $value;
            }
        }

        return Mage::helper('core')->jsonEncode($config);
    }

    /**
     * Return true if product has options
     *
     * @return bool
     */
    public function hasOptions() {
        if ($this->getProduct()->getTypeInstance(true)->hasOptions($this->getProduct())) {
            return true;
        }
        return false;
    }
    
    /**
     * Get default qty - either as preconfigured, or as 1.
     * Also restricts it by minimal qty.
     *
     * @param null|Mage_Catalog_Model_Product $product
     * @return int|float
     */
    public function getProductDefaultQty($product = null)
    {
        if (!$product) {
            $product = $this->getProduct();
        }

        $qty = $this->getMinimalQty($product);
        $config = $product->getPreconfiguredValues();
        $configQty = $config->getQty();
        if ($configQty > $qty) {
            $qty = $configQty;
        }
        
        if(!$qty) {
            $qty = $this->getPackageSession()->getQty();
        }

        return $qty;
    }

}