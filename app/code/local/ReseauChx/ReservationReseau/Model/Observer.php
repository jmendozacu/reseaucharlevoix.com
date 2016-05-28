<?php
//Eric: Ajout des observer

class ReseauChx_ReservationReseau_Model_Observer extends Mage_Core_Model_Observer{
	
	public function checkCartAdd($observer) {
		if($observer->getProduct()->getTypeId() == 'booking')
		{
			Mage::log('Booking product added to cart.');
			$quoteItem = $observer->getQuoteItem();
			$quoteItem->save();
			$quote_item_id = $quoteItem->getItemId();
			
			$quote = $quoteItem->getQuote();
			$quote->save();
			$quote_id= $quote->getId();
			
			Mage::log('quote_item_id:' . $quote_item_id . ' quote_id:' . $quote_id );
			
			if(!isset($quote_id)){
				Mage::throwException("Erreur, no id!");
			}
			
			$options = $quoteItem->getProduct()->getOptions();
			$multidate_type_id = 0;
			$selectionsiege_type_id = 0;
				
			foreach ($options as $option ) {
				Mage::log('Option: '. $option->getType());
				if($option->getType() =='multidate_type'){
					Mage::log('Found Multidate Type Id: ' .  $option->getId());
					$multidate_type_id	= $option->getId();
				}
				if($option->getType() =='selectionsiege_type'){
					Mage::log('Found Selection Siege Type Id: ' .  $option->getId());
					$selectionsiege_type_id	= $option->getId();
				}
			}
			if($selectionsiege_type_id != 0)
			{
				$values = unserialize($quoteItem->getOptionByCode('info_buyRequest')->getValue());
				$siegesStr =$values['options'][$selectionsiege_type_id]['val'];
				$siegesStr = rtrim($siegesStr, ',');
				Mage::log('Siege: ' . $siegesStr);
				
				date_default_timezone_set("America/New_York");
				
				$selectedSessionStr = $quoteItem->getOptionByCode('option_'.$multidate_type_id)->getValue();
				$data = unserialize($selectedSessionStr);
				$selectedSession = explode("#", $data['values']);
				Mage::log('$session: ' . $data['values']);
				$session = Mage::getModel('bookmepro/session')->load($selectedSession[0]);
				
				$selectedDateStr = Mage::helper('core')->formatDate($session['date_from']." ".date('H:i:s', strtotime($session['time_from'])), 'medium', true);
				
				
				
				$selectedDate = new DateTime($selectedDateStr);

				Mage::log('Date: ' . $selectedDate->format('Y-m-d H:i:s') );
				
				$sieges = explode(",", $siegesStr);
				
				foreach ($sieges as $siege){
					$siegeId =  ltrim($siege, "s");
					$modelSalle = Mage::getModel('reseauchx_reservationreseau/siege')->load($siegeId);
					$salle = $modelSalle->getData('salle_id');
					Mage::log('Salle: ' . $salle);
					$data = array(
							'salle_id'=>$salle,
							'siege_id'=>$siegeId,
							'dateheuredebut'=>$selectedDate->format('Y-m-d H:i:s'),
							'dateheurefin'=>$selectedDate->format('Y-m-d H:i:s'),
							'confirme'=>0,
							'idquote'=>$quote_id,
							'book_id'=>$quote_item_id,
							'status'=>1,
							'updated_at'=>time(),
							'created_at'=>time(),
					);

					$model = Mage::getModel('reseauchx_reservationreseau/reservationsiege')->setData($data);
					try {
						$insertId = $model->save()->getId();
						Mage::log("Data successfully inserted. Insert ID: ".$insertId);
					} catch (Exception $e){
						Mage::log($e->getMessage());
					}
				}
			}
		}
		return $this;
	}
	
