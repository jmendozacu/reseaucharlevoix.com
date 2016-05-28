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

        
        Mage::log('Drieworks_EmailAttachments_Model_Order_Pdf_Invoice');
        
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
            Mage::log('Add tickets if available');
			/* add tickets if available */
			$resource = Mage::getSingleton('core/resource');
			$write = $resource->getConnection('core_write');
			
			$incrementId = $order->getIncrementId();
			$invoiceId = $invoice->getId();
			




//Eric: Get Booking and seats info, if any occurs.
				
ini_set('display_errors',1);
ini_set('display_startup_errors',1);
error_reporting(-1);

include 'fonctiondatereserv.php';


// Mysql Connection


function dbCon(){
	$databaseHost = 'localhost';
$databaseName = 'Magento';
$databaseUser = 'root';
$databasePassword = '1217Patate!';
	$con =  mysql_connect($databaseHost, $databaseUser, $databasePassword) or die(mysql_error());
	mysql_select_db($databaseName) or die(mysql_error());
	return $con;
}



	$SQL  =  "SELECT  ticket_orders.product, ticket_orders.order_id, entity_id, res_all, ticket_id ";
	$SQL .=  "FROM ticket_orders ";
	$SQL .=  "JOIN sales_flat_order ON ticket_orders.order_id=sales_flat_order.increment_id ";
	$SQL .=  "WHERE ticket_orders.order_id = '" . $incrementId . "' ";
	$query = mysql_query($SQL, dbCon());


