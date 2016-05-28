<?php
class Drieworks_Tickets_Model_Observer{

	/**
	 *
	 * This function is called after order gets saved to database.
	 * Here we transfer our custom fields from quote table to order table i.e sales_order_custom
	 * @param $evt
	 */
	public function saveOrderAfter($observer){

		
		// Moeilijk doen deel 1
		/**
		 * Get the resource model
		 */
		$resource = Mage::getSingleton('core/resource');
	 
		/**
		 * Retrieve the write connection
		 */
		$write = $resource->getConnection('core_write');
		
		
		$event = $observer->getEvent();
		$order = $event->getOrder();

		$RealOrder = $order->getRealOrderId();	
		$tickets = Mage::getSingleton('tickets/session')->getTicket();
		
		Foreach ($tickets as $ticket){
		
			foreach ($order->getAllItems() as $item){
				if ($item->getSku() == $ticket['kaarten']){
					$PriceTax = $item->getPriceInclTax();
					//Mage::Log($item->getPriceInclTax());
				}
			}
			// moeilijk doen
			$ticket_type = $ticket['ticsub'];
			$gender = $ticket['gender'];
			$naam = $ticket['name'];
			$email = $ticket['email'];
			$verjaar = $ticket['date'];
			$adress = $ticket['adress'];
			$zipcode = $ticket['zipcode'];
			$city = $ticket['city'];
			$kaarten = $ticket['kaarten'];
			$themecolor = $ticket['themecolor'];
			$themebg = $ticket['themebg'];
			 
				switch ($ticket_type) {	
						
					case 'Ticket': //ticket
							
							$availableFor = $ticket['type'];
							
							$ticket_type = 'ticket';
							$write->query("INSERT INTO `ticket_orders` (`ticket_id`,`ticket_type`,`gender`, `name`, `email`,`birthday`,`adress`,`zipcode`,`city`,`themecolor`,`themebg`,`product`,`price`,`date`,`order_id`) VALUES (NULL,'" . $ticket_type . "','" . $gender . "','" . $naam . "','" . $email . "','" . $verjaar . "','" . $adress . "','" . $zipcode . "','" . $city . "','" . $themecolor . "','" . $themebg . "','" . $kaarten . "','" . $PriceTax . "',NOW(),'" . $RealOrder . "')");
							$lastinsertId = $write->fetchOne('SELECT last_insert_id()');
							
							if($availableFor == 'Adults' || $availableFor == 'Adult' ) { // for adults
								$write->query("UPDATE `ticket_barcode` SET `order_id` = " . $RealOrder . ",`ticket_id` = " . $lastinsertId . " WHERE `order_id` = ' ' AND `invoice_id` = ' ' AND `available_for` = 'A' LIMIT 1");
							} elseif($availableFor == 'Children' || $availableFor == 'Child' ) { //for children
								$write->query("UPDATE `ticket_barcode` SET `order_id` = " . $RealOrder . ",`ticket_id` = " . $lastinsertId . " WHERE `order_id` = ' ' AND `invoice_id` = ' ' AND `available_for` = 'C' LIMIT 1");
							} else { // For elderly 65+
								$write->query("UPDATE `ticket_barcode` SET `order_id` = " . $RealOrder . ",`ticket_id` = " . $lastinsertId . " WHERE `order_id` = ' ' AND `invoice_id` = ' ' AND `available_for` = 'E' LIMIT 1");
							}
							break;
						
					default: //subscription
							$ticket_type = 'subscription';
							$write->query("INSERT INTO `ticket_orders` (`ticket_id`,`ticket_type`,`gender`, `name`, `email`,`birthday`,`adress`,`zipcode`,`city`,`themecolor`,`themebg`,`product`,`price`,`date`,`order_id`) VALUES (NULL,'" . $ticket_type . "','" . $gender . "','" . $naam . "','" . $email . "','" . $verjaar . "','" . $adress . "','" . $zipcode . "','" . $city . "','" . $themecolor . "','" . $themebg . "','" . $kaarten . "','" . $PriceTax . "',NOW(),'" . $RealOrder . "')");
							break;
							
					}
			
			
		}

		Mage::getSingleton( 'tickets/session' )->setTicket('');
	}

}