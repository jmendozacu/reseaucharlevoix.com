<?php

class Peexl_BundleCreatorPlus_Block_Package_Layer_View extends Mage_Catalog_Block_Layer_View
{

    /**
     * Get layer object
     *
     * @return Mage_Catalog_Model_Layer
     */
    public function getLayer()
    {
        return Mage::getSingleton('bundlecreatorplus/layer');
    }

}
