<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category    Mage
 * @package     Mage_Sales
 * @copyright   Copyright (c) 2011 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


/**
 * Sales Order Invoice PDF model
 *
 * @category   Mage
 * @package    Mage_Sales
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Drieworks_EmailAttachments_Model_Order_Pdf_Invoice extends Mage_Sales_Model_Order_Pdf_Abstract
{
    public function getPdf($invoices = array())
    {
        $this->_beforeGetPdf();
        $this->_initRenderer('invoice');

        $pdf = new Zend_Pdf();
        $this->_setPdf($pdf);
        $style = new Zend_Pdf_Style();
        $this->_setFontBold($style, 10);

        foreach ($invoices as $invoice) {
            if ($invoice->getStoreId()) {
                Mage::app()->getLocale()->emulate($invoice->getStoreId());
                Mage::app()->setCurrentStore($invoice->getStoreId());
            }
            $page = $pdf->newPage(Zend_Pdf_Page::SIZE_A4);
            $pdf->pages[] = $page;

            $order = $invoice->getOrder();

            /* Add image */
            $this->insertLogo($page, $invoice->getStore());

            /* Add address */
            $this->insertAddress($page, $invoice->getStore());

            /* Add head */
            $this->insertOrder($page, $order, Mage::getStoreConfigFlag(self::XML_PATH_SALES_PDF_INVOICE_PUT_ORDER_ID, $order->getStoreId()));


            $page->setFillColor(new Zend_Pdf_Color_GrayScale(1));
            $this->_setFontRegular($page);
            $page->drawText(Mage::helper('sales')->__('Invoice # ') . $invoice->getIncrementId(), 35, 780, 'UTF-8');

            /* Add table */
            $page->setFillColor(new Zend_Pdf_Color_RGB(0.93, 0.92, 0.92));
            $page->setLineColor(new Zend_Pdf_Color_GrayScale(0.5));
            $page->setLineWidth(0.5);

            $page->drawRectangle(25, $this->y, 570, $this->y -15);
            $this->y -=10;

            /* Add table head */
            $page->setFillColor(new Zend_Pdf_Color_RGB(0.4, 0.4, 0.4));
            $page->drawText(Mage::helper('sales')->__('Products'), 35, $this->y, 'UTF-8');
            $page->drawText(Mage::helper('sales')->__('SKU'), 255, $this->y, 'UTF-8');
            $page->drawText(Mage::helper('sales')->__('Price'), 380, $this->y, 'UTF-8');
            $page->drawText(Mage::helper('sales')->__('Qty'), 430, $this->y, 'UTF-8');
            $page->drawText(Mage::helper('sales')->__('Tax'), 480, $this->y, 'UTF-8');
            $page->drawText(Mage::helper('sales')->__('Subtotal'), 535, $this->y, 'UTF-8');

            $this->y -=15;

            $page->setFillColor(new Zend_Pdf_Color_GrayScale(0));

            /* Add body */
            foreach ($invoice->getAllItems() as $item){
                if ($item->getOrderItem()->getParentItem()) {
                    continue;
                }

                if ($this->y < 15) {
                    $page = $this->newPage(array('table_header' => true));
                }

                /* Draw item */
                $page = $this->_drawItem($item, $page, $order);
            }

            /* Add totals */
            $page = $this->insertTotals($page, $invoice);
			
			if ($invoice->getStoreId()) {
                Mage::app()->getLocale()->revert();
            }
			
			/* add tickets if availeble */
			$resource = Mage::getSingleton('core/resource');
			$write = $resource->getConnection('core_write');
			
			$incrementId = $order->getIncrementId();
			$invoiceId = $invoice->getId();
			
			$queryTicketOrders = "SELECT * FROM `ticket_orders` WHERE `order_id` = " . $incrementId . " AND `ticket_type`='ticket'";
			$pdfTicketOrders = $write->fetchAll($queryTicketOrders);
			
			if (!empty($pdfTicketOrders)){
			
			require_once('class/BCGFont.php');
			require_once('class/BCGColor.php');
			require_once('class/BCGDrawing.php');
			require_once('class/BCGcode128.barcode.php'); 
			
			$fontPath = Mage::getBaseDir() . '/lib/LinLibertineFont/Arial.ttf'; 
			$font = new BCGFont($fontPath, 18);
			
			$xy=0;	
			
			foreach ($pdfTicketOrders as $tiCor){
				$ticketId = $tiCor['ticket_id'];
				
				// query om de barcode te halen
				$queryTicket = "SELECT * FROM `ticket_barcode` WHERE `ticket_id` = " . $ticketId . " LIMIT 1";
				$rezTicket = $write->fetchAll($queryTicket);
				
				$ean = $rezTicket[0]['barcode']; 
				$oid = $rezTicket[0]['order_id'];
				
				$kaarten = $tiCor['product'];
				
				if(strlen($ean) == 11) {
					//$page = $this->newTicketPage();
					$page = $pdf->newPage(Zend_Pdf_Page::SIZE_A4);
					$pdf->pages[] = $page;
					$this->y = 800;
					
					//Hier afblijven - hier wordt de code gemaakt.
					$color_black = new BCGColor(0, 0, 0);
					$color_white = new BCGColor(255, 255, 255); 
					$code = new BCGcode128();
					//print_r($code); die();
					$code->setScale(2); // Resolution
					$code->setThickness(30); // Thickness
					$code->setForegroundColor($color_black); // Color of bars
					$code->setBackgroundColor($color_white); // Color of spaces
					//$code->setTextFont(0);
					$code->setFont($font);
					$code->setStart('B');
					$code->setTilde(true);
					$code->parse($ean); // Text
					$drawing = new BCGDrawing(Mage::getBaseDir('var').'/barcode/used/'.$ean.'.png', $color_white);
					//print_r($drawing); die();
					$drawing->setBarcode($code);
					$drawing->draw();
					$drawing->finish(BCGDrawing::IMG_FORMAT_PNG);

					//$this->insertTicketBck($page);
					// Set Background of PDF - is set in db table of ticket.
					// Thus variable for every ticket
					// Image formaat is als volgt.
					//
					// Nr 1 is aanduiding links boven
					// Nr 2 is aanduiding rechts onder
					// X1 , Y1 , X2 , Y2
					//
					
					
					//mooie voor de toekomst
					$pdfBg = $tiCor['themebg'];
					$pdfColor = $tiCor['themecolor'];
					
					if (!empty($pdfBg)){
						$image = Mage::getBaseDir('var').'/barcode/' . $pdfBg . '.png';
					} else {
						$image = Mage::getBaseDir('var').'/barcode/ticket3.png';
					}
					
					//$image = Mage::getBaseDir('var').'/barcode/ticket3.png';
					if (is_file($image)) {
						$image = Zend_Pdf_Image::imageWithPath($image);
						$page->drawImage($image, 0, 380, 595, 822);
					}
					
					$barcodeImg = Mage::getBaseDir('var').'/barcode/used/'.$ean.'.png';
					$barcodeImg = Zend_Pdf_Image::imageWithPath($barcodeImg);
					
					$page->drawImage($barcodeImg, 160, 650, 391, 730);
					
					$this->_setFontRegularArial($page);
					
					// Comment items om ze weg te halen.
					// Uncomment ze om ze er weer bij te zetten.
					// let erop de items worden via coordinaten geplaatst.
					// Dit zijn de 2 getallen na de helper.
					// Horizontaal (x) + Vertikaal (y)
					$x_col1 = 167;
					$x_col2 = 200;
					$y_col1 = 600;
					$y_col2 = 600;
					
					// set font kleur
					$page->setFillColor(Zend_Pdf_Color_Html::color('#' . $pdfColor));
					
					
					//$page->drawText(Mage::helper('sales')->__('Purchased:'), $x_col1, $y_col1, 'UTF-8');
					$page->drawText($tiCor['date'], $x_col2, $y_col2, 'UTF-8');
					//$x_col1 -= 20;
					//$x_col2 -= 20;
					$y_col1 -= 20;
					$y_col2 -= 20;
					
					
					//$page->drawText(Mage::helper('sales')->__('Order ID:'), $x_col1, $y_col1, 'UTF-8');
					$page->drawText('#'.$incrementId, $x_col2, $y_col2, 'UTF-8');
					//$x_col1 -= 20;
					//$x_col2 -= 20;
					$y_col1 -= 20;
					$y_col2 -= 20;
					
					
					//$page->drawText(Mage::helper('sales')->__('Prijs:'), $x_col1, $y_col1, 'UTF-8');
					//$page->drawText(strip_tags(Mage::helper('core')->currency($tiCor['price'], true, true)), $x_col2, 560, 'UTF-8');
					//$x_col1 -= 20;
					//$x_col2 -= 20;
					$y_col1 -= 20;
					$y_col2 -= 20;
					
					$tableName = $resource->getTableName('sales_flat_order_item');
					$queryName = "SELECT `name` FROM `" . $tableName . "` WHERE `sku` = '" . $kaarten . "' ";
					$rezName = $write->fetchAll($queryName);
					$pName = $rezName[0]['name'];
					
					//$page->drawText(Mage::helper('sales')->__('Product:'), $x_col1, $y_col1, 'UTF-8');
					$page->drawText($pName, $x_col2, $y_col2, 'UTF-8');
					//$x_col1 -= 20;
					//$x_col2 -= 20;
					$y_col1 -= 20;
					$y_col2 -= 20;
					
					
					//$page->drawText(Mage::helper('sales')->__('Name:'), $x_col1, $y_col1, 'UTF-8');
					$page->drawText($tiCor['name'], $x_col2, $y_col2, 'UTF-8');
					//$x_col1 -= 20;
					//$x_col2 -= 20;
					$y_col1 -= 20;
					$y_col2 -= 20;
					
					
					$d1 = explode('-',$tiCor['birthday']);
					$d2 = $d1[2].'-'.$d1[1].'-'.$d1[0];
					//$page->drawText(Mage::helper('sales')->__('Date of birth:'), $x_col1, $y_col1, 'UTF-8');
					//$page->drawText($d2, $x_col2, $y_col2, 'UTF-8');
					//$x_col1 -= 20;
					//$x_col2 -= 20;
					$y_col1 -= 20;
					$y_col2 -= 20;
					
					
					//$page->drawText(Mage::helper('sales')->__('E-mail:'), $x_col1, $y_col1, 'UTF-8');
					//$page->drawText($tiCor['email'], $x_col2, $y_col2, 'UTF-8');
					
					
					
					// Delete barcode for safety.
					
					$deleteBar = Mage::getBaseDir('var').'/barcode/used/'.$ean.'.png';
					while(is_file($deleteBar) == TRUE)
					{
						chmod($deleteBar, 0666);
						unlink($deleteBar);
					}
					
					// Update sql for invoice id
					$write->query("UPDATE `ticket_barcode` SET `invoice_id` = " . $invoiceId . " WHERE `ticket_id` = '" . $ticketId . "' LIMIT 1");
					
					
					$xy++;
				}
           }
		   }
        }

        $this->_afterGetPdf();

        return $pdf;
    }

    /**
     * Create new page and assign to PDF object
     *
     * @param array $settings
     * @return Zend_Pdf_Page
     */
    public function newPage(array $settings = array())
    {
        /* Add new table head */
        $page = $this->_getPdf()->newPage(Zend_Pdf_Page::SIZE_A4);
        $this->_getPdf()->pages[] = $page;
        $this->y = 800;

        if (!empty($settings['table_header'])) {
            $this->_setFontRegular($page);
            $page->setFillColor(new Zend_Pdf_Color_RGB(0.93, 0.92, 0.92));
            $page->setLineColor(new Zend_Pdf_Color_GrayScale(0.5));
            $page->setLineWidth(0.5);
            $page->drawRectangle(25, $this->y, 570, $this->y-15);
            $this->y -=10;

            $page->setFillColor(new Zend_Pdf_Color_RGB(0.4, 0.4, 0.4));
            $page->drawText(Mage::helper('sales')->__('Product'), 35, $this->y, 'UTF-8');
            $page->drawText(Mage::helper('sales')->__('SKU'), 255, $this->y, 'UTF-8');
            $page->drawText(Mage::helper('sales')->__('Price'), 380, $this->y, 'UTF-8');
            $page->drawText(Mage::helper('sales')->__('Qty'), 430, $this->y, 'UTF-8');
            $page->drawText(Mage::helper('sales')->__('Tax'), 480, $this->y, 'UTF-8');
            $page->drawText(Mage::helper('sales')->__('Subtotal'), 535, $this->y, 'UTF-8');

            $page->setFillColor(new Zend_Pdf_Color_GrayScale(0));
            $this->y -=20;
        }

        return $page;
    }
	
	public function insertTicketBck(&$page)
    {
        $image = Mage::getBaseDir('var').'/barcode/ticket3.png';
		if (is_file($image)) {
			Mage::Log("GetPDF Order : image link 3 " . $image);
			$image = Zend_Pdf_Image::imageWithPath($image);
			$page->drawImage($image, 0, 380, 595, 822);
		}
        //return $page;
    }
	
	public function _setFontRegularArial($object, $size = 14)
    {
        $font = Zend_Pdf_Font::fontWithPath(Mage::getBaseDir() . '/lib/LinLibertineFont/Arial.ttf');
        $object->setFont($font, $size);
        return $font;
    }
}
