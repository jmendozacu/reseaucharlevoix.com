<?php
/**
 * ITORIS
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the ITORIS's Magento Extensions License Agreement
 * which is available through the world-wide-web at this URL:
 * http://www.itoris.com/magento-extensions-license.html
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to sales@itoris.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade the extensions to newer
 * versions in the future. If you wish to customize the extension for your
 * needs please refer to the license agreement or contact sales@itoris.com for more information.
 *
 * @category   ITORIS
 * @package    ITORIS_GROUPEDPRODUCTS
 * @copyright  Copyright (c) 2013 ITORIS INC. (http://www.itoris.com)
 * @license    http://www.itoris.com/magento-extensions-license.html  Commercial License
 */


class Itoris_GroupedProductConfiguration_Model_Observer {

	public function saveProduct($obj) {
		$products = $obj->getDataObject();
		if($this->getDataHelper()->isRegisteredAdmin()) {
			$productId = $products->getId();
			if ($productId) {
				$checkStore = (int)Mage::app()->getRequest()->getParam('store');
				$configuration =  Mage::app()->getRequest()->getParam('itoris_groupedproductconfiguration');
				$settings = array();
				if (isset($configuration['show_qty_as'])) {
					$settings['show_qty_as'] =  array('value' => $configuration['show_qty_as']);
				}
				if ($checkStore) {
					$scope = 'store';
				} else {
					$scope = 'default';
				}
				Mage::getModel('itoris_groupedproductconfiguration/settings')->save($settings, $scope, $checkStore, $productId);
			}
		}
	}

    /**
     * @return Itoris_GroupedProductConfiguration_Helper_Data
     */
    public function getDataHelper() {
        return Mage::helper('itoris_groupedproductconfiguration');
    }

}

?>