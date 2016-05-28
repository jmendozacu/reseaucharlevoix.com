<?php

/**
 * @category   Peexl
 * @package    Peexl_BundleCreatorPlus
 * @copyright  Copyright (c) 2012 Peexl Web Development (http://www.peexl.com)
 * @license    http://framework.zend.com/license/new-bsd    New BSD License
 * @version    1.0
 */
class Peexl_BundleCreatorPlus_Adminhtml_Package_SelectionController extends Mage_Adminhtml_Controller_Action {

    protected function _construct() {
        $this->setUsedModuleName('Peexl_BundleCreatorPlus');
    }
    
    protected function _isAllowed() {
        return true;
    }

    public function searchAction() {
        return $this->getResponse()->setBody(
                        $this->getLayout()
                                ->createBlock('bundlecreatorplus/adminhtml_product_edit_tab_package_item_search')
                                ->setIndex($this->getRequest()->getParam('index'))
                                ->setFirstShow(true)
                                ->toHtml()
        );
    }

    public function gridAction() {
        return $this->getResponse()->setBody(
                        $this->getLayout()
                                ->createBlock('bundlecreatorplus/adminhtml_product_edit_tab_package_item_search_grid', 'adminhtml.catalog.product.edit.tab.bundle.option.search.grid')
                                ->setIndex($this->getRequest()->getParam('index'))
                                ->toHtml()
        );
    }

}