//echo $query;
while ($row = mysql_fetch_array($query, MYSQL_ASSOC)) {
    //print_r($row);
   		$typeproduit = substr($row['product'],0,3);
		$stationDepart = substr($row['product'],3,3);
		$stationArrive = substr($row['product'],6,3);
		$allezRetour = substr($row['product'],12,3);
		$subidorder =  $row['entity_id'];   
		$chreservation =   $row['res_all']; 
		$ticketid =   $row['ticket_id'];          

		
		if ($typeproduit == 'spc' AND $chreservation == '')	{

			//echo "spectacle<br>";

			$stationDepart = 'bla';
			$stationArrive = 'bla';
			//getdate, write used write date in ticket
					//determination du trajet (salle)
					$ftrajet = nosalle($stationDepart,$stationArrive);
			//get date
					$data = getinforeserv($ftrajet,$subidorder);


			if ($data['c']== 'rien')
					{ //echo 'rien pantoute';
 					}else
					{
					$dheuredepart = date_format( new DateTime($data['c']), 'Y/m/d H:i:s' );
					$dheuredepart = new DateTime($dheuredepart);
					$idsiege = $data['b'];
					$idreservation = $data['a'];

					$txtreserv = 'Date ';
					$txtreserv = $txtreserv . date_format( $dheuredepart, 'Y/m/d H:i' );
					$txtreserv = $txtreserv . "sieg: " . $idsiege;
					//write date in ticket ret

					updatereserv($idreservation);
					updateticketreserv($ticketid,$txtreserv);

					}





			} elseif ($typeproduit == 'bus' AND $chreservation == '') {



			//getdate, write used write date in ticket
			} 
			elseif ($typeproduit == 'nav' AND $chreservation == '') {



					//determination du trajet (salle)
					$ftrajet = nosalle($stationDepart,$stationArrive);

					//direction
					$fdirection = direction($stationDepart,$stationArrive);


					//aller chercher l'heure dans la table reservation 
					// une fois si all 2 fois si ret
					//get date

			if ($allezRetour == 'all')	{


					$data = getinforeserv($ftrajet,$subidorder);


			if ($data['c']== 'rien')
					{ //echo 'rien pantoute';
 					}else
					{
					$dheuredepart = date_format( new DateTime($data['c']), 'Y/m/d H:i:s' );
					$dheuredepart = new DateTime($dheuredepart);
					$idsiege = $data['b'];
					$idreservation = $data['a'];

					$txtreserv = 'Date ';
					$txtreserv = $txtreserv . date_format( $dheuredepart, 'Y/m/d H:i' );
					$txtreserv = $txtreserv . "sieg: " . $idsiege;
					//write date in ticket ret

					updatereserv($idreservation);
					updateticketreserv($ticketid,$txtreserv);

					}

					


			}else {
  			  
					$data = getinforeserv($ftrajet,$subidorder);

					
					if ($data['c']== 'rien')
					{ echo 'rien pantoute';
 					}else
					{
					$dheuredepart = date_format( new DateTime($data['c']), 'Y/m/d H:i:s' );
					//echo $dheuredepart;
					$dheuredepart = new DateTime($dheuredepart);
					$idsiege = $data['b'];
					$idreservation = $data['a'];
					
					//correction de l'heure seulement si train
		
					$heurecorige = changeheure($dheuredepart,$ftrajet,$stationDepart,$fdirection);

					// write used in reservation
					updatereserv($idreservation);

					//write date in ticket  aller

					$txtreserv = 'AL: ';
					$txtreserv = $txtreserv . date_format( $heurecorige, 'Y/m/d H:i' );
					$txtreserv = $txtreserv . " sieg: " . $idsiege;
					//write date in ticket ret

					updatereserv($idreservation);


					}
					//2e fois  ne pas oublier de changer la direction $fdirection

					//direction
					$fdirection2 = direction($stationArrive,$stationDepart);
					$stationDepart2 = $stationArrive;
							
					
					$data = getinforeservret($ftrajet,$subidorder);

					
					if ($data['c']== 'rien')
					{ //echo 'rien pantoute';
 					}else
					{
					$dheuredepart = date_format( new DateTime($data['c']), 'Y/m/d H:i:s' );
					//echo $dheuredepart;
					$dheuredepart = new DateTime($dheuredepart);
					$idsiege = $data['b'];
					$idreservation = $data['a'];

					//correction de l'heure seulement si train
		
					$heurecorige = changeheure($dheuredepart,$ftrajet,$stationDepart2,$fdirection2);

					// write used in reservation
					updatereserv($idreservation);

					//write date in ticket ret
					$txtreserv = $txtreserv . " ret: ";
					$txtreserv = $txtreserv . date_format( $heurecorige, 'Y/m/d H:i' );
					$txtreserv = $txtreserv . " sieg: " . $idsiege;

					updateticketreserv($ticketid,$txtreserv);

					}
			}


			
			}else {
  			  //echo "ne rien faire";
			}


   
   
}
				// Eric fin modif reservation ----------------------------------------------------------------------------------------------------------------------


			
			$queryTicketOrders = "SELECT * FROM `ticket_orders` WHERE `order_id` = " . $incrementId . " AND `ticket_type`='ticket'";
			$pdfTicketOrders = $write->fetchAll($queryTicketOrders);
			
			if (!empty($pdfTicketOrders)){
				Mage::log('Tickets is available');
				require_once('class/BCGFont.php');
				require_once('class/BCGColor.php');
				require_once('class/BCGDrawing.php');
				require_once('class/BCGcode128.barcode.php'); 
				
				$fontPath = Mage::getBaseDir() . '/lib/LinLibertineFont/Arial.ttf'; 
				$font = new BCGFont($fontPath, 10);
				
				$xy=0;
					
				// _____________________________________________________________________________________
				// Début de la création des tickets
				// _____________________________________________________________________________________
					
				
				
				Mage::log('Add new page');
				// Add new page
				$page = $pdf->newPage ( Zend_Pdf_Page::SIZE_A4 );
				$pdf->pages [] = $page;
				// begin loop counter
				$ticketcounter = 0;
				$ticketcounterReservation = 0;
				
				
				foreach ( $pdfTicketOrders as $tiCor ) {
					$ticketId = $tiCor ['ticket_id'];
					//Break Page
					if ($ticketcounter == 6) {
						$page = $pdf->newPage ( Zend_Pdf_Page::SIZE_A4 );
						$pdf->pages [] = $page;
						// Reset Ticket counter
						$ticketcounter = 0;
					}
					
					
					// query om de barcode te halen
					$queryTicket = "SELECT * FROM `ticket_barcode` WHERE `ticket_id` = " . $ticketId . " LIMIT 1";
					$rezTicket = $write->fetchAll ( $queryTicket );
					
					$ean = $rezTicket [0] ['barcode'];
					$oid = $rezTicket [0] ['order_id'];
					
					$kaarten = $tiCor ['product'];
					
					if (strlen ( $ean ) == 11) {
						// $page = $this->newTicketPage();
						
						$this->y = 1;
						
						// Hier afblijven - hier wordt de code gemaakt.
						$color_black = new BCGColor ( 0, 0, 0 );
						$color_white = new BCGColor ( 255, 255, 255 );
						$code = new BCGcode128 ();
						// print_r($code); die();
						$code->setScale ( 2 ); // Resolution
						$code->setThickness ( 30 ); // Thickness
						$code->setForegroundColor ( $color_black ); // Color of bars
						$code->setBackgroundColor ( $color_white ); // Color of spaces
						                                         // $code->setTextFont(0);
						$code->setFont ( $font, 10 );
						$font = Zend_Pdf_Font::fontWithName ( Zend_Pdf_Font::FONT_HELVETICA );
						$code->setStart ( 'B' );
						$code->setTilde ( true );
						$code->parse ( $ean ); // Text
						$drawing = new BCGDrawing ( Mage::getBaseDir ( 'var' ) . '/barcode/used/' . $ean . '.png', $color_white );
						// print_r($drawing); die();
						$drawing->setBarcode ( $code );
						$drawing->draw ();
						$drawing->finish ( BCGDrawing::IMG_FORMAT_PNG );
						
						// $this->insertTicketBck($page);
						// Set Background of PDF - is set in db table of ticket.
						// Thus variable for every ticket
						// Image formaat is als volgt.
						//
						// Nr 1 is aanduiding links boven
						// Nr 2 is aanduiding rechts onder
						// X1 , Y1 , X2 , Y2
						//
						
						// mooie voor de toekomst
						$pdfBg = $tiCor ['themebg'];
						$pdfColor = $tiCor ['themecolor'];
						
						if (! empty ( $pdfBg )) {
							$image = Mage::getBaseDir ( 'var' ) . '/barcode/' . $pdfBg . '.png';
						} else {
							$image = Mage::getBaseDir ( 'var' ) . '/barcode/ticket3.png';
						}
						
						// $image = Mage::getBaseDir('var').'/barcode/ticket3.png';
						if (is_file ( $image )) {
							$image = Zend_Pdf_Image::imageWithPath ( $image );
							// $page->drawImage($image, 8, 16, 306, 238);
							switch ($ticketcounter) {
								case 0 :
									$page->drawImage ( $image, 8, 528, 306, 756 ); // ticket bacground 1
									break;
								case 1 :
									$page->drawImage ( $image, 307, 528, 595, 756 ); // ticket bacground 2
									break;
								case 2 :
									$page->drawImage ( $image, 8, 268, 306, 492 ); // ticket bacground 3
									break;
								case 3 :
									$page->drawImage ( $image, 307, 268, 595, 492 ); // ticket bacground 4
									break;
								case 4 :
									$page->drawImage ( $image, 8, 13, 306, 237 ); // ticket bacground 5
									break;
								case 5 :
									$page->drawImage ( $image, 307, 13, 595, 237 ); // ticket bacground 6
									break;
							}
							// $page->drawImage($image, 8, 524, 306, 748); // ticket bacground 1
							// $page->drawImage($image, 307, 524, 595, 748); // ticket bacground 2
							// $page->drawImage($image, 8, 268, 306, 492); // ticket bacground 3
							// $page->drawImage($image, 307, 268, 595, 492); // ticket bacground 4
							// $page->drawImage($image, 8, 13, 306, 237); // ticket bacground 5
							// $page->drawImage($image, 307, 13, 595, 237); // ticket bacground 6
							
							// $page->drawText('Ticket 2', 307, 16);
							// $page->drawText('Ticket 3', 8, 272);
							// $page->drawText('Ticket 4', 307, 272);
							// $page->drawText('Ticket 5', 8, 526);
							// $page->drawText('Ticket 6', 307, 526);
						}
						
						$barcodeImg = Mage::getBaseDir ( 'var' ) . '/barcode/used/' . $ean . '.png';
						$barcodeImg = Zend_Pdf_Image::imageWithPath ( $barcodeImg );
						
						switch ($ticketcounter) {
							case 0 :
								$page->drawImage ( $barcodeImg, 182, 526, 300, 558 );
								break;
							case 1 :
								$page->drawImage ( $barcodeImg, 466, 526, 584, 558 );
								break;
							case 2 :
								$page->drawImage ( $barcodeImg, 182, 267, 300, 299 );
								break;
							case 3 :
								$page->drawImage ( $barcodeImg, 466, 267, 584, 299 );
								break;
							case 4 :
								$page->drawImage ( $barcodeImg, 182, 13, 300, 45 );
								break;
							case 5 :
								$page->drawImage ( $barcodeImg, 466, 13, 584, 45 );
								break;
						}
						// $page->drawImage($barcodeImg, 156, 526, 274, 558);
						// /dupliquer le code bar
						// $page->drawImage($barcodeImg, 440, 526, 558, 558);
						// $page->drawImage($barcodeImg, 156, 267, 274, 299);
						// $page->drawImage($barcodeImg, 440, 267, 558, 299);
						// $page->drawImage($barcodeImg, 156, 13, 274, 45);
						// $page->drawImage($barcodeImg, 440, 13, 558, 45);
						
						// $this->_setFontRegularArial($page);
						$page->setFont ( $font, 8 );
						
						// Comment items om ze weg te halen.
						// Uncomment ze om ze er weer bij te zetten.
						// let erop de items worden via coordinaten geplaatst.
						// Dit zijn de 2 getallen na de helper.
						// Horizontaal (x) + Vertikaal (y)
					switch ($ticketcounter) {
							case 0 :
								$x_col1 = 12;
								$x_col2 = 55;
								$y_col1 = 570;
								$y_col2 = 570;
								
								break;
							case 1 :
								$x_col1 = 312;
								$x_col2 = 355;
								$y_col1 = 570;
								$y_col2 = 570;
								break;
							case 2 :
								$x_col1 = 12;
								$x_col2 = 55;
								$y_col1 = 310;
								$y_col2 = 310;
								break;
							case 3 :
								$x_col1 = 312;
								$x_col2 = 355;
								$y_col1 = 310;
								$y_col2 = 310;
								break;
							case 4 :
								$x_col1 = 12;
								$x_col2 = 55;
								$y_col1 = 56;
								$y_col2 = 56;
								break;
							case 5 :
								$x_col1 = 312;
								$x_col2 = 355;
								$y_col1 = 56;
								$y_col2 = 56;
								break;
						}
						
						// $x_col1 = 12;
						// $x_col2 = 55;
						// $y_col1 = 558;
						// $y_col2 = 558;
						
						// set font kleur
						$page->setFillColor ( Zend_Pdf_Color_Html::color ( '#' . $pdfColor ) );
						
						// $page->drawText('Ticket 1', 8, 526, 'UTF-8');
						
						// $page->drawText('Ticket 2', 312, 558);
						// $page->drawText('Ticket 3', 12, 302);
						// $page->drawText('Ticket 4', 312, 302);
						// $page->drawText('Ticket 5', 12, 47);
						// $page->drawText('Ticket 6', 312, 47);
						
						// $page->drawText('coin 1 ', 0, 0);
						// $page->drawText('coin 2', 0, 837);
						// $page->drawText('coin 3', 590, 0);
						// $page->drawText('coin 4', 590, 837);
						
						$tableName = $resource->getTableName ( 'sales_flat_order_item' );
						$queryName = "SELECT `name` FROM `" . $tableName . "` WHERE `sku` = '" . $kaarten . "' ";
						$rezName = $write->fetchAll ( $queryName );
						$pName = $rezName [0] ['name'];
						// $pName = $kaarten;
						
						$page->drawText ( Mage::helper ( 'sales' )->__ ( 'Produit:' ), $x_col1, $y_col1, 'UTF-8' );
						$page->drawText ( $pName, $x_col2, $y_col2, 'UTF-8' );
						// $x_col1 -= 10;
						// $x_col2 -= 10;
						$y_col1 -= 8;
						$y_col2 -= 8;
						
						$rerservation = $tiCor ['res_all'];
						$page->drawText ( 'Reserv: ' , $x_col1, $y_col1, 'UTF-8' );

						

						$page->drawText ($rerservation , $x_col2 + 5, $y_col2, 'UTF-8' );
						// $x_col1 -= 10;
						// $x_col2 -= 10;
						$y_col1 -= 8;
						$y_col2 -= 8;
						
						$page->drawText ( 'Categorie :', $x_col1, $y_col1, 'UTF-8' );
						$skuage = substr ( $kaarten, 16, 4 );
						
						switch ($skuage) {
							case '0006' :
								$categorie = "0 à 6 ans";
								break;
							case '0717' :
								$categorie = "7 à 17 ans";
								break;
							case '1899' :
								$categorie = "18 ans et plus";
								break;
							case '1825' :
								$categorie = "18 à 25 ans";
								break;
							case '2664' :
								$categorie = "26 à 64 ans";
								break;
							case '6599' :
								$categorie = "65 ans et plus";
								break;
							default :
								$categorie = "";
								break;
						}
						
						$categorie = utf8_encode ( $categorie );
						$page->drawText ( $categorie, $x_col2, $y_col2, 'UTF-8' );
						// $x_col1 -= 10;
						// $x_col2 -= 10;
						$y_col1 -= 8;
						$y_col2 -= 8;
						
						$page->drawText ( Mage::helper ( 'sales' )->__ ( 'Commande:' ), $x_col1, $y_col1, 'UTF-8' );
						$page->drawText ( '#' . $incrementId, $x_col2, $y_col2, 'UTF-8' );
						// $x_col1 -= 10;
						// $x_col2 -= 10;
						// $y_col1 -= 10;
						// $y_col2 -= 10;
						
						// $page->drawText(Mage::helper('sales')->__('Prijs:'), $x_col1, $y_col1, 'UTF-8');
						// $page->drawText(strip_tags(Mage::helper('core')->currency($tiCor['price'], true, true)), $x_col2, 560, 'UTF-8');
						// $x_col1 -= 10;
						// $x_col2 -= 10;
						$y_col1 -= 8;
						$y_col2 -= 8;
						
						$page->drawText ( Mage::helper ( 'sales' )->__ ( 'Nom:' ), $x_col1, $y_col1, 'UTF-8' );
						$page->drawText ( $tiCor ['name'], $x_col2, $y_col2, 'UTF-8' );
						// $x_col1 -= 10;
						// $x_col2 -= 10;
						$y_col1 -= 8;
						$y_col2 -= 8;
						
						
						
						// $d1 = explode('-',$tiCor['birthday']);
						// $d2 = $d1[2].'-'.$d1[1].'-'.$d1[0];
						// $page->drawText(Mage::helper('sales')->__('Date of birth:'), $x_col1, $y_col1, 'UTF-8');
						// $page->drawText($d2, $x_col2, $y_col2, 'UTF-8');
						// $x_col1 -= 20;
						// $x_col2 -= 20;
						// $y_col1 -= 10;
						// $y_col2 -= 10;
						
						// $page->drawText(Mage::helper('sales')->__('E-mail:'), $x_col1, $y_col1, 'UTF-8');
						// $page->drawText($tiCor['email'], $x_col2, $y_col2, 'UTF-8');
						
						// Delete barcode for safety.
						
						$deleteBar = Mage::getBaseDir ( 'var' ) . '/barcode/used/' . $ean . '.png';
						while ( is_file ( $deleteBar ) == TRUE ) {
							chmod ( $deleteBar, 0666 );
							unlink ( $deleteBar );
						}
						Mage::log("UPDATE `ticket_barcode` SET `invoice_id` = " . $invoiceId . " WHERE `ticket_id` = '" . $ticketId . "' LIMIT 1");
						// Update sql for invoice id
						$write->query ( "UPDATE `ticket_barcode` SET `invoice_id` = " . $invoiceId . " WHERE `ticket_id` = '" . $ticketId . "' LIMIT 1" );
						
						$xy ++;
						// Ticket counter increment
						$ticketcounter += 1;
						$ticketcounterReservation += 1;
					}
				}
				//_____________________________________________________________________________________
				// Fin de la création des tickets
				//_____________________________________________________________________________________
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
