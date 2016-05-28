<?php
/**
 * @category   Peexl
 * @package    Peexl_BundleCreatorPlus
 * @copyright  Copyright (c) 2012 Peexl Web Development (http://www.peexl.com)
 * @license    http://framework.zend.com/license/new-bsd    New BSD License
 * @version    1.0
 */
class Peexl_BundleCreatorPlus_Block_Adminhtml_Product_Edit_Tab_Attributes_Price
    extends Mage_Adminhtml_Block_Catalog_Form_Renderer_Fieldset_Element
{

    /**
     * Class constructor
     */
    public function __construct()
    {
        parent::__construct();
        $this->setCanEditPrice(true);
        $this->setCanReadPrice(true);
    }

    /**
     * Get Element Html
     *
     * @return string
     */
    public function getElementHtml()
    {
        $elementHtml = parent::getElementHtml();

        $switchAttributeCode = $this->getAttribute()->getAttributeCode().'_type';
        $switchAttributeValue = $this->getProduct()->getData($switchAttributeCode);

        $html = '<select name="product[' . $switchAttributeCode . ']" id="' . $switchAttributeCode
        . '" type="select" class="required-entry select next-toinput">
            <option value="">' . $this->__('-- Select --') . '</option>
            <option ' . ($switchAttributeValue == Mage::helper('bundlecreatorplus')->getPriceType('fixed') ? 'selected' : '')
            . ' value="' . Mage::helper('bundlecreatorplus')->getPriceType('fixed') . '">' . $this->__('Fixed') . '</option>
            <option ' . ($switchAttributeValue == Mage::helper('bundlecreatorplus')->getPriceType('dynamic') ? 'selected' : '')
            . ' value="' . Mage::helper('bundlecreatorplus')->getPriceType('dynamic') . '">' . $this->__('Dynamic') . '</option>
        </select>';
        
        return $html;
    }

    public function getProduct()
    {
        if (!$this->getData('product')){
            $this->setData('product', Mage::registry('product'));
        }
        return $this->getData('product');
    }
}
