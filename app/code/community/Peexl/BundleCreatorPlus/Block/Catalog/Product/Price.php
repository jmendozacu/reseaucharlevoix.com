<?php

class Peexl_BundleCreatorPlus_Block_Catalog_Product_Price extends Mage_Catalog_Block_Product_Price
{
    /**
     * Check if we have display prices including and excluding tax
     * With corrections for Dynamic prices
     *
     * @return bool
     */
    public function displayBothPrices()
    {
        return $this->helper('tax')->displayBothPrices();
    }
    
}
