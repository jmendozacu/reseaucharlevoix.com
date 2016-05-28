<?php

/**
 * @category   Peexl
 * @package    Peexl_BundleCreatorPlus
 * @copyright  Copyright (c) 2012 Peexl Web Development (http://www.peexl.com)
 * @license    http://framework.zend.com/license/new-bsd    New BSD License
 * @version    1.0
 */
class Peexl_BundleCreatorPlus_Block_Adminhtml_Product_Edit_Tab_Package_Item extends Mage_Adminhtml_Block_Widget {

    public function __construct() {
        parent::__construct();
        $this->setTemplate('bundlecreatorplus/product/edit/tab/package/item.phtml');
    }

    public function getFieldId() {
        return 'extendedbundle_item';
    }

    public function getFieldName() {
        return 'extendedbundle_items';
    }

    public function getShowItemsButton() {
        return $this->getLayout()->createBlock('adminhtml/widget_button')
                        ->setData(array(
                            'id' => $this->getFieldId() . '_{{index}}_add_button',
                            'label' => Mage::helper('catalog')->__('Add Selection'),
                            'on_click' => 'packageSelection.showSearch(event)',
                            'class' => 'add'
                        ))->toHtml();
    }

    public function getRemoveButton() {
        return $this->getLayout()->createBlock('adminhtml/widget_button')
                        ->setData(array(
                            'label' => Mage::helper('catalog')->__('Delete Item'),
                            'class' => 'delete delete-product-item',
                            'on_click' => 'packageItem.remove(event)'
                        ))->toHtml();
    }

    public function getSelectionHtml() {
        return $this->getLayout()
                        ->createBlock('bundlecreatorplus/adminhtml_product_edit_tab_package_item_selection')
                        ->toHtml();
    }

    public function getItems() {
        return Mage::registry('current_product')->getTypeInstance()->getItems($this->_getStoreId());
    }
    
    protected function _getStoreId()
    {
        return (int) $this->getRequest()->getParam('store', 0);
    }

}