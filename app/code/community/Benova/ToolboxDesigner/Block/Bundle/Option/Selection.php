<?php

/**
 * Created by PhpStorm.
 * User: Nguyen
 * Date: 4/2/2016
 * Time: 1:22 AM
 */
class Benova_ToolboxDesigner_Block_Bundle_Option_Selection extends  Mage_Bundle_Block_Adminhtml_Catalog_Product_Edit_Tab_Bundle_Option_Selection
{
    public function __construct()
    {
        parent::__construct();
        $this->setTemplate('benova/toolbox-designer/selection.phtml');

    }
}