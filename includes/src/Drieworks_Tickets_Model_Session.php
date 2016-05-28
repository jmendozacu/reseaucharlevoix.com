<?php
class DRIEWORKS_Tickets_Model_Session extends Mage_Core_Model_Session_Abstract {

    public function __construct()
    {
        $namespace = 'tickets';
        $namespace .= '_' . (Mage::app()->getStore()->getWebsite()->getCode());
        

        $this->init($namespace);
        Mage::dispatchEvent('tickets_session_init', array('tickets_session'=>$this));
    }
}