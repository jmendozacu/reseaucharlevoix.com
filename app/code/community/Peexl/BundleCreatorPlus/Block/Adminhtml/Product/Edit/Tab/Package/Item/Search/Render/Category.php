<?php

class Peexl_BundleCreatorPlus_Block_Adminhtml_Product_Edit_Tab_Package_Item_Search_Render_Category extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract {

    public function render(Varien_Object $row) {
        $product = Mage::getModel('catalog/product')->load($row->getEntityId());
        $cats = $product->getCategoryIds();
        $allCats = '';
        foreach ($cats as $key => $cat) {
            $_category = Mage::getModel('catalog/category')->load($cat);
            $allCats.= $_category->getName();
            if ($key < count($cats) - 1)
                $allCats.= ',<br />';
        }
        return $allCats;
    }

}