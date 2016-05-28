<?php

/**
 * @category   Peexl
 * @package    Peexl_BundleCreatorPlus
 * @copyright  Copyright (c) 2012 Peexl Web Development (http://www.peexl.com)
 * @license    http://framework.zend.com/license/new-bsd    New BSD License
 * @version    1.0
 */
class Peexl_BundleCreatorPlus_WishlistController extends Mage_Core_Controller_Front_Action {

    /**
     * If true, authentication in this controller (wishlist) could be skipped
     *
     * @var bool
     */
    protected $_skipAuthentication = false;

    public function preDispatch()
    {
        parent::preDispatch();

        if (!$this->_skipAuthentication && !Mage::getSingleton('customer/session')->authenticate($this)) {
            $this->setFlag('', 'no-dispatch', true);
            if (!Mage::getSingleton('customer/session')->getBeforeWishlistUrl()) {
                Mage::getSingleton('customer/session')->setBeforeWishlistUrl($this->_getRefererUrl());
            }
            Mage::getSingleton('customer/session')->setBeforeWishlistRequest($this->getRequest()->getParams());
        }
        if (!Mage::getStoreConfigFlag('wishlist/general/active')) {
            $this->norouteAction();
            return;
        }
    }
    
    /**
     * Retrieve wishlist object
     * @param int $wishlistId
     * @return Mage_Wishlist_Model_Wishlist|bool
     */
    protected function _getWishlist($wishlistId = null)
    {
        $wishlist = Mage::registry('wishlist');
        if ($wishlist) {
            return $wishlist;
        }

        try {
            if (!$wishlistId) {
                $wishlistId = $this->getRequest()->getParam('wishlist_id');
            }
            $customerId = Mage::getSingleton('customer/session')->getCustomerId();
            /* @var Mage_Wishlist_Model_Wishlist $wishlist */
            $wishlist = Mage::getModel('wishlist/wishlist');
            if ($wishlistId) {
                $wishlist->load($wishlistId);
            } else {
                $wishlist->loadByCustomer($customerId, true);
            }

            if (!$wishlist->getId() || $wishlist->getCustomerId() != $customerId) {
                $wishlist = null;
                Mage::throwException(
                    Mage::helper('wishlist')->__("Requested wishlist doesn't exist")
                );
            }

            Mage::register('wishlist', $wishlist);
        } catch (Mage_Core_Exception $e) {
            Mage::getSingleton('wishlist/session')->addError($e->getMessage());
            return false;
        } catch (Exception $e) {
            Mage::getSingleton('wishlist/session')->addException($e,
                Mage::helper('wishlist')->__('Wishlist could not be created.')
            );
            return false;
        }

        return $wishlist;
    }
    
    public function addAction() {
        if ($packageId = $this->getRequest()->getParam('product')) {
            $wishlist = $this->_getWishlist();
            if (!$wishlist) {
                return $this->norouteAction();
            }

            $session = Mage::getSingleton('customer/session');

            $product = Mage::getModel('catalog/product')->load($packageId);
            if (!$product->getId() || !$product->isVisibleInCatalog()) {
                $session->addError($this->__('Cannot specify product.'));
                $this->_redirect('*/');
                return;
            }
            try {
                $requestParams = $this->getRequest()->getParams();
                if ($session->getBeforeWishlistRequest()) {
                    $requestParams = $session->getBeforeWishlistRequest();
                    $session->unsBeforeWishlistRequest();
                }
                $requestParams['items'] = $this->_prepareItems($packageId);
                $requestParams['uniqueKey'] = rand(0, 999999999);
                $buyRequest = new Varien_Object($requestParams);
                
                $result = $wishlist->addNewItem($product, $buyRequest);
                if (is_string($result)) {
                    Mage::throwException($result);
                }
                $wishlist->save();

                $referer = $session->getBeforeWishlistUrl();
                if ($referer) {
                    $session->setBeforeWishlistUrl(null);
                } else {
                    $referer = $this->_getRefererUrl();
                }

                /**
                 *  Set referer to avoid referring to the compare popup window
                 */
                $session->setAddActionReferer($referer);

                Mage::helper('wishlist')->calculate();

                $message = $this->__('%1$s has been added to your wishlist. Click <a href="%2$s">here</a> to continue shopping.', $product->getName(), Mage::helper('core')->escapeUrl($referer));
                $session->addSuccess($message);
                }
                catch (Mage_Core_Exception $e) {
                    $session->addError($this->__('An error occurred while adding item to wishlist: %s', $e->getMessage()));
                }
                catch (Exception $e) {
                    $session->addError($this->__('An error occurred while adding item to wishlist.'));
                }

                $this->_redirect('wishlist', array('wishlist_id' => $wishlist->getId()));
        }
    }

    protected function _prepareItems($packageId) {
        $items = array();
        $sessionItems = Mage::helper('bundlecreatorplus')->getPackageSessionById($packageId)->getItems();
        foreach ($sessionItems as $item) {
            if($item->getProduct()) {
                $items[$item->getCode()] = array('product_id' => $item->getProduct()->getId());
            }
        }
        return $items;
    }

}