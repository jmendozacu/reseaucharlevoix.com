<?php
/**
 * Created by JetBrains PhpStorm.
 * User: MichaelK
 * Date: 5/28/13
 * Time: 2:21 PM
 * To change this template use File | Settings | File Templates.
 */ 
class Demac_Taxfix_Model_Resource_Tax_Calculation extends Mage_Tax_Model_Resource_Calculation {

    /**
     * Retrieve Calculation Process
     *
     * @param Varien_Object $request
     * @param array $rates
     * @return array
     */
    public function getCalculationProcess($request, $rates = null)
    {
        if (is_null($rates)) {
            $rates = $this->_getRates($request);
        }

        $result = array();
        $row = array();
        $ids = array();
        $currentRate = 0;
        $totalPercent = 0;
        $countedRates = count($rates);
        for ($i = 0; $i < $countedRates; $i++) {
            $rate = $rates[$i];
            $value = (isset($rate['value']) ? $rate['value'] : $rate['percent'])*1;

            $oneRate = array(
                'code'=>$rate['code'],
                'title'=>$rate['title'],
                'percent'=>$value,
                'position'=>$rate['position'],
                'priority'=>$rate['priority'],
            );
            if (isset($rate['tax_calculation_rule_id'])) {
                $oneRate['rule_id'] = $rate['tax_calculation_rule_id'];
            }

            if (isset($rate['hidden'])) {
                $row['hidden'] = $rate['hidden'];
            }

            if (isset($rate['amount'])) {
                $row['amount'] = $rate['amount'];
            }

            if (isset($rate['base_amount'])) {
                $row['base_amount'] = $rate['base_amount'];
            }
            if (isset($rate['base_real_amount'])) {
                $row['base_real_amount'] = $rate['base_real_amount'];
            }
            $row['rates'][] = $oneRate;

            if (isset($rates[$i+1]['tax_calculation_rule_id'])) {
                $rule = $rate['tax_calculation_rule_id'];
            }
            $priority = $rate['priority'];
            $ids[] = $rate['code'];

            if (isset($rates[$i+1]['tax_calculation_rule_id'])) {
                while(isset($rates[$i+1]) && $rates[$i+1]['tax_calculation_rule_id'] == $rule) {
                    $i++;
                }
            }

            $currentRate += $value;

            if (!isset($rates[$i+1]) || $rates[$i+1]['priority'] != $priority
                || (isset($rates[$i+1]['process']) && $rates[$i+1]['process'] != $rate['process']) || ($rates[$i+1]['code'] != $rate['code'])
            ) {
                if(($rates[$i+1]['code'] != $rate['code'])){
                    $row['percent'] = $value;
                }
                else{
                    $row['percent'] = (100+$totalPercent)*($currentRate/100);
                }
                $row['id'] = implode($ids);
                $result[] = $row;
                $row = array();
                $ids = array();

                $totalPercent += (100+$totalPercent)*($currentRate/100);
                $currentRate = 0;
            }
        }

        return $result;
    }
}