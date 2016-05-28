<?php

/**
 * @category   Peexl
 * @package    Peexl_BundleCreatorPlus
 * @copyright  Copyright (c) 2016 Peexl Web Development (http://www.peexl.com)
 * @license    http://framework.zend.com/license/new-bsd    New BSD License
 * @version    1.0
 */
class Peexl_BundleCreatorPlus_Model_Resource_Item_Attribute_Value extends Mage_Core_Model_Mysql4_Abstract {

    protected function _construct() {
        $this->_init('bundlecreatorplus/item_attribute_value', 'value_id');
    }

}