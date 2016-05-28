<?php

/**
 * @category   Peexl
 * @package    Peexl_BundleCreatorPlus
 * @copyright  Copyright (c) 2012 Peexl Web Development (http://www.peexl.com)
 * @license    http://framework.zend.com/license/new-bsd    New BSD License
 * @version    1.0
 */
class Peexl_BundleCreatorPlus_Model_Package_Session extends Mage_Core_Model_Session_Abstract {

    public function __construct() {
        $this->init('package_product');
    }

    public function getPackageSession($product) {
        $currentPackage = null;
        $packages = array();
        if ($this->hasData('packages')) {
            $packages = $this->getData('packages');
            foreach ($packages as $package) {
                if ($package->getId() == $product->getId()) {
                    $currentPackage = $package;
                    //break;
                }
            }
        }
        if (!$currentPackage) {
            $currentPackage = Mage::getModel('bundlecreatorplus/package_session_product');
            $currentPackage->buildFromProduct($product);
            $packages[] = $currentPackage;
            $this->setData('packages', $packages);
        }
        return $currentPackage;
    }
    
    public function resetSession($product) {
        $this->removePackageSession($product->getId());
    }

    public function getPackageSessionById($packageId) {
        $product = Mage::getModel('catalog/product')->load($packageId);
        return $this->getPackageSession($product);
    }

    protected function removePackageSession($packageId) {
        if ($this->hasData('packages')) {
            $packages = $this->getData('packages');
            foreach ($packages as $i => $package) {
                if ($package->getId() == $packageId) {
                    unset($packages[$i]);
                }
            }
            $this->unsetData('packages');
            $this->setData('packages', $packages);
        }
        return $this;
    }

}