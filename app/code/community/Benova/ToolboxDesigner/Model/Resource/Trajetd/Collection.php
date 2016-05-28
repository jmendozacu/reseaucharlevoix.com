<?php

/**
 * Created by PhpStorm.
 * User: hung
 * Date: 5/2/16
 * Time: 1:01 PM
 */
class Benova_ToolboxDesigner_Model_Resource_Trajetd_Collection extends Mage_Core_Model_Resource_Db_Collection_Abstract
{
    public function _construct()
    {
        parent::_construct();
        $this->_init('toolbox/trajetd');
    }
}