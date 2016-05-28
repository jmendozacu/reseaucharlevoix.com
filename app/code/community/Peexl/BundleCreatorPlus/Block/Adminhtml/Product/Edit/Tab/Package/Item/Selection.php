<?php

/**
 * @category   Peexl
 * @package    Peexl_BundleCreatorPlus
 * @copyright  Copyright (c) 2012 Peexl Web Development (http://www.peexl.com)
 * @license    http://framework.zend.com/license/new-bsd    New BSD License
 * @version    1.0
 */
class Peexl_BundleCreatorPlus_Block_Adminhtml_Product_Edit_Tab_Package_Item_Selection extends Mage_Adminhtml_Block_Widget {

    public function __construct() {
        $this->setTemplate('bundlecreatorplus/product/edit/tab/package/item/selection.phtml');
    }

    public function getFieldId() {
        return 'item_option';
    }

    public function getFieldName() {
        return 'product[item_options]';
    }

    protected function _prepareLayout() {
        $this->setChild('selection_delete_button', $this->getLayout()->createBlock('adminhtml/widget_button')
                        ->setData(array(
                            'label' => Mage::helper('catalog')->__('Delete'),
                            'class' => 'delete icon-btn',
                            'on_click' => 'packageSelection.remove(event)'
                        ))
        );
        return parent::_prepareLayout();
    }

    public function getSelectionDeleteButtonHtml() {
        return $this->getChildHtml('selection_delete_button');
    }

    public function getSelectionSearchUrl() {
        return $this->getUrl('adminhtml/package_selection/search');
    }

}
