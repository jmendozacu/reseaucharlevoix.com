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
class Vianetz_AutomaticInvoice_Model_Order_Observer
{
	/**
     * XML Admin Path For Generate Invoice
     * @var string
     */
	const XML_PATH_SALES_AUTOMATICINVOICE_GENERATE_INVOICE = 'automaticinvoice/invoice/generate';
	
	/**
     * XML Admin Path For Generate Shipment
     * @var string
     */
	const XML_PATH_SALES_AUTOMATICINVOICE_GENERATE_SHIPMENT = 'automaticinvoice/shipment/generate';
	
	/**
     * XML Admin Path For New Order Status
     * @var string
     */
	const XML_PATH_SALES_AUTOMATICINVOICE_ORDER_STATUS = 'automaticinvoice/general/order_status';
	
	/**
     * XML Admin Path For Payment Methods
     * @var string
     */
	const XML_PATH_SALES_AUTOMATICINVOICE_PAYMENT_METHODS = 'automaticinvoice/general/payment_methods';
	
	/**
     * XML Admin Path For Product Types
     * @var string
     */
	const XML_PATH_SALES_AUTOMATICINVOICE_PRODUCT_TYPES = 'automaticinvoice/general/product_types';
	
	/**
     * XML Admin Path For Invoice Comment
     * @var string
     */
	const XML_PATH_SALES_AUTOMATICINVOICE_INVOICE_COMMENT = 'automaticinvoice/invoice/comment';
	
	/**
     * XML Admin Path For Saving PDF document
     * @var string
     */
	const XML_PATH_SALES_AUTOMATICINVOICE_INVOICE_SAVEPDF = 'automaticinvoice/invoice/savepdf';

    /**
     * Generate invoice automatically based on several config values.
     * 
     * @param Varien_Event_Observer $observer
     * @return Vianetz_AutomaticInvoice_Model_Order_Observer
     */
    public function generateInvoice(Varien_Event_Observer $observer)
    {
        // Check if enabled.
    	if (Mage::getStoreConfigFlag(self::XML_PATH_SALES_AUTOMATICINVOICE_GENERATE_INVOICE) === false) {
            return $this;
        }

        $event = $observer->getEvent();
        /** @var Mage_Sales_Model_Order $order */
        $order = $event->getOrder();

        if ($this->_hasConfiguredInvoiceOrderStatus($order) === false) {
            return $this;
        }

        try {
            $isSendEmail = Mage::getStoreConfigFlag('automaticinvoice/invoice/notify_customer');
            $allowedPaymentMethods = explode(',', Mage::getStoreConfig(self::XML_PATH_SALES_AUTOMATICINVOICE_PAYMENT_METHODS));
            $paymentMethod = $order->getPayment()->getMethodInstance()->getCode();

            $productTypes = explode(',', Mage::getStoreConfig(self::XML_PATH_SALES_AUTOMATICINVOICE_PRODUCT_TYPES));
            $isInAllowedTypes = false;
            foreach ( $order->getAllItems() as $item ) {
                if ( in_array($item->getProductType(), $productTypes) ) {
                    $isInAllowedTypes = true;
                    break;
                }
            }

            if (in_array($paymentMethod, $allowedPaymentMethods) && $order->canInvoice()) {
                $invoice = $order->prepareInvoice();

                $invoice->setIsPaid(false);

                $isCaptureOnline = Mage::getStoreConfigFlag('automaticinvoice/invoice/capture_online');
                if ($isCaptureOnline === true) {
                    $invoice->setRequestedCaptureCase(Mage_Sales_Model_Order_Invoice::CAPTURE_ONLINE);
                } else {
                    $invoice->setRequestedCaptureCase(Mage_Sales_Model_Order_Invoice::CAPTURE_OFFLINE);
                }

                $invoice->register();
                if ( Mage::getStoreConfig(self::XML_PATH_SALES_AUTOMATICINVOICE_INVOICE_COMMENT) ) {
                    foreach ($order->getStatusHistoryCollection() as $status) {
                        if (!$status->isDeleted() && $status->getComment()) {
                            $invoice->addComment($status->getComment());
                        }
                    }
                }
                Mage::getModel('core/resource_transaction')
                    ->addObject($invoice)
                    ->addObject($invoice->getOrder())
                    ->save();

                $order->addRelatedObject($invoice);

                // Set order status.
                $comment = $this->_getHelper()->__("Invoice automatically generated.");
                $order->setState(Mage_Sales_Model_Order::STATE_PROCESSING, Mage::getStoreConfig(self::XML_PATH_SALES_AUTOMATICINVOICE_ORDER_STATUS), $comment, $isSendEmail);
                $order->save();

                try {
                    if (Mage::getStoreConfig(self::XML_PATH_SALES_AUTOMATICINVOICE_INVOICE_SAVEPDF) &&
                        is_dir(Mage::getBaseDir('media') . DS . 'invoices')) {
                        // Save PDF document
                        $filename = $this->_getHelper()->stringToFilename('INV-' . $invoice->getIncrementId() . '.pdf');
                        Mage::getModel('sales/order_pdf_invoice')->getPdf(array($invoice))->save(Mage::getBaseDir('media') . DS . 'invoices' . DS . $filename);
                    }
                }
                catch ( Exception $ex ) {
                    Mage::logException($ex);
                }
            }
        }
        catch ( Exception $ex) {
            Mage::logException($ex);
        }

        return $this;
    }

