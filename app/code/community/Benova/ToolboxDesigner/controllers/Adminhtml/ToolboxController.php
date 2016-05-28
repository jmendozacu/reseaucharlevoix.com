<?php

/**
 * Created by PhpStorm.
 * User: Nguyen
 * Date: 4/11/2016
 * Time: 9:24 PM
 */

require_once(BP . DS . 'app' . DS . 'code' . DS . 'core' . DS . 'Mage' . DS . 'Adminhtml' . DS . 'controllers' . DS . 'Catalog' . DS . 'ProductController.php');

class Benova_ToolboxDesigner_Adminhtml_ToolboxController extends Mage_Adminhtml_Controller_Action
{
    public function indexAction(){
        $this->loadLayout();
        $this->getLayout()->getBlock('head')->setTitle($this->__('Toolbox designer'));
        $this->renderLayout();
    }

    public function massAttrSetAction()
    {
        $productIds = (array)$this->getRequest()->getParam('product');
        $setId     = (int)$this->getRequest()->getParam('set_id');
        try {
            foreach ($productIds as $id) {
                $product = Mage::getModel('catalog/product')->load($id);
                $product->setAttributeSetId($setId)->save();
            }

            $this->_getSession()->addSuccess(
                $this->__('Total of %d record(s) have been updated.', count($productIds))
            );
        }
        catch (Mage_Core_Model_Exception $e) {
            $this->_getSession()->addError($e->getMessage());
        } catch (Mage_Core_Exception $e) {
            $this->_getSession()->addError($e->getMessage());
        } catch (Exception $e) {
            $this->_getSession()
                ->addException($e, $this->__('An error occurred while updating the product(s) attributes set.'));
        }

        $this->_redirect('*/*/');
    }
}