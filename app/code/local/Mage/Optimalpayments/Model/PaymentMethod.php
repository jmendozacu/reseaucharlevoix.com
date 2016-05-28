<?php
 
class Mage_Optimalpayments_Model_PaymentMethod extends Mage_Payment_Model_Method_Cc
{
    /**
    * unique internal payment method identifier
    *
    * @var string [a-z0-9_]
    */
    protected $_code = 'optimalpayments';

	const PAYMENT_ACTION_AUTH_CAPTURE = 'ccPurchase';
	const PAYMENT_ACTION_AUTH = 'ccAuthorize';
	const PAYMENT_ACTION_SETTLE = 'ccSettlement';
	const PAYMENT_ACTION_CREDIT = 'ccCredit';
	const PAYMENT_ACTION_CANCEL_PAYMENT = 'ccCancelPayment';
	const PAYMENT_ACTION_CANCEL_CREDIT = 'ccCancelCredit';
	const PAYMENT_ACTION_CANCEL_SETTLE = 'ccCancelSettle';
	const STATUS_APPROVED = 'ACCEPTED';
	const STATUS_DECLINED = 'DECLINED';
	
	const RESULT_STATUS_APPROVED = 'APPROVED';


    protected $_isGateway               = true;
    protected $_canAuthorize            = true;
    protected $_canCapture              = true;
    protected $_canCapturePartial       = true;
    protected $_canRefund               = true;
    protected $_canVoid                 = true;
    protected $_canUseInternal          = true;
    protected $_canUseCheckout          = true;
    protected $_canUseForMultishipping  = true;
    protected $_canSaveCc = false;

    public function canRefund()
    {
        return $this->_canRefund;
        
    }
   public function canVoid(Varien_Object $payment)
    {
        return $this->_canVoid;
    }
  public function canCapturePartial()
    {
        return $this->_canCapturePartial;
    }

 public function canAuthorize()
    {
        return $this->_canAuthorize;
    }

    
	public function canCapture()
    {
        return $this->_canCapture;
    }
 
	
	
	
	private function getStoreid()
	{
		return Mage::getStoreConfig('payment/optimalpayments/store_id');
	}
		private function getPassword()
	{
		return Mage::getStoreConfig('payment/optimalpayments/password');
	}
		private function getAccount()
	{
		return Mage::getStoreConfig('payment/optimalpayments/account');
	}
		private function getTest()
	{
		return Mage::getStoreConfig('payment/optimalpayments/test');
	}
		private function getpaymentAction()
	{
		return Mage::getStoreConfig('payment/optimalpayments/payment_action');
	}
	 
	   public function authorize(Varien_Object $payment, $amount)
    {
        $error = false;

        if($amount>0){
			   $txnMode = self::PAYMENT_ACTION_AUTH;
		
            $payment->setAmount($amount);

    	$txnRequest = $this->_buildRequest($payment, $txnMode, $amount);
		$result = $this->_postRequest($txnMode, $txnRequest);
		
		$payment->setCcApproval($result['authCode'])
                ->setLastTransId($result['confirmation'])
                ->setCcTransId($result['confirmation'])
                ->setCcAvsStatus($result['avsResponse'])
                ->setCcCidStatus($result['cvdResponse']);

            switch ($result['status']) {
                case self::STATUS_APPROVED:
                    $payment->setStatus(self::RESULT_STATUS_APPROVED);
                    break;
                case self::STATUS_DECLINED:
                    $error = Mage::helper('paygate')->__('Payment authorization transaction has been declined.<br />'.$result['description']);
                    break;
                default:
                    $error = Mage::helper('paygate')->__('Payment authorization error.<br />'.$result['description']);
                    break;
            }
        }else{
            $error = Mage::helper('paygate')->__('Invalid amount for authorization.<br />'.$result['description']);
        }

        if ($error !== false) {
            Mage::throwException($error);
        }



        return $this;
    }


