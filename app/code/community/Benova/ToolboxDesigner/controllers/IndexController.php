    <?php

/**
 * Created by PhpStorm.
 * User: hungnt
 * Date: 4/7/16
 * Time: 2:44 PM
 */
class Benova_ToolboxDesigner_IndexController extends Mage_Core_Controller_Front_Action
{
    protected $_salle;
    protected $_storeId;
    protected $fromReservationArray = array();
    protected $returnReservationArray = array();
    public function indexAction()
    {
        $this->loadLayout();
        $this->getLayout()->getBlock('head')->setTitle($this->__('Toolbox designer'));
        $this->renderLayout();
    }

    public function productsAction()
    {

        $data = $this->getRequest()->getParams();
        $origin = $data['origin'];
        $des = $data['destination'];
        $from = $data['from'];
        $return = $data['return'];
        $rOa = $data['ra'];
        $this->storeId = $data['storeId'];

        $trajets = array();
        $toolbox = Mage::helper('toolbox');
        $departTrajetsCollection = $toolbox->getTrajetWCollection($origin, $des);
        $arriveTrajetsCollection = array();
        if($rOa == 'ret'){
            $arriveTrajetsCollection = $toolbox->getTrajetWCollection($des, $origin);
        }

        foreach ($departTrajetsCollection as $departTrajet) {
            $departTrajetTime = date_create($departTrajet->getData('heure_depart'));
            if($rOa == 'ret'){
                foreach ($arriveTrajetsCollection as $arriveTrajet) {
                    $arriveTrajetTime = date_create($arriveTrajet->getData('heure_depart'));
                    $diffDate = date_diff($arriveTrajetTime, $departTrajetTime);
                    $data = array(
                        'depart' => $departTrajet->getData(),
                        'arrive' => $arriveTrajet->getData(),
                    );
                    $departTrajetInfo = $toolbox->getTrajetInfo($departTrajet, $from);
                    $arriveTrajetInfo = $toolbox->getTrajetInfo($arriveTrajet, $return);

                    $data['depart']['info'] = $departTrajetInfo;
                    $data['arrive']['info'] = $arriveTrajetInfo;

                    if($from == $return){
                        if($diffDate->invert == 1){
                            // var_dump($arriveTrajet->getData('heure_depart'),$departTrajet->getData('heure_depart'));
                            // echo "depart a: {$departTrajet->getData('heure_depart')} <br/>";
                            // echo "depart a-b: {$departTrajet->getData('heure_depart2')} <br/>";
                            // echo "depart c: {$departTrajetInfo['xtrips']['h_mal']} <br/>";

                            // echo "arrive c: {$arriveTrajet->getData('heure_depart')} <br/>";
                            // echo "depart c-b: {$arriveTrajet->getData('heure_depart2')} <br/>";
                            // echo "depart a: {$arriveTrajetInfo['xtrips']['h_mmo']} <br/>";
                            if($departTrajet->getData('reservation_2') != 0){

                                $endOfArrive = $arriveTrajetInfo['trips']['h_bsp'];
                                $startOfArrive = $arriveTrajetInfo['trips']['h_mal'];
                                $endOfDepart = $departTrajetInfo['xtrips']['h_mal'];
                                if($departTrajet->getData('station_depart') == 'mal'){
                                    $startOfArrive = $arriveTrajetInfo['trips']['h_mmo'];
                                    $endOfDepart = $departTrajetInfo['xtrips']['h_mmo'];
                                }
                                $endOfArriveTime = date_create($endOfArrive);
                                $startOfArriveTime = date_create($startOfArrive);
                                $endOfDepartTime = date_create($endOfDepart);

                                $diffDateEndOfArriveVRstartDepart = date_diff($endOfArriveTime, $departTrajetTime);
                                $diffDateEndOfArriveVRendDepart = date_diff($endOfArriveTime, $endOfDepartTime);
                                $canArrive = 0;
                                 
                                 foreach ($departTrajetInfo['invertTrajets'] as $key => $invertTrajet) {
                                    $start = date_create($invertTrajet['heure_depart']);
                                    $xdiffDate = date_diff($start, $endOfArriveTime);
                                     if(
                                        $xdiffDate->invert == 1 &&
                                        $diffDateEndOfArriveVRstartDepart->invert == 1 &&
                                         $diffDateEndOfArriveVRendDepart->invert == 1
                                    ){
                                         $canArrive++;
                                     }
                                 }
                                 if($canArrive > 0){
                                    $trajets[] = $data;
                                 }
                            }else{
                                $trajets[] = $data;
                            }
                        }
                    }else{
                        $trajets[] = $data;
                    }
                }
                // echo "-----------------------------------------";
                continue;
            }
            $data = array(
                'depart' => $departTrajet->getData(),
            );
            $availableDateForDepartTrajet = $toolbox->getTrajetInfo($departTrajet, $from);
            $data['depart']['info'] = $availableDateForDepartTrajet;
            $trajets[] = $data;
        }

            // die;
		
		Mage::getSingleton('core/session')->setTrajetCol($trajets); 
        $this->getResponse()->setBody(json_encode($trajets));
    }


