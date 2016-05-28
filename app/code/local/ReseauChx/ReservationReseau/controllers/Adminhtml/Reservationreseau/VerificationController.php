<?php

class ReseauChx_ReservationReseau_Adminhtml_Reservationreseau_VerificationController extends ReseauChx_ReservationReseau_Controller_Adminhtml_ReservationReseau
{
	public function indexAction(){
		// Let's call our initAction method which will set some basic params for each action
		$this->_initAction()
		->renderLayout();
	}
	
	
	/**
	* Initialize action
	*
	* Here, we set the breadcrumbs and the active menu
	*
	* @return Mage_Adminhtml_Controller_Action
	*/
	protected function _initAction()
	{
		$this->loadLayout()
		// Make the active menu match the menu config nodes (without 'children' inbetween)
		->_setActiveMenu('bookmepro/reseauchx_reservationreseau/verification')
		->_title($this->__('Reservation siege'))->_title($this->__('Verification'));
		 
		return $this;
	}
}