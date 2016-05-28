<?php

/**
 * Created by IntelliJ IDEA.
 * User: vjcspy
 * Date: 10/30/15
 * Time: 4:37 PM
 */
class SM_CustomWidget_Block_Source_Color extends Mage_Adminhtml_Block_System_Config_Form_Field {

    protected function _getElementHtml(Varien_Data_Form_Element_Abstract $element) {
        $color = new Varien_Data_Form_Element_Text();
        $data = array(
            'name' => $element->getName(),
            'html_id' => $element->getId(),
        );
        $color->setData($data);
        $color->setValue($element->getValue(), null);
        $color->setForm($element->getForm());
        $color->addClass('color ' . $element->getClass());

        return $color->getElementHtml();
    }
}