    public function getAvailableDatesAction()
    {
        $data = $this->getRequest()->getParams();
        $origin = $data['origin'];
        $rOa = $data['ra'];
        $toolbox = Mage::helper('toolbox');

        $availableForDepart = array();
        $availableForArrive = array();

        if($rOa == 'ret'){
            $des = $data['destination'];
            $departTrajetsCollection = $toolbox->getTrajetWCollection($origin, $des);
            $arriveTrajetsCollection = $toolbox->getTrajetWCollection($des, $origin);
            foreach ($departTrajetsCollection as $departTrajet) {

                foreach ($arriveTrajetsCollection as $arriveTrajet) {
                    $departTrajetInfo = $toolbox->getTrajetInfo($departTrajet);
                    $arriveTrajetInfo = $toolbox->getTrajetInfo($arriveTrajet);
                    $availableForDepart = array_merge($availableForDepart,$departTrajetInfo['availableDates']);
                    $availableForArrive = array_merge($availableForArrive,$arriveTrajetInfo['availableDates']);
                }

            }
        }else{
            $departTrajetsCollection = $toolbox->getTrajetWCollection($origin, false);
            foreach ($departTrajetsCollection as $departTrajet) {
                    $departTrajetInfo = $toolbox->getTrajetInfo($departTrajet);
                    $availableForDepart = array_merge($availableForDepart,$departTrajetInfo['availableDates']);
            }
        }

        foreach ($availableForDepart as $k => $date) {
            $dateArray = explode('-',$date);
            $dateArray[1] = $dateArray[1] - 1;
            $availableForDepart[$k] = $dateArray;
        }


        $response = array(
            'depart' => $availableForDepart,
        );
        if($rOa == 'ret'){
            foreach ($availableForArrive as $k => $date) {
                $dateArray = explode('-',$date);
                $dateArray[1] = $dateArray[1] - 1;
                $availableForArrive[$k] = $dateArray;
            }
            $response['arrive'] = $availableForArrive;

        }
        $this->getResponse()->setBody(json_encode($response));
    }


//    private function findReservationWithSessions($trajet, $sessions)
//    {
//        $coreHelper = Mage::helper('core');
//        foreach ($sessions as $session) {
//
//            $sessionRaw = $session->getData();
//            $selectedDateStr = $coreHelper->formatDate($sessionRaw['date_from'] . " " . date('H:i:s', strtotime($sessionRaw['time_from'])), 'medium', true);
//            $product = Mage::getModel('catalog/product')->load($session->getProductId());
//            $productRaw = $product->getData();
//            $salleId = $product->getData('typesalle');
//
//            $trajet['bookmes'] = array();
//            $trajet['bookmes'] = array(
//                'time' => $selectedDateStr,
//                'product' => array(
//                    'id' => $productRaw['entity_id'],
//                    'sku' => $productRaw['sku'],
//                    'type' => $productRaw['type_id'],
//                ),
//                'sessionId' => $sessionRaw['session_id'],
//                'salleId' => $salleId,
//            );
//        }
//        return $trajet;
//    }