    /**
     * Generate shipment automatically based on several config values.
     *
     * @param $observer
     *
     * @return Vianetz_AutomaticInvoice_Model_Order_Observer
     */
    public function generateShipment(Varien_Event_Observer $observer)
    {
    	if (Mage::getStoreConfigFlag(self::XML_PATH_SALES_AUTOMATICINVOICE_GENERATE_SHIPMENT) === false) {
            return $this;
        }

        $event = $observer->getEvent();
        $order = $event->getOrder();

        if ($this->_hasConfiguredShipmentOrderStatus($order) === false) {
            return $this;
        }

        try {
            $isSendEmail = Mage::getStoreConfigFlag('automaticinvoice/shipment/notify_customer');
            $allowedPaymentMethods = explode(',', Mage::getStoreConfig(self::XML_PATH_SALES_AUTOMATICINVOICE_PAYMENT_METHODS));
            $paymentMethod = $order->getPayment()->getMethodInstance()->getCode();

            $productTypes = explode(',', Mage::getStoreConfig(self::XML_PATH_SALES_AUTOMATICINVOICE_PRODUCT_TYPES));
            $isInAllowedTypes = false;
            foreach ($order->getAllItems() as $item) {
                if ( in_array($item->getProductType(), $productTypes) ) {
                    $isInAllowedTypes = true;
                    break;
                }
            }

            if ($isInAllowedTypes && in_array($paymentMethod, $allowedPaymentMethods) && $order->canShip()) {
                $shipment = $order->prepareShipment();

                $shipment->register();
                $order->setIsInProcess(true);
                $shipment->setEmailSent($isSendEmail);

                Mage::getModel('core/resource_transaction')
                    ->addObject($shipment)
                    ->addObject($shipment->getOrder())
                    ->save();

                $order->addRelatedObject($shipment);
                $shipment->sendEmail($isSendEmail, '');

                // Set status
                $comment = $this->_getHelper()->__("Shipment automatically generated.");
                $order->setState(Mage_Sales_Model_Order::STATE_PROCESSING, Mage::getStoreConfig(self::XML_PATH_SALES_AUTOMATICINVOICE_ORDER_STATUS), $comment, $isSendEmail);
                $order->save();
            }
        }
        catch ( Exception $ex) {
            Mage::logException($ex);
        }
    }

    /**
     * @return Vianetz_AutomaticInvoice_Helper_Data
     */
    protected function _getHelper()
    {
        return Mage::helper('automaticinvoice');
    }

    /**
     * Check if order is in the configured order status for invoice generation.
     *
     * @param $order
     *
     * @return bool
     */
    protected function _hasConfiguredInvoiceOrderStatus($order)
    {
        $orderStatusToGenerate = Mage::getStoreConfig('automaticinvoice/invoice/generate_on_order_status', $order->getStoreId());

        return ($order->getStatus() == $orderStatusToGenerate);
    }

    /**
     * Check if order is in the configured order status for invoice generation.
     *
     * @param $order
     *
     * @return bool
     */
    protected function _hasConfiguredShipmentOrderStatus($order)
    {
        $orderStatusToGenerate = Mage::getStoreConfig('automaticinvoice/shipment/generate_on_order_status', $order->getStoreId());

        return ($order->getStatus() == $orderStatusToGenerate);
    }

    /**
     * Send invoice email to customer if it has not been sent yet.
     * This method is used in case that the payment module generates the invoice but does not send the email.
     *
     * Event: sales_order_invoice_pay
     * @param Varien_Event_Observer $observer
     *
     * @return $this
     */
    public function sendInvoiceEmail($observer)
    {
        /** @var Mage_Sales_Model_Order_Invoice $invoice */
        $invoice = $observer->getEvent()->getInvoice();

        $isNotifyCustomer = Mage::getStoreConfigFlag('automaticinvoice/invoice/notify_customer', $invoice->getStoreId());

        try {
            // First we have to save the invoice to get an id.
            $invoice->save();

            // If email has not been sent, then send it.
            if ($invoice->getState() == Mage_Sales_Model_Order_Invoice::STATE_PAID
             && $isNotifyCustomer === true && $invoice->getEmailSent() == false) {
	
                $invoice->sendEmail(true);
                $invoice->save();
            }
        }
        catch (Exception $ex) {
            Mage::logException($ex);
        }

        return $this;
    }
}