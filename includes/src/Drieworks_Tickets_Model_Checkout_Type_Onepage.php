<?php
class Drieworks_Tickets_Model_Checkout_Type_Onepage extends Mage_Checkout_Model_Type_Onepage{

	public function saveTickets($data){
		if (empty($data)) {
			return array('error' => -1, 'message' => $this->_helper->__('Invalid data.'));
		}
		
		Mage::getSingleton( 'tickets/session' )->setTicket($data);
		//$this->getQuote()->setTickets($data);
		//$this->getQuote()->collectTotals();
		//$this->getQuote()->save();
		Mage::Log('Gelukt om te saven: model\checkout\type\onepage');

		$this->getCheckout()
		->setStepData('tickets', 'allow', true)
		->setStepData('tickets', 'complete', true)
		->setStepData('review', 'allow', true);

		return array();
	}
}