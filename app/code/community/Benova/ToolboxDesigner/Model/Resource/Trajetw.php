<?php

/**
 * Created by PhpStorm.
 * User: hung
 * Date: 5/2/16
 * Time: 12:55 PM
 */
class Benova_ToolboxDesigner_Model_Resource_Trajetw extends Mage_Core_Model_Resource_Db_Abstract
{

    protected function _construct()
    {
        // TODO: Implement _construct() method.
        $this->_init('toolbox/trajetw', 'trajet_id');
    }
}