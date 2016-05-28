<?php

class Vianetz_AutomaticInvoice_Model_Source_Producttypes extends Mage_Catalog_Model_Product_Type
{
	static public function toOptionArray()
	{
		$options = array();
		$i = 0;
        foreach(self::getTypes() as $typeId=>$type) {
            $options[$i]['label'] = Mage::helper('catalog')->__($type['label']);
            $options[$i]['value'] = $typeId;
            $i++;
        }
Mage::log($options);
Mage::log(Mage::getSingleton('adminhtml/system_config_source_yesno')->toOptionArray());
        return $options;
	}
}