   public function capture(Varien_Object $payment, $amount)
    {
        $error = false;
	
		$txnMode  = 0;
		
        if ($payment->getCcTransId()) {
		//	$txRefid = $payment->getCcTransId();
            $txnMode = self::PAYMENT_ACTION_SETTLE;
        } else {
            $txnMode = self::PAYMENT_ACTION_AUTH_CAPTURE;
        }

        $payment->setAmount($amount);

		$txnRequest = $this->_buildRequest($payment, $txnMode, $amount);
		$request = $this->_postRequest($txnMode, $txnRequest);

        if ($request['status'] == self::STATUS_APPROVED) {
            $payment->setStatus(self::RESULT_STATUS_APPROVED);
            
            $payment->setLastTransId($request['confirmation']);
        }
        else {
            if ($request['description']) {
                $error = $request['description'];
            }
            else {
                $error = Mage::helper('paygate')->__('Error in capturing the payment '.$request['description']);
            }
        }

        if ($error !== false) {
            Mage::throwException($error);
        }




        return $this;
    }


	
	  public function refund(Varien_Object $payment, $amount)
    {

        $error = false;
        if ($payment->getRefundTransactionId() && $amount>0) {
		$txnRequest = $this->_buildRequest($payment,self::PAYMENT_ACTION_CREDIT,$amount); 
		$request = $this->_postRequest(self::PAYMENT_ACTION_CREDIT, $txnRequest);

            if ($request['status']==self::STATUS_APPROVED) {
                $payment->setStatus(self::STATUS_APPROVED);
            } else {
                $error = $request['description'];
            }

        } else {
            $error = Mage::helper('paygate')->__('Error in refunding the payment');
        }

        if ($error !== false) {
            Mage::throwException($error);
        }
        return $this;
    }


  public function cancel(Varien_Object $payment)
    {
        $error = false;
	if($payment->getCcApproval() && $payment->getOrder()->getTotalPaid() == 0){ // this is where we store the auth number, no reall way other than checking order attribute 
			//if($this->getpaymentAction() == 'authorize'){  // this should be the way to handle this, however it returns  the current state of the payment module, not the state it was in when the payment was captured
				$payment->setStatus(self::STATUS_APPROVED );		
				return $this; // we dont cancel auths
        } 

        if($payment->getLastTransId()){
				  $txmode = self::PAYMENT_ACTION_CANCEL_SETTLE;

		$txnRequest = $this->_buildRequest($payment,$txmode,0); 
		$request = $this->_postRequest($txmode, $txnRequest);
	
            if($request['status']==self::STATUS_APPROVED){
                 $payment->setStatus(self::STATUS_APPROVED );
            }
            else{
                $payment->setStatus(self::STATUS_ERROR);
                $error = $result['description'];
            }
        }else{
            $payment->setStatus(self::STATUS_ERROR);
            $error = Mage::helper('paygate')->__('Invalid transaction id');
        }
        if ($error !== false) {
            Mage::throwException($error);
		Mage::log(__FILE__.' '.$error);
			}
        return $this;
    }

	

