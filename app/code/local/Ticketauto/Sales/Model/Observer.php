<?php
/**
 * Event sales_order_invoice_pay
 *
 */
require_once 'Mage/Sales/Model/Observer.php';
class Name_Sales_Model_Observer extends Mage_Sales_Model_Observer {
  
    public function sendInvoiceEmail($observer) {
            $invoice = $observer->getEvent ()->getInvoice ();
  
            switch ($invoice->getState ()) {
                case Mage_Sales_Model_Order_Invoice::STATE_PAID :
  
        try {
			

            // Save first time the invoice to get an id and send the invoice to the customer with the correct id
            $invoice->save ();
            Mage::log ('Email sent to the customer by the Name/sales/observer - Invoice number ' . $invoice->getIncrementId(), Zend_Log::DEBUG );
			
            $invoice->sendEmail ();
            $invoice->setEmailSent ( true );
            // Save a second time to save the EmailSent value
            $invoice->save ();
        } catch ( Exception $e ) {
            $session = Mage::getSingleton ( 'core/session' );
            $exception = new Exception ( Mage::helper('Sales')->__('Error during the email sending.'), 0 );
            $session->addException ( $exception, Mage::helper('Sales')->__('Error to send invoice email. Please, contact the website administrator.') );
        }
            }
        return $this;
    }
}