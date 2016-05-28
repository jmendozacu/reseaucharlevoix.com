<?php
require_once(Mage::getModuleDir('controllers', 'Mage_Adminhtml') . DS . 'Catalog' . DS . 'ProductController.php');
//require_once(BP . DS . 'app' . DS . 'code' . DS . 'core' . DS . 'Mage' . DS . 'Adminhtml' . DS . 'controllers' . DS . 'Sales' . DS . 'Order' . DS . 'CreateController.php');
/**
 * Created by PhpStorm.
 * User: vjcspy
 * Date: 7/17/15
 * Time: 11:00 AM
 */
class SM_XPos_Adminhtml_Catalog_ProductController extends Mage_Adminhtml_Catalog_ProductController
{
    public function saveAction()
    {
        parent::saveAction();
        $productId = $this->getRequest()->getParam('id');
        $storeId = $this->getRequest()->getParam('store');
        Mage::dispatchEvent('update_real_time_after_save', array('productId' => $productId, 'storeId' => $storeId));
    }
}