    public function reservationsAction()
    {
		 date_default_timezone_set("America/New_York");
        $request = $this->getRequest()->getParams();

        $response = array( );

        $coreHelper = Mage::helper('core');
        $toolbox = Mage::helper('toolbox');
        $fromDepartSessionId = $request['from_depart_session_id'];
        $departSalleId = $request['from_salles_id'];
		//$trajets = $request['trajets'];
		//$toolbox->cache($trajets);

        $fromSession = $toolbox->getSession($fromDepartSessionId)->getData();
        $FheureDateDebut = $fromSession['date_from'] . " " . $fromSession['time_from'];
        $reservations['from']= array(
            'reservationsNon' => $this->getNonReservation($FheureDateDebut, $departSalleId),
            'reservations' => $this->getReservation($FheureDateDebut, $departSalleId)
        );

        $response = array(
            'reservations' => $reservations,
        );
        if(!empty($request['from_arrive_session_id'])){
            $fromArriveSessionId = $request['from_arrive_session_id'];
            $arriveSalleId = $request['return_salles_id'];
            $returnSession = $toolbox->getSession($fromArriveSessionId);
            $RheureDateDebut = $returnSession['date_from'] . " " . $returnSession['time_from'];
            $reservations['return']= array(
                'reservationsNon' => $this->getNonReservation($RheureDateDebut, $arriveSalleId),
                'reservations' => $this->getReservation($RheureDateDebut, $arriveSalleId)
            );
            $response = array(
                'reservations' => $reservations,
            );
        }
        if(!empty($request['xfrom_arrive_session_id'])){
            $xFromArriveSessionId = $request['xfrom_arrive_session_id'];
            $xarriveSalleId = $request['xreturn_salles_id'];
            $xreturnSessionA = $toolbox->getSession($xFromArriveSessionId);
            
            $xRheureDateDebut = $xreturnSessionA['date_from'] . " " . $xreturnSessionA['time_from'];
            $reservations['xreturn']= array(
                'reservationsNon' => $this->getNonReservation($xRheureDateDebut, $xarriveSalleId),
                'reservations' => $this->getReservation($xRheureDateDebut, $xarriveSalleId)
            );
            $xdepartSalleId = $request['xfrom_salles_id'];
            
            $response['reservations'] =  $reservations;
        }
        if(!empty($request['xfrom_depart_session_id'])){
           
            $xdepartSalleId = $request['xfrom_salles_id'];
            $xFromDepartSessionId = $request['xfrom_depart_session_id'];

            $xfromSession = $toolbox->getSession($xFromDepartSessionId);
            $xFheureDateDebut =  $xfromSession['date_from'] . " " . $xfromSession['time_from'];
            $reservations['xfrom']= array(
                'reservationsNon' => $this->getNonReservation($xFheureDateDebut, $xdepartSalleId),
                'reservations' => $this->getReservation($xFheureDateDebut, $xdepartSalleId)
            );
            $response['reservations'] =  $reservations;
        }
        $this->getResponse()->setBody(json_encode($response));
    }



    private function getSession($id)
    {
        return Mage::getModel('bookmepro/session')->getCollection()
        ->addFieldToFilter('session_id', array('eq' => $id))
        ->setOrder('time_from', 'ASC')->getFirstItem();
    }

//    private function getSessionById($id)
//    {
//        return  Mage::getModel('bookmepro/session')->getCollection()
//        ->addFieldToFilter('session_id', array('eq' => $id))
//        ->getFirstItem()->getData();
//    }



    

//    private function getPrices($time ,$station_depart,$station_arrive)
//    {
//        $trajetws = $this->getTrajetWCollection($station_depart,$station_arrive);
////        ->addFieldToSelect('*');
////        die($trajetws->getSelect());die;
//        $trajetw = $trajetws->getFirstItem()->getData();
//        return array(
//            'ret_2664' => $trajetw['ret_2664'],
//            'ret_1825' => $trajetw['ret_1825'],
//            'ret_0307' => $trajetw['ret_0307'],
//            'ret_6599' => $trajetw['ret_6599'],
//            'all_2664' => $trajetw['all_2664'],
//            'all_1825' => $trajetw['all_1825'],
//            'all_0307' => $trajetw['all_0307'],
//            'all_6599' => $trajetw['all_6599'],
//        );
//    }

