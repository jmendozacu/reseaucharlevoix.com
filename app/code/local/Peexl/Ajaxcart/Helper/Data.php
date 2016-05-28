<?php

class Peexl_Ajaxcart_Helper_Data extends Mage_Core_Helper_Abstract {

    public function renderOptions($addToExtendedBundle = false) {
        $product = Mage::registry('current_product');
        if (!$product->isConfigurable() && $product->getTypeId() != 'bundle' && $product->getTypeId() != 'simple' && $product->getTypeId() != 'downloadable' && $product->getTypeId() != 'virtual' && $product->getTypeId() != 'grouped') {
            echo 'false';
            die();
        }
        
        if($addToExtendedBundle) {
            $packageSession = Mage::helper('bundlecreatorplus')->getPackageSession();
            $isFixedPrice = $packageSession->getProduct()->getTypeInstance()->getIsFixed();
            $_itemProduct = $packageSession->getActiveItem()->getProduct();
            if($_itemProduct) {
                $rp = $_itemProduct->getRequestParams();
                if(isset($rp['super_attribute'])) {                 
                    $itemProductId = $rp['product'];
                }
                if($itemProductId == $product->getId()) {            
                    if($params = $_itemProduct->getRequestParams()) {
                        $buyRequest = new Varien_Object($params);
                        if ($buyRequest) {
                            Mage::helper('catalog/product')->prepareProductOptions($product, $buyRequest);
                        }
                    }
                }
            }
        }

        if ($product->getTypeId() == 'grouped') {

            $g = Mage::getSingleton('core/layout');

            $product_type_data_extra = $g->createBlock('core/text_list', 'product_type_data_extra');
            if (version_compare(Mage::getVersion(), '1.4.0.1', '>')) {
                $reference_product_type_data_extra = $g->createBlock('cataloginventory/stockqty_type_grouped', 'reference_product_type_data_extra')
                        ->setTemplate('cataloginventory/stockqty/composite.phtml');
                $product_type_data_extra->append($reference_product_type_data_extra);
            }
            if($addToExtendedBundle) {
                $addtocart = $g->createBlock('catalog/product_view', 'addtocart')
                    ->setTemplate('bundlecreatorplus/product/view/addtopackage.phtml')
                    ->setIsAjax(true);
            } else {
                $addtocart = $g->createBlock('catalog/product_view', 'addtocart')
                    ->setTemplate('peexl_ajaxcart/addtocart.phtml');
            }
            $grouped = $g->createBlock('catalog/product_view_type_grouped', 'product_type_data')
                    ->setTemplate('peexl_ajaxcart/grouped.phtml')
                    ->append($product_type_data_extra)
                    ->append($addtocart);
            return $grouped->renderView();
        } else {
            $block = Mage::getSingleton('core/layout');
            //options.phtml
            $options = $block->createBlock('catalog/product_view_options', 'product_options')
                    ->setTemplate('catalog/product/view/options.phtml')
                    ->addOptionRenderer('text', 'catalog/product_view_options_type_text', 'catalog/product/view/options/type/text.phtml')
                    ->addOptionRenderer('file', 'catalog/product_view_options_type_file', 'catalog/product/view/options/type/file.phtml')
                    ->addOptionRenderer('select', 'catalog/product_view_options_type_select', 'catalog/product/view/options/type/select.phtml')
                    ->addOptionRenderer('date', 'catalog/product_view_options_type_date', 'catalog/product/view/options/type/date.phtml');
            $price = $block->createBlock('catalog/product_view', 'product_price')
                    ->setTemplate('catalog/product/view/price_clone.phtml');
            $js = $block->createBlock('core/template', 'product_js')
                    ->setTemplate('catalog/product/view/options/js.phtml');
            if ($product->getTypeId() == 'bundle') {
                $price->addPriceBlockType('bundle', 'bundle/catalog_product_price', 'peexl_ajaxcart/bundle/catalog/product/view/price.phtml');
                $tierprices = $block->createBlock('bundle/catalog_product_view', 'tierprices')
                        ->setTemplate('bundle/catalog/product/view/tierprices.phtml');
                $extrahind = $block->createBlock('cataloginventory/qtyincrements', 'extrahint')
                        ->setTemplate('cataloginventory/qtyincrements.phtml');
                $bundle = $block->createBlock('bundle/catalog_product_view_type_bundle', 'type_bundle_options')
                        ->setTemplate('bundle/catalog/product/view/type/bundle/options.phtml');
                $bundle->addRenderer('select', 'bundle/catalog_product_view_type_bundle_option_select');
                $bundle->addRenderer('multi', 'bundle/catalog_product_view_type_bundle_option_multi');
                $bundle->addRenderer('radio', 'bundle/catalog_product_view_type_bundle_option_radio');
                $bundle->addRenderer('checkbox', 'bundle/catalog_product_view_type_bundle_option_checkbox');
                $bundlejs = $block->createBlock('bundle/catalog_product_view_type_bundle', 'jsforbundle')
                        ->setTemplate('peexl_ajaxcart/bundle.phtml');
            }
            if ($product->isConfigurable()) {
                $configurable = $block->createBlock('catalog/product_view_type_configurable', 'product_configurable_options')
                        ->setTemplate('catalog/product/view/type/options/configurable.phtml');
                $configurable->append($block->createBlock('core/text_list', 'attr_renderers'));
                $configurable->append($block->createBlock('core/text_list', 'after'));
                $configurableData = $block->createBlock('catalog/product_view_type_configurable', 'product_type_data')
                        ->setTemplate('catalog/product/view/type/configurable.phtml');
            }
            if ($product->getTypeId() == 'downloadable') {
                $downloadable = $block->createBlock('downloadable/catalog_product_links', 'product_downloadable_options')
                        ->setTemplate('peexl_ajaxcart/downloadable/catalog/product/links.phtml');
                $downloadableData = $block->createBlock('downloadable/catalog_product_view_type', 'product_type_data')
                        ->setTemplate('downloadable/catalog/product/type.phtml');
            }
            if($addToExtendedBundle) {
                $addtocart = $block->createBlock('catalog/product_view', 'addtocart')
                    ->setTemplate('bundlecreatorplus/product/view/addtopackage.phtml')
                    ->setIsAjax(true);
            } else {
                $addtocart = $block->createBlock('catalog/product_view', 'addtocart')
                    ->setTemplate('peexl_ajaxcart/addtocart.phtml');
            }
            $main = $block->createBlock('catalog/product_view')
                    ->setTemplate('peexl_ajaxcart/wrapper.phtml')
                    ->append($js)
                    ->append($options);
            if($addToExtendedBundle) {
                $main->setIsFixedPrice($isFixedPrice);
            }
            if (version_compare(Mage::getVersion(), '1.4.0.1', '>')) {
                $calendar = $block->createBlock('core/html_calendar', 'html_calendar')
                        ->setTemplate('page/js/calendar.phtml');
                $main->append($calendar);
            }
            if ($product->isConfigurable()) {
                $main->append($configurableData);
                $main->append($configurable);
            }
            if ($product->getTypeId() == 'downloadable') {
                $main->append($downloadableData);
                $main->append($downloadable);
                $main->insert($downloadable);
            }
            if ($product->getTypeId() == 'bundle') {
                $main->append($bundle);
                $main->insert($bundle);
                $main->append($tierprices);
                $main->append($extrahind);
                $main->append($bundlejs);
            }
            $main->append($price)->append($addtocart);
            return $main->renderView();
        }
    }