    public function void(Varien_Object $payment)
    {
        $this->cancel($payment); // same thing

        return $this;
    }

	
	
  	
    protected function _buildRequest(Varien_Object $payment, $requestType, $amount = 0)
    {
        $order = $payment->getOrder();
		$txRefid = 0;

	$start_xml_Type = "";
	$end_xml_Type = "";
	$order_data = 1;
	$merchantRefNum = 0;

	switch($requestType) {
		case self::PAYMENT_ACTION_AUTH:
		case self::PAYMENT_ACTION_AUTH_CAPTURE:
	$start_xml_Type = "<ccAuthRequestV1 xmlns=\"http://www.optimalpayments.com/creditcard/xmlschema/v1\" xmlns:xsi=\"http://www.w3.org/2001/XMLSchema-instance\" xsi:schemaLocation=\"http://www.optimalpayments.com/creditcard/xmlschema/v1\">\n";
	$end_xml_Type = "</ccAuthRequestV1>";
	$merchantRefNum = $payment->getOrder()->getIncrementId();
		// purchase header here
		break;
		case self::PAYMENT_ACTION_SETTLE: // these get the same XML bits but different RefIds
	 $start_xml_Type = "<ccPostAuthRequestV1 xmlns=\"http://www.optimalpayments.com/creditcard/xmlschema/v1\"
xmlns:xsi=\"http://www.w3.org/2001/XMLSchema-instance\"
xsi:schemaLocation=\"http://www.optimalpayments.com/creditcard/xmlschema/v1\">\n";
	$end_xml_Type = "</ccPostAuthRequestV1>";
	$order_data = 0;
	$txRefid = $payment->getCcTransId();
	$merchantRefNum = $payment->getOrder()->getIncrementId();
		break;		case self::PAYMENT_ACTION_CREDIT:
	 $start_xml_Type = "<ccPostAuthRequestV1 xmlns=\"http://www.optimalpayments.com/creditcard/xmlschema/v1\"
xmlns:xsi=\"http://www.w3.org/2001/XMLSchema-instance\"
xsi:schemaLocation=\"http://www.optimalpayments.com/creditcard/xmlschema/v1\">\n";
	$end_xml_Type = "</ccPostAuthRequestV1>";
	$order_data = 0;
	$txRefid = $payment->getRefundTransactionId();
	$merchantRefNum = $payment->getOrder()->getIncrementId();
		break;
		case self::PAYMENT_ACTION_CANCEL_SETTLE:
		case self::PAYMENT_ACTION_CANCEL_CREDIT:
		case self::PAYMENT_ACTION_CANCEL_PAYMENT:
	 $start_xml_Type = "<ccCancelRequestV1 xmlns=\"http://www.optimalpayments.com/creditcard/xmlschema/v1\"
xmlns:xsi=\"http://www.w3.org/2001/XMLSchema-instance\"
xsi:schemaLocation=\"http://www.optimalpayments.com/creditcard/xmlschema/v1\">\n";
	$end_xml_Type = "</ccCancelRequestV1>";
	$order_data = 0;
	$txRefid = $payment->getVoidTransactionId();
		if(!$txRefid)
		 $txRefid = $payment->getLastTransId();
		break;
	}

	
		$txnRequest = "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n";
		$txnRequest .= $start_xml_Type;

		$txnRequest .= "<merchantAccount>\n";
		$txnRequest .= "<accountNum>".$this->getAccount()."</accountNum>\n";
		$txnRequest .= "<storeID>".$this->getStoreid()."</storeID>\n";
		$txnRequest .= "<storePwd>".$this->getPassword()."</storePwd>\n";
		$txnRequest .= "</merchantAccount>\n";


		if($txRefid > 0)  //  if refund / cancel we will pass in ref id else it will just be Zero
	$txnRequest .= "<confirmationNumber>".$txRefid."</confirmationNumber>\n";


 if ($merchantRefNum)        // whats the diff in this or $order->getId() && payment->getOrder()->getIncrementId()
		$txnRequest .= "<merchantRefNum>".$merchantRefNum."</merchantRefNum>\n";


	if($amount)
$txnRequest .= "<amount>".sprintf("%01.2f",$amount)."</amount>\n";
        
		
	if($payment->getCcNumber() && $order_data){             
				$txnRequest .= "<card>\n";
				$txnRequest .= "<cardNum>".$payment->getCcNumber()."</cardNum>\n";
				$txnRequest .= "<cardExpiry>\n<month>".sprintf('%02d',  $payment->getCcExpMonth())."</month>\n<year>".sprintf('%04d',  $payment->getCcExpYear())."</year>\n</cardExpiry>\n";
				//$txnRequest .= "<cardType>".$cardType."</cardType>\n";
				//$txnRequest .= "<cvdIndicator>".$cvdIndicator."</cvdIndicator>\n";
				$txnRequest .= "<cvdIndicator>1</cvdIndicator>\n";
				$txnRequest .= "<cvd>".$payment->getCcCid()."</cvd>\n";
				$txnRequest .= "</card>\n";
}




   if (!empty($order) &&  $order_data) {

            $billing = $order->getBillingAddress();
            if (!empty($billing)) {
$txnRequest .= "<billingDetails>\n";
$txnRequest .= "<cardPayMethod>WEB</cardPayMethod>\n";
$txnRequest .= "<firstName>".$billing->getFirstname()."</firstName>\n";
$txnRequest .= "<lastName>".$billing->getLastname()."</lastName>\n";
$txnRequest .= "<street>".$billing->getStreet(1)."</street>\n";
//l$txnRequest .= "<street2>".$bStreet2."</street2>\n";
$txnRequest .= "<city>".$billing->getCity()."</city>\n";
$txnRequest .= "<region>".$billing->getRegionCode()."</region>\n";
$txnRequest .= "<country>".$billing->getCountry()."</country>\n";
$txnRequest .= "<zip>".preg_replace('/[^a-z0-9]/i','',$billing->getPostcode())."</zip>\n";
//$txnRequest .= "<zip>".$billing->getPostcode()."</zip>\n";
$txnRequest .= "<phone>".$billing->getTelephone()."</phone>\n";
$txnRequest .= "<email>".$billing->getEmail()."</email>\n";
$txnRequest .= "</billingDetails>\n";
}

            $shipping = $order->getShippingAddress();
            if (!empty($shipping)) {
$txnRequest .= "<shippingDetails>\n";
// $txnRequest .= "<carrier>FEX</carrier>\n";
/// $txnRequest .= "<shipMethod>T</shipMethod>\n";
$txnRequest .= "<firstName>".$shipping->getFirstname()."</firstName>\n";
$txnRequest .= "<lastName>".$shipping->getLastname()."</lastName>\n";
$txnRequest .= "<street>".$shipping->getStreet(1)."</street>\n";
$txnRequest .= "<city>".$shipping->getCity()."</city>\n";
$txnRequest .= "<region>".$shipping->getRegionCode()."</region>\n";
$txnRequest .= "<country>".$shipping->getCountry()."</country>\n";
//$txnRequest .= "<zip>".$shipping->getPostcode()."</zip>\n";
$txnRequest .= "<zip>".preg_replace('/[^a-z0-9]/i','',$shipping->getPostcode())."</zip>\n";
//$txnRequest .= "<phone>".$shipping->get()."</phone>\n";
//$txnRequest .= "<email>".$shipping->get()."</email>\n";
$txnRequest .= "</shippingDetails>\n";

         }

        //    $request->setXPoNum($payment->getPoNumber())
          //      ->setXTax($order->getTaxAmount())
            //    ->setXFreight($order->getShippingAmount());
        }
		
		
$txnRequest .= $end_xml_Type;
        return $txnRequest;
    }

