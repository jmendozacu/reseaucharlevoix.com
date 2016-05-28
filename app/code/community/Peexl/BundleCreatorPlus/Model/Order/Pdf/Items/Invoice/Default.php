<?php

class Peexl_BundleCreatorPlus_Model_Order_Pdf_Items_Invoice_Default extends Mage_Sales_Model_Order_Pdf_Items_Invoice_Default
{
    /**
     * Draw item line
	   **/
    public function draw()
    {   
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
            'feed'  => 255,
        );

        // draw QTY
        $lines[0][] = array(
            'text'  => $item->getQty() * 1,
            'feed'  => 435,
            'font'  => 'bold',
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
                    'text' => Mage::helper('core/string')->str_split(strip_tags($option['label']), 70, true, true),
                    'font' => 'italic',
                    'feed' => 35
                );

                if ($option['value']) {
                    $_printValue = isset($option['print_value']) ? $option['print_value'] : strip_tags($option['value']);
                    $values = explode(', ', $_printValue);
                    foreach ($values as $value) {
                        $lines[][] = array(
                            'text' => Mage::helper('core/string')->str_split($value, 50, true, true),
                            'feed' => 40
                        );
                    }
                }
            }
        }
        
        $childItems = $this->getChildren();
        if (count($childItems)) {
            $lines[][] = array(
                'text' => Mage::helper('core/string')->str_split('Included Items', 80, true, true),
                'font' => 'italic',
                'feed' => 35
            );
            foreach ($childItems as $childItem) {
                $line = array(
                            array(
                                'text' => Mage::helper('core/string')->str_split(round($childItem->getQtyOrdered()) . 'x ' . $childItem->getName(), 60, true, true),
                                'feed' => 50
                            ),
                      );    
                $lines[] = $line;
                
                // custom options
                $options = $this->getItemOptions($childItem);
                if ($options) {
                    foreach ($options as $option) {
                        // draw options label
                        $lines[][] = array(
                            'text' => Mage::helper('core/string')->str_split(strip_tags($option['label']), 70, true, true),
                            'font' => 'italic',
                            'feed' => 65
                        );
        
                        if ($option['value']) {
                            $_printValue = html_entity_decode(isset($option['print_value']) ? $option['print_value'] : strip_tags($option['value']));
                            $values = explode(', ', $_printValue);
                            foreach ($values as $value) {
                                $lines[][] = array(
                                    'text' => Mage::helper('core/string')->str_split($value, 50, true, true),
                                    'feed' => 75
                                );
                            }
                        }
                    }
                }
                
                if ($childItem->getParentItem() && $childItem->getProduct()->getTypeId() == 'bundle') {
                    $_bundleItems = $childItem->getChildrenItems();
                    foreach($_bundleItems as $_bundleItem) {
                        $attributes = $this->getSelectionAttributes($_bundleItem);
                        if (is_array($attributes)) {
                            $optionId   = $attributes['option_id'];
                        }
                        else {
                            $optionId = 0;
                        }

                        if ($_prevOptionId != $attributes['option_id']) {
                            $line[0] = array(
                                'font'  => 'italic',
                                'text'  => Mage::helper('core/string')->str_split($attributes['option_label'], 45, true, true),
                                'feed'  => 65
                            );

                            $lines[] = $line;;

                            $_prevOptionId = $attributes['option_id'];
                        }
     
                        $line = array(
                            array(
                                'text' => Mage::helper('core/string')->str_split(round($_bundleItem->getQtyOrdered()) . 'x ' . $_bundleItem->getName(), 35, true, true),
                                'feed' => 80
                            ),
                        );
                        $lines[] = $line;
                        
                        // custom options
                        $options = $this->getItemOptions($_bundleItem);
                        if ($options) {
                            foreach ($options as $option) {
                                // draw options label
                                $lines[][] = array(
                                    'text' => Mage::helper('core/string')->str_split(strip_tags($option['label']), 70, true, true),
                                    'font' => 'italic',
                                    'feed' => 65
                                );
                
                                if ($option['value']) {
                                    $_printValue = isset($option['print_value']) ? $option['print_value'] : strip_tags($option['value']);
                                    $values = explode(', ', $_printValue);
                                    foreach ($values as $value) {
                                        $lines[][] = array(
                                            'text' => Mage::helper('core/string')->str_split($value, 50, true, true),
                                            'feed' => 75
                                        );
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }
        
              
		    //Edited default line height
        $lineBlock = array(
            'lines'  => $lines,
            'height' => 15,
            'shift'  => 1
        );

        $page = $pdf->drawLineBlocks($page, array($lineBlock), array('table_header' => true));
        $this->setPage($page);
		
    }
       
    public function getChildren() {
        $children = array();
        $orderItems = $this->getOrder()->getItemsCollection();
        foreach ($orderItems as $item) {
            if ($item->getParentItemId() == $this->getItem()->getOrderItem()->getId())
                $children[] = $item;
        }
        return $children;
    }
    
    /**
     * Retrieve item options
     *
     * @return array
     */
    public function getItemOptions($item = null) {
        if(!$item) {           
            $item = $this->getItem()->getOrderItem();
        }
        if (!($item instanceof Mage_Sales_Model_Order_Item)) {
            $item = $item->getOrderItem();
        } 
        $result = array();
        if ($options = $item->getProductOptions()) {
            if (isset($options['options'])) {
                $result = array_merge($result, $options['options']);
            }
            if (isset($options['additional_options'])) {
                $result = array_merge($result, $options['additional_options']);
            }
            if (isset($options['attributes_info'])) {
                $result = array_merge($result, $options['attributes_info']);
            }
        }
        return $result;
    }
    
    /**
     * Retrieve Selection attributes
     *
     * @param Varien_Object $item
     * @return mixed
     */
    public function getSelectionAttributes($item)
    {
        if ($item instanceof Mage_Sales_Model_Order_Item) {
            $options = $item->getProductOptions();
        } else {
            $options = $item->getOrderItem()->getProductOptions();
        }
        if (isset($options['bundle_selection_attributes'])) {
            return unserialize($options['bundle_selection_attributes']);
        }
        return null;
    }
}
