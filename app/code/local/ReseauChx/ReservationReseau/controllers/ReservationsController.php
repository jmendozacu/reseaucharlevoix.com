<?php
class ReseauChx_ReservationReseau_ReservationsController extends Mage_Core_Controller_Front_Action
{
	public function getreservationAction(){
		Mage::log('frontend ajax getreservation');
		date_default_timezone_set("America/New_York");
		$salleId = $this->getRequest()->getParam('salleId');
		$confirme = $this->getRequest()->getParam('confirme');
		
		$sessionId = urldecode($this->getRequest()->getParam('heureDateDebut'));
		$session = Mage::getModel('bookmepro/session')->load($sessionId);
		$selectedDateStr = Mage::helper('core')->formatDate($session['date_from']." ".date('H:i:s', strtotime($session['time_from'])), 'medium', true);
		$heureDateDebut = new DateTime($selectedDateStr);
		
		//$heureDateDebut = new DateTime());
		//$heureDateFin =  new DateTime(urldecode($this->getRequest()->getParam('heureDateDebut')));
		//$heureDateFin->add(new DateInterval('PT01H00S'));
		
		Mage::log('salleId: ' . $salleId);
		Mage::log('sessionId: ' . $sessionId);
		
		Mage::log('heureDateDebut: ' . date_format($heureDateDebut,"Y/m/d H:i:s"));
		//Mage::log('heureDateFin: ' . date_format($heureDateFin,"Y/m/d H:i:s"));
		
		$_salle = Mage::getModel('reseauchx_reservationreseau/salle')->load($salleId);
		$_reservations = null;
		if($confirme == 0){
			$_reservations = $_salle->getSelectedReservationssiegesCollectionByDateDebutNonConfirme($heureDateDebut);
		}
		else{
			$_reservations = $_salle->getSelectedReservationssiegesCollectionByDateDebutConfirme($heureDateDebut);
		}
		
		
		
		$jsonData = Mage::helper('core')->jsonEncode($_reservations);
		$this->getResponse()->setHeader('Content-type', 'application/json');
		$this->getResponse()->setBody($jsonData);
		
		
	}
	
	
}