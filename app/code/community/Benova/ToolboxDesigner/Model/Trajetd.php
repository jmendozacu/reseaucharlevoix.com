<?php

/**
 * Created by PhpStorm.
 * User: hung
 * Date: 5/2/16
 * Time: 1:04 PM
 */
class Benova_ToolboxDesigner_Model_Trajetd extends Mage_Core_Model_Abstract
{
    protected function _construct()
    {
        parent::_construct();
        $this->_init('toolbox/trajetd');
    }
}