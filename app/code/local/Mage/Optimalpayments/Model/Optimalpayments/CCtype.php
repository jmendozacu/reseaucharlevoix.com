<?php
class Mage_Optimalpayments_Model_Optimalpayments_CCtype extends Mage_Payment_Model_Source_Cctype
{
    public function getAllowedTypes()
    {
        return array('VI', 'MC', 'AE', 'DI', 'JCB');
    }
}