    private function getReservation($datedebut, $salleId)
    {
        return Mage::getResourceModel('reseauchx_reservationreseau/reservationsiege_collection')
//        ->addFieldToFilter('salle_id', $this->_salle->getId())
        ->addFieldToFilter('salle_id', $salleId)
        ->addFieldToFilter('confirme', 1)
        ->addFieldToFilter('dateheuredebut', array(
            'eq' => $datedebut,
            'datetime' => false))->getData()
        ;
    }

    private function getNonReservation($datedebut ,$salleId)
    {

        $updateMin = date("Y-m-d H:i:s", strtotime("-10 minutes"));
        $updateMax = date("Y-m-d H:i:s", strtotime("NOW"));
        
		return Mage::getResourceModel('reseauchx_reservationreseau/reservationsiege_collection')
//        ->addFieldToFilter('salle_id', $this->_salle->getId())
        ->addFieldToFilter('salle_id', $salleId)
        ->addFieldToFilter('confirme', 0)
        ->addFieldToFilter('dateheuredebut', array(
            'eq' => $datedebut,
            'datetime' => false))
        ->addFieldToFilter('updated_at', array(
            'from' => $updateMin,
            'to' => $updateMax,
            'datetime' => false))
			->getData()
        ;
    }
	public function test1Action()
    {
       
        date_default_timezone_set("America/New_York");
				$t=time();
echo(date('Y-m-d H:i:s',$t));die;
		
		$c= Mage::getResourceModel('reseauchx_reservationreseau/reservationsiege_collection')
        ->getLastItem()
			->getData()
        ;
		Zend_debug::dump($c);
    }


    public function addAction()
    {
        $res = array(
            'success' => true,
            'message' => array()
            );
        $cart = $this->_getCart();
        $params = $this->getRequest()->getParams();
		$toolbox = Mage::helper('toolbox');


        $params['simples'] = json_decode($params['simples'], true);

        $params['depart'] = json_decode($params['depart'], true);
        if(!empty($params['arrive'])){
            $params['arrive'] = json_decode($params['arrive'], true);
        }
        if(!empty($params['xdepart'])){
            $params['xdepart'] = json_decode($params['xdepart'], true);
        }
        if(!empty($params['xarrive'])){
            $params['xarrive'] = json_decode($params['xarrive'], true);
        }
        if(!empty($params['bag'])){
            $params['bag'] = json_decode($params['bag'], true);
        }
        try {
			$toolbox->cache(array(
				'station' => $params['station'],
				'products' => $params['simples']
			));
            $simpleCollection = Mage::getModel('catalog/product')->getCollection()
                ->addAttributeToFilter('sku', array('in' => array_keys($params['simples'])));

            foreach ($simpleCollection as $simple) {
                $product = Mage::getModel('catalog/product')->load($simple->getId());
                $cart->addProduct($product, array('qty' => $params['simples'][$simple->getSku()]['qty']));
            }
            $departProduct =  Mage::getModel('catalog/product')->load($params['depart']['id']);

            $cart->addProduct($departProduct, array(
                    'qty' => $params['depart']['qty'],
                    'options'=>$params['depart']['options']
            ));
            if(!empty($params['arrive'])){
                $arriveProduct =  Mage::getModel('catalog/product')->load($params['arrive']['id']);
                $cart->addProduct($arriveProduct, array(
                    'qty' => $params['arrive']['qty'],
                    'options'=>$params['arrive']['options']
                ));
            }

            if(!empty($params['xdepart'])){
                $xdepartProduct =  Mage::getModel('catalog/product')->load($params['xdepart']['id']);
                $cart->addProduct($xdepartProduct, array(
                    'qty' => $params['xdepart']['qty'],
                    'options'=>$params['xdepart']['options']
                ));
            }

            if(!empty($params['xarrive'])){
                $xarriveProduct =  Mage::getModel('catalog/product')->load($params['xarrive']['id']);
                $cart->addProduct($xarriveProduct, array(
                    'qty' => $params['xarrive']['qty'],
                    'options'=>$params['xarrive']['options']
                ));
            }
            if(!empty($params['bag'])){
            	foreach ($params['bag'] as $id => $qty) {
            		$bag =  Mage::getModel('catalog/product')->load($id);
	                $cart->addProduct($bag, array(
	                    'qty' => $qty,
	                ));
            	}
            }


            $cart->save();
			$toolbox->clear();
//            $sidebar = $this->getLayout()->createBlock('checkout/cart_minicart')->setTemplate('checkout/cart/minicart.phtml');
//            $sidebarItems = $this->getLayout()->createBlock('checkout/cart_sidebar')->setTemplate('checkout/cart/minicart/items.phtml');
//            $res['cart'] = $sidebar->toHtml();
//            $res['cartItems'] = $sidebarItems->toHtml();

        } catch (Mage_Core_Exception $e) {
            $res['success'] = false;
            $res['message'][] = $e->getMessage();
        }
        $this->getResponse()->setBody(json_encode($res));
    }

