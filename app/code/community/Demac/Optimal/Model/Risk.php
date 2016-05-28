<?php

class Demac_Optimal_Model_Risk extends Mage_Core_Model_Abstract
{
    public function __construct()
    {
        parent::_construct();
        $this->_init('optimal/risk');
    }

    /**
     * @param $manifestId
     * @return Demac_CanadaPost_Model_Artifact
     */
    public function loadByCode($errorCode)
    {
        $this->_getResource()->loadByCode($this, $errorCode);
        return $this;
    }
}