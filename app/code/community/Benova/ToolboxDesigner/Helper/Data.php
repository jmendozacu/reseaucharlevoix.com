<?php

/**
 * Created by PhpStorm.
 * User: Nguyen
 * Date: 11/22/2015
 * Time: 9:37 PM
 */
class Benova_ToolboxDesigner_Helper_Data extends Mage_Core_Helper_Abstract
{
    public function getAvailableDates(){
        $min = Mage::getModel('toolbox/trajetd')->getCollection()->setOrder('id_date','ASC')->setPageSize(1)->getFirstItem()->getData();
        $max=  Mage::getModel('toolbox/trajetd')->getCollection()->setOrder('id_date','DESC')->setPageSize(1)->getFirstItem()->getData();
        return array(
            'min' => $min,
            'max' => $max,
        );
    }
    public function getTrajetInfo($trajetw, $date){
        $trajet = $this->getTrajet($trajetw['reservation_1']);
        $iD = 'mmo'; 
        $iA = 'bsp';
        if($trajetw->getData('station_depart') == 'mmo'){
            $iD = 'bsp';
            $iA = 'mal';
        }
        $invertTrajets = $this->getTrajetWCollection($iD, $iA)->getData();
        $xtrajet = false;
        if($trajetw['reservation_2'] != 0){
            $xtrajet = $this->getTrajet($trajetw['reservation_2']);
            // $invertxTrajets = $this->getInvertTrajets($xtrajet);
        }

        $availableDates = array();
        $infoCollection = Mage::getModel('toolbox/trajetc')->getCollection()
            ->addFieldToFilter('sku', array('eq' => $trajet->getData('sku')))
            ->addFieldToFilter('date_trajet', array('gteq' => substr(date_create()->format('Y-m-d'),0,10)." 00:00:00"))
            ;
        foreach ($infoCollection  as $info) {
            $availableDates[] = $info->getData('date_trajet');
        }

        $result =  array(
            'availableDates' => $availableDates,
            'booking' => $this->getBookingInfo($trajet->getData('sku'), $date),
            'trips' => $trajet->getData(),
            'invertTrajets' => $invertTrajets,
        );

        if($xtrajet){
            $xavailableDates = array();
            $xinfoCollection = Mage::getModel('toolbox/trajetc')->getCollection()
                ->addFieldToFilter('sku', array('eq' => $xtrajet->getData('sku')));
            foreach ($xinfoCollection  as $info) {
                $availableDates[] = $info->getData('date_trajet');
            }
            $result['xbooking'] = $this->getBookingInfo($xtrajet->getData('sku'), $date);
            $result['xtrips'] = $xtrajet->getData();
            // $result['invertxTrajets'] = $invertxTrajets;
        }
        return $result;
    }

    public function getTrajet($id)
    {
        return $collection = Mage::getModel('toolbox/trajet')->getCollection()
            ->addFieldToFilter('trajet2', array('eq' => $id))
            ->getFirstItem();
    }

    public function getTrajetWCollection($station_depart, $station_arrive, $time){
         $collection = Mage::getModel('toolbox/trajetw')
            ->getCollection()
            ->addFieldToFilter('station_depart',  array('eq' => $station_depart));
        if($station_arrive){
            $collection->addFieldToFilter('station_arrive', array('eq' => $station_arrive));
        }
        if($time){
            $collection->addFieldToFilter('heure_depart', array('eq' => $time));
        }
        return $collection;
    }

    public function getInvertTrajets($trajet)
    {
        $sku = substr($trajet->getSku(), 0, 9);
        if('bookbspmal' == $sku){
            $sku = 'bookmalbsp';
        }else
        if('bookmalbsp' == $sku){
            $sku = 'bookbspmal';
        }else
        if('bookqcbsp' == $sku){
             $sku = 'bookbspqc';
        }else
        if('bookbspqc' == $sku){
             $sku = 'bookqcbsp';
        }
        $dir = $trajet->getDirection();
        return $collection = Mage::getModel('toolbox/trajet')->getCollection()
            ->addFieldToFilter('sku', array('like' => $sku.'%'))
            ->addFieldToFilter('direction', array('neq' => $dir))
            ->getData();
    }

    public function getBookingInfo($sku, $date){
        $time = substr($sku,10);
        $check = intval(substr($sku,9,1));
        $time = explode('h', $time);
        if($check != 0){
            $time[0] = $check.$time[0];
        }
        $time = $time[0].':'.$time[1].':00';
        $booking = Mage::getModel('catalog/product')->loadByAttribute('sku', $sku);
        $options = Mage::getModel('catalog/product_option')->getProductOptionCollection($booking);
        $session = $this->getSessionDate($booking->getId(), date_create($date)->format('Y-m-d'), $time);
        return  array(
            'id' => $booking->getId(),
            'sku' => $booking->getSku(),
            'name' => $booking->getName(),
            'price' => $booking->getPrice(),
            'finalPrice' => $booking->getFinalPrice(),
            'salleId' => $booking->getData('typesalle'),
            'customOptions' =>  $options->getData(),
            'sessionId' =>  $session['session_id']
        );
    }

    public function getSessionDate($id, $date, $time)
    {
        return  Mage::getModel('bookmepro/session')->getCollection()
            ->addFieldToFilter('date_from', array('eq' => $date))
            ->addFieldToFilter('time_from', array('eq' => $time))
            ->addFieldToFilter('product_id', array('eq' => $id))
            ->getFirstItem()->getData();
    }
    public function getSession($id)
    {
        return Mage::getModel('bookmepro/session')->getCollection()
            ->addFieldToFilter('session_id', array('eq' => $id))
            ->setOrder('time_from', 'ASC')->getFirstItem();
    }
	
	public function getSelectedTrajets(){
		return Mage::getSingleton('core/session')->getSelectedTrajets(); 
	}
	
	
	public function getTrajetColSession(){
		return Mage::getSingleton('core/session')->getTrajetCol(); 
	}
	
	public function getPriceForTrajet($product){
		$sku = $product->getSku();
		$return = 0;
		if($trajets = $this->getSelectedTrajets()){
		   
		   foreach($trajets['products'] as $id => $trajet){
			   
			   if($id == $sku){
				   $return = $trajet['price'];
				   
			   }
		   }
	   }
	   //if($trajets['station'] == 2){
		//	$return = $return/2;
		//}
		//var_dump($return);
		return floatval($return);
	}
	public function inTrajet($product){
		$sku = $product->getSku();
		$return = false;
		if($trajets = $this->getSelectedTrajets()){
		   
		   foreach($trajets['products'] as $id => $trajet){
			   
			   if($id == $sku){
				   $return = true;
			   }
		   }
	   }
	   //var_dump($return);
		return $return; 
	}
	public function cache($trajets){
		Mage::getSingleton('core/session')->setSelectedTrajets($trajets); 
	}
	public function clear($trajets){
		Mage::getSingleton('core/session')->setSelectedTrajets(null); 
		Mage::getSingleton('core/session')->setTrajetColSession(null); 
	}
}