	protected function _postRequest($txnMode, $txnRequest) 
	{
	
	     $url = "https://webservices.test.optimalpayments.com/creditcardWS/CreditCardServlet/v1";	       
  
  if($this->getTest() != 1)  // if we are not in test mode than lets go live
	    $url = "https://webservices.optimalpayments.com/creditcardWS/CreditCardServlet/v1";	       

		$ch = curl_init();
		curl_setopt($ch, CURLOPT_POST,1);
		curl_setopt($ch, CURLOPT_POSTFIELDS,"&txnMode=".$txnMode."&txnRequest=".urlencode($txnRequest));
		curl_setopt($ch, CURLOPT_URL,$url);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);

        $qresult = curl_exec($ch); 
				$result = array();
		$result['status'] = 0;
		$result['confirmation'] = 0;


				
	//	echo("\n" + $qresult);
        if (curl_errno($ch)) {
            print "\nError: " . curl_error($ch);
        } else {
       //     var_dump($qresult);
            curl_close($ch);
        
		// Parse the XML object
		// stupid namespace hax0r
$qresult = str_replace("xmlns=","a=",$qresult);

		$xmlObj = simplexml_load_string($qresult);
		
		
		$xpath = $xmlObj->xpath('decision');
		$result['status'] = ($xpath !== FALSE && isset($xpath[0])) ? (string)$xpath[0] : '';
		$xpath = $xmlObj->xpath('confirmationNumber');
		$result['confirmation'] = ($xpath !== FALSE && isset($xpath[0])) ? (string)$xpath[0] : '';

		$xpath = $xmlObj->xpath('code');
		$result['code'] = ($xpath !== FALSE && isset($xpath[0])) ? (string)$xpath[0] : '';
		$xpath = $xmlObj->xpath('description');
		$result['description'] = ($xpath !== FALSE && isset($xpath[0])) ? (string)$xpath[0] : '';
		$xpath = $xmlObj->xpath('authCode');
		$result['authCode'] = ($xpath !== FALSE && isset($xpath[0])) ? (string)$xpath[0] : '';
		$xpath = $xmlObj->xpath('avsResponse');
		$result['avsResponse'] = ($xpath !== FALSE && isset($xpath[0])) ? (string)$xpath[0] : '';
		$xpath = $xmlObj->xpath('cvdResponse');
		$result['cvdResponse'] = ($xpath !== FALSE && isset($xpath[0])) ? (string)$xpath[0] : '';
		
		

		}
		return $result;
	}

	}
	
	
	 
	 
	 
	 
//}  these guy got lost and stopped in...