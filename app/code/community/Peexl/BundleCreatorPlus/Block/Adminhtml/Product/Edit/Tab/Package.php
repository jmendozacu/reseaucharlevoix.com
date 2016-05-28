<?php

/**
 * @category   Peexl
 * @package    Peexl_BundleCreatorPlus
 * @copyright  Copyright (c) 2012 Peexl Web Development (http://www.peexl.com)
 * @license    http://framework.zend.com/license/new-bsd    New BSD License
 * @version    1.0
 */
class Peexl_BundleCreatorPlus_Block_Adminhtml_Product_Edit_Tab_Package extends Mage_Adminhtml_Block_Widget implements Mage_Adminhtml_Block_Widget_Tab_Interface {

    public function __construct() {
        parent::__construct();
        $this->setProductId($this->getRequest()->getParam('id'));
        $this->setTemplate('bundlecreatorplus/product/edit/tab/package.phtml');
    }

    protected function _prepareLayout() {
        $this->setChild('add_button', $this->getLayout()->createBlock('adminhtml/widget_button')
                        ->setData(array(
                            'label' => Mage::helper('catalog')->__('Add New Item'),
                            'class' => 'add',
                            'id' => 'add_new_item',
                            'on_click' => 'packageItem.add()'
                        ))
        );

        $this->setChild('items_box', $this->getLayout()->createBlock('bundlecreatorplus/adminhtml_product_edit_tab_package_item', 'adminhtml.product.edit.tab.package.item')
        );

        return parent::_prepareLayout();
    }

    public function getItemsBoxHtml() {
        return $this->getChildHtml('items_box');
    }

    public function getAddButtonHtml() {
        return $this->getChildHtml('add_button');
    }

    public function getComponentHtml($component = null, $componentCount) {
        return $this->getLayout()->createBlock('bundlecreatorplus/adminhtml_product_edit_tab_package_component')
                        ->setComponent($component)
                        ->setCount($componentCount)
                        ->toHtml();
    }

    public function getTabClass() {
        return 'ajax';
    }

    public function isReadonly() {
        return $this->_getProduct()->getCompositeReadonly();
    }

    protected function _getProduct() {
        return Mage::registry('current_product');
    }

    public function getTabLabel() {
        return Mage::helper('bundlecreatorplus')->__('Extended Bundle Items');
    }

    public function getTabTitle() {
        return Mage::helper('bundlecreatorplus')->__('Extended Bundle Items');
    }

    public function canShowTab() {
        return true;
    }

    public function isHidden() {
        return false;
    }

}