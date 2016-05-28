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

 

require_once Mage::getModuleDir('controllers', 'Mage_Checkout') . '/CartController.php';

class Itoris_GroupedProductConfiguration_CartController extends Mage_Checkout_CartController {

	public function addAction() {
        try {
            $this->_getSession()->getMessages(true);
            $url = $this->getRequest()->getParam('back_url');
            $this->_getSession()->setRedirectUrl($url);
            $params = $this->getRequest()->getParams();
            $associatedProductParams = array();
            $cart = $this->_getCart();
            $qtyCount = 0;
            foreach ($params as $paramName => $value) {
                if ($paramName == 'super_group' && is_array($params['super_group'])) {
                    foreach ($value as $productId => $qty) {
                        if ($qty >= 1) {
                            $product = Mage::getModel('catalog/product')->load((int)$productId);
                            $productOptions = $product->getOptions();
                            $options = '';
                            foreach ($productOptions as $optionId => $option) {
                                $options .=  $optionId . ',';
                            }
                            $options = substr($options, 0, -1);
                            $optionArray = array();
                            if (array_key_exists('options', $params)) {
                                foreach ($params['options'] as $idOption => $checkValue) {
                                    if (strpos($options, (string)$idOption) !== false) {
                                        $optionArray[$idOption] = $checkValue;
                                    }
                                }
                            }
                            $bundleOptions = array();
                            $bundleOptionsQty = array();
                            $related = array_key_exists('related_product', $params) ? $params['related_product'] : null;
                            if (array_key_exists('bundle_option', $params) && array_key_exists($productId, $params['bundle_option'])) {
                                $bundleOptions = $params['bundle_option'][$productId];
                            }
                            if (array_key_exists('bundle_option_qty', $params) && array_key_exists($productId, $params['bundle_option_qty'])) {
                                $bundleOptionsQty = $params['bundle_option_qty'][$productId];
                            }

                            $associatedProductParams = array(
                                //'uenc' => $params['uenc'],
                                'product' => (string)$productId,
                                'related_product' => $related,
                                'options' => $optionArray,
                                'super_attribute' => isset($params['super_product'][$productId]['super_attribute'])
                                    ? $params['super_product'][$productId]['super_attribute'] : null,
                                'bundle_option' => $bundleOptions,
                                'bundle_option_qty' => $bundleOptionsQty,
                                'qty' => $qty
                            );
                            $cart->addProduct($product, $associatedProductParams);
							$cart->getQuote()->getItemsCollection()->getLastItem()->addOption(array(
									'code' => 'product_type',
									//Eric: Put real product type to cart, ITORIS BUG !!!
									'value' => $product->getTypeId(),
									'product_id' => intval($params['product'])
								)
							);

                            if (!empty($related)) {
                                $cart->addProductsByIds(explode(',', $related));
                            }
                            $qtyCount++;
                        }
                    }
                }
            }
            if ($qtyCount) {
                $cart->save();
                if (isset($params['product'])) {
                    $product = Mage::getModel('catalog/product')->load($params['product']);
                    if ($product->getId()) {
                        $message = Mage::helper('checkout')->__('%s was added to your shopping cart.', Mage::helper('core')->htmlEscape($product->getName()));
                        Mage::getSingleton('core/session')->addSuccess($message);
                    }
                }
                if (!!intval(Mage::getStoreConfig('checkout/cart/redirect_to_cart'))) $this->_redirect('checkout/cart'); else $this->getResponse()->setRedirect($url);
            } else {
                Mage::getSingleton('core/session')->addNotice('Please specify the quantity of product(s)');
                if ($url) {
                    $this->getResponse()->setRedirect($url);
                } else if (isset($params['product'])) {
                    $product = Mage::getModel('catalog/product')->load($params['product']);
                    $this->_redirectUrl($product->getProductUrl());
                } else {
                    $this->_redirect('checkout/cart');
                }
            }
        } catch (Mage_Core_Exception $e) {
            Mage::getSingleton('core/session')->addError($e->getMessage());
            $this->getResponse()->setRedirect($url);
        } catch (Exception $e) {
            Mage::logException($e);
            $this->getResponse()->setRedirect($url);
        }
	}
}
?>