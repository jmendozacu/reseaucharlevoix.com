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



class Itoris_GroupedProductConfiguration_Block_Admin_Product_Edit_Tab_Configuration
    extends Mage_Adminhtml_Block_Catalog_Form
    implements Mage_Adminhtml_Block_Widget_Tab_Interface
{

    public function __construct()
    {
        parent::__construct();
        $this->_setAfter();
    }

    /**
     * Set after general tab
     *
     * @return Itoris_GroupedProductConfiguration_Block_Admin_Product_Edit_Tab_Configuration
     */
    protected function _setAfter() {
        /** @var $product Mage_Catalog_Model_Product */
        $product = $this->getCurrentProduct();
        $attributes = $product->getAttributes();
        $altAttribute = null;
        /** @var $attribute Mage_Catalog_Model_Resource_Eav_Attribute */
        foreach ($attributes as $attribute) {
            if ($attribute->getAttributeCode() == 'general') {
                return $this->_setAfterAttribute($attribute);
            } else if ($attribute->getAttributeCode() == 'name') {
                $altAttribute = $attribute;
            }
        }
        if ($altAttribute) {
            return $this->_setAfterAttribute($altAttribute);
        }
        return $this;
    }

    public function _setAfterAttribute($attribute) {
        $info = $attribute->getAttributeSetInfo();
        if (is_array($info)) {
            foreach ($info as $set) {
                if (isset($set['group_id'])) {
                    $this->setAfter('group_' . $set['group_id']);
                    break;
                }
            }
        }
        return $this;
    }

    public function getTabLabel() {
        return $this ->__('Grouped Product Configuration');
    }

    public function getTabTitle() {
        return $this ->__('Grouped Product Configuration');
    }

    public function canShowTab() {
        if($this->getDataHelper()->getSettings()->getEnabled() && $this->getDataHelper()->isAdminRegistered() && $this->getCurrentProduct()->getTypeId() == 'grouped') {
            return true;
        } else {
            return false;
        }
    }

    public function isHidden() {
        return false;
    }

    /**
     * @return Itoris_GroupedProductConfiguration_Helper_Data
     */
    public function getDataHelper() {
        return Mage::helper('itoris_groupedproductconfiguration');
    }

    public function getCurrentProduct() {
        return Mage::registry('current_product');
    }

    protected function _prepareForm() {
        $form = new Varien_Data_Form();
        $productId = $this->getCurrentProduct()->getId();
        $checkStore = Mage::app()->getRequest()->getParam('store');
        $config = Mage::getModel('itoris_groupedproductconfiguration/settings')->load(0, $checkStore, $productId);
        $productVisibility = $form->addFieldset('product_configuration', array(
            'legend' => $this->__('Grouped Product Configuration'),
        ));

        $productVisibility->addField('show_qty_as', 'select', array(
            'name'		   => 'itoris_groupedproductconfiguration[show_qty_as]',
            'label'        => $this->__('Show QTY for Associated Products as'),
            'title'        => $this->__('Show QTY for Associated Products as'),
            'values' => array(
                array(
                    'label' => $this->__('Input Box'),
                    'value' => Itoris_GroupedProductConfiguration_Model_Settings::SHOW_QTY_INPUT,
                ),
                 array(
                     'label' => $this->__('Check Box'),
                     'value' => Itoris_GroupedProductConfiguration_Model_Settings::SHOW_QTY_CHECK,
                 ),
            ),
            'value'         => $config->getShowQtyAs(),
            'show_checkbox' => $config->isParentValue('show_qty_as')
        ));

        $this->setForm($form);

    }
}

?>