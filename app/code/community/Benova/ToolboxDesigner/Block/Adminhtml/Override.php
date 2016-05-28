<?php

/**
 * Created by PhpStorm.
 * User: Nguyen
 * Date: 4/11/2016
 * Time: 11:29 PM
 */
class Benova_ToolboxDesigner_Block_Adminhtml_Override extends Mage_Adminhtml_Block_Catalog_Product_Edit
{
    protected function _prepareLayout()
    {
        parent::_prepareLayout();
        if($this->getProduct()->getTypeId() == 'bundle'){
            if (!$this->getRequest()->getParam('popup')) {
                $this->setChild('back_button',
                    $this->getLayout()->createBlock('adminhtml/widget_button')
                        ->setData(array(
                            'label'     => Mage::helper('catalog')->__('Back'),
                            'onclick'   => 'setLocation(\''
                                . $this->getUrl('*/toolbox/index/').'\')',
                            'class' => 'back'
                        ))
                );
            } else {
                $this->setChild('back_button',
                    $this->getLayout()->createBlock('adminhtml/widget_button')
                        ->setData(array(
                            'label'     => Mage::helper('catalog')->__('Close Window'),
                            'onclick'   => 'window.close()',
                            'class' => 'cancel'
                        ))
                );
            }
        }
    }
}