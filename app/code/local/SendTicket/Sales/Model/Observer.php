<?php
/**
 * Event sales_order_invoice_pay
 *
*/
require_once 'Mage/Sales/Model/Observer.php';
class SendTicket_Sales_Model_Observer extends Mage_Sales_Model_Observer {
	
	public function sendInvoiceEmail($observer) {
		Mage::log('on entre dans la fonction');
		
		$event = $observer->getEvent();
		$orderids = $event->getOrderIds();
		foreach ($orderids as $_orderid) {
			Mage::log('on recupere le orderid: '. $_orderid);
			Mage::log('On load le Model Order.');
			$order = new Mage_Sales_Model_Order();
			Mage::log('On load le order dans le model.');
	        $order->load($_orderid);
			Mage::log('Si il y a des Invoices?');
			if ($order->hasInvoices()) {
				Mage::log('Pour chaqye Invoice.');
				Mage::log('Store Avant:' . Mage::app()->getStore()->getId());
				Mage::log('Store Order Avant:' . $order->getStoreId());
				Mage::log('Skin Avant:' . Mage::getDesign()->getSkinBaseDir());
				foreach ($order->getInvoiceCollection() as $_eachInvoice) {
					Mage::log('On ship par courriel le billiet');
					$_eachInvoice->sendEmail ();
				}
				Mage::log('Store Apres:' . Mage::app()->getStore()->getId());
				Mage::log('Store Order Apres:' . $order->getStoreId());
			    Mage::log('Skin Apres:' . Mage::getDesign()->getSkinBaseDir());
				Mage::log('setTheme...');
				Mage::getDesign()->setTheme('theme420');
				Mage::log('Skin Apres setTheme:' . Mage::getDesign()->getSkinBaseDir());
			}
		}
	}
}