    /**
     * Retrieve shopping cart model object
     *
     * @return Mage_Checkout_Model_Cart
     */
    protected function _getCart()
    {
        return Mage::getSingleton('checkout/cart');
    }

    /**
     * Initialize product instance from request data
     *
     * @return Mage_Catalog_Model_Product || false
     */
    protected function _initProduct()
    {
        $productId = (int)$this->getRequest()->getParam('product');
        if ($productId) {
            $product = Mage::getModel('catalog/product')
            ->setStoreId(Mage::app()->getStore()->getId())
            ->load($productId);
            if ($product->getId()) {
                return $product;
            }
        }
        return false;
    }
    public function testAction()
    {

        $response = array();
        $coreHelper = Mage::helper('core');
        $toolbox = Mage::helper('toolbox');
        $fromDepartSessionId = 704372;
        $departSalleId = 4;
		$pro = $xarriveProduct =  Mage::getModel('catalog/product')->loadByAttribute('sku', "navbspmalregall-2664");


        $fromSession = $toolbox->getSession($fromDepartSessionId)->getData();
        $FselectedDateStr = $coreHelper->formatDate($fromSession['date_from'] . " " . date('H:i:s', strtotime($fromSession['time_from'])), 'medium', true);
        $FheureDateDebut = new DateTime($FselectedDateStr);
        $reservations = array();
        // $reservations['from']= array(
        //     'reservationsNon' => $this->getNonReservation($FheureDateDebut, $departSalleId),
        //     'reservations' => $this->getReservation($FheureDateDebut, $departSalleId)
        // );

        $response = array(
            'reservations' => $reservations,
        );
        $e = Mage::getResourceModel('reseauchx_reservationreseau/reservationsiege_collection')
//        ->addFieldToFilter('salle_id', $this->_salle->getId())
        ->addFieldToFilter('salle_id', $departSalleId)
        //->addFieldToFilter('confirme', 1)
        ->addFieldToFilter('dateheuredebut', array('eq'=> $fromSession['date_from'].' '.$fromSession['time_from'], 'datetime' => true))
         ->addFieldToFilter('dateheuredebut', array(
             'from' => date_format($datedebut,"Y/m/d H:i:s"),
             'to' => date_format($datedebut,"Y/m/d H:i:s"),
             'datetime' => false)
         )
    	->getData()
        ;
        // $s = $toolbox->getBookingInfo(1025, "2016/10/09");
        echo "<pre>";

        // var_dump(date_format($FheureDateDebut,"Y/m/d H:i:s"));

        //var_dump( $fromSession['date_from'].' '.$fromSession['time_from']);
        //var_dump($pro->getData());
		//var_dump(Mage::getSingleton('core/session')->getTrajetCol());
		//var_dump($toolbox->getSelectedTrajets());
		var_dump($toolbox->inTrajet($pro));
        die;
    }
}