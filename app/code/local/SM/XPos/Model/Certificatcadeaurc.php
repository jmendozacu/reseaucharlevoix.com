<?php
/**
 * Created by PhpStorm.
 * User: tungomi
 * Date: 6/25/2015
 * Time: 11:32
 */
class SM_XPos_Model_Certificatcadeaurc extends Mage_Payment_Model_Method_Abstract
{
    protected $_code = 'xpos_certificatcadeaurc';
    protected $_canUseInternal = true;
    protected $_canUseCheckout = false;
    protected $_canUseForMultishipping = false;
}
