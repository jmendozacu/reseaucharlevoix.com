<?php

/**
 * Created by PhpStorm.
 * User: Nguyen
 * Date: 4/11/2016
 * Time: 9:17 PM
 */
class Benova_ToolboxDesigner_Block_Adminhtml_Toolbox extends Mage_Adminhtml_Block_Catalog_Product
{
    public function __construct()
    {
        $this->_blockGroup = 'toolbox';
        $this->_controller = 'adminhtml_toolbox';
        $this->_headerText = Mage::helper('catalog')->__('Toolbox List');
        parent::__construct();
    }
    /**
     * Prepare button and grid
     *
     * @return Mage_Adminhtml_Block_Catalog_Product
     */
    protected function _prepareLayout()
    {
        $this->_addButton('add_new', array(
            'label'   => Mage::helper('catalog')->__('Add Product'),
            'onclick' => "setLocation('{$this->getUrl('*/*/new')}')",
            'class'   => 'add'
        ));

        $this->setChild('grid', $this->getLayout()->createBlock('toolbox/adminhtml_toolbox_grid', 'toolbox.product.grid'));
    }
}