<?php

class Peexl_BundleCreatorPlus_Block_Product_List extends Mage_Catalog_Block_Product_List
{
    public function getAddToCartUrl($product, $additional = array())
    {
        if ($product->getTypeId() == 'extendedbundle') {
            return $this->getProductUrl($product, $additional);
        }
        return parent::getAddToCartUrl($product, $additional);
    }
    
    public function getSubmitUrl($product, $additional = array())
    {
        if($product->getTypeId() == 'extendedbundle') {
            return $this->getAddToCartUrl($product, $additional);
        }
        return parent::getSubmitUrl($product, $additional);
    }
}
