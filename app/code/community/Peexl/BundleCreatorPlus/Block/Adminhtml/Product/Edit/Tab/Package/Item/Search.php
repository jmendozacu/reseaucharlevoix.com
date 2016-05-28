<?php

/**
 * @category   Peexl
 * @package    Peexl_BundleCreatorPlus
 * @copyright  Copyright (c) 2012 Peexl Web Development (http://www.peexl.com)
 * @license    http://framework.zend.com/license/new-bsd    New BSD License
 * @version    1.0
 */
class Peexl_BundleCreatorPlus_Block_Adminhtml_Product_Edit_Tab_Package_Item_Search extends Mage_Adminhtml_Block_Widget {

    protected function _construct() {
        $this->setId('extendedbundle_item_selection_search');
        $this->setTemplate('bundlecreatorplus/product/edit/tab/package/item/search.phtml');
    }

    public function getHeaderText() {
        return Mage::helper('catalog')->__('Please Select Products to Add');
    }

    protected function _prepareLayout() {
        $this->setChild(
                'grid', $this->getLayout()->createBlock('bundlecreatorplus/adminhtml_product_edit_tab_package_item_search_grid', 'adminhtml.product.edit.tab.package.items.search.grid')
        );
        return parent::_prepareLayout();
    }

    protected function _beforeToHtml() {
        $this->getChild('grid')->setIndex($this->getIndex())
                ->setFirstShow($this->getFirstShow());

        return parent::_beforeToHtml();
    }

    public function getButtonsHtml() {
        $addButtonData = array(
            'id' => 'add_button_' . $this->getIndex(),
            'label' => Mage::helper('sales')->__('Add Selected Product(s) to Option'),
            'onclick' => 'packageSelection.productGridAddSelected(event)',
            'class' => 'add',
        );
        return $this->getLayout()->createBlock('adminhtml/widget_button')->setData($addButtonData)->toHtml();
    }

    public function getHeaderCssClass() {
        return 'head-catalog-product';
    }

}
