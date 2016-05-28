<?php
/**
 * @category   Peexl
 * @package    Peexl_BundleCreatorPlus
 * @copyright  Copyright (c) 2015 Peexl Web Development (http://www.peexl.com)
 * @license    http://framework.zend.com/license/new-bsd    New BSD License
 * @version    1.0
 */
class Peexl_BundleCreatorPlus_Block_Adminhtml_Product_Edit_Tab_Attributes_Preview
    extends Mage_Adminhtml_Block_Catalog_Form_Renderer_Fieldset_Element
{

    /**
     * Get Element Html
     *
     * @return string
     */
    public function getElementHtml()
    {
        $elementHtml = parent::getElementHtml();

        $showPreviewAttributeCode = $this->getAttribute()->getAttributeCode();
        $showPreviewAttributeValue = $this->getProduct()->getData($showPreviewAttributeCode);
        
        $previewTypeAttributeCode = 'preview_type';
        $previewTypeAttributeValue = $this->getProduct()->getData($previewTypeAttributeCode);

        $html = '<select name="product[' . $showPreviewAttributeCode . ']" id="' . $showPreviewAttributeCode
        . '" type="select" class="select" onchange="if($(this).value == ' . Peexl_BundleCreatorPlus_Model_Preview_Type::LAYERS . ') $(\'' . $previewTypeAttributeCode . '_wrapper\').hide(); else $(\'' . $previewTypeAttributeCode . '_wrapper\').show();">
            <option ' . ($showPreviewAttributeValue == 0 ? 'selected' : '')
            . ' value="0">' . $this->__('No') . '</option>
            <option ' . ($showPreviewAttributeValue == 1 ? 'selected' : '')
            . ' value="1">' . $this->__('Yes') . '</option>
        </select>';
        
        $html .= '<div style="padding: 5px 0px;" id="' . $previewTypeAttributeCode . '_wrapper">
                    <strong>' . $this->__('Preview Type') . '</strong>
                    <ul>
                        <li><input type="radio" name="product[' . $previewTypeAttributeCode . ']" id="' . $previewTypeAttributeCode . '" value="' . Peexl_BundleCreatorPlus_Model_Preview_Type::LAYERS . '" ' . ($previewTypeAttributeValue == Peexl_BundleCreatorPlus_Model_Preview_Type::LAYERS ? 'checked' : '') . '>
                        ' . $this->__('Layers') . '</li>    
                        <li><input type="radio" name="product[' . $previewTypeAttributeCode . ']" id="' . $previewTypeAttributeCode . '" value="' . Peexl_BundleCreatorPlus_Model_Preview_Type::GRID . '" ' . ($previewTypeAttributeValue == Peexl_BundleCreatorPlus_Model_Preview_Type::GRID ? 'checked' : '') . '>
                        ' . $this->__('Grid') . '</li>
                    </ul>
                </div>';
        
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
