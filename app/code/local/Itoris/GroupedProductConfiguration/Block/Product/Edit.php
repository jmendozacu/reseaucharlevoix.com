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

 

class Itoris_GroupedProductConfiguration_Block_Product_Edit extends Mage_Core_Block_Template {

	public function optionBlock($subProduct) {
		$wrapper = $this->getLayout()->createBlock('catalog/product_view');
		$wrapper->setTemplate('catalog/product/view/options/wrapper.phtml');
        $coreTemplate = $this->getLayout()->createBlock('core/template');
        $coreTemplate->setTemplate('catalog/product/view/options/js.phtml');
        $wrapper->append($coreTemplate);
		$product = Mage::getModel('catalog/product')->load($subProduct->getId());
		if ($subProduct->getTypeId() == 'bundle') {
			//$optionBlock = $this->getLayout()->createBlock('bundle/catalog_product_view_type_bundle');
			$bundleBlock = $this->getLayout()->createBlock('bundle/catalog_product_view_type_bundle');
			$bundleBlock->setProduct($product);
			//$bundleBlock->addPriceBlockType('bundle', 'bundle/catalog_product_price', 'bundle/catalog/product/price.phtml');
			$bundleBlock->setTemplate('itoris/groupedproductconfiguration/product/bundle/bundle.phtml');
			$wrapper->append($bundleBlock);
			$optionBlock = $this->getLayout()->createBlock('itoris_groupedproductconfiguration/product_bundle_option');
			$optionBlock->setProduct($product);
			$optionBlock->addRenderer('select', 'itoris_groupedproductconfiguration/product_bundle_options_select');
			$optionBlock->addRenderer('multi', 'itoris_groupedproductconfiguration/product_bundle_options_multi');
			$optionBlock->addRenderer('radio', 'itoris_groupedproductconfiguration/product_bundle_options_radio');
			$optionBlock->addRenderer('checkbox', 'itoris_groupedproductconfiguration/product_bundle_options_checkbox');
			//$optionBlock->setTemplate('bundle/catalog/product/view/type/bundle/options.phtml');
			$optionBlock->setTemplate('itoris/groupedproductconfiguration/product/bundle/options.phtml');
		} elseif ($subProduct->getTypeId() == 'configurable') {
			$optionBlock = $this->getLayout()->createBlock('catalog/product_view_type_configurable');
			$optionBlock->setProduct($product);
			$optionBlock->setTemplate('itoris/groupedproductconfiguration/product/configurable/options.phtml');
		} else {
            /* @var $optionBlock Mage_Catalog_Block_Product_View_options */
			$optionBlock = $this->getLayout()->createBlock('catalog/product_view_options');
			$optionBlock->addOptionRenderer('text', 'catalog/product_view_options_type_text', 'catalog/product/view/options/type/text.phtml');
			$optionBlock->addOptionRenderer('file', 'catalog/product_view_options_type_file', 'catalog/product/view/options/type/file.phtml');
			$optionBlock->addOptionRenderer('select', 'catalog/product_view_options_type_select', 'catalog/product/view/options/type/select.phtml');
			$optionBlock->addOptionRenderer('date', 'catalog/product_view_options_type_date', 'catalog/product/view/options/type/date.phtml');
			//Eric: Add bookme, bookmepro, multidate and selectionsiege
			$optionBlock->addOptionRenderer('multidate', 'bookme/adminhtml_catalog_product_view_options_type_multidate', 'itoris/groupedproductconfiguration/product/option/multidate.phtml');
			$optionBlock->addOptionRenderer('selectionsiege', 'bookme/adminhtml_catalog_product_view_options_type_selectionsiege', 'itoris/groupedproductconfiguration/product/option/selectionsiege.phtml');
			$optionBlock->setProduct($product);
			$optionBlock->setTemplate('itoris/groupedproductconfiguration/product/options.phtml');
		}

		$wrapper->append($optionBlock);
		return $wrapper;
	}

	public function optionBlockHtml($optionBlock) {
		return $optionBlock->toHtml();
	}

	public function getGroupedProduct() {
		return Mage::registry('current_product');
	}

    public function showQtyAsCheck() {
        $settings = Mage::getModel('itoris_groupedproductconfiguration/settings')->load(Mage::app()->getWebsite()->getId(), Mage::app()->getStore()->getId(), $this->getGroupedProduct()->getId());
        if ($settings->getShowQtyAs()) {
            return (int)$settings->getShowQtyAs();
        }
        return 0;

    }

	public function getSubProducts() {
		$groupedProduct = $this->getGroupedProduct();
		return $groupedProduct->getTypeInstance(true)->getAssociatedProducts($groupedProduct);
	}

    public function calendarHtml() {
        $calendar = Mage::app()->getLayout()->createBlock('core/html_calendar')->setTemplate('page/js/calendar.phtml');
        return $calendar->toHtml();
    }

	public function getConfig($subProduct) {
		$config = array();
		/*if (!$subProduct->hasOptions()) {
			return Mage::helper('core')->jsonEncode($config);
		}*/

		$_request = Mage::getSingleton('tax/calculation')->getRateRequest(false, false, false);
		/* @var $product Mage_Catalog_Model_Product */
		$product = Mage::getModel('catalog/product')->load($subProduct->getId());
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
				Mage::helper('tax')->getPrice($product, (int)$tierPrice['website_price'], true),
				false, false);
		}
		$config = array(
			'productId'           => $product->getId(),
			'priceFormat'         => Mage::app()->getLocale()->getJsPriceFormat(),
			'includeTax'          => Mage::helper('tax')->priceIncludesTax() ? 'true' : 'false',
			'showIncludeTax'      => Mage::helper('tax')->displayPriceIncludingTax(),
			'showBothPrices'      => Mage::helper('tax')->displayBothPrices(),
			'productPrice'        => Mage::helper('core')->currency($_finalPrice, false, false),
			'productOldPrice'     => Mage::helper('core')->currency($_regularPrice, false, false),
			'priceInclTax'        => Mage::helper('core')->currency($_priceInclTax, false, false),
			'priceExclTax'        => Mage::helper('core')->currency($_priceExclTax, false, false),
			/**
			 * @var skipCalculate
			 * @deprecated after 1.5.1.0
			 */
			'skipCalculate'       => ($_priceExclTax != $_priceInclTax ? 0 : 1),
			'defaultTax'          => $defaultTax,
			'currentTax'          => $currentTax,
			'idSuffix'            => '_clone',
			'oldPlusDisposition'  => 0,
			'plusDisposition'     => 0,
			'plusDispositionTax'  => 0,
			'oldMinusDisposition' => 0,
			'minusDisposition'    => 0,
			'tierPrices'          => $_tierPrices,
			'tierPricesInclTax'   => $_tierPricesInclTax,
		);

		$responseObject = new Varien_Object();
		if (is_array($responseObject->getAdditionalOptions())) {
			foreach ($responseObject->getAdditionalOptions() as $option=>$value) {
				$config[$option] = $value;
			}
		}
		//return $config;
		return Mage::helper('core')->jsonEncode($config);
	}

	protected function _prepareLayout() {
		$head = $this->getLayout()->getBlock('head');
		if ($head) {
			$head->addItem('skin_js', 'js/bundle.js');
		}
		return parent::_prepareLayout();
	}

	/**
	 * @return Itoris_GroupedProductConfiguration_Helper_Data
	 */
	public function getDataHelper() {
		return Mage::helper('itoris_groupedproductconfiguration');
	}
}
?>