	public function validateReservationOptions($observer){
		Mage::log('Observer sales_order_place_before started.');
		$order = $observer->getEvent()->getOrder();
		$orderId  = $order->getId();
		$quoteId = $order->getQuoteId();
		$quote = $order->getQuote();
		$hasDoubleBook = false;
		foreach ($quote->getAllVisibleItems() as $item){
			$reservations = Mage::getModel('reseauchx_reservationreseau/reservationsiege')->getCollection()->addFieldToFilter('book_id',$item->getItemId());
			foreach ($reservations as $reservation){
				$doubleBookCollection =  Mage::getModel('reseauchx_reservationreseau/reservationsiege')
				->getCollection()
				->addFieldToFilter('siege_id', array('eq' => $reservation->siege_id))
				->addFieldToFilter('dateheuredebut', array('eq' => $reservation->dateheuredebut))
				->addFieldToFilter('idorder', array('notnull' => true));
				if($doubleBookCollection->count() > 0){
					$hasDoubleBook = true;
				}
			}
		}
		if($hasDoubleBook){
			$cart = Mage::getSingleton('checkout/cart');
			foreach ($quote->getAllVisibleItems() as $item){
				$cart->removeItem($item->getId());
			}
			Mage::log('DoubleBook: Quote exeption, cart his now empty. Quote:' . $quoteId . ' Order:' . $orderId);
			//return array('error'=>-1,'message'=> 'Erreur de réservation de siège. Veuillez recommencer.');
			//throw new Mage_Payment_Model_Info_Exception(Mage::helper('checkout')->__('Erreur de réservation de siège. Veuillez recommencer.'));
			Mage::getSingleton('checkout/session')->addError('Erreur de r&eacute;servation de si&egrave;ge. Veuillez recommencer.');
			//Mage::app()->getResponse()->setRedirct(Mage::getUrl('checkout/cart'));
			throw new Mage_Payment_Model_Info_Exception(Mage::helper('checkout')->__('Erreur de réservation de siège. Veuillez recommencer.'));
		}
	}
	

	
	public function confirmeReservationOptions($observer){
		Mage::log('Observer sales_order_place_after started.');
		$order = $observer->getEvent()->getOrder();
		$orderId  = $order->getId();
		$quoteId = $order->getQuoteId();
		//Eric:  Remplacer par un numéros da vrais quote du ordre.
		//$quote = Mage::getSingleton('checkout/session')->getQuote();
		$quote = $order->getQuote();
		foreach ($quote->getAllVisibleItems() as $item){
			Mage::log('Confirme selection siege: orderId: ' . $orderId . ' quoteId: ' . $quoteId. ' book_id: ' . $item->getItemId());
			$reservations = Mage::getModel('reseauchx_reservationreseau/reservationsiege')->getCollection()->addFieldToFilter('book_id',$item->getItemId());
			foreach ($reservations as $reservation){
				$data = array('confirme'=>1,'idorder'=>$orderId);
				$reservation->addData($data);
				$reservation->save();
			}
		}
	}
	
	/*public function cancelQuoteSiege(Varien_Event_Observer $observer){
	
		$item = $observer->getData('quote_item');
    	$itemId = $item->getItemId();
    	Mage::log('Delete siege: $itemId: ' . $itemId);
		if($item->getProduct()->getTypeId() == 'booking')
		{
			$reservations = Mage::getModel('reseauchx_reservationreseau/reservationsiege')->getCollection()->addFieldToFilter('book_id',$itemId);
			foreach ($reservations as $reservation){
				$reservation->delete();
			}
		}
		return true;
	}*/
	
	public function cancelOrderSiege(Varien_Event_Observer $observer)
	{
		$creditmemo = $observer->getEvent()->getCreditmemo();
		$order = $creditmemo->getOrder();
		Mage::log('CancelOrderSiege Called for Order: ' . $order->getId());
		$reservations = Mage::getModel('reseauchx_reservationreseau/reservationsiege')->getCollection()->addFieldToFilter('idorder',$order->getId());
		foreach ($reservations as $reservation){
			$reservation->delete();
		}
		return true;
	}
}