    public function sendResponse($link, $content = null) {
        $options = null;
        $hasproduct = "0";
                                                        
        if ($product = Mage::registry('current_product')) {
            $options = $product->getHasOptions();
            $hasproduct = "1";
            if ($product->getTypeId() == 'grouped') {
                $options = "1";
            }
        }

        try {
            if ($product = Mage::getModel('peexl_ajaxcart/observer')->getProduct()) {
                $hasproduct = 1;
            }
        } catch (Mage_Core_Exception $e) {
            Mage::helper("logger")->error($e->getMessage());
        }

        $productName = null;
        $item_block = null;
        if ($hasproduct && !$options) {
            $item_layout = Mage::getSingleton('core/layout');
            $item_block = $item_layout->createBlock('catalog/product_list', 'item')->setTemplate('peexl_ajaxcart/catalog/product/item.phtml')->setData('product', $product)->renderView();
            $productName = $product->getName();          
        }
        
        header('content-type: text/javascript');
        echo json_encode(array('link' => $link, 'has_options' => $options, 'content' => $content, 'product_name' => $productName, 'cart_item' => $item_block));   
        die;
    }

    public function getCartLinkText() {
        $count = Mage::helper('checkout/cart')->getSummaryCount();
        if ($count == 1) {
            $text = Mage::helper('peexl_ajaxcart')->__('My Cart (%s item)', $count);
        } elseif ($count > 0) {
            $text = Mage::helper('peexl_ajaxcart')->__('My Cart (%s items)', $count);
        } else {
            $text = Mage::helper('peexl_ajaxcart')->__('My Cart');
        }
        return $text;
    }

    public function getJQquery() {
        if (null == Mage::registry('jquery')) {
            Mage::register('jquery', 1);
            return 'peexl_ajaxcart/js/jquery.min.js';
        }
        return;
    }

    public function getJQqueryNoconflict() {
        if (null == Mage::registry('jquerynoconflict')) {
            Mage::register('jquerynoconflict', 1);
            return 'peexl_ajaxcart/js/noconflict.js';
        }
        return;
    }
}
