<?php
/**
 * AutomaticInvoice Observer Class
 *
 * @category Vianetz
 * @package Vianetz_AutomaticInvoice
 * @author Christoph Massmann <C.Massmann@vianetz.com>
 * @license http://www.vianetz.com/license
 *
 * NOTICE
 * Magento 1.4.x has a bug with the prepareShipment() function in app/code/core/Mage/Sales/Model/Order.php
 * (Please apply the patch mentioned in http://magebase.com/magento-tutorials/shipment-api-in-magento-1-4-1-broken/)s
 */

class Vianetz_AutomaticInvoice_Model_System_Config_Source_Order_Status_Allstatuses extends Mage_Adminhtml_Model_System_Config_Source_Order_Status
{
     protected $_stateStatuses = array(
        Mage_Sales_Model_Order::STATE_NEW,
        Mage_Sales_Model_Order::STATE_PENDING_PAYMENT,
        Mage_Sales_Model_Order::STATE_PROCESSING,
        Mage_Sales_Model_Order::STATE_COMPLETE,
        Mage_Sales_Model_Order::STATE_CLOSED,
        Mage_Sales_Model_Order::STATE_CANCELED,
        Mage_Sales_Model_Order::STATE_HOLDED,
    );
}
