<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the End User License Agreement (EULA)
 * that is bundled with this package in the file LICENSE.txt.
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to vdesign.support@outlook.com so we can send you a copy immediately.
 *
 * @category   Mage
 * @copyright  Copyright (c) 2014 VDesign
 * @license    End User License Agreement (EULA)
 */
Mage::log('BookmePro Invoice');
class VDesign_BookmePro_Model_Sales_Order_Pdf_Items_Invoice
extends Mage_Sales_Model_Order_Pdf_Items_Abstract{
	
	/**
	 * Draw item line
	 *
	 */
	public function draw()
	{
		date_default_timezone_set(Mage::app()->getStore()->getConfig('general/locale/timezone'));
		$helper = Mage::helper('bookmepro');
		$config = Mage::getStoreConfig('bookmepro_sales/sales/invoice_print', Mage::app()->getStore()->getId());
		$order  = $this->getOrder();
		$item   = $this->getItem();
		$pdf    = $this->getPdf();
		$page   = $this->getPage();
		$lines  = array();
	
		// draw Product name
		$lines[0] = array(array(
				'text' => Mage::helper('core/string')->str_split($item->getName(), 35, true, true),
				'feed' => 35,
		));
	
		// draw SKU
		$lines[0][] = array(
				'text'  => Mage::helper('core/string')->str_split($this->getSku($item), 17),
				'feed'  => 290,
				'align' => 'right'
		);
	
		// draw QTY
		$lines[0][] = array(
				'text'  => $item->getQty() * 1,
				'feed'  => 435,
				'align' => 'right'
		);
	
		// draw item Prices
		$i = 0;
		$prices = $this->getItemPricesForDisplay();
		$feedPrice = 395;
		$feedSubtotal = $feedPrice + 170;
		foreach ($prices as $priceData){
			if (isset($priceData['label'])) {
				// draw Price label
				$lines[$i][] = array(
						'text'  => $priceData['label'],
						'feed'  => $feedPrice,
						'align' => 'right'
				);
				// draw Subtotal label
				$lines[$i][] = array(
						'text'  => $priceData['label'],
						'feed'  => $feedSubtotal,
						'align' => 'right'
				);
				$i++;
			}
			// draw Price
			$lines[$i][] = array(
					'text'  => $priceData['price'],
					'feed'  => $feedPrice,
					'font'  => 'bold',
					'align' => 'right'
			);
			// draw Subtotal
			$lines[$i][] = array(
					'text'  => $priceData['subtotal'],
					'feed'  => $feedSubtotal,
					'font'  => 'bold',
					'align' => 'right'
			);
			$i++;
		}
	
		// draw Tax
		$lines[0][] = array(
				'text'  => $order->formatPriceTxt($item->getTaxAmount()),
				'feed'  => 495,
				'font'  => 'bold',
				'align' => 'right'
		);
	
		// custom options
		$options = $this->getItemOptions();
		if ($options) {
			foreach ($options as $option) {
				// draw options label
				$lines[][] = array(
						'text' => Mage::helper('core/string')->str_split(strip_tags($option['label']), 40, true, true),
						'font' => 'italic',
						'feed' => 35
				);
	
				if ($option['value']) {
					if (isset($option['print_value'])) {
						$_printValue = $option['print_value'];
					} else {
						$_printValue = strip_tags($option['value']);
					}
					
					//Eric: Render custom option
					Mage::log('Render Option Eric');
					if($option['option_type'] =='multidate_type'){
						$_printValue = "";
						$selectedSession = unserialize($option['value']);
						$selectedSession = explode("#",$selectedSession['values']);
						Mage::log('Session: ' . $selectedSession[0]);
						$session = Mage::getModel('bookmepro/session')->load($selectedSession[0]);
						$selectedDateStr = Mage::helper('core')->formatDate($session['date_from']." ".date('H:i:s', strtotime($session['time_from'])), 'medium', true);
						$_printValue = $selectedDateStr;
					}
					if($option['option_type'] =='selectionsiege_type'){
						$_printValue = "";
						$sieges = explode(',', $option['value']);
						foreach ($sieges as $siege) {
							$siegeId = ltrim($siege, 's');
							$siegeData = Mage::getModel('reseauchx_reservationreseau/siege')->load($siegeId);
							Mage::log('Siege: ' . $siegeData->getData('code'));
							$_printValue = $_printValue . $siegeData->getData('code') . ' ';
						}
					}
					
					
					$p_out = array();
					$out = array();
					$data = unserialize($option['value']);
					
					
					$profiles = $data['profiles'];
					foreach ($profiles as $key => $value)
						if($value > 0)
						{
							$id = explode("#", $key);
							$p_out[] = $helper->getProfileTranslation($id[1], Mage::app()->getStore()->getCode())." : ".$value;
						}
					 
					if (strpos($data['values'], '-') !== FALSE){ //adventure
						$values = explode("#", $data['values']);
						$session = Mage::getModel('bookmepro/session')->load($values[1]);
					
						$from = strtotime($session['date_from'].' '.date('H:i:s', strtotime($session['time_from'])));
						$to = strtotime($session['date_to'].' '.date('H:i:s', strtotime($session['time_to'])));
					
						if($config == 1)
							$out[] = Mage::helper('bookmepro')->__('1 reserved event.');
						else
							$out[] = Mage::helper('core')->formatDate(date("d-m-Y H:i:s",$from), 'medium', true)." - ".Mage::helper('core')->formatDate(date("d-m-Y H:i:s",$to), 'medium', true);
					}
					 
					 
					if (strpos($data['values'], ',') !== FALSE){ //date
						$values = explode(",", $data['values']);
						$from = date("d-m-Y", $values[0]/1000);
						$to = date("d-m-Y", $values[count($values)-2]/1000);
						 
						if($config == 1)
							$out[] = (count($values) - 1).Mage::helper('bookmepro')->__(' reserved days.');
						else
							$out[] = Mage::helper('core')->formatDate($from, 'medium', false)." - ".Mage::helper('core')->formatDate($to, 'medium', false);
					}
					 
					 
					if (strpos($data['values'], '#') !== FALSE && strpos($data['values'], '-') == FALSE){ //session
						$values = explode("#", $data['values']);
						 
						if($config == 1)
							$out[] = (count($values) - 1).Mage::helper('bookmepro')->__(' reserved sessions.');
						else
							foreach ($values as $id)
							{
								$session = Mage::getModel('bookmepro/session')->load($id);
								if($session->getId())
									$out[] = Mage::helper('core')->formatDate($session['date_from']." ".date('H:i:s', strtotime($session['time_from'])), 'medium', true);
							}
					}
					
					if($option['option_type'] =='selectionsiege_type'){
						$out[] = $_printValue;
					}
					
					$out = array_merge($out, $p_out);
					
					foreach ($out as $o)
						$lines[][] = array(
										'text' => Mage::helper('core/string')->str_split($o, 30, true, true),
										'feed' => 40
								);
					
// 					$values = explode(', ', $_printValue);
					
// 					foreach ($values as $value) {
// 						$out = array();
// 						$data = explode(",", $value);
// 						$from = date("d-m-Y", $data[0]/1000);
						
// 						if(count($data) == 1 || strtotime($from) < $data[0] / 1000){
// 							for($i = 0; $i < count($data) - 1; $i++)
// 								$out[] = Mage::helper('core')->formatDate(date("d-m-Y H:i:s", $data[$i]/1000), 'medium', true);
// 						}else{
// 							$to = date("d-m-Y", $data[count($data)-2]/1000);

// 							$out[] = Mage::helper('core')->formatDate($from, 'medium', false).' - '.Mage::helper('core')->formatDate($to, 'medium', false);	
// 						}
// 						foreach ($out as $o)
// 							$lines[][] = array(
// 									'text' => Mage::helper('core/string')->str_split($o, 30, true, true),
// 									'feed' => 40
// 							);
// 					}
				}
			}
		}
		
	
		$lineBlock = array(
				'lines'  => $lines,
				'height' => 20
		);
	
		$page = $pdf->drawLineBlocks($page, array($lineBlock), array('table_header' => true));
		$this->setPage($page);
	}
} 