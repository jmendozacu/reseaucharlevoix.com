<?php

/**
 * Created by IntelliJ IDEA.
 * User: vjcspy
 * Date: 10/30/15
 * Time: 4:29 PM
 */
class SM_CustomWidget_Model_SelectTypeLoading {
    public function toOptionArray() {
        return array(
            array('value' => 1, 'label' => Mage::helper('adminhtml')->__('Type 1')),
            array('value' => 2, 'label' => Mage::helper('adminhtml')->__('Type 2')),
            array('value' => 3, 'label' => Mage::helper('adminhtml')->__('Type 3')),
            array('value' => 4, 'label' => Mage::helper('adminhtml')->__('Type 4')),
            array('value' => 5, 'label' => Mage::helper('adminhtml')->__('Type 5')),
            array('value' => 6, 'label' => Mage::helper('adminhtml')->__('Type 6')),
            array('value' => 7, 'label' => Mage::helper('adminhtml')->__('Type 7')),
            array('value' => 8, 'label' => Mage::helper('adminhtml')->__('Type 8')),
            array('value' => 9, 'label' => Mage::helper('adminhtml')->__('Type 9')),
            array('value' => 10, 'label' => Mage::helper('adminhtml')->__('Type 10')),
        );
    }
}
