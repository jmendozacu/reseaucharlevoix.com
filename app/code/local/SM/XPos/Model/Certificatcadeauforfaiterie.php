<?php
/**
 * Created by PhpStorm.
 * User: vjcpsy
 * Date: 6/25/2015
 * Time: 11:31
 */
class SM_XPos_Model_Certificatcadeauforfaiterie extends Mage_Payment_Model_Method_Abstract
{
    protected $_code = 'xpos_certificatcadeauforfaiterie';
    protected $_canUseInternal = true;
    protected $_canUseCheckout = false;
    protected $_canUseForMultishipping = false;
}
