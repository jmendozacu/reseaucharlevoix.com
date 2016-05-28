<?php

/**
 * Created by PhpStorm.
 * User: Nguyen
 * Date: 11/22/2015
 * Time: 8:14 PM
 */
class Benova_ToolboxDesigner_Model_Observer
{
    /*
     * after submit save #1
     * */
    public function catalogProductPrepareSave(Varien_Event_Observer $observer)
    {
        $product = $observer->getEvent()->getProduct();
        $request = $observer->getEvent()->getRequest();
        if(!$product || !$request) return $observer;

        $data = $request->getParam('smartproduct');
        if(!$data){
            return $observer;
        }
        $product->setData('drawer_info',json_encode($data));
        $product->save();
        return $observer;
    }

    public function addToTopmenu(Varien_Event_Observer $observer)
    {
        $menu = $observer->getMenu();
        $tree = $menu->getTree();

        $node = new Varien_Data_Tree_Node(array(
            'name'   => 'Toolbox Designer',
            'id'     => 'toolbox-designer',
            'url'    => Mage::getUrl('toolbox-designer'), // point somewhere
        ), 'id', $tree, $menu);

        $menu->addChild($node);
    }
	 public function processFrontFinalPrice($observer)
    {
        
       $toolbox = Mage::helper('toolbox');
	  
		 /* @var $item Mage_Sales_Model_Quote_Item */
        $item = $observer->getQuoteItem();
        if ($item->getParentItem()) {
            $item = $item->getParentItem();
        }

       
		if($toolbox->inTrajet($item->getProduct())){
			$finalPrice = $toolbox->getPriceForTrajet($item->getProduct());
			$item->setCustomPrice($finalPrice);
            $item->setOriginalCustomPrice($finalPrice);
            $item->getProduct()->setIsSuperMode(true);
		}
		//var_dump($toolbox->getSelectedTrajets());
        
    }

}