<?php
require_once 'Mage/Checkout/controllers/OnepageController.php';
class Drieworks_Tickets_OnepageController extends  Mage_Checkout_OnepageController{
	
		/**
     * Save ticket ajax action
     *
     * Sets either redirect or a JSON response
     */
	public function saveTicketsAction()
    {
        if ($this->_expireAjax()) {
			return;
		}
		
        if ($this->getRequest()->isPost()) {
            $data = $this->getRequest()->getPost('tickets', array());
			
			if (!empty($data)){
			
			$m = 0;
				foreach ($data as $ticket){
					$m++;
					if (strlen($ticket['name']) < 2 && strlen($ticket['email']) < 2) { 
						unset($data[$m]); 
					}
				}
			}
			
            $result = $this->getOnepage()->saveTickets($data);

            //$this->getOnepage()->saveOrder($data);
			
			Mage::Log("Onepage controller");
            if (!isset($result['error'])) {

                $this->loadLayout('checkout_onepage_review');
                $result['goto_section'] = 'review';
                $result['update_section'] = array(
                    'name' => 'review',
                    'html' => $this->_getReviewHtml()
                );
            }
			
            $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($result));
        }
    }

	public function savePaymentAction()
	{
		if ($this->_expireAjax()) {
			return;
		}
		try {
			if (!$this->getRequest()->isPost()) {
				$this->_ajaxRedirectResponse();
				return;
			}

			// set payment to quote
			$result = array();
			$data = $this->getRequest()->getPost('payment', array());
			$result = $this->getOnepage()->savePayment($data);

			if (empty($result['error'])) {
				if ($this->getOnepage()->getQuote()->getItemVirtualQty() > 0) {
					$virtual = false;
					foreach ($this->getOnepage()->getQuote()->getAllItems() as $item) {
						if($item->getProductType() == 'virtual') $virtual = true;
					}
					if($virtual){
						$result['goto_section'] = 'tickets';                
						Mage::Log('Goto Tickets');
					} else {
						Mage::Log('Goto Review');
						$this->loadLayout('checkout_onepage_review');
						$result['goto_section'] = 'review';
						$result['update_section'] = array(
							'name' => 'review',
							'html' => $this->_getReviewHtml()
						);
					}
				} else {
				Mage::Log('Goto Review');
				$this->loadLayout('checkout_onepage_review');
                $result['goto_section'] = 'review';
                $result['update_section'] = array(
                    'name' => 'review',
                    'html' => $this->_getReviewHtml()
                );
				}
			}

		} catch (Mage_Payment_Exception $e) {
			if ($e->getFields()) {
				$result['fields'] = $e->getFields();
			}
			$result['error'] = $e->getMessage();
		} catch (Mage_Core_Exception $e) {
			$result['error'] = $e->getMessage();
		} catch (Exception $e) {
			Mage::logException($e);
			$result['error'] = $this->__('Unable to set Payment Method.');
		}
		$this->getResponse()->setBody(Mage::helper('core')->jsonEncode($result));
